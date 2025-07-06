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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta área.');
        }

        // Verificar que el usuario sea administrador
        $user = Auth::user();
        
        // Verificar si el método esAdmin() existe en el modelo User
        if (method_exists($user, 'esAdmin') && $user->esAdmin()) {
            return $next($request);
        }

        // Verificar por rol directo si no existe el método
        if (isset($user->rol) && $user->rol === 'admin') {
            return $next($request);
        }

        // Si no es admin, redirigir con error
        return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta área.');
    }
}
?>