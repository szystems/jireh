<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Articulo;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Trabajador;
use App\Models\TrabajadorDetalleVenta;
use App\Models\Comision;
use App\Models\Config;
use App\Models\MetaVenta;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TestController extends Controller
{
    /**
     * Constructor: eliminamos el middleware de autenticación para pruebas
     */
    public function __construct()
    {
        // No requiere autenticación para pruebas
    }
    
    /**
     * Muestra la página de pruebas
     */
    public function index()
    {
        return view('admin.test.index');
    }
    /**
     * Realiza una prueba de creación de venta con un servicio y trabajadores asignados,
     * y verifica la generación de comisiones, incluyendo comisiones por meta para vendedores
     */
    public function testVentaConServicio()
    {
        try {
            DB::beginTransaction();

            // Registrar que estamos iniciando la prueba
            Log::info('TEST: Iniciando prueba de creación de venta con servicio');

            // 1. Buscar un servicio disponible
            $servicio = Articulo::where('tipo', 'servicio')->first();
            if (!$servicio) {
                Log::error('TEST: No se encontró ningún artículo de tipo servicio para la prueba');
                return response()->json(['error' => 'No se encontró ningún servicio para la prueba'], 404);
            }

            Log::info('TEST: Servicio encontrado', [
                'id' => $servicio->id,
                'nombre' => $servicio->nombre,
                'precio_venta' => $servicio->precio_venta,
                'comision_carwash' => $servicio->comision_carwash
            ]);

            // 2. Buscar un cliente y un vehículo
            $cliente = Cliente::first();
            $vehiculo = Vehiculo::where('cliente_id', $cliente->id)->first();

            if (!$vehiculo) {
                // Si el cliente no tiene vehículos, buscamos otro cliente que sí tenga
                $clienteConVehiculo = Cliente::whereHas('vehiculos')->first();
                if ($clienteConVehiculo) {
                    $cliente = $clienteConVehiculo;
                    $vehiculo = Vehiculo::where('cliente_id', $cliente->id)->first();
                } else {
                    Log::error('TEST: No se encontró ningún cliente con vehículos para la prueba');
                    return response()->json(['error' => 'No se encontró ningún cliente con vehículos para la prueba'], 404);
                }
            }

            Log::info('TEST: Cliente y vehículo encontrados', [
                'cliente_id' => $cliente->id,
                'cliente_nombre' => $cliente->nombre,
                'vehiculo_id' => $vehiculo->id,
                'vehiculo_placa' => $vehiculo->placa
            ]);

            // 3. Buscar trabajadores de tipo Car Wash
            $trabajadores = Trabajador::whereHas('tipoTrabajador', function($query) {
                $query->where('nombre', 'Car Wash')->where('estado', true);
            })->where('estado', true)->take(2)->get();

            if ($trabajadores->isEmpty()) {
                Log::error('TEST: No se encontraron trabajadores de Car Wash para la prueba');
                return response()->json(['error' => 'No se encontraron trabajadores de Car Wash para la prueba'], 404);
            }

            $trabajadorIds = $trabajadores->pluck('id')->toArray();
            Log::info('TEST: Trabajadores encontrados', [
                'ids' => $trabajadorIds,
                'nombres' => $trabajadores->pluck('nombre_completo')->toArray()
            ]);

            // 4. Crear o verificar meta de venta para el vendedor
            $usuarioId = Auth::id() ?: 1; // Usuario activo o el primero
            $metaVenta = MetaVenta::where('usuario_id', $usuarioId)
                        ->where('estado', true)
                        ->first();

            if (!$metaVenta) {
                // Crear una meta de venta para el usuario si no existe
                $metaVenta = new MetaVenta();
                $metaVenta->usuario_id = $usuarioId;
                $metaVenta->monto_minimo = 100; // Monto mínimo bajo para garantizar que se cumpla la meta
                $metaVenta->monto_maximo = 10000;
                $metaVenta->porcentaje_comision = 2.5; // 2.5% de comisión
                $metaVenta->periodo = 'mensual';
                $metaVenta->fecha_inicio = Carbon::now()->startOfMonth();
                $metaVenta->fecha_fin = Carbon::now()->endOfMonth();
                $metaVenta->estado = true;
                $metaVenta->save();

                Log::info('TEST: Meta de venta creada para el vendedor', [
                    'id' => $metaVenta->id,
                    'usuario_id' => $metaVenta->usuario_id,
                    'monto_minimo' => $metaVenta->monto_minimo,
                    'porcentaje_comision' => $metaVenta->porcentaje_comision
                ]);
            } else {
                Log::info('TEST: Meta de venta existente encontrada para el vendedor', [
                    'id' => $metaVenta->id,
                    'usuario_id' => $metaVenta->usuario_id,
                    'monto_minimo' => $metaVenta->monto_minimo,
                    'porcentaje_comision' => $metaVenta->porcentaje_comision
                ]);
            }

            // 5. Crear la venta
            $config = Config::first();

            $venta = new Venta();
            $venta->cliente_id = $cliente->id;
            $venta->vehiculo_id = $vehiculo->id;
            $venta->fecha = now();
            $venta->tipo_venta = 'Car Wash';
            $venta->numero_factura = 'TEST-' . rand(1000, 9999);
            $venta->usuario_id = $usuarioId;
            $venta->estado = true;
            $venta->estado_pago = 'pendiente';
            $venta->save();

            Log::info('TEST: Venta creada', [
                'id' => $venta->id,
                'numero_factura' => $venta->numero_factura
            ]);

            // 6. Crear detalle de venta para el servicio
            $detalleVenta = new DetalleVenta();
            $detalleVenta->venta_id = $venta->id;
            $detalleVenta->articulo_id = $servicio->id;
            $detalleVenta->cantidad = 1;
            $detalleVenta->precio_costo = $servicio->precio_compra;
            $detalleVenta->precio_venta = $servicio->precio_venta;
            $detalleVenta->sub_total = $servicio->precio_venta;
            $detalleVenta->usuario_id = $venta->usuario_id;
            $detalleVenta->porcentaje_impuestos = $config->impuesto ?? 0;
            $detalleVenta->save();

            Log::info('TEST: Detalle de venta creado', [
                'id' => $detalleVenta->id,
                'articulo_id' => $detalleVenta->articulo_id,
                'sub_total' => $detalleVenta->sub_total
            ]);

            // 7. Asignar trabajadores al detalle de venta
            $asignaciones = $detalleVenta->asignarTrabajadores($trabajadorIds, $servicio->comision_carwash);

            Log::info('TEST: Trabajadores asignados', [
                'cantidad_asignaciones' => count($asignaciones),
                'asignaciones' => collect($asignaciones)->map(function($a) {
                    return [
                        'id' => $a->id,
                        'trabajador_id' => $a->trabajador_id,
                        'monto_comision' => $a->monto_comision
                    ];
                })->toArray()
            ]);

            // 8. Generar comisiones
            $venta->generarComisiones();

            // 9. Verificar comisiones generadas
            $comisiones = Comision::where('venta_id', $venta->id)->get();

            Log::info('TEST: Comisiones generadas', [
                'cantidad' => $comisiones->count(),
                'comisiones' => $comisiones->map(function($c) {
                    return [
                        'id' => $c->id,
                        'tipo' => $c->tipo_comision,
                        'monto' => $c->monto,
                        'estado' => $c->estado,
                        'receptor_id' => $c->commissionable_id,
                        'receptor_tipo' => $c->commissionable_type
                    ];
                })->toArray()
            ]);

            // 10. Verificar específicamente la comisión del vendedor
            $comisionVendedor = Comision::where('venta_id', $venta->id)
                               ->where('commissionable_type', 'App\Models\User')
                               ->where('commissionable_id', $usuarioId)
                               ->first();

            if ($comisionVendedor) {
                Log::info('TEST: Comisión del vendedor generada correctamente', [
                    'id' => $comisionVendedor->id,
                    'tipo' => $comisionVendedor->tipo_comision,
                    'monto' => $comisionVendedor->monto,
                    'estado' => $comisionVendedor->estado
                ]);
            } else {
                Log::warning('TEST: No se generó comisión para el vendedor', [
                    'usuario_id' => $usuarioId,
                    'meta_id' => $metaVenta->id,
                    'venta_id' => $venta->id
                ]);
            }

            DB::commit();

            // 11. Retornar resultado
            return response()->json([
                'success' => true,
                'message' => 'Prueba completada exitosamente',
                'venta_id' => $venta->id,
                'detalle_id' => $detalleVenta->id,
                'asignaciones' => count($asignaciones),
                'comisiones' => $comisiones->count(),
                'comision_vendedor' => $comisionVendedor ? [
                    'id' => $comisionVendedor->id,
                    'monto' => $comisionVendedor->monto
                ] : 'No generada'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TEST: Error en prueba de venta con servicio', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Elimina una venta de prueba y verifica que las comisiones asociadas sean eliminadas
     */
    public function testEliminarVenta($ventaId)
    {
        try {
            DB::beginTransaction();

            Log::info('TEST: Iniciando prueba de eliminación de venta', [
                'venta_id' => $ventaId
            ]);

            // 1. Verificar comisiones existentes antes de eliminar
            $comisionesAntes = Comision::where('venta_id', $ventaId)->get();
            Log::info('TEST: Comisiones antes de eliminar venta', [
                'cantidad' => $comisionesAntes->count(),
                'comisiones' => $comisionesAntes->map(function($c) {
                    return [
                        'id' => $c->id,
                        'tipo' => $c->tipo_comision,
                        'monto' => $c->monto
                    ];
                })->toArray()
            ]);

            // 2. Obtener la venta
            $venta = Venta::find($ventaId);
            if (!$venta) {
                return response()->json(['error' => 'Venta no encontrada'], 404);
            }

            // 3. Guardar información para reporte
            $ventaInfo = [
                'id' => $venta->id,
                'numero_factura' => $venta->numero_factura,
                'monto_total' => $venta->detalles->sum('sub_total')
            ];

            // 4. Eliminar la venta (esto debería eliminar también las comisiones por el onDelete cascade)
            $venta->delete();
            
            // 5. Verificar que las comisiones se hayan eliminado
            $comisionesDespues = Comision::where('venta_id', $ventaId)->get();
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Prueba de eliminación completada exitosamente',
                'venta_eliminada' => $ventaInfo,
                'comisiones_antes' => $comisionesAntes->count(),
                'comisiones_despues' => $comisionesDespues->count(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TEST: Error en prueba de eliminación de venta', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Realiza una prueba completa creando una venta con múltiples productos y servicios
     * para verificar la correcta generación de comisiones para todos los tipos de receptor
     */
    public function testVentaCompleta()
    {
        try {
            DB::beginTransaction();
            
            // Registrar inicio de la prueba
            Log::info('TEST COMPLETO: Iniciando prueba de venta con productos y servicios');
            
            $resultados = [
                'exito' => true,
                'detalle' => 'Prueba de venta con diferentes tipos de artículos',
                'fecha' => now()->format('Y-m-d H:i:s'),
                'articulos' => [],
                'comisiones' => []
            ];
            
            // 1. Buscar un cliente y un vehículo
            $cliente = Cliente::whereHas('vehiculos')->first();
            if (!$cliente) {
                throw new \Exception('No se encontró ningún cliente con vehículos para la prueba');
            }
            
            $vehiculo = Vehiculo::where('cliente_id', $cliente->id)->first();
            
            $resultados['cliente'] = [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'vehiculo' => [
                    'id' => $vehiculo->id,
                    'placa' => $vehiculo->placa,
                    'modelo' => $vehiculo->modelo
                ]
            ];
            
            // 2. Buscar trabajadores para diferentes roles
            $mecanicos = Trabajador::whereHas('tipoTrabajador', function($query) {
                $query->where('nombre', 'like', '%Mecanico%')->orWhere('nombre', 'like', '%Mecánico%');
            })->where('estado', true)->take(2)->get();
            
            $carwasheros = Trabajador::whereHas('tipoTrabajador', function($query) {
                $query->where('nombre', 'like', '%Car Wash%')->orWhere('nombre', 'like', '%Carwash%');
            })->where('estado', true)->take(2)->get();
            
            if ($mecanicos->isEmpty()) {
                throw new \Exception('No se encontraron mecánicos para la prueba');
            }
            
            if ($carwasheros->isEmpty()) {
                throw new \Exception('No se encontraron trabajadores de Car Wash para la prueba');
            }
            
            $resultados['trabajadores'] = [
                'mecanicos' => $mecanicos->map(function($t) {
                    return ['id' => $t->id, 'nombre' => $t->nombre . ' ' . $t->apellido];
                })->toArray(),
                'carwasheros' => $carwasheros->map(function($t) {
                    return ['id' => $t->id, 'nombre' => $t->nombre . ' ' . $t->apellido];
                })->toArray()
            ];
            
            // 3. Buscar artículos para la prueba
            // 3.1 Servicio de mecánica
            $servicioMecanica = Articulo::where('tipo', 'servicio')
                ->where(function($query) {
                    $query->where('nombre', 'like', '%reparacion%')
                        ->orWhere('nombre', 'like', '%mantenimiento%')
                        ->orWhere('nombre', 'like', '%mecánica%')
                        ->orWhere('nombre', 'like', '%mecanica%');
                })
                ->first();
                
            // 3.2 Servicio de car wash
            $servicioCarwash = Articulo::where('tipo', 'servicio')
                ->where(function($query) {
                    $query->where('nombre', 'like', '%lavado%')
                        ->orWhere('nombre', 'like', '%car wash%')
                        ->orWhere('nombre', 'like', '%limpieza%');
                })
                ->first();
                
            // 3.3 Producto normal (repuesto, aceite, etc.)
            $producto = Articulo::where('tipo', 'producto')
                ->where('stock', '>', 0)
                ->where(function($query) {
                    $query->where('nombre', 'like', '%filtro%')
                        ->orWhere('nombre', 'like', '%aceite%')
                        ->orWhere('nombre', 'like', '%repuesto%');
                })
                ->first();
                
            if (!$servicioMecanica) {
                $servicioMecanica = Articulo::where('tipo', 'servicio')->first();
                if (!$servicioMecanica) {
                    throw new \Exception('No se encontró ningún servicio de mecánica para la prueba');
                }
            }
            
            if (!$servicioCarwash) {
                $servicioCarwash = Articulo::where('tipo', 'servicio')->where('id', '!=', $servicioMecanica->id)->first();
                if (!$servicioCarwash) {
                    throw new \Exception('No se encontró ningún servicio de car wash para la prueba');
                }
            }
            
            if (!$producto) {
                $producto = Articulo::where('tipo', 'producto')->where('stock', '>', 0)->first();
                if (!$producto) {
                    throw new \Exception('No se encontró ningún producto para la prueba');
                }
            }
            
            $resultados['articulos'] = [
                'servicio_mecanica' => [
                    'id' => $servicioMecanica->id,
                    'nombre' => $servicioMecanica->nombre,
                    'precio' => $servicioMecanica->precio_venta,
                    'comision_mecanico' => 100.00 // Valor fijo para prueba
                ],
                'servicio_carwash' => [
                    'id' => $servicioCarwash->id,
                    'nombre' => $servicioCarwash->nombre,
                    'precio' => $servicioCarwash->precio_venta,
                    'comision_carwash' => $servicioCarwash->comision_carwash ?? 5.00
                ],
                'producto' => [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'precio' => $producto->precio_venta,
                    'stock' => $producto->stock
                ]
            ];
            
            // 4. Crear o verificar meta de venta para el vendedor
            $usuarioId = Auth::id() ?: 1; // Usuario activo o el primero
            $metaVenta = MetaVenta::where('usuario_id', $usuarioId)
                        ->where('estado', true)
                        ->first();
                        
            if (!$metaVenta) {
                // Crear una meta de venta para el usuario si no existe
                $metaVenta = new MetaVenta();
                $metaVenta->usuario_id = $usuarioId;
                $metaVenta->monto_minimo = 100;
                $metaVenta->monto_maximo = 10000;
                $metaVenta->porcentaje_comision = 2.5;
                $metaVenta->periodo = 'mensual';
                $metaVenta->fecha_inicio = Carbon::now()->startOfMonth();
                $metaVenta->fecha_fin = Carbon::now()->endOfMonth();
                $metaVenta->estado = true;
                $metaVenta->save();
            }
            
            $resultados['meta_venta'] = [
                'id' => $metaVenta->id,
                'usuario_id' => $metaVenta->usuario_id,
                'monto_minimo' => $metaVenta->monto_minimo,
                'porcentaje_comision' => $metaVenta->porcentaje_comision
            ];
            
            // 5. Crear la venta
            $config = Config::first();
            
            $venta = new Venta();
            $venta->cliente_id = $cliente->id;
            $venta->vehiculo_id = $vehiculo->id;
            $venta->fecha_hora = now();
            $venta->tipo_venta = 'Completa';
            $venta->numero_factura = 'TEST-COMPLETA-' . rand(1000, 9999);
            $venta->usuario_id = $usuarioId;
            $venta->total = 0; // Se actualizará después
            $venta->estado = true;
            $venta->estado_pago = 'pendiente';
            $venta->save();
            
            $resultados['venta'] = [
                'id' => $venta->id,
                'numero_factura' => $venta->numero_factura,
                'fecha' => $venta->fecha_hora
            ];
            
            // 6. Agregar detalles de venta
            $detalles = [];
            
            // 6.1 Agregar el servicio de mecánica
            $detalleMecanica = new DetalleVenta();
            $detalleMecanica->venta_id = $venta->id;
            $detalleMecanica->articulo_id = $servicioMecanica->id;
            $detalleMecanica->cantidad = 1;
            $detalleMecanica->precio_costo = $servicioMecanica->precio_compra;
            $detalleMecanica->precio_venta = $servicioMecanica->precio_venta;
            $detalleMecanica->sub_total = $servicioMecanica->precio_venta;
            $detalleMecanica->usuario_id = $venta->usuario_id;
            $detalleMecanica->porcentaje_impuestos = $config->impuesto ?? 0;
            $detalleMecanica->save();
            
            // Asignar mecánicos al detalle de venta
            $asignacionesMecanicos = $detalleMecanica->asignarTrabajadores($mecanicos->pluck('id')->toArray(), 100.00);
            
            $detalles[] = [
                'id' => $detalleMecanica->id,
                'articulo' => $servicioMecanica->nombre,
                'tipo' => 'servicio_mecanica',
                'subtotal' => $detalleMecanica->sub_total,
                'asignaciones' => count($asignacionesMecanicos)
            ];
            
            // 6.2 Agregar el servicio de car wash
            $detalleCarwash = new DetalleVenta();
            $detalleCarwash->venta_id = $venta->id;
            $detalleCarwash->articulo_id = $servicioCarwash->id;
            $detalleCarwash->cantidad = 1;
            $detalleCarwash->precio_costo = $servicioCarwash->precio_compra;
            $detalleCarwash->precio_venta = $servicioCarwash->precio_venta;
            $detalleCarwash->sub_total = $servicioCarwash->precio_venta;
            $detalleCarwash->usuario_id = $venta->usuario_id;
            $detalleCarwash->porcentaje_impuestos = $config->impuesto ?? 0;
            $detalleCarwash->save();
            
            // Asignar trabajadores de car wash al detalle de venta
            $asignacionesCarwash = $detalleCarwash->asignarTrabajadores($carwasheros->pluck('id')->toArray(), $servicioCarwash->comision_carwash ?? 5.00);
            
            $detalles[] = [
                'id' => $detalleCarwash->id,
                'articulo' => $servicioCarwash->nombre,
                'tipo' => 'servicio_carwash',
                'subtotal' => $detalleCarwash->sub_total,
                'asignaciones' => count($asignacionesCarwash)
            ];
            
            // 6.3 Agregar el producto
            $detalleProducto = new DetalleVenta();
            $detalleProducto->venta_id = $venta->id;
            $detalleProducto->articulo_id = $producto->id;
            $detalleProducto->cantidad = 1;
            $detalleProducto->precio_costo = $producto->precio_compra;
            $detalleProducto->precio_venta = $producto->precio_venta;
            $detalleProducto->sub_total = $producto->precio_venta;
            $detalleProducto->usuario_id = $venta->usuario_id;
            $detalleProducto->porcentaje_impuestos = $config->impuesto ?? 0;
            $detalleProducto->save();
            
            $detalles[] = [
                'id' => $detalleProducto->id,
                'articulo' => $producto->nombre,
                'tipo' => 'producto',
                'subtotal' => $detalleProducto->sub_total
            ];
            
            // Actualizar el total de la venta
            $venta->total = $detalleMecanica->sub_total + $detalleCarwash->sub_total + $detalleProducto->sub_total;
            $venta->save();
            
            $resultados['venta']['total'] = $venta->total;
            $resultados['detalles'] = $detalles;
            
            // 7. Generar comisiones
            $venta->generarComisiones();
            
            // 8. Verificar comisiones generadas
            $comisiones = Comision::where('venta_id', $venta->id)->get();
            
            $resultados['comisiones'] = [
                'total' => $comisiones->count(),
                'detalle' => $comisiones->map(function($c) {
                    return [
                        'id' => $c->id,
                        'tipo' => $c->tipo_comision,
                        'monto' => $c->monto,
                        'receptor_tipo' => $c->commissionable_type == 'App\\Models\\Trabajador' ? 'Trabajador' : 'Vendedor',
                        'receptor_id' => $c->commissionable_id,
                        'receptor_nombre' => $c->commissionable_type == 'App\\Models\\Trabajador' 
                            ? $c->commissionable->nombre . ' ' . $c->commissionable->apellido
                            : $c->commissionable->name
                    ];
                })->toArray()
            ];
            
            // Verificar que hay comisiones para cada tipo esperado
            $comisionesMecanicos = $comisiones->where('tipo_comision', 'mecanico')->count();
            $comisionesCarwash = $comisiones->where('tipo_comision', 'carwash')->count();
            $comisionesVendedor = $comisiones->where('tipo_comision', 'meta_venta')->count();
            
            $resultados['verificacion'] = [
                'comisiones_mecanicos' => $comisionesMecanicos,
                'comisiones_carwash' => $comisionesCarwash,
                'comisiones_vendedor' => $comisionesVendedor,
                'todos_tipos_generados' => ($comisionesMecanicos > 0 && $comisionesCarwash > 0 && $comisionesVendedor > 0)
            ];
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Prueba completa ejecutada exitosamente',
                'resultados' => $resultados
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('TEST COMPLETO: Error en la prueba', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}
