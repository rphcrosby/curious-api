<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\UserRepository;
use League\Fractal\Manager as FractalManager;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    protected $repositories = [
        UserRepository::class
    ];

    public function boot()
    {
        Validator::extend('invite', function($attribute, $value, $parameters, $validator)
        {
            dd($validator);
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
