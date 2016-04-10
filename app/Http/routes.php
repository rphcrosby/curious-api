<?php

$api = app('Dingo\Api\Routing\Router');

Route::post('users/authentication', function()
{
    return response()->json(Authorizer::issueAccessToken());
});

$api->version('v1', ['middleware' => 'oauth'], function($api)
{
    $api->group(['namespace' => 'App\Http\Controllers'], function($api)
    {
        include 'Routes/users.php';
    });
});

// $app->group([
//     'namespace' => 'App\Http\Controllers'
// ], function() use ($app)
// {
//     include 'Routes/answers.php';
//     include 'Routes/feed.php';
//     include 'Routes/oauth.php';
//     include 'Routes/questions.php';
//     include 'Routes/search.php';
//     include 'Routes/tags.php';
//     //include 'Routes/users.php';
// });
