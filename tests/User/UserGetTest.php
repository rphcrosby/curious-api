<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use App\Transformers\UserTransformer;
use App\Transformers\InviteTransformer;

class UserGetTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test that trying to retrieve a non existent user returns a proper resource not found error
     *
     */
    public function testGetNonexistentUserReturnsNotFound()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        $user = App\User::find(1);
        $this->actingAs($user);

        $this->json('GET', '/users/10000', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJsonEquals([
                "message" => trans('api.errors.resource.missing'),
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

        $user = App\User::find(1);
        $this->actingAs($user);

        // Get the user
        $this->json('GET', '/users/1', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson([
                "id" => 1,
                "username" => "testuser123"
            ]);
    }

    /**
     * Test that getting the currently authenticated user returns the user
     *
     */
    public function testGetMe()
    {
        // Create the user
        $this->json('POST', '/users', [
            'username' => 'testuser123',
            'password' => '123456',
            'password_confirmation' => '123456',
            'email' => 'test123@test.com'
        ], ['accept' => 'application/vnd.curious.v1+json']);

        $user = App\User::find(1);
        $this->actingAs($user);

        // Get the user
        $this->json('GET', '/users/me', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson([
                "id" => 1,
                "username" => "testuser123"
            ]);
    }
}
