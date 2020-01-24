<?php

namespace Cesg\Jwt;

use Cesg\Jwt\Guards\JwtTokenGuard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class JwtTokenServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        Auth::resolved(function ($auth) {
            $auth->extend('jwt', function ($app, $name, array $config) {
                $guard = new JwtTokenGuard(
                    Auth::createUserProvider($config['provider']),
                    $this->app['request'],
                    $config['key'] ?? $this->app['config']->get('app.key')
                );
                $this->app->refresh('request', $guard, 'setRequest');

                return $guard;
            });
        });
    }
}
