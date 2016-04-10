<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Transformers\UserTransformer;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserCreateRequest;
use Validator;

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
        $user = $this->repository->create($request->only([
            'username',
            'password'
        ]));

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
    public function destroy($id)
    {
        $this->repository->destroy($id);

        return $this->response->noContent();
    }
}
