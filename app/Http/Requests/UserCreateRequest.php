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
        return [
            'username' => 'required|min:4|unique:users',
            'password' => 'required|confirmed|min:6'
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
            'username.required' => 'The username field is required',
            'username.min' => 'The username must be at least 4 characters long',
            'username.unique' => 'The username must be unique',
            'password.required' => 'The password field is required',
            'password.confirmed' => 'The passwords do not match',
            'password.min' => 'The password must be at least 6 characters long'
        ];
    }
}
