<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use App\Transformers\UserTransformer;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserDeleteRequest;
use Illuminate\Contracts\Auth\Guard;

class UsersController extends ApiController
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
     * Create a new user
     *
     * @param App\Http\Requests\UserCreateRequest $request
     */
    public function create(UserCreateRequest $request)
    {
        $user = $this->repository->create(
            $request->only([
                'username',
                'password',
                'email'
            ]),
            $request->input('invite'),
            $request->input('tags')
        );

        return $this->response->item($user, new UserTransformer);
    }

    /**
     * Show a user
     *
     * @param int $id
     */
    public function show($id)
    {
        $user = $this->repository->show($id);

        return $this->response->item($user, new UserTransformer);
    }

    /**
     * Show the currently authenticated user
     *
     * @param Illuminate\Contracts\Auth\Guard $auth
     */
    public function me(Guard $auth)
    {
        return $this->response->item($auth->user(), new UserTransformer);
    }

    /**
     * Update a username
     *
     * @param App\Http\Requests\UserUpdateRequest $request
     * @param int $id
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $user = $this->repository->update($id, array_filter($request->only([
            'username',
            'password'
        ])));

        return $this->response->item($user, new UserTransformer);
    }

    /**
     * Destroy a user
     *
     * @param int $id
     */
    public function destroy(UserDeleteRequest $request, $id)
    {
        $this->repository->destroy($id);

        return $this->response->noContent();
    }
}
