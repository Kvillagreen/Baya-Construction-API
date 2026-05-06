<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: [
            __DIR__.'/../routes/api/auth.php',
            __DIR__.'/../routes/api/dashboard.php',
            __DIR__.'/../routes/api/identity.php',
            __DIR__.'/../routes/api/crm.php',
            __DIR__.'/../routes/api/hr.php',
            __DIR__.'/../routes/api/assets.php',
            __DIR__.'/../routes/api/inventory.php',
            __DIR__.'/../routes/api/sales.php',
            __DIR__.'/../routes/api/documents.php',
        ],
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->validateCsrfTokens(except: [
            'api/v1/auth/login',
            'api/v1/auth/logout',
        ]);
        $middleware->throttleApi('api');
        $middleware->alias([
            'request.id' => \App\Http\Middleware\AssignRequestId::class,
            'hidden.captcha' => \App\Http\Middleware\TrapHiddenCaptcha::class,
            'endpoint.permission' => \App\Http\Middleware\EnsureEndpointPermission::class,
        ]);
        $middleware->append(\App\Http\Middleware\AssignRequestId::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $exception, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                ], 401);
            }

            return null;
        });

        $exceptions->render(function (AuthorizationException $exception, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Forbidden.',
                ], 403);
            }

            return null;
        });
    })->create();
