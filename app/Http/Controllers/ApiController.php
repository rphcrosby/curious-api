<?php

namespace App\Http\Controllers;

use Validator;
use Dingo\Api\Routing\Helpers;
use Dingo\Api\Exception\StoreResourceFailedException;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    use Helpers;

    /**
     * The repository that should be used for controller methods
     *
     * @var App\Repositories\Repository
     */
    protected $repository;
}
