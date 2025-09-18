<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
     * Mostrar guía de primeros pasos
     */
    public function primerosPasos()
    {
        $user = Auth::user();
        $isAdmin = $user->role_as == 0;
        
        return view('admin.ayuda.primeros-pasos', compact('user', 'isAdmin'));
    }

    /**
     * Mostrar documentación de módulos
     */
    public function modulos()
    {
        $user = Auth::user();
        $isAdmin = $user->role_as == 0;
        
        return view('admin.ayuda.modulos', compact('user', 'isAdmin'));
    }

    /**
     * Mostrar preguntas frecuentes
     */
    public function faq()
    {
        $user = Auth::user();
        $isAdmin = $user->role_as == 0;
        
        return view('admin.ayuda.faq', compact('user', 'isAdmin'));
    }

    /**
     * Mostrar información de soporte
     */
    public function soporte()
    {
        $user = Auth::user();
        $isAdmin = $user->role_as == 0;
        
        return view('admin.ayuda.soporte', compact('user', 'isAdmin'));
    }
}