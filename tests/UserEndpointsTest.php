<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use App\Transformers\UserTransformer;
use App\Transformers\InviteTransformer;

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
                    ],
                    "email" => [
                        "The email field is required"
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
            'password_confirmation' => '123',
            'email' => 'test123@test.com'
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
            'password_confirmation' => '1234567',
            'email' => 'test123@test.com'
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
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
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
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        $this->json('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test1234@test.com'
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
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson([
                "id" => 1,
                "username" => "testuser123"
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
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        // Get the user
        $this->json('GET', '/users/1', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson([
                "id" => 1,
                "username" => "testuser123"
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
            'password_confirmation' => '123',
            'email' => 'test123@test.com'
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
            'email' => 'test123@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        // Create another user
        $this->json('POST', '/users', [
            'username' => 'seconduser',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test124@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        // Get the user
        $this->json('GET', '/users/1?include=subscribers', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson([
                "subscribers" => [
                    "data" => []
                ]
            ]);

        $user = App\User::find(1);
        $subscriber = App\User::find(2);

        // Login as the user
        $this->actingAs($subscriber);

        // Subscribe the second user to the first
        $this->json('POST', '/users/1/subscribers', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->assertResponseStatus(204);

        // Get the user and check that it has the proper list of subscribers
        $resource = new Collection($user->subscribers, new UserTransformer);
        $subscribers = with(new Manager)->createData($resource)->toArray();
        $this->json('GET', '/users/1?include=subscribers', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson($subscribers);

        // Subscribe the second user to the first AGAIN
        $this->json('POST', '/users/1/subscribers', [], ['accept' => 'application/vnd.curious.v1+json']);

        // Double check they aren't subscribed twice
        $this->json('GET', '/users/1?include=subscribers', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson($subscribers);
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
            'email' => 'test123@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        // Create another user
        $this->json('POST', '/users', [
            'username' => 'seconduser',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test1234@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        $user = App\User::find(1);
        $subscriber = App\User::find(2);

        // Login as the user
        $this->actingAs($subscriber);

        // Subscribe the second user to the first
        $this->json('POST', '/users/1/subscribers', [], ['accept' => 'application/vnd.curious.v1+json']);

        // Get the first user
        $resource = new Collection($user->subscribers, new UserTransformer);
        $subscribers = with(new Manager)->createData($resource)->toArray();
        $this->json('GET', '/users/1?include=subscribers', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson($subscribers);

        // Unsubscribe the second user from the first
        $this->json('DELETE', '/users/1/subscribers', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->assertResponseStatus(204);

        // Get the first user
        $this->json('GET', '/users/1?include=subscribers', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson([
                "subscribers" => [
                    "data" => []
                ]
            ]);
    }

    /**
     * Test subscribing a user to another user shows up in their channels
     *
     */
    public function testChannelsInclude()
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
        $this->json('GET', '/users/2?include=channels', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson([
                "channels" => [
                    "data" => []
                ]
            ]);

        $user = App\User::find(1);
        $subscriber = App\User::find(2);

        // Login as the user
        $this->actingAs($subscriber);

        // Subscribe the second user to the first
        $this->json('POST', '/users/1/subscribers', [], ['accept' => 'application/vnd.curious.v1+json']);

        // Get the user
        $resource = new Collection($subscriber->channels, new UserTransformer);
        $channels = with(new Manager)->createData($resource)->toArray();
        $this->json('GET', '/users/2?include=channels', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson($channels);
    }

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
