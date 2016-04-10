<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [], function($api) {

    /*
    |--------------------------------------------------------------------------
    | OAuth Client Authentication
    |--------------------------------------------------------------------------
    |
    | This route must be requested first by a client to authenticate them for
    | using the API.
    |
    */
    $api->post('/authentication/client', [
        'uses' => 'AuthenticationController@client'
    ]);

    /*
    |--------------------------------------------------------------------------
    | OAuth Routes
    |--------------------------------------------------------------------------
    |
    | Nearly all routes in curious require the client to authenticate using oauth
    |
    */
    $api->group([
        'middleware' => 'oauth',
        'namespace' => 'App\Http\Controllers'
    ], function($api)
    {
        /*
        |--------------------------------------------------------------------------
        | User Authentication
        |--------------------------------------------------------------------------
        |
        | Authenticates the user into the app so they can use the API.
        |
        */
        $api->post('/authentication/user', [
            'uses' => 'AuthenticationController@user'
        ]);

        /*
        |--------------------------------------------------------------------------
        | OAuth Routes
        |--------------------------------------------------------------------------
        |
        | Nearly all routes in curious require the client to authenticate using oauth
        |
        */
        include 'Routes/users.php';
    });
});
