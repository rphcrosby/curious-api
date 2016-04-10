<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserEndpointsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test that creating a user requires that certain fields be set
     *
     */
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

    /**
     * Test that creating a user requires that the password be set with a minimum length
     *
     */
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

    /**
     * Test that creating a user requires the user enter in a matching confirmation password
     *
     */
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

    /**
     * Test that creating a user requires that the username be set with a minimum length
     *
     */
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

    /**
     * Test that creating a user requires that a unique username be chosen
     *
     */
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

    /**
     * Test that creating a user returns the newly created user
     *
     */
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

    /**
     * Test that trying to retrieve a non existent user returns a proper resource not found error
     *
     */
    public function testGetNonexistentUserReturnsNotFound()
    {
        $this->json('GET', '/users/10000', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => "Resource could not be found",
                "status_code" => 422
            ]);
    }

    /**
     * Test that getting a user returns the user
     *
     */
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
            'password_confirmation' => '123456'
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
                        "The username must be at least 4 characters long"
                    ],
                    "password" => [
                        "The password must be at least 6 characters long"
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
            'password_confirmation' => '123456'
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
                        "The passwords do not match"
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
            'password_confirmation' => '123456'
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

    /**
     * Test subscribing a user to another user
     *
     */
    public function testUserSubscribesToUser()
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

        // Get the user
        $this->json('GET', '/users/1?include=subscribers', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "data" => [
                    "id" => 1,
                    "username" => "firstuser",
                    "subscribers" => [
                        "data" => []
                    ]
                ]
            ]);

        $user = App\User::find(2);

        // Login as the user
        $this->actingAs($user);

        // Subscribe the second user to the first
        $this->json('POST', '/users/1/subscribers', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->assertResponseStatus(204);

        // Get the user
        $this->json('GET', '/users/1?include=subscribers', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "data" => [
                    "id" => 1,
                    "username" => "firstuser",
                    "subscribers" => [
                        "data" => [[
                            "id" => 2,
                            "username" => "seconduser"
                        ]]
                    ]
                ]
            ]);
    }

    /**
     * Test unsubscribing a user from another user
     *
     */
    public function testUserUnsubscribesFromUser()
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

        $user = App\User::find(2);

        // Login as the user
        $this->actingAs($user);

        // Subscribe the second user to the first
        $this->json('POST', '/users/1/subscribers', [], ['accept' => 'application/vnd.curious.v1+json']);

        // Get the first user
        $this->json('GET', '/users/1?include=subscribers', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "data" => [
                    "id" => 1,
                    "username" => "firstuser",
                    "subscribers" => [
                        "data" => [[
                            "id" => 2,
                            "username" => "seconduser"
                        ]]
                    ]
                ]
            ]);

        // Unsubscribe the second user from the first
        $this->json('DELETE', '/users/1/subscribers', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->assertResponseStatus(204);

        // Get the first user
        $this->json('GET', '/users/1?include=subscribers', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "data" => [
                    "id" => 1,
                    "username" => "firstuser",
                    "subscribers" => [
                        "data" => []
                    ]
                ]
            ]);
    }
}
