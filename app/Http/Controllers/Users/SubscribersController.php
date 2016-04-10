<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\ApiController;
use App\Repositories\UserRepository;

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
     * @param int $userId
     * @param int $subscriberId
     */
    public function create($userId, $subscriberId)
    {
        $this->repository->subscribe($userId, $subscriberId);

        return $this->response->noContent();
    }

    /**
     * Unsubscribers a user from another user
     *
     * @param int $userId
     * @param int $subscriberId
     */
    public function destroy($userId, $subscriberId)
    {
        $this->repository->unsubscribe($userId, $subscriberId);

        return $this->response->noContent();
    }
}
