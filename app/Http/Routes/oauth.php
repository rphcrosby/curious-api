<?php

$app->post('oauth/access_token', function() {
    return response()->json(Authorizer::issueAccessToken());
});
