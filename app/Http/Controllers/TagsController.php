<?php

namespace App\Http\Controllers;

use App\Repositories\TagRepository;
use App\Transformers\TagTransformer;

class TagsController extends ApiController
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
     * Show a tag
     *
     * @param int $id
     */
    public function show($id)
    {
        $tag = $this->repository->show($id);

        return $this->response->item($tag, new TagTransformer);
    }
}
