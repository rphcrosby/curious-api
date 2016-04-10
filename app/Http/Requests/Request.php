<?php

namespace App\Http\Requests;

use Dingo\Api\Http\FormRequest;

abstract class Request extends FormRequest
{
    protected function ownerOrAdministrator($user, $id)
    {
        if ($user->id == $id) {
            return true;
        }

        if ($user->is('administrator')) {
            return true;
        }

        return false;
    }
}
