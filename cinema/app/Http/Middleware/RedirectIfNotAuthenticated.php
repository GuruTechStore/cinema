<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            // Si es una petición AJAX, devolver respuesta JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'No autenticado',
                    'redirect' => route('login')
                ], 401);
            }

            // Para peticiones web, guardar la URL actual y redirigir
            $intendedUrl = $request->fullUrl();
            
            // Guardar en sesión para usar después del login
            session(['url.intended' => $intendedUrl]);
            
            return redirect()->route('login')
                ->with('info', 'Debes iniciar sesión para comprar entradas')
                ->with('intended_url', $intendedUrl);
        }

        return $next($request);
    }
}

?>