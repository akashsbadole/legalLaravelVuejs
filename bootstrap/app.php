<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(\App\Http\Middleware\SetLocale::class);
        $middleware->web(\App\Http\Middleware\CheckAdminOnboarding::class);
        $middleware->web(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->web(\App\Http\Middleware\ForceHttpsMiddleware::class);
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'two-factor' => \App\Http\Middleware\TwoFactorMiddleware::class,
            'auth:sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withProviders([
        \App\Providers\LoggingServiceProvider::class,
    ])
    ->create();
