<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\PagoSueldo;
use App\Models\DetallePagoSueldo;
use App\Models\Trabajador;
use App\Models\User;
use App\Models\Config;

class PagoSueldoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = PagoSueldo::with(['detalles', 'usuario']);

        // Filtros
        if ($request->filled('periodo')) {
            $query->where('periodo_mes', $request->periodo);
        }

        if ($request->filled('anio')) {
            $query->where('periodo_anio', $request->anio);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_lote', 'LIKE', "%{$search}%")
                  ->orWhere('observaciones', 'LIKE', "%{$search}%");
            });
        }

        $pagosSueldos = $query->orderBy('created_at', 'desc')->paginate(20);

        // Obtener periodos y años únicos para los filtros
        $periodos = PagoSueldo::distinct()->pluck('periodo_mes')->sort()->values();
        $anios = PagoSueldo::distinct()->pluck('periodo_anio')->sort()->values();

        return view('admin.pago-sueldo.index', compact('pagosSueldos', 'periodos', 'anios'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Cargar trabajadores y usuarios activos
        $trabajadores = Trabajador::where('estado', 1)->orderBy('nombre')->get();
        $usuarios = User::where('estado', 1)->where('role_as', 0)->orderBy('name')->get();

        // Generar el próximo número de lote simple
        $ultimoLote = PagoSueldo::max('numero_lote') ?? 0;
        $proximoNumero = intval($ultimoLote) + 1;

        // Obtener datos old si hay errores de validación
        $oldEmpleados = old('empleados', []);

        return view('admin.pago-sueldo.create', compact('trabajadores', 'usuarios', 'proximoNumero', 'oldEmpleados'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Log de datos recibidos para debugging
        Log::info('Datos recibidos en store:', [
            'all_data' => $request->all(),
            'empleados_count' => is_array($request->empleados) ? count($request->empleados) : 'no_array'
        ]);

        // Validación de los datos del formulario
        $validator = Validator::make($request->all(), [
            'periodo_mes' => 'required|integer|min:1|max:12',
            'periodo_anio' => 'required|integer|min:2024|max:2030',
            'metodo_pago' => 'required|in:efectivo,transferencia,cheque',
            'fecha_pago' => 'required|date',
            'estado' => 'required|in:pendiente,pagado',
            'observaciones' => 'nullable|string|max:1000',
            
            // Validación de empleados
            'empleados' => 'required|array|min:1',
            'empleados.*.tipo' => 'required|in:trabajador,vendedor',
            'empleados.*.id' => 'required|integer|min:1',
            'empleados.*.sueldo_base' => 'required|numeric|min:0',
            'empleados.*.horas_extra' => 'nullable|numeric|min:0',
            'empleados.*.valor_hora_extra' => 'nullable|numeric|min:0',
            'empleados.*.comisiones' => 'nullable|numeric|min:0',
            'empleados.*.bonificaciones' => 'nullable|numeric|min:0',
            'empleados.*.descuentos' => 'nullable|numeric|min:0',
            'empleados.*.observaciones' => 'nullable|string|max:500'
        ], [
            'empleados.required' => 'Debe agregar al menos un empleado al lote.',
            'empleados.min' => 'Debe agregar al menos un empleado al lote.',
            'empleados.*.tipo.required' => 'El tipo de empleado es obligatorio.',
            'empleados.*.id.required' => 'Debe seleccionar un empleado.',
            'empleados.*.sueldo_base.required' => 'El sueldo base es obligatorio.',
            'empleados.*.sueldo_base.numeric' => 'El sueldo base debe ser un número válido.',
        ]);

        if ($validator->fails()) {
            Log::info('Validación falló:', [
                'errores' => $validator->errors()->toArray(),
                'datos_recibidos' => $request->all()
            ]);
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput()
                           ->with('error', 'Por favor corrija los errores en el formulario.');
        }

        try {
            DB::beginTransaction();
            
            // Generar número de lote con formato PS-YYYYMM-XXX
            $periodo = date('Ym', strtotime($request->periodo_anio . '-' . $request->periodo_mes . '-01'));
            $prefijo = "PS-{$periodo}-";
            
            $ultimoRegistro = PagoSueldo::where('numero_lote', 'like', $prefijo . '%')
                                       ->orderBy('numero_lote', 'desc')
                                       ->first();
            
            if ($ultimoRegistro) {
                $ultimoNumero = str_replace($prefijo, '', $ultimoRegistro->numero_lote);
                $proximoNumero = str_pad(intval($ultimoNumero) + 1, 3, '0', STR_PAD_LEFT);
                $numeroLote = $prefijo . $proximoNumero;
            } else {
                $numeroLote = $prefijo . '001';
            }

            // Calcular total del lote
            $totalMonto = 0;
            foreach ($request->empleados as $empleado) {
                $sueldoBase = floatval($empleado['sueldo_base']);
                $horasExtra = floatval($empleado['horas_extra'] ?? 0);
                $valorHoraExtra = floatval($empleado['valor_hora_extra'] ?? 0);
                $comisiones = floatval($empleado['comisiones'] ?? 0);
                $bonificaciones = floatval($empleado['bonificaciones'] ?? 0);
                $descuentos = floatval($empleado['descuentos'] ?? 0);
                
                $totalEmpleado = $sueldoBase + ($horasExtra * $valorHoraExtra) + $comisiones + $bonificaciones - $descuentos;
                $totalMonto += $totalEmpleado;
            }

            // Crear el pago de sueldo principal
            $pagoSueldo = PagoSueldo::create([
                'numero_lote' => $numeroLote,
                'periodo_mes' => $request->periodo_mes,
                'periodo_anio' => $request->periodo_anio,
                'metodo_pago' => $request->metodo_pago,
                'fecha_pago' => $request->fecha_pago,
                'estado' => $request->estado,
                'observaciones' => $request->observaciones,
                'usuario_creo_id' => auth()->id(),
                'total_monto' => $totalMonto
            ]);

            // Crear los detalles para cada empleado usando campos detallados
            foreach ($request->empleados as $empleado) {
                $sueldoBase = floatval($empleado['sueldo_base']);
                $horasExtra = floatval($empleado['horas_extra'] ?? 0);
                $valorHoraExtra = floatval($empleado['valor_hora_extra'] ?? 0);
                $comisiones = floatval($empleado['comisiones'] ?? 0);
                $bonificaciones = floatval($empleado['bonificaciones'] ?? 0);
                $descuentos = floatval($empleado['descuentos'] ?? 0);
                
                $totalHorasExtra = $horasExtra * $valorHoraExtra;
                $totalPagar = $sueldoBase + $bonificaciones + $totalHorasExtra + $comisiones - $descuentos;

                DetallePagoSueldo::create([
                    'pago_sueldo_id' => $pagoSueldo->id,
                    'trabajador_id' => $empleado['tipo'] === 'trabajador' ? $empleado['id'] : null,
                    'usuario_id' => $empleado['tipo'] === 'vendedor' ? $empleado['id'] : null,
                    'tipo_empleado' => $empleado['tipo'],
                    'sueldo_base' => $sueldoBase,
                    'horas_extra' => $horasExtra,
                    'valor_hora_extra' => $valorHoraExtra,
                    'comisiones' => $comisiones,
                    'bonificaciones' => $bonificaciones,
                    'deducciones' => $descuentos,
                    'total_pagar' => $totalPagar,
                    'observaciones' => $empleado['observaciones'] ?? null,
                    'estado' => 'pendiente' // Todos los detalles nuevos empiezan como pendientes
                ]);
            }

            DB::commit();

            return redirect('/pagos-sueldos')
                           ->with('success', "Lote de sueldos #{$numeroLote} creado exitosamente con " . count($request->empleados) . " empleados.");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al crear lote de sueldos: ' . $e->getMessage());
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al crear el lote: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $pagoSueldo = PagoSueldo::with(['detalles.trabajador', 'detalles.usuario', 'usuario'])->findOrFail($id);
        
        // Si hay un mensaje de éxito en los parámetros URL, agregarlo a la sesión flash
        if ($request->has('success')) {
            session()->flash('success', $request->get('success'));
        }
        
        return view('admin.pago-sueldo.show', compact('pagoSueldo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pagoSueldo = PagoSueldo::with('detalles')->findOrFail($id);
        
        // Solo permitir editar si está en estado pendiente
        if ($pagoSueldo->estado !== 'pendiente') {
            return redirect()->route('admin.pago-sueldo.index')
                           ->with('error', 'Solo se pueden editar los lotes en estado pendiente.');
        }

        $trabajadores = Trabajador::where('estado', 1)->orderBy('nombre')->get();
        $usuarios = User::where('estado', 1)->where('role_as', 0)->orderBy('name')->get();

        // Preparar datos para reconstitución de empleados
        $empleadosExistentes = [];
        foreach ($pagoSueldo->detalles as $detalle) {
            $empleadosExistentes[] = [
                'detalle_id' => $detalle->id, // ID del detalle para identificación
                'tipo' => $detalle->tipo_empleado,
                'id' => $detalle->tipo_empleado === 'trabajador' ? $detalle->trabajador_id : $detalle->usuario_id,
                'sueldo_base' => $detalle->sueldo_base,
                'horas_extra' => $detalle->horas_extra ?? 0,
                'valor_hora_extra' => $detalle->valor_hora_extra ?? 0,
                'comisiones' => $detalle->comisiones ?? 0,
                'bonificaciones' => $detalle->bonificaciones, // Solo bonificaciones puras
                'descuentos' => $detalle->deducciones,
                'observaciones' => $detalle->observaciones ?? '',
                'total_pagar' => $detalle->total_pagar,
                'estado' => $detalle->estado, // Estado del detalle
                'fecha_pago' => $detalle->fecha_pago,
                'observaciones_pago' => $detalle->observaciones_pago
            ];
        }
        
        Log::info('Empleados existentes para edit:', $empleadosExistentes);

        return view('admin.pago-sueldo.edit', compact('pagoSueldo', 'trabajadores', 'usuarios', 'empleadosExistentes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $pagoSueldo = PagoSueldo::findOrFail($id);
        
        // Solo permitir editar si está en estado pendiente
        if ($pagoSueldo->estado !== 'pendiente') {
            return redirect('/pagos-sueldos')
                           ->with('error', 'Solo se pueden editar los lotes en estado pendiente.');
        }

        // Log de datos recibidos para debugging
        Log::info('Datos recibidos en update:', [
            'id' => $id,
            'empleados_count' => is_array($request->empleados) ? count($request->empleados) : 'no_array',
            'empleados_raw' => $request->input('empleados'),
            'detalles_existentes_antes' => $pagoSueldo->detalles->pluck('id')->toArray()
        ]);

        // Si los empleados vienen como JSON, procesarlos
        if ($request->has('empleados_json') && $request->empleados_json) {
            $empleadosFromJson = json_decode($request->empleados_json, true);
            if ($empleadosFromJson) {
                $request->merge(['empleados' => $empleadosFromJson]);
                Log::info('Empleados procesados desde JSON:', $empleadosFromJson);
            }
        }

        // Validación de los datos del formulario (adaptada para edit)
        $validator = Validator::make($request->all(), [
            'periodo_mes' => 'required|integer|min:1|max:12',
            'periodo_anio' => 'required|integer|min:2024|max:2030',
            'metodo_pago' => 'required|in:efectivo,transferencia,cheque',
            'fecha_pago' => 'required|date',
            'estado' => 'nullable|in:pendiente,pagado', // Ahora opcional
            'observaciones' => 'nullable|string|max:1000',
            
            // Validación de empleados para formulario de edición (con campos detallados)
            'empleados' => 'required|array|min:1',
            'empleados.*.tipo' => 'required|in:trabajador,vendedor',
            'empleados.*.id' => 'required|integer|min:1',
            'empleados.*.sueldo_base' => 'required|numeric|min:0',
            'empleados.*.horas_extra' => 'nullable|numeric|min:0',
            'empleados.*.valor_hora_extra' => 'nullable|numeric|min:0',
            'empleados.*.comisiones' => 'nullable|numeric|min:0',
            'empleados.*.bonificaciones' => 'nullable|numeric|min:0',
            'empleados.*.descuentos' => 'nullable|numeric|min:0',
            'empleados.*.observaciones' => 'nullable|string|max:500'
        ], [
            'empleados.required' => 'Debe agregar al menos un empleado al lote.',
            'empleados.min' => 'Debe agregar al menos un empleado al lote.',
            'empleados.*.tipo.required' => 'El tipo de empleado es obligatorio.',
            'empleados.*.id.required' => 'Debe seleccionar un empleado.',
            'empleados.*.sueldo_base.required' => 'El sueldo base es obligatorio.',
            'empleados.*.sueldo_base.numeric' => 'El sueldo base debe ser un número válido.',
        ]);

        if ($validator->fails()) {
            Log::info('Validación falló en update:', [
                'errores' => $validator->errors()->toArray(),
                'datos_recibidos' => $request->all()
            ]);
            
            // Si es una petición AJAX, devolver JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Por favor corrija los errores en el formulario.',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput()
                           ->with('error', 'Por favor corrija los errores en el formulario.');
        }

        try {
            DB::beginTransaction();

            // Calcular total del lote usando campos detallados
            $totalMonto = 0;
            foreach ($request->empleados as $empleado) {
                $sueldoBase = floatval($empleado['sueldo_base']);
                $horasExtra = floatval($empleado['horas_extra'] ?? 0);
                $valorHoraExtra = floatval($empleado['valor_hora_extra'] ?? 0);
                $comisiones = floatval($empleado['comisiones'] ?? 0);
                $bonificaciones = floatval($empleado['bonificaciones'] ?? 0);
                $descuentos = floatval($empleado['descuentos'] ?? 0);
                
                $totalHorasExtra = $horasExtra * $valorHoraExtra;
                $totalEmpleado = $sueldoBase + $bonificaciones + $totalHorasExtra + $comisiones - $descuentos;
                $totalMonto += $totalEmpleado;
            }

            // Actualizar el pago de sueldo principal (sin estado, se actualizará automáticamente)
            $pagoSueldo->update([
                'periodo_mes' => $request->periodo_mes,
                'periodo_anio' => $request->periodo_anio,
                'metodo_pago' => $request->metodo_pago,
                'fecha_pago' => $request->fecha_pago,
                'observaciones' => $request->observaciones,
                'total_monto' => $totalMonto
            ]);

            // Obtener detalles existentes
            $detallesExistentes = $pagoSueldo->detalles->keyBy('id');
            $detallesEnFormulario = [];
            
            Log::info('Detalles existentes en BD:', $detallesExistentes->pluck('id')->toArray());

            // Procesar empleados del formulario
            foreach ($request->empleados as $index => $empleado) {
                Log::info("Procesando empleado $index:", $empleado);
                
                $sueldoBase = floatval($empleado['sueldo_base']);
                $horasExtra = floatval($empleado['horas_extra'] ?? 0);
                $valorHoraExtra = floatval($empleado['valor_hora_extra'] ?? 0);
                $comisiones = floatval($empleado['comisiones'] ?? 0);
                $bonificaciones = floatval($empleado['bonificaciones'] ?? 0);
                $descuentos = floatval($empleado['descuentos'] ?? 0);
                
                $totalHorasExtra = $horasExtra * $valorHoraExtra;
                $totalPagar = $sueldoBase + $bonificaciones + $totalHorasExtra + $comisiones - $descuentos;

                // Si tiene detalle_id, es un empleado existente
                if (isset($empleado['detalle_id']) && $empleado['detalle_id']) {
                    $detalleId = $empleado['detalle_id'];
                    $detallesEnFormulario[] = $detalleId;
                    Log::info("Empleado existente - Detalle ID: $detalleId agregado a formulario");
                    
                    $detalle = $detallesExistentes->get($detalleId);
                    if ($detalle) {
                        // Solo actualizar si no está pagado
                        if ($detalle->estado !== 'pagado') {
                            Log::info("Actualizando detalle pendiente ID: $detalleId");
                            $detalle->update([
                                'sueldo_base' => $sueldoBase,
                                'horas_extra' => $horasExtra,
                                'valor_hora_extra' => $valorHoraExtra,
                                'comisiones' => $comisiones,
                                'bonificaciones' => $bonificaciones,
                                'deducciones' => $descuentos,
                                'total_pagar' => $totalPagar,
                                'observaciones' => $empleado['observaciones'] ?? null
                            ]);
                        } else {
                            Log::info("Detalle pagado ID: $detalleId - No se actualiza pero se conserva");
                        }
                        // Si está pagado, no se modifica pero sí se incluye en el total
                    }
                } else {
                    Log::info("Creando nuevo empleado");
                    // Es un empleado nuevo
                    DetallePagoSueldo::create([
                        'pago_sueldo_id' => $pagoSueldo->id,
                        'trabajador_id' => $empleado['tipo'] === 'trabajador' ? $empleado['id'] : null,
                        'usuario_id' => $empleado['tipo'] === 'vendedor' ? $empleado['id'] : null,
                        'tipo_empleado' => $empleado['tipo'],
                        'sueldo_base' => $sueldoBase,
                        'horas_extra' => $horasExtra,
                        'valor_hora_extra' => $valorHoraExtra,
                        'comisiones' => $comisiones,
                        'bonificaciones' => $bonificaciones,
                        'deducciones' => $descuentos,
                        'total_pagar' => $totalPagar,
                        'observaciones' => $empleado['observaciones'] ?? null,
                        'estado' => 'pendiente'
                    ]);
                }
            }
            
            Log::info('Detalles en formulario:', $detallesEnFormulario);

            // Eliminar detalles que ya no están en el formulario
            // Ahora SÍ permitimos eliminar detalles pagados (usuario ya confirmó en frontend)
            foreach ($detallesExistentes as $detalle) {
                if (!in_array($detalle->id, $detallesEnFormulario)) {
                    // Eliminar el detalle sin importar su estado
                    // La confirmación ya se hizo en el frontend
                    Log::info('Eliminando detalle ID ' . $detalle->id . ' con estado: ' . $detalle->estado);
                    $detalle->delete();
                }
            }

            // Recalcular el total del lote considerando todos los detalles finales
            $totalMonto = $pagoSueldo->detalles()->sum('total_pagar');
            $pagoSueldo->update(['total_monto' => $totalMonto]);
            
            // Verificar y actualizar automáticamente el estado del lote
            $pagoSueldo->verificarEstadoCompleto();

            DB::commit();

            // Si es una petición AJAX (como fetch), devolver JSON
            if ($request->expectsJson() || $request->ajax()) {
                $message = "Lote de sueldos #{$pagoSueldo->numero_lote} actualizado exitosamente con " . count($request->empleados) . " empleados.";
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'redirect' => route('admin.pago-sueldo.show', $pagoSueldo->id) . '?success=' . urlencode($message)
                ]);
            }

            return redirect()->route('admin.pago-sueldo.show', $pagoSueldo->id)
                           ->with('success', "Lote de sueldos #{$pagoSueldo->numero_lote} actualizado exitosamente con " . count($request->empleados) . " empleados.");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al actualizar lote de sueldos: ' . $e->getMessage());
            
            // Si es una petición AJAX, devolver JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el lote: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar el lote: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $pagoSueldo = PagoSueldo::findOrFail($id);
            
            // Solo permitir cancelar si está en estado pendiente
            if ($pagoSueldo->estado !== 'pendiente') {
                return redirect()->back()->with('error', 'Solo se pueden cancelar los lotes en estado pendiente.');
            }

            DB::beginTransaction();
            
            // Cambiar estado del lote principal a cancelado
            $pagoSueldo->estado = 'cancelado';
            $pagoSueldo->save();
            
            // Cambiar estado de todos los detalles a cancelado
            $pagoSueldo->detalles()->update([
                'estado' => 'cancelado'
            ]);
            
            DB::commit();

            return redirect()->route('admin.pago-sueldo.index')->with('success', 'Lote de pago cancelado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al cancelar lote de pago: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cancelar el lote de pago.');
        }
    }

    /**
     * Cambiar el estado de un lote de pago
     */
    public function cambiarEstado(Request $request, $id)
    {
        try {
            $pagoSueldo = PagoSueldo::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'estado' => 'required|in:pendiente,pagado,cancelado',
                'observaciones' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator);
            }

            $pagoSueldo->update([
                'estado' => $request->estado,
                'observaciones' => $request->observaciones ?: $pagoSueldo->observaciones
            ]);

            return redirect()->back()->with('success', 'Estado actualizado exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al cambiar estado: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al cambiar el estado.');
        }
    }

    /**
     * Cambiar estado de un detalle individual de pago
     */
    public function cambiarEstadoDetalle(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'estado' => 'required|in:pendiente,pagado,cancelado',
            'observaciones_pago' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $detalle = DetallePagoSueldo::findOrFail($id);
            
            // Verificar que el lote principal esté en estado que permita cambios
            if ($detalle->pagoSueldo->estado === 'cancelado') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede modificar un detalle de un lote cancelado'
                ], 403);
            }

            $estadoAnterior = $detalle->estado;
            
            // Actualizar el detalle
            $detalle->update([
                'estado' => $request->estado,
                'fecha_pago' => $request->estado === 'pagado' ? now() : null,
                'observaciones_pago' => $request->observaciones_pago
            ]);

            // Verificar si el estado del lote principal debe cambiar
            $resumen = $detalle->pagoSueldo->verificarEstadoCompleto();

            Log::info("Estado del detalle {$id} cambiado de {$estadoAnterior} a {$request->estado}", [
                'detalle_id' => $id,
                'lote_id' => $detalle->pagoSueldo->id,
                'resumen_lote' => $resumen
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'detalle' => [
                    'estado' => $detalle->estado,
                    'estado_texto' => $detalle->estado_texto,
                    'estado_color' => $detalle->estado_color,
                    'fecha_pago' => $detalle->fecha_pago ? $detalle->fecha_pago->format('d/m/Y H:i') : null,
                    'observaciones_pago' => $detalle->observaciones_pago
                ],
                'lote' => [
                    'estado' => $detalle->pagoSueldo->estado,
                    'progreso' => $resumen['progreso']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error al cambiar estado del detalle: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar PDF del lote de pago
     */
    public function generarPDF($id)
    {
        try {
            $config = Config::first();
            $pagoSueldo = PagoSueldo::with(['detalles.trabajador', 'detalles.usuario', 'usuario'])->findOrFail($id);
            
            // Procesar logo en base64 para PDF
            $logoBase64 = null;
            if ($config && $config->logo) {
                $logoPath = public_path('assets/imgs/logos/' . $config->logo);
                if (file_exists($logoPath)) {
                    $imageData = file_get_contents($logoPath);
                    $imageType = pathinfo($logoPath, PATHINFO_EXTENSION);
                    $logoBase64 = 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
                }
            }
            
            // Calcular resumen de datos
            $resumen = [
                'total_empleados' => $pagoSueldo->detalles->count(),
                'empleados_pagados' => $pagoSueldo->detalles->where('estado', 'pagado')->count(),
                'empleados_pendientes' => $pagoSueldo->detalles->where('estado', 'pendiente')->count(),
                'empleados_cancelados' => $pagoSueldo->detalles->where('estado', 'cancelado')->count(),
            ];
            
            // Datos para el PDF
            $fechaGeneracion = now()->format('d/m/Y H:i:s');
            
            // Generar el PDF usando DomPDF
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('admin.pago-sueldo.pdf.individual', compact(
                'config',
                'pagoSueldo',
                'resumen',
                'fechaGeneracion',
                'logoBase64'
            ));
            
            // Configurar opciones del PDF
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'Arial'
            ]);
            
            // Nombre del archivo
            $filename = "lote-sueldos-{$pagoSueldo->numero_lote}.pdf";
            
            // Devolver el PDF
            return $pdf->stream($filename);
            
        } catch (\Exception $e) {
            Log::error('Error generando PDF del lote de sueldos: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }
}