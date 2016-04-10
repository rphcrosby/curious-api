<?php

$app->post('/questions', [
    'uses' => 'QuestionsController@create'
]);

$app->get('/questions/{id}', [
    'uses' => 'QuestionsController@show'
]);

$app->put('/questions/{id}', [
    'uses' => 'QuestionsController@update'
]);

$app->delete('/questions/{id}', [
    'uses' => 'QuestionsController@destroy'
]);

$app->post('/questions/{id}/reports', [
    'uses' => 'Questions\\ReportsController@create'
]);

$app->post('/questions/{id}/answers', [
    'uses' => 'Questions\\AnswersController@create'
]);
