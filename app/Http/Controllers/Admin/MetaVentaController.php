<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MetaVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MetaVentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $metas = MetaVenta::orderBy('monto_minimo', 'asc')->paginate(10);
        $filtroAplicado = null; // Sin filtro aplicado
        return view('admin.metas-ventas.index', compact('metas', 'filtroAplicado'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.metas-ventas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'monto_minimo' => 'required|numeric|min:0',
            'monto_maximo' => 'nullable|numeric|min:0|gt:monto_minimo',
            'porcentaje_comision' => 'required|numeric|min:0|max:100',
            'periodo' => 'required|in:mensual,trimestral,semestral,anual'
        ]);

        // Verificar que no haya solapamiento de rangos para el mismo período
        $errorSolapamiento = $this->validarRangos($request->all());
        if ($errorSolapamiento) {
            return back()->withErrors(['monto_minimo' => $errorSolapamiento])->withInput();
        }

        $meta = MetaVenta::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'monto_minimo' => $request->monto_minimo,
            'monto_maximo' => $request->monto_maximo,
            'porcentaje_comision' => $request->porcentaje_comision,
            'periodo' => $request->periodo,
            'estado' => $request->has('estado') ? 1 : 0
        ]);

        return redirect()->route('metas-ventas.index')
                        ->with('success', 'Meta de venta creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MetaVenta $metaVenta)
    {
        return view('admin.metas-ventas.show', compact('metaVenta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MetaVenta $metaVenta)
    {
        return view('admin.metas-ventas.edit', compact('metaVenta'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MetaVenta $metaVenta)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'monto_minimo' => 'required|numeric|min:0',
            'monto_maximo' => 'nullable|numeric|min:0|gt:monto_minimo',
            'porcentaje_comision' => 'required|numeric|min:0|max:100',
            'periodo' => 'required|in:mensual,trimestral,semestral,anual'
        ]);

        // Verificar que no haya solapamiento de rangos (excluyendo la meta actual)
        $errorSolapamiento = $this->validarRangos($request->all(), $metaVenta->id);
        if ($errorSolapamiento) {
            return back()->withErrors(['monto_minimo' => $errorSolapamiento])->withInput();
        }

        $metaVenta->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'monto_minimo' => $request->monto_minimo,
            'monto_maximo' => $request->monto_maximo,
            'porcentaje_comision' => $request->porcentaje_comision,
            'periodo' => $request->periodo,
            'estado' => $request->has('estado') ? 1 : 0
        ]);

        return redirect()->route('metas-ventas.index')
                        ->with('success', 'Meta de venta actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MetaVenta $metaVenta)
    {
        $metaVenta->delete();
        
        return redirect()->route('metas-ventas.index')
                        ->with('success', 'Meta de venta eliminada exitosamente.');
    }

    /**
     * Cambiar el estado de una meta
     */
    public function toggleEstado(MetaVenta $metaVenta)
    {
        $metaVenta->update(['estado' => !$metaVenta->estado]);
        
        $estado = $metaVenta->estado ? 'activada' : 'desactivada';
        
        return redirect()->route('metas-ventas.index')
                        ->with('success', "Meta de venta {$estado} exitosamente.");
    }

    /**
     * Mostrar metas por período
     */
    public function porPeriodo(Request $request, $periodo)
    {
        // Validar que el período sea válido
        $periodosValidos = ['mensual', 'trimestral', 'semestral', 'anual'];
        if (!in_array($periodo, $periodosValidos)) {
            return redirect()->route('metas-ventas.index')
                           ->with('error', 'Período no válido.');
        }

        // Obtener metas filtradas por período con paginación
        $metas = MetaVenta::where('periodo', $periodo)
                          ->orderBy('estado', 'desc') // Activas primero
                          ->orderBy('monto_minimo', 'asc')
                          ->paginate(10);

        // Pasar información del filtro aplicado a la vista
        $filtroAplicado = [
            'tipo' => 'periodo',
            'valor' => $periodo,
            'etiqueta' => ucfirst($periodo)
        ];

        // Usar la misma vista del index pero con datos filtrados
        return view('admin.metas-ventas.index', compact('metas', 'filtroAplicado'));
    }

    /**
     * Validar que no haya solapamiento de rangos de montos
     * @param array $data Datos del formulario
     * @param int|null $metaIdExcluir ID de la meta a excluir de la validación (para edición)
     * @return string|null Mensaje de error si hay solapamiento, null si no hay problemas
     */
    private function validarRangos($data, $metaIdExcluir = null)
    {
        $query = MetaVenta::where('periodo', $data['periodo'])
                          ->where('estado', true);

        if ($metaIdExcluir) {
            $query->where('id', '!=', $metaIdExcluir);
        }

        $metasExistentes = $query->get();

        foreach ($metasExistentes as $meta) {
            // Verificar solapamiento
            $nuevoMin = $data['monto_minimo'];
            $nuevoMax = $data['monto_maximo'] ?? PHP_FLOAT_MAX;
            
            $existenteMin = $meta->monto_minimo;
            $existenteMax = $meta->monto_maximo ?? PHP_FLOAT_MAX;

            // Verificar si hay solapamiento
            if ($nuevoMin <= $existenteMax && $nuevoMax >= $existenteMin) {
                return "El rango de montos se solapa con la meta existente: '{$meta->nombre}'. Por favor, ajuste los montos mínimo y máximo.";
            }
        }
        
        return null;
    }

    /**
     * API para obtener meta correspondiente a un monto
     */
    public function obtenerMetaPorMonto(Request $request)
    {
        $monto = $request->get('monto');
        $periodo = $request->get('periodo', 'mensual');

        $meta = MetaVenta::where('periodo', $periodo)
                        ->where('estado', true)
                        ->where('monto_minimo', '<=', $monto)
                        ->where(function($query) use ($monto) {
                            $query->whereNull('monto_maximo')
                                  ->orWhere('monto_maximo', '>=', $monto);
                        })
                        ->orderBy('monto_minimo', 'desc')
                        ->first();

        if ($meta) {
            return response()->json([
                'meta' => $meta,
                'comision_calculada' => $monto * ($meta->porcentaje_comision / 100)
            ]);
        }

        return response()->json(['meta' => null, 'comision_calculada' => 0]);
    }
}
