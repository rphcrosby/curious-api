<?php

namespace App\Repositories;

use App\Repositories\UserRepository;
use App\User;
use App\Tag;

class TagRepository extends Repository
{
    /**
     * The class that this repository is responsible for operating on
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $class = Tag::class;

    /**
     * Subscribes a user to a tag
     *
     * @param int $id
     * @param App\User $user
     * @return void
     */
    public function subscribe($id, User $user)
    {
        return $user->tags()->sync($user->tags
            ->lists('id')
            ->push($id)
            ->unique()
            ->toArray()
        );
    }

    /**
     * Unsubscribes a user from a tag
     *
     * @param int $id
     * @param App\User $user
     * @return void
     */
    public function unsubscribe($id, User $user)
    {
        return $user->tags()->detach($id);
    }

    /**
     * Subscribes a user to an array of tag names
     *
     * @param array $tags
     * @param App\User $user
     * @return void
     */
    public function subscribeToNames(array $tags, User $user)
    {
        $existing = $user->tags;

        $tags = $this->findByNameOrCreate($tags);

        $new = $existing->merge($tags);

        return $user->tags()->sync($new->lists('id')->toArray());
    }

    /**
     * Unsubscribes a user from an array of tag names
     *
     * @param array $tags
     * @param App\User $user
     * @return void
     */
    public function unsubscribeFromNames(array $tags, User $user)
    {
        return $user->tags()->detach(
            $this->findByName($tags)->lists('id')->toArray()
        );
    }

    /**
     * Create tags using an array of names
     *
     * @param array $names
     * @return Illuminate\Support\Collection
     */
    public function insert(array $names = [])
    {
        return collect($names)->map(function($name)
        {
            return Tag::create(['name' => $name]);
        });
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
        $existing = $this->findByName($names);

        // Get the tag names that don't exist yet
        $missing = collect($names)->diff(collect($existing)->lists('name'));

        // Create any new tags
        $new = $this->insert($names);

        // Return all of the tags
        return $existing->merge($new);
    }
}
