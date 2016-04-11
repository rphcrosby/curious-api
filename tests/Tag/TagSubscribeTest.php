<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Tag;
use App\User;

class TagSubscribeTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test that a user can subscribe to a tag
     *
     */
    public function testSubscribeToTag()
    {
        $tag = factory(Tag::class)->create();
        $user = factory(User::class)->create();
        $token = $this->authenticate($user);

        $this->api('POST', "/tags/{$tag->id}/subscribers", [], $token)
            ->assertResponseStatus(204);

        $this->api('GET', "/users/{$user->id}?include=tags", [], $token)
            ->seeJson([
                'name' => $tag->name
            ]);
    }

    /**
     * Test that subscribing a user to a tag more than once doesn't result in two tags being returned
     *
     */
    public function testUserCantSubscribeToTagMoreThanOnce()
    {
        $tag = factory(Tag::class)->create();
        $user = factory(User::class)->create();
        $token = $this->authenticate($user);

        $this->api('POST', "/tags/{$tag->id}/subscribers", [], $token);
        $this->api('POST', "/tags/{$tag->id}/subscribers", [], $token);

        $this->assertEquals(1, $user->tags->count());
    }

    /**
     * Test that a user can unsubscribe from a tag
     *
     */
    public function testUnsubscribeFromTag()
    {
        $tag = factory(Tag::class)->create();
        $user = factory(User::class)->create();
        $token = $this->authenticate($user);

        $this->api('POST', "/tags/{$tag->id}/subscribers", [], $token);
        $this->api('GET', "/users/{$user->id}?include=tags", [], $token)
            ->seeJson([
                'name' => $tag->name
            ]);

        $this->api('DELETE', "/tags/{$tag->id}/subscribers", [], $token)
            ->assertResponseStatus(204);

        $this->api('GET', "/users/{$user->id}?include=tags", [], $token)
            ->seeJson([
                'tags' => [
                    "data" => []
                ]
            ]);
    }
}
