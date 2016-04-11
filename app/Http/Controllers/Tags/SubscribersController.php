<?php

namespace App\Http\Controllers\Tags;

use App\Http\Controllers\ApiController;
use App\Repositories\TagRepository;
use Illuminate\Contracts\Auth\Guard;

class SubscribersController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @param App\Repositories\UserRepository $repository
     * @return void
     */
    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Subscribers a user to a tag
     *
     * @param Illuminate\Contracts\Auth\Guard $auth
     * @param int $id
     */
    public function create(Guard $auth, $id)
    {
        $this->repository->subscribe($id, $auth->user());

        return $this->response()->noContent();
    }

    /**
     * Unsubscribers a user from a tag
     *
     * @param Illuminate\Contracts\Auth\Guard $auth
     * @param int $id
     */
    public function destroy(Guard $auth, $id)
    {
        $this->repository->unsubscribe($id, $auth->user());

        return $this->response()->noContent();
    }
}
