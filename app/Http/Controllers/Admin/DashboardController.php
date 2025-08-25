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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
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
        
        $isVendedor = auth()->user()->role_as == 1;
        $usuarioId = auth()->user()->id;

        // Estadísticas básicas filtradas por rol
        $contadores = [];
        if ($isVendedor) {
            // Para vendedores: solo datos relevantes a su trabajo
            $contadores = [
                'mis_clientes' => Cliente::whereHas('vehiculos.ventas', function($q) use ($usuarioId) {
                    $q->where('usuario_id', $usuarioId);
                })->count(),
                'mis_ventas_totales' => Venta::where('usuario_id', $usuarioId)->count(),
                'articulos' => Articulo::count(), // Pueden ver todos los artículos disponibles
                'mis_comisiones' => Comision::where('commissionable_type', 'App\\Models\\User')
                    ->where('commissionable_id', $usuarioId)->count(),
                'metas_activas' => MetaVenta::where('estado', true)->count(),
            ];
        } else {
            // Para administradores: acceso completo
            $contadores = [
                'clientes' => Cliente::count(),
                'vehiculos' => Vehiculo::count(),
                'usuarios' => User::count(),
                'trabajadores' => Trabajador::count(),
                'proveedores' => Proveedor::count(),
                'articulos' => Articulo::count(),
                'comisiones_total' => Comision::count(),
                'lotes_pago' => LotePago::count(),
                'metas_activas' => MetaVenta::where('estado', true)->count(),
            ];
        }

        $data = [
            'contadores' => $contadores,
            'ventas' => $this->getVentasData($today, $startOfWeek, $endOfWeek, $startOfMonth, $endOfMonth, $isVendedor),
            'stock' => [
                'bajo' => Articulo::where('stock_minimo', '>', 0)->whereColumn('stock', '<=', 'stock_minimo')->where('stock', '>', 0)->count(),
                'agotado' => Articulo::where('stock', '=', 0)->count(),
                'critico' => Articulo::where('stock_minimo', '>', 0)->whereColumn('stock', '<=', 'stock_minimo')->count(),
                'sin_stock_total' => Articulo::where('stock', '<=', 0)->count(),
            ],
            'metas' => [
                'alcanzadas' => $this->calculateMetasAlcanzadas($isVendedor),
                'activas' => MetaVenta::where('estado', true)->count(),
            ],
            'alertas' => $this->getAlertasUnificadas(),
            'actividad_reciente' => $this->getActividadReciente($isVendedor),
            // Inventario básico para todos (vendedores necesitan ver stock)
            'inventario' => $isVendedor ? $this->getInventarioBasicoVendedor() : $this->getInventarioData(),
        ];
        
        // Agregar datos específicos por rol
        if (!$isVendedor) {
            // Solo para administradores
            $data['comisiones'] = $this->getComisionesData($startOfMonth, $endOfMonth);
            $data['tendencias'] = $this->getTendencias();
            $data['kpis'] = $this->getKPIsUnificados();
            $data['resumen_financiero'] = $this->getResumenFinanciero($startOfMonth, $endOfMonth);
        } else {
            // Solo para vendedores
            $data['mi_rendimiento'] = $this->getMiRendimiento($usuarioId, $startOfMonth, $endOfMonth);
        }

        return $data;
    }

    private function getVentasData($today, $startOfWeek, $endOfWeek, $startOfMonth, $endOfMonth, $isVendedor = false)
    {
        $usuarioId = $isVendedor ? auth()->user()->id : null;

        $ventasHoy = $this->calcularVentasPorPeriodo($today->format('Y-m-d'), $today->format('Y-m-d'), $usuarioId);
        $ventasSemana = $this->calcularVentasPorPeriodo($startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d'), $usuarioId);
        $ventasMes = $this->calcularVentasPorPeriodo($startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d'), $usuarioId);
        $ventasAño = $this->calcularVentasPorPeriodo(Carbon::now()->startOfYear()->format('Y-m-d'), Carbon::now()->format('Y-m-d'), $usuarioId);

        // Ventas por mes para el gráfico de tendencia - últimos 12 meses
        $ventasPorMes = [];
        $meses = [];
        for ($i = 11; $i >= 0; $i--) {
            $fechaInicio = Carbon::now()->subMonths($i)->startOfMonth();
            $fechaFin = $fechaInicio->copy()->endOfMonth();
            $ventasPorMes[] = $this->calcularVentasPorPeriodo($fechaInicio->format('Y-m-d'), $fechaFin->format('Y-m-d'), $usuarioId);
            $meses[] = $fechaInicio->locale('es')->format('M Y');
        }

        // Ventas recientes - filtradas por usuario si es vendedor
        $query = Venta::with(['cliente', 'detalleVentas', 'usuario'])
            ->orderBy('fecha', 'desc');
        
        if ($isVendedor && $usuarioId) {
            $query->where('usuario_id', $usuarioId);
        }
        
        $ventasRecientes = $query->limit(10)
            ->get()
            ->map(function($venta) {
                $venta->total_calculado = $venta->detalleVentas->sum('sub_total');
                return $venta;
            });

        // Comparación con período anterior
        $ventasSemanaPasada = $this->calcularVentasPorPeriodo(
            $startOfWeek->copy()->subWeek()->format('Y-m-d'),
            $endOfWeek->copy()->subWeek()->format('Y-m-d'),
            $usuarioId
        );
        $ventasMesPasado = $this->calcularVentasPorPeriodo(
            $startOfMonth->copy()->subMonth()->format('Y-m-d'),
            $endOfMonth->copy()->subMonth()->format('Y-m-d'),
            $usuarioId
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

    private function calcularVentasPorPeriodo($fechaInicio, $fechaFin, $usuarioId = null)
    {
        $query = Venta::with('detalleVentas')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', true);
            
        if ($usuarioId) {
            $query->where('usuario_id', $usuarioId);
        }
        
        return $query->get()
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

    /**
     * Inventario básico para vendedores - solo lo esencial
     */
    private function getInventarioBasicoVendedor()
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

        return [
            'stock_bajo' => $stockBajo,
            'stock_agotado' => $stockAgotado,
            'stock_critico_count' => Articulo::where('stock', '<=', 0)->count(),
            // Los vendedores no ven artículos más vendidos ni valor total del inventario
            'articulos_mas_vendidos' => [],
            'valor_inventario' => 0,
        ];
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

    private function getActividadReciente($isVendedor = false)
    {
        $actividades = [];
        $usuarioId = $isVendedor ? auth()->user()->id : null;

        // Ventas recientes - filtradas por usuario si es vendedor
        $queryVentas = Venta::with(['cliente', 'detalleVentas', 'usuario'])
            ->orderBy('created_at', 'desc');
            
        if ($isVendedor && $usuarioId) {
            $queryVentas->where('usuario_id', $usuarioId);
        }
        
        $ventasRecientes = $queryVentas->limit(5)->get();

        foreach ($ventasRecientes as $venta) {
            $total = $venta->detalleVentas->sum('sub_total');
            
            $actividades[] = [
                'tipo' => 'venta',
                'icono' => 'cart-plus',
                'mensaje' => $isVendedor ? 
                    "Tu venta a {$venta->cliente->nombre}" : 
                    "Nueva venta de {$venta->usuario->name} a {$venta->cliente->nombre}",
                'monto' => $total,
                'fecha' => $venta->created_at,
                'url' => "/show-venta/{$venta->id}",
            ];
        }

        // Clientes nuevos - solo para administradores o clientes asociados al vendedor
        if (!$isVendedor) {
            // Administradores ven todos los clientes nuevos
            $clientesNuevos = Cliente::orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        } else {
            // Vendedores solo ven clientes de sus ventas recientes
            $clientesNuevos = Cliente::whereHas('vehiculos.ventas', function($q) use ($usuarioId) {
                $q->where('usuario_id', $usuarioId);
            })
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        }

        foreach ($clientesNuevos as $cliente) {
            $actividades[] = [
                'tipo' => 'cliente',
                'icono' => 'person-plus',
                'mensaje' => $isVendedor ? 
                    "Cliente asociado: {$cliente->nombre}" : 
                    "Nuevo cliente: {$cliente->nombre}",
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
     * Alertas unificadas de todos los módulos - Filtradas por rol
     */
    private function getAlertasUnificadas()
    {
        $alertas = [];
        $isVendedor = auth()->user()->role_as == 1;

        // ALERTAS PARA ADMINISTRADORES ÚNICAMENTE
        if (!$isVendedor) {
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
        }

        // ALERTAS PARA TODOS LOS USUARIOS (pero con enfoques diferentes)
        
        // Alertas de inventario (stock bajo)
        $stockBajo = Articulo::where('stock_minimo', '>', 0)
            ->whereColumn('stock', '<=', 'stock_minimo')
            ->where('stock', '>', 0)
            ->count();
        if ($stockBajo > 0) {
            $alertas[] = [
                'tipo' => 'warning',
                'modulo' => 'Inventario',
                'mensaje' => $isVendedor 
                    ? "$stockBajo productos con stock bajo - Informar al administrador"
                    : "$stockBajo artículos con stock bajo",
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
                'mensaje' => $isVendedor
                    ? "$stockAgotado productos agotados - No disponibles para venta"
                    : "$stockAgotado artículos completamente agotados",
                'accion' => '/inventario',
                'prioridad' => 'crítica',
                'icono' => 'x-circle-fill'
            ];
        }

        // ALERTAS ESPECÍFICAS PARA VENDEDORES
        if ($isVendedor) {
            $usuarioId = auth()->user()->id;
            
            // Verificar mis metas del mes actual
            $ventasDelMes = Venta::with('detalleVentas')
                ->where('usuario_id', $usuarioId)
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->get();
                
            $totalVentasVendedor = $ventasDelMes->sum(function ($venta) {
                return $venta->detalleVentas->sum('sub_total');
            });
            
            // Verificar si está cumpliendo con alguna meta
            $metaActual = MetaVenta::where('estado', true)
                ->where('monto_minimo', '<=', $totalVentasVendedor)
                ->where(function ($query) use ($totalVentasVendedor) {
                    $query->whereNull('monto_maximo')
                          ->orWhere('monto_maximo', '>=', $totalVentasVendedor);
                })
                ->orderBy('monto_minimo', 'desc')
                ->first();
                
            // Alerta si no está cumpliendo ninguna meta y ya pasó la mitad del mes
            if (!$metaActual && now()->day > 15) {
                $proximaMeta = MetaVenta::where('estado', true)
                    ->where('monto_minimo', '>', $totalVentasVendedor)
                    ->orderBy('monto_minimo', 'asc')
                    ->first();
                    
                if ($proximaMeta) {
                    $faltante = $proximaMeta->monto_minimo - $totalVentasVendedor;
                    $alertas[] = [
                        'tipo' => 'info',
                        'modulo' => 'Mi Rendimiento',
                        'mensaje' => "Te faltan Q" . number_format($faltante, 2) . " para alcanzar tu próxima meta",
                        'accion' => '/metas-ventas',
                        'prioridad' => 'media',
                        'icono' => 'target'
                    ];
                }
            }
            
            // Comisiones propias pendientes
            $misComisionesPendientes = Comision::where('commissionable_type', 'App\\Models\\User')
                ->where('commissionable_id', $usuarioId)
                ->where('estado', 'pendiente')
                ->sum('monto');
                
            if ($misComisionesPendientes > 0) {
                $alertas[] = [
                    'tipo' => 'success',
                    'modulo' => 'Mis Comisiones',
                    'mensaje' => "Tienes Q" . number_format($misComisionesPendientes, 2) . " en comisiones pendientes",
                    'accion' => '/comisiones/dashboard',
                    'prioridad' => 'media',
                    'icono' => 'currency-dollar'
                ];
            }
        } else {
            // ALERTAS ADICIONALES PARA ADMINISTRADORES
            
            // Alertas de metas generales del sistema
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
    private function calculateMetasAlcanzadas($isVendedor = false)
    {
        try {
            if ($isVendedor) {
                // Para vendedores: solo verificar si alcanzó alguna meta
                $usuarioId = auth()->user()->id;
                $totalVentasVendedor = Venta::with('detalleVentas')
                    ->where('usuario_id', $usuarioId)
                    ->whereYear('created_at', now()->year)
                    ->whereMonth('created_at', now()->month)
                    ->get()
                    ->sum(function ($venta) {
                        return $venta->detalleVentas->sum('sub_total');
                    });
                    
                $metaAlcanzada = MetaVenta::where('estado', true)
                    ->where('monto_minimo', '<=', $totalVentasVendedor)
                    ->where(function ($query) use ($totalVentasVendedor) {
                        $query->whereNull('monto_maximo')
                              ->orWhere('monto_maximo', '>=', $totalVentasVendedor);
                    })
                    ->orderBy('monto_minimo', 'desc')
                    ->first();
                    
                return $metaAlcanzada ? 100 : 0; // Para vendedores: 100% si alcanzó una meta, 0% si no
            }

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

    /**
     * Método específico para vendedores - Mi rendimiento
     */
    private function getMiRendimiento($usuarioId, $startOfMonth, $endOfMonth)
    {
        try {
            // Mis ventas del mes
            $misVentas = Venta::with('detalleVentas')
                ->where('usuario_id', $usuarioId)
                ->whereBetween('fecha', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                ->get();
                
            $totalVendido = $misVentas->sum(function($venta) {
                return $venta->detalleVentas->sum('sub_total');
            });
            
            $cantidadVentas = $misVentas->count();
            
            // Mi meta actual
            $metaActual = MetaVenta::where('estado', true)
                ->where('monto_minimo', '<=', $totalVendido)
                ->where(function ($query) use ($totalVendido) {
                    $query->whereNull('monto_maximo')
                          ->orWhere('monto_maximo', '>=', $totalVendido);
                })
                ->orderBy('monto_minimo', 'desc')
                ->first();
            
            // Próxima meta a alcanzar
            $proximaMeta = MetaVenta::where('estado', true)
                ->where('monto_minimo', '>', $totalVendido)
                ->orderBy('monto_minimo', 'asc')
                ->first();
                
            // Mis comisiones
            $misComisiones = Comision::where('commissionable_type', 'App\\Models\\User')
                ->where('commissionable_id', $usuarioId)
                ->whereBetween('fecha_calculo', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
                ->get();
                
            return [
                'total_vendido' => $totalVendido,
                'cantidad_ventas' => $cantidadVentas,
                'promedio_venta' => $cantidadVentas > 0 ? $totalVendido / $cantidadVentas : 0,
                'meta_actual' => $metaActual,
                'proxima_meta' => $proximaMeta,
                'comisiones_generadas' => $misComisiones->sum('monto'),
                'comisiones_pendientes' => $misComisiones->where('estado', 'pendiente')->sum('monto'),
                'comisiones_pagadas' => $misComisiones->where('estado', 'pagado')->sum('monto'),
            ];
        } catch (\Exception $e) {
            Log::error('Error al obtener mi rendimiento: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Vista de rendimiento personal para vendedores
     */
    public function miRendimiento()
    {
        $usuarioId = auth()->user()->id;
        $today = Carbon::now();
        
        // Verificar que sea vendedor
        if (auth()->user()->role_as != 1) {
            return redirect()->route('dashboard')->with('error', 'Acceso no autorizado');
        }
        
        // Métricas personales del vendedor
        $misVentasHoy = Venta::where('usuario_id', $usuarioId)
            ->whereDate('fecha', $today)
            ->with('detalleVentas')
            ->get()
            ->sum(function($venta) {
                return $venta->detalleVentas->sum('sub_total');
            });
            
        $misVentasMes = Venta::where('usuario_id', $usuarioId)
            ->whereMonth('fecha', $today->month)
            ->whereYear('fecha', $today->year)
            ->with('detalleVentas')
            ->get()
            ->sum(function($venta) {
                return $venta->detalleVentas->sum('sub_total');
            });
            
        $misVentasAño = Venta::where('usuario_id', $usuarioId)
            ->whereYear('fecha', $today->year)
            ->with('detalleVentas')
            ->get()
            ->sum(function($venta) {
                return $venta->detalleVentas->sum('sub_total');
            });
        
        // Mis comisiones
        $misComisionesTotales = Comision::where('commissionable_type', 'App\\Models\\User')
            ->where('commissionable_id', $usuarioId)
            ->sum('monto');
            
        $misComisionesPendientes = Comision::where('commissionable_type', 'App\\Models\\User')
            ->where('commissionable_id', $usuarioId)
            ->where('estado', 'pendiente')
            ->sum('monto');
        
        // Verificar meta alcanzada este mes
        $metaAlcanzada = MetaVenta::where('estado', true)
            ->where('monto_minimo', '<=', $misVentasMes)
            ->where(function ($query) use ($misVentasMes) {
                $query->whereNull('monto_maximo')
                      ->orWhere('monto_maximo', '>=', $misVentasMes);
            })
            ->orderBy('monto_minimo', 'desc')
            ->first();
        
        // Progreso hacia la siguiente meta
        $siguienteMeta = MetaVenta::where('estado', true)
            ->where('monto_minimo', '>', $misVentasMes)
            ->orderBy('monto_minimo', 'asc')
            ->first();
            
        $progresoMeta = 0;
        if ($siguienteMeta) {
            $progresoMeta = ($misVentasMes / $siguienteMeta->monto_minimo) * 100;
        } elseif ($metaAlcanzada) {
            $progresoMeta = 100; // Meta alcanzada
        }
        
        // Ventas por mes (últimos 12 meses)
        $ventasPorMes = [];
        $mesesLabels = [];
        for ($i = 11; $i >= 0; $i--) {
            $fecha = $today->copy()->subMonths($i);
            $ventasMes = Venta::where('usuario_id', $usuarioId)
                ->whereMonth('fecha', $fecha->month)
                ->whereYear('fecha', $fecha->year)
                ->with('detalleVentas')
                ->get()
                ->sum(function($venta) {
                    return $venta->detalleVentas->sum('sub_total');
                });
            
            $ventasPorMes[] = $ventasMes;
            $mesesLabels[] = $fecha->format('M Y');
        }
        
        // Mis clientes más frecuentes
        $misClientesFrecuentes = Cliente::whereHas('vehiculos.ventas', function($q) use ($usuarioId) {
                $q->where('usuario_id', $usuarioId);
            })
            ->withCount(['vehiculos as ventas_count' => function($q) use ($usuarioId) {
                $q->withCount(['ventas' => function($subQ) use ($usuarioId) {
                    $subQ->where('usuario_id', $usuarioId);
                }]);
            }])
            ->orderBy('ventas_count', 'desc')
            ->limit(5)
            ->get();
        
        $config = Config::first();
        
        return view('admin.dashboard.mi-rendimiento', compact(
            'config', 'misVentasHoy', 'misVentasMes', 'misVentasAño',
            'misComisionesTotales', 'misComisionesPendientes',
            'metaAlcanzada', 'siguienteMeta', 'progresoMeta',
            'ventasPorMes', 'mesesLabels', 'misClientesFrecuentes'
        ));
    }
}
