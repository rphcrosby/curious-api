<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email'
    ];

    /**
     * An invite is create by a user
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inviter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * An invite is created for one user
     *
     * @return Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function invited()
    {
        return $this->hasOne(User::class);
    }
}
