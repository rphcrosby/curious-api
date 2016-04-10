<?php

namespace App\Http\Controllers;

use Validator;
use Dingo\Api\Routing\Helpers;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;
use Authorizer;

class AuthenticationController extends ApiController
{
    use Helpers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Authenticates the client into the API
     *
     */
    public function client()
    {
        return response()->json(Authorizer::issueAccessToken());
    }

    /**
     * Authenticates the user into the app
     *
     */
    public function user()
    {
        $this->repository->verify($request->only([
            'username',
            'password'
        ]));

        return $this->response()->noContent();
    }
}
