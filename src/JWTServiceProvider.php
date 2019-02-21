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
        $this->publishes([
            __DIR__.'/../config/jwt.php' => config_path('jwt.php'),
        ], 'jwt');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom( __DIR__.'/../config/jwt.php', 'jwt');

        $this->app->singleton('jwt', function ($app) {
            $config = $app->make('config');
            $privateKey = $config->get('jwt.private_key');
            $publicKey = $config->get('jwt.public_key');

            return new JWTService($privateKey, $publicKey);
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
