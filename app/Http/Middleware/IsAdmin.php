<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Verificar que el usuario esté autenticado
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debe iniciar sesión para continuar.');
        }

        // Verificar que el usuario sea administrador (role_as = 0)
        if (auth()->user()->role_as != 0) {
            abort(403, 'Acceso denegado. Esta función está restringida para administradores únicamente.');
        }

        return $next($request);
    }
}
