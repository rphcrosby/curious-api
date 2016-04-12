<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;
use App\Invite;

class InviteTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'invited',
        'inviter'
    ];

    public function transform(Invite $invite)
    {
        return [
            'id'            => (int) $invite->id,
            'code'          => $invite->inviter->invite_code,
            'email'         => $invite->email
        ];
    }

    /**
     * Include the invited user
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeInvited(Invite $invite)
    {
        return $this->item($invite->invited, new UserTransformer);
    }

    /**
     * Include the user who invited
     *
     * @return \League\Fractal\Resource\Item
     */
    public function includeInviter(Invite $invite)
    {
        return $this->item($invite->inviter, new UserTransformer);
    }
}
