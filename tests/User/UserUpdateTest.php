<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use App\Transformers\UserTransformer;
use App\Transformers\InviteTransformer;

class UserUpdateTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test that updating a user works
     *
     */
    public function testUpdateUser()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        $this->actingAs(App\User::find(1));

        // Update the user
        $this->json('PUT', '/users/1', [
            'username' => 'testuser1234'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson([
                "username" => "testuser1234"
            ]);
    }

    /**
     * Test that updating certain fields requires they be set to a minimum length
     *
     */
    public function testUpdateUserMinLengthFields()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        $this->actingAs(App\User::find(1));

        $this->json('PUT', '/users/1', [
            'username' => '123',
            'password' => '123',
            'password_confirmation' => '123'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => "422 Unprocessable Entity",
                "errors" => [
                    "username" => [
                        trans('api.validation.users.username.min')
                    ],
                    "password" => [
                        trans('api.validation.users.password.min')
                    ]
                ],
                "status_code" => 422
            ]);
    }

    /**
     * Test that updating a password requires that a matching confirmation password be passed
     *
     */
    public function testUpdateUserPasswordsDontMatch()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        $this->actingAs(App\User::find(1));

        $this->json('PUT', '/users/1', [
            'password' => '123456',
            'password_confirmation' => '1234567'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => "422 Unprocessable Entity",
                "errors" => [
                    "password" => [
                        trans('api.validation.users.password.confirmed')
                    ]
                ],
                "status_code" => 422
            ]);
    }

    /**
     * Test that updating a user requires that a unique email be chosen if it's changed
     *
     */
    public function testUpdateUserEmailUnique()
    {
        $this->json('POST', '/users', [
            'username' => 'firstuser',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'firstuser@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        // Create the second user
        $this->json('POST', '/users', [
            'username' => 'seconduser',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'seconduser@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        $this->actingAs(App\User::find(1));

        // Try and update the first user to the second user's email
        $this->json('PUT', '/users/1', [
            'email' => 'seconduser@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => "422 Unprocessable Entity",
                "errors" => [
                    "email" => [
                        trans('api.validation.users.email.unique')
                    ]
                ],
                "status_code" => 422
            ]);
    }

    /**
     * Test that updating a user requires that a unique username be chosen if it's changed
     *
     */
    public function testUpdateUserUsernameUnique()
    {
        $this->json('POST', '/users', [
            'username' => 'firstuser',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'firstuser@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        // Create the second user
        $this->json('POST', '/users', [
            'username' => 'seconduser',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'seconduser@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        $this->actingAs(App\User::find(1));

        // Try and update the first user to the second user's email
        $this->json('PUT', '/users/1', [
            'username' => 'seconduser'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => "422 Unprocessable Entity",
                "errors" => [
                    "username" => [
                        trans('api.validation.users.username.unique')
                    ]
                ],
                "status_code" => 422
            ]);
    }

    /**
     * Test that a user cannot update another user
     *
     */
    public function testUpdateOtherUserFails()
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
        $this->json('PUT', '/users/2', [
            'username' => 'testuser1234'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => "403 Forbidden",
                "status_code" => 403
            ]);
    }

    /**
     * Test that an administrator can perform an update on any user
     *
     */
    public function testAdministratorCanUpdateAnyUser()
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
        $this->json('PUT', '/users/2', [
            'username' => 'testuser1234'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson([
                "username" => "testuser1234"
            ]);
    }
}
