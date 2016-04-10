<?php

$api->post('/users', [
    'uses' => 'UsersController@create'
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

$api->post('/users/{id}/reports', [
    'uses' => 'Users\\ReportsController@create'
]);

$api->post('/users/{id}/subscribers/{subscriberId}', [
    'uses' => 'Users\\SubscribersController@create'
]);

$api->delete('/users/{id}/subscribers/{subscriberId}', [
    'uses' => 'Users\\SubscribersController@destroy'
]);
