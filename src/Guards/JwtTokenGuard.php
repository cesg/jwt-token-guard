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

    public function __construct(UserProvider $provider, string $key)
    {
        $this->provider = $provider;
        $this->key = $key;
    }

    public function user()
    {
        if (!\is_null($this->user)) {
            return $this->user;
        }

        $token = $this->request->bearerToken();

        if (\is_null($token)) {
            return;
        }

        $jwt = JWT::decode($token, $this->key, ['HS256']);

        return $this->user = $this->getProvider()
            ->retrieveById(
                $jwt->sub
            )
        ;
    }

    /**
     * Validate a user's credentials.
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return !is_null(
            (new static($this->getProvider(), $this->key))->setRequest($credentials['request'])->user()
        );
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;

        return $this;
    }
}
