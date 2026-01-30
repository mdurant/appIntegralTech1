<?php

use App\Http\Middleware\CheckApiAccess;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureEmailCodeVerified;
use App\Http\Middleware\EnsureNotClient;
use App\Http\Middleware\ResolveTenantContext;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api.access' => CheckApiAccess::class,
            'admin' => EnsureAdmin::class,
            'email.code' => EnsureEmailCodeVerified::class,
            'not.client' => EnsureNotClient::class,
            'tenant' => ResolveTenantContext::class,
        ]);
    })
    ->withSchedule(function ($schedule): void {
        $schedule->command('quotes:mark-expired')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
