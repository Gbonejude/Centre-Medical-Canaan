<?php

use App\Http\Middleware\GuestAuth;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(function ($request) {
            // Check if the matched route belongs to the admin group
            if ($request->route()) {
                $action = $request->route()->getAction();
                if ((isset($action['domain']) && str_contains($action['domain'], 'admin.')) ||
                    (isset($action['controller']) && str_contains($action['controller'], 'BackOffice'))) {
                    return route('auth.loginForm');
                }
            }

            // Fallback to host checking
            if (str_contains($request->getHost(), 'admin.')) {
                return route('auth.loginForm');
            }

            return '/login';
        });
        $middleware->redirectUsersTo(fn () => route('dashboard.index'));
        $middleware->alias([
            'guest.auth' => GuestAuth::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
