<?php

namespace App\Traits;

use App\Tag;

trait Taggable
{
    /**
     * Get all of the tags for this resource
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
