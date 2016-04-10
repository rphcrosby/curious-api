<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\UserRepository;
use League\Fractal\Manager as FractalManager;
use Dingo\Api\Auth\Provider\Basic as BasicAuth;
use Dingo\Api\Auth\Auth;
use App\Invite;
use Validator;
use DB;

class AppServiceProvider extends ServiceProvider
{
    protected $repositories = [
        UserRepository::class
    ];

    public function boot()
    {
        Validator::extend('invite', function($attribute, $value, $parameters, $validator)
        {
            $email = array_get($validator->getData(), 'email');

            // Find an invite where the email and invite code matches
            return Invite::where('email', $email)
                ->whereHas('inviter', function($q) use ($value)
                {
                    $q->where('invite_code', $value);
                })->exists();
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->repositories as $repository) {
            $this->app->instance($repository, new $repository);
        }

        $fractal = new FractalManager();

        if (isset($_GET['include'])) {
            $fractal->parseIncludes($_GET['include']);
        }
    }
}
