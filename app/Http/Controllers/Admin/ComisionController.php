<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comision;
use App\Models\Venta;
use App\Models\Trabajador;
use App\Models\User;
use App\Models\PagoComision;
use App\Models\LotePago;
use App\Models\MetaVenta;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ComisionController extends Controller
{
    /**
     * Muestra las comisiones asociadas a una venta
     */
    public function verComisiones($ventaId)
    {
        $comisiones = Comision::where('venta_id', $ventaId)->get();

        return response()->json([
            'comisiones' => $comisiones
        ]);
    }

    /**
     * Nueva vista consolidada de gestión de comisiones
     */
    /**
     * Vista consolidada de gestión de comisiones
     */
    public function gestion(Request $request)
    {
        $config = Config::first();
        
        // Obtener datos necesarios para dropdowns y filtros
        $trabajadores = Trabajador::with('tipoTrabajador')
            ->whereHas('comisiones')
            ->orderBy('nombre')
            ->get();
            
        $vendedores = User::where('role_as', 1)
            ->whereHas('comisiones')
            ->orderBy('name')
            ->get();
            
        return view('admin.comisiones.gestion', compact('trabajadores', 'vendedores', 'config'));
    }

    /**
     * API para obtener todas las comisiones con filtros avanzados
     */
    public function apiTodasComisiones(Request $request)
    {
        // Debug temporal - eliminar en producción
        \Illuminate\Support\Facades\Log::info('Filtros recibidos en apiTodasComisiones:', $request->all());
        
        $config = Config::first();
        $query = Comision::with(['commissionable', 'venta', 'detalleVenta', 'articulo', 'pagos.lotePago']);

        // Aplicar filtros
        $this->aplicarFiltrosAvanzados($query, $request);

        $comisiones = $query->orderBy('fecha_calculo', 'desc')
                           ->paginate(50);

        // Transformar datos para la vista
        $comisionesTransformadas = $comisiones->map(function ($comision) use ($config) {
            // Obtener información del lote de pago si existe
            $loteInfo = null;
            $pagoCompleto = $comision->pagos()->where('estado', 'completado')->first();
            if ($pagoCompleto && $pagoCompleto->lotePago) {
                $loteInfo = [
                    'id' => $pagoCompleto->lotePago->id,
                    'numero_lote' => $pagoCompleto->lotePago->numero_lote,
                    'fecha_pago' => $pagoCompleto->lotePago->fecha_pago,
                    'metodo_pago' => $pagoCompleto->lotePago->metodo_pago,
                    'referencia' => $pagoCompleto->lotePago->referencia
                ];
            }
            
            return [
                'id' => $comision->id,
                'beneficiario_nombre' => $this->obtenerNombreBeneficiario($comision),
                'tipo_receptor' => $this->obtenerTipoReceptor($comision),
                'tipo_comision' => $comision->tipo_comision,
                'tipo_comision_texto' => $this->obtenerTextoTipoComision($comision->tipo_comision),
                'meta_info' => $this->obtenerInfoMeta($comision, $config),
                'monto' => $comision->monto,
                'estado' => $comision->estado,
                'estado_texto' => ucfirst($comision->estado),
                'fecha_calculo' => $comision->fecha_calculo,
                'puede_pagar' => $comision->estado === 'pendiente',
                'venta_id' => $comision->venta_id,
                'venta_info' => $comision->venta ? [
                    'id' => $comision->venta->id,
                    'fecha' => $comision->venta->fecha,
                    'tipo' => $comision->venta->tipo_venta
                ] : null,
                'lote_pago' => $loteInfo
            ];
        });

        // Calcular estadísticas
        $estadisticas = $this->calcularEstadisticas($request);

        return response()->json([
            'comisiones' => $comisionesTransformadas,
            'pagination' => [
                'current_page' => $comisiones->currentPage(),
                'last_page' => $comisiones->lastPage(),
                'total' => $comisiones->total()
            ],
            'estadisticas' => $estadisticas
        ]);
    }

    /**
     * API para obtener comisiones agrupadas por trabajador
     */
    public function apiComisionesTrabajadores(Request $request)
    {
        $query = Trabajador::with(['tipoTrabajador', 'comisiones' => function($q) use ($request) {
            $this->aplicarFiltrosAvanzados($q, $request);
        }])->whereHas('comisiones', function($q) use ($request) {
            $this->aplicarFiltrosAvanzados($q, $request);
            
            // Filtro específico por tipo de trabajador
            if ($request->filled('tipo_trabajador')) {
                $q->where('tipo_comision', $request->tipo_trabajador);
            }
        });

        // Filtro por trabajador específico
        if ($request->filled('trabajador_id')) {
            $query->where('id', $request->trabajador_id);
        }

        $trabajadores = $query->get();

        $trabajadoresTransformados = $trabajadores->map(function ($trabajador) {
            $comisionesTotales = $trabajador->comisiones->sum('monto');
            $comisionesPagadas = $trabajador->comisiones->where('estado', 'pagado')->sum('monto');
            $comisionesPendientes = $trabajador->comisiones->where('estado', 'pendiente')->sum('monto');
            
            return [
                'id' => $trabajador->id,
                'nombre' => $trabajador->nombre . ' ' . $trabajador->apellido,
                'tipo' => $trabajador->tipoTrabajador->nombre ?? 'N/A',
                'total_comisiones' => $comisionesTotales,
                'pagado' => $comisionesPagadas,
                'pendiente' => $comisionesPendientes,
                'cantidad_comisiones' => $trabajador->comisiones->count(),
                'comisiones' => $trabajador->comisiones->map(function($comision) {
                    return [
                        'id' => $comision->id,
                        'monto' => $comision->monto,
                        'estado' => $comision->estado,
                        'fecha_calculo' => $comision->fecha_calculo,
                        'tipo_comision' => $comision->tipo_comision,
                    ];
                })
            ];
        });

        return response()->json([
            'trabajadores' => $trabajadoresTransformados
        ]);
    }

    /**
     * API para obtener comisiones agrupadas por vendedor
     */
    public function apiComisionesVendedores(Request $request)
    {
        $query = User::where('role_as', 1)
            ->with(['comisiones' => function($q) use ($request) {
                $q->where('tipo_comision', 'venta_meta');
                $this->aplicarFiltrosAvanzados($q, $request);
            }])
            ->whereHas('comisiones', function($q) use ($request) {
                $q->where('tipo_comision', 'venta_meta');
                $this->aplicarFiltrosAvanzados($q, $request);
            });

        // Filtro por vendedor específico
        if ($request->filled('vendedor_id')) {
            $query->where('id', $request->vendedor_id);
        }

        $vendedores = $query->get();

        $vendedoresTransformados = $vendedores->map(function ($vendedor) {
            $comisionesTotales = $vendedor->comisiones->sum('monto');
            $comisionesPagadas = $vendedor->comisiones->where('estado', 'pagado')->sum('monto');
            $comisionesPendientes = $vendedor->comisiones->where('estado', 'pendiente')->sum('monto');
            
            return [
                'id' => $vendedor->id,
                'nombre' => $vendedor->name,
                'email' => $vendedor->email,
                'total_comisiones' => $comisionesTotales,
                'pagado' => $comisionesPagadas,
                'pendiente' => $comisionesPendientes,
                'cantidad_comisiones' => $vendedor->comisiones->count(),
                'comisiones' => $vendedor->comisiones->map(function($comision) {
                    return [
                        'id' => $comision->id,
                        'monto' => $comision->monto,
                        'estado' => $comision->estado,
                        'fecha_calculo' => $comision->fecha_calculo,
                        'porcentaje' => $comision->porcentaje,
                    ];
                })
            ];
        });

        return response()->json([
            'vendedores' => $vendedoresTransformados
        ]);
    }

    /**
     * Aplicar filtros avanzados a la consulta
     */
    private function aplicarFiltrosAvanzados($query, $request)
    {
        // FILTRO AUTOMÁTICO DE SEGURIDAD: Los vendedores solo ven sus propias comisiones
        if (auth()->user()->role_as == 1) {
            $query->where(function($q) {
                $q->where('commissionable_type', 'App\\Models\\User')
                  ->where('commissionable_id', auth()->user()->id);
            });
        }

        // Filtro por fechas específicas
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha_calculo', [
                Carbon::parse($request->fecha_inicio)->startOfDay(),
                Carbon::parse($request->fecha_fin)->endOfDay()
            ]);
        } elseif ($request->filled('tipo_periodo')) {
            $fechas = $this->calcularFechasPorTipo($request->tipo_periodo);
            $query->whereBetween('fecha_calculo', [$fechas['inicio'], $fechas['fin']]);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por tipo de comisión
        if ($request->filled('tipo_comision')) {
            $query->where('tipo_comision', $request->tipo_comision);
        }

        // Filtro por rango de monto
        if ($request->filled('monto_minimo')) {
            $query->where('monto', '>=', $request->monto_minimo);
        }
        if ($request->filled('monto_maximo')) {
            $query->where('monto', '<=', $request->monto_maximo);
        }

        // FILTROS ESPECÍFICOS POR TRABAJADOR (Solo para administradores)
        if (auth()->user()->role_as != 1 && $request->filled('trabajador_id')) {
            $query->where('commissionable_type', 'App\\Models\\Trabajador')
                  ->where('commissionable_id', $request->trabajador_id);
        }

        // Filtro por tipo de trabajador (Solo para administradores)
        if (auth()->user()->role_as != 1 && $request->filled('tipo_trabajador')) {
            $query->where('commissionable_type', 'App\\Models\\Trabajador')
                  ->whereHas('commissionable.tipoTrabajador', function($q) use ($request) {
                      switch ($request->tipo_trabajador) {
                          case 'mecanico':
                              $q->where('nombre', 'like', '%mecánico%')
                                ->orWhere('nombre', 'like', '%Mecánico%');
                              break;
                          case 'carwash':
                              $q->where('nombre', 'like', '%Car Wash%')
                                ->orWhere('nombre', 'like', '%car wash%')
                                ->orWhere('nombre', 'like', '%carwash%');
                              break;
                          case 'administrativo':
                              $q->where('nombre', 'like', '%administrativo%')
                                ->orWhere('nombre', 'like', '%Administrativo%');
                              break;
                      }
                  });
        }

        // FILTROS ESPECÍFICOS POR VENDEDOR (Solo para administradores)
        if (auth()->user()->role_as != 1 && $request->filled('vendedor_id')) {
            $query->where('commissionable_type', 'App\\Models\\User')
                  ->where('commissionable_id', $request->vendedor_id);
        }

        // Filtro por período de meta (solo aplica para comisiones de venta_meta)
        if ($request->filled('periodo_meta')) {
            $query->where('tipo_comision', 'venta_meta');
            
            // Calcular fechas según el período de meta seleccionado
            $fechasBase = $this->calcularPeriodo('mes_actual'); // fechas base
            $fechasMeta = $this->ajustarFechasPorPeriodoMeta($fechasBase, $request->periodo_meta);
            $query->whereBetween('fecha_calculo', [$fechasMeta['inicio'], $fechasMeta['fin']]);
            
            // Verificar que existan metas activas para este período
            $query->whereExists(function($q) use ($request) {
                $q->select(DB::raw(1))
                  ->from('metas_ventas')
                  ->where('periodo', $request->periodo_meta)
                  ->where('estado', true);
            });
        }

        // Filtro por beneficiario específico (mantenido para compatibilidad)
        if ($request->filled('beneficiario_id') && $request->filled('beneficiario_tipo')) {
            $query->where('commissionable_id', $request->beneficiario_id)
                  ->where('commissionable_type', $request->beneficiario_tipo);
        }

        return $query;
    }

    /**
     * Calcular fechas según tipo de período
     */
    private function calcularFechasPorTipo($tipoPeriodo)
    {
        $ahora = Carbon::now();
        
        switch ($tipoPeriodo) {
            case 'hoy':
                return [
                    'inicio' => $ahora->copy()->startOfDay(),
                    'fin' => $ahora->copy()->endOfDay()
                ];
            case 'ayer':
                return [
                    'inicio' => $ahora->copy()->subDay()->startOfDay(),
                    'fin' => $ahora->copy()->subDay()->endOfDay()
                ];
            case 'semana_actual':
                return [
                    'inicio' => $ahora->copy()->startOfWeek(),
                    'fin' => $ahora->copy()->endOfWeek()
                ];
            case 'semana_pasada':
                return [
                    'inicio' => $ahora->copy()->subWeek()->startOfWeek(),
                    'fin' => $ahora->copy()->subWeek()->endOfWeek()
                ];
            case 'mes_actual':
                return [
                    'inicio' => $ahora->copy()->startOfMonth(),
                    'fin' => $ahora->copy()->endOfMonth()
                ];
            case 'mes_pasado':
                return [
                    'inicio' => $ahora->copy()->subMonth()->startOfMonth(),
                    'fin' => $ahora->copy()->subMonth()->endOfMonth()
                ];
            case 'trimestre_actual':
                return [
                    'inicio' => $ahora->copy()->startOfQuarter(),
                    'fin' => $ahora->copy()->endOfQuarter()
                ];
            case 'trimestre_pasado':
                return [
                    'inicio' => $ahora->copy()->subQuarter()->startOfQuarter(),
                    'fin' => $ahora->copy()->subQuarter()->endOfQuarter()
                ];
            case 'ultimo_30_dias':
                return [
                    'inicio' => $ahora->copy()->subDays(30)->startOfDay(),
                    'fin' => $ahora->copy()->endOfDay()
                ];
            default:
                return [
                    'inicio' => $ahora->copy()->startOfMonth(),
                    'fin' => $ahora->copy()->endOfMonth()
                ];
        }
    }

    /**
     * Calcular estadísticas para las cards
     */
    private function calcularEstadisticas($request)
    {
        $query = Comision::query();
        $this->aplicarFiltrosAvanzados($query, $request);

        $total = $query->sum('monto');
        $totalPagadas = $query->where('estado', 'pagado')->sum('monto');
        $totalPendientes = $query->where('estado', 'pendiente')->sum('monto');
        
        $cantidadTotal = $query->count();
        $cantidadPagadas = $query->where('estado', 'pagado')->count();
        $cantidadPendientes = $query->where('estado', 'pendiente')->count();

        return [
            'total' => $total,
            'total_pagadas' => $totalPagadas,
            'total_pendientes' => $totalPendientes,
            'cantidad_total' => $cantidadTotal,
            'cantidad_pagadas' => $cantidadPagadas,
            'cantidad_pendientes' => $cantidadPendientes
        ];
    }

    /**
     * Obtener nombre del beneficiario
     */
    private function obtenerNombreBeneficiario($comision)
    {
        if ($comision->commissionable_type === 'App\Models\User') {
            return $comision->commissionable->name ?? 'Usuario eliminado';
        } elseif ($comision->commissionable_type === 'App\Models\Trabajador') {
            return ($comision->commissionable->nombre ?? '') . ' ' . ($comision->commissionable->apellido ?? '');
        }
        return 'Desconocido';
    }

    /**
     * Obtener tipo de receptor
     */
    private function obtenerTipoReceptor($comision)
    {
        if ($comision->commissionable_type === 'App\Models\User') {
            return 'vendedor';
        } elseif ($comision->commissionable_type === 'App\Models\Trabajador') {
            return 'trabajador';
        }
        return 'desconocido';
    }

    /**
     * Obtener texto descriptivo del tipo de comisión
     */
    private function obtenerTextoTipoComision($tipo)
    {
        switch($tipo) {
            case 'meta_venta':
            case 'venta_meta':
                return 'Meta de Ventas';
            case 'mecanico':
                return 'Mecánico';
            case 'carwash':
                return 'Car Wash';
            default:
                return ucfirst($tipo);
        }
    }

    /**
     * Obtener información de la meta alcanzada para comisiones por metas
     */
    private function obtenerInfoMeta($comision, $config = null)
    {
        // Solo para comisiones de meta_venta
        if ($comision->tipo_comision !== 'meta_venta' && $comision->tipo_comision !== 'venta_meta') {
            return null;
        }

        // Obtener configuración si no se proporciona
        if (!$config) {
            $config = Config::first();
        }

        // Buscar la meta que coincida con el porcentaje de la comisión
        $meta = MetaVenta::where('porcentaje_comision', $comision->porcentaje)
                        ->where('estado', true)
                        ->first();
        
        if ($meta) {
            // Generar color basado en el nombre de la meta para consistencia
            $colores = ['primary', 'success', 'info', 'warning', 'secondary', 'dark'];
            $colorIndex = abs(crc32($meta->nombre)) % count($colores);
            
            return [
                'nombre' => $meta->nombre,
                'color' => $colores[$colorIndex],
                'rango' => $config->currency_simbol . number_format($meta->monto_minimo, 0) . ' - ' . $config->currency_simbol . number_format($meta->monto_maximo, 0),
                'porcentaje' => $meta->porcentaje_comision
            ];
        }

        // Si no se encuentra la meta, usar porcentaje como fallback
        return [
            'nombre' => $comision->porcentaje . '%',
            'color' => 'secondary',
            'rango' => 'Meta no encontrada',
            'porcentaje' => $comision->porcentaje
        ];
    }

    /**
     * Dashboard principal modernizado de comisiones
     */
    public function dashboard(Request $request)
    {
        $config = Config::first();
        
        // FILTRO DE SEGURIDAD: Los vendedores solo ven sus propios datos
        $baseQuery = Comision::query();
        if (auth()->user()->role_as == 1) {
            $baseQuery->where('commissionable_type', 'App\\Models\\User')
                      ->where('commissionable_id', auth()->user()->id);
        }
        
        // === ESTADÍSTICAS PRINCIPALES ===
        
        // Comisiones pendientes (filtradas por usuario si es vendedor)
        $comisionesPendientes = (clone $baseQuery)->where('estado', 'pendiente')->sum('monto');
        $cantidadPendientes = (clone $baseQuery)->where('estado', 'pendiente')->count();
        
        // Comisiones pagadas este mes (filtradas por usuario si es vendedor)
        $comisionesPagadasMes = (clone $baseQuery)->where('estado', 'pagado')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('monto');
        $cantidadPagadasMes = (clone $baseQuery)->where('estado', 'pagado')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Lotes de pago recientes - Solo administradores
        if (auth()->user()->role_as != 1) {
            $lotesPendientes = LotePago::where('created_at', '>=', now()->subDays(30))->count();
            $montoLotesPendientes = LotePago::where('created_at', '>=', now()->subDays(30))->sum('monto_total');
            $metasProximasVencer = MetaVenta::where('estado', true)->count();
        } else {
            $lotesPendientes = 0;
            $montoLotesPendientes = 0;
            $metasProximasVencer = 0;
        }
            
        // === DISTRIBUCIÓN POR TIPOS (filtrada) ===
        $comisionesPorTipo = (clone $baseQuery)->select('tipo_comision', DB::raw('SUM(monto) as total_monto'), DB::raw('COUNT(*) as cantidad'))
            ->where('estado', 'pendiente')
            ->groupBy('tipo_comision')
            ->get();
            
        // === TENDENCIAS MENSUALES (filtrada) ===
        $tendenciaMensual = (clone $baseQuery)->select(
                DB::raw('YEAR(created_at) as año'),
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('SUM(monto) as total'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('año', 'mes')
            ->orderBy('año', 'desc')
            ->orderBy('mes', 'desc')
            ->get();
            
        // === TOP BENEFICIARIOS ===
        // Top vendedores por comisiones (filtrado)
        $topVendedoresQuery = Comision::where('commissionable_type', 'App\Models\User');
        if (auth()->user()->role_as == 1) {
            $topVendedoresQuery->where('commissionable_id', auth()->user()->id);
        }
        
        $topVendedoresData = $topVendedoresQuery->select('commissionable_id', DB::raw('SUM(monto) as total_comisiones'), DB::raw('COUNT(*) as cantidad'))
            ->groupBy('commissionable_id')
            ->orderBy('total_comisiones', 'desc')
            ->limit(5)
            ->get();
            
        // Cargar los usuarios correspondientes
        $topVendedores = $topVendedoresData->map(function($item) {
            $usuario = User::find($item->commissionable_id);
            return (object)[
                'commissionable_id' => $item->commissionable_id,
                'total_comisiones' => $item->total_comisiones,
                'cantidad' => $item->cantidad,
                'commissionable' => $usuario
            ];
        });
            
        // Top trabajadores por comisiones - Solo administradores
        if (auth()->user()->role_as != 1) {
            $topTrabajadoresData = Comision::where('commissionable_type', 'App\Models\Trabajador')
                ->select('commissionable_id', DB::raw('SUM(monto) as total_comisiones'), DB::raw('COUNT(*) as cantidad'))
                ->groupBy('commissionable_id')
                ->orderBy('total_comisiones', 'desc')
                ->limit(5)
                ->get();
                
            // Cargar los trabajadores correspondientes
            $topTrabajadores = $topTrabajadoresData->map(function($item) {
                $trabajador = Trabajador::find($item->commissionable_id);
                return (object)[
                    'commissionable_id' => $item->commissionable_id,
                    'total_comisiones' => $item->total_comisiones,
                    'cantidad' => $item->cantidad,
                    'commissionable' => $trabajador
                ];
            });
        } else {
            $topTrabajadores = collect();
        }
            
        // === ACTIVIDAD RECIENTE ===
        // Últimas comisiones generadas
        $ultimasComisiones = Comision::with(['commissionable', 'venta'])
            ->orderBy('fecha_calculo', 'desc')
            ->limit(10)
            ->get();
            
        // Últimos lotes de pago
        $ultimosLotes = LotePago::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // === ALERTAS Y NOTIFICACIONES ===
        $alertas = [];
        
        // Comisiones pendientes por más de 30 días
        $comisionesVencidas = Comision::where('estado', 'pendiente')
            ->where('fecha_calculo', '<', now()->subDays(30))
            ->count();
        if ($comisionesVencidas > 0) {
            $alertas[] = [
                'tipo' => 'warning',
                'mensaje' => "$comisionesVencidas comisiones pendientes por más de 30 días",
                'accion' => route('comisiones.gestion', ['estado' => 'pendiente'])
            ];
        }
        
        // Lotes de pago realmente pendientes (no completados)
        $lotesPendientesReales = LotePago::where('estado', '!=', 'completado')->count();
        if ($lotesPendientesReales > 0) {
            $alertas[] = [
                'tipo' => 'info',
                'mensaje' => "$lotesPendientesReales lotes de pago pendientes de procesamiento",
                'accion' => route('lotes-pago.index')
            ];
        }
        
        // Compilar datos para la vista
        $datos = [
            'estadisticas' => [
                'comisiones_pendientes' => $comisionesPendientes,
                'cantidad_pendientes' => $cantidadPendientes,
                'comisiones_pagadas_mes' => $comisionesPagadasMes,
                'cantidad_pagadas_mes' => $cantidadPagadasMes,
                'lotes_pendientes' => $lotesPendientes,
                'monto_lotes_pendientes' => $montoLotesPendientes,
                'metas_proximas_vencer' => $metasProximasVencer
            ],
            'distribucion_tipos' => $comisionesPorTipo,
            'tendencia_mensual' => $tendenciaMensual,
            'top_vendedores' => $topVendedores,
            'top_trabajadores' => $topTrabajadores,
            'ultimas_comisiones' => $ultimasComisiones,
            'ultimos_lotes' => $ultimosLotes,
            'alertas' => $alertas
        ];
        
        return view('admin.comisiones.dashboard-moderno', compact('datos', 'config'));
    }
    
    /**
     * Calcular comisiones de vendedores por metas
     */
    private function calcularComisionesVendedores($fechas, $periodoMeta = 'mensual')
    {
        // Ajustar fechas según el período de la meta
        $fechasAjustadas = $this->ajustarFechasPorPeriodoMeta($fechas, $periodoMeta);
        
        // Obtener todos los vendedores con sus ventas en el período
        $vendedores = DB::table('ventas as v')
            ->join('users as u', 'v.usuario_id', '=', 'u.id')
            ->join('detalle_ventas as dv', 'v.id', '=', 'dv.venta_id')
            ->select(
                'u.id',
                'u.name as nombre',
                DB::raw('COUNT(DISTINCT v.id) as total_ventas'),
                DB::raw('SUM(dv.sub_total) as total_vendido')
            )
            ->whereBetween('v.fecha', [$fechasAjustadas['inicio'], $fechasAjustadas['fin']])
            ->where('v.estado', 1)
            ->groupBy('u.id', 'u.name')
            ->get();

        // Calcular comisión basada en metas para cada vendedor
        $resultado = collect();
        
        foreach ($vendedores as $vendedor) {
            $meta = MetaVenta::determinarMetaPorMonto($vendedor->total_vendido, $periodoMeta);
            
            $comisionCalculada = 0;
            $metaNombre = 'Sin meta aplicable';
            $porcentajeAplicado = 0;
            $metaDetalles = null;
            
            if ($meta) {
                $comisionCalculada = $vendedor->total_vendido * ($meta->porcentaje_comision / 100);
                $metaNombre = $meta->nombre;
                $porcentajeAplicado = $meta->porcentaje_comision;
                $metaDetalles = [
                    'id' => $meta->id,
                    'nombre' => $meta->nombre,
                    'descripcion' => $meta->descripcion,
                    'rango_minimo' => $meta->monto_minimo,
                    'rango_maximo' => $meta->monto_maximo,
                    'porcentaje' => $meta->porcentaje_comision,
                    'periodo' => $meta->periodo
                ];
            }
            
            // Verificar si ya existe comisión registrada en BD
            $comisionExistente = Comision::where('commissionable_type', 'App\Models\User')
                ->where('commissionable_id', $vendedor->id)
                ->where('tipo_comision', 'venta_meta')
                ->whereBetween('fecha_calculo', [$fechasAjustadas['inicio'], $fechasAjustadas['fin']])
                ->first();
                
            $estado = $comisionExistente ? $comisionExistente->estado : 'calculado';
            
            $resultado->push((object)[
                'id' => $vendedor->id,
                'nombre' => $vendedor->nombre,
                'total_ventas' => $vendedor->total_ventas,
                'total_vendido' => $vendedor->total_vendido,
                'comision_calculada' => round($comisionCalculada, 2),
                'meta_alcanzada' => $metaNombre,
                'porcentaje_aplicado' => $porcentajeAplicado,
                'meta_detalles' => $metaDetalles,
                'periodo_meta' => $periodoMeta,
                'estado' => $estado,
                'comision_id' => $comisionExistente->id ?? null
            ]);
        }
        
        return $resultado;
    }
    
    /**
     * Ajustar fechas según el período de la meta
     */
    private function ajustarFechasPorPeriodoMeta($fechas, $periodoMeta)
    {
        $hoy = Carbon::now();
        
        switch($periodoMeta) {
            case 'mensual':
                return [
                    'inicio' => $hoy->copy()->startOfMonth(),
                    'fin' => $hoy->copy()->endOfMonth()
                ];
            case 'trimestral':
                return [
                    'inicio' => $hoy->copy()->startOfQuarter(),
                    'fin' => $hoy->copy()->endOfQuarter()
                ];
            case 'semestral':
                $mesActual = $hoy->month;
                if ($mesActual <= 6) {
                    return [
                        'inicio' => $hoy->copy()->startOfYear(),
                        'fin' => $hoy->copy()->startOfYear()->addMonths(5)->endOfMonth()
                    ];
                } else {
                    return [
                        'inicio' => $hoy->copy()->startOfYear()->addMonths(6),
                        'fin' => $hoy->copy()->endOfYear()
                    ];
                }
            case 'anual':
                return [
                    'inicio' => $hoy->copy()->startOfYear(),
                    'fin' => $hoy->copy()->endOfYear()
                ];
            default:
                return $fechas;
        }
    }
    
    /**
     * Calcular comisiones de mecánicos
     */
    private function calcularComisionesMecanicos($fechas)
    {
        return DB::table('detalle_ventas as dv')
            ->join('ventas as v', 'dv.venta_id', '=', 'v.id')
            ->join('articulos as a', 'dv.articulo_id', '=', 'a.id')
            ->join('trabajadors as t', 'a.mecanico_id', '=', 't.id')
            ->select(
                't.id',
                't.nombre',
                't.apellido',
                DB::raw('COUNT(dv.id) as total_servicios'),
                DB::raw('SUM(a.costo_mecanico * dv.cantidad) as comision_calculada'),
                DB::raw('"pendiente" as estado')
            )
            ->whereBetween('v.fecha', [$fechas['inicio'], $fechas['fin']])
            ->where('v.estado', 1)
            ->where('a.tipo', 'servicio')
            ->whereNotNull('a.mecanico_id')
            ->groupBy('t.id', 't.nombre', 't.apellido')
            ->get();
    }
    
    /**
     * Calcular comisiones de carwash
     */
    private function calcularComisionesCarwash($fechas)
    {
        return DB::table('trabajador_detalle_venta as tdv')
            ->join('detalle_ventas as dv', 'tdv.detalle_venta_id', '=', 'dv.id')
            ->join('ventas as v', 'dv.venta_id', '=', 'v.id')
            ->join('trabajadors as t', 'tdv.trabajador_id', '=', 't.id')
            ->join('tipo_trabajadors as tt', 't.tipo_trabajador_id', '=', 'tt.id')
            ->select(
                't.id',
                't.nombre',
                't.apellido',
                DB::raw('COUNT(tdv.id) as total_servicios'),
                DB::raw('SUM(tdv.monto_comision) as comision_calculada'),
                DB::raw('"pendiente" as estado')
            )
            ->whereBetween('v.fecha', [$fechas['inicio'], $fechas['fin']])
            ->where('v.estado', 1)
            ->where('tt.nombre', 'like', '%Car Wash%')
            ->groupBy('t.id', 't.nombre', 't.apellido')
            ->get();
    }
    
    /**
     * Calcular período de fechas
     */
    private function calcularPeriodo($periodo)
    {
        switch($periodo) {
            case 'hoy':
                return [
                    'inicio' => Carbon::today(),
                    'fin' => Carbon::today()
                ];
            case 'semana_actual':
                return [
                    'inicio' => Carbon::now()->startOfWeek(),
                    'fin' => Carbon::now()->endOfWeek()
                ];
            case 'mes_actual':
                return [
                    'inicio' => Carbon::now()->startOfMonth(),
                    'fin' => Carbon::now()->endOfMonth()
                ];
            case 'mes_anterior':
                return [
                    'inicio' => Carbon::now()->subMonth()->startOfMonth(),
                    'fin' => Carbon::now()->subMonth()->endOfMonth()
                ];
            default:
                return [
                    'inicio' => Carbon::now()->startOfMonth(),
                    'fin' => Carbon::now()->endOfMonth()
                ];
        }
    }

    /**
     * Mostrar un listado de comisiones con filtros
     */
    public function index(Request $request)
    {
        $query = Comision::with(['commissionable', 'venta', 'detalleVenta', 'articulo']);

        // Filtrar por tipo de comisión
        if ($request->has('tipo_comision')) {
            $query->where('tipo_comision', $request->tipo_comision);
        }

        // Filtrar por tipo de receptor (trabajador o vendedor)
        if ($request->has('tipo_receptor')) {
            $query->porTipoReceptor($request->tipo_receptor);
        }

        // Filtrar por estado de pago
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtrar por fechas
        if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
            $query->whereBetween('fecha_calculo', [
                Carbon::parse($request->fecha_inicio)->startOfDay(),
                Carbon::parse($request->fecha_fin)->endOfDay()
            ]);
        } elseif ($request->has('periodo') && $request->periodo === 'mes_actual') {
            $query->delMesActual();
        }

        $comisiones = $query->orderBy('fecha_calculo', 'desc')->paginate(20);

        // Obtener los tipos de comisión para el filtro
        $tiposComision = Comision::select('tipo_comision')
            ->distinct()
            ->pluck('tipo_comision');

        $config = Config::first();

        return view('admin.comisiones.index', compact('comisiones', 'tiposComision', 'config'));
    }

    /**
     * Muestra los detalles de una comisión específica
     */
    public function show($id)
    {
        $config = Config::first();
        $comision = Comision::with(['commissionable', 'venta', 'detalleVenta', 'articulo', 'pagos.lotePago'])
            ->findOrFail($id);

        // Agregar información de meta si es comisión de meta_venta
        $metaInfo = $this->obtenerInfoMeta($comision, $config);

        return view('admin.comisiones.show', compact('comision', 'config', 'metaInfo'));
    }

    /**
     * Mostrar comisiones agrupadas por trabajador
     */
    public function porTrabajador(Request $request)
    {
        $query = Trabajador::with(['comisiones' => function($q) use ($request) {
            // Aplicar filtros a las comisiones
            if ($request->has('estado')) {
                $q->where('estado', $request->estado);
            }

            if ($request->has('tipo_comision')) {
                $q->where('tipo_comision', $request->tipo_comision);
            }

            // Filtrar por fechas
            if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
                $q->whereBetween('fecha_calculo', [
                    Carbon::parse($request->fecha_inicio)->startOfDay(),
                    Carbon::parse($request->fecha_fin)->endOfDay()
                ]);
            } elseif ($request->has('periodo') && $request->periodo === 'mes_actual') {
                $q->delMesActual();
            }
        }]);

        // Filtrar trabajadores que tengan comisiones
        $query->whereHas('comisiones', function($q) use ($request) {
            if ($request->has('estado')) {
                $q->where('estado', $request->estado);
            }

            if ($request->has('tipo_comision')) {
                $q->where('tipo_comision', $request->tipo_comision);
            }
        });

        $trabajadores = $query->paginate(15);

        // Obtener los tipos de comisión para el filtro
        $tiposComision = Comision::select('tipo_comision')
            ->whereIn('tipo_comision', ['mecanico', 'carwash'])
            ->distinct()
            ->pluck('tipo_comision');

        $config = Config::first();

        return view('admin.comisiones.por_trabajador', compact('trabajadores', 'tiposComision', 'config'));
    }

    /**
     * Mostrar comisiones agrupadas por vendedor (usuarios)
     */
    public function porVendedor(Request $request)
    {
        $query = User::whereHas('comisiones', function($q) {
            $q->where('tipo_comision', 'meta_venta');
        })->with(['comisiones' => function($q) use ($request) {
            $q->where('tipo_comision', 'meta_venta');

            // Aplicar filtros a las comisiones
            if ($request->has('estado')) {
                $q->where('estado', $request->estado);
            }

            // Filtrar por fechas
            if ($request->has('fecha_inicio') && $request->has('fecha_fin')) {
                $q->whereBetween('fecha_calculo', [
                    Carbon::parse($request->fecha_inicio)->startOfDay(),
                    Carbon::parse($request->fecha_fin)->endOfDay()
                ]);
            } elseif ($request->has('periodo') && $request->periodo === 'mes_actual') {
                $q->delMesActual();
            }
        }]);

        $vendedores = $query->paginate(15);

        $config = Config::first();

        return view('admin.comisiones.por_vendedor', compact('vendedores', 'config'));
    }

    /**
     * Registrar un pago individual y crear lote automáticamente
     */
    public function registrarPago(Request $request, $id)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|string',
            'referencia' => 'nullable|string|max:255',
            'comprobante_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'observaciones' => 'nullable|string|max:255',
        ]);

        $comision = Comision::findOrFail($id);

        // Asegurar formato correcto del monto (punto como separador decimal)
        $montoLimpio = floatval(str_replace(',', '.', $request->monto));

        // Verificar que el monto a pagar no exceda el pendiente
        $montoPendiente = $comision->montoPendiente();
        if ($montoLimpio > $montoPendiente) {
            return back()->with('error', "El monto máximo a pagar es de $montoPendiente");
        }

        try {
            DB::beginTransaction();

            // Manejar upload de comprobante
            $comprobanteNombre = null;
            if ($request->hasFile('comprobante_imagen')) {
                $comprobante = $request->file('comprobante_imagen');
                $comprobanteNombre = time() . '_' . $comprobante->getClientOriginalName();
                $comprobante->move(public_path('uploads/comprobantes'), $comprobanteNombre);
            }

            // Crear el lote de pago individual
            $lotePago = LotePago::create([
                'numero_lote' => LotePago::generarNumeroLote(),
                'fecha_pago' => $request->fecha_pago,
                'metodo_pago' => $request->metodo_pago,
                'referencia' => $request->referencia,
                'comprobante_imagen' => $comprobanteNombre,
                'monto_total' => $montoLimpio,
                'cantidad_comisiones' => 1,
                'estado' => 'completado',
                'usuario_id' => auth()->id(),
                'observaciones' => $request->observaciones,
            ]);

            // Crear el pago y asociarlo al lote
            $pago = PagoComision::create([
                'comision_id' => $comision->id,
                'lote_pago_id' => $lotePago->id,
                'monto' => $montoLimpio,
                'metodo_pago' => $request->metodo_pago,
                'usuario_id' => auth()->id(),
                'fecha_pago' => $request->fecha_pago,
                'referencia' => $request->referencia,
                'comprobante_imagen' => $comprobanteNombre,
                'observaciones' => $request->observaciones,
                'estado' => 'completado',
            ]);

            // Actualizar el estado de la comisión
            $comision->actualizarEstado();

            DB::commit();

            return redirect()->route('lotes-pago.show', $lotePago)
                            ->with('success', 'Pago registrado y lote creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar resumen de comisiones por periodo
     */
    public function resumen(Request $request)
    {
        // Fecha por defecto: mes actual
        $fechaInicio = $request->fecha_inicio ? Carbon::parse($request->fecha_inicio)->startOfDay() : Carbon::now()->startOfMonth();
        $fechaFin = $request->fecha_fin ? Carbon::parse($request->fecha_fin)->endOfDay() : Carbon::now()->endOfMonth();

        // Totales por tipo de comisión
        $totalesPorTipo = Comision::select('tipo_comision', DB::raw('SUM(monto) as total'))
            ->whereBetween('fecha_calculo', [$fechaInicio, $fechaFin])
            ->groupBy('tipo_comision')
            ->get();

        // Totales por estado
        $totalesPorEstado = Comision::select('estado', DB::raw('SUM(monto) as total'))
            ->whereBetween('fecha_calculo', [$fechaInicio, $fechaFin])
            ->groupBy('estado')
            ->get();

        // Top 5 trabajadores con más comisiones
        $topTrabajadores = Trabajador::whereHas('comisiones', function($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_calculo', [$fechaInicio, $fechaFin]);
            })
            ->withSum(['comisiones' => function($q) use ($fechaInicio, $fechaFin) {
                $q->whereBetween('fecha_calculo', [$fechaInicio, $fechaFin]);
            }], 'monto')
            ->orderByDesc('comisiones_sum_monto')
            ->limit(5)
            ->get();

        // Top 5 vendedores con más comisiones por metas
        $topVendedores = User::whereHas('comisiones', function($q) use ($fechaInicio, $fechaFin) {
                $q->where('tipo_comision', 'meta_venta')
                  ->whereBetween('fecha_calculo', [$fechaInicio, $fechaFin]);
            })
            ->withSum(['comisiones' => function($q) use ($fechaInicio, $fechaFin) {
                $q->where('tipo_comision', 'meta_venta')
                  ->whereBetween('fecha_calculo', [$fechaInicio, $fechaFin]);
            }], 'monto')
            ->orderByDesc('comisiones_sum_monto')
            ->limit(5)
            ->get();

        $config = Config::first();

        return view('admin.comisiones.resumen', compact(
            'fechaInicio',
            'fechaFin',
            'totalesPorTipo',
            'totalesPorEstado',
            'topTrabajadores',
            'topVendedores',
            'config'
        ));
    }

    /**
     * Detalles de comisión de vendedor
     */
    private function detalleComisionVendedor($vendedorId, $fechas)
    {
        return DB::table('ventas as v')
            ->join('detalle_ventas as dv', 'v.id', '=', 'dv.venta_id')
            ->select(
                'v.id',
                'v.fecha',
                DB::raw('SUM(dv.sub_total) as sub_total'),
                DB::raw('ROUND(SUM(dv.sub_total) * 0.05, 2) as comision'),
                'v.estado'
            )
            ->where('v.usuario_id', $vendedorId)
            ->whereBetween('v.fecha', [$fechas['inicio'], $fechas['fin']])
            ->where('v.estado', 1)
            ->groupBy('v.id', 'v.fecha', 'v.estado')
            ->orderBy('v.fecha', 'desc')
            ->get();
    }
    
    /**
     * Detalles de comisión de mecánico
     */
    private function detalleComisionMecanico($mecanicoId, $fechas)
    {
        return DB::table('detalle_ventas as dv')
            ->join('ventas as v', 'dv.venta_id', '=', 'v.id')
            ->join('articulos as a', 'dv.articulo_id', '=', 'a.id')
            ->select(
                'v.id as venta_id',
                'v.fecha',
                'a.nombre as servicio',
                'dv.cantidad',
                'a.costo_mecanico',
                DB::raw('(a.costo_mecanico * dv.cantidad) as comision')
            )
            ->where('a.mecanico_id', $mecanicoId)
            ->whereBetween('v.fecha', [$fechas['inicio'], $fechas['fin']])
            ->where('v.estado', 1)
            ->where('a.tipo', 'servicio')
            ->orderBy('v.fecha', 'desc')
            ->get();
    }
    
    /**
     * Detalles de comisión de carwash
     */
    private function detalleComisionCarwash($carwashId, $fechas)
    {
        return DB::table('trabajador_detalle_venta as tdv')
            ->join('detalle_ventas as dv', 'tdv.detalle_venta_id', '=', 'dv.id')
            ->join('ventas as v', 'dv.venta_id', '=', 'v.id')
            ->join('articulos as a', 'dv.articulo_id', '=', 'a.id')
            ->select(
                'v.id as venta_id',
                'v.fecha',
                'a.nombre as servicio',
                'tdv.monto_comision as comision',
                DB::raw('"carwash" as tipo_trabajo')
            )
            ->where('tdv.trabajador_id', $carwashId)
            ->whereBetween('v.fecha', [$fechas['inicio'], $fechas['fin']])
            ->where('v.estado', 1)
            ->orderBy('v.fecha', 'desc')
            ->get();
    }

    /**
     * Procesar y guardar comisiones de vendedores por metas
     */
    public function procesarComisionesVendedores(Request $request)
    {
        $periodoMeta = $request->get('periodo_meta', 'mensual');
        $fechas = $this->ajustarFechasPorPeriodoMeta(
            $this->calcularPeriodo('mes_actual'), 
            $periodoMeta
        );
        
        $comisionesCalculadas = $this->calcularComisionesVendedores($fechas, $periodoMeta);
        $procesadas = 0;
        $actualizadas = 0;
        
        DB::beginTransaction();
        
        try {
            foreach ($comisionesCalculadas as $comisionData) {
                if ($comisionData->comision_calculada > 0) {
                    $existente = Comision::where('commissionable_type', 'App\Models\User')
                        ->where('commissionable_id', $comisionData->id)
                        ->where('tipo_comision', 'venta_meta')
                        ->whereBetween('fecha_calculo', [$fechas['inicio'], $fechas['fin']])
                        ->first();
                    
                    if ($existente) {
                        // Actualizar comisión existente
                        $existente->update([
                            'monto' => $comisionData->comision_calculada,
                            'porcentaje' => $comisionData->porcentaje_aplicado,
                            'estado' => 'calculado'
                        ]);
                        $actualizadas++;
                    } else {
                        // Crear nueva comisión
                        Comision::create([
                            'commissionable_type' => 'App\Models\User',
                            'commissionable_id' => $comisionData->id,
                            'tipo_comision' => 'venta_meta',
                            'monto' => $comisionData->comision_calculada,
                            'porcentaje' => $comisionData->porcentaje_aplicado,
                            'fecha_calculo' => Carbon::now(),
                            'estado' => 'calculado'
                        ]);
                        $procesadas++;
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', 
                "Comisiones procesadas exitosamente: {$procesadas} nuevas, {$actualizadas} actualizadas."
            );
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 
                'Error al procesar comisiones: ' . $e->getMessage()
            );
        }
    }

    /**
     * Pagar una comisión individual - Ahora crea un lote con 1 comisión
     */
    public function pagarIndividual(Request $request)
    {
        try {
            DB::beginTransaction();

            $comision = Comision::findOrFail($request->comision_id);
            
            if ($comision->estado === 'pagado') {
                return response()->json([
                    'success' => false,
                    'message' => 'La comisión ya está pagada'
                ]);
            }

            // Crear el lote de pago para esta comisión individual
            $lotePago = LotePago::create([
                'numero_lote' => LotePago::generarNumeroLote(),
                'fecha_pago' => now(),
                'metodo_pago' => 'efectivo', // Valor por defecto, se puede editar después
                'referencia' => null,
                'comprobante_imagen' => null,
                'monto_total' => $comision->monto,
                'cantidad_comisiones' => 1,
                'estado' => 'completado',
                'usuario_id' => auth()->id(),
            ]);

            // Crear registro de pago asociado al lote
            PagoComision::create([
                'comision_id' => $comision->id,
                'lote_pago_id' => $lotePago->id,
                'monto' => $comision->monto,
                'metodo_pago' => 'efectivo',
                'fecha_pago' => now(),
                'referencia' => null,
                'observaciones' => 'Pago individual desde gestión de comisiones',
                'usuario_id' => auth()->id(),
                'estado' => 'completado'
            ]);

            // Actualizar estado de la comisión
            $comision->update([
                'estado' => 'pagado',
                'fecha_pago' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Comisión pagada exitosamente',
                'lote_id' => $lotePago->id,
                'redirect_url' => route('lotes-pago.show', $lotePago->id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Pagar múltiples comisiones - Ahora crea un lote con N comisiones
     */
    public function pagarMultiples(Request $request)
    {
        try {
            DB::beginTransaction();

            $cantidadProcesadas = 0;
            $comisionIds = [];

            if ($request->tipo === 'todas' && $request->comision_ids) {
                $comisionIds = $request->comision_ids;
            } elseif ($request->tipo === 'trabajadores' && $request->trabajador_ids) {
                // Obtener comisiones pendientes de los trabajadores seleccionados
                $comisionIds = Comision::whereHas('trabajador', function($q) use ($request) {
                    $q->whereIn('id', $request->trabajador_ids);
                })->where('estado', 'pendiente')->pluck('id')->toArray();
            } elseif ($request->tipo === 'vendedores' && $request->vendedor_ids) {
                // Obtener comisiones pendientes de los vendedores seleccionados
                $comisionIds = Comision::where('commissionable_type', 'App\\Models\\User')
                    ->whereIn('commissionable_id', $request->vendedor_ids)
                    ->where('estado', 'pendiente')
                    ->pluck('id')->toArray();
            }

            if (empty($comisionIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron comisiones para procesar'
                ]);
            }

            // Obtener las comisiones y calcular totales
            $comisiones = Comision::whereIn('id', $comisionIds)->where('estado', 'pendiente')->get();
            $montoTotal = $comisiones->sum('monto');
            $cantidadComisiones = $comisiones->count();

            if ($cantidadComisiones === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron comisiones pendientes para procesar'
                ]);
            }

            // Crear el lote de pago para estas comisiones múltiples
            $lotePago = LotePago::create([
                'numero_lote' => LotePago::generarNumeroLote(),
                'fecha_pago' => now(),
                'metodo_pago' => 'efectivo', // Valor por defecto, se puede editar después
                'referencia' => null,
                'comprobante_imagen' => null,
                'monto_total' => $montoTotal,
                'cantidad_comisiones' => $cantidadComisiones,
                'estado' => 'completado',
                'usuario_id' => auth()->id(),
            ]);

            // Crear los pagos individuales asociados al lote
            foreach ($comisiones as $comision) {
                PagoComision::create([
                    'comision_id' => $comision->id,
                    'lote_pago_id' => $lotePago->id,
                    'monto' => $comision->monto,
                    'metodo_pago' => 'efectivo',
                    'fecha_pago' => now(),
                    'referencia' => null,
                    'observaciones' => 'Pago múltiple desde gestión de comisiones',
                    'usuario_id' => auth()->id(),
                    'estado' => 'completado'
                ]);

                // Actualizar estado de la comisión
                $comision->update([
                    'estado' => 'pagado',
                    'fecha_pago' => now()
                ]);

                $cantidadProcesadas++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Se procesaron {$cantidadProcesadas} comisiones exitosamente",
                'cantidad_procesadas' => $cantidadProcesadas,
                'monto_total' => $montoTotal,
                'lote_id' => $lotePago->id,
                'redirect_url' => route('lotes-pago.show', $lotePago->id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar los pagos: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Pagar todas las comisiones pendientes de un trabajador - Ahora crea un lote
     */
    public function pagarTrabajador(Request $request)
    {
        try {
            DB::beginTransaction();

            $comisiones = Comision::whereHas('trabajador', function($q) use ($request) {
                $q->where('id', $request->trabajador_id);
            })->where('estado', 'pendiente')->get();

            $cantidadProcesadas = 0;
            $montoTotal = 0;

            if ($comisiones->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron comisiones pendientes para este trabajador'
                ]);
            }

            $montoTotal = $comisiones->sum('monto');
            $cantidadComisiones = $comisiones->count();

            // Obtener el nombre del trabajador para referencia
            $trabajador = \App\Models\Trabajador::find($request->trabajador_id);
            $nombreTrabajador = $trabajador ? $trabajador->nombre : 'Trabajador';

            // Crear el lote de pago para este trabajador
            $lotePago = LotePago::create([
                'numero_lote' => LotePago::generarNumeroLote(),
                'fecha_pago' => now(),
                'metodo_pago' => 'efectivo', // Valor por defecto
                'referencia' => "Pago completo - {$nombreTrabajador}",
                'comprobante_imagen' => null,
                'monto_total' => $montoTotal,
                'cantidad_comisiones' => $cantidadComisiones,
                'estado' => 'completado',
                'usuario_id' => auth()->id(),
            ]);

            foreach ($comisiones as $comision) {
                // Crear registro de pago asociado al lote
                PagoComision::create([
                    'comision_id' => $comision->id,
                    'lote_pago_id' => $lotePago->id,
                    'monto' => $comision->monto,
                    'fecha_pago' => now(),
                    'metodo_pago' => 'efectivo',
                    'referencia' => "Pago completo - {$nombreTrabajador}",
                    'observaciones' => 'Pago por trabajador desde gestión consolidada',
                    'usuario_id' => auth()->id(),
                    'estado' => 'completado'
                ]);

                // Actualizar estado de la comisión
                $comision->update([
                    'estado' => 'pagado',
                    'fecha_pago' => now()
                ]);

                $cantidadProcesadas++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Se pagaron {$cantidadProcesadas} comisiones por un total de Q " . number_format($montoTotal, 2),
                'cantidad_procesadas' => $cantidadProcesadas,
                'monto_total' => $montoTotal,
                'lote_id' => $lotePago->id,
                'redirect_url' => route('lotes-pago.show', $lotePago->id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Pagar todas las comisiones pendientes de un vendedor - Ahora crea un lote
     */
    public function pagarVendedor(Request $request)
    {
        try {
            DB::beginTransaction();

            $comisiones = Comision::where('commissionable_type', 'App\\Models\\User')
                ->where('commissionable_id', $request->vendedor_id)
                ->where('estado', 'pendiente')
                ->get();

            $cantidadProcesadas = 0;
            $montoTotal = 0;

            if ($comisiones->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron comisiones pendientes para este vendedor'
                ]);
            }

            $montoTotal = $comisiones->sum('monto');
            $cantidadComisiones = $comisiones->count();

            // Obtener el nombre del vendedor para referencia
            $vendedor = \App\Models\User::find($request->vendedor_id);
            $nombreVendedor = $vendedor ? $vendedor->name : 'Vendedor';

            // Crear el lote de pago para este vendedor
            $lotePago = LotePago::create([
                'numero_lote' => LotePago::generarNumeroLote(),
                'fecha_pago' => now(),
                'metodo_pago' => 'efectivo', // Valor por defecto
                'referencia' => "Pago completo - {$nombreVendedor}",
                'comprobante_imagen' => null,
                'monto_total' => $montoTotal,
                'cantidad_comisiones' => $cantidadComisiones,
                'estado' => 'completado',
                'usuario_id' => auth()->id(),
            ]);

            foreach ($comisiones as $comision) {
                // Crear registro de pago asociado al lote
                PagoComision::create([
                    'comision_id' => $comision->id,
                    'lote_pago_id' => $lotePago->id,
                    'monto' => $comision->monto,
                    'fecha_pago' => now(),
                    'metodo_pago' => 'efectivo',
                    'referencia' => "Pago completo - {$nombreVendedor}",
                    'observaciones' => 'Pago por vendedor desde gestión consolidada',
                    'usuario_id' => auth()->id(),
                    'estado' => 'completado'
                ]);

                // Actualizar estado de la comisión
                $comision->update([
                    'estado' => 'pagado',
                    'fecha_pago' => now()
                ]);

                $cantidadProcesadas++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Se pagaron {$cantidadProcesadas} comisiones por un total de Q " . number_format($montoTotal, 2),
                'cantidad_procesadas' => $cantidadProcesadas,
                'monto_total' => $montoTotal,
                'lote_id' => $lotePago->id,
                'redirect_url' => route('lotes-pago.show', $lotePago->id)
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener detalles de meta de ventas para modal
     */
    public function detallesMeta($id)
    {
        try {
            $comision = Comision::with(['commissionable'])->findOrFail($id);
            
            if ($comision->tipo_comision !== 'meta_venta') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta comisión no es de tipo meta de ventas'
                ]);
            }

            // Obtener información de la meta y ventas
            $resumenMeta = $comision->info_meta_resumen;
            $ventas = $comision->ventas_de_meta;
            $config = Config::first();
            
            $html = view('admin.comisiones.detalles-meta', compact('comision', 'resumenMeta', 'ventas', 'config'))->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar los detalles: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generar PDF del listado general de comisiones
     */
    public function generarPDFListado(Request $request)
    {
        $config = Config::first();
        
        // Obtener comisiones con filtros aplicados
        $query = Comision::with(['commissionable', 'venta', 'detalleVenta', 'articulo', 'pagos.lotePago']);
        $this->aplicarFiltrosAvanzados($query, $request);
        $comisiones = $query->orderBy('fecha_calculo', 'desc')->get();
        
        // Calcular estadísticas
        $totalComisiones = $comisiones->sum('monto');
        $cantidadComisiones = $comisiones->count();
        $pendientes = $comisiones->where('estado', 'pendiente')->count();
        $pagadas = $comisiones->where('estado', 'pagado')->count();
        $canceladas = $comisiones->where('estado', 'cancelado')->count();
        
        // Obtener información de filtros aplicados
        $filtrosAplicados = $this->obtenerTextoFiltrosAplicados($request);
        
        // Transformar comisiones para la vista
        $comisionesTransformadas = $comisiones->map(function ($comision) use ($config) {
            return [
                'id' => $comision->id,
                'beneficiario_nombre' => $this->obtenerNombreBeneficiario($comision),
                'tipo_receptor' => $this->obtenerTipoReceptor($comision),
                'tipo_comision' => $comision->tipo_comision,
                'tipo_comision_texto' => $this->obtenerTextoTipoComision($comision->tipo_comision),
                'meta_info' => $this->obtenerInfoMeta($comision, $config),
                'monto' => $comision->monto,
                'estado' => $comision->estado,
                'fecha_calculo' => $comision->fecha_calculo,
                'venta_id' => $comision->venta_id,
                'fecha_venta' => $comision->venta ? $comision->venta->fecha : null,
            ];
        });
        
        $data = [
            'config' => $config,
            'comisiones' => $comisionesTransformadas,
            'totalComisiones' => $totalComisiones,
            'cantidadComisiones' => $cantidadComisiones,
            'pendientes' => $pendientes,
            'pagadas' => $pagadas,
            'canceladas' => $canceladas,
            'filtrosAplicados' => $filtrosAplicados,
            'fechaGeneracion' => now()->format('d/m/Y H:i:s')
        ];
        
        $pdf = Pdf::loadView('comisiones.pdf.listado-general', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('reporte-comisiones-' . now()->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Generar PDF individual de una comisión específica
     */
    public function generarPDFIndividual($id)
    {
        $config = Config::first();
        $comision = Comision::with(['commissionable', 'venta', 'detalleVenta', 'articulo', 'pagos.lotePago'])
            ->findOrFail($id);
        
        // Obtener información adicional
        $metaInfo = $this->obtenerInfoMeta($comision, $config);
        $beneficiario = $this->obtenerNombreBeneficiario($comision);
        $tipoReceptor = $this->obtenerTipoReceptor($comision);
        $tipoComisionTexto = $this->obtenerTextoTipoComision($comision->tipo_comision);
        
        // Información del lote de pago si existe
        $loteInfo = null;
        $pagoCompleto = $comision->pagos()->where('estado', 'completado')->first();
        if ($pagoCompleto && $pagoCompleto->lotePago) {
            $loteInfo = [
                'id' => $pagoCompleto->lotePago->id,
                'numero_lote' => $pagoCompleto->lotePago->numero_lote,
                'fecha_pago' => $pagoCompleto->lotePago->fecha_pago,
                'metodo_pago' => $pagoCompleto->lotePago->metodo_pago,
                'referencia' => $pagoCompleto->lotePago->referencia
            ];
        }
        
        $data = [
            'config' => $config,
            'comision' => $comision,
            'metaInfo' => $metaInfo,
            'beneficiario' => $beneficiario,
            'tipoReceptor' => $tipoReceptor,
            'tipoComisionTexto' => $tipoComisionTexto,
            'loteInfo' => $loteInfo,
            'fechaGeneracion' => now()->format('d/m/Y H:i:s')
        ];
        
        $pdf = Pdf::loadView('comisiones.pdf.individual', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('comision-' . $comision->id . '-' . now()->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Obtener texto descriptivo de los filtros aplicados
     */
    private function obtenerTextoFiltrosAplicados(Request $request)
    {
        $filtros = [];
        
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $filtros[] = 'Fechas: ' . Carbon::parse($request->fecha_inicio)->format('d/m/Y') . ' - ' . Carbon::parse($request->fecha_fin)->format('d/m/Y');
        } elseif ($request->filled('tipo_periodo')) {
            $periodos = [
                'hoy' => 'Hoy',
                'ayer' => 'Ayer',
                'semana_actual' => 'Semana actual',
                'mes_actual' => 'Mes actual',
                'mes_anterior' => 'Mes anterior',
                'trimestre_actual' => 'Trimestre actual',
                'ano_actual' => 'Año actual'
            ];
            if (isset($periodos[$request->tipo_periodo])) {
                $filtros[] = 'Período: ' . $periodos[$request->tipo_periodo];
            }
        }
        
        if ($request->filled('estado')) {
            $filtros[] = 'Estado: ' . ucfirst($request->estado);
        }
        
        if ($request->filled('tipo_comision')) {
            $tipos = [
                'mecanico' => 'Mecánico',
                'carwash' => 'Car Wash',
                'venta_meta' => 'Meta de Ventas',
                'meta_venta' => 'Meta de Ventas'
            ];
            $filtros[] = 'Tipo: ' . ($tipos[$request->tipo_comision] ?? ucfirst($request->tipo_comision));
        }
        
        if ($request->filled('trabajador_id')) {
            $trabajador = Trabajador::find($request->trabajador_id);
            if ($trabajador) {
                $filtros[] = 'Trabajador: ' . $trabajador->nombre;
            }
        }
        
        if ($request->filled('vendedor_id')) {
            $vendedor = User::find($request->vendedor_id);
            if ($vendedor) {
                $filtros[] = 'Vendedor: ' . $vendedor->name;
            }
        }
        
        if ($request->filled('monto_minimo')) {
            $filtros[] = 'Monto mínimo: $' . number_format($request->monto_minimo, 2);
        }
        
        if ($request->filled('monto_maximo')) {
            $filtros[] = 'Monto máximo: $' . number_format($request->monto_maximo, 2);
        }
        
        return empty($filtros) ? 'Sin filtros aplicados' : implode(' | ', $filtros);
    }

    /**
     * Dashboard específico para vendedores - Solo sus comisiones
     */
    public function dashboardVendedor()
    {
        $usuarioId = auth()->user()->id;
        $config = Config::first();
        
        // Solo comisiones del vendedor actual
        $comisiones = Comision::with(['venta.cliente', 'trabajador', 'metaVenta'])
            ->where('commissionable_type', 'App\\Models\\User')
            ->where('commissionable_id', $usuarioId)
            ->orderBy('fecha_calculo', 'desc')
            ->paginate(15);
        
        // Métricas del vendedor
        $totalComisiones = Comision::where('commissionable_type', 'App\\Models\\User')
            ->where('commissionable_id', $usuarioId)
            ->sum('monto');
            
        $comisionesPendientes = Comision::where('commissionable_type', 'App\\Models\\User')
            ->where('commissionable_id', $usuarioId)
            ->where('estado', 'pendiente')
            ->sum('monto');
            
        $comisionesPagadas = Comision::where('commissionable_type', 'App\\Models\\User')
            ->where('commissionable_id', $usuarioId)
            ->where('estado', 'pagada')
            ->sum('monto');
            
        // Comisiones por mes (últimos 6 meses)
        $comisionesPorMes = [];
        for ($i = 5; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $monto = Comision::where('commissionable_type', 'App\\Models\\User')
                ->where('commissionable_id', $usuarioId)
                ->whereYear('fecha_calculo', $fecha->year)
                ->whereMonth('fecha_calculo', $fecha->month)
                ->sum('monto');
                
            $comisionesPorMes[] = [
                'mes' => $fecha->format('M Y'),
                'monto' => $monto
            ];
        }
        
        return view('admin.comisiones.dashboard-vendedor', compact(
            'config', 'comisiones', 'totalComisiones', 'comisionesPendientes',
            'comisionesPagadas', 'comisionesPorMes'
        ));
    }
}
