<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;
use App\User;

class UserTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'subscribers',
        'channels',
        'invites',
        'invite'
    ];

    public function transform(User $user)
    {
        return [
            'id'            => (int) $user->id,
            'username'      => $user->username,
            'email'         => $user->email,
            'invite_code'   => $user->inviteCode,
            'invite_count'  => (int) $user->invite_count
        ];
    }

    /**
     * Include subscribers
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeSubscribers(User $user)
    {
        return $this->collection($user->subscribers, new static);
    }

    /**
     * Include subscribers
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeChannels(User $user)
    {
        return $this->collection($user->channels, new static);
    }

    /**
     * Include subscribers
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeInvites(User $user)
    {
        return $this->collection($user->invites, new InviteTransformer);
    }

    /**
     * Include subscribers
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeInvite(User $user)
    {
        return $this->item($user->invite, new InviteTransformer);
    }
}
