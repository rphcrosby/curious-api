<?php

namespace App\Repositories;

use Dingo\Api\Exception\StoreResourceFailedException;
use Auth;
use App\User;
use App\Invite;

class UserRepository extends Repository
{
    /**
     * The class that this repository is responsible for operating on
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $class = User::class;

    /**
     * Verify a user's credentials for authentication
     *
     * @param string $username
     * @param string $password
     */
    public function verify($username, $password)
    {
        $credentials = [
            'username' => $username,
            'password' => $password
        ];

        return Auth::attempt($credentials);
    }

    /**
     * Creates a new user
     *
     * @param array $data
     * @param string $invite
     * @return Illuminate\Database\Eloquent\Model
     */
    public function create($data, $inviteCode = null)
    {
        // If an invite is provided then find the invite and associate it with
        // the new user
        if ($inviteCode) {

            // Find the invite by email and invite code
            $invite = Invite::where('email', $data['email'])
                ->whereHas('inviter', function($q) use ($inviteCode)
                {
                    $q->where('invite_code', $inviteCode);
                })->firstOrFail();

            // Set the invite id on the data to associate it with the user when
            // it's created
            $data['invite_id'] = $invite->id;
        }

        $user = parent::create($data);

        return $user;
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
