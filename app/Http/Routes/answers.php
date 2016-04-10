<?php

$app->get('/answers/{id}', [
    'uses' => 'AnswersController@show'
]);

$app->put('/answers/{id}', [
    'uses' => 'AnswersController@update'
]);

$app->delete('/answers/{id}', [
    'uses' => 'AnswersController@destroy'
]);

$app->post('/answers/{id}/upvote', [
    'uses' => 'AnswersController@upvote'
]);

$app->post('/answers/{id}/downvote', [
    'uses' => 'AnswersController@downvote'
]);

$app->post('/answers/{id}/reports', [
    'uses' => 'Answers\\ReportsController@create'
]);
