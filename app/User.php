<?php

namespace App;

use Illuminate\Foundation\Auth\User as BaseUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Taggable;

class User extends BaseUser
{
    use SoftDeletes, Taggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'password',
        'email',
        'display_picture',
        'role_id',
        'invite_code',
        'invite_count',
        'invite_id'
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

    /**
     * A user belongs to a single role
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * A user has many invites that they have created
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function invites()
    {
        return $this->hasMany(Invite::class);
    }

    /**
     * A user has up to one invite that they joined the app with
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function invite()
    {
        return $this->belongsTo(Invite::class);
    }

    /**
     * Checks if the user is of a certain role
     *
     * @param string $role
     * @return bool
     */
    public function is($role)
    {
        return object_get($this->role, 'name') == $role;
    }
}
