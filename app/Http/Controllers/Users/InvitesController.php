<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\ApiController;
use App\Repositories\UserRepository;
use App\Http\Requests\InviteUserRequest;

class InvitesController extends ApiController
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
     * Subscribers a user to another user
     *
     * @param int $id
     */
    public function create(InviteUserRequest $request, $id)
    {
        $this->repository->invite($id, $request->input('email'));

        return $this->response->noContent();
    }
}
