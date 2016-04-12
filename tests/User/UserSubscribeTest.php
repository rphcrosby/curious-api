<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use App\Transformers\UserTransformer;
use App\Transformers\OtherUserTransformer;
use App\Transformers\InviteTransformer;

class UserSubscribeTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test subscribing a user to another user
     *
     */
    public function testUserSubscribesToUser()
    {
        $first = factory(\App\User::class)->create();
        $second = factory(\App\User::class)->create();
        $token = $this->authenticate($second);

        // Get the user
        $this->api('GET', "/users/{$first->id}?include=subscribers", [], $token)
            ->seeJson([
                "subscribers" => [
                    "data" => []
                ]
            ]);

        // Subscribe the second user to the first
        $this->api('POST', "/users/{$first->id}/subscribers", [], $token)
            ->assertResponseStatus(204);

        // Get the user and check that it has the proper list of subscribers
        $resource = new Collection($first->subscribers, new OtherUserTransformer);
        $subscribers = with(new Manager)->createData($resource)->toArray();
        $this->api('GET', "/users/{$first->id}?include=subscribers", [], $token)
            ->seeJson($subscribers);

        // Subscribe the second user to the first AGAIN
        $this->api('POST', "/users/{$first->id}/subscribers", [], $token);

        // Double check they aren't subscribed twice
        $this->api('GET', "/users/{$first->id}?include=subscribers", [], $token)
            ->seeJson($subscribers);
    }

    /**
     * Test unsubscribing a user from another user
     *
     */
    public function testUserUnsubscribesFromUser()
    {
        $first = factory(\App\User::class)->create();
        $second = factory(\App\User::class)->create();
        $token = $this->authenticate($second);

        // Subscribe the second user to the first
        $this->api('POST', "/users/{$first->id}/subscribers", [], $token);

        // Get the first user
        $resource = new Collection($first->subscribers, new OtherUserTransformer);
        $subscribers = with(new Manager)->createData($resource)->toArray();
        $this->api('GET', "/users/{$first->id}?include=subscribers", [], $token)
            ->seeJson($subscribers);

        // Unsubscribe the second user from the first
        $this->api('DELETE', "/users/{$first->id}/subscribers", [], $token)
            ->assertResponseStatus(204);

        // Get the first user
        $this->api('GET', "/users/{$first->id}?include=subscribers", [], $token)
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
        $first = factory(\App\User::class)->create();
        $second = factory(\App\User::class)->create();
        $token = $this->authenticate($second);

        // Get the user
        $this->api('GET', "/users/{$second->id}?include=channels", [], $token)
            ->seeJson([
                "channels" => [
                    "data" => []
                ]
            ]);

        // Subscribe the second user to the first
        $this->api('POST', "/users/{$first->id}/subscribers", [], $token);

        // Get the user
        $resource = new Collection($second->channels, new UserTransformer);
        $channels = with(new Manager)->createData($resource)->toArray();
        $this->api('GET', "/users/me?include=channels", [], $token)
            ->seeJson($channels);
    }
}
