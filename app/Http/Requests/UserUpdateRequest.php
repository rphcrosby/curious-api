<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Contracts\Auth\Guard;
use Dingo\Api\Exception\UpdateResourceFailedException;
use App\User;

class UserUpdateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @param Illuminate\Contracts\Auth\Guard $auth
     * @return bool
     */
    public function authorize(Guard $auth)
    {
        if ($auth->id() == $this->route('id')) {
            return true;
        }

        if ($auth->user()->is('administrator')) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'min:4|unique:users',
            'password' => 'confirmed|min:6',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'username.min' => 'The username must be at least 4 characters long',
            'username.unique' => 'The username must be unique',
            'password.confirmed' => 'The passwords do not match',
            'password.min' => 'The password must be at least 6 characters long'
        ];
    }
}
