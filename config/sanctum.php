<?php

use Laravel\Sanctum\Sanctum;

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Aquí defines los dominios que deberían recibir cookies de sesión
    | (como XSRF-TOKEN y laravel_session). Asegúrate de incluir tu
    | dominio del frontend (como Vite en :5173).
    |
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS',
        'localhost,localhost:5173,127.0.0.1:5173,127.0.0.1:8000,::1'
    )),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | Define el guard que se usará para autenticar las sesiones.
    | En la mayoría de casos de SPA, debe ser "web".
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | Cuánto dura un token personal. Las sesiones basadas en cookies
    | no están afectadas por esta opción.
    |
    */

    'expiration' => null,

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | Agrega un prefijo a los tokens si quieres más seguridad al
    | detectar tokens comprometidos en repositorios públicos.
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware que Sanctum usa para procesar las peticiones.
    | Incluye validación de cookies, sesión y token CSRF.
    |
    */

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];
