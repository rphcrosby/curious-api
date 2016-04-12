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
        $first = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        // Get the user
        $this->api('GET', '/users/me?include=invites', [], $token)
            ->seeJson([
                "invites" => [
                    "data" => []
                ]
            ]);

        // Invite another user via email
        $this->api('POST', "/users/{$first->id}/invites", [
            'email' => 'test132@test.com'
        ], $token)->assertResponseStatus(204);

        $resource = new Collection($first->invites, new InviteTransformer);
        $invites = with(new Manager)->createData($resource)->toArray();

        // Get the user again and this time confirm they have an invite added
        $this->api('GET', '/users/me?include=invites', [], $token)
            ->seeJson($invites);
    }

    /**
     * Test that inviting a user by email shows up in invites
     *
     */
    public function testCreateInviteForOtherUserFails()
    {
        $first = factory(\App\User::class)->create();
        $second = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        // Invite another user via email
        $this->api('POST', "/users/{$second->id}/invites", [
            'email' => 'test132@test.com'
        ], $token)->seeJsonEquals([
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
        $first = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        // Get the user
        $this->api('GET', "/users/me?include=invites", [], $token)
            ->seeJson([
                "invites" => [
                    "data" => []
                ]
            ]);

        // Invite another user via email
        $this->api('POST', "/users/{$first->id}/invites", [
            'email' => 'test132@test.com'
        ], $token)->assertResponseStatus(204);

        // Invite the same user via email
        $this->api('POST', "/users/{$first->id}/invites", [
            'email' => 'test132@test.com'
        ], $token)->seeJsonEquals([
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
        $first = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        // Use up all of the user's invites
        for ($i = 0; $i < $first->invite_count; $i++) {
            $this->api('POST', "/users/{$first->id}/invites", [
                'email' => "test132{$i}@test.com"
            ], $token)->assertResponseStatus(204);
        }

        // Invite the same user via email
        $this->api('POST', "/users/{$first->id}/invites", [
            'email' => 'test1329@test.com'
        ], $token)->seeJsonEquals([
            "message" => trans('api.invites.insufficient'),
            "status_code" => 422
        ]);
    }
}
