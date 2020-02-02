<?php

namespace Cesg\Jwt\Tests;

use Cesg\Jwt\Guards\JwtTokenGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

/**
 * @internal
 * @coversNothing
 */
class JwtTokenGuardTest extends TestCase
{
    /** @test */
    public function testUserCanBePulledViaBearerToken()
    {
        $user = new User(['id' => 1]);

        /** @var \Illuminate\Contracts\Auth\UserProvider $userProvider */
        $userProvider = $this->partialMock(UserProvider::class, function ($mock) use ($user) {
            $mock->shouldReceive('retrieveById')->andReturn($user);
        });
        $request = Request::create('/');
        $request->headers->set('Authorization', 'Bearer '.$user->jwt_token);
        $guard = new JwtTokenGuard($userProvider, $request, config('auth.guards.api.key'));

        $this->assertEquals($user, $guard->user());
        $this->assertNotNull($guard->getJwt());
        $this->assertEquals($user->id, $guard->getJwt()->sub);
    }
}
