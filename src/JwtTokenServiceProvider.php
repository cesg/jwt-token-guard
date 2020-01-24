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
                return tap(new JwtTokenGuard(
                    Auth::createUserProvider($config['provider']),
                    $config['key'] ?? $this->app['config']->get('app.key')
                ), function (JwtTokenGuard $guard) {
                    $this->app->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }
}
