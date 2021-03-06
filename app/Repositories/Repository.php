<?php

namespace App\Repositories;

use Dingo\Api\Exception\ResourceException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Repository
{
    /**
     * The class that this repository is responsible for operating on
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    protected $class;

    /**
     * Creates a new resource
     *
     * @param array $data
     * @return Illuminate\Database\Eloquent\Model
     */
    public function create($data)
    {
        $resource = new $this->class;

        foreach ($data as $key => $value) {
            $resource->$key = $value;
        }

        $resource->save();

        return $resource;
    }

    /**
     * Finds a resource and returns it
     *
     * @param int $id
     * @return Illuminate\Database\Eloquent\Model
     */
    public function show($id)
    {
        try {
            return with(new $this->class)
                ->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ResourceException('Resource could not be found');
        }
    }

    /**
     * Finds a resource and returns it
     *
     * @param int $id
     * @param array $attributes
     * @return Illuminate\Database\Eloquent\Model
     */
    public function update($id, array $attributes)
    {
        $resource = $this->show($id);

        foreach ($attributes as $key => $value) {
            $resource->$key = $value;
        }

        $resource->save();
        return $resource;
    }

    /**
     * Finds a resource and destroys it
     *
     * @param int $id
     * @return Illuminate\Database\Eloquent\Model
     */
    public function destroy($id)
    {
        $resource = $this->show($id);
        $resource->delete();
    }
}
