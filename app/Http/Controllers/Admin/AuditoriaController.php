<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\StockValidation;
use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Exports\StockTiempoRealExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class AuditoriaController extends Controller
{
    use StockValidation;

    public function index()
    {
        // Obtener estadísticas generales
        $estadisticas = $this->obtenerEstadisticasGenerales();
        
        // Obtener últimos reportes de auditoría
        $ultimosReportes = $this->obtenerUltimosReportes();
        
        // Obtener alertas de stock
        $alertasStock = $this->obtenerAlertasStock();
        
        return view('admin.auditoria.index', compact('estadisticas', 'ultimosReportes', 'alertasStock'));
    }

    public function reporteStockTiempoReal(Request $request)
    {
        $reporteStock = $this->generarReporteStockTiempoReal($request);
        $categorias = Categoria::orderBy('nombre')->get();
        
        return view('admin.auditoria.stock-tiempo-real', compact('reporteStock', 'categorias'));
    }

    public function ejecutarAuditoria(Request $request)
    {
        $dias = $request->input('dias', 30);
        $aplicarCorrecciones = $request->boolean('aplicar_correcciones');
        $articuloEspecifico = $request->input('articulo_id');

        try {
            // Logging detallado del inicio de auditoría
            Log::info("Iniciando auditoría de inventario", [
                'usuario' => auth()->user()->name ?? 'Sistema',
                'parametros' => [
                    'dias' => $dias,
                    'aplicar_correcciones' => $aplicarCorrecciones,
                    'articulo_especifico' => $articuloEspecifico
                ]
            ]);

            // Generar fecha actual para el reporte
            $fechaReporte = Carbon::now()->format('Y-m-d_H-i-s');
            
            // Ejecutar auditoría manual (sin comando Artisan para mayor control)
            $inconsistencias = $this->ejecutarAuditoriaManual($dias, $aplicarCorrecciones, $articuloEspecifico);
            
            // Logging de resultados
            Log::info("Auditoría completada", [
                'inconsistencias_encontradas' => count($inconsistencias),
                'correcciones_aplicadas' => $aplicarCorrecciones,
                'tiempo_ejecucion' => Carbon::now()->format('H:i:s')
            ]);

            // Crear contenido del reporte
            $contenidoReporte = [
                'fecha_auditoria' => Carbon::now()->format('Y-m-d H:i:s'),
                'parametros' => [
                    'dias' => $dias,
                    'aplicar_correcciones' => $aplicarCorrecciones,
                    'correcciones_aplicadas' => $aplicarCorrecciones,
                    'articulo_especifico' => $articuloEspecifico
                ],
                'estadisticas' => [
                    'ventas_auditadas' => Venta::where('created_at', '>=', Carbon::now()->subDays($dias))->count(),
                    'detalles_auditados' => DetalleVenta::whereHas('venta', function($q) use ($dias) {
                        $q->where('created_at', '>=', Carbon::now()->subDays($dias));
                    })->count(),
                    'total_inconsistencias' => count($inconsistencias)
                ],
                'inconsistencias' => $inconsistencias
            ];
            
            // Guardar reporte en archivo JSON
            $rutaAuditorias = storage_path('app/auditorias');
            if (!is_dir($rutaAuditorias)) {
                mkdir($rutaAuditorias, 0755, true);
            }
            
            $archivoReporte = $rutaAuditorias . "/auditoria_ventas_{$fechaReporte}.json";
            file_put_contents($archivoReporte, json_encode($contenidoReporte, JSON_PRETTY_PRINT));
            
            // Preparar output para mostrar
            $output = "=== AUDITORÍA DE VENTAS COMPLETADA ===\n";
            $output .= "Fecha: " . Carbon::now()->format('d/m/Y H:i:s') . "\n";
            $output .= "Período analizado: Últimos {$dias} días\n";
            $output .= "Ventas auditadas: " . $contenidoReporte['estadisticas']['ventas_auditadas'] . "\n";
            $output .= "Detalles auditados: " . $contenidoReporte['estadisticas']['detalles_auditados'] . "\n";
            $output .= "Inconsistencias encontradas: " . count($inconsistencias) . "\n\n";
            
            if (count($inconsistencias) > 0) {
                $output .= "INCONSISTENCIAS DETECTADAS:\n";
                foreach ($inconsistencias as $inconsistencia) {
                    $output .= "- Artículo: {$inconsistencia['articulo']['nombre']} (ID: {$inconsistencia['articulo']['id']})\n";
                    $output .= "  Stock actual: {$inconsistencia['stock_actual']}, Teórico: {$inconsistencia['stock_teorico']}\n";
                    $output .= "  Diferencia: " . ($inconsistencia['stock_actual'] - $inconsistencia['stock_teorico']) . "\n\n";
                }
            } else {
                $output .= "¡Sistema consistente! No se encontraron inconsistencias de stock.\n";
            }

            return response()->json([
                'success' => true,
                'message' => 'Auditoría ejecutada exitosamente',
                'output' => $output,
                'inconsistencias' => count($inconsistencias),
                'reporte_url' => url("admin/auditoria/reporte/{$fechaReporte}")
            ]);

        } catch (\Exception $e) {
            Log::error('Error en auditoría: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verReporte($fecha)
    {
        $rutaReporte = storage_path("app/auditorias/auditoria_ventas_{$fecha}.json");
        
        if (!file_exists($rutaReporte)) {
            abort(404, 'Reporte no encontrado');
        }

        $contenido = json_decode(file_get_contents($rutaReporte), true);
        
        return view('admin.auditoria.ver-reporte', compact('contenido', 'fecha'));
    }

    public function alertasStock()
    {
        $alertas = $this->obtenerAlertasStockDetalladas();
        
        return view('admin.auditoria.alertas-stock', compact('alertas'));
    }

    public function inconsistenciasVentas(Request $request)
    {
        $dias = $request->input('dias', 7);
        $fechaInicio = Carbon::now()->subDays($dias);
        
        $inconsistencias = $this->detectarInconsistenciasVentas($fechaInicio);
        
        return view('admin.auditoria.inconsistencias-ventas', compact('inconsistencias', 'dias'));
    }

    private function obtenerEstadisticasGenerales()
    {
        // Cache de estadísticas por 5 minutos para mejorar rendimiento
        return Cache::remember('auditoria_estadisticas_generales', 300, function() {
            $ventasUltimos30Dias = Venta::where('fecha', '>=', Carbon::now()->subDays(30))
                                       ->where('estado', true)
                                       ->count();

            $articulosStockNegativo = Articulo::where('stock', '<', 0)->count();
            $articulosStockBajo = Articulo::where('stock', '>', 0)
                                         ->where('stock', '<=', 10)
                                         ->count();

            $ventasHoy = Venta::whereDate('fecha', Carbon::today())
                              ->where('estado', true)
                              ->count();

            $detallesVentaHoy = DetalleVenta::whereHas('venta', function($query) {
                                             $query->whereDate('fecha', Carbon::today())
                                                   ->where('estado', true);
                                         })->count();

            return [
                'ventas_ultimos_30_dias' => $ventasUltimos30Dias,
                'articulos_stock_negativo' => $articulosStockNegativo,
                'articulos_stock_bajo' => $articulosStockBajo,
                'ventas_hoy' => $ventasHoy,
                'detalles_venta_hoy' => $detallesVentaHoy,
                'cache_generado' => Carbon::now()->format('H:i:s')
            ];
        });
    }

    private function obtenerUltimosReportes()
    {
        $rutaAuditorias = storage_path('app/auditorias');
        
        if (!is_dir($rutaAuditorias)) {
            return [];
        }

        $archivos = glob($rutaAuditorias . '/auditoria_ventas_*.json');
        $reportes = [];

        foreach ($archivos as $archivo) {
            $nombreArchivo = basename($archivo);
            preg_match('/auditoria_ventas_(.+)\.json/', $nombreArchivo, $matches);
            
            if (isset($matches[1])) {
                $fecha = $matches[1];
                $fechaFormateada = Carbon::createFromFormat('Y-m-d_H-i-s', $fecha);
                
                $contenido = json_decode(file_get_contents($archivo), true);
                
                $reportes[] = [
                    'fecha' => $fecha,
                    'fecha_formateada' => $fechaFormateada->format('d/m/Y H:i:s'),
                    'total_inconsistencias' => count($contenido['inconsistencias'] ?? []),
                    'archivo' => $nombreArchivo
                ];
            }
        }

        // Ordenar por fecha descendente
        usort($reportes, function($a, $b) {
            return strcmp($b['fecha'], $a['fecha']);
        });

        return array_slice($reportes, 0, 10); // Solo los últimos 10
    }

    private function obtenerAlertasStock()
    {
        return Articulo::with('categoria')
            ->where('estado', true)
            ->where(function($query) {
                $query->where('stock', '<', 0)
                      ->orWhere('stock', '<=', 10);
            })
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get()
            ->map(function($articulo) {
                return [
                    'articulo' => $articulo,
                    'tipo_alerta' => $articulo->stock < 0 ? 'CRITICA' : 'ADVERTENCIA',
                    'mensaje' => $articulo->stock < 0 
                        ? "Stock negativo: {$articulo->stock}" 
                        : "Stock bajo: {$articulo->stock}"
                ];
            });
    }

    private function obtenerAlertasStockDetalladas()
    {
        $articulosProblematicos = Articulo::with('categoria')
            ->where('estado', true)
            ->where(function($query) {
                $query->where('stock', '<', 0)
                      ->orWhere('stock', '<=', 10);
            })
            ->orderBy('stock', 'asc')
            ->get();

        $alertas = [];

        foreach ($articulosProblematicos as $articulo) {
            $consistencia = $this->verificarConsistenciaStock($articulo->id);
            
            $alerta = [
                'articulo' => $articulo,
                'stock_actual' => $articulo->stock,
                'stock_teorico' => $consistencia['stock_teorico'],
                'diferencia' => $consistencia['diferencia'],
                'consistente' => $consistencia['consistente'],
                'tipo_alerta' => $articulo->stock < 0 ? 'CRITICA' : 'ADVERTENCIA',
                'ventas_recientes' => $this->obtenerVentasRecientesArticulo($articulo->id)
            ];

            $alertas[] = $alerta;
        }

        return $alertas;
    }

    private function obtenerVentasRecientesArticulo($articuloId)
    {
        return DetalleVenta::with(['venta.cliente'])
            ->where('articulo_id', $articuloId)
            ->whereHas('venta', function($query) {
                $query->where('fecha', '>=', Carbon::now()->subDays(7))
                      ->where('estado', true);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function detectarInconsistenciasVentas($fechaInicio)
    {
        $inconsistencias = [];

        // 1. Ventas con detalles sospechosos
        $detallesSospechosos = DetalleVenta::whereHas('venta', function($query) use ($fechaInicio) {
                $query->where('fecha', '>=', $fechaInicio)
                      ->where('estado', true);
            })
            ->where(function($query) {
                $query->where('cantidad', '<=', 0)
                      ->orWhere('cantidad', '>', 1000)
                      ->orWhereNull('articulo_id');
            })
            ->with(['venta.cliente', 'articulo'])
            ->get();

        foreach ($detallesSospechosos as $detalle) {
            $inconsistencias[] = [
                'tipo' => 'DETALLE_SOSPECHOSO',
                'descripcion' => 'Detalle de venta con datos anómalos',
                'detalle' => $detalle,
                'problema' => $detalle->cantidad <= 0 ? 'Cantidad negativa o cero' : 
                             ($detalle->cantidad > 1000 ? 'Cantidad muy alta' : 'Artículo no válido')
            ];
        }

        // 2. Ventas duplicadas potenciales
        $ventasDuplicadas = $this->detectarVentasDuplicadasPotenciales($fechaInicio);
        
        foreach ($ventasDuplicadas as $duplicada) {
            $inconsistencias[] = [
                'tipo' => 'VENTA_DUPLICADA',
                'descripcion' => 'Posible venta duplicada',
                'venta1' => Venta::find($duplicada->venta1_id),
                'venta2' => Venta::find($duplicada->venta2_id),
                'coincidencias' => $duplicada->coincidencias
            ];
        }

        return $inconsistencias;
    }

    private function detectarVentasDuplicadasPotenciales($fechaInicio)
    {
        return DB::select("
            SELECT v1.id as venta1_id, v2.id as venta2_id, 
                   v1.cliente_id, v1.fecha, 
                   COUNT(*) as coincidencias
            FROM ventas v1
            JOIN ventas v2 ON v1.cliente_id = v2.cliente_id 
                           AND v1.fecha = v2.fecha 
                           AND v1.id < v2.id
                           AND v1.estado = 1 AND v2.estado = 1
            WHERE v1.fecha >= ?
            GROUP BY v1.id, v2.id, v1.cliente_id, v1.fecha
            HAVING coincidencias > 0
            LIMIT 20
        ", [$fechaInicio]);
    }

    public function generarReporteStockTiempoReal($request = null)
    {
        $query = Articulo::with('categoria')
            ->select('id', 'codigo', 'nombre', 'stock', 'categoria_id', 'tipo')
            ->where('estado', true);

        // Aplicar filtros si se proporcionan
        if ($request) {
            if ($request->filled('categoria')) {
                $query->where('categoria_id', $request->categoria);
            }
            
            if ($request->filled('estado_stock')) {
                switch ($request->estado_stock) {
                    case 'negativo':
                        $query->where('stock', '<', 0);
                        break;
                    case 'bajo':
                        $query->where('stock', '>=', 0)->where('stock', '<=', 10);
                        break;
                    case 'normal':
                        $query->where('stock', '>', 10);
                        break;
                }
            }

            // Nuevo filtro: por fecha de última venta
            if ($request->filled('sin_ventas_dias')) {
                $diasSinVentas = (int)$request->sin_ventas_dias;
                $fechaLimite = Carbon::now()->subDays($diasSinVentas);
                
                $query->whereDoesntHave('detallesVenta', function($subQuery) use ($fechaLimite) {
                    $subQuery->whereHas('venta', function($ventaQuery) use ($fechaLimite) {
                        $ventaQuery->where('fecha', '>=', $fechaLimite)
                                   ->where('estado', true);
                    });
                });
            }
            
            if ($request->filled('consistencia')) {
                // Este filtro se aplicará después del cálculo
            }
            
            if ($request->filled('busqueda')) {
                $busqueda = $request->busqueda;
                $query->where(function($q) use ($busqueda) {
                    $q->where('codigo', 'LIKE', "%{$busqueda}%")
                      ->orWhere('nombre', 'LIKE', "%{$busqueda}%");
                });
            }
        }

        $articulos = $query->orderBy('stock', 'asc')->get();

        $reporteStock = [
            'estadisticas' => [
                'stock_negativo' => $articulos->where('stock', '<', 0)->count(),
                'stock_bajo' => $articulos->where('stock', '>', 0)->where('stock', '<=', 10)->count(),
                'stock_normal' => $articulos->where('stock', '>', 10)->count(),
                'total_articulos' => $articulos->count()
            ],
            'articulos' => []
        ];

        // Optimización: Pre-cargar últimas ventas para evitar N+1 queries
        $articuloIds = $articulos->pluck('id');
        $ultimasVentas = $this->obtenerUltimasVentasOptimizado($articuloIds);

        foreach ($articulos as $articulo) {
            $consistencia = $this->verificarConsistenciaStock($articulo->id);
            $ultimaVenta = $ultimasVentas[$articulo->id] ?? null;

            $item = [
                'articulo' => $articulo,
                'stock_actual' => $articulo->stock,
                'stock_teorico' => $consistencia['stock_teorico'],
                'diferencia' => $consistencia['diferencia'],
                'consistente' => $consistencia['consistente'],
                'ultima_venta' => $ultimaVenta
            ];

            // Aplicar filtro de consistencia si se especifica
            if ($request && $request->filled('consistencia')) {
                if ($request->consistencia === 'consistente' && !$item['consistente']) {
                    continue;
                }
                if ($request->consistencia === 'inconsistente' && $item['consistente']) {
                    continue;
                }
            }

            $reporteStock['articulos'][] = $item;
        }

        return $reporteStock;
    }

    /**
     * Optimización: Obtener últimas ventas de múltiples artículos en una sola consulta optimizada
     */
    private function obtenerUltimasVentasOptimizado($articuloIds)
    {
        if (empty($articuloIds) || !is_array($articuloIds)) {
            return [];
        }

        // Asegurar que todos los IDs sean enteros
        $articuloIds = array_map('intval', $articuloIds);
        $idsString = implode(',', $articuloIds);

        // Consulta optimizada usando window function para obtener la última venta de cada artículo
        $ultimasVentas = DB::select("
            SELECT dv.*
            FROM (
                SELECT dv.*,
                       ROW_NUMBER() OVER (PARTITION BY dv.articulo_id ORDER BY dv.created_at DESC) as rn
                FROM detalle_ventas dv
                INNER JOIN ventas v ON dv.venta_id = v.id
                WHERE dv.articulo_id IN ({$idsString})
                  AND v.estado = 1
            ) dv
            WHERE dv.rn = 1
        ");

        // Convertir a array indexado por articulo_id
        $resultado = [];
        foreach ($ultimasVentas as $venta) {
            $resultado[$venta->articulo_id] = (array) $venta;
        }

        return $resultado;
    }

    private function obtenerUltimaVentaArticulo($articuloId)
    {
        $ultimaVenta = DetalleVenta::where('articulo_id', $articuloId)
            ->whereHas('venta', function($query) {
                $query->where('estado', true);
            })
            ->orderBy('created_at', 'desc')
            ->first();

        return $ultimaVenta ? $ultimaVenta->venta->fecha : null;
    }

    public function corregirStock($articuloId)
    {
        try {
            $articulo = Articulo::findOrFail($articuloId);
            $consistencia = $this->verificarConsistenciaStock($articuloId);

            if ($consistencia['consistente']) {
                return response()->json([
                    'success' => false,
                    'message' => 'El stock ya es consistente'
                ]);
            }

            $stockAnterior = $articulo->stock;
            $articulo->stock = $consistencia['stock_teorico'];
            $articulo->save();            // Registrar la corrección en logs
            Log::info("Stock corregido", [
                'articulo_id' => $articuloId,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $consistencia['stock_teorico'],
                'diferencia' => $consistencia['diferencia'],
                'usuario' => auth()->user()->id ?? 'sistema'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Stock corregido de {$stockAnterior} a {$consistencia['stock_teorico']}"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al corregir stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ajusteManual(Request $request)
    {
        $request->validate([
            'articulo_id' => 'required|exists:articulos,id',
            'nuevo_stock' => 'required|integer|min:0',
            'motivo' => 'required|string|max:500'
        ]);        try {
            $articulo = Articulo::findOrFail($request->articulo_id);
            $stockAnterior = $articulo->stock;
            
            // Actualizar el stock actual
            $articulo->stock = $request->nuevo_stock;
            
            // Si no tiene stock_inicial, establecerlo basado en el cálculo teórico actual
            if (!$articulo->stock_inicial || $articulo->stock_inicial == 0) {
                $consistencia = $this->verificarConsistenciaStock($request->articulo_id);
                $totalIngresos = \App\Models\DetalleIngreso::where('articulo_id', $request->articulo_id)->sum('cantidad');
                $totalVentas = \App\Models\DetalleVenta::whereHas('venta', function($query) {
                        $query->where('estado', true);
                    })
                    ->where('articulo_id', $request->articulo_id)
                    ->sum('cantidad');
                
                // Establecer stock_inicial para que el nuevo stock sea consistente
                $articulo->stock_inicial = $request->nuevo_stock + $totalVentas - $totalIngresos;
            }
            
            $articulo->save();

            // Registrar el ajuste
            Log::info("Ajuste manual de stock", [
                'articulo_id' => $request->articulo_id,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $request->nuevo_stock,
                'stock_inicial_ajustado' => $articulo->stock_inicial,
                'motivo' => $request->motivo,
                'usuario' => auth()->user()->id ?? 'sistema'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ajuste aplicado exitosamente. Stock sincronizado con el sistema de auditoría.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al aplicar ajuste: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reabastecer(Request $request, $articuloId)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1'
        ]);

        try {
            $articulo = Articulo::findOrFail($articuloId);
            $stockAnterior = $articulo->stock;
            
            $articulo->stock += $request->cantidad;
            $articulo->save();            // Registrar el reabastecimiento
            Log::info("Reabastecimiento de stock", [
                'articulo_id' => $articuloId,
                'stock_anterior' => $stockAnterior,
                'cantidad_agregada' => $request->cantidad,
                'stock_nuevo' => $articulo->stock,
                'usuario' => auth()->user()->id ?? 'sistema'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Reabastecimiento registrado: +{$request->cantidad} unidades"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reabastecer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function articuloDetalle($articuloId)
    {
        try {
            $articulo = Articulo::findOrFail($articuloId);
            $consistencia = $this->verificarConsistenciaStock($articuloId);
            $ventasRecientes = $this->obtenerVentasRecientesArticulo($articuloId);

            $html = view('admin.auditoria.partials.articulo-detalle', compact(
                'articulo', 'consistencia', 'ventasRecientes'
            ))->render();

            return response()->json(['html' => $html]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar detalle: ' . $e->getMessage()
            ], 500);
        }
    }

    public function historialMovimientos($articuloId)
    {
        try {
            $movimientos = DetalleVenta::with(['venta.cliente'])
                ->where('articulo_id', $articuloId)
                ->whereHas('venta', function($query) {
                    $query->where('estado', true);
                })
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            $html = view('admin.auditoria.partials.historial-movimientos', compact('movimientos'))->render();

            return response()->json(['html' => $html]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar historial: ' . $e->getMessage()
            ], 500);
        }
    }

    public function compararVentas($venta1Id, $venta2Id)
    {
        try {
            $venta1 = Venta::with(['cliente', 'detalleVenta.articulo'])->findOrFail($venta1Id);
            $venta2 = Venta::with(['cliente', 'detalleVenta.articulo'])->findOrFail($venta2Id);

            $html = view('admin.auditoria.partials.comparacion-ventas', compact('venta1', 'venta2'))->render();

            return response()->json(['html' => $html]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al comparar ventas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function enviarNotificaciones()
    {
        try {            $alertasCriticas = collect($this->obtenerAlertasStockDetalladas())
                ->filter(function($alerta) {
                    return $alerta['tipo_alerta'] === 'CRITICA';
                });

            // Aquí se implementaría el envío de notificaciones
            // (email, SMS, Slack, etc.)
            
            foreach ($alertasCriticas as $alerta) {
                // Enviar notificación por el canal preferido
                // Mail::to($adminEmail)->send(new AlertaStockCritica($alerta));
            }

            Log::info("Notificaciones de stock crítico enviadas", [
                'cantidad' => $alertasCriticas->count(),
                'usuario' => auth()->user()->id ?? 'sistema'
            ]);

            return response()->json([
                'success' => true,
                'message' => "Notificaciones enviadas: {$alertasCriticas->count()} alertas críticas"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar notificaciones: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportarStock($formato, Request $request)
    {
        try {
            $reporteStock = $this->generarReporteStockTiempoReal($request);
            
            if ($formato === 'excel') {
                return $this->exportarStockExcel($reporteStock);
            } elseif ($formato === 'pdf') {
                return $this->exportarStockPDF($reporteStock);
            }

            return response()->json([
                'success' => false,
                'message' => 'Formato no soportado'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar: ' . $e->getMessage()
            ], 500);
        }
    }

    private function exportarStockExcel($reporteStock)
    {
        $filename = 'reporte_stock_tiempo_real_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new StockTiempoRealExport($reporteStock), $filename);
    }

    private function exportarStockPDF($reporteStock)
    {
        $filename = 'reporte_stock_tiempo_real_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        $pdf = Pdf::loadView('admin.auditoria.exports.stock-pdf', compact('reporteStock'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true
            ]);
        
        return $pdf->download($filename);
    }    public function exportarReporte($fecha)
    {
        try {
            $rutaReporte = storage_path("app/auditorias/auditoria_ventas_{$fecha}.json");
            
            if (!file_exists($rutaReporte)) {
                abort(404, 'Reporte no encontrado');
            }

            $contenido = json_decode(file_get_contents($rutaReporte), true);
            
            // Generar HTML del reporte
            $html = $this->generarReporteHTML($contenido, $fecha);
            
            $filename = "reporte_auditoria_ventas_{$fecha}.html";
            
            return response($html)
                ->header('Content-Type', 'text/html; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al exportar reporte: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generarReporteHTML($contenido, $fecha)
    {
        $fechaFormateada = \Carbon\Carbon::parse($contenido['fecha_auditoria'])->format('d/m/Y H:i:s');
        $inconsistencias = $contenido['inconsistencias'] ?? [];
        $estadisticas = $contenido['estadisticas'] ?? [];
        $parametros = $contenido['parametros'] ?? [];
        
        $tiposInconsistencias = collect($inconsistencias)->groupBy('tipo');
        
        $html = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Auditoría de Ventas - ' . $fecha . '</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f8f9fa;
        }
        .header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5rem;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .stat-label {
            color: #666;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }
        .success { color: #28a745; }
        .warning { color: #ffc107; }
        .danger { color: #dc3545; }
        .info { color: #17a2b8; }
        .primary { color: #007bff; }
        
        .section {
            background: white;
            margin-bottom: 30px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .section-header {
            padding: 20px;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .section-header.success { background: #d4edda; color: #155724; }
        .section-header.warning { background: #fff3cd; color: #856404; }
        .section-header.danger { background: #f8d7da; color: #721c24; }
        .section-header.info { background: #d1ecf1; color: #0c5460; }
        
        .section-content {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: bold;
        }
        .badge.success { background: #d4edda; color: #155724; }
        .badge.warning { background: #fff3cd; color: #856404; }
        .badge.danger { background: #f8d7da; color: #721c24; }
        .badge.info { background: #d1ecf1; color: #0c5460; }
        .badge.primary { background: #cce5ff; color: #004085; }
        .badge.secondary { background: #e2e3e5; color: #383d41; }
        
        .parameters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .parameter {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        .parameter-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 5px;
        }
        .parameter-value {
            font-weight: bold;
            color: #333;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-top: 30px;
            color: #666;
        }
        
        @media print {
            body { background: white; }
            .header { background: #007bff !important; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔍 Reporte de Auditoría de Ventas</h1>
        <p>Generado el ' . $fechaFormateada . '</p>
        <p>Sistema Jireh Automotriz</p>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number primary">' . ($estadisticas['ventas_auditadas'] ?? 0) . '</div>
            <div class="stat-label">Ventas Auditadas</div>
        </div>
        <div class="stat-card">
            <div class="stat-number info">' . ($estadisticas['detalles_auditados'] ?? 0) . '</div>
            <div class="stat-label">Detalles Auditados</div>
        </div>
        <div class="stat-card">
            <div class="stat-number ' . (count($inconsistencias) > 0 ? 'warning' : 'success') . '">' . count($inconsistencias) . '</div>
            <div class="stat-label">Inconsistencias</div>
        </div>
        <div class="stat-card">
            <div class="stat-number danger">' . ($estadisticas['articulos_con_problemas'] ?? 0) . '</div>
            <div class="stat-label">Artículos Afectados</div>
        </div>
    </div>

    <div class="section">
        <div class="section-header info">
            📋 Parámetros de Auditoría
        </div>
        <div class="section-content">
            <div class="parameters">
                <div class="parameter">
                    <div class="parameter-label">Período Auditado</div>
                    <div class="parameter-value">' . ($parametros['dias_auditados'] ?? 'N/A') . ' días</div>
                </div>
                <div class="parameter">
                    <div class="parameter-label">Artículo Específico</div>
                    <div class="parameter-value">' . (isset($parametros['articulo_especifico']) && $parametros['articulo_especifico'] ? 'ID: ' . $parametros['articulo_especifico'] : 'Todos los artículos') . '</div>
                </div>
                <div class="parameter">
                    <div class="parameter-label">Modo de Corrección</div>
                    <div class="parameter-value">' . ($parametros['correcciones_aplicadas'] ? 'Correcciones Habilitadas' : 'Solo Detección') . '</div>
                </div>
            </div>
        </div>
    </div>';

        if (count($inconsistencias) == 0) {
            $html .= '
    <div class="section">
        <div class="section-header success">
            ✅ ¡Auditoría Exitosa!
        </div>
        <div class="section-content" style="text-align: center; padding: 40px;">
            <h2 style="color: #28a745; margin-bottom: 20px;">No se encontraron inconsistencias</h2>
            <p style="font-size: 1.1rem; color: #666;">El sistema de ventas e inventario está funcionando correctamente.</p>
            <p style="color: #28a745; font-weight: bold;">✓ Inventario sincronizado</p>
            <p style="color: #28a745; font-weight: bold;">✓ Sin duplicaciones</p>
            <p style="color: #28a745; font-weight: bold;">✓ Stock positivo</p>
        </div>
    </div>';
        } else {
            foreach ($tiposInconsistencias as $tipo => $items) {
                $tipoLabel = '';
                $headerClass = 'warning';
                $icon = '⚠️';
                
                switch ($tipo) {
                    case 'STOCK_INCONSISTENTE':
                        $tipoLabel = 'Inconsistencias de Stock';
                        $headerClass = 'warning';
                        $icon = '📊';
                        break;
                    case 'STOCK_NEGATIVO':
                        $tipoLabel = 'Stock Negativo';
                        $headerClass = 'danger';
                        $icon = '❌';
                        break;
                    case 'VENTA_DUPLICADA':
                    case 'VENTAS_DUPLICADAS':
                        $tipoLabel = 'Ventas Duplicadas';
                        $headerClass = 'info';
                        $icon = '📄';
                        break;
                    default:
                        $tipoLabel = ucwords(str_replace('_', ' ', $tipo));
                        break;
                }
                
                $html .= '
    <div class="section">
        <div class="section-header ' . $headerClass . '">
            ' . $icon . ' ' . $tipoLabel . ' (' . count($items) . ' ' . (count($items) == 1 ? 'caso' : 'casos') . ')
        </div>
        <div class="section-content">
            <table>';
                
                if ($tipo == 'STOCK_INCONSISTENTE') {
                    $html .= '
                <thead>
                    <tr>
                        <th>Artículo</th>
                        <th>Código</th>
                        <th>Stock Actual</th>
                        <th>Stock Teórico</th>
                        <th>Diferencia</th>
                        <th>Severidad</th>
                    </tr>
                </thead>
                <tbody>';
                    
                    foreach ($items as $item) {
                        $diferencia = $item['diferencia'] ?? 0;
                        $badgeClass = $diferencia < 0 ? 'danger' : ($diferencia > 0 ? 'success' : 'warning');
                        $severidad = $item['severidad'] ?? 'BAJA';
                        $severidadClass = $severidad == 'ALTA' ? 'danger' : ($severidad == 'MEDIA' ? 'warning' : 'info');
                        
                        $html .= '
                    <tr>
                        <td><strong>' . ($item['articulo_nombre'] ?? 'N/A') . '</strong><br><small>ID: ' . ($item['articulo_id'] ?? 'N/A') . '</small></td>
                        <td><code>' . ($item['articulo_codigo'] ?? 'N/A') . '</code></td>
                        <td><span class="badge primary">' . ($item['stock_actual'] ?? 'N/A') . '</span></td>
                        <td><span class="badge secondary">' . ($item['stock_teorico'] ?? 'N/A') . '</span></td>
                        <td><span class="badge ' . $badgeClass . '">' . ($diferencia > 0 ? '+' : '') . $diferencia . '</span></td>
                        <td><span class="badge ' . $severidadClass . '">' . $severidad . '</span></td>
                    </tr>';
                    }
                } elseif (in_array($tipo, ['VENTA_DUPLICADA', 'VENTAS_DUPLICADAS'])) {
                    $html .= '
                <thead>
                    <tr>
                        <th>Ventas Involucradas</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Coincidencias</th>
                        <th>Severidad</th>
                    </tr>
                </thead>
                <tbody>';
                    
                    foreach ($items as $item) {
                        $html .= '
                    <tr>
                        <td>
                            <span class="badge primary">Venta #' . ($item['venta1_id'] ?? 'N/A') . '</span> 
                            ↔ 
                            <span class="badge secondary">Venta #' . ($item['venta2_id'] ?? 'N/A') . '</span>
                        </td>
                        <td><span class="badge info">Cliente #' . ($item['cliente_id'] ?? 'N/A') . '</span></td>
                        <td>' . \Carbon\Carbon::parse($item['fecha'] ?? now())->format('d/m/Y') . '</td>
                        <td><span class="badge warning">' . ($item['detalles_similares'] ?? 0) . ' coincidencias</span></td>
                        <td><span class="badge warning">' . ($item['severidad'] ?? 'MEDIA') . '</span></td>
                    </tr>';
                    }
                }
                
                $html .= '
                </tbody>
            </table>
        </div>
    </div>';
            }
        }
        
        $html .= '
    <div class="footer">
        <p><strong>Reporte generado automáticamente por el Sistema de Auditoría Jireh</strong></p>
        <p>Fecha: ' . $fechaFormateada . ' | Versión: 1.0 | © Szystems</p>
    </div>
</body>
</html>';
        
        return $html;
    }

    public function corregirDetalle(Request $request)
    {
        $request->validate([
            'detalle_id' => 'required|exists:detalle_venta,id',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $detalle = DetalleVenta::findOrFail($request->detalle_id);
            $cantidadAnterior = $detalle->cantidad;
            
            // Actualizar cantidad
            $detalle->cantidad = $request->cantidad;
            $detalle->save();

            // Recalcular total de la venta
            $venta = $detalle->venta;
            $venta->total = $venta->detalleVenta()->sum(DB::raw('cantidad * precio'));
            $venta->save();

            // Registrar la corrección
            Log::info("Detalle de venta corregido", [
                'detalle_id' => $request->detalle_id,
                'venta_id' => $venta->id,
                'cantidad_anterior' => $cantidadAnterior,
                'cantidad_nueva' => $request->cantidad,
                'motivo' => $request->motivo,
                'usuario' => auth()->user()->id ?? 'sistema'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Detalle corregido exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al corregir detalle: ' . $e->getMessage()
            ], 500);
        }
    }

    public function eliminarDetalle($detalleId)
    {
        try {
            DB::beginTransaction();

            $detalle = DetalleVenta::findOrFail($detalleId);
            $venta = $detalle->venta;
            
            // Verificar que la venta tenga más de un detalle
            if ($venta->detalleVenta()->count() <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el único detalle de la venta'
                ], 400);
            }

            // Devolver stock al artículo
            if ($detalle->articulo) {
                $this->actualizarStockSeguro($detalle->articulo_id, $detalle->cantidad, $venta->id);
            }

            // Eliminar detalle
            $detalle->delete();

            // Recalcular total de la venta
            $venta->total = $venta->detalleVenta()->sum(DB::raw('cantidad * precio'));
            $venta->save();

            // Registrar la eliminación
            Log::info("Detalle de venta eliminado", [
                'detalle_id' => $detalleId,
                'venta_id' => $venta->id,
                'articulo_id' => $detalle->articulo_id,
                'cantidad_devuelta' => $detalle->cantidad,
                'usuario' => auth()->user()->id ?? 'sistema'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Detalle eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar detalle: ' . $e->getMessage()
            ], 500);
        }
    }

    public function fusionarVentas($venta1Id, $venta2Id)
    {
        try {
            DB::beginTransaction();

            $venta1 = Venta::findOrFail($venta1Id);
            $venta2 = Venta::findOrFail($venta2Id);

            // Verificar que las ventas sean del mismo cliente
            if ($venta1->cliente_id !== $venta2->cliente_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las ventas deben ser del mismo cliente para fusionarse'
                ], 400);
            }

            // Mover todos los detalles de venta2 a venta1
            foreach ($venta2->detalleVenta as $detalle) {
                $detalle->venta_id = $venta1->id;
                $detalle->save();
            }

            // Recalcular total de venta1
            $venta1->total = $venta1->detalleVenta()->sum(DB::raw('cantidad * precio'));
            $venta1->save();

            // Eliminar venta2
            $venta2->delete();

            // Registrar la fusión
            Log::info("Ventas fusionadas", [
                'venta_principal' => $venta1Id,
                'venta_fusionada' => $venta2Id,
                'cliente_id' => $venta1->cliente_id,
                'nuevo_total' => $venta1->total,
                'usuario' => auth()->user()->id ?? 'sistema'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ventas fusionadas exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al fusionar ventas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function eliminarVenta($ventaId)
    {
        try {
            DB::beginTransaction();

            $venta = Venta::findOrFail($ventaId);

            // Devolver stock de todos los artículos
            foreach ($venta->detalleVenta as $detalle) {
                if ($detalle->articulo) {
                    $this->actualizarStockSeguro($detalle->articulo_id, $detalle->cantidad, $venta->id);
                }
            }

            // Eliminar detalles de venta
            $venta->detalleVenta()->delete();

            // Eliminar venta
            $venta->delete();

            // Registrar la eliminación
            Log::info("Venta eliminada", [
                'venta_id' => $ventaId,
                'cliente_id' => $venta->cliente_id,
                'total' => $venta->total,
                'detalles_eliminados' => $venta->detalleVenta->count(),
                'usuario' => auth()->user()->id ?? 'sistema'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ejecuta auditoría manual de inconsistencias de stock
     */
    private function ejecutarAuditoriaManual($dias = 30, $aplicarCorrecciones = false, $articuloEspecifico = null)
    {
        $inconsistencias = [];
        
        // Obtener artículos a auditar (incluye artículos y servicios con stock)
        $query = Articulo::where('estado', 1)->whereIn('tipo', ['articulo', 'servicio']);
        
        if ($articuloEspecifico) {
            $query->where('id', $articuloEspecifico);
        }
        
        $articulos = $query->get();
        
        foreach ($articulos as $articulo) {
            // Calcular stock teórico basado en movimientos
            $stockTeorico = $this->calcularStockTeorico($articulo->id);
            
            // Comparar con stock actual
            if ($articulo->stock != $stockTeorico) {
                $inconsistencias[] = [
                    'articulo' => [
                        'id' => $articulo->id,
                        'codigo' => $articulo->codigo,
                        'nombre' => $articulo->nombre
                    ],
                    'stock_actual' => $articulo->stock,
                    'stock_teorico' => $stockTeorico,
                    'diferencia' => $articulo->stock - $stockTeorico,
                    'severidad' => abs($articulo->stock - $stockTeorico) > 10 ? 'ALTA' : 'MEDIA',
                    'fecha_deteccion' => Carbon::now()->format('Y-m-d H:i:s')
                ];
                
                // Aplicar corrección si se solicita
                if ($aplicarCorrecciones && !$articuloEspecifico) {
                    $this->corregirInconsistenciaStock($articulo->id, $stockTeorico);
                }
            }
        }
        
        return $inconsistencias;
    }
    
    /**
     * Calcula el stock teórico de un artículo basado en sus movimientos
     */
    /**
     * Limpiar cache de estadísticas cuando se realizan cambios importantes
     */
    private function limpiarCacheEstadisticas()
    {
        Cache::forget('auditoria_estadisticas_generales');
    }

    private function calcularStockTeorico($articuloId)
    {
        // Obtener todos los movimientos de stock del artículo
        $movimientos = DB::table('movimientos_stock')
            ->where('articulo_id', $articuloId)
            ->orderBy('created_at', 'asc')
            ->get();
        
        $stockTeorico = 0;
        
        foreach ($movimientos as $movimiento) {
            switch ($movimiento->tipo) {
                case 'CREACION':
                case 'AJUSTE_INICIAL':
                case 'AJUSTE_MANUAL':
                case 'CORRECCION_AUTOMATICA':
                case 'INGRESO':
                    $stockTeorico += $movimiento->cantidad;
                    break;
                case 'VENTA':
                    $stockTeorico -= $movimiento->cantidad;
                    break;
            }
        }
        
        return $stockTeorico;
    }
    
    /**
     * Corrige una inconsistencia de stock actualizando el stock real
     */
    private function corregirInconsistenciaStock($articuloId, $stockTeorico)
    {
        try {
            $articulo = Articulo::find($articuloId);
            if (!$articulo) return false;
            
            $stockActual = $articulo->stock;
            
            // Verificar si el artículo tiene movimientos registrados
            $tieneMovimientos = DB::table('movimientos_stock')
                ->where('articulo_id', $articuloId)
                ->exists();
            
            if (!$tieneMovimientos && $stockActual > 0) {
                // Caso especial: artículo sin movimientos pero con stock positivo
                // Crear un movimiento AJUSTE_INICIAL para establecer una línea base
                DB::table('movimientos_stock')->insert([
                    'articulo_id' => $articuloId,
                    'tipo' => 'AJUSTE_INICIAL',
                    'stock_anterior' => 0,
                    'stock_nuevo' => $stockActual,
                    'cantidad' => $stockActual,
                    'referencia_tipo' => 'AJUSTE_INICIAL',
                    'referencia_id' => $articuloId,
                    'observaciones' => 'Ajuste inicial automático para establecer línea base de stock',
                    'user_id' => auth()->id() ?? 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                
                Log::info("Creado movimiento AJUSTE_INICIAL para artículo {$articulo->codigo} con stock {$stockActual}");
                
            } else {
                // Caso normal: corregir stock basado en movimientos existentes
                $articulo->stock = $stockTeorico;
                $articulo->save();
                
                // Registrar el movimiento de corrección
                DB::table('movimientos_stock')->insert([
                    'articulo_id' => $articuloId,
                    'tipo' => 'CORRECCION_AUTOMATICA',
                    'stock_anterior' => $stockActual,
                    'stock_nuevo' => $stockTeorico,
                    'cantidad' => $stockTeorico - $stockActual,
                    'referencia_tipo' => 'CORRECCION_AUTOMATICA',
                    'referencia_id' => $articuloId,
                    'observaciones' => 'Corrección automática por inconsistencia detectada en auditoría',
                    'user_id' => auth()->id() ?? 1,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                
                // Limpiar cache ya que cambió el stock
                $this->limpiarCacheEstadisticas();
                
                Log::info("Corregido stock del artículo {$articulo->codigo} de {$stockActual} a {$stockTeorico}");
            }
            
            return true;
            
        } catch (\Exception $e) {
            Log::error("Error al corregir inconsistencia de stock para artículo {$articuloId}: " . $e->getMessage());
            return false;
        }
    }
}
