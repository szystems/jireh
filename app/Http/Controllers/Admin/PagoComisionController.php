<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comision;
use App\Models\PagoComision;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PagoComisionController extends Controller
{
    /**
     * Mostrar dashboard de pagos de comisiones
     */
    public function index(Request $request)
    {
        $periodo = $request->get('periodo', 'mes_actual');
        $tipoComision = $request->get('tipo', 'todas');
        $estado = $request->get('estado', 'todas');
        
        // Calcular fechas según período
        $fechas = $this->calcularPeriodo($periodo);
        
        // Obtener comisiones pendientes y parciales para pagos
        $query = Comision::with(['commissionable', 'pagos'])
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->whereBetween('fecha_calculo', [$fechas['inicio'], $fechas['fin']]);
            
        // Filtrar por tipo de comisión
        if ($tipoComision !== 'todas') {
            switch($tipoComision) {
                case 'vendedores':
                    $query->where('commissionable_type', 'App\Models\User');
                    break;
                case 'trabajadores':
                    $query->where('commissionable_type', 'App\Models\Trabajador');
                    break;
                default:
                    $query->where('tipo_comision', $tipoComision);
            }
        }
        
        $comisionesPendientes = $query->orderBy('fecha_calculo', 'desc')->get();
        
        // Estadísticas del período
        $estadisticas = $this->obtenerEstadisticas($fechas, $tipoComision);
        
        return view('admin.pagos_comisiones.index', compact(
            'comisionesPendientes', 'estadisticas', 'fechas',
            'periodo', 'tipoComision', 'estado'
        ));
    }
    
    /**
     * Procesar pagos masivos mensuales
     */
    public function procesarPagosMasivos(Request $request)
    {
        $request->validate([
            'comisiones' => 'required|array|min:1',
            'comisiones.*' => 'exists:comisiones,id',
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|in:efectivo,transferencia,cheque,otro',
            'observaciones' => 'nullable|string|max:500'
        ]);
        
        DB::beginTransaction();
        
        try {
            $comisionesIds = $request->comisiones;
            $fechaPago = Carbon::parse($request->fecha_pago);
            $totalPagado = 0;
            $pagosProcesados = 0;
            
            foreach ($comisionesIds as $comisionId) {
                $comision = Comision::findOrFail($comisionId);
                $montoPendiente = $comision->montoPendiente();
                
                if ($montoPendiente > 0) {
                    // Crear pago de comisión
                    $pago = PagoComision::create([
                        'comision_id' => $comision->id,
                        'monto' => $montoPendiente,
                        'metodo_pago' => $request->metodo_pago,
                        'usuario_id' => Auth::id(),
                        'fecha_pago' => $fechaPago,
                        'observaciones' => $request->observaciones . " - Pago masivo procesado",
                        'estado' => 'completado'
                    ]);
                    
                    if ($pago) {
                        // Actualizar estado de comisión
                        $comision->actualizarEstado();
                        $totalPagado += $montoPendiente;
                        $pagosProcesados++;
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', 
                "Pagos procesados exitosamente: {$pagosProcesados} comisiones pagadas por un total de Q" . number_format($totalPagado, 2)
            );
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 
                'Error al procesar pagos masivos: ' . $e->getMessage()
            );
        }
    }
    
    /**
     * Registrar pago individual de comisión
     */
    public function registrarPago(Request $request, $id)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,transferencia,cheque,otro',
            'fecha_pago' => 'required|date',
            'referencia' => 'nullable|string|max:100',
            'comprobante_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $comision = Comision::findOrFail($id);

        // Verificar que el monto a pagar no exceda el pendiente
        $montoPendiente = $comision->montoPendiente();
        if ($request->monto > $montoPendiente) {
            return back()->with('error', 
                "El monto máximo a pagar es de Q" . number_format($montoPendiente, 2)
            );
        }

        DB::beginTransaction();
        
        try {
            // Manejar archivo de comprobante si existe
            $rutaComprobante = null;
            if ($request->hasFile('comprobante_imagen')) {
                $rutaComprobante = $this->guardarComprobante($request->file('comprobante_imagen'));
            }
            
            // Registrar el pago
            $pago = PagoComision::create([
                'comision_id' => $comision->id,
                'monto' => $request->monto,
                'metodo_pago' => $request->metodo_pago,
                'usuario_id' => Auth::id(),
                'fecha_pago' => $request->fecha_pago,
                'referencia' => $request->referencia,
                'comprobante_imagen' => $rutaComprobante,
                'observaciones' => $request->observaciones,
                'estado' => 'completado'
            ]);

            // Actualizar el estado de la comisión
            $comision->actualizarEstado();
            
            DB::commit();

            return redirect()->back()->with('success', 
                'Pago registrado correctamente por Q' . number_format($request->monto, 2)
            );
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 
                'Error al registrar pago: ' . $e->getMessage()
            );
        }
    }
    
    /**
     * Ver historial de pagos
     */
    public function historial(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ? 
            Carbon::parse($request->fecha_inicio)->startOfDay() : 
            Carbon::now()->startOfMonth();
            
        $fechaFin = $request->fecha_fin ? 
            Carbon::parse($request->fecha_fin)->endOfDay() : 
            Carbon::now()->endOfMonth();
        
        $pagos = PagoComision::with(['comision.commissionable', 'usuario'])
            ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_pago', 'desc')
            ->paginate(20);
            
        $estadisticasPagos = $this->obtenerEstadisticasPagos($fechaInicio, $fechaFin);
        
        return view('admin.pagos_comisiones.historial', compact(
            'pagos', 'estadisticasPagos', 'fechaInicio', 'fechaFin'
        ));
    }
    
    /**
     * Marcar comisiones como "pendientes de pago" mensualmente
     */
    public function marcarPendientesPago(Request $request)
    {
        $fechaCorte = $request->fecha_corte ? 
            Carbon::parse($request->fecha_corte) : 
            Carbon::now()->endOfMonth();
        
        DB::beginTransaction();
        
        try {
            // Marcar comisiones calculadas como pendientes
            $comisionesActualizadas = Comision::where('estado', 'calculado')
                ->where('fecha_calculo', '<=', $fechaCorte)
                ->update(['estado' => 'pendiente']);
            
            DB::commit();
            
            return redirect()->back()->with('success', 
                "Se marcaron {$comisionesActualizadas} comisiones como pendientes de pago"
            );
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 
                'Error al marcar comisiones: ' . $e->getMessage()
            );
        }
    }
    
    /**
     * Anular pago de comisión
     */
    public function anularPago($id)
    {
        DB::beginTransaction();
        
        try {
            $pago = PagoComision::findOrFail($id);
            
            // Anular el pago
            $pago->anular();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Pago anulado correctamente');
            
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 
                'Error al anular pago: ' . $e->getMessage()
            );
        }
    }
    
    /**
     * Generar reporte de pagos por período
     */
    public function generarReporte(Request $request)
    {
        $fechaInicio = Carbon::parse($request->fecha_inicio)->startOfDay();
        $fechaFin = Carbon::parse($request->fecha_fin)->endOfDay();
        
        $datosReporte = [
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin
            ],
            'pagos_por_tipo' => $this->obtenerPagosPorTipo($fechaInicio, $fechaFin),
            'pagos_por_metodo' => $this->obtenerPagosPorMetodo($fechaInicio, $fechaFin),
            'top_receptores' => $this->obtenerTopReceptores($fechaInicio, $fechaFin),
            'resumen_mensual' => $this->obtenerResumenMensual($fechaInicio, $fechaFin)
        ];
        
        return view('admin.pagos_comisiones.reporte', compact('datosReporte'));
    }
    
    // =================================
    // MÉTODOS PRIVADOS DE UTILIDAD
    // =================================
    
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
    
    private function obtenerEstadisticas($fechas, $tipoComision)
    {
        $query = Comision::whereBetween('fecha_calculo', [$fechas['inicio'], $fechas['fin']]);
        
        if ($tipoComision !== 'todas') {
            if ($tipoComision === 'vendedores') {
                $query->where('commissionable_type', 'App\Models\User');
            } elseif ($tipoComision === 'trabajadores') {
                $query->where('commissionable_type', 'App\Models\Trabajador');
            } else {
                $query->where('tipo_comision', $tipoComision);
            }
        }
        
        return [
            'total_comisiones' => $query->sum('monto'),
            'comisiones_pendientes' => $query->where('estado', 'pendiente')->sum('monto'),
            'comisiones_pagadas' => $query->where('estado', 'pagado')->sum('monto'),
            'cantidad_pendientes' => $query->where('estado', 'pendiente')->count(),
            'cantidad_pagadas' => $query->where('estado', 'pagado')->count()
        ];
    }
    
    private function obtenerEstadisticasPagos($fechaInicio, $fechaFin)
    {
        return [
            'total_pagado' => PagoComision::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                                ->where('estado', 'completado')->sum('monto'),
            'cantidad_pagos' => PagoComision::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                                ->where('estado', 'completado')->count(),
            'promedio_pago' => PagoComision::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                                ->where('estado', 'completado')->avg('monto'),
        ];
    }
    
    private function obtenerPagosPorTipo($fechaInicio, $fechaFin)
    {
        return DB::table('pagos_comisiones as pc')
            ->join('comisiones as c', 'pc.comision_id', '=', 'c.id')
            ->select('c.tipo_comision', DB::raw('SUM(pc.monto) as total'), DB::raw('COUNT(*) as cantidad'))
            ->whereBetween('pc.fecha_pago', [$fechaInicio, $fechaFin])
            ->where('pc.estado', 'completado')
            ->groupBy('c.tipo_comision')
            ->get();
    }
    
    private function obtenerPagosPorMetodo($fechaInicio, $fechaFin)
    {
        return PagoComision::select('metodo_pago', DB::raw('SUM(monto) as total'), DB::raw('COUNT(*) as cantidad'))
            ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
            ->where('estado', 'completado')
            ->groupBy('metodo_pago')
            ->get();
    }
    
    private function obtenerTopReceptores($fechaInicio, $fechaFin)
    {
        return DB::table('pagos_comisiones as pc')
            ->join('comisiones as c', 'pc.comision_id', '=', 'c.id')
            ->select(
                'c.commissionable_type',
                'c.commissionable_id',
                DB::raw('SUM(pc.monto) as total_recibido'),
                DB::raw('COUNT(*) as cantidad_pagos')
            )
            ->whereBetween('pc.fecha_pago', [$fechaInicio, $fechaFin])
            ->where('pc.estado', 'completado')
            ->groupBy('c.commissionable_type', 'c.commissionable_id')
            ->orderByDesc('total_recibido')
            ->limit(10)
            ->get();
    }
    
    private function obtenerResumenMensual($fechaInicio, $fechaFin)
    {
        return DB::table('pagos_comisiones')
            ->select(
                DB::raw('YEAR(fecha_pago) as año'),
                DB::raw('MONTH(fecha_pago) as mes'),
                DB::raw('SUM(monto) as total'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
            ->where('estado', 'completado')
            ->groupBy('año', 'mes')
            ->orderBy('año', 'desc')
            ->orderBy('mes', 'desc')
            ->get();
    }
    
    private function guardarComprobante($archivo)
    {
        $nombreArchivo = time() . '_' . uniqid() . '.' . $archivo->getClientOriginalExtension();
        $rutaCarpeta = public_path('assets/imgs/pagos_comisiones');
        
        // Crear carpeta si no existe
        if (!file_exists($rutaCarpeta)) {
            mkdir($rutaCarpeta, 0755, true);
        }
        
        $archivo->move($rutaCarpeta, $nombreArchivo);
        
        return 'assets/imgs/pagos_comisiones/' . $nombreArchivo;
    }
}
