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

$api->get('/users/{id}/invites', [
    'uses' => 'Users\\InvitesController@index'
]);

$api->post('/users/{id}/invites', [
    'uses' => 'Users\\InvitesController@create'
]);

$api->post('/users/{id}/subscribers', [
    'uses' => 'Users\\SubscribersController@create'
]);

$api->delete('/users/{id}/subscribers', [
    'uses' => 'Users\\SubscribersController@destroy'
]);
