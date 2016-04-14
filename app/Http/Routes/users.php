<?php

/*
|--------------------------------------------------------------------------
| Basic User Routes
|--------------------------------------------------------------------------
|
| The following routes can be requested by the client without the user
| authenticating first.
|
*/

$api->post('/users', [
    'uses' => 'UsersController@create'
]);

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
|
| These routes all require the user to be authenticated before making the
| call.
|
*/
$api->group([
    'middleware' => 'auth'
], function($api)
{
    $api->get('/users/me', [
        'uses' => 'UsersController@me'
    ]);

    $api->get('/users/{id}', [
        'uses' => 'UsersController@show'
    ]);

    $api->put('/users/{id}', [
        'uses' => 'UsersController@update'
    ]);

    $api->delete('/users/{id}', [
        'uses' => 'UsersController@destroy'
    ]);

    /*
    |--------------------------------------------------------------------------
    | Reporting
    |--------------------------------------------------------------------------
    |
    | Routes for reporting a user
    |
    */

    $api->post('/users/{id}/reports', [
        'uses' => 'Users\\ReportsController@create'
    ]);

    /*
    |--------------------------------------------------------------------------
    | Invites
    |--------------------------------------------------------------------------
    |
    | Routes for generating invites
    |
    */

    $api->post('/users/{id}/invites', [
        'uses' => 'Users\\InvitesController@create'
    ]);

    /*
    |--------------------------------------------------------------------------
    | Subscribers
    |--------------------------------------------------------------------------
    |
    | Routes for subscribing and unsubscribing to a user
    |
    */

    $api->post('/users/{id}/subscribers', [
        'uses' => 'Users\\SubscribersController@create'
    ]);

    $api->delete('/users/{id}/subscribers', [
        'uses' => 'Users\\SubscribersController@destroy'
    ]);

    /*
    |--------------------------------------------------------------------------
    | Tags
    |--------------------------------------------------------------------------
    |
    | Routes for subscribing and unsubscribing to a tag
    |
    */

    $api->post('/users/{id}/tags', [
        'uses' => 'Users\\TagsController@create'
    ]);

    $api->delete('/users/{id}/tags', [
        'uses' => 'Users\\TagsController@destroy'
    ]);
});
