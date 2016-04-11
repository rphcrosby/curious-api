<?php

namespace App\Repositories;

use App\Invite;

class InviteRepository extends Repository
{
    /**
     * Find an invite by it's code and email
     *
     * @param string $code
     * @param string $email
     * @return App\Invite
     */
    public function find($code, $email)
    {
        return Invite::where('email', $email)
            ->whereHas('inviter', function($q) use ($code)
            {
                $q->where('invite_code', $code);
            })->firstOrFail();
    }
}
