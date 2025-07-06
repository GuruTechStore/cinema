<?php
// app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta área.');
        }

        $user = Auth::user();
        
        if (method_exists($user, 'esAdmin') && $user->esAdmin()) {
            return $next($request);
        }

        if (isset($user->rol) && $user->rol === 'admin') {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta área.');
    }
}
?>