<?php

namespace App;

use Illuminate\Foundation\Auth\User as BaseUser;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends BaseUser
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'password',
        'display_picture'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * When setting the password, always make sure to hash it properly
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = app('hash')->make($value);
    }

    /**
     * A user can have many users subscribed to them
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'user_subscribers', 'user_id', 'subscriber_id');
    }

    /**
     * A user can be subscribed to many other users channels
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function channels()
    {
        return $this->belongsToMany(User::class, 'user_subscribers', 'subscriber_id', 'user_id');
    }
}
