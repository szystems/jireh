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
use Barryvdh\DomPDF\Facade\Pdf;

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
        $fechaIteracion = $fechaInicio->copy();
        while ($fechaIteracion->lte($fechaFin)) {
            $fechaKey = $fechaIteracion->format('Y-m-d');
            
            if ($ventasPorFecha->has($fechaKey)) {
                $estadisticasDiarias[$fechaKey] = $ventasPorFecha[$fechaKey];
            } else {
                $estadisticasDiarias[$fechaKey] = [
                    'cantidad' => 0,
                    'total' => 0
                ];
            }
            
            $fechaIteracion->addDay();
        }

        $totalVendidoPeriodo = $ventas->sum('total');
        $cantidadVentas = $ventas->count();
        
        // Obtener TODAS las metas activas para mostrar progreso correcto
        $todasLasMetas = MetaVenta::where('estado', 1)->orderBy('monto_minimo')->get();
        
        // Calcular progreso por cada meta según su tipo específico
        $metasConProgreso = collect();
        foreach($todasLasMetas as $meta) {
            // Calcular ventas según el tipo específico de la meta
            $ventasParaMeta = $this->calcularVentasSegunTipoMeta($trabajador, $meta, $fechaActual);
            
            $alcanzada = $ventasParaMeta >= $meta->monto_minimo;
            $porcentaje = $meta->monto_minimo > 0 ? ($ventasParaMeta / $meta->monto_minimo) * 100 : 0;
            $porcentaje = min($porcentaje, 100);
            
            // Usar los métodos genéricos para colores
            $color = $this->generarColorMeta($meta->id);
            $claseProgreso = $this->generarClaseProgreso($meta->id);
            
            $metaData = [
                'meta' => $meta,
                'ventas_para_meta' => $ventasParaMeta,
                'alcanzada' => $alcanzada,
                'porcentaje' => $porcentaje,
                'color' => $color,
                'clase_progreso' => $claseProgreso,
                'faltante' => max(0, $meta->monto_minimo - $ventasParaMeta)
            ];
            
            $metasConProgreso->push($metaData);
        }
        
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
        // Usar el campo 'periodo' de la meta para determinar el tipo
        $tipoMeta = strtolower($meta->periodo);
        
        if ($tipoMeta === 'mensual' || $tipoMeta === 'mes') {
            // Meta mensual: ventas del mes actual
            $fechaInicio = $fechaActual->copy()->startOfMonth();
            $fechaFin = $fechaActual->copy()->endOfMonth();
        } elseif ($tipoMeta === 'semestral' || $tipoMeta === 'semestre') {
            // Meta semestral: ventas del semestre actual
            $mes = $fechaActual->month;
            if ($mes <= 6) {
                $fechaInicio = $fechaActual->copy()->startOfYear();
                $fechaFin = $fechaActual->copy()->month(6)->endOfMonth();
            } else {
                $fechaInicio = $fechaActual->copy()->month(7)->startOfMonth();
                $fechaFin = $fechaActual->copy()->endOfYear();
            }
        } elseif ($tipoMeta === 'anual' || $tipoMeta === 'año') {
            // Meta anual: ventas del año actual
            $fechaInicio = $fechaActual->copy()->startOfYear();
            $fechaFin = $fechaActual->copy()->endOfYear();
        } else {
            // Por defecto, usar el período anual (es lo más común)
            $fechaInicio = $fechaActual->copy()->startOfYear();
            $fechaFin = $fechaActual->copy()->endOfYear();
        }
        
        // Calcular ventas del período específico de esta meta
        $ventas = $trabajador->ventas()
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', 1)
            ->get();
            
        return $ventas->sum('total');
    }

    public function generarPDFGeneral(Request $request)
    {
        $periodo = $request->get('periodo', 'mes');
        $fechaActual = Carbon::now();
        
        // Obtener datos igual que en el método index
        $metas = MetaVenta::where('estado', 1)->get();
        $config = Config::first();
        
        // Calcular fechas según período
        switch($periodo) {
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
        }

        // Obtener trabajadores con sus datos de metas
        $trabajadores = User::where('role_as', 1)
            ->where('estado', 1)
            ->get()
            ->map(function($trabajador) use ($metas, $fechaActual) {
                $metasConProgreso = collect();
                foreach($metas as $meta) {
                    $ventasParaMeta = $this->calcularVentasSegunTipoMeta($trabajador, $meta, $fechaActual);
                    $porcentaje = $meta->monto_minimo > 0 ? ($ventasParaMeta / $meta->monto_minimo) * 100 : 0;
                    $porcentaje = min($porcentaje, 100);
                    $alcanzada = $ventasParaMeta >= $meta->monto_minimo;
                    $color = $this->generarColorMeta($meta->id);
                    $claseProgreso = $this->generarClaseProgreso($meta->id);
                    
                    $metasConProgreso->push([
                        'meta' => $meta,
                        'ventas_para_meta' => $ventasParaMeta,
                        'porcentaje' => $porcentaje,
                        'alcanzada' => $alcanzada,
                        'color' => $color,
                        'clase_progreso' => $claseProgreso,
                        'faltante' => max(0, $meta->monto_minimo - $ventasParaMeta)
                    ]);
                }
                
                $trabajador->metasConProgreso = $metasConProgreso;
                return $trabajador;
            });

        // Preparar metas con sus clases CSS y progreso promedio
        $metasConClases = $metas->map(function($meta) use ($trabajadores) {
            $meta->clase_progreso = $this->generarClaseProgreso($meta->id);
            
            // Calcular progreso promedio de todos los trabajadores para esta meta
            $progresos = $trabajadores->map(function($trabajador) use ($meta) {
                $metaData = $trabajador->metasConProgreso->first(function($item) use ($meta) {
                    return $item['meta']->id == $meta->id;
                });
                return $metaData ? $metaData['porcentaje'] : 0;
            });
            
            $meta->progreso_promedio = $progresos->avg();
            return $meta;
        });

        // Ordenar trabajadores por total de ventas (de mayor a menor)
        $trabajadoresOrdenados = $trabajadores->sortByDesc(function($trabajador) {
            return $trabajador->metasConProgreso->sum('ventas_para_meta');
        });

        // Preparar datos para el PDF
        $data = [
            'titulo' => 'Reporte de Metas de Ventas',
            'periodo' => $periodo,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'trabajadores' => $trabajadoresOrdenados,
            'metasOriginales' => $metasConClases,
            'config' => $config,
            'logoPath' => $this->obtenerLogoPath($config)
        ];

        $pdf = Pdf::loadView('admin.reportes.pdf.metas-general', $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('reporte-metas-general-' . $periodo . '-' . date('Y-m-d') . '.pdf');
    }

    public function generarPDFTrabajador(Request $request, User $trabajador)
    {
        $periodo = $request->get('periodo', 'año');
        $fechaActual = Carbon::now();
        $config = Config::first();
        
        // Calcular fechas según período seleccionado para estadísticas
        switch($periodo) {
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
        }

        // Obtener datos igual que en trabajadorDetalle
        $todasLasMetas = MetaVenta::where('estado', 1)->get();
        
        $metasConProgreso = $todasLasMetas->map(function($meta) use ($trabajador, $fechaActual) {
            $ventasParaMeta = $this->calcularVentasSegunTipoMeta($trabajador, $meta, $fechaActual);
            $porcentaje = $meta->monto_minimo > 0 ? ($ventasParaMeta / $meta->monto_minimo) * 100 : 0;
            $porcentaje = min($porcentaje, 100);
            $alcanzada = $ventasParaMeta >= $meta->monto_minimo;
            $color = $this->generarColorMeta($meta->id);
            $claseProgreso = $this->generarClaseProgreso($meta->id);
            
            return [
                'meta' => $meta,
                'ventas_para_meta' => $ventasParaMeta,
                'porcentaje' => $porcentaje,
                'alcanzada' => $alcanzada,
                'color' => $color,
                'clase_progreso' => $claseProgreso,
                'faltante' => max(0, $meta->monto_minimo - $ventasParaMeta)
            ];
        });

        // Obtener ventas del período seleccionado
        $ventas = Venta::where('usuario_id', $trabajador->id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', 1)
            ->with(['cliente', 'detalleVentas.articulo'])
            ->orderBy('fecha', 'desc')
            ->get();

        $totalVendidoPeriodo = $ventas->sum(function($venta) {
            return $venta->detalleVentas->sum('sub_total');
        });
        $cantidadVentas = $ventas->count();

        // Preparar datos para el PDF
        $data = [
            'titulo' => 'Reporte Individual de Metas',
            'trabajador' => $trabajador,
            'periodo' => $periodo,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'metasConProgreso' => $metasConProgreso,
            'ventas' => $ventas,
            'totalVentas' => $totalVendidoPeriodo,
            'config' => $config,
            'logoPath' => $this->obtenerLogoPath($config)
        ];

        $pdf = Pdf::loadView('admin.reportes.pdf.trabajador-detalle', $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('reporte-metas-' . $trabajador->name . '-' . $periodo . '-' . date('Y-m-d') . '.pdf');
    }

    private function obtenerLogoPath($config)
    {
        if ($config && $config->logo) {
            $logoConfigPath = public_path('img/' . $config->logo);
            if (file_exists($logoConfigPath)) {
                return $logoConfigPath;
            }
        }
        
        // Logo por defecto
        $logoDefaultPath = public_path('img/jireh.png');
        if (file_exists($logoDefaultPath)) {
            return $logoDefaultPath;
        }
        
        return null;
    }
}
