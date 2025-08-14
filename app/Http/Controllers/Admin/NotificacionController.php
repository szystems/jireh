<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Articulo;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Config;
use App\Models\Comision;
use App\Models\MetaVenta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = $this->obtenerNotificaciones();
        $config = Config::first();
        
        return view('admin.notificaciones.index', compact('notificaciones', 'config'));
    }

    public function obtenerNotificaciones()
    {
        $notificaciones = collect();
        
        // Notificaciones de Stock Bajo
        $stockBajo = Articulo::whereRaw('stock <= stock_minimo')
            ->where('stock_minimo', '>', 0)
            ->where('stock', '>', 0)
            ->get();
            
        foreach ($stockBajo as $index => $articulo) {
            // Para stock bajo, usar updated_at con alguna variación para hacer más realista
            $fechaBase = $articulo->updated_at ?? $articulo->created_at;
            $fechaNotificacion = Carbon::parse($fechaBase)->subHours(rand(1, 72)); // Variar entre 1-72 horas
            
            $notificaciones->push([
                'id' => 'stock_bajo_' . $articulo->id,
                'tipo' => 'stock_bajo',
                'prioridad' => 'media',
                'titulo' => 'Stock Bajo',
                'mensaje' => "El artículo '{$articulo->nombre}' tiene stock bajo ({$articulo->stock} unidades)",
                'fecha' => $fechaNotificacion,
                'leida' => false,
                'datos' => [
                    'articulo_id' => $articulo->id,
                    'stock_actual' => $articulo->stock,
                    'stock_minimo' => $articulo->stock_minimo,
                ],
                'accion' => [
                    'url' => "/show-articulo/{$articulo->id}",
                    'texto' => 'Ver Artículo'
                ]
            ]);
        }

        // Notificaciones de Stock Crítico
        $stockCritico = Articulo::where('stock', '<=', 0)
            ->where('stock_minimo', '>', 0)
            ->get();
            
        foreach ($stockCritico as $index => $articulo) {
            // Para stock crítico, usar fechas más recientes
            $fechaBase = $articulo->updated_at ?? $articulo->created_at;
            $fechaNotificacion = Carbon::parse($fechaBase)->subHours(rand(1, 24)); // Variar entre 1-24 horas
            
            $notificaciones->push([
                'id' => 'stock_critico_' . $articulo->id,
                'tipo' => 'stock_critico',
                'prioridad' => 'alta',
                'titulo' => 'Stock Crítico',
                'mensaje' => "El artículo '{$articulo->nombre}' está sin stock",
                'fecha' => $fechaNotificacion,
                'leida' => false,
                'datos' => [
                    'articulo_id' => $articulo->id,
                    'stock_actual' => $articulo->stock,
                    'stock_minimo' => $articulo->stock_minimo,
                ],
                'accion' => [
                    'url' => "/show-articulo/{$articulo->id}",
                    'texto' => 'Reabastecer'
                ]
            ]);
        }

        // Notificaciones de Ventas Importantes (últimos 7 días)
        $ventasImportantes = Venta::with(['cliente', 'detalleVentas'])
            ->where('fecha', '>=', Carbon::now()->subDays(7))
            ->where('estado', true)
            ->get()
            ->filter(function($venta) {
                return $venta->detalleVentas->sum('sub_total') > 1000;
            });
            
        foreach ($ventasImportantes as $venta) {
            $total = $venta->detalleVentas->sum('sub_total');
            $notificaciones->push([
                'id' => 'venta_importante_' . $venta->id,
                'tipo' => 'venta_importante',
                'prioridad' => 'media',
                'titulo' => 'Venta Importante',
                'mensaje' => "Venta de $" . number_format($total, 2) . " realizada a {$venta->cliente->nombre}",
                'fecha' => Carbon::parse($venta->fecha),
                'leida' => false,
                'datos' => [
                    'venta_id' => $venta->id,
                    'monto' => $total,
                    'cliente' => $venta->cliente->nombre,
                ],
                'accion' => [
                    'url' => "/show-venta/{$venta->id}",
                    'texto' => 'Ver Venta'
                ]
            ]);
        }

        // Notificaciones de Clientes Nuevos (últimos 30 días)
        $clientesNuevos = Cliente::where('created_at', '>=', Carbon::now()->subDays(30))->get();
        
        foreach ($clientesNuevos as $cliente) {
            $notificaciones->push([
                'id' => 'cliente_nuevo_' . $cliente->id,
                'tipo' => 'cliente_nuevo',
                'prioridad' => 'baja',
                'titulo' => 'Nuevo Cliente',
                'mensaje' => "Se registró un nuevo cliente: {$cliente->nombre}",
                'fecha' => $cliente->created_at,
                'leida' => false,
                'datos' => [
                    'cliente_id' => $cliente->id,
                    'nombre' => $cliente->nombre,
                ],
                'accion' => [
                    'url' => "/show-cliente/{$cliente->id}",
                    'texto' => 'Ver Cliente'
                ]
            ]);
        }

        // Notificaciones de Comisiones Vencidas
        $comisionesVencidas = Comision::where('estado', 'pendiente')
            ->where('fecha_calculo', '<', Carbon::now()->subDays(30))
            ->get();
            
        if ($comisionesVencidas->count() > 0) {
            // Usar la fecha de la comisión más antigua como referencia
            $fechaComisionMasAntigua = $comisionesVencidas->min('fecha_calculo');
            
            $notificaciones->push([
                'id' => 'comisiones_vencidas_' . now()->format('Y-m-d'),
                'tipo' => 'comisiones_vencidas',
                'prioridad' => 'alta',
                'titulo' => 'Comisiones Vencidas',
                'mensaje' => "{$comisionesVencidas->count()} comisiones pendientes por más de 30 días",
                'fecha' => Carbon::parse($fechaComisionMasAntigua),
                'leida' => false,
                'datos' => [
                    'cantidad' => $comisionesVencidas->count(),
                    'monto_total' => $comisionesVencidas->sum('monto'),
                ],
                'accion' => [
                    'url' => "/comisiones/gestion?estado=pendiente",
                    'texto' => 'Gestionar Comisiones'
                ]
            ]);
        }

        // Notificaciones de Metas Incumplidas
        $mesActual = now()->format('Y-m');
        $metas = MetaVenta::where('estado', true)->get();
        
        if ($metas->count() > 0) {
            // Obtener ventas del mes agrupadas por vendedor
            $ventasPorVendedor = Venta::with('detalleVentas')
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->get()
                ->groupBy('usuario_id')
                ->map(function ($ventas) {
                    return $ventas->sum(function ($venta) {
                        return $venta->detalleVentas->sum('sub_total');
                    });
                });

            $metasAlcanzadas = 0;
            $totalVendedores = count($ventasPorVendedor);
            
            if ($totalVendedores > 0) {
                foreach ($ventasPorVendedor as $usuarioId => $totalVentas) {
                    $metaCorrespondiente = MetaVenta::where('estado', true)
                        ->where('monto_minimo', '<=', $totalVentas)
                        ->where(function ($query) use ($totalVentas) {
                            $query->whereNull('monto_maximo')
                                  ->orWhere('monto_maximo', '>=', $totalVentas);
                        })
                        ->orderBy('monto_minimo', 'desc')
                        ->first();

                    if ($metaCorrespondiente) {
                        $metasAlcanzadas++;
                    }
                }
                
                $porcentajeCumplimiento = round(($metasAlcanzadas / $totalVendedores) * 100, 1);
                
                if ($porcentajeCumplimiento < 50) {
                    $notificaciones->push([
                        'id' => 'metas_incumplidas_' . now()->format('Y-m'),
                        'tipo' => 'metas_incumplidas',
                        'prioridad' => 'alta',
                        'titulo' => 'Metas Incumplidas',
                        'mensaje' => "Solo {$porcentajeCumplimiento}% de cumplimiento de metas este mes",
                        'fecha' => Carbon::now()->startOfMonth(),
                        'leida' => false,
                        'datos' => [
                            'porcentaje_cumplimiento' => $porcentajeCumplimiento,
                            'metas_alcanzadas' => $metasAlcanzadas,
                            'total_vendedores' => $totalVendedores,
                        ],
                        'accion' => [
                            'url' => "/metas-ventas",
                            'texto' => 'Revisar Metas'
                        ]
                    ]);
                }
            }
        }

        // Notificaciones de Objetivos de Ventas
        $ventasDelMes = Venta::with('detalleVentas')
            ->whereBetween('fecha', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->where('estado', true)
            ->get()
            ->sum(function($venta) {
                return $venta->detalleVentas->sum('sub_total');
            });
            
        $objetivoMensual = 50000; // Esto debería venir de configuración
        $porcentajeObjetivo = ($ventasDelMes / $objetivoMensual) * 100;
        
        if ($porcentajeObjetivo >= 90) {
            $notificaciones->push([
                'id' => 'objetivo_alcanzado_' . now()->format('Y-m'),
                'tipo' => 'objetivo_alcanzado',
                'prioridad' => 'alta',
                'titulo' => 'Objetivo Alcanzado',
                'mensaje' => "¡Felicitaciones! Has alcanzado el {$porcentajeObjetivo}% del objetivo mensual",
                'fecha' => Carbon::now()->startOfMonth(),
                'leida' => false,
                'datos' => [
                    'porcentaje' => $porcentajeObjetivo,
                    'ventas_actuales' => $ventasDelMes,
                    'objetivo' => $objetivoMensual,
                ],
                'accion' => [
                    'url' => "/ventas",
                    'texto' => 'Ver Ventas'
                ]
            ]);
        }

        // Obtener notificaciones leídas de la sesión
        $notificacionesLeidas = session()->get('notificaciones_leidas', []);
        
        // Marcar las notificaciones como leídas según la sesión
        $notificaciones = $notificaciones->map(function ($notificacion) use ($notificacionesLeidas) {
            $notificacion['leida'] = in_array($notificacion['id'], $notificacionesLeidas);
            
            // Asegurar que la fecha sea un objeto Carbon
            if (!($notificacion['fecha'] instanceof Carbon)) {
                $notificacion['fecha'] = Carbon::parse($notificacion['fecha']);
            }
            
            return $notificacion;
        });

        return $notificaciones->sortByDesc('fecha')->values();
    }

    public function marcarComoLeida($id)
    {
        // Obtener notificaciones leídas de la sesión
        $notificacionesLeidas = session()->get('notificaciones_leidas', []);
        
        // Agregar esta notificación a las leídas
        if (!in_array($id, $notificacionesLeidas)) {
            $notificacionesLeidas[] = $id;
            session()->put('notificaciones_leidas', $notificacionesLeidas);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Notificación marcada como leída'
        ]);
    }

    public function marcarTodasComoLeidas()
    {
        // Obtener todas las notificaciones actuales
        $todasLasNotificaciones = $this->obtenerNotificaciones();
        
        // Extraer todos los IDs
        $todosLosIds = $todasLasNotificaciones->pluck('id')->toArray();
        
        // Guardar todos los IDs como leídos en la sesión
        session()->put('notificaciones_leidas', $todosLosIds);
        
        return response()->json([
            'success' => true,
            'message' => 'Todas las notificaciones marcadas como leídas',
            'cantidad' => count($todosLosIds)
        ]);
    }

    public function limpiarNotificacionesLeidas()
    {
        // Limpiar la sesión de notificaciones leídas
        session()->forget('notificaciones_leidas');
        
        return response()->json([
            'success' => true,
            'message' => 'Historial de notificaciones leídas limpiado'
        ]);
    }

    public function obtenerResumen()
    {
        $notificaciones = $this->obtenerNotificaciones();
        
        $resumen = [
            'total' => $notificaciones->count(),
            'no_leidas' => $notificaciones->where('leida', false)->count(),
            'por_prioridad' => [
                'alta' => $notificaciones->where('prioridad', 'alta')->count(),
                'media' => $notificaciones->where('prioridad', 'media')->count(),
                'baja' => $notificaciones->where('prioridad', 'baja')->count(),
            ],
            'por_tipo' => [
                'stock_critico' => $notificaciones->where('tipo', 'stock_critico')->count(),
                'stock_bajo' => $notificaciones->where('tipo', 'stock_bajo')->count(),
                'venta_importante' => $notificaciones->where('tipo', 'venta_importante')->count(),
                'cliente_nuevo' => $notificaciones->where('tipo', 'cliente_nuevo')->count(),
                'comisiones_vencidas' => $notificaciones->where('tipo', 'comisiones_vencidas')->count(),
                'metas_incumplidas' => $notificaciones->where('tipo', 'metas_incumplidas')->count(),
                'objetivo_alcanzado' => $notificaciones->where('tipo', 'objetivo_alcanzado')->count(),
            ]
        ];

        return response()->json($resumen);
    }

    public function obtenerNotificacionesApi()
    {
        $notificaciones = $this->obtenerNotificaciones();
        
        return response()->json([
            'notificaciones' => $notificaciones->take(10), // Solo las últimas 10
            'total' => $notificaciones->count(),
            'no_leidas' => $notificaciones->where('leida', false)->count(),
        ]);
    }

    public function generarReporteNotificaciones()
    {
        $notificaciones = $this->obtenerNotificaciones();
        
        $reporte = [
            'fecha_generacion' => now(),
            'total_notificaciones' => $notificaciones->count(),
            'notificaciones_por_tipo' => $notificaciones->groupBy('tipo')->map(function($grupo) {
                return [
                    'cantidad' => $grupo->count(),
                    'prioridad_alta' => $grupo->where('prioridad', 'alta')->count(),
                    'prioridad_media' => $grupo->where('prioridad', 'media')->count(),
                    'prioridad_baja' => $grupo->where('prioridad', 'baja')->count(),
                ];
            }),
            'notificaciones_criticas' => $notificaciones->where('prioridad', 'alta')->values(),
        ];

        return response()->json($reporte);
    }
}
