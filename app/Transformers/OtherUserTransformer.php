<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;
use App\User;

class OtherUserTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'subscribers',
        'channels'
    ];

    public function transform(User $user)
    {
        return [
            'id'            => (int) $user->id,
            'username'      => $user->username
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
}
