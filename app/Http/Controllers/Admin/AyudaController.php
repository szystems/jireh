<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AyudaController extends Controller
{
    /**
     * Constructor con middleware de autenticación
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar el Centro de Ayuda principal
     */
    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->role_as == 0;
        $isVendedor = $user->role_as == 1;

        return view('admin.ayuda.index', compact('user', 'isAdmin', 'isVendedor'));
    }

    /**
     * Las rutas antiguas abrían vistas que no existen. Ahora llevan a la pestaña correcta.
     */
    public function primerosPasos()
    {
        return redirect()->route('ayuda.index', ['tab' => 'primeros-pasos']);
    }

    public function modulos()
    {
        return redirect()->route('ayuda.index', ['tab' => 'modulos']);
    }

    public function faq()
    {
        return redirect()->route('ayuda.index', ['tab' => 'faq']);
    }

    public function soporte()
    {
        return redirect()->route('ayuda.index', ['tab' => 'soporte']);
    }
}