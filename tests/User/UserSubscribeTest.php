<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use App\Transformers\UserTransformer;
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

        $user = App\User::find(1);
        $subscriber = App\User::find(2);

        // Login as the user
        $this->actingAs($subscriber);

        // Get the user
        $this->json('GET', '/users/1?include=subscribers', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson([
                "subscribers" => [
                    "data" => []
                ]
            ]);

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

        $user = App\User::find(1);
        $subscriber = App\User::find(2);

        // Login as the user
        $this->actingAs($subscriber);

        // Get the user
        $this->json('GET', '/users/2?include=channels', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson([
                "channels" => [
                    "data" => []
                ]
            ]);

        // Subscribe the second user to the first
        $this->json('POST', '/users/1/subscribers', [], ['accept' => 'application/vnd.curious.v1+json']);

        // Get the user
        $resource = new Collection($subscriber->channels, new UserTransformer);
        $channels = with(new Manager)->createData($resource)->toArray();
        $this->json('GET', '/users/2?include=channels', [], ['accept' => 'application/vnd.curious.v1+json'])
            ->seeJson($channels);
    }
}
