<?php

namespace Cesg\Jwt\Tests;

use Cesg\Jwt\JwtTokenServiceProvider;
use Firebase\JWT\JWT;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

/**
 * @internal
 * @coversNothing
 */
class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app)
    {
        return [JwtTokenServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('auth.guards.api', [
            'driver' => 'jwt',
            'provider' => 'users',
            'key' => 'Som3RandonKey_',
        ]);
    }
}

class User extends \Illuminate\Foundation\Auth\User
{
    protected $guarded = [];

    public function getJwtTokenAttribute()
    {
        return JWT::encode([
            'sub' => $this->getAuthIdentifier(),
            'iss' => 'testing',
            'iat' => now()->timestamp,
        ], 'Som3RandonKey_');
    }
}
