<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Verifica si el usuario tiene el rol adecuado
        if (Auth::check() && Auth::user()->hasRole($role)) {
            return $next($request);  // Permite continuar si tiene el rol
        }

        // Si no tiene el rol adecuado, redirige a otra página o muestra un mensaje de error
        return redirect()->route('dashboard')->with('error', 'No tienes permiso para acceder a esta página');
    }
}
