<?php

namespace App\Repositories;

use Illuminate\Auth\EloquentUserProvider;
use Dingo\Api\Exception\StoreResourceFailedException;
use App\User;
use App\Invite;
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
        $user = $this->show($userId);

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
        $user = $this->show($userId);
        $user->subscribers()->detach($subscriberId);
    }

    /**
     * Invites a user to the app
     *
     * @param int $id
     * @param string $email
     * @return void
     */
    public function invite($id, $email)
    {
        $user = $this->show($id);

        // If an invite has already been sent out for this user then throw an error
        if (Invite::where('email', $email)->exists()) {
            throw new StoreResourceFailedException(trans('api.invites.exists'));
        }

        // If the user has no invites left then throw an error
        if ($user->invite_count == 0) {
            throw new StoreResourceFailedException(trans('api.invites.insufficient'));
        }

        // Reduce the invite count for this user
        $user->invite_count -= 1;
        $user->save();

        // Create the invite
        $user->invites()->save(new Invite([
            'email' => $email
        ]));

        // TODO: Send an email/notification here
    }
}
