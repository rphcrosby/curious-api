<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use App\Transformers\UserTransformer;
use App\Transformers\OtherUserTransformer;
use App\Transformers\TagTransformer;
use App\Tag;

class UserTagTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test subscribing a user to another user
     *
     */
    public function testUserSubscribesToTags()
    {
        $first = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        // Get the user
        $this->api('GET', "/users/me?include=tags", [], $token)
            ->seeJson([
                "tags" => [
                    "data" => []
                ]
            ]);

        // Subscribe the second user to the first
        $this->api('POST', "/users/{$first->id}/tags", [
            'tags' => [
                'tag1',
                'tag2',
            ]
        ], $token)->assertResponseStatus(204);

        // Get the user and check that it has the proper list of tags
        $resource = new Collection($first->tags, new TagTransformer);
        $tags = with(new Manager)->createData($resource)->toArray();
        $this->api('GET', "/users/me?include=tags", [], $token)
            ->seeJson($tags);

        // Check that the user has two tags they're subscribed to now
        $this->assertEquals(2, $first->tags->count());

        // Check that two were created
        $this->assertEquals(2, Tag::count());
    }

    /**
     * Test subscribing a user to another user
     *
     */
    public function testUserUnsubscribesFromTags()
    {
        $first = factory(\App\User::class)->create();
        $token = $this->authenticate($first);

        // Subscribe the second user to the first
        $this->api('POST', "/users/{$first->id}/tags", [
            'tags' => [
                'tag1',
                'tag2',
            ]
        ], $token);

        // Get the user and check that it has the proper list of tags
        $resource = new Collection($first->tags, new TagTransformer);
        $tags = with(new Manager)->createData($resource)->toArray();
        $this->api('GET', "/users/me?include=tags", [], $token)
            ->seeJson($tags);

        // Check that the user has two tags they're subscribed to now
        $this->assertEquals(2, $first->tags->count());

        // Now unsubscribe from one of the tags
        $this->api('DELETE', "/users/{$first->id}/tags", [
            'tags' => [
                'tag1'
            ]
        ], $token)->assertResponseStatus(204);

        $first->load('tags');

        // Check that the user now has just one tag
        $this->assertEquals('tag2', $first->tags->first()->name);
    }
}
