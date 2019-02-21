<?php

namespace Altelma\JWT;

use Illuminate\Support\ServiceProvider;

class JWTServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('jwt', function ($app) {
            return new JWTService();
        });
    }

    /**
     * Bootstrap provides.
     *
     * @return void
     */
    public function provides()
    {
        return ['jwt'];
    }
}
