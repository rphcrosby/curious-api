<?php

/*
|--------------------------------------------------------------------------
| Authenticated Tag Routes
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
    $api->get('/tags/{id}', [
        'uses' => 'TagsController@show'
    ]);

    /*
    |--------------------------------------------------------------------------
    | Subscribe/Unsubscribe Routes
    |--------------------------------------------------------------------------
    |
    | Users are able to subscribe or unsubscribe from a particular tag
    |
    */
    $api->post('/tags/{id}/subscribers', [
        'uses' => 'Tags\\SubscribersController@create'
    ]);

    $api->delete('/tags/{id}/subscribers', [
        'uses' => 'Tags\\SubscribersController@destroy'
    ]);
});
