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
        $first = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        // Delete the user
        $this->api('DELETE', "/users/{$first->id}", [], $token)
            ->assertResponseStatus(204);
    }

    /**
     * Test that a user cannot delete another user
     *
     */
    public function testDeleteOtherUserFails()
    {
        $first = factory(\App\User::class)->create();
        $second = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        // Delete the user
        $this->api('DELETE', "/users/{$second->id}", [], $token)
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
        factory(\App\Role::class, 'admin')->create();
        $first = factory(\App\User::class, 'admin')->create();
        $second = factory(\App\User::class)->create();

        $token = $this->authenticate($first);

        // Delete the user
        $this->api('DELETE', "/users/{$second->id}", [], $token)
            ->assertResponseStatus(204);
    }
}
