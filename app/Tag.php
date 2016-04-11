<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Tag extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Get all the users that are subscribed to this tag
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphedByMany
     */
    public function subscribers()
    {
        return $this->morphedByMany(User::class, 'taggable');
    }
}
