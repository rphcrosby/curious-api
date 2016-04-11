<?php

namespace App\Repositories;

use App\Repositories\UserRepository;
use App\User;
use App\Tag;

class TagRepository extends Repository
{
    /**
     * Subscribes a user to a tag
     *
     * @param array $tags
     * @param App\User $user
     * @return void
     */
    public function subscribe(array $tags, User $user)
    {
        $tags = $this->findByNameOrCreate($tags);

        return $user->tags()->sync($tags->lists('id'));
    }

    /**
     * Create tags using an array of names
     *
     * @param array $names
     * @return Illuminate\Support\Collection
     */
    public function insert(array $names = [])
    {
        return Tag::insert(collect($names)->map(function($name)
        {
            return ['name' => $name];
        })->toArray());
    }

    /**
     * Fetch tags by name
     *
     * @param array $names
     * @return Illuminate\Support\Collection
     */
    public function findByName(array $names = [])
    {
        return Tag::whereIn('name', $names)->get();
    }

    /**
     * Find all tags by name or create them if they don't exist
     *
     * @param array $names
     * @return Illuminate\Support\Collection
     */
    public function findByNameOrCreate(array $names = [])
    {
        // Find existing tags
        $existing = $htis->findByName($names);

        // Get the tag names that don't exist yet
        $missing = collect($names)->diff(collect($existing)->lists('name'));

        // Create any new tags
        $new = $this->insert($names);

        // Return all of the tags
        return $existing->merge($new);
    }
}
