<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ==================== MIDDLEWARE ALIASES ====================
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'user.active' => \App\Http\Middleware\UserActiveMiddleware::class,
            'periode.active' => \App\Http\Middleware\PeriodeActiveMiddleware::class,
            'log.activity' => \App\Http\Middleware\LogActivityMiddleware::class,
        ]);

        // ==================== CUSTOM WEB MIDDLEWARE ====================
        // Hanya yang custom — sisanya sudah otomatis oleh Laravel 12
        // (Kosongkan kalau VerifyCsrfToken custom tidak dipakai)

        // ==================== CUSTOM API MIDDLEWARE ====================
        // (Kosongkan kalau tidak ada custom API middleware)
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ==================== CUSTOM 429 HANDLER ====================
        $exceptions->render(function (TooManyRequestsHttpException $e, $request) {
            if ($request->is('login') || $request->routeIs('login.post')) {
                $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;
                $seconds = (int) $retryAfter;
                $minutes = ceil($seconds / 60);

                return response()->view('auth.login-blocked', [
                    'retryAfter' => $retryAfter,
                    'minutes' => $minutes,
                    'seconds' => $seconds,
                ], 429);
            }
        });
    })
    ->create();