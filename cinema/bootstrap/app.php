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
        // Registrar middleware personalizado con alias
        $middleware->alias([
            // Middleware existente de admin
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            
            // Nuevo middleware para redirección de autenticación
            'auth.redirect' => \App\Http\Middleware\RedirectIfNotAuthenticated::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
    })->create();

?>