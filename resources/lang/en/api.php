<?php

return [

    'errors' => [
        'resource' => [
            'missing' => 'Resource could not be found'
        ]
    ],

    'validation' => [
        'users' => [
            'username' => [
                'required' => 'The username field is required',
                'min' => 'The username must be at least 4 characters long',
                'unique' => 'That username is already taken'
            ],
            'password' => [
                'required' => 'The password field is required',
                'confirmed' => 'The passwords do not match',
                'min' => 'The password must be at least 6 characters long',
            ],
            'email' => [
                'required' => 'The email field is required',
                'email' => 'The email field must be a valid email',
                'unique' => 'That email address is already taken'
            ],
            'invite' => [
                'required' => 'The invite field is required',
                'invite' => 'No invite exists matching this email address and code'
            ]
        ]
    ],

    'invites' => [
        'exists' => 'A user with that email address has already been invited',
        'insufficient' => "You don't have any more invites. Please contact support if you'd like more."
    ]
];
