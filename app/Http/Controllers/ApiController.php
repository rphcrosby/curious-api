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

    /**
     * Validates the input and throws a proper error if it fails
     *
     * @param Illuminate\Http\Request $request
     * @param array $rules
     * @param array $messages
     * @throws Dingo\Api\Exception\StoreResourceFailedException
     * @return void
     */
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validation = Validator::make($request->all(), $rules, $messages);

        if ($validation->fails()) {
            throw new StoreResourceFailedException('Could not create resource', $validation->errors());
        }
    }
}
