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
}
