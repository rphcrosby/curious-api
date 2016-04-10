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
        return $this->ownerOrAdministrator($auth->user(), $this->route('id'));
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
            'email' => 'email|unique:users'
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
            'username.min' => trans('api.validation.users.username.min'),
            'username.unique' => trans('api.validation.users.username.unique'),
            'password.confirmed' => trans('api.validation.users.password.confirmed'),
            'password.min' => trans('api.validation.users.password.min'),
            'email.email' => trans('api.validation.users.email.email'),
            'email.unique' => trans('api.validation.users.email.unique')
        ];
    }
}
