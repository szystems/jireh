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
use App\Http\Controllers\Admin\TipoTrabajadorController; // Añadir esta importación
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\DescuentoController;
use App\Http\Controllers\Admin\ComisionController;
use App\Http\Controllers\Admin\PagoComisionController;
use App\Http\Controllers\Admin\PagoSueldoController; // Nuevo controlador de pagos de sueldos
use App\Http\Controllers\LotePagoController;
use App\Http\Controllers\Admin\VentaController;
use App\Http\Controllers\Admin\PagoController;
use App\Http\Controllers\Admin\ReporteArticuloController;
use App\Http\Controllers\Admin\ReporteMetasController; // Controlador de reportes de metas
use App\Http\Controllers\Admin\TestController; // Controlador de pruebas
use App\Http\Controllers\Admin\DatosInicialesController; // Controlador para generar datos iniciales
use App\Http\Controllers\Admin\AuditoriaController; // Controlador de auditoría
use App\Http\Controllers\Admin\PrevencionInconsistenciasController; // Controlador de prevención
use App\Http\Controllers\Admin\DashboardController; // Nuevo controlador de dashboard mejorado
use App\Http\Controllers\Admin\NotificacionController; // Controlador de notificaciones

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
    //Control Panel - Dashboard Unificado
    Route::get('/dashboard', function() {
        return redirect('/dashboard-pro');
    });
    

    // Notificaciones
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::get('/api/notificaciones', [NotificacionController::class, 'obtenerNotificacionesApi'])->name('notificaciones.api');
    Route::post('/api/notificaciones/marcar-leida/{id}', [NotificacionController::class, 'marcarComoLeida'])->name('notificaciones.marcar_leida');
    Route::post('/api/notificaciones/marcar-todas-leidas', [NotificacionController::class, 'marcarTodasComoLeidas'])->name('notificaciones.marcar_todas');
    Route::post('/api/notificaciones/limpiar-leidas', [NotificacionController::class, 'limpiarNotificacionesLeidas'])->name('notificaciones.limpiar');
    Route::get('/api/notificaciones/resumen', [NotificacionController::class, 'obtenerResumen'])->name('notificaciones.resumen');
    Route::get('/api/notificaciones/reporte', [NotificacionController::class, 'generarReporteNotificaciones'])->name('notificaciones.reporte');
    
    // Dashboard Mejorado
    Route::get('/dashboard-pro',[DashboardController::class, 'index'])->name('dashboard.pro');
    Route::get('/api/dashboard/estado-sistema',[DashboardController::class, 'getEstadoSistema'])->name('dashboard.estado');
    Route::get('/api/dashboard/metricas-vivo',[DashboardController::class, 'getMetricasEnVivo'])->name('dashboard.metricas');

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

    //Tipos de Trabajador (rutas estilo convencional)
    Route::get('tipo-trabajador', [TipoTrabajadorController::class, 'index']);
    Route::get('show-tipo-trabajador/{id}', [TipoTrabajadorController::class, 'show']);
    Route::get('add-tipo-trabajador', [TipoTrabajadorController::class, 'create']);
    Route::post('insert-tipo-trabajador', [TipoTrabajadorController::class, 'store']);
    Route::get('edit-tipo-trabajador/{id}', [TipoTrabajadorController::class, 'edit']);
    Route::put('update-tipo-trabajador/{id}', [TipoTrabajadorController::class, 'update']);
    Route::get('delete-tipo-trabajador/{id}', [TipoTrabajadorController::class, 'destroy']); // Cambiado de DELETE a GET

    //Pagos de Sueldos (solo administradores)
    Route::get('pagos-sueldos', [PagoSueldoController::class, 'index'])->name('admin.pago-sueldo.index');
    Route::get('pagos-sueldos/create', [PagoSueldoController::class, 'create'])->name('admin.pago-sueldo.create');
    Route::post('pagos-sueldos', [PagoSueldoController::class, 'store'])->name('admin.pago-sueldo.store');
    Route::get('pagos-sueldos/{id}', [PagoSueldoController::class, 'show'])->name('admin.pago-sueldo.show');
    Route::get('pagos-sueldos/{id}/edit', [PagoSueldoController::class, 'edit'])->name('admin.pago-sueldo.edit');
    Route::put('pagos-sueldos/{id}', [PagoSueldoController::class, 'update'])->name('admin.pago-sueldo.update');
    Route::post('pagos-sueldos/{id}/update-post', [PagoSueldoController::class, 'update'])->name('admin.pago-sueldo.update-post');
    Route::delete('pagos-sueldos/{id}', [PagoSueldoController::class, 'destroy'])->name('admin.pago-sueldo.destroy');
    Route::patch('pagos-sueldos/{id}/cambiar-estado', [PagoSueldoController::class, 'cambiarEstado'])->name('admin.pago-sueldo.cambiar-estado');
    Route::patch('pagos-sueldos/detalle/{id}/estado', [PagoSueldoController::class, 'cambiarEstadoDetalle'])->name('admin.pago-sueldo.cambiar-estado-detalle');
    Route::get('pagos-sueldos/{id}/pdf', [PagoSueldoController::class, 'generarPDF'])->name('admin.pago-sueldo.pdf');

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
    Route::get('ventas', [VentaController::class, 'index'])->name('admin.ventas.index'); // <- AÑADIR ESTA LÍNEA
    Route::get('add-venta', [VentaController::class, 'create']);
    Route::post('insert-venta', [VentaController::class, 'store']);
    Route::get('show-venta/{id}', [VentaController::class, 'show'])->name('ventas.show');
    Route::get('edit-venta/{id}', [VentaController::class, 'edit']);
    Route::put('update-venta/{id}', [VentaController::class, 'update'])->name('admin.ventas.update'); // Named this route
    Route::get('delete-venta/{id}', [VentaController::class, 'destroy']);
    Route::get('venta/export/pdf', [VentaController::class, 'exportPdf'])->name('ventas.export.pdf');
    Route::get('venta/export/excel', [VentaController::class, 'exportExcel'])->name('ventas.export.excel');
    Route::get('venta/export/single/pdf/{id}', [VentaController::class, 'exportSinglePdf'])->name('ventas.export.single.pdf');
    Route::post('update-trabajadores-detalle', [VentaController::class, 'updateTrabajadoresDetalle'])->name('ventas.update.trabajadores');

    // Rutas para detalles de venta (AJAX)
    Route::put('ventas/{venta}/detalles/{detalle}', [VentaController::class, 'updateDetalle'])->name('admin.ventas.detalle.update');
    Route::delete('ventas/{venta}/detalles/{detalle}', [VentaController::class, 'destroyDetalle'])->name('admin.ventas.detalle.destroy');
    Route::post('ventas/{venta}/detalles/{detalle}/restore', [VentaController::class, 'restoreDetalle'])->name('admin.ventas.detalle.restore');


    // Reactivado: Ruta para registrar pagos de ventas
    Route::post('pagos', [PagoController::class, 'store'])->name('pagos.store');
    // Reactivado: Ruta para actualizar pagos de ventas
    Route::put('pagos/{id}', [PagoController::class, 'update'])->name('pagos.update');
    // Reactivado: Ruta para eliminar pagos de ventas
    Route::delete('pagos/{id}', [PagoController::class, 'destroy'])->name('pagos.destroy');
    //Reportes Articulos
    Route::get('reportearticulos', [ReporteArticuloController::class, 'index']);
    Route::get('reportearticulos/export/pdf', [ReporteArticuloController::class, 'exportPdf'])->name('reportearticulo.export.pdf');

    //config
    Route::get('config', [ConfigController::class, 'index']);
    Route::put('update-config', [ConfigController::class, 'update']);

});

// Rutas para trabajadores
Route::middleware(['auth', 'isAdmin'])->group(function () {
    // ...existing routes...
    Route::get('/toggle-status-trabajador/{id}', [App\Http\Controllers\Admin\TrabajadorController::class, 'toggleStatus']);
    // ...existing routes...
});

// Rutas para comisiones (requiere autenticación)
Route::middleware(['auth'])->group(function () {
    Route::prefix('comisiones')->name('comisiones.')->group(function() {
        Route::get('/', [ComisionController::class, 'index'])->name('index');
        Route::get('/dashboard', [ComisionController::class, 'dashboard'])->name('dashboard');
        
        // Rutas PDF - DEBEN IR ANTES de las rutas con parámetros
        Route::get('/pdf', [ComisionController::class, 'generarPDFListado'])->name('pdf_listado');
        
        Route::get('/show/{id}', [ComisionController::class, 'show'])->name('show');
        Route::get('/{id}/pdf', [ComisionController::class, 'generarPDFIndividual'])->name('pdf_individual');
        Route::get('/{id}/detalles-meta', [ComisionController::class, 'detallesMeta'])->name('detalles_meta');
        Route::get('/por-trabajador', [ComisionController::class, 'porTrabajador'])->name('por_trabajador');
        Route::get('/por-vendedor', [ComisionController::class, 'porVendedor'])->name('por_vendedor');
        Route::post('/registrar-pago/{id}', [ComisionController::class, 'registrarPago'])->name('registrar_pago');
        Route::get('/resumen', [ComisionController::class, 'resumen'])->name('resumen');
        // Nueva ruta para procesar comisiones de vendedores por metas
        Route::post('/procesar-vendedores', [ComisionController::class, 'procesarComisionesVendedores'])->name('procesar_vendedores');
        
        // NUEVAS RUTAS CONSOLIDADAS
        Route::get('/gestion', [ComisionController::class, 'gestion'])->name('gestion');
        Route::get('/gestion/todas', [ComisionController::class, 'apiTodasComisiones'])->name('api_todas');
        Route::get('/gestion/trabajadores', [ComisionController::class, 'apiComisionesTrabajadores'])->name('api_trabajadores');
        Route::get('/gestion/vendedores', [ComisionController::class, 'apiComisionesVendedores'])->name('api_vendedores');
        
        // RUTAS DE PAGO
        Route::post('/pagar-individual', [ComisionController::class, 'pagarIndividual'])->name('pagar_individual');
        Route::post('/pagar-multiples', [ComisionController::class, 'pagarMultiples'])->name('pagar_multiples');
        Route::post('/pagar-trabajador', [ComisionController::class, 'pagarTrabajador'])->name('pagar_trabajador');
        Route::post('/pagar-vendedor', [ComisionController::class, 'pagarVendedor'])->name('pagar_vendedor');
    });
    
    // Rutas para lotes de pago (nuevo sistema híbrido)
    Route::prefix('lotes-pago')->name('lotes-pago.')->group(function() {
        Route::get('/', [LotePagoController::class, 'index'])->name('index');
        Route::get('/create', [LotePagoController::class, 'create'])->name('create');
        
        // Rutas para PDFs (deben ir ANTES de las rutas con parámetros)
        Route::get('/pdf', [LotePagoController::class, 'generarPDFListado'])->name('pdf');
        
        Route::post('/', [LotePagoController::class, 'store'])->name('store');
        Route::get('/{lotePago}', [LotePagoController::class, 'show'])->name('show');
        Route::get('/{lotePago}/edit', [LotePagoController::class, 'edit'])->name('edit');
        Route::get('/{lotePago}/pdf', [LotePagoController::class, 'generarPDFIndividual'])->name('pdf.individual');
        Route::put('/{lotePago}', [LotePagoController::class, 'update'])->name('update');
        Route::delete('/{lotePago}', [LotePagoController::class, 'destroy'])->name('destroy');
    });
    
    // Rutas para reportes de metas de ventas
    Route::prefix('reportes/metas')->name('reportes.metas.')->group(function() {
        Route::get('/', [ReporteMetasController::class, 'index'])->name('index');
        Route::get('/trabajador/{trabajador}', [ReporteMetasController::class, 'trabajadorDetalle'])->name('trabajador');
        
        // Rutas para PDFs
        Route::get('/pdf', [ReporteMetasController::class, 'generarPDFGeneral'])->name('pdf');
        Route::get('/pdf/trabajador/{trabajador}', [ReporteMetasController::class, 'generarPDFTrabajador'])->name('pdf.trabajador');
    });
    
    // APIs simples para datos de filtros
    Route::get('/api/trabajadores', function() {
        return response()->json(\App\Models\Trabajador::select('id', 'nombre', 'apellido')->get());
    });
    
    Route::get('/api/vendedores', function() {
        return response()->json(\App\Models\User::where('role_as', 1)->select('id', 'name')->get());
    });

    // Rutas para pagos de comisiones (requiere autenticación)
    Route::prefix('pagos_comisiones')->name('pagos_comisiones.')->group(function() {
        Route::get('/', [PagoComisionController::class, 'index'])->name('index');
        Route::post('/procesar-masivos', [PagoComisionController::class, 'procesarPagosMasivos'])->name('procesar_masivos');
        Route::post('/registrar/{id}', [PagoComisionController::class, 'registrarPago'])->name('registrar');
        Route::get('/historial', [PagoComisionController::class, 'historial'])->name('historial');
        Route::post('/marcar-pendientes', [PagoComisionController::class, 'marcarPendientesPago'])->name('marcar_pendientes');
        Route::post('/anular/{id}', [PagoComisionController::class, 'anularPago'])->name('anular');
        Route::get('/reporte', [PagoComisionController::class, 'generarReporte'])->name('reporte');
        
        // NUEVAS RUTAS PARA PAGOS MASIVOS
        Route::post('/masivo', [PagoComisionController::class, 'procesarPagoMasivo'])->name('pago_masivo');
    });

    // Rutas de Auditoría de Ventas e Inventario
    Route::prefix('admin/auditoria')->name('auditoria.')->group(function() {
        Route::get('/', [AuditoriaController::class, 'index'])->name('index');
        Route::post('/ejecutar', [AuditoriaController::class, 'ejecutarAuditoria'])->name('ejecutar');
        Route::get('/stock-tiempo-real', [AuditoriaController::class, 'reporteStockTiempoReal'])->name('stock_tiempo_real');
        Route::get('/alertas-stock', [AuditoriaController::class, 'alertasStock'])->name('alertas_stock');
        Route::get('/inconsistencias-ventas', [AuditoriaController::class, 'inconsistenciasVentas'])->name('inconsistencias_ventas');
        Route::get('/reporte/{fecha}', [AuditoriaController::class, 'verReporte'])->name('ver_reporte');
        
        // Nuevas rutas para funcionalidades avanzadas
        Route::post('/corregir-stock/{articuloId}', [AuditoriaController::class, 'corregirStock'])->name('corregir_stock');
        Route::post('/ajuste-manual', [AuditoriaController::class, 'ajusteManual'])->name('ajuste_manual');
        Route::post('/reabastecer/{articuloId}', [AuditoriaController::class, 'reabastecer'])->name('reabastecer');
        Route::get('/articulo-detalle/{articuloId}', [AuditoriaController::class, 'articuloDetalle'])->name('articulo_detalle');
        Route::get('/historial-movimientos/{articuloId}', [AuditoriaController::class, 'historialMovimientos'])->name('historial_movimientos');
        Route::get('/comparar-ventas/{venta1Id}/{venta2Id}', [AuditoriaController::class, 'compararVentas'])->name('comparar_ventas');
        Route::post('/enviar-notificaciones', [AuditoriaController::class, 'enviarNotificaciones'])->name('enviar_notificaciones');
        Route::get('/exportar-stock/{formato}', [AuditoriaController::class, 'exportarStock'])->name('exportar_stock');
        Route::get('/exportar-reporte/{fecha}', [AuditoriaController::class, 'exportarReporte'])->name('exportar_reporte');
        
        // Rutas para corrección de inconsistencias
        Route::post('/corregir-detalle', [AuditoriaController::class, 'corregirDetalle'])->name('corregir_detalle');
        Route::delete('/eliminar-detalle/{detalleId}', [AuditoriaController::class, 'eliminarDetalle'])->name('eliminar_detalle');
        Route::post('/fusionar-ventas/{venta1Id}/{venta2Id}', [AuditoriaController::class, 'fusionarVentas'])->name('fusionar_ventas');
        Route::delete('/eliminar-venta/{ventaId}', [AuditoriaController::class, 'eliminarVenta'])->name('eliminar_venta');
    });

    // 🆕 RUTAS PARA PREVENCIÓN DE INCONSISTENCIAS
    Route::prefix('admin/prevencion')->name('admin.prevencion.')->group(function () {
        Route::get('/test', [PrevencionInconsistenciasController::class, 'test'])->name('test');
        Route::get('/dashboard', [PrevencionInconsistenciasController::class, 'dashboard'])->name('dashboard');
        Route::get('/estado-sistema', [PrevencionInconsistenciasController::class, 'estadoSistema'])->name('estado_sistema');
        
        // OPCIÓN 1: Validación preventiva en tiempo real
        Route::post('/validacion-preventiva', [PrevencionInconsistenciasController::class, 'ejecutarValidacionPreventiva'])->name('validacion_preventiva');
        
        // OPCIÓN 2: Transacciones atómicas
        Route::post('/venta-atomica', [PrevencionInconsistenciasController::class, 'ejecutarVentaAtomica'])->name('venta_atomica');
        
        // OPCIÓN 3: Monitoreo continuo y auto-corrección
        Route::post('/monitoreo-continuo', [PrevencionInconsistenciasController::class, 'ejecutarMonitoreoContinuo'])->name('monitoreo_continuo');
        Route::post('/configurar-monitoreo', [PrevencionInconsistenciasController::class, 'configurarMonitoreoAutomatico'])->name('configurar_monitoreo');
        
        // Reportes y análisis
        Route::get('/reporte-inconsistencias', [PrevencionInconsistenciasController::class, 'generarReporteInconsistencias'])->name('reporte_inconsistencias');
    });

    // Rutas para metas de ventas
    Route::prefix('metas-ventas')->name('metas-ventas.')->group(function() {
        Route::get('/', [App\Http\Controllers\Admin\MetaVentaController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\MetaVentaController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\MetaVentaController::class, 'store'])->name('store');
        Route::get('/{metaVenta}', [App\Http\Controllers\Admin\MetaVentaController::class, 'show'])->name('show');
        Route::get('/{metaVenta}/edit', [App\Http\Controllers\Admin\MetaVentaController::class, 'edit'])->name('edit');
        Route::put('/{metaVenta}', [App\Http\Controllers\Admin\MetaVentaController::class, 'update'])->name('update');
        Route::delete('/{metaVenta}', [App\Http\Controllers\Admin\MetaVentaController::class, 'destroy'])->name('destroy');
        Route::patch('/{metaVenta}/toggle-estado', [App\Http\Controllers\Admin\MetaVentaController::class, 'toggleEstado'])->name('toggle-estado');
        Route::get('/periodo/{periodo}', [App\Http\Controllers\Admin\MetaVentaController::class, 'porPeriodo'])->name('por-periodo');
        Route::get('/api/meta-por-monto', [App\Http\Controllers\Admin\MetaVentaController::class, 'obtenerMetaPorMonto'])->name('meta-por-monto');
    });

});

//Rutas de prueba - Sin autenticación
Route::get('test', [TestController::class, 'index']);
Route::get('test/venta-con-servicio', [TestController::class, 'testVentaConServicio']);

// Ruta de prueba para dashboard sin autenticación
Route::get('test-dashboard-pro', [DashboardController::class, 'index']);

// Ruta de prueba para notificaciones sin autenticación
Route::get('test-notificaciones', [NotificacionController::class, 'index']);

// Ruta de prueba para dashboard de prevención sin autenticación
Route::get('prevencion-test', [PrevencionInconsistenciasController::class, 'dashboard'])->name('prevencion.test');
Route::get('prevencion-test/estado', [PrevencionInconsistenciasController::class, 'estadoSistema'])->name('prevencion.test.estado');
Route::post('prevencion-test/validacion', [PrevencionInconsistenciasController::class, 'ejecutarValidacionPreventiva'])->name('prevencion.test.validacion');
Route::post('prevencion-test/transaccion', [PrevencionInconsistenciasController::class, 'ejecutarVentaAtomica'])->name('prevencion.test.transaccion');
Route::post('prevencion-test/monitoreo', [PrevencionInconsistenciasController::class, 'ejecutarMonitoreoContinuo'])->name('prevencion.test.monitoreo');
Route::get('test/venta-completa', [TestController::class, 'testVentaCompleta']);
Route::get('test/ver-comisiones/{ventaId}', [App\Http\Controllers\Admin\ComisionController::class, 'verComisiones']);
Route::get('test/eliminar-venta/{ventaId}', [TestController::class, 'testEliminarVenta']);
Route::get('test/crear-datos-prueba', [DatosInicialesController::class, 'crearDatosPrueba']);

// Ruta de prueba para prevención
Route::get('/test-prevencion', function() {
    return response()->json(['mensaje' => 'Rutas de prevención funcionando', 'timestamp' => now()]);
});

// Ruta de prueba simplificada para prevención (sin autenticación)
Route::get('/prevencion-simple', function() {
    return view('admin.prevencion.dashboard-simple');
});

// ============================================================================
// RUTAS ESPECÍFICAS PARA VENDEDORES (Control de Acceso por Rol)
// ============================================================================
Route::middleware(['auth'])->group(function () {
    // Rutas solo para vendedores con filtrado automático
    Route::prefix('vendedor')->name('vendedor.')->group(function() {
        // Dashboard de comisiones personal
        Route::get('/mis-comisiones', [ComisionController::class, 'dashboardVendedor'])->name('mis_comisiones');
        
        // Mis ventas filtradas automáticamente
        Route::get('/mis-ventas', [VentaController::class, 'misVentas'])->name('mis_ventas');
        
        // Mi rendimiento personal
        Route::get('/mi-rendimiento', [DashboardController::class, 'miRendimiento'])->name('mi_rendimiento');
        
        // Vista de metas (solo lectura para vendedores)
        Route::get('/metas-disponibles', [ReporteMetasController::class, 'metasVendedor'])->name('metas_disponibles');
    });
});
