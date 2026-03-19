<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\Middleware\HandleCors;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // 🔹 Solo usas API, no web.php
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
->withMiddleware(function (Middleware $middleware): void {

    // CORS
    $middleware->prepend(HandleCors::class);

    // 🔥 WEB (AQUÍ VA CSRF Y SESIÓN)
    $middleware->group('web', [
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ]);

    // 🔥 API (SOLO SANCTUM)
    $middleware->group('api', [
        EnsureFrontendRequestsAreStateful::class,
    ]);

    // Alias
    $middleware->alias([
        'auth'       => \Illuminate\Auth\Middleware\Authenticate::class,
        'role'       => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions): void {
        // Manejo de excepciones personalizadas
    })
    ->create();
