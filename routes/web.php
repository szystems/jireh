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
use App\Http\Controllers\Admin\TrabajadorController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\DescuentoController;
use App\Http\Controllers\Admin\VentaController;
use App\Http\Controllers\Admin\PagoController;
use App\Http\Controllers\Admin\ReporteArticuloController;

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
    Route::get('export-articulos-pdf', [ArticuloController::class, 'exportPdf']);
    Route::get('export-articulo-pdf/{id}', [ArticuloController::class, 'exportPdfSingle']); // Nueva ruta para exportar artículo individual

    // Ruta para guardar la preferencia de visualización
    Route::post('set-view-preference', [App\Http\Controllers\Admin\ArticuloController::class, 'setViewPreference']);

    //Ingresos
    Route::get('ingresos', [IngresoController::class, 'index']);
    Route::get('add-ingreso', [IngresoController::class, 'create']);
    Route::post('insert-ingreso', [IngresoController::class, 'store']);
    Route::get('show-ingreso/{id}', [IngresoController::class, 'show']);
    Route::get('edit-ingreso/{id}', [IngresoController::class, 'edit']);
    Route::put('update-ingreso/{id}', [IngresoController::class, 'update']);
    Route::get('delete-ingreso/{id}', [IngresoController::class, 'destroy']);
    Route::get('ingresos/export/pdf', [IngresoController::class, 'exportPdf'])->name('ingresos.export.pdf');
    Route::get('ingresos/export/excel', [IngresoController::class, 'exportExcel'])->name('ingresos.export.excel');
    Route::get('ingresos/export/single/pdf/{id}', [IngresoController::class, 'exportSinglePdf'])->name('ingresos.export.single.pdf');
    Route::get('create-ingreso', [IngresoController::class, 'create']);

    //Trabajadores
    Route::get('trabajadores', [TrabajadorController::class, 'index']);
    Route::get('show-trabajador/{id}', [TrabajadorController::class, 'show']);
    Route::get('add-trabajador', [TrabajadorController::class, 'add']);
    Route::post('insert-trabajador',[TrabajadorController::class,'insert']);
    Route::get('edit-trabajador/{id}',[TrabajadorController::class,'edit']);
    Route::put('update-trabajador/{id}', [TrabajadorController::class, 'update']);
    Route::get('delete-trabajador/{id}', [TrabajadorController::class, 'destroy']);

    //inventario
    Route::get('inventario', [InventarioController::class, 'index']);
    Route::get('print-inventario', [InventarioController::class, 'printinventario']);
    Route::get('exportinventario', [InventarioController::class, 'exportinventario']);
    Route::get('api/buscar-articulos', [InventarioController::class, 'buscarArticulos']);

    //Descuentos
    Route::get('descuentos', [DescuentoController::class, 'index']);
    Route::get('show-descuento/{id}', [DescuentoController::class, 'show']);
    Route::get('add-descuento', [DescuentoController::class, 'add']);
    Route::post('insert-descuento',[DescuentoController::class,'insert']);
    Route::get('edit-descuento/{id}',[DescuentoController::class,'edit']);
    Route::put('update-descuento/{id}', [DescuentoController::class, 'update']);
    Route::get('delete-descuento/{id}', [DescuentoController::class, 'destroy']);

    //Ventas
    Route::get('ventas', [VentaController::class, 'index']);
    Route::get('add-venta', [VentaController::class, 'create']);
    Route::post('insert-venta', [VentaController::class, 'store']);
    Route::get('show-venta/{id}', [VentaController::class, 'show'])->name('ventas.show');
    Route::get('edit-venta/{id}', [VentaController::class, 'edit']);
    Route::put('update-venta/{id}', [VentaController::class, 'update']);
    Route::get('delete-venta/{id}', [VentaController::class, 'destroy']);
    Route::get('venta/export/pdf', [VentaController::class, 'exportPdf'])->name('ventas.export.pdf');
    Route::get('venta/export/excel', [VentaController::class, 'exportExcel'])->name('ventas.export.excel');
    Route::get('venta/export/single/pdf/{id}', [VentaController::class, 'exportSinglePdf'])->name('ventas.export.single.pdf');

    // Pagos
    Route::post('pagos', [PagoController::class, 'store']);
    Route::put('pagos/{id}', [PagoController::class, 'update']);
    Route::delete('pagos/{id}', [PagoController::class, 'destroy']);

    //Reportes Articulos
    Route::get('reportearticulos', [ReporteArticuloController::class, 'index']);
    Route::get('reportearticulos/export/pdf', [ReporteArticuloController::class, 'exportPdf'])->name('reportearticulo.export.pdf');

    //config
    Route::get('config', [ConfigController::class, 'index']);
    Route::put('update-config', [ConfigController::class, 'update']);
});
