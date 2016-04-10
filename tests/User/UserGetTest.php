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
        $first = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        $this->api('GET', '/users/10000', [], $token)
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
        $first = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        // Get the user
        $this->api('GET', '/users/1', [], $token)
            ->seeJson([
                "id" => $first->id,
                "username" => $first->username
            ]);
    }

    /**
     * Test that getting the currently authenticated user returns the user
     *
     */
    public function testGetMe()
    {
        $first = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        // Get the user
        $this->api('GET', '/users/me', [], $token)
            ->seeJson([
                "id" => $first->id,
                "username" => $first->username
            ]);
    }
}
