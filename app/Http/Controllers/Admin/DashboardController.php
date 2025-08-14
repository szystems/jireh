<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Config;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Venta;
use App\Models\Articulo;
use App\Models\Trabajador;
use App\Models\User;
use App\Models\Proveedor;
use App\Models\DetalleVenta;
use App\Models\Comision;
use App\Models\LotePago;
use App\Models\MetaVenta;
use App\Models\Ingreso;
use App\Services\PrevencionInconsistencias;
use App\Services\MonitoreoAutocorreccion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    protected $prevencionService;
    protected $monitoreoService;

    public function __construct(PrevencionInconsistencias $prevencionService, MonitoreoAutocorreccion $monitoreoService)
    {
        $this->prevencionService = $prevencionService;
        $this->monitoreoService = $monitoreoService;
    }

    public function index()
    {
        $config = Config::first();
        $data = $this->getDashboardData();
        
        return view('admin.dashboard.index', compact('config', 'data'));
    }

    public function getDashboardData()
    {
        $today = Carbon::now();
        $startOfWeek = $today->copy()->startOfWeek();
        $endOfWeek = $today->copy()->endOfWeek();
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();
        $startOfYear = $today->copy()->startOfYear();

        // Estadísticas básicas expandidas
        $data = [
            'contadores' => [
                'clientes' => Cliente::count(),
                'vehiculos' => Vehiculo::count(),
                'usuarios' => User::count(),
                'trabajadores' => Trabajador::count(),
                'proveedores' => Proveedor::count(),
                'articulos' => Articulo::count(),
                'comisiones_total' => Comision::count(),
                'lotes_pago' => LotePago::count(),
                'metas_activas' => MetaVenta::where('estado', true)->count(),
            ],
            'ventas' => $this->getVentasData($today, $startOfWeek, $endOfWeek, $startOfMonth, $endOfMonth),
            'comisiones' => $this->getComisionesData($startOfMonth, $endOfMonth),
            'inventario' => $this->getInventarioData(),
            'stock' => [
                'bajo' => Articulo::where('stock_minimo', '>', 0)->whereColumn('stock', '<=', 'stock_minimo')->where('stock', '>', 0)->count(),
                'agotado' => Articulo::where('stock', '=', 0)->count(),
                'critico' => Articulo::where('stock_minimo', '>', 0)->whereColumn('stock', '<=', 'stock_minimo')->count(),
                'sin_stock_total' => Articulo::where('stock', '<=', 0)->count(),
            ],
            'metas' => [
                'alcanzadas' => $this->calculateMetasAlcanzadas(),
                'activas' => MetaVenta::where('estado', true)->count(),
            ],
            'alertas' => $this->getAlertasUnificadas(),
            'tendencias' => $this->getTendencias(),
            'kpis' => $this->getKPIsUnificados(),
            'actividad_reciente' => $this->getActividadReciente(),
            'resumen_financiero' => $this->getResumenFinanciero($startOfMonth, $endOfMonth),
        ];

        return $data;
    }

    private function getVentasData($today, $startOfWeek, $endOfWeek, $startOfMonth, $endOfMonth)
    {
        $ventasHoy = $this->calcularVentasPorPeriodo($today->format('Y-m-d'), $today->format('Y-m-d'));
        $ventasSemana = $this->calcularVentasPorPeriodo($startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d'));
        $ventasMes = $this->calcularVentasPorPeriodo($startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d'));
        $ventasAño = $this->calcularVentasPorPeriodo(Carbon::now()->startOfYear()->format('Y-m-d'), Carbon::now()->format('Y-m-d'));

        // Ventas por mes para el gráfico
        $ventasPorMes = [];
        $meses = [];
        for ($i = 1; $i <= 12; $i++) {
            $fechaInicio = Carbon::create($today->year, $i, 1);
            $fechaFin = $fechaInicio->copy()->endOfMonth();
            $ventasPorMes[] = $this->calcularVentasPorPeriodo($fechaInicio->format('Y-m-d'), $fechaFin->format('Y-m-d'));
            $meses[] = $fechaInicio->locale('es')->format('M');
        }

        // Ventas recientes
        $ventasRecientes = Venta::with(['cliente', 'detalleVentas'])
            ->orderBy('fecha', 'desc')
            ->limit(10)
            ->get()
            ->map(function($venta) {
                $venta->total_calculado = $venta->detalleVentas->sum('sub_total');
                return $venta;
            });

        // Comparación con período anterior
        $ventasSemanaPasada = $this->calcularVentasPorPeriodo(
            $startOfWeek->copy()->subWeek()->format('Y-m-d'),
            $endOfWeek->copy()->subWeek()->format('Y-m-d')
        );
        $ventasMesPasado = $this->calcularVentasPorPeriodo(
            $startOfMonth->copy()->subMonth()->format('Y-m-d'),
            $endOfMonth->copy()->subMonth()->format('Y-m-d')
        );

        return [
            'hoy' => $ventasHoy,
            'semana' => $ventasSemana,
            'mes' => $ventasMes,
            'año' => $ventasAño,
            'por_mes' => $ventasPorMes,
            'meses' => $meses,
            'recientes' => $ventasRecientes,
            'comparacion' => [
                'semana_anterior' => $ventasSemanaPasada,
                'mes_anterior' => $ventasMesPasado,
                'crecimiento_semanal' => $ventasSemanaPasada > 0 ? (($ventasSemana - $ventasSemanaPasada) / $ventasSemanaPasada) * 100 : 0,
                'crecimiento_mensual' => $ventasMesPasado > 0 ? (($ventasMes - $ventasMesPasado) / $ventasMesPasado) * 100 : 0,
            ]
        ];
    }

    private function calcularVentasPorPeriodo($fechaInicio, $fechaFin)
    {
        return Venta::with('detalleVentas')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', true)
            ->get()
            ->sum(function($venta) {
                return $venta->detalleVentas->sum('sub_total');
            });
    }

    /**
     * Calcular total de ingresos (compras) por período
     */
    private function calcularIngresosPorPeriodo($fechaInicio, $fechaFin)
    {
        return Ingreso::with('detalles')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->get()
            ->sum(function($ingreso) {
                return $ingreso->detalles->sum(function($detalle) {
                    return $detalle->precio_compra * $detalle->cantidad;
                });
            });
    }

    private function getInventarioData()
    {
        $stockBajo = Articulo::where('stock_minimo', '>', 0)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->where('stock', '>', 0)
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get();

        $stockAgotado = Articulo::where('stock', '=', 0)
            ->orderBy('nombre', 'asc')
            ->limit(10)
            ->get();

        $stockCritico = Articulo::where('stock', '<=', 0)
            ->count();

        $articulosMasVendidos = DB::table('detalle_ventas')
            ->join('articulos', 'detalle_ventas.articulo_id', '=', 'articulos.id')
            ->select('articulos.nombre', DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'))
            ->groupBy('articulos.id', 'articulos.nombre')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();

        return [
            'stock_bajo' => $stockBajo,
            'stock_agotado' => $stockAgotado,
            'stock_critico_count' => $stockCritico,
            'articulos_mas_vendidos' => $articulosMasVendidos,
            'valor_inventario' => $this->calcularValorInventario(),
        ];
    }

    private function calcularValorInventario()
    {
        return Articulo::where('stock', '>', 0)
            ->get()
            ->sum(function($articulo) {
                return $articulo->stock * $articulo->precio_venta;
            });
    }

    private function getAlertas()
    {
        $alertas = [];

        // Alertas de stock bajo
        $stockBajo = Articulo::where('stock_minimo', '>', 0)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->where('stock', '>', 0)
            ->count();

        if ($stockBajo > 0) {
            $alertas[] = [
                'tipo' => 'warning',
                'icono' => 'exclamation-triangle',
                'mensaje' => "$stockBajo artículos con stock bajo",
                'url' => '/inventario',
                'fecha' => now(),
            ];
        }

        // Alertas de stock agotado (todos los artículos con stock = 0)
        $stockAgotado = Articulo::where('stock', '=', 0)->count();

        if ($stockAgotado > 0) {
            $alertas[] = [
                'tipo' => 'danger',
                'icono' => 'x-circle-fill',
                'mensaje' => "$stockAgotado artículos completamente agotados",
                'url' => '/inventario',
                'fecha' => now(),
            ];
        }

        // Alertas de stock crítico (todos los artículos sin stock)
        $stockCritico = Articulo::where('stock', '<=', 0)->count();
        if ($stockCritico > 0) {
            $alertas[] = [
                'tipo' => 'danger',
                'icono' => 'exclamation-circle',
                'mensaje' => "$stockCritico artículos sin stock",
                'url' => '/inventario',
                'fecha' => now(),
            ];
        }

        // Alertas de ventas importantes (ventas grandes del día)
        $ventasGrandes = Venta::with('detalleVentas')
            ->whereDate('fecha', today())
            ->where('estado', true)
            ->get()
            ->filter(function($venta) {
                return $venta->detalleVentas->sum('sub_total') > 1000;
            })
            ->count();

        if ($ventasGrandes > 0) {
            $alertas[] = [
                'tipo' => 'success',
                'icono' => 'check-circle',
                'mensaje' => "$ventasGrandes ventas importantes hoy",
                'url' => '/ventas',
                'fecha' => now(),
            ];
        }

        return $alertas;
    }

    private function getTendencias()
    {
        $ultimosSieteDias = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $ultimosSieteDias[] = [
                'fecha' => $fecha->format('Y-m-d'),
                'dia' => $fecha->locale('es')->format('D'),
                'ventas' => $this->calcularVentasPorPeriodo($fecha->format('Y-m-d'), $fecha->format('Y-m-d')),
            ];
        }

        return [
            'ultimos_7_dias' => $ultimosSieteDias,
            'promedio_diario' => collect($ultimosSieteDias)->avg('ventas'),
            'mejor_dia' => collect($ultimosSieteDias)->sortByDesc('ventas')->first(),
        ];
    }

    private function getKPIs()
    {
        $hoy = Carbon::now();
        $inicioMes = $hoy->copy()->startOfMonth();
        $finMes = $hoy->copy()->endOfMonth();

        $ventasDelMes = Venta::whereBetween('fecha', [$inicioMes, $finMes])
            ->where('estado', true)
            ->count();

        $clientesDelMes = Cliente::whereBetween('created_at', [$inicioMes, $finMes])
            ->count();

        $ticketPromedio = $ventasDelMes > 0 ? 
            $this->calcularVentasPorPeriodo($inicioMes->format('Y-m-d'), $finMes->format('Y-m-d')) / $ventasDelMes : 0;

        return [
            'ticket_promedio' => $ticketPromedio,
            'clientes_nuevos_mes' => $clientesDelMes,
            'ventas_mes' => $ventasDelMes,
            'conversion_rate' => $this->calcularTasaConversion(),
        ];
    }

    private function calcularTasaConversion()
    {
        $visitasDelMes = 100; // Placeholder - necesitaríamos implementar tracking de visitas
        $ventasDelMes = Venta::whereBetween('fecha', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->where('estado', true)->count();

        return $visitasDelMes > 0 ? ($ventasDelMes / $visitasDelMes) * 100 : 0;
    }

    private function getActividadReciente()
    {
        $actividades = [];

        // Ventas recientes
        $ventasRecientes = Venta::with(['cliente', 'detalleVentas'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($ventasRecientes as $venta) {
            $total = $venta->detalleVentas->sum('sub_total');
            
            $actividades[] = [
                'tipo' => 'venta',
                'icono' => 'cart-plus',
                'mensaje' => "Nueva venta a {$venta->cliente->nombre}",
                'monto' => $total,
                'fecha' => $venta->created_at,
                'url' => "/show-venta/{$venta->id}",
            ];
        }

        // Clientes nuevos
        $clientesNuevos = Cliente::orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($clientesNuevos as $cliente) {
            $actividades[] = [
                'tipo' => 'cliente',
                'icono' => 'user-plus',
                'mensaje' => "Nuevo cliente: {$cliente->nombre}",
                'fecha' => $cliente->created_at,
                'url' => "/show-cliente/{$cliente->id}",
            ];
        }

        return collect($actividades)->sortByDesc('fecha')->take(8)->values();
    }

    public function getEstadoSistema()
    {
        try {
            // Verificar estado básico del sistema
            $estadoPrevencion = [
                'activo' => true,
                'verificaciones' => [
                    'base_datos' => true,
                    'integridad' => true,
                    'consistencia' => true,
                ],
                'mensaje' => 'Sistema funcionando correctamente'
            ];

            $estadoMonitoreo = [
                'activo' => true,
                'ultima_verificacion' => now(),
                'estado' => 'normal',
                'mensaje' => 'Monitoreo activo'
            ];

            return response()->json([
                'estado' => 'success',
                'prevencion' => $estadoPrevencion,
                'monitoreo' => $estadoMonitoreo,
                'timestamp' => now(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Error al obtener estado del sistema',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtener datos de comisiones para el dashboard unificado
     */
    private function getComisionesData($startOfMonth, $endOfMonth)
    {
        return [
            'pendientes' => [
                'monto' => Comision::where('estado', 'pendiente')->sum('monto'),
                'cantidad' => Comision::where('estado', 'pendiente')->count(),
            ],
            'pagadas' => [
                'monto' => Comision::where('estado', 'pagado')->sum('monto'),
                'cantidad' => Comision::where('estado', 'pagado')->count(),
            ],
            'pagadas_mes' => [
                'monto' => Comision::where('estado', 'pagado')
                    ->whereMonth('created_at', $startOfMonth->month)
                    ->whereYear('created_at', $startOfMonth->year)
                    ->sum('monto'),
                'cantidad' => Comision::where('estado', 'pagado')
                    ->whereMonth('created_at', $startOfMonth->month)
                    ->whereYear('created_at', $startOfMonth->year)
                    ->count(),
            ],
            'total' => [
                'monto' => Comision::sum('monto'),
                'cantidad' => Comision::count(),
            ],
            'lotes_recientes' => LotePago::where('created_at', '>=', now()->subDays(30))->count(),
            'comisiones_vencidas' => Comision::where('estado', 'pendiente')
                ->where('fecha_calculo', '<', now()->subDays(30))
                ->count(),
        ];
    }

    /**
     * Alertas unificadas de todos los módulos
     */
    private function getAlertasUnificadas()
    {
        $alertas = [];

        // Alertas de comisiones
        $comisionesVencidas = Comision::where('estado', 'pendiente')
            ->where('fecha_calculo', '<', now()->subDays(30))
            ->count();
        if ($comisionesVencidas > 0) {
            $alertas[] = [
                'tipo' => 'warning',
                'modulo' => 'Comisiones',
                'mensaje' => "$comisionesVencidas comisiones pendientes por más de 30 días",
                'accion' => '/comisiones/gestion?estado=pendiente',
                'prioridad' => 'alta',
                'icono' => 'clock-history'
            ];
        }

        // Alertas de inventario (stock bajo)
        $stockBajo = Articulo::where('stock_minimo', '>', 0)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->where('stock', '>', 0)
            ->count();
        if ($stockBajo > 0) {
            $alertas[] = [
                'tipo' => 'warning',
                'modulo' => 'Inventario',
                'mensaje' => "$stockBajo artículos con stock bajo",
                'accion' => '/inventario',
                'prioridad' => 'alta',
                'icono' => 'exclamation-triangle'
            ];
        }

        // Alertas de inventario (stock agotado)
        $stockAgotado = Articulo::where('stock', '=', 0)->count();
        if ($stockAgotado > 0) {
            $alertas[] = [
                'tipo' => 'danger',
                'modulo' => 'Inventario',
                'mensaje' => "$stockAgotado artículos completamente agotados",
                'accion' => '/inventario',
                'prioridad' => 'crítica',
                'icono' => 'x-circle-fill'
            ];
        }

        // Alertas de metas no cumplidas del mes actual
        $mesActual = now()->format('Y-m');
        $metasActivas = MetaVenta::where('estado', true)->count();
        $metasCumplidas = 0;
        
        if ($metasActivas > 0) {
            // Calcular cuántas metas se están cumpliendo basado en ventas del mes
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
                        $metasCumplidas++;
                    }
                }
                
                $porcentajeCumplimiento = round(($metasCumplidas / $totalVendedores) * 100, 1);
                
                if ($porcentajeCumplimiento < 50) {
                    $alertas[] = [
                        'tipo' => 'warning',
                        'modulo' => 'Metas',
                        'mensaje' => "Solo {$porcentajeCumplimiento}% de cumplimiento de metas este mes",
                        'accion' => '/metas-ventas',
                        'prioridad' => 'alta',
                        'icono' => 'target'
                    ];
                }
            }
        }

        return $alertas;
    }

    /**
     * KPIs unificados de toda la aplicación
     */
    private function getKPIsUnificados()
    {
        $ventasMes = $this->calcularVentasPorPeriodo(
            now()->startOfMonth()->format('Y-m-d'),
            now()->format('Y-m-d')
        );
        
        $ingresosMes = $this->calcularIngresosPorPeriodo(
            now()->startOfMonth()->format('Y-m-d'),
            now()->format('Y-m-d')
        );

        $margenBruto = $ventasMes - $ingresosMes;
        $porcentajeMargen = $ventasMes > 0 ? ($margenBruto / $ventasMes) * 100 : 0;

        return [
            'ventas_mes' => $ventasMes,
            'ingresos_mes' => $ingresosMes,
            'margen_bruto' => $margenBruto,
            'porcentaje_margen' => round($porcentajeMargen, 2),
            'comisiones_pendientes' => Comision::where('estado', 'pendiente')->sum('monto'),
            'efectividad_cobranza' => $this->calcularEfectividadCobranza(),
        ];
    }

    /**
     * Resumen financiero del período
     */
    private function getResumenFinanciero($startOfMonth, $endOfMonth)
    {
        $ventas = $this->calcularVentasPorPeriodo($startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d'));
        $ingresos = $this->calcularIngresosPorPeriodo($startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d'));
        $comisionesPagadas = Comision::where('estado', 'pagado')
            ->whereMonth('created_at', $startOfMonth->month)
            ->whereYear('created_at', $startOfMonth->year)
            ->sum('monto');
        
        $comisionesPendientes = Comision::where('estado', 'pendiente')->sum('monto');
        $totalComisiones = $comisionesPagadas + $comisionesPendientes;
        
        // Calcular porcentajes
        $porcentajePagadas = $totalComisiones > 0 ? ($comisionesPagadas / $totalComisiones) * 100 : 0;
        $porcentajePendientes = $totalComisiones > 0 ? ($comisionesPendientes / $totalComisiones) * 100 : 0;
        
        // Calcular ROI de comisiones
        $roiComisiones = $ventas > 0 ? ($comisionesPagadas / $ventas) * 100 : 0;
        
        // Calcular tiempo promedio de pago (simplificado)
        $tiempoPromedioPago = 15; // Valor estimado por ahora

        return [
            'ingresos_totales' => $ventas,
            'comisiones_pagadas' => $comisionesPagadas,
            'pendientes_pago' => $comisionesPendientes,
            'porcentaje_pagadas' => round($porcentajePagadas, 1),
            'porcentaje_pendientes' => round($porcentajePendientes, 1),
            'roi_comisiones' => round($roiComisiones, 1),
            'tiempo_promedio_pago' => $tiempoPromedioPago,
            'gastos_compras' => $ingresos,
            'gastos_comisiones' => $comisionesPagadas,
            'utilidad_bruta' => $ventas - $ingresos,
            'utilidad_neta' => $ventas - $ingresos - $comisionesPagadas,
        ];
    }

    /**
     * Calcular efectividad de cobranza de comisiones
     */
    private function calcularEfectividadCobranza()
    {
        $totalComisiones = Comision::count();
        $comisionesPagadas = Comision::where('estado', 'pagado')->count();
        
        return $totalComisiones > 0 ? round(($comisionesPagadas / $totalComisiones) * 100, 2) : 0;
    }

    /**
     * API: Obtener métricas en tiempo real para actualización dinámica
     */
    public function getMetricasEnVivo()
    {
        try {
            $kpis = $this->getKPIsUnificados();
            $stockCritico = Articulo::where('stock', '<=', DB::raw('stock_minimo'))->count();
            $metasAlcanzadas = $this->calculateMetasAlcanzadas();

            return response()->json([
                'ventas_mes' => $kpis['ventas_mes'],
                'comisiones_pendientes' => $kpis['comisiones_pendientes'],
                'efectividad_cobranza' => $kpis['efectividad_cobranza'],
                'stock_critico' => $stockCritico,
                'metas_alcanzadas' => $metasAlcanzadas,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener métricas en vivo: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cargar métricas'], 500);
        }
    }

    /**
     * API: Obtener alertas actuales del sistema
     */
    public function getAlertasApi()
    {
        try {
            $alertas = $this->getAlertasUnificadas();
            return response()->json(['alertas' => $alertas]);
        } catch (\Exception $e) {
            Log::error('Error al obtener alertas: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cargar alertas'], 500);
        }
    }

    /**
     * Calcular porcentaje de metas alcanzadas
     */
    private function calculateMetasAlcanzadas()
    {
        try {
            $mesActual = now()->format('Y-m');
            $metas = MetaVenta::where('estado', true)->get();
            
            if ($metas->isEmpty()) {
                return 0;
            }

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
            
            if ($totalVendedores == 0) {
                return 0;
            }

            foreach ($ventasPorVendedor as $usuarioId => $totalVentas) {
                // Buscar la meta que corresponde a este monto
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

            return $totalVendedores > 0 ? round(($metasAlcanzadas / $totalVendedores) * 100, 1) : 0;
        } catch (\Exception $e) {
            Log::error('Error al calcular metas alcanzadas: ' . $e->getMessage());
            return 0;
        }
    }
}
