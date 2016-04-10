<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserEndpointsTest extends TestCase
{
    use DatabaseMigrations;

    public function testCreateUserRequiredFields()
    {
        $this->json('POST', '/users', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => "422 Unprocessable Entity",
                "errors" => [
                    "username" => [
                        "The username field is required"
                    ],
                    "password" => [
                        "The password field is required"
                    ]
                ],
                "status_code" => 422
            ]);
    }

    public function testCreateUserMinPasswordLength()
    {
        $this->json('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123',
            'password_confirmation' => '123'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => "422 Unprocessable Entity",
                "errors" => [
                    "password" => [
                        "The password must be at least 6 characters long"
                    ]
                ],
                "status_code" => 422
            ]);
    }

    public function testCreateUserPasswordsDontMatch()
    {
        $this->json('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '1234567'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => "422 Unprocessable Entity",
                "errors" => [
                    "password" => [
                        "The passwords do not match"
                    ]
                ],
                "status_code" => 422
            ]);
    }

    public function testCreateUserMinUsernameLength()
    {
        $this->json('POST', '/users', [
            'username' => 'tes',
            'password' => '123456',
            'password_confirmation' => '123456'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => "422 Unprocessable Entity",
                "errors" => [
                    "username" => [
                        "The username must be at least 4 characters long"
                    ]
                ],
                "status_code" => 422
            ]);
    }

    public function testCreateUserUsernameUnique()
    {
        $this->json('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        $this->json('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => "422 Unprocessable Entity",
                "errors" => [
                    "username" => [
                        "The username must be unique"
                    ]
                ],
                "status_code" => 422
            ]);
    }

    public function testCreateUserReturnsUser()
    {
        $this->json('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "data" => [
                    "id" => 1,
                    "username" => "testuser123"
                ]
            ]);
    }

    public function testGetNonexistentUserReturnsNotFound()
    {
        $this->json('GET', '/users/10000', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => "Resource could not be found",
                "status_code" => 422
            ]);
    }

    public function testGetUser()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        // Get the user
        $this->json('GET', '/users/1', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "data" => [
                    "id" => 1,
                    "username" => "testuser123"
                ]
            ]);
    }

    public function testUpdateUser()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        $this->actingAs(App\User::find(1));

        // Update the user
        $this->json('PUT', '/users/1', [
            'username' => 'testuser1234'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "data" => [
                    "id" => 1,
                    "username" => "testuser1234"
                ]
            ]);
    }

    public function testUpdateOtherUserFails()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'firstuser',
            'password' => '123456',
            'password_confirmation' => '123456'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        // Create another user
        $this->json('POST', '/users', [
            'username' => 'seconduser',
            'password' => '123456',
            'password_confirmation' => '123456'
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

    public function testAdministratorCanUpdateAnyUser()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'firstuser',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role_id' => 1
        ], ['accept' => 'application/vnd.curious.v1+json']);

        // Create another user
        $this->json('POST', '/users', [
            'username' => 'seconduser',
            'password' => '123456',
            'password_confirmation' => '123456'
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
            ->seeJsonEquals([
                "data" => [
                    "id" => 2,
                    "username" => "testuser1234"
                ]
            ]);
    }

    public function testDeleteUser()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        $this->actingAs(App\User::find(1));

        // Delete the user
        $this->json('DELETE', '/users/1', [
            'username' => 'testuser1234'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->assertResponseStatus(204);
    }

    public function testDeleteOtherUserFails()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'firstuser',
            'password' => '123456',
            'password_confirmation' => '123456'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        // Create another user
        $this->json('POST', '/users', [
            'username' => 'seconduser',
            'password' => '123456',
            'password_confirmation' => '123456'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        $this->actingAs(App\User::find(1));

        // Update the user
        $this->json('DELETE', '/users/2', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => "403 Forbidden",
                "status_code" => 403
            ]);
    }

    public function testAdministratorCanDeleteAnyUser()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'firstuser',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role_id' => 1
        ], ['accept' => 'application/vnd.curious.v1+json']);

        // Create another user
        $this->json('POST', '/users', [
            'username' => 'seconduser',
            'password' => '123456',
            'password_confirmation' => '123456'
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
