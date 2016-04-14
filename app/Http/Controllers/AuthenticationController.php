<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Authorizer;
use Log;

class AuthenticationController extends ApiController
{
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
    public function clientAuthentication(Request $request)
    {
        // Fixes weird bug where passing these three values doesn't set it
        // properly on the request
        $request->request->add([
            'grant_type' => $request->input('grant_type'),
            'client_id' => $request->input('client_id'),
            'client_secret' => $request->input('client_secret')
        ]);

        Log::info('Client authentication attempt');

        return response()->json(Authorizer::issueAccessToken());
    }

    /**
     * Authenticates the user into the app
     *
     * @param Illuminate\Http\Request $request
     */
    public function userAuthentication(Request $request)
    {
        // Fixes weird bug where passing these three values doesn't set it
        // properly on the request
        $request->request->add([
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'grant_type' => $request->input('grant_type'),
            'client_id' => $request->input('client_id'),
            'client_secret' => $request->input('client_secret')
        ]);

        return response()->json(Authorizer::issueAccessToken());
    }
}
