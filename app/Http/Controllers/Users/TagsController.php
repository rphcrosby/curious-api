<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\ApiController;
use App\Repositories\TagRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class TagsController extends ApiController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Subscribers a user to a set of tags
     *
     * @param int $id
     */
    public function create(Request $request, $id)
    {
        $user = app(UserRepository::class)->show($id);

        $this->repository->subscribeToNames(
            $request->input('tags'),
            $user
        );

        return $this->response->noContent();
    }

    /**
     * Unsubscribers a user from another user
     *
     * @param Illuminate\Contracts\Auth\Guard $auth
     * @param int $id
     */
    public function destroy(Request $request, Guard $auth, $id)
    {
        $user = app(UserRepository::class)->show($id);

        $this->repository->unsubscribeFromNames(
            $request->only('tags'),
            $user
        );

        return $this->response->noContent();
    }
}
