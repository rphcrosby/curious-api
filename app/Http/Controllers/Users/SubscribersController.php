<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\ApiController;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\Guard;

class SubscribersController extends ApiController
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
     * @param Illuminate\Contracts\Auth\Guard $auth
     * @param int $id
     */
    public function create(Guard $auth, $id)
    {
        $this->repository->subscribe($id, $auth->id());

        return $this->response->noContent();
    }

    /**
     * Unsubscribers a user from another user
     *
     * @param Illuminate\Contracts\Auth\Guard $auth
     * @param int $id
     */
    public function destroy(Guard $auth, $id)
    {
        $this->repository->unsubscribe($id, $auth->id());

        return $this->response->noContent();
    }
}
