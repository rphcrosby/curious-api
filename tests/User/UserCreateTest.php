<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Manager;
use App\Transformers\UserTransformer;
use App\Transformers\InviteTransformer;

class UserCreateTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test that creating a user requires that certain fields be set
     *
     */
    public function testCreateUserRequiredFields()
    {
        $this->api('POST', '/users', [])
            ->seeJsonEquals([
                "message" => "422 Unprocessable Entity",
                "errors" => [
                    "username" => [
                        trans('api.validation.users.username.required')
                    ],
                    "password" => [
                        trans('api.validation.users.password.required')
                    ],
                    "email" => [
                        trans('api.validation.users.email.required')
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
        $this->api('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123',
            'password_confirmation' => '123',
            'email' => 'test123@test.com'
        ])->seeJsonEquals([
            "message" => "422 Unprocessable Entity",
            "errors" => [
                "password" => [
                    trans('api.validation.users.password.min')
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
        $this->api('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '1234567',
            'email' => 'test123@test.com'
        ])->seeJsonEquals([
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
     * Test that creating a user requires that the username be set with a minimum length
     *
     */
    public function testCreateUserMinUsernameLength()
    {
        $this->api('POST', '/users', [
            'username' => 'tes',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ])->seeJsonEquals([
            "message" => "422 Unprocessable Entity",
            "errors" => [
                "username" => [
                    trans('api.validation.users.username.min')
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
        $this->api('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ]);

        $this->api('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test1234@test.com'
        ])->seeJsonEquals([
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
     * Test that creating a user requires that a unique email be chosen
     *
     */
    public function testCreateUserEmailUnique()
    {
        $this->api('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ]);

        $this->api('POST', '/users', [
            'username' => 'testuser1234',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ])->seeJsonEquals([
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
     * Test that creating a user returns the newly created user
     *
     */
    public function testCreateUserReturnsUser()
    {
        $this->api('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ])->seeJson([
            "id" => 1,
            "username" => "testuser123"
        ]);
    }

    /**
     * Test that a valid invite code is required if creating a user whilst in beta
     *
     */
    public function testCreateUserFailsUnlessValidInvite()
    {
        $user = factory(\App\User::class)->create();
        $token = $this->authenticate($user);

        // Invite a user via email
        $response = $this->api('POST', "/users/{$user->id}/invites", [
            'email' => 'test1234@test.com'
        ], $token);

        app('config')->set('curious.beta', true);

        // Try creating the user without the invite key added in beta mode
        $this->api('POST', '/users', [
            'username' => 'testuser1234',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test12345@test.com'
        ])->seeJsonEquals([
            "message" => "422 Unprocessable Entity",
            "errors" => [
                "invite" => [
                    trans('api.validation.users.invite.required')
                ]
            ],
            "status_code" => 422
        ]);

        // Try creating the user with an invalid invite key added
        $this->api('POST', '/users', [
            'username' => 'testuser1234',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test12345@test.com',
            'invite' => 'randomString1234'
        ])->seeJsonEquals([
            "message" => "422 Unprocessable Entity",
            "errors" => [
                "invite" => [
                    trans('api.validation.users.invite.invite')
                ]
            ],
            "status_code" => 422
        ]);

        // Try creating a user with a valid email but an invalid key
        $this->api('POST', '/users', [
            'username' => 'testuser1234',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test1234@test.com',
            'invite' => 'randomString1234'
        ])->seeJsonEquals([
            "message" => "422 Unprocessable Entity",
            "errors" => [
                "invite" => [
                    trans('api.validation.users.invite.invite')
                ]
            ],
            "status_code" => 422
        ]);

        // Try creating a user with a valid email but a valid invite key
        $this->api('POST', '/users', [
            'username' => 'testuser1234',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test1234@test.com',
            'invite' => $user->invite_code
        ])->seeJson([
            "username" => "testuser1234"
        ]);

        // Test that the new user is now associated with the invite
        $invited = App\User::where('username', 'testuser1234')->first();
        $resource = new Item($invited->invite, new InviteTransformer);
        $invites = with(new Manager)->createData($resource)->toArray();
        $this->api('GET', '/users/2?include=invite')->seeJson($invites);

        // Set back to false for the remaining tests
        app('config')->set('curious.beta', false);
    }
}
