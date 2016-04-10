<?php

namespace App\Repositories;

use Illuminate\Auth\EloquentUserProvider;
use App\User;
use Auth;

class UserRepository extends Repository
{
    protected $class = User::class;

    /**
     * Verify the user's username and password
     *
     * @param string $username
     * @param string $password
     * @return int|bool
     */
    public function verify($username, $password)
    {
        $credentials = [
            'username' => $username,
            'password' => $password
        ];

        if (!Auth::attempt($credentials)) {
            return false;
        }

        return Auth::id();
    }

    /**
     * Subscribes a user to another user
     *
     * @param int $user_id
     * @param int $subscriber_id
     */
    public function subscribe($userId, $subscriberId)
    {
        try {
            $user = with(new $this->class)
                ->with('subscribers')
                ->findOrFail($userId);
            $subscriber = with(new $this->class)->findOrFail($subscriberId);
        } catch (ModelNotFoundException $e) {
            throw new ResourceException('Resource could not be found');
        }

        $subscribers = $user->subscribers
            ->lists('id')
            ->push($subscriberId)
            ->unique()
            ->toArray();

        $user->subscribers()->sync($subscribers);
    }

    /**
     * Subscribes a user to another user
     *
     * @param int $user_id
     * @param int $subscriber_id
     */
    public function unsubscribe($userId, $subscriberId)
    {
        try {
            $user = with(new $this->class)->findOrFail($userId);
            $subscriber = with(new $this->class)->findOrFail($subscriberId);
        } catch (ModelNotFoundException $e) {
            throw new ResourceException('Resource could not be found');
        }

        $user->subscribers()->detach($subscriberId);
    }
}
