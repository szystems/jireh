<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Venta;
use App\Models\MetaVenta;
use App\Models\Config;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteMetasController extends Controller
{
    /**
     * Generar un color consistente basado en el ID de la meta
     */
    private function generarColorMeta($metaId)
    {
        $colores = [
            'primary',   // Azul
            'success',   // Verde  
            'warning',   // Amarillo/Naranja
            'info',      // Cian
            'secondary', // Gris
            'danger',    // Rojo
            'dark'       // Negro
        ];
        
        // Usar el ID para generar un índice consistente
        $indice = $metaId % count($colores);
        return $colores[$indice];
    }

    /**
     * Generar una clase CSS de progreso basada en el ID de la meta
     */
    private function generarClaseProgreso($metaId)
    {
        $clases = [
            'progress-primary',
            'progress-success', 
            'progress-warning',
            'progress-info',
            'progress-secondary',
            'progress-danger',
            'progress-dark'
        ];
        
        // Usar el ID para generar un índice consistente
        $indice = $metaId % count($clases);
        return $clases[$indice];
    }
    public function index(Request $request)
    {
        $periodo = $request->get('periodo', 'mes'); // mes, trimestre, semestre, año
        $fechaActual = Carbon::now();
        
        // Obtener configuración de moneda
        $config = Config::first();
        
        // Definir fechas según el período
        switch ($periodo) {
            case 'trimestre':
                $fechaInicio = $fechaActual->copy()->startOfQuarter();
                $fechaFin = $fechaActual->copy()->endOfQuarter();
                $tipoMetaBuscado = 'trimestral';
                break;
            case 'semestre':
                $mes = $fechaActual->month;
                if ($mes <= 6) {
                    $fechaInicio = $fechaActual->copy()->startOfYear();
                    $fechaFin = $fechaActual->copy()->month(6)->endOfMonth();
                } else {
                    $fechaInicio = $fechaActual->copy()->month(7)->startOfMonth();
                    $fechaFin = $fechaActual->copy()->endOfYear();
                }
                $tipoMetaBuscado = 'semestral';
                break;
            case 'año':
                $fechaInicio = $fechaActual->copy()->startOfYear();
                $fechaFin = $fechaActual->copy()->endOfYear();
                $tipoMetaBuscado = 'anual';
                break;
            default: // mes
                $fechaInicio = $fechaActual->copy()->startOfMonth();
                $fechaFin = $fechaActual->copy()->endOfMonth();
                $tipoMetaBuscado = 'mensual';
                break;
        }

        // Obtener TODAS las metas activas para mostrar en la cabecera
        $todasLasMetas = MetaVenta::where('estado', 1)->orderBy('monto_minimo')->get();
        
        // Obtener metas específicas del período para los cálculos de progreso
        $metasDelPeriodo = MetaVenta::where('estado', 1)
            ->where(function($query) use ($tipoMetaBuscado) {
                $query->where('nombre', 'like', "%{$tipoMetaBuscado}%")
                      ->orWhere('periodo', $tipoMetaBuscado);
            })
            ->orderBy('monto_minimo')
            ->get();
        
        // Si no hay metas específicas del período, usar todas las metas activas para cálculos
        if ($metasDelPeriodo->isEmpty()) {
            $metasDelPeriodo = $todasLasMetas;
        }

        // Obtener TODOS los trabajadores (vendedores) del sistema
        $trabajadores = User::where('role_as', 1) // Solo trabajadores
            ->get()
            ->map(function($trabajador) use ($fechaInicio, $fechaFin, $periodo, $metasDelPeriodo, $todasLasMetas, $fechaActual) {
                // Obtener ventas del período
                $ventas = $trabajador->ventas()
                    ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                    ->where('estado', 1)
                    ->with('detalleVentas')
                    ->get();
                
                // Calcular totales de ventas
                $totalVendido = $ventas->sum('total');
                $cantidadVentas = $ventas->count();
                
                // Determinar meta actual basado en el TIPO de cada meta (no del período filtrado)
                // Cada meta debe evaluarse según su propio período
                $metaActual = null;
                $metaSiguiente = null;
                $porcentajeProgreso = 0;
                
                // Evaluar cada meta según su tipo específico
                foreach ($todasLasMetas->sortBy('monto_minimo') as $meta) {
                    // Calcular ventas según el tipo de meta
                    $ventasParaMeta = $this->calcularVentasSegunTipoMeta($trabajador, $meta, $fechaActual);
                    
                    if ($ventasParaMeta >= $meta->monto_minimo) {
                        $metaActual = $meta;
                    } else {
                        if (!$metaSiguiente) {
                            $metaSiguiente = $meta;
                        }
                    }
                }
                
                // Si no tiene meta actual, la siguiente es la primera de todas las metas
                if (!$metaActual && $todasLasMetas->count() > 0) {
                    $metaSiguiente = $todasLasMetas->sortBy('monto_minimo')->first();
                }
                
                // Calcular porcentaje de progreso hacia la siguiente meta
                if ($metaSiguiente) {
                    $porcentajeProgreso = ($totalVendido / $metaSiguiente->monto_minimo) * 100;
                    $porcentajeProgreso = min($porcentajeProgreso, 100);
                }
                
                // Calcular promedio por día
                $diasTranscurridos = $fechaInicio->diffInDays(Carbon::now()) + 1;
                $promedioDiario = $diasTranscurridos > 0 ? $totalVendido / $diasTranscurridos : 0;
                
                // Proyección del período completo
                $totalDiasPeriodo = $fechaInicio->diffInDays($fechaFin) + 1;
                $proyeccionTotal = $promedioDiario * $totalDiasPeriodo;

                return [
                    'trabajador' => $trabajador,
                    'total_vendido' => $totalVendido,
                    'cantidad_ventas' => $cantidadVentas,
                    'meta_actual' => $metaActual,
                    'meta_siguiente' => $metaSiguiente,
                    'porcentaje_progreso' => $porcentajeProgreso,
                    'promedio_diario' => $promedioDiario,
                    'proyeccion_total' => $proyeccionTotal,
                    'dias_transcurridos' => $diasTranscurridos,
                    'total_dias_periodo' => $totalDiasPeriodo
                ];
            })
            ->sortByDesc('total_vendido');

        // Calcular el máximo de ventas para la barra comparativa
        $maxVentas = $trabajadores->max('total_vendido');
        
        return view('admin.reportes.metas-ventas', [
            'trabajadores' => $trabajadores,
            'periodo' => $periodo,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'metas' => $todasLasMetas, // Para la cabecera, mostramos todas las metas
            'metasDelPeriodo' => $metasDelPeriodo, // Para los cálculos específicos del período
            'config' => $config,
            'maxVentas' => $maxVentas
        ]);
    }

    public function trabajadorDetalle($trabajadorId, Request $request)
    {
        $periodo = $request->get('periodo', 'mes');
        $fechaActual = Carbon::now();
        
        // Obtener configuración de moneda
        $config = Config::first();

        // Definir fechas según el período (para mostrar las ventas del período)
        switch ($periodo) {
            case 'trimestre':
                $fechaInicio = $fechaActual->copy()->startOfQuarter();
                $fechaFin = $fechaActual->copy()->endOfQuarter();
                break;
            case 'semestre':
                $mes = $fechaActual->month;
                if ($mes <= 6) {
                    $fechaInicio = $fechaActual->copy()->startOfYear();
                    $fechaFin = $fechaActual->copy()->month(6)->endOfMonth();
                } else {
                    $fechaInicio = $fechaActual->copy()->month(7)->startOfMonth();
                    $fechaFin = $fechaActual->copy()->endOfYear();
                }
                break;
            case 'año':
                $fechaInicio = $fechaActual->copy()->startOfYear();
                $fechaFin = $fechaActual->copy()->endOfYear();
                break;
            default: // mes
                $fechaInicio = $fechaActual->copy()->startOfMonth();
                $fechaFin = $fechaActual->copy()->endOfMonth();
                break;
        }

        $trabajador = User::findOrFail($trabajadorId);
        
        // Obtener ventas del trabajador en el período (para mostrar)
        $ventas = Venta::where('usuario_id', $trabajadorId)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', 1)
            ->with(['cliente', 'detalleVentas.articulo'])
            ->orderBy('fecha', 'desc')
            ->get();

        // Calcular estadísticas por día para TODO EL AÑO (más contexto histórico)
        $ventasAnuales = Venta::where('usuario_id', $trabajador->id)
            ->whereYear('fecha', Carbon::now()->year)
            ->orderBy('fecha', 'asc')
            ->get();

        // Crear array completo del año con ceros para días sin ventas
        $añoActual = Carbon::now()->year;
        $fechaInicio = Carbon::create($añoActual, 1, 1);
        $fechaFin = Carbon::create($añoActual, 12, 31);
        
        $estadisticasDiarias = collect();
        
        // Agrupar ventas reales por fecha
        $ventasPorFecha = $ventasAnuales->groupBy(function($venta) {
            return Carbon::parse($venta->fecha)->format('Y-m-d');
        })->map(function($ventasDelDia) {
            return [
                'cantidad' => $ventasDelDia->count(),
                'total' => $ventasDelDia->sum(function($venta) {
                    // Usar el sub_total que ya está calculado en cada detalle
                    return $venta->detalleVentas->sum('sub_total');
                })
            ];
        });
        
        // Llenar todo el año, día por día
        $fechaActual = $fechaInicio->copy();
        while ($fechaActual->lte($fechaFin)) {
            $fechaKey = $fechaActual->format('Y-m-d');
            
            if ($ventasPorFecha->has($fechaKey)) {
                $estadisticasDiarias[$fechaKey] = $ventasPorFecha[$fechaKey];
            } else {
                $estadisticasDiarias[$fechaKey] = [
                    'cantidad' => 0,
                    'total' => 0
                ];
            }
            
            $fechaActual->addDay();
        }

        $totalVendidoPeriodo = $ventas->sum('total');
        $cantidadVentas = $ventas->count();
        
        // Obtener TODAS las metas activas para mostrar progreso correcto
        $todasLasMetas = MetaVenta::where('estado', 1)->orderBy('monto_minimo')->get();
        
        // Calcular progreso por cada meta según su tipo específico
        $metasConProgreso = $todasLasMetas->map(function($meta) use ($trabajador, $fechaActual) {
            // Calcular ventas según el tipo específico de la meta
            $ventasParaMeta = $this->calcularVentasSegunTipoMeta($trabajador, $meta, $fechaActual);
            
            $alcanzada = $ventasParaMeta >= $meta->monto_minimo;
            $porcentaje = ($ventasParaMeta / $meta->monto_minimo) * 100;
            $porcentaje = min($porcentaje, 100);
            
            // Usar los métodos genéricos para colores
            $color = $this->generarColorMeta($meta->id);
            $claseProgreso = $this->generarClaseProgreso($meta->id);
            
            return [
                'meta' => $meta,
                'ventas_para_meta' => $ventasParaMeta,
                'alcanzada' => $alcanzada,
                'porcentaje' => $porcentaje,
                'color' => $color,
                'clase_progreso' => $claseProgreso,
                'faltante' => $meta->monto_minimo - $ventasParaMeta
            ];
        });
        
        return view('admin.reportes.trabajador-detalle', compact(
            'trabajador',
            'ventas',
            'estadisticasDiarias',
            'totalVendidoPeriodo',
            'cantidadVentas',
            'todasLasMetas',
            'metasConProgreso',
            'periodo',
            'fechaInicio',
            'fechaFin',
            'config'
        ));
    }

    /**
     * Calcular ventas de un trabajador según el tipo específico de meta
     */
    private function calcularVentasSegunTipoMeta($trabajador, $meta, $fechaActual)
    {
        // Determinar el período según el tipo de meta
        $tipoMeta = strtolower($meta->nombre);
        
        if (strpos($tipoMeta, 'mensual') !== false || strpos($tipoMeta, 'mes') !== false) {
            // Meta mensual: ventas del mes actual
            $fechaInicio = $fechaActual->copy()->startOfMonth();
            $fechaFin = $fechaActual->copy()->endOfMonth();
        } elseif (strpos($tipoMeta, 'semestral') !== false || strpos($tipoMeta, 'semestre') !== false) {
            // Meta semestral: ventas del semestre actual
            $mes = $fechaActual->month;
            if ($mes <= 6) {
                $fechaInicio = $fechaActual->copy()->startOfYear();
                $fechaFin = $fechaActual->copy()->month(6)->endOfMonth();
            } else {
                $fechaInicio = $fechaActual->copy()->month(7)->startOfMonth();
                $fechaFin = $fechaActual->copy()->endOfYear();
            }
        } elseif (strpos($tipoMeta, 'anual') !== false || strpos($tipoMeta, 'año') !== false) {
            // Meta anual: ventas del año actual
            $fechaInicio = $fechaActual->copy()->startOfYear();
            $fechaFin = $fechaActual->copy()->endOfYear();
        } else {
            // Por defecto, usar el período mensual
            $fechaInicio = $fechaActual->copy()->startOfMonth();
            $fechaFin = $fechaActual->copy()->endOfMonth();
        }
        
        // Calcular ventas del período específico de esta meta
        $ventas = $trabajador->ventas()
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', 1)
            ->get();
            
        return $ventas->sum('total');
    }
}
