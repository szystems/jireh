<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
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
        // Verificar que el usuario esté autenticado y sea SOLO administrador (role_as = 0)
        if (!Auth::check() || Auth::user()->role_as != 0) {
            return redirect('/dashboard')->with('error', 'Acceso denegado. Solo los administradores pueden realizar esta acción.');
        }

        return $next($request);
    }
}