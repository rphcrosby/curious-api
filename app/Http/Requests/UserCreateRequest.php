<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UserCreateRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'username' => 'required|min:4|unique:users',
            'password' => 'required|confirmed|min:6',
            'email' => 'required|email|unique:users'
        ];

        // If the app is in beta then require the user to use an invite
        if (config('curious.beta')) {
            $rules['invite'] = 'required|invite';
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'username.required' => trans('api.validation.users.username.required'),
            'username.min' => trans('api.validation.users.username.min'),
            'username.unique' => trans('api.validation.users.username.unique'),
            'password.required' => trans('api.validation.users.password.required'),
            'password.confirmed' => trans('api.validation.users.password.confirmed'),
            'password.min' => trans('api.validation.users.password.min'),
            'email.required' => trans('api.validation.users.email.required'),
            'email.email' => trans('api.validation.users.email.email'),
            'email.unique' => trans('api.validation.users.email.unique'),
            'invite.required' => trans('api.validation.users.invite.required'),
            'invite.invite' => trans('api.validation.users.invite.invite'),
        ];
    }
}
