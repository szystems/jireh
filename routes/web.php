<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

//admin
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\VehiculosController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\ProveedorController;
use App\Http\Controllers\Admin\UnidadController;
use App\Http\Controllers\Admin\TipoComisionController;
use App\Http\Controllers\Admin\ArticuloController;
use App\Http\Controllers\Admin\IngresoController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//Admin Dashboard
Route::middleware(['auth'])->group(function () {
    //Control Panel
    Route::get('/dashboard',[AdminController::class, 'index']);

    //Admin Users
    Route::get('users', [UsersController::class, 'users']);
    Route::get('show-user/{id}', [UsersController::class, 'showuser']);
    Route::get('add-user', [UsersController::class, 'adduser']);
    Route::post('insert-user', [UsersController::class, 'insertuser']);
    Route::get('edit-user/{id}',[UsersController::class,'edituser']);
    Route::put('update-user/{id}', [UsersController::class, 'updateuser']);
    Route::get('delete-user/{id}', [UsersController::class, 'destroyuser']);
    Route::get('pdf-users', [UsersController::class, 'pdf']);
    Route::get('pdf-user/{id}', [UsersController::class, 'pdfuser']);

    //Clientes
    Route::get('clientes', [ClienteController::class, 'clientes']);
    Route::get('show-cliente/{id}', [ClienteController::class, 'show']);
    Route::get('add-cliente', [ClienteController::class, 'add']);
    Route::post('insert-cliente', [ClienteController::class, 'insert']);
    Route::get('edit-cliente/{id}',[ClienteController::class,'edit']);
    Route::put('update-cliente/{id}', [ClienteController::class, 'update']);
    Route::get('delete-cliente/{id}', [ClienteController::class, 'destroy']);
    Route::get('pdf-clientes', [ClienteController::class, 'pdf']);
    Route::get('pdf-cliente/{id}', [ClienteController::class, 'pdfcliente']);

    //Vehiculos
    Route::get('vehiculos', [VehiculosController::class, 'index']);
    Route::get('show-vehiculo/{id}', [VehiculosController::class, 'show']);
    Route::get('add-vehiculo', [VehiculosController::class, 'add']);
    Route::post('insert-vehiculo',[VehiculosController::class,'insert']);
    Route::get('edit-vehiculo/{id}',[VehiculosController::class,'edit']);
    Route::put('update-vehiculo/{id}', [VehiculosController::class, 'update']);
    Route::get('delete-vehiculo/{id}', [VehiculosController::class, 'destroy']);
    Route::get('print-vehiculos', [VehiculosController::class, 'printvehiculos']);
    Route::get('print-vehiculo', [VehiculosController::class, 'printvehiculo']);

    //Categorías
    Route::get('categorias', [CategoriaController::class, 'index']);
    Route::get('show-categoria/{id}', [CategoriaController::class, 'show']);
    Route::get('add-categoria', [CategoriaController::class, 'add']);
    Route::post('insert-categoria',[CategoriaController::class,'insert']);
    Route::get('edit-categoria/{id}',[CategoriaController::class,'edit']);
    Route::put('update-categoria/{id}', [CategoriaController::class, 'update']);
    Route::get('delete-categoria/{id}', [CategoriaController::class, 'destroy']);

    //Proveedores
    Route::get('proveedores', [ProveedorController::class, 'index']);
    Route::get('show-proveedor/{id}', [ProveedorController::class, 'show']);
    Route::get('add-proveedor', [ProveedorController::class, 'add']);
    Route::post('insert-proveedor',[ProveedorController::class,'insert']);
    Route::get('edit-proveedor/{id}',[ProveedorController::class,'edit']);
    Route::put('update-proveedor/{id}', [ProveedorController::class, 'update']);
    Route::get('delete-proveedor/{id}', [ProveedorController::class, 'destroy']);
    Route::get('pdf-proveedores', [ProveedorController::class, 'pdf']);
    Route::get('pdf-proveedor/{id}', [ProveedorController::class, 'pdfproveedor']);

    //Unidades de medida
    Route::get('unidades', [UnidadController::class, 'index']);
    Route::get('show-unidad/{id}', [UnidadController::class, 'show']);
    Route::get('add-unidad', [UnidadController::class, 'add']);
    Route::post('insert-unidad',[UnidadController::class,'insert']);
    Route::get('edit-unidad/{id}',[UnidadController::class,'edit']);
    Route::put('update-unidad/{id}', [UnidadController::class, 'update']);
    Route::get('delete-unidad/{id}', [UnidadController::class, 'destroy']);
    Route::get('/api/articulos/{articulo}/unidad', [UnidadController::class, 'getUnidadTipo']);

    //Tipo comisiones
    Route::get('tipo-comisiones', [TipoComisionController::class, 'index']);
    Route::get('show-tipo-comision/{id}', [TipoComisionController::class, 'show']);
    Route::get('add-tipo-comision', [TipoComisionController::class, 'add']);
    Route::post('insert-tipo-comision',[TipoComisionController::class,'insert']);
    Route::get('edit-tipo-comision/{id}',[TipoComisionController::class,'edit']);
    Route::put('update-tipo-comision/{id}', [TipoComisionController::class, 'update']);
    Route::get('delete-tipo-comision/{id}', [TipoComisionController::class, 'destroy']);


    //Articulos
    Route::get('articulos', [ArticuloController::class, 'index']);
    Route::get('add-articulo', [ArticuloController::class, 'add']);
    Route::post('insert-articulo', [ArticuloController::class, 'insert']);
    Route::get('show-articulo/{id}', [ArticuloController::class, 'show']);
    Route::get('edit-articulo/{id}', [ArticuloController::class, 'edit']);
    Route::put('update-articulo/{id}', [ArticuloController::class, 'update']);
    Route::get('delete-articulo/{id}', [ArticuloController::class, 'destroy']);

    //Ingresos
    Route::get('ingresos', [IngresoController::class, 'index']);
    Route::get('add-ingreso', [IngresoController::class, 'create']);
    Route::post('insert-ingreso', [IngresoController::class, 'store']);
    Route::get('show-ingreso/{id}', [IngresoController::class, 'show']);
    Route::get('edit-ingreso/{id}', [IngresoController::class, 'edit']);
    Route::put('update-ingreso/{id}', [IngresoController::class, 'update']);
    Route::get('ingresos/export/pdf', [IngresoController::class, 'exportPdf'])->name('ingresos.export.pdf');
    Route::get('ingresos/export/excel', [IngresoController::class, 'exportExcel'])->name('ingresos.export.excel');
    Route::get('ingresos/export/single/pdf/{id}', [IngresoController::class, 'exportSinglePdf'])->name('ingresos.export.single.pdf');

    //config
    Route::get('config', [ConfigController::class, 'index']);
    Route::put('update-config', [ConfigController::class, 'update']);
});
