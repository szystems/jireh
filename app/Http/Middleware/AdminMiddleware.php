<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class AdminMiddleware
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
        if(Auth::check())
        {
            if(Auth::user()->role_as == '1' or Auth::user()->role_as == '0')
            {
                return $next($request);
            }
            else
            {
                return redirect('/dashboard')->with('status','Acceso denegado no eres Usuario de Jireh');
            }
        }
        else
        {
            return redirect('/dashboard')->with('status','Porfavor inicie sesión para acceder');
        }
    }

}
