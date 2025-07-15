<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comision;
use App\Models\Venta;
use App\Models\Trabajador;
use App\Models\User;
use App\Models\PagoComision;
use App\Models\MetaVenta;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
     * Dashboard principal de comisiones con 3 tipos
     */
    public function dashboard(Request $request)
    {
        // Filtros
        $periodo = $request->get('periodo', 'mes_actual');
        $tipoComision = $request->get('tipo', 'todas');
        $periodoMeta = $request->get('periodo_meta', 'mensual');
        
        // Calcular fechas según período
        $fechas = $this->calcularPeriodo($periodo);
        
        // Obtener comisiones por tipo
        $comisiones = [
            'vendedores' => $this->calcularComisionesVendedores($fechas, $periodoMeta),
            'mecanicos' => $this->calcularComisionesMecanicos($fechas),
            'carwash' => $this->calcularComisionesCarwash($fechas)
        ];
        
        // Obtener trabajadores para filtros
        $vendedores = User::where('role_as', 1)->get();
        $mecanicos = Trabajador::whereHas('tipoTrabajador', function($q) {
            $q->where('nombre', 'like', '%mecánico%');
        })->get();
        $carwasheros = Trabajador::whereHas('tipoTrabajador', function($q) {
            $q->where('nombre', 'like', '%carwash%');
        })->get();
        
        // Obtener metas disponibles
        $metasDisponibles = MetaVenta::activas()->get()->groupBy('periodo');
        
        return view('admin.comisiones.dashboard', compact(
            'comisiones', 'vendedores', 'mecanicos', 'carwasheros',
            'periodo', 'tipoComision', 'periodoMeta', 'fechas', 'metasDisponibles'
        ));
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

        return view('admin.comisiones.index', compact('comisiones', 'tiposComision'));
    }

    /**
     * Muestra los detalles de una comisión específica
     */
    public function show($id)
    {
        $comision = Comision::with(['commissionable', 'venta', 'detalleVenta', 'articulo', 'pagos'])
            ->findOrFail($id);

        return view('admin.comisiones.show', compact('comision'));
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

        return view('admin.comisiones.por_trabajador', compact('trabajadores', 'tiposComision'));
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

        return view('admin.comisiones.por_vendedor', compact('vendedores'));
    }

    /**
     * Registrar un pago de comisión
     */
    public function registrarPago(Request $request, $id)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'fecha_pago' => 'required|date',
            'observaciones' => 'nullable|string|max:255',
        ]);

        $comision = Comision::findOrFail($id);

        // Verificar que el monto a pagar no exceda el pendiente
        $montoPendiente = $comision->montoPendiente();
        if ($request->monto > $montoPendiente) {
            return back()->with('error', "El monto máximo a pagar es de $montoPendiente");
        }

        // Registrar el pago
        $pago = new PagoComision();
        $pago->comision_id = $comision->id;
        $pago->monto = $request->monto;
        $pago->fecha_pago = $request->fecha_pago;
        $pago->observaciones = $request->observaciones;
        $pago->estado = 'completado';
        $pago->save();

        // Actualizar el estado de la comisión
        $comision->actualizarEstado();

        return redirect()->route('comisiones.show', $comision->id)
                        ->with('success', 'Pago registrado correctamente');
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

        return view('admin.comisiones.resumen', compact(
            'fechaInicio',
            'fechaFin',
            'totalesPorTipo',
            'totalesPorEstado',
            'topTrabajadores',
            'topVendedores'
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
}
