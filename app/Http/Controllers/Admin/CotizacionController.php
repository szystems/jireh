<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\Articulo;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Descuento;
use App\Models\Config;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class CotizacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cotizacion::query();

        // Filtros de fecha
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_cotizacion', '>=', $request->fecha_desde);
        } else {
            $query->where('fecha_cotizacion', '>=', Carbon::now()->subDays(30)->format('Y-m-d'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_cotizacion', '<=', $request->fecha_hasta);
        } else {
            $query->where('fecha_cotizacion', '<=', Carbon::now()->format('Y-m-d'));
        }

        // Filtro por número de cotización
        if ($request->filled('numero_cotizacion')) {
            $query->where('numero_cotizacion', 'like', '%' . $request->numero_cotizacion . '%');
        }

        // Filtro por cliente
        if ($request->filled('cliente')) {
            $query->where('cliente_id', $request->cliente);
        }

        // Filtro por vehículo
        if ($request->filled('vehiculo')) {
            $query->where('vehiculo_id', $request->vehiculo);
        }

        // Filtro por tipo de cotización
        if ($request->filled('tipo_cotizacion')) {
            $query->where('tipo_cotizacion', $request->tipo_cotizacion);
        }

        // Filtro por usuario
        if ($request->filled('usuario')) {
            $query->where('usuario_id', $request->usuario);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $cotizaciones = $query->with(['cliente', 'vehiculo', 'usuario', 'detalleCotizaciones.articulo'])
                             ->orderBy('fecha_cotizacion', 'desc')
                             ->get();

        // Actualizar estados automáticamente
        foreach ($cotizaciones as $cotizacion) {
            $cotizacion->actualizarEstado();
        }

        $clientes = Cliente::all();
        $vehiculos = Vehiculo::all();
        $usuarios = User::all();
        $config = Config::first();

        return view('admin.cotizacion.index', compact('cotizaciones', 'clientes', 'vehiculos', 'usuarios', 'config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $config = Config::first();
        $todosArticulos = Articulo::with('unidad')->get();
        $clientes = Cliente::all();
        $descuentos = Descuento::where('estado', 1)->get();

        // Obtener cliente_id de la URL si existe
        $cliente_id = $request->query('cliente_id');
        $cliente_selected = $cliente_id ? Cliente::find($cliente_id) : null;

        // Obtener vehículos del cliente seleccionado
        $vehiculos = $cliente_id ? Vehiculo::where('cliente_id', $cliente_id)->get() : collect();

        // Generar siguiente número de cotización
        $ultimaCotizacion = Cotizacion::orderBy('id', 'desc')->first();
        $siguienteNumero = 'COT-001';
        
        if ($ultimaCotizacion && $ultimaCotizacion->numero_cotizacion) {
            // Extraer el número de la última cotización
            $ultimoNumero = (int) substr($ultimaCotizacion->numero_cotizacion, 4);
            $siguienteNumero = 'COT-' . str_pad($ultimoNumero + 1, 3, '0', STR_PAD_LEFT);
        }

        return view('admin.cotizacion.create', compact(
            'config', 
            'todosArticulos', 
            'clientes', 
            'descuentos', 
            'cliente_selected', 
            'vehiculos',
            'siguienteNumero'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'vehiculo_id' => 'nullable|exists:vehiculos,id',
            'fecha_cotizacion' => 'required|date',
            'fecha_vencimiento' => 'required|date',
            'tipo_cotizacion' => 'required|in:Car Wash,CDS',
            'detalles' => 'required|array',
            'detalles.*.articulo_id' => 'required|exists:articulos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.sub_total' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Generar número de cotización
            $ultimaCotizacion = Cotizacion::orderBy('id', 'desc')->first();
            $siguienteNumero = 'COT-001';
            
            if ($ultimaCotizacion && $ultimaCotizacion->numero_cotizacion) {
                // Extraer el número de la última cotización
                $ultimoNumero = (int) substr($ultimaCotizacion->numero_cotizacion, 4);
                $siguienteNumero = 'COT-' . str_pad($ultimoNumero + 1, 3, '0', STR_PAD_LEFT);
            }

            // Crear la cotización
            $cotizacion = Cotizacion::create([
                'numero_cotizacion' => $siguienteNumero,
                'cliente_id' => $request->cliente_id,
                'vehiculo_id' => $request->vehiculo_id,
                'fecha_cotizacion' => $request->fecha_cotizacion,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'tipo_cotizacion' => $request->tipo_cotizacion,
                'usuario_id' => Auth::id(),
                'estado' => 'Generado',
                'observaciones' => $request->observaciones,
            ]);

            // Crear los detalles de la cotización
            foreach ($request->detalles as $detalle) {
                $articulo = Articulo::find($detalle['articulo_id']);
                
                DetalleCotizacion::create([
                    'cotizacion_id' => $cotizacion->id,
                    'articulo_id' => $detalle['articulo_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_costo' => $articulo ? $articulo->precio_compra : 0,
                    'precio_venta' => $detalle['precio_unitario'],
                    'descuento_id' => !empty($detalle['descuento_id']) ? $detalle['descuento_id'] : null,
                    'usuario_id' => Auth::id(),
                    'sub_total' => $detalle['sub_total'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.cotizaciones.index')
                           ->with('success', 'Cotización creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al crear cotización: ' . $e->getMessage());
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al crear la cotización: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cotizacion = Cotizacion::with([
            'cliente', 
            'vehiculo', 
            'usuario', 
            'detalleCotizaciones.articulo.unidad',
            'detalleCotizaciones.descuento'
        ])->findOrFail($id);

        // Actualizar estado si es necesario
        $cotizacion->actualizarEstado();

        $config = Config::first();

        return view('admin.cotizacion.show', compact('cotizacion', 'config'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $cotizacion = Cotizacion::with([
            'detalleCotizaciones.articulo',
            'detalleCotizaciones.descuento'
        ])->findOrFail($id);

        // Verificar que la cotización se pueda editar
        if ($cotizacion->estado === 'convertida') {
            return redirect()->route('admin.cotizaciones.index')
                           ->with('error', 'No se puede editar una cotización que ya fue convertida a venta.');
        }

        $config = Config::first();
        $todosArticulos = Articulo::with('unidad')->get();
        $clientes = Cliente::all();
        $vehiculos = Vehiculo::where('cliente_id', $cotizacion->cliente_id)->get();
        $descuentos = Descuento::where('estado', 1)->get();

        return view('admin.cotizacion.edit', compact(
            'cotizacion',
            'config', 
            'todosArticulos', 
            'clientes', 
            'vehiculos',
            'descuentos'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $cotizacion = Cotizacion::findOrFail($id);

        // Verificar que la cotización se pueda editar
        if ($cotizacion->estado === 'convertida') {
            return redirect()->route('admin.cotizaciones.index')
                           ->with('error', 'No se puede editar una cotización que ya fue convertida a venta.');
        }

        // Validación básica
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'vehiculo_id' => 'nullable|exists:vehiculos,id',
            'fecha_cotizacion' => 'required|date',
            'fecha_vencimiento' => 'required|date',
            'estado' => 'required|in:vigente,vencida,aprobada,rechazada,convertida,Generado,Aprobado',
            'tipo_cotizacion' => 'required|in:Car Wash,CDS',
        ]);

        // Validar nuevos detalles si existen
        if ($request->has('nuevos_detalles')) {
            $request->validate([
                'nuevos_detalles.*.articulo_id' => 'required|exists:articulos,id',
                'nuevos_detalles.*.cantidad' => 'required|numeric|min:0.01',
                'nuevos_detalles.*.precio_venta' => 'required|numeric|min:0',
            ]);
        }

        try {
            DB::beginTransaction();

            // Actualizar la cotización
            $cotizacion->update([
                'cliente_id' => $request->cliente_id,
                'vehiculo_id' => $request->vehiculo_id,
                'fecha_cotizacion' => $request->fecha_cotizacion,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'estado' => $request->estado,
                'tipo_cotizacion' => $request->tipo_cotizacion,
                'observaciones' => $request->observaciones,
            ]);

            // Eliminar detalles marcados para eliminar
            if ($request->filled('detalles_a_eliminar')) {
                $detallesAEliminar = explode(',', $request->detalles_a_eliminar);
                DetalleCotizacion::whereIn('id', $detallesAEliminar)->delete();
            }

            // Crear nuevos detalles si existen
            if ($request->has('nuevos_detalles')) {
                foreach ($request->nuevos_detalles as $nuevoDetalle) {
                    $articulo = Articulo::find($nuevoDetalle['articulo_id']);
                    
                    // Calcular sub_total
                    $subtotal = $nuevoDetalle['cantidad'] * $nuevoDetalle['precio_venta'];
                    
                    // Aplicar descuento si existe
                    if (!empty($nuevoDetalle['descuento_id'])) {
                        $descuento = Descuento::find($nuevoDetalle['descuento_id']);
                        if ($descuento) {
                            $montoDescuento = $subtotal * ($descuento->porcentaje_descuento / 100);
                            $subtotal -= $montoDescuento;
                        }
                    }
                    
                    DetalleCotizacion::create([
                        'cotizacion_id' => $cotizacion->id,
                        'articulo_id' => $nuevoDetalle['articulo_id'],
                        'cantidad' => $nuevoDetalle['cantidad'],
                        'precio_costo' => $articulo->precio_costo ?? 0,
                        'precio_venta' => $nuevoDetalle['precio_venta'],
                        'descuento_id' => $nuevoDetalle['descuento_id'] ?? null,
                        'usuario_id' => Auth::id(),
                        'sub_total' => $subtotal,
                        'porcentaje_impuestos' => 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.cotizaciones.index')
                           ->with('success', 'Cotización actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error al actualizar cotización: ' . $e->getMessage());
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar la cotización: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $cotizacion = Cotizacion::findOrFail($id);
            
            // Verificar que la cotización se pueda eliminar
            if ($cotizacion->estado === 'convertida') {
                return redirect()->route('admin.cotizaciones.index')
                               ->with('error', 'No se puede eliminar una cotización que ya fue convertida a venta.');
            }

            $cotizacion->delete();

            return redirect()->route('admin.cotizaciones.index')
                           ->with('success', 'Cotización eliminada exitosamente.');

        } catch (\Exception $e) {
            Log::error('Error al eliminar cotización: ' . $e->getMessage());
            
            return redirect()->route('admin.cotizaciones.index')
                           ->with('error', 'Error al eliminar la cotización.');
        }
    }

    /**
     * Exportar cotización individual a PDF
     */
    public function exportSinglePdf($id)
    {
        $cotizacion = Cotizacion::with([
            'cliente', 
            'vehiculo', 
            'usuario',
            'detalleCotizaciones.articulo.unidad',
            'detalleCotizaciones.descuento'
        ])->findOrFail($id);

        $config = Config::first();

        $pdf = PDF::loadView('admin.cotizacion.single_pdf', compact('cotizacion', 'config'));
        
        return $pdf->stream('cotizacion_' . $cotizacion->numero_cotizacion . '.pdf');
    }

    /**
     * Cambiar estado de la cotización
     */
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:Generado,Aprobado'
        ]);

        try {
            $cotizacion = Cotizacion::findOrFail($id);
            $estadoAnterior = $cotizacion->estado;
            
            // Si estamos regenerando (Aprobado → Generado), dar 15 días frescos
            if ($estadoAnterior === 'Aprobado' && $request->estado === 'Generado') {
                $cotizacion->update([
                    'estado' => $request->estado,
                    'fecha_vencimiento' => now()->addDays(15)
                ]);
                
                $mensaje = 'Cotización regenerada exitosamente con 15 días de vigencia.';
            } else {
                // Para otros cambios de estado, solo actualizar el estado
                $cotizacion->update(['estado' => $request->estado]);
                $mensaje = 'Estado de la cotización actualizado exitosamente.';
            }

            // Si es una petición AJAX, retornar JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $mensaje,
                    'nuevo_estado' => $request->estado
                ]);
            }

            return redirect()->back()
                           ->with('success', $mensaje);

        } catch (\Exception $e) {
            Log::error('Error al cambiar estado de cotización: ' . $e->getMessage());
            
            // Si es una petición AJAX, retornar JSON de error
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cambiar el estado de la cotización: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                           ->with('error', 'Error al cambiar el estado de la cotización.');
        }
    }
}
