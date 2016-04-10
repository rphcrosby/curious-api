<?php

$app->post('/tags', [
    'uses' => 'UsersController@create'
]);

$app->get('/tags/{id}', [
    'uses' => 'AnswersController@show'
]);

$app->post('/tags/{id}/subscribers', [
    'uses' => 'Tags\\SubscribersController@create'
]);

$app->delete('/tags/{id}/subscribers', [
    'uses' => 'Tags\\SubscribersController@destroy'
]);
