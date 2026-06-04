<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Intercepta la petición y valida que el usuario tenga rol de administrador.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Si no hay sesión autenticada, se redirige al login.
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder al panel de administración.');
        }

        // Aunque exista sesión, solo el rol admin puede continuar.
        if (auth()->user()->user_type !== 'admin') {
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder al panel de administración.');
        }

        // Si ambas comprobaciones se superan, la petición sigue su curso normal.
        return $next($request);
    }
}