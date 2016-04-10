<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Beta
    |--------------------------------------------------------------------------
    |
    | Whilst in beta mode, a number of features apply such as only being able
    | to create accounts using invite codes. When the app goes live, disable this.
    |
    */
    'beta' => env('APP_BETA', false),

    /*
    |--------------------------------------------------------------------------
    | Invites
    |--------------------------------------------------------------------------
    |
    | Spcifies how many invites a new user is awarded when they signup. This only
    | applies during the beta launch of the app.
    |
    */

    'invites' => 5,
];
