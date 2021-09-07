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
    protected $jwtSecretKey = '46cbfc4212aaeb4d7607fe8b5d54ab253f7c3b0965f1234d4260d5f22c54097d2b49c3cd05d35f2c6b15271ff6c089059e50974af39fefbebfcaf8f5326fbcab';

    protected function getPackageProviders($app)
    {
        return [JwtTokenServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('auth.guards.api', [
            'driver' => 'jwt',
            'provider' => 'users',
            'key' => $this->jwtSecretKey,
        ]);
    }
}

trait HasJwtTokenAttribute
{
    public function getJwtTokenAttribute()
    {
        return JWT::encode([
            'sub' => $this->getAuthIdentifier(),
            'iss' => 'testing',
            'iat' => now()->timestamp,
        ], config('auth.guards.api.key'));
    }
}

class User extends \Illuminate\Foundation\Auth\User
{
    use HasJwtTokenAttribute;
    protected $guarded = [];
}
