<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection;
use App\Invite;

class InviteTransformer extends TransformerAbstract
{
    public function transform(Invite $invite)
    {
        return [
            'id'            => (int) $invite->id,
            'code'          => $invite->inviter->inviteCode,
            'email'         => $invite->email
        ];
    }
}
