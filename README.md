<p align="center">
<img src="https://chathu.me/2017/08/28/jwt-introduction/banner.jpg" alt="JWT">
</p>

## Simple JWT for Larvel
Simple library creating and verify JWT Signatures in PHP using HS256 and RS256.

### Setup
- Run `$ composer require altelma/php-jwt`

#### Laravel

- **(Only for Laravel 5.5 or minor)** Add provider to config/app.php

```php
'providers' => [
    Altelma\JWT\JWTServiceProvider::class,
],
```

- Run `$ php artisan vendor:publish` to publish the configuration file `config/jwt.php` and insert:
    - private_key
    - public_key
    
#### Lumen

- Add provider to `bootstrap/app.php`

```php
$app->register(Altelma\JWT\JWTServiceProvider::class);
```

- Copy `vendor/altelma/php-jwt/config/jwt.php` to `config/jwt.php` and insert:
    - private_key
    - public_key

- Add config to `bootstrap/app.php`

```php
$app->configure('jwt');
```
- Allow call package via Facade, uncomment
```php
$app->withFacades();

if (!class_exists('JWT')) {
    class_alias('Altelma\JWT\JWTFacade', 'JWT');
}
```

### Basic usage

The following example is for generate JWT
```php
<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;

class JwtController extends Controller
{
    private function getJWT()
    {
        $client = new Client();
        $response = $client->post('your_auth_url', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'email' => 'admin@example.com',
                'password' => 'password'
            ]
        ]);

        $response = json_decode($response->getBody(), true);

        return $response['accesstoken'];
    }

    public function verifyJwt()
    {
        $jwtToken = $this->getJWT();
        $verifyToken = \JWT::verify('sha256', $jwtToken);

        return ['success' => $verifyToken];
    }

    public function genJwt()
    {
        $header = [
            "alg"     => "RS256",
            "typ"     => "JWT"
        ];

        $payload = [
            "sub"        => "465465464646",
            "name"        => "John Doe",
            "admin"        => true
        ];

        return ['success' => true, 'access_token' => \JWT::generate('sha256', $header, $payload)];
    }
}
```

## Bug report
This package is not perfect now, but it can be improve together. If you found any bug or have any suggestion.
Send that to me or create new issue. Thank you to use it.
