<?php
// app/Http/Kernel.php - AGREGAR el nuevo middleware

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     */
    protected $middleware = [
        // ... otros middleware globales
    ];

    /**
     * The application's route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            // ... otros middleware web
        ],

        'api' => [
            // ... middleware api
        ],
    ];

    /**
     * The application's route middleware.
     */
    protected $routeMiddleware = [
        // ... otros middleware existentes
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
        'auth.redirect' => \App\Http\Middleware\RedirectIfNotAuthenticated::class,
    ];
}

?>