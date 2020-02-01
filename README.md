# jwt-token-guard
Laravel simple JWT token guard

# Install
```sh
composer require cesg/jwt-token-guard
```

# Configure
Configure the auth driver

```php
'api' => [
    'driver' => 'jwt',
    'provider' => 'users',
    'key' => env('JWT_SECRET_KEY'),
],
```

Example secret key

```sh
openssl rand -hex 64
```

# Usage
## Javascript
```js
const token = '';
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
```

## Laravel
```php
protected function authenticated(Request $request, $user)
{
    $jwt = JWT::encode([
        'sub' => $user->getAuthIdentifier(),
        'iss' => config('app.name'),
        'iat' => now()->timestamp,
    ], config('auth.guards.api.key'));

    session(\compact('jwt'));
}
```

