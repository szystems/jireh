<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'prevencion-test/*'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, \Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            // Si es una petición AJAX, devolver JSON con error
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'La sesión ha expirado. Por favor, recarga la página.',
                    'error' => 'token_expired'
                ], 419);
            }
            
            // Si es una petición normal, redirigir con mensaje
            return redirect()->back()
                ->withInput($request->except(['_token', 'password', 'password_confirmation']))
                ->with('error', 'La sesión ha expirado. Por favor, inténtalo de nuevo.');
        }
    }
}
