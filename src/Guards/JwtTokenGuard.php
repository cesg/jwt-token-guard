<?php

namespace Cesg\Jwt\Guards;

use Firebase\JWT\JWT;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class JwtTokenGuard implements Guard
{
    use GuardHelpers;

    protected $request;
    protected $key;
    protected $jwt;

    public function __construct(UserProvider $provider, Request $request, string $key)
    {
        $this->provider = $provider;
        $this->request = $request;
        $this->key = $key;
    }

    public function user()
    {
        if (null !== $this->user) {
            return $this->user;
        }

        $jwt = $this->getJwt();
        if (null === $jwt) {
            return null;
        }

        return $this->user = $this->getProvider()
            ->retrieveById(
                $jwt->sub
            )
        ;
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        return null !== $this->user();
    }

    /**
     * Validate a user's credentials.
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return null !== (new static($this->getProvider(), $credentials['request'], $this->key))->user();
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getJwt(): object
    {
        if (null !== $this->jwt) {
            return $this->jwt;
        }

        return $this->jwt = $this->decodeRequestJwt();
    }

    private function decodeRequestJwt(): ?object
    {
        $token = $this->request->bearerToken();

        if (null === $token) {
            return null;
        }

        return JWT::decode($token, $this->key, ['HS256']);
    }
}
