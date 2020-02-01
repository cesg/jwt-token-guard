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
        if (!\is_null($this->user)) {
            return $this->user;
        }

        $jwt = $this->decodeRequestJwt();
        if (is_null($jwt)) {
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
        return !is_null($this->user());
    }

    /**
     * Validate a user's credentials.
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return !is_null(
            (new static($this->getProvider(), $credentials['request'], $this->key))->user()
        );
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }

    private function decodeRequestJwt(): ?object
    {
        if (!\is_null($this->jwt)) {
            return $this->jwt;
        }

        $token = $this->request->bearerToken();

        if (is_null($token)) {
            return null;
        }

        return $this->jwt = JWT::decode($token, $this->key, ['HS256']);
    }
}
