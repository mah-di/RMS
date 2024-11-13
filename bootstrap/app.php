<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        apiPrefix: '_api',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.jwt' => \App\Http\Middleware\JWTAuthenticate::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'scope' => \App\Http\Middleware\CheckAccessScope::class,
            'refreshable' => \App\Http\Middleware\EnsureTokenIsRefreshable::class,
            'guest' => \App\Http\Middleware\RestrictAuthUser::class,
        ]);
    })
    ->withBindings([
        'Permission' => \App\Facades\Permission::class,
        'UserRole' => \App\Facades\UserRole::class
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
