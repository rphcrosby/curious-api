<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Contracts\Auth\Guard;

class InviteUserRequest extends Request
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
            'email' => 'required'
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
            'email.required' => 'The email field is required',
            'email.email' => 'The email field must be an email'
        ];
    }
}
