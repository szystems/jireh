<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClienteVehiculoController;
use App\Http\Controllers\Admin\ArticuloController; // Asegúrate que el namespace y controlador sean correctos
use App\Http\Controllers\Admin\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('clientes/{cliente}/vehiculos', [ClienteVehiculoController::class, 'getVehiculos']);

// Ruta para obtener artículos para el selector de ventas
Route::get('/articulos-para-venta', [ArticuloController::class, 'getArticulosParaVentaApi'])->name('api.articulos.para_venta');

// Rutas API para Dashboard Unificado
Route::group(['prefix' => 'dashboard', 'middleware' => 'auth'], function () {
    Route::get('/metricas-vivo', [DashboardController::class, 'getMetricasEnVivo']);
    Route::get('/alertas', [DashboardController::class, 'getAlertasApi']);
});
