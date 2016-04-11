<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers'
], function($api) {

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
        'uses' => 'AuthenticationController@clientAuthentication'
    ]);

    /*
    |--------------------------------------------------------------------------
    | User Authentication
    |--------------------------------------------------------------------------
    |
    | Authenticates the user into the app so they can use the API.
    |
    */
    $api->post('/authentication/user', [
        'uses' => 'AuthenticationController@userAuthentication'
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
        'middleware' => 'oauth'
    ], function($api)
    {
        /*
        |--------------------------------------------------------------------------
        | Main API Routes
        |--------------------------------------------------------------------------
        |
        | Routes for various aspects of the API are broken up into separate files
        | for easy maintainability.
        |
        */
        include 'Routes/tags.php';
        include 'Routes/users.php';
    });
});
