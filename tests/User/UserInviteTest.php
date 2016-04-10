<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use App\Transformers\UserTransformer;
use App\Transformers\InviteTransformer;

class UserInviteTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test that inviting a user by email shows up in invites
     *
     */
    public function testCreateInvite()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'firstuser',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        // Get the user
        $this->json('GET', '/users/1?include=invites', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson([
                "invites" => [
                    "data" => []
                ]
            ]);

        $user = App\User::find(1);
        $user->invite_code = rand(100000, 999999);
        $user->invite_count = config('curious.invites');
        $user->save();
        $this->actingAs($user);

        // Invite another user via email
        $this->json('POST', '/users/1/invites', [
            'email' => 'test132@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->assertResponseStatus(204);

        $resource = new Collection($user->invites, new InviteTransformer);
        $invites = with(new Manager)->createData($resource)->toArray();

        // Get the user again and this time confirm they have an invite added
        $this->json('GET', '/users/1?include=invites', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson($invites);
    }

    /**
     * Test that inviting a user by email shows up in invites
     *
     */
    public function testCreateInviteForOtherUserFails()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'firstuser',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        // Create another user
        $this->json('POST', '/users', [
            'username' => 'seconduser',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test1234@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        // Get the user
        $this->json('GET', '/users/1?include=invites', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson([
                "invites" => [
                    "data" => []
                ]
            ]);

        $user = App\User::find(2);
        $user->invite_code = rand(100000, 999999);
        $user->invite_count = config('curious.invites');
        $user->save();
        $this->actingAs($user);

        // Invite another user via email
        $this->json('POST', '/users/1/invites', [
            'email' => 'test132@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => "403 Forbidden",
                "status_code" => 403
            ]);
    }

    /**
     * Test that a user can't invite the same user more than once
     *
     */
    public function testInviteTheSameUserTwice()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'firstuser',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        // Get the user
        $this->json('GET', '/users/1?include=invites', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson([
                "invites" => [
                    "data" => []
                ]
            ]);

        $user = App\User::find(1);
        $user->invite_code = rand(100000, 999999);
        $user->invite_count = config('curious.invites');
        $user->save();
        $this->actingAs($user);

        // Invite another user via email
        $this->json('POST', '/users/1/invites', [
            'email' => 'test132@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->assertResponseStatus(204);

        // Invite the same user via email
        $this->json('POST', '/users/1/invites', [
            'email' => 'test132@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => trans('api.invites.exists'),
                "status_code" => 422
            ]);
    }

    /**
     * Test that a user can't invite if they don't have any invites left
     *
     */
    public function testInviteWhenNoInvitesLeft()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'firstuser',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        // Save the invite information
        $user = App\User::find(1);
        $user->invite_code = rand(100000, 999999);
        $user->invite_count = config('curious.invites');
        $user->save();
        $this->actingAs($user);

        // Use up all of the user's invites
        for ($i = 0; $i < $user->invite_count; $i++) {
            $this->json('POST', '/users/1/invites', [
                'email' => "test132{$i}@test.com"
            ], ['accept' => 'application/vnd.curious.v1+json'])
                ->assertResponseStatus(204);
        }

        // Invite the same user via email
        $this->json('POST', '/users/1/invites', [
            'email' => 'test1329@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => trans('api.invites.insufficient'),
                "status_code" => 422
            ]);
    }
}
