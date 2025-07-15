<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Articulo;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Config;
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
            
        foreach ($stockBajo as $articulo) {
            $notificaciones->push([
                'id' => 'stock_bajo_' . $articulo->id,
                'tipo' => 'stock_bajo',
                'prioridad' => 'media',
                'titulo' => 'Stock Bajo',
                'mensaje' => "El artículo '{$articulo->nombre}' tiene stock bajo ({$articulo->stock} unidades)",
                'fecha' => now(),
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
            
        foreach ($stockCritico as $articulo) {
            $notificaciones->push([
                'id' => 'stock_critico_' . $articulo->id,
                'tipo' => 'stock_critico',
                'prioridad' => 'alta',
                'titulo' => 'Stock Crítico',
                'mensaje' => "El artículo '{$articulo->nombre}' está sin stock",
                'fecha' => now(),
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

        // Notificaciones de Ventas Importantes
        $ventasImportantes = Venta::with(['cliente', 'detalleVentas'])
            ->whereDate('fecha', today())
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
                'fecha' => $venta->created_at,
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

        // Notificaciones de Clientes Nuevos
        $clientesNuevos = Cliente::whereDate('created_at', today())->get();
        
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
                'fecha' => now(),
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

        return $notificaciones->sortByDesc('fecha')->values();
    }

    public function marcarComoLeida($id)
    {
        // Aquí implementarías la lógica para marcar como leída
        // Por ahora simularemos que se marcó como leída
        return response()->json([
            'success' => true,
            'message' => 'Notificación marcada como leída'
        ]);
    }

    public function marcarTodasComoLeidas()
    {
        // Aquí implementarías la lógica para marcar todas como leídas
        return response()->json([
            'success' => true,
            'message' => 'Todas las notificaciones marcadas como leídas'
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
