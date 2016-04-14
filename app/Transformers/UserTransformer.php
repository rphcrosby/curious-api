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
        'invite',
        'tags'
    ];

    public function transform(User $user)
    {
        return [
            'id'            => (int) $user->id,
            'username'      => $user->username,
            'email'         => $user->email,
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
     * Include invites this user has created for others to join the app
     *
     * @return \League\Fractal\Resource\Collection
     */
    public function includeInvites(User $user)
    {
        $user->load('invites');
        return $this->collection($user->invites, new InviteTransformer);
    }

    /**
     * Include the invite that this user used to join the app
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeInvite(User $user)
    {
        return $this->item($user->invite, new InviteTransformer);
    }

    /**
     * Include tags this user is subscribed to
     *
     * @return \League\Fractal\Resource\Collection
     */
    public function includeTags(User $user)
    {
        $user->load('tags');
        return $this->collection($user->tags, new TagTransformer);
    }
}
