<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use App\Transformers\UserTransformer;
use App\Transformers\InviteTransformer;

class UserDeleteTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test deleting a user
     *
     */
    public function testDeleteUser()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        $this->actingAs(App\User::find(1));

        // Delete the user
        $this->json('DELETE', '/users/1', [
            'username' => 'testuser1234'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->assertResponseStatus(204);
    }

    /**
     * Test that a user cannot delete another user
     *
     */
    public function testDeleteOtherUserFails()
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

        $this->actingAs(App\User::find(1));

        // Update the user
        $this->json('DELETE', '/users/2', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => "403 Forbidden",
                "status_code" => 403
            ]);
    }

    /**
     * Test that an administrator can delete anyone
     *
     */
    public function testAdministratorCanDeleteAnyUser()
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

        $role = App\Role::create([
            'name' => 'administrator'
        ]);

        // Make the user an administrator
        $user = App\User::find(1);
        $user->role_id = $role->id;
        $user->save();

        // Login as the user
        $this->actingAs($user);

        // Update the user
        $this->json('DELETE', '/users/2', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->assertResponseStatus(204);
    }
}
