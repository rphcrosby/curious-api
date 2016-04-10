<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\UserRepository;
use League\Fractal\Manager as FractalManager;

class AppServiceProvider extends ServiceProvider
{
    protected $repositories = [
        UserRepository::class
    ];

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
