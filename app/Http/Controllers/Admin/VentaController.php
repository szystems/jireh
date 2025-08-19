<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VentaFormRequest;
use App\Http\Requests\VentaEditFormRequest;
use App\Models\Venta;
use Illuminate\Http\Request;
use App\Models\Articulo;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\DetalleVenta;
use App\Models\Descuento;
use App\Models\Config;
use App\Models\User;
use App\Models\Trabajador;
use App\Models\Comision;
use App\Traits\StockValidation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VentasExport;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{
    use StockValidation;
    public function index(Request $request)
    {
        $query = Venta::query();

        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        } else {
            $query->where('fecha', '>=', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        } else {
            $query->where('fecha', '<=', \Carbon\Carbon::now()->format('Y-m-d'));
        }

        if ($request->filled('numero_factura')) {
            $query->where('numero_factura', 'like', '%' . $request->numero_factura . '%');
        }

        if ($request->filled('cliente')) {
            $query->where('cliente_id', $request->cliente);
        }

        if ($request->filled('vehiculo')) {
            $query->where('vehiculo_id', $request->vehiculo);
        }

        if ($request->filled('tipo_venta')) {
            $query->where('tipo_venta', $request->tipo_venta);
        }

        if ($request->filled('usuario')) {
            $query->where('usuario_id', $request->usuario);
        }

        // Nuevo filtro para estado de la venta
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Nuevo filtro para estado de pago
        if ($request->filled('estado_pago')) {
            $query->where('estado_pago', $request->estado_pago);
        }

        $ventas = $query->with(['detalleVentas.articulo'])->orderBy('fecha', 'desc')->get();

        $clientes = Cliente::all();
        $vehiculos = Vehiculo::all();
        $usuarios = User::all();
        $config = Config::first();

        return view('admin.venta.index', compact('ventas', 'clientes', 'vehiculos', 'usuarios', 'config'));
    }

    public function create(Request $request)
    {
        $config = Config::first();
        $todosArticulos = Articulo::with('unidad')->get();
        $clientes = Cliente::all();
        $descuentos = Descuento::where('estado', 1)->get();

        // Obtener trabajadores de tipo Car Wash
        $trabajadoresCarwash = Trabajador::whereHas('tipoTrabajador', function($query) {
            $query->where('nombre', 'Car Wash')
                  ->where('estado', true);
        })->where('estado', true)->get();

        // Obtener cliente_id de la URL si existe
        $cliente_id = $request->query('cliente_id');
        $cliente_seleccionado = null;

        if ($cliente_id) {
            $cliente_seleccionado = Cliente::find($cliente_id);
        }

        return view('admin.venta.create', compact(
            'todosArticulos',
            'clientes',
            'config',
            'descuentos',
            'trabajadoresCarwash',
            'cliente_id',
            'cliente_seleccionado'
        ));
    }    /**
     * Actualiza el stock de un artículo y sus componentes si es un servicio
     * DEPRECATED: Usar actualizarStockSeguro del trait StockValidation
     *
     * @param int $articuloId ID del artículo
     * @param float $cantidad Cantidad a restar del stock
     * @param bool $restar True para restar, false para sumar al stock
     * @param int $ventaId ID de la venta (para auditoría)
     * @return void
     */
    private function actualizarStockArticulo($articuloId, $cantidad, $restar = true, $ventaId = null)
    {
        try {
            $operacion = $restar ? 'restar' : 'sumar';
            $descripcion = $restar ? 'Venta realizada' : 'Venta cancelada/restaurada';
            
            $resultado = $this->actualizarStockSeguro($articuloId, $cantidad, $operacion, $ventaId, $descripcion);
            
            if (!$resultado['exitoso']) {
                throw new \Exception("Error al actualizar stock para artículo ID {$articuloId}");
            }
            
        } catch (\Exception $e) {
            Log::error("Error en actualizarStockArticulo", [
                'articulo_id' => $articuloId,
                'cantidad' => $cantidad,
                'restar' => $restar,
                'venta_id' => $ventaId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }public function store(VentaFormRequest $request)
    {
        try {
            DB::beginTransaction();

            // Registrar datos de depuración para ver qué está recibiendo el servidor
            Log::info('Datos recibidos en store:', [
                'trabajadores_carwash' => $request->trabajadores_carwash,
                'detalles' => $request->detalles,
                'articulos_servicios' => array_filter($request->detalles ?? [], function($detalle) {
                    $articulo = Articulo::find($detalle['articulo_id'] ?? 0);
                    return $articulo && $articulo->tipo === 'servicio';
                })
            ]);

            $validated = $request->validated();
            $config = Config::first();

            // VALIDACIÓN DE STOCK ANTES DE CREAR LA VENTA
            $erroresStock = [];
            foreach ($validated['detalles'] as $index => $detalle) {
                $validacion = $this->validarStockDisponible($detalle['articulo_id'], $detalle['cantidad']);
                if (!$validacion['valido']) {
                    $erroresStock[] = $validacion['mensaje'];
                }
            }

            if (!empty($erroresStock)) {
                throw new \Exception('Errores de stock: ' . implode('; ', $erroresStock));
            }

            $venta = Venta::create(array_merge(
                $request->only('cliente_id', 'vehiculo_id', 'numero_factura', 'fecha', 'tipo_venta', 'estado', 'estado_pago'),
                ['usuario_id' => Auth::user()->id]
            ));

            foreach ($validated['detalles'] as $index => $detalle) {
                $detalle['porcentaje_impuestos'] = $config->impuesto;
                $articulo = Articulo::find($detalle['articulo_id']);
                if ($articulo) {
                    $detalle['precio_costo'] = $articulo->precio_compra;
                    $detalle['precio_venta'] = $articulo->precio_venta;
                    $this->actualizarStockArticulo($detalle['articulo_id'], $detalle['cantidad'], true, $venta->id);
                }
                $detalleVenta = $venta->detalleVentas()->create($detalle);

                // Asignar trabajadores de carwash al detalle de venta, si existen
                if ($articulo && $articulo->tipo === 'servicio') {
                    // Intentar obtener los trabajadores asignados por varios métodos
                    $trabajadoresCarwash = $request->trabajadores_carwash[$index] ??
                                          $request->input("trabajadores_carwash.{$index}") ??
                                          [];

                    Log::info("Procesando detalle #{$index} (ID: {$detalleVenta->id}) - Artículo tipo: {$articulo->tipo}", [
                        'trabajadores_encontrados' => $trabajadoresCarwash,
                        'indice_usado' => $index
                    ]);

                    if (!empty($trabajadoresCarwash)) {
                        $detalleVenta->asignarTrabajadores($trabajadoresCarwash, $articulo->comision_carwash);
                        Log::info("Trabajadores asignados para detalle {$detalleVenta->id}: " . implode(', ', (array)$trabajadoresCarwash), [
                            'detalle_id' => $detalleVenta->id,
                            'trabajadores' => $trabajadoresCarwash
                        ]);
                    } else {
                        Log::warning("No se encontraron trabajadores para asignar al servicio (detalle {$detalleVenta->id})");
                    }
                }            }

            // Generar todas las comisiones asociadas a esta venta
            $venta->generarComisiones();            // Registrar información en el log
            Log::info('Venta registrada exitosamente', ['venta_id' => $venta->id]);
            Log::info('Comisiones generadas para venta', ['venta_id' => $venta->id]);

            DB::commit();
            return redirect('show-venta/'.$venta->id)->with('status', __('Venta registrada exitosamente.'));
        } catch (\Exception $e) {
            DB::rollBack();
            // Registrar el error detallado en el log
            Log::error('Error al registrar venta: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()->with('error', 'Error al registrar la venta: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $venta = Venta::with([
            'pagos',
            'detalleVentas.articulo.mecanico',
            'detalleVentas.trabajadoresCarwash'
        ])->findOrFail($id);
        $config = Config::first();
        return view('admin.venta.show', compact('venta', 'config'));
    }

    public function edit($id)
    {
        $venta = Venta::with(['detalleVentas.articulo.unidad', 'detalleVentas.trabajadoresCarwash', 'cliente', 'vehiculo'])->findOrFail($id);
        $todosArticulos = Articulo::with('unidad') // Cargar relaciones necesarias para la config JS
                                ->where('estado', 1)
                                ->get();
        $articulos = $todosArticulos; // $articulos parece ser usado para la config JS, $todosArticulos para el @foreach inicial
        $clientes = Cliente::where('estado', 1)->orderBy('nombre')->get();
        $config = Config::first();
        $todosDescuentos = Descuento::where('estado', 1)->orderBy('nombre')->get(); // Para la config JS y el @foreach
        $descuentos = $todosDescuentos; // Mantener consistencia si se usa $descuentos en el @foreach

        $trabajadoresCarwash = Trabajador::with('tipoTrabajador')
                                        ->whereHas('tipoTrabajador', function($query) {
                                            $query->where('nombre', 'Car Wash')
                                                  ->where('estado', true); // Asegúrate que el estado en tipo_trabajadors sea 'activo' o un booleano. Si es string, usa 'activo'.
                                        })
                                        ->where('estado', true) // Asumiendo que 'estado' en la tabla 'trabajadors' es un booleano.
                                        ->orderBy('nombre')
                                        ->orderBy('apellido')
                                        ->get();

        return view('admin.venta.edit', compact(
            'venta',
            'todosArticulos',
            'articulos',
            'clientes',
            'config',
            'descuentos', // Pasar $descuentos que ahora es $todosDescuentos
            'todosDescuentos', // Pasar $todosDescuentos para la config JS
            'trabajadoresCarwash'
        ));
    }    public function update(VentaEditFormRequest $request, $id)
    {
        // LOG CRÍTICO: Verificar que llega la petición
        Log::critical('🚨 PETICIÓN UPDATE RECIBIDA', [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'venta_id' => $id,
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl()
        ]);
        
        try {
            DB::beginTransaction();            // Log COMPLETO para depuración            Log::info('=== INICIO DEBUG EDICIÓN VENTA ===', []);
            Log::info('Iniciando actualización de venta', ['venta_id' => $id]);
            Log::info('TODOS los datos recibidos:', ['data' => $request->all()]);
            Log::info('Datos específicos de trabajadores_carwash:', ['data' => $request->input('trabajadores_carwash', 'NO_ENCONTRADO')]);
            Log::info('Datos específicos de nuevos_trabajadores_carwash:', ['data' => $request->input('nuevos_trabajadores_carwash', 'NO_ENCONTRADO')]);
            Log::info('Headers de la petición:', ['headers' => $request->headers->all()]);
            Log::info('Método de la petición:', ['method' => $request->method()]);
            Log::info('=== FIN DEBUG INICIAL ===', []);

            $venta = Venta::findOrFail($id);

            // Actualizar datos básicos siempre
            $venta->update($request->only('cliente_id', 'vehiculo_id', 'numero_factura', 'fecha', 'tipo_venta', 'estado', 'estado_pago'));
            Log::info('Venta actualizada con datos básicos', ['venta_id' => $venta->id]);

            $config = Config::first();

            // 1. Procesar detalles a eliminar
            $detallesAEliminar = $request->input('detalles_a_eliminar', []);
            if (is_array($detallesAEliminar) && count($detallesAEliminar) > 0) {
                Log::info('Eliminando detalles de venta por array detalles_a_eliminar:', ['detalles_ids' => $detallesAEliminar]);

                foreach ($detallesAEliminar as $detalleId) {
                    $detalle = DetalleVenta::find($detalleId);
                    if ($detalle) {                        Log::info("Procesando eliminación detalle ID: {$detalleId}", []);                        // Restaurar el stock del artículo y sus componentes si es servicio
                        $this->actualizarStockArticulo($detalle->articulo_id, $detalle->cantidad, false, $venta->id);
                        Log::info("Stock actualizado para artículo ID: {$detalle->articulo_id}", []);

                        // Eliminar vínculos de trabajadores antes de eliminar el detalle
                        $detalle->trabajadoresCarwash()->detach();
                        Log::info("Trabajadores desvinculados del detalle ID: {$detalleId}", []);

                        // Eliminar el detalle
                        $detalle->delete();
                        Log::info("Detalle ID: {$detalleId} eliminado", []);
                    } else {
                        Log::warning("Detalle ID: {$detalleId} no encontrado");
                    }
                }
            } else {
                Log::info('No hay detalles en detalles_a_eliminar para eliminar', []);
            }

            // También procesar detalles marcados para eliminar con campo eliminar=1
            if ($request->has('detalles')) {
                foreach ($request->detalles as $detalleId => $detalleData) {
                    if (isset($detalleData['eliminar']) && $detalleData['eliminar'] == '1') {
                        $detalle = DetalleVenta::find($detalleId);
                        if ($detalle) {                            Log::info("Procesando eliminación detalle ID: {$detalleId} (via campo eliminar)", []);                            // Restaurar el stock del artículo y sus componentes si es servicio
                            $this->actualizarStockArticulo($detalle->articulo_id, $detalle->cantidad, false, $venta->id);
                            Log::info("Stock actualizado para artículo ID: {$detalle->articulo_id}", []);

                            // Eliminar el detalle
                            $detalle->delete();
                            Log::info("Detalle ID: {$detalleId} eliminado (via campo eliminar)", []);
                        }
                    }
                }
            } else {
                Log::info('No hay detalles a eliminar', []);
            }

            // 2. Procesar detalles a mantener (actualizar)
            if ($request->has('detalles')) {
                foreach ($request->detalles as $detalleId => $detalleData) {
                    // Ignorar detalles marcados para eliminar
                    if (isset($detalleData['eliminar']) && $detalleData['eliminar'] == '1') {
                        Log::info("Saltando actualización del detalle ID: {$detalleId} porque está marcado para eliminar", []);
                        continue;
                    }

                    // Verificar si el detalle existe y no está en la lista de eliminados
                    if (!in_array($detalleId, $detallesAEliminar)) {
                        $detalle = DetalleVenta::find($detalleId);
                        if ($detalle) {
                            Log::info("Actualizando detalle ID: {$detalleId}", $detalleData);

                            $cantidadAnterior = $detalle->cantidad;
                            $cantidadNueva = $detalleData['cantidad'];
                            $articuloIdAnterior = $detalle->articulo_id;
                            $articuloIdNuevo = $detalleData['articulo_id'];

                            // LÓGICA MEJORADA: Solo validar/actualizar stock si hay cambios reales
                            $cambioArticulo = $articuloIdAnterior != $articuloIdNuevo;
                            $cambioCantidad = $cantidadAnterior != $cantidadNueva;

                            if ($cambioArticulo) {
                                Log::info("Cambio de artículo detectado", [
                                    'articulo_anterior' => $articuloIdAnterior,
                                    'articulo_nuevo' => $articuloIdNuevo
                                ]);
                                
                                // Restaurar stock del artículo anterior completamente
                                $this->actualizarStockArticulo($articuloIdAnterior, $cantidadAnterior, false, $venta->id);
                                Log::info("Stock restaurado para artículo anterior ID: {$articuloIdAnterior}");

                                // Validar y descontar stock del artículo nuevo
                                $validacionStock = $this->validarStockDisponible($articuloIdNuevo, $cantidadNueva, $venta->id);
                                if (!$validacionStock['valido']) {
                                    throw new \Exception($validacionStock['mensaje']);
                                }
                                $this->actualizarStockArticulo($articuloIdNuevo, $cantidadNueva, true, $venta->id);
                                Log::info("Stock descontado para artículo nuevo ID: {$articuloIdNuevo}");

                            } elseif ($cambioCantidad) {
                                Log::info("Cambio de cantidad detectado", [
                                    'cantidad_anterior' => $cantidadAnterior,
                                    'cantidad_nueva' => $cantidadNueva
                                ]);

                                $diferenciaCantidad = $cantidadNueva - $cantidadAnterior;
                                
                                if ($diferenciaCantidad > 0) {
                                    // Aumentó la cantidad: validar y descontar solo el incremento
                                    $validacionStock = $this->validarStockDisponible($articuloIdAnterior, $diferenciaCantidad, $venta->id);
                                    if (!$validacionStock['valido']) {
                                        throw new \Exception($validacionStock['mensaje']);
                                    }
                                    $this->actualizarStockArticulo($articuloIdAnterior, $diferenciaCantidad, true, $venta->id);
                                    Log::info("Stock descontado por incremento", ['incremento' => $diferenciaCantidad]);
                                    
                                } elseif ($diferenciaCantidad < 0) {
                                    // Disminuyó la cantidad: devolver al stock la diferencia
                                    $cantidadADevolver = abs($diferenciaCantidad);
                                    $this->actualizarStockArticulo($articuloIdAnterior, $cantidadADevolver, false, $venta->id);
                                    Log::info("Stock restaurado por reducción", ['cantidad_devuelta' => $cantidadADevolver]);
                                }
                                // Si diferenciaCantidad == 0, no hacer nada con el stock
                            } else {
                                Log::info("No hay cambios en artículo ni cantidad - stock sin modificar");
                            }

                            // Actualizar el detalle
                            $detalle->update($detalleData);
                            Log::info("Detalle ID: {$detalleId} actualizado correctamente", []);                            // Actualizar trabajadores de carwash
                            $articulo = Articulo::find($detalleData['articulo_id']);
                            if ($articulo && $articulo->tipo === 'servicio') {
                                // Intentar múltiples formatos para obtener trabajadores
                                $trabajadoresCarwash = [];
                                
                                // Formato 1: trabajadores_carwash[ID][]
                                $formato1 = $request->input("trabajadores_carwash.{$detalleId}", []);
                                if (!empty($formato1)) {
                                    $trabajadoresCarwash = $formato1;
                                }
                                
                                // Formato 2: trabajadores_carwash[ID] (sin array)
                                if (empty($trabajadoresCarwash)) {
                                    $formato2 = $request->input("trabajadores_carwash.{$detalleId}");
                                    if ($formato2) {
                                        $trabajadoresCarwash = is_array($formato2) ? $formato2 : [$formato2];
                                    }
                                }
                                
                                // Formato 3: buscar en todos los trabajadores_carwash
                                if (empty($trabajadoresCarwash)) {
                                    $todosTrabajadores = $request->input('trabajadores_carwash', []);
                                    if (isset($todosTrabajadores[$detalleId])) {
                                        $trabajadoresCarwash = $todosTrabajadores[$detalleId];
                                    }
                                }
                                
                                // Si no es array, convertirlo
                                if (!is_array($trabajadoresCarwash)) {
                                    $trabajadoresCarwash = $trabajadoresCarwash ? [$trabajadoresCarwash] : [];
                                }

                                // Filtrar valores nulos o vacíos
                                $trabajadoresCarwash = array_filter($trabajadoresCarwash, function($value) {
                                    return $value !== null && $value !== '' && $value > 0;
                                });

                                // Convertir a enteros
                                $trabajadoresCarwash = array_map('intval', $trabajadoresCarwash);
                                $trabajadoresCarwash = array_unique($trabajadoresCarwash);

                                Log::info("Procesando trabajadores para detalle ID: {$detalleId}", [
                                    'trabajadores' => $trabajadoresCarwash,
                                    'count' => count($trabajadoresCarwash)
                                ]);

                                // Asignar trabajadores (esto ya limpia los anteriores)
                                $detalle->asignarTrabajadores($trabajadoresCarwash, $articulo->comision_carwash);
                                
                                // Regenerar comisiones para este detalle
                                $detalle->refresh();
                                $detalle->generarComisionesCarwash(true);
                                  Log::info("Trabajadores actualizados para detalle ID: {$detalleId}", []);
                            }
                        } else {
                            Log::warning("No se encontró el detalle ID: {$detalleId} para actualizar");
                        }
                    } else {
                        Log::info("Saltando actualización del detalle ID: {$detalleId} porque está en la lista de eliminados", []);
                    }
                }
            } else {
                Log::info('No hay detalles existentes para actualizar', []);
            }            // 3. Procesar nuevos detalles
            Log::info('=== INICIANDO PROCESAMIENTO DE NUEVOS DETALLES ===', []);
            if ($request->has('nuevos_detalles')) {                Log::info('DATOS RECIBIDOS de nuevos_detalles:', $request->input('nuevos_detalles'));
                Log::info('CONTEO de nuevos_detalles:', ['count' => count($request->input('nuevos_detalles'))]);
                
                foreach ($request->nuevos_detalles as $index => $nuevoDetalle) {
                    Log::info("=== PROCESANDO NUEVO DETALLE ÍNDICE: {$index} ===", []);
                    Log::info("Datos del nuevo detalle:", $nuevoDetalle);
                    
                    // Verificar si ya existe un detalle con estos datos para evitar duplicación
                    $detallesExistentes = $venta->detalleVentas()
                        ->where('articulo_id', $nuevoDetalle['articulo_id'])
                        ->where('cantidad', $nuevoDetalle['cantidad'])
                        ->where('created_at', '>', now()->subMinute()) // Solo verificar creados en el último minuto
                        ->get();
                    
                    if ($detallesExistentes->count() > 0) {
                        Log::warning("DUPLICACIÓN DETECTADA: Ya existen {$detallesExistentes->count()} detalles similares para artículo {$nuevoDetalle['articulo_id']}, SALTANDO");
                        foreach ($detallesExistentes as $existe) {
                            Log::info("Detalle existente ID: {$existe->id}, creado: {$existe->created_at}", []);
                        }
                        continue; // Saltar este detalle para evitar duplicación
                    }
                
                    // Registrar lo que estamos procesando para depuración
                    Log::info("Procesando nuevo detalle con índice: {$index}", $nuevoDetalle);
                    
                    // Agregar el porcentaje de impuestos desde la configuración
                    $nuevoDetalle['porcentaje_impuestos'] = $config->impuesto;
                    
                    // Agregar el usuario_id (requerido en la tabla detalle_ventas)
                    $nuevoDetalle['usuario_id'] = Auth::user()->id;

                    // Obtener artículo y guardar precios
                    $articulo = Articulo::find($nuevoDetalle['articulo_id']);
                    if ($articulo) {
                        // Agregar precios del artículo
                        $nuevoDetalle['precio_costo'] = $articulo->precio_compra;
                        $nuevoDetalle['precio_venta'] = $articulo->precio_venta;

                        // VALIDAR STOCK ANTES DE PROCEDER
                        $validacionStock = $this->validarStockDisponible($nuevoDetalle['articulo_id'], $nuevoDetalle['cantidad'], $venta->id);
                        if (!$validacionStock['valido']) {
                            throw new \Exception($validacionStock['mensaje']);
                        }
                        Log::info("Validación de stock exitosa para nuevo detalle", [
                            'articulo_id' => $nuevoDetalle['articulo_id'],
                            'cantidad' => $nuevoDetalle['cantidad'],
                            'stock_disponible' => $validacionStock['stock_actual']
                        ]);

                        // Actualizar el stock del artículo y sus componentes si es servicio
                        $this->actualizarStockArticulo($nuevoDetalle['articulo_id'], $nuevoDetalle['cantidad'], true, $venta->id);
                        Log::info("Stock descontado para artículo ID: {$nuevoDetalle['articulo_id']}", []);
                    }

                    // Crear el nuevo detalle
                    $detalle = $venta->detalleVentas()->create($nuevoDetalle);
                    Log::info("Nuevo detalle creado con ID: {$detalle->id}", []);

                    // Asignar trabajadores de carwash al detalle de venta, si existen
                    if (isset($nuevoDetalle['articulo_id']) && $articulo && $articulo->tipo === 'servicio') {
                        // Intentar múltiples formatos para obtener trabajadores
                        $trabajadoresCarwash = [];
                        
                        // Formato 1: nuevos_trabajadores_carwash[index][]
                        $formato1 = $request->input("nuevos_trabajadores_carwash.{$index}", []);
                        if (!empty($formato1)) {
                            $trabajadoresCarwash = $formato1;
                        }
                        
                        // Formato 2: nuevos_detalles[index][trabajadores_carwash][]
                        if (empty($trabajadoresCarwash)) {
                            $formato2 = $request->input("nuevos_detalles.{$index}.trabajadores_carwash", []);
                            if (!empty($formato2)) {
                                $trabajadoresCarwash = $formato2;
                            }
                        }
                        
                        // Formato 3: buscar en todos los nuevos_trabajadores_carwash
                        if (empty($trabajadoresCarwash)) {
                            $todosTrabajadores = $request->input('nuevos_trabajadores_carwash', []);
                            if (isset($todosTrabajadores[$index])) {
                                $trabajadoresCarwash = $todosTrabajadores[$index];
                            }
                        }
                        
                        // Si no es array, convertirlo
                        if (!is_array($trabajadoresCarwash)) {
                            $trabajadoresCarwash = $trabajadoresCarwash ? [$trabajadoresCarwash] : [];
                        }

                        // Filtrar valores nulos o vacíos
                        $trabajadoresCarwash = array_filter($trabajadoresCarwash, function($value) {
                            return $value !== null && $value !== '' && $value > 0;
                        });
                        
                        // Convertir a enteros
                        $trabajadoresCarwash = array_map('intval', $trabajadoresCarwash);
                        $trabajadoresCarwash = array_unique($trabajadoresCarwash);
                        
                        Log::info("Procesando trabajadores para nuevo detalle índice: {$index}", [
                            'trabajadores' => $trabajadoresCarwash,
                            'count' => count($trabajadoresCarwash)
                        ]);

                        if (!empty($trabajadoresCarwash)) {
                            $detalle->asignarTrabajadores($trabajadoresCarwash, $articulo->comision_carwash);
                            $detalle->generarComisionesCarwash();
                            Log::info("Trabajadores asignados al nuevo detalle ID: {$detalle->id}", []);
                        } else {
                            Log::info("No se encontraron trabajadores para asignar al nuevo detalle ID: {$detalle->id}", []);
                        }
                    }
                    
                    Log::info("=== NUEVO DETALLE COMPLETADO: {$detalle->id} ===", []);
                }
            } else {
                Log::info('No hay nuevos detalles para procesar', []);
            }

            // Regenerar comisiones: eliminar todas las comisiones de esta venta y regenerarlas
            Comision::where('venta_id', $venta->id)->delete();
            
            // Regenerar comisiones para cada detalle individualmente
            foreach ($venta->fresh()->detalleVentas as $detalle) {
                // Regenerar comisiones de carwash si aplica
                if ($detalle->articulo && $detalle->articulo->tipo === 'servicio') {
                    $detalle->generarComisionesCarwash(true);
                }                // Regenerar comisiones de carwash si aplica
                if ($detalle->articulo && $detalle->articulo->tipo === 'servicio') {
                    $detalle->generarComisionesCarwash(true);
                }
                
                // Regenerar comisiones de mecánico si aplica
                $detalle->generarComisionMecanico();
            }

            // Registrar información en el log
            Log::info('Venta actualizada exitosamente', ['venta_id' => $venta->id]);
            Log::info('Comisiones regeneradas para venta', ['venta_id' => $venta->id]);
            DB::commit();
            
            // Verificar si es una solicitud AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Venta actualizada exitosamente.'),
                    'redirect_url' => url('show-venta/'.$venta->id.'?success=true')
                ]);
            }
            
            return redirect('show-venta/'.$venta->id.'?success=true')->with('status', __('Venta actualizada exitosamente.'));
        } catch (\Exception $e) {
            DB::rollBack();

            // Registrar el error detallado en el log
            Log::error('Error al actualizar venta: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Mensaje de error más específico para el usuario
            $errorMessage = $e->getMessage();
            
            // Personalizar el mensaje para errores de stock
            if (strpos($errorMessage, 'stock negativo') !== false) {
                $errorMessage = 'Error de stock: ' . $errorMessage . '. Por favor, revise las cantidades y el inventario disponible.';
            } else if (strpos($errorMessage, 'stock') !== false) {
                $errorMessage = 'Error de inventario: ' . $errorMessage;
            }

            // Verificar si es una solicitud AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => $errorMessage])->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $venta = Venta::findOrFail($id);            // Restaurar stock de todos los detalles antes de eliminar
            foreach ($venta->detalleVentas as $detalle) {
                // Restaurar stock del artículo y sus componentes si es servicio
                $this->actualizarStockArticulo($detalle->articulo_id, $detalle->cantidad, false, $venta->id);
            }

            // Actualizar el estado de las comisiones asociadas a la venta
            $venta->comisiones()->update(['estado' => 'cancelado']);

            // Registrar información en el log
            Log::info('Comisiones actualizadas a canceladas para venta ID: ' . $venta->id, []);

            // Marcar venta como eliminada
            $venta->update(['estado' => false]);

            DB::commit();

            return redirect('ventas')->with('status', 'Venta eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar la venta: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        // Obtener ventas con todos los datos relacionados necesarios
        $ventas = $this->getFilteredVentas($request)->load([
            'cliente',
            'vehiculo',
            'usuario',
            'detalleVentas.articulo.unidad',
            'detalleVentas.descuento',
            'pagos.usuario'
        ]);

        $config = Config::first();
        $clientes = Cliente::all();
        $usuarios = User::all();

        // Preparar datos de filtros para mostrarlos en el reporte
        $filters = [
            'fecha_desde' => $request->input('fecha_desde') ?? \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'),
            'fecha_hasta' => $request->input('fecha_hasta') ?? \Carbon\Carbon::now()->format('Y-m-d'),
            'numero_factura' => $request->input('numero_factura'),
            'cliente' => $request->filled('cliente') ? $clientes->find($request->cliente)->nombre : null,
            'tipo_venta' => $request->input('tipo_venta'),
            'usuario' => $request->filled('usuario') ? $usuarios->find($request->usuario)->name : null,
            'estado' => $request->filled('estado') ? ($request->estado == '1' ? 'Activa' : 'Cancelada') : null,
            'estado_pago' => $request->filled('estado_pago') ? ucfirst($request->estado_pago) : null,
        ];        // Generar PDF
        $pdf = Pdf::loadView('admin.venta.pdf', compact('ventas', 'config', 'filters'));
        $pdf->setPaper('a4', 'landscape'); // Usar formato apaisado para incluir más datos

        return $pdf->stream('reporte_ventas_'.date('Y-m-d').'.pdf');
    }

    public function exportSinglePdf($id)
    {
        // Cargar todos los datos relacionados necesarios para el PDF
        $venta = Venta::with([
            'detalleVentas.articulo.unidad',
            'detalleVentas.articulo.mecanico',
            'detalleVentas.descuento',
            'detalleVentas.trabajadoresCarwash',
            'cliente',
            'vehiculo',
            'usuario',
            'pagos.usuario'
        ])->findOrFail($id);

        $config = Config::first();

        // Calcular totales para el reporte
        $totales = $this->calcularTotalesVenta($venta);        // Generar PDF
        $pdf = Pdf::loadView('admin.venta.single_pdf', compact('venta', 'config', 'totales'));

        return $pdf->stream('venta_' . $venta->id . '_' . date('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filename = 'reporte_ventas_'.date('Y-m-d').'.xlsx';
        return Excel::download(new VentasExport($request), $filename);
    }

    private function getFilteredVentas(Request $request)
    {
        $query = Venta::query();

        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        } else {
            $query->where('fecha', '>=', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        } else {
            $query->where('fecha', '<=', \Carbon\Carbon::now()->format('Y-m-d'));
        }

        if ($request->filled('numero_factura')) {
            $query->where('numero_factura', 'like', '%' . $request->numero_factura . '%');
        }

        if ($request->filled('cliente')) {
            $query->where('cliente_id', $request->cliente);
        }

        if ($request->filled('vehiculo')) {
            $query->where('vehiculo_id', $request->vehiculo);
        }

        if ($request->filled('tipo_venta')) {
            $query->where('tipo_venta', $request->tipo_venta);
        }

        if ($request->filled('usuario')) {
            $query->where('usuario_id', $request->usuario);
        }

        // Filtros adicionales que hemos agregado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('estado_pago')) {
            $query->where('estado_pago', $request->estado_pago);
        }

        return $query->orderBy('fecha', 'desc')->get();
    }

    /**
     * Función auxiliar para calcular todos los totales de una venta
     */
    private function calcularTotalesVenta($venta)
    {
        $totalDescuentos = 0;
        $totalVenta = 0;
        $subtotalSinDescuentoTotal = 0;
        $totalImpuestos = 0;
        $totalCostoCompra = 0;
        $totalPagado = 0;

        // Calcular totales de los detalles
        foreach ($venta->detalleVentas as $detalle) {
            $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;
            $subtotalSinDescuentoTotal += $subtotalSinDescuento;

            // Calcular descuento
            $montoDescuento = 0;
            if ($detalle->descuento_id) {
                $descuento = $detalle->descuento;
                if ($descuento) {
                    $montoDescuento = $subtotalSinDescuento * ($descuento->porcentaje_descuento / 100);
                }
            }
            $totalDescuentos += $montoDescuento;

            // Subtotal con descuento
            $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;
            $totalVenta += $subtotalConDescuento;

            // Impuestos
            $impuestoDetalle = $subtotalConDescuento * ($detalle->porcentaje_impuestos ?? 0) / 100;
            $totalImpuestos += $impuestoDetalle;

            // Costo de compra (incluir precio_costo + comisiones)
            $costoCompra = $detalle->precio_costo * $detalle->cantidad;
            
            // Agregar comisiones de trabajadores carwash/mecánico si es un servicio
            if($detalle->articulo && $detalle->articulo->tipo === 'servicio') {
                // Sumar comisiones de trabajadores carwash asignados
                foreach($detalle->trabajadoresCarwash as $trabajador) {
                    if($trabajador->pivot && $trabajador->pivot->monto_comision) {
                        $costoCompra += $trabajador->pivot->monto_comision;
                    }
                }
                
                // Sumar comisión de mecánico si aplica
                if($detalle->articulo->mecanico_id && $detalle->articulo->costo_mecanico > 0) {
                    $costoCompra += $detalle->articulo->costo_mecanico * $detalle->cantidad;
                }
            }
            
            $totalCostoCompra += $costoCompra;
        }

        // Calcular total pagado
        if ($venta->pagos) {
            $totalPagado = $venta->pagos->sum('monto');
        }

        // Ganancia neta
        $gananciaNeta = $totalVenta - $totalImpuestos - $totalCostoCompra;

        return compact(
            'totalDescuentos',
            'totalVenta',
            'subtotalSinDescuentoTotal',
            'totalImpuestos',
            'totalCostoCompra',
            'totalPagado',
            'gananciaNeta'
        );
    }

    /**
     * Actualiza los trabajadores asignados a un detalle de venta vía AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateTrabajadoresDetalle(Request $request)
    {
        try {
            $detalleId = $request->input('detalle_id');
            $trabajadoresIds = $request->input('trabajadores_ids', []);

            // Validar datos de entrada
            if (!$detalleId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID del detalle no proporcionado'
                ], 400);
            }

            // Obtener el detalle de venta
            $detalle = DetalleVenta::find($detalleId);

            if (!$detalle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Detalle de venta no encontrado'
                ], 404);
            }

            // Verificar que el detalle corresponda a un servicio
            $articulo = $detalle->articulo;
            if (!$articulo || $articulo->tipo != 'servicio') {
                return response()->json([
                    'success' => false,
                    'message' => 'Este detalle no corresponde a un servicio'
                ], 400);
            }

            // Limpiar y preparar array de trabajadores
            if (!is_array($trabajadoresIds)) {
                $trabajadoresIds = [$trabajadoresIds];
            }

            // Filtrar valores nulos o vacíos
            $trabajadoresIds = array_filter($trabajadoresIds, function($value) {
                return $value !== null && $value !== '';
            });

            // Asegurarnos de que el array de trabajadores no tenga duplicados
            $trabajadoresIds = array_unique($trabajadoresIds);

            // Convertir los IDs a enteros para evitar problemas de comparación
            $trabajadoresIds = array_map('intval', $trabajadoresIds);

            // Registrar la operación en el log
            Log::info("Actualizando trabajadores para detalle ID: {$detalleId} vía AJAX", [
                'trabajadores' => $trabajadoresIds,
                'count' => count($trabajadoresIds)
            ]);

            // Asignar trabajadores al detalle
            $resultado = $detalle->asignarTrabajadores($trabajadoresIds, $articulo->comision_carwash);

            // Verificar si se asignaron correctamente
            $totalAsignados = $detalle->trabajadoresCarwash()->count();

            if ($totalAsignados > 0 || empty($trabajadoresIds)) {
                return response()->json([
                    'success' => true,
                    'message' => $totalAsignados > 0
                        ? "Se asignaron {$totalAsignados} trabajadores correctamente"
                        : "Se eliminaron todos los trabajadores asignados",
                    'trabajadores_count' => $totalAsignados,
                    'detalle_id' => $detalleId
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudieron asignar los trabajadores'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error("Error al actualizar trabajadores: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar solo las ventas del vendedor autenticado
     */
    public function misVentas(Request $request)
    {
        $usuarioId = auth()->user()->id;
        $query = Venta::with(['cliente', 'detalleVentas', 'vehiculo'])
            ->where('usuario_id', $usuarioId);

        // Aplicar filtros si existen
        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        } else {
            $query->where('fecha', '>=', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        } else {
            $query->where('fecha', '<=', \Carbon\Carbon::now()->format('Y-m-d'));
        }

        if ($request->filled('numero_factura')) {
            $query->where('numero_factura', 'like', '%' . $request->numero_factura . '%');
        }

        if ($request->filled('cliente')) {
            $query->whereHas('cliente', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->cliente . '%');
            });
        }

        $ventas = $query->orderBy('fecha', 'desc')->paginate(15);
        
        // Calcular totales del vendedor
        $totalVentas = Venta::where('usuario_id', $usuarioId)
            ->with('detalleVentas')
            ->get()
            ->sum(function($venta) {
                return $venta->detalleVentas->sum('sub_total');
            });
            
        $ventasEstesMes = Venta::where('usuario_id', $usuarioId)
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->with('detalleVentas')
            ->get()
            ->sum(function($venta) {
                return $venta->detalleVentas->sum('sub_total');
            });
        
        $config = Config::first();
        
        return view('admin.ventas.mis-ventas', compact(
            'ventas', 'config', 'totalVentas', 'ventasEstesMes'
        ));
    }
}
