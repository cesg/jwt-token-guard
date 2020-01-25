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
    'key' => env('APP_KEY'),
],
```

# Usage
## Frontend
```js
const token = '';
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
```

## Backend
```php
protected function authenticated(Request $request, $user)
{
    $jwt = JWT::encode([
        'sub' => $user->getAuthIdentifier(),
        'iss' => config('app.name'),
        'iat' => now()->timestamp,
    ], config('app.key'));

    session(\compact('jwt'));
}
```

