<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Tag;
use App\User;

class TagGetTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test that a user can get basic information about a tag
     *
     */
    public function testGetTag()
    {
        $tag = factory(Tag::class)->create();
        $user = factory(User::class)->create();
        $token = $this->authenticate($user);

        $this->api('GET', "/tags/{$tag->id}", [], $token)
            ->seeJson([
                'name' => $tag->name
            ]);
    }
}
