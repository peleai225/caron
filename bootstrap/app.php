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
        // Enregistrer les middlewares personnalisés
        $middleware->alias([
            'role.owner' => \App\Http\Middleware\EnsureOwnerRole::class,
            'role.tenant' => \App\Http\Middleware\EnsureTenantRole::class,
            'role.accountant' => \App\Http\Middleware\EnsureAccountantRole::class,
            'restrict.admin' => \App\Http\Middleware\RestrictAdminRoutes::class,
            'admin.only' => \App\Http\Middleware\EnsureAdminOrAdminAgence::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
