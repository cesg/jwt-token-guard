<?php

namespace Cesg\Jwt;

use Cesg\Jwt\Guards\JwtTokenGuard;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class JwtTokenServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Auth::resolved(function (AuthManager $auth): void {
            $auth->extend('jwt', function ($app, $name, array $config) use ($auth) {
                $guard = new JwtTokenGuard(
                    $auth->createUserProvider($config['provider']),
                    $app['request'],
                    $config['key']
                );
                $app->refresh('request', $guard, 'setRequest');

                return $guard;
            });
        });
    }
}
