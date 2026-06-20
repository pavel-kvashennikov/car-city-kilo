<?php

use App\Http\Middleware\CheckProfileAccess;
use App\Http\Middleware\CheckTenantSubscription;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureTenant;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetTenantContext;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'tenant.active' => CheckTenantSubscription::class,
            'set.tenant.context' => SetTenantContext::class,
            'profile' => CheckProfileAccess::class,
            'role' => RoleMiddleware::class,
            'admin' => EnsureAdmin::class,
            'tenant' => EnsureTenant::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Перенаправляем 403 (недостаточно прав) на главную с сообщением
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, Request $request) {
            if (! $request->is('api/*')) {
                return redirect('/')->with('error', 'У вас нет прав для доступа к этому разделу.');
            }
        });

        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, Request $request) {
            if (! $request->is('api/*')) {
                return redirect('/')->with('error', 'У вас нет прав для доступа к этому разделу.');
            }
        });
    })->create();
