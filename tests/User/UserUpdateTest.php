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
        $first = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        // Update the user
        $this->api('PUT', "/users/{$first->id}", [
            'username' => 'testuser1234'
        ], $token)->seeJson([
            "username" => "testuser1234"
        ]);
    }

    /**
     * Test that updating certain fields requires they be set to a minimum length
     *
     */
    public function testUpdateUserMinLengthFields()
    {
        $first = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        $this->api('PUT', "/users/{$first->id}", [
            'username' => '123',
            'password' => '123',
            'password_confirmation' => '123'
        ], $token)->seeJsonEquals([
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
        $first = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        $this->api('PUT', "/users/{$first->id}", [
            'password' => '123456',
            'password_confirmation' => '1234567'
        ], $token)->seeJsonEquals([
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
        $first = factory(\App\User::class)->create();
        $second = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        // Try and update the first user to the second user's email
        $this->api('PUT', "/users/{$first->id}", [
            'email' => $second->email
        ], $token)->seeJsonEquals([
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
        $first = factory(\App\User::class)->create();
        $second = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        // Try and update the first user to the second user's email
        $this->api('PUT', "/users/{$first->id}", [
            'username' => $second->username
        ], $token)->seeJsonEquals([
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
        $first = factory(\App\User::class)->create();
        $second = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        // Update the user
        $this->api('PUT', "/users/{$second->id}", [
            'username' => 'testuser1234'
        ], $token)->seeJsonEquals([
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
        factory(\App\Role::class, 'admin')->create();
        $first = factory(\App\User::class, 'admin')->create();
        $second = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        // Update the user
        $this->api('PUT', "/users/{$second->id}", [
            'username' => 'testuser1234'
        ], $token)->seeJson([
            "username" => "testuser1234"
        ]);
    }
}
