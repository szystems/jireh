<?php

namespace App\Http\Controllers;

use App\Models\LotePago;
use App\Models\PagoComision;
use App\Models\Comision;
use App\Models\Trabajador;
use App\Models\User;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LotePagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $config = Config::first();
        
        $query = LotePago::with(['usuario', 'pagosComisiones'])
                          ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_pago', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_pago', '<=', $request->fecha_fin);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        $lotesPago = $query->paginate(15);

        return view('lotes-pago.index', compact('lotesPago', 'config'));
    }

    /**
     * Show the form for creating a new resource with advanced filters.
     */
    public function create(Request $request)
    {
        // Construir consulta base con comisiones pendientes
        $query = Comision::with(['commissionable', 'venta', 'venta.cliente'])
                         ->where('estado', 'pendiente');

        // FILTROS AVANZADOS
        
        // Filtro por trabajador específico
        if ($request->filled('trabajador_id')) {
            $query->where('commissionable_type', 'App\\Models\\Trabajador')
                  ->where('commissionable_id', $request->trabajador_id);
        }

        // Filtro por vendedor específico
        if ($request->filled('vendedor_id')) {
            $query->where('commissionable_type', 'App\\Models\\User')
                  ->where('commissionable_id', $request->vendedor_id);
        }

        // Filtro por tipo de comisión
        if ($request->filled('tipo_comision')) {
            $query->where('tipo_comision', $request->tipo_comision);
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_calculo', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('fecha_calculo', '<=', $request->fecha_fin);
        }

        // Filtros rápidos por período (por defecto "este mes" si no hay otros filtros)
        // Las fechas personalizadas tienen prioridad sobre el período rápido
        $periodoSeleccionado = null;
        if (!$request->filled('fecha_inicio') && !$request->filled('fecha_fin')) {
            $periodoSeleccionado = $request->filled('periodo') ? $request->periodo : 
                (!$request->hasAny(['trabajador_id', 'vendedor_id', 'tipo_comision', 'monto_minimo', 'monto_maximo']) ? 'este_mes' : null);
        }
        
        if ($periodoSeleccionado) {
            switch ($periodoSeleccionado) {
                case 'hoy':
                    $query->whereDate('fecha_calculo', today());
                    break;
                case 'ayer':
                    $query->whereDate('fecha_calculo', now()->subDay());
                    break;
                case 'esta_semana':
                    $query->whereBetween('fecha_calculo', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]);
                    break;
                case 'semana_anterior':
                    $query->whereBetween('fecha_calculo', [
                        now()->subWeek()->startOfWeek(),
                        now()->subWeek()->endOfWeek()
                    ]);
                    break;
                case 'este_mes':
                    $query->whereBetween('fecha_calculo', [
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ]);
                    break;
                case 'mes_anterior':
                    $query->whereBetween('fecha_calculo', [
                        now()->subMonth()->startOfMonth(),
                        now()->subMonth()->endOfMonth()
                    ]);
                    break;
                case 'este_trimestre':
                    $query->whereBetween('fecha_calculo', [
                        now()->startOfQuarter(),
                        now()->endOfQuarter()
                    ]);
                    break;
                case 'este_año':
                    $query->whereBetween('fecha_calculo', [
                        now()->startOfYear(),
                        now()->endOfYear()
                    ]);
                    break;
                case 'año_anterior':
                    $query->whereBetween('fecha_calculo', [
                        now()->subYear()->startOfYear(),
                        now()->subYear()->endOfYear()
                    ]);
                    break;
                case 'ultimos_30_dias':
                    $query->whereBetween('fecha_calculo', [
                        now()->subDays(30),
                        now()
                    ]);
                    break;
                case 'ultimos_90_dias':
                    $query->whereBetween('fecha_calculo', [
                        now()->subDays(90),
                        now()
                    ]);
                    break;
            }
        }

        // Filtro por rango de monto
        if ($request->filled('monto_minimo')) {
            $query->where('monto', '>=', $request->monto_minimo);
        }
        if ($request->filled('monto_maximo')) {
            $query->where('monto', '<=', $request->monto_maximo);
        }

        // Obtener comisiones sin paginación para evitar pérdida de selecciones
        $comisionesPendientes = $query->orderBy('fecha_calculo', 'desc')
                                     ->get();

        // Obtener datos para los filtros
        $trabajadores = Trabajador::whereHas('comisiones', function($q) {
            $q->where('estado', 'pendiente');
        })->orderBy('nombre')->get();

        $vendedores = User::where('role_as', 1)
                         ->whereHas('comisiones', function($q) {
                             $q->where('estado', 'pendiente');
                         })
                         ->orderBy('name')->get();

        $tiposComision = Comision::where('estado', 'pendiente')
                                ->select('tipo_comision')
                                ->distinct()
                                ->pluck('tipo_comision');

        // Calcular estadísticas de los filtros aplicados
        $estadisticas = [
            'total_comisiones' => $comisionesPendientes->count(),
            'monto_total' => $comisionesPendientes->sum('monto'),
            'comisiones_mostradas' => $comisionesPendientes->count()
        ];

        // Obtener configuración
        $config = Config::first();

        return view('lotes-pago.create', compact(
            'comisionesPendientes',
            'trabajadores', 
            'vendedores',
            'tiposComision',
            'estadisticas',
            'config',
            'periodoSeleccionado'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|string',
            'referencia' => 'nullable|string|max:255',
            'comprobante_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'observaciones' => 'nullable|string|max:255',
            'comision_ids' => 'required|array|min:1',
            'comision_ids.*' => 'exists:comisiones,id',
        ]);

        try {
            DB::beginTransaction();

            // Calcular totales de las comisiones seleccionadas
            $comisiones = Comision::whereIn('id', $request->comision_ids)->get();
            $montoTotal = $comisiones->sum('monto');
            $cantidadComisiones = $comisiones->count();

            // Manejar upload de comprobante
            $comprobanteNombre = null;
            if ($request->hasFile('comprobante_imagen')) {
                $comprobante = $request->file('comprobante_imagen');
                $comprobanteNombre = time() . '_' . $comprobante->getClientOriginalName();
                $comprobante->move(public_path('uploads/comprobantes'), $comprobanteNombre);
            }

            // Crear el lote de pago con reintento en caso de número duplicado
            $lotePago = null;
            $maxIntentos = 3;
            
            for ($intento = 1; $intento <= $maxIntentos; $intento++) {
                try {
                    $numeroLote = LotePago::generarNumeroLote();
                    
                    $lotePago = LotePago::create([
                        'numero_lote' => $numeroLote,
                        'fecha_pago' => $request->fecha_pago,
                        'metodo_pago' => $request->metodo_pago,
                        'referencia' => $request->referencia,
                        'comprobante_imagen' => $comprobanteNombre,
                        'observaciones' => $request->observaciones,
                        'monto_total' => $montoTotal,
                        'cantidad_comisiones' => $cantidadComisiones,
                        'estado' => 'completado',
                        'usuario_id' => Auth::id(),
                    ]);
                    
                    // Si llegamos aquí, la creación fue exitosa
                    break;
                    
                } catch (\Illuminate\Database\QueryException $e) {
                    // Si es error de clave duplicada y no es el último intento
                    if ($e->getCode() == 23000 && $intento < $maxIntentos) {
                        Log::warning("Intento {$intento}: Número de lote duplicado {$numeroLote}, reintentando...");
                        continue;
                    }
                    // Si no es error de clave duplicada o es el último intento, relanzar
                    throw $e;
                }
            }
            
            if (!$lotePago) {
                throw new \Exception('No se pudo generar un número de lote único después de ' . $maxIntentos . ' intentos.');
            }

            // Crear los pagos individuales y asociarlos al lote
            foreach ($comisiones as $comision) {
                PagoComision::create([
                    'comision_id' => $comision->id,
                    'lote_pago_id' => $lotePago->id,
                    'monto' => $comision->monto,
                    'metodo_pago' => $request->metodo_pago,
                    'usuario_id' => Auth::id(),
                    'fecha_pago' => $request->fecha_pago,
                    'referencia' => $request->referencia,
                    'comprobante_imagen' => $comprobanteNombre,
                    'observaciones' => $request->observaciones,
                    'estado' => 'completado',
                ]);

                // Actualizar estado de la comisión
                $comision->actualizarEstado();
            }

            DB::commit();

            return redirect()->route('lotes-pago.show', $lotePago)
                            ->with('success', 'Lote de pago creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Error al crear el lote de pago: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(LotePago $lotePago)
    {
        $config = Config::first();
        $lotePago->load(['usuario', 'pagosComisiones.comision.commissionable', 'pagosComisiones.comision.venta.cliente']);
        
        return view('lotes-pago.show', compact('lotePago', 'config'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LotePago $lotePago)
    {
        // Solo permitir edición si está en estado procesando o completado (no anulado)
        if ($lotePago->estado === 'anulado') {
            return redirect()->route('lotes-pago.show', $lotePago)
                            ->with('error', 'No se puede editar un lote de pago en estado: ' . $lotePago->estado);
        }

        $config = Config::first();
        return view('lotes-pago.edit', compact('lotePago', 'config'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LotePago $lotePago)
    {
        if ($lotePago->estado === 'anulado') {
            return redirect()->route('lotes-pago.show', $lotePago)
                            ->with('error', 'No se puede editar un lote de pago anulado.');
        }

        $request->validate([
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|string',
            'referencia' => 'nullable|string|max:255',
            'comprobante_imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'observaciones' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Manejar upload de nuevo comprobante
            $comprobanteNombre = $lotePago->comprobante_imagen;
            if ($request->hasFile('comprobante_imagen')) {
                // Eliminar comprobante anterior si existe
                if ($comprobanteNombre && file_exists(public_path('uploads/comprobantes/' . $comprobanteNombre))) {
                    unlink(public_path('uploads/comprobantes/' . $comprobanteNombre));
                }

                $comprobante = $request->file('comprobante_imagen');
                $comprobanteNombre = time() . '_' . $comprobante->getClientOriginalName();
                $comprobante->move(public_path('uploads/comprobantes'), $comprobanteNombre);
            }

            // Actualizar el lote
            $lotePago->update([
                'fecha_pago' => $request->fecha_pago,
                'metodo_pago' => $request->metodo_pago,
                'referencia' => $request->referencia,
                'comprobante_imagen' => $comprobanteNombre,
                'observaciones' => $request->observaciones,
            ]);

            // Actualizar los pagos asociados
            $lotePago->pagosComisiones()->update([
                'fecha_pago' => $request->fecha_pago,
                'metodo_pago' => $request->metodo_pago,
                'referencia' => $request->referencia,
                'comprobante_imagen' => $comprobanteNombre,
                'observaciones' => $request->observaciones,
            ]);

            DB::commit();

            return redirect()->route('lotes-pago.show', $lotePago)
                            ->with('success', 'Lote de pago actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                        ->with('error', 'Error al actualizar el lote de pago: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LotePago $lotePago)
    {
        if ($lotePago->estado === 'anulado') {
            return redirect()->route('lotes-pago.index')
                            ->with('error', 'No se puede eliminar un lote de pago que ya está anulado.');
        }

        try {
            DB::beginTransaction();

            // Anular todos los pagos asociados
            $lotePago->pagosComisiones()->update(['estado' => 'anulado']);

            // Actualizar estado de las comisiones
            foreach ($lotePago->pagosComisiones as $pago) {
                if ($pago->comision) {
                    $pago->comision->actualizarEstado();
                }
            }

            // Marcar el lote como anulado en lugar de eliminarlo
            $lotePago->update(['estado' => 'anulado']);

            // Eliminar comprobante si existe
            if ($lotePago->comprobante_imagen && file_exists(public_path('uploads/comprobantes/' . $lotePago->comprobante_imagen))) {
                unlink(public_path('uploads/comprobantes/' . $lotePago->comprobante_imagen));
            }

            DB::commit();

            return redirect()->route('lotes-pago.index')
                            ->with('success', 'Lote de pago anulado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al anular el lote de pago: ' . $e->getMessage());
        }
    }
}
