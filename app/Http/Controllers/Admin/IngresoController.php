<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Ingreso;
use App\Models\DetalleIngreso;
use App\Models\Articulo;
use App\Models\Proveedor;
use App\Models\Config;
use App\Models\User;
use App\Http\Requests\IngresoFormRequest;
use App\Http\Requests\IngresoEditFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IngresosExport;
use Barryvdh\DomPDF\Facade\Pdf;

class IngresoController extends Controller
{
    public function index(Request $request)
    {
        $query = Ingreso::query();

        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        } else {
            $query->where('fecha', '>=', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        } else {
            $query->where('fecha', '<=', \Carbon\Carbon::now()->format('Y-m-d'));
        }

        if ($request->filled('numero_factura')) {
            $query->where('numero_factura', 'like', '%' . $request->numero_factura . '%');
        }

        if ($request->filled('proveedor')) {
            $query->where('proveedor_id', $request->proveedor);
        }

        if ($request->filled('tipo_compra')) {
            $query->where('tipo_compra', $request->tipo_compra);
        }

        if ($request->filled('usuario')) {
            $query->where('usuario_id', $request->usuario);
        }

        $ingresos = $query->with(['detalles.articulo'])->orderBy('fecha', 'desc')->get();

        $proveedores = Proveedor::all();
        $usuarios = User::all();
        $config = Config::first();

        return view('admin.ingreso.index', compact('ingresos', 'proveedores', 'usuarios', 'config'));
    }

    public function create(Request $request)
    {
        $config = Config::first();
        $todosArticulos = Articulo::with('unidad')->get();
        $proveedores = Proveedor::all();

        // Verificamos si se proporcionó un ID de artículo
        $articuloSeleccionado = null;
        if ($request->has('articulo')) {
            $articuloSeleccionado = Articulo::with('unidad')->find($request->articulo);
        }

        return view('admin.ingreso.create', compact('todosArticulos', 'proveedores', 'config', 'articuloSeleccionado'));
    }

    public function store(IngresoFormRequest $request)
    {
        // dd($request->all());
        $validated = $request->validated();

        $ingreso = Ingreso::create(array_merge(
            $request->only('numero_factura', 'fecha', 'proveedor_id', 'tipo_compra'),
            ['usuario_id' => Auth::user()->id]
        ));

        foreach ($validated['detalles'] as $detalle) {
            // Se crea el detalle del ingreso
            $ingreso->detalles()->create($detalle);

            // Actualizar el stock del artículo sumando la cantidad
            $articulo = Articulo::find($detalle['articulo_id']);
            if ($articulo) {
                $articulo->stock += $detalle['cantidad'];
                $articulo->precio_compra = $detalle['precio_compra'];
                $articulo->precio_venta = $detalle['precio_venta'];
                $articulo->save();
            }
        }

        return redirect('ingresos')->with('status',__('Ingreso registrado exitosamente.'));
    }

    public function show($id)
    {
        $ingreso = Ingreso::with(['detalles.articulo', 'proveedor', 'usuario'])->findOrFail($id);
        $config = Config::first();
        return view('admin.ingreso.show', compact('ingreso', 'config'));
    }

    public function edit($id)
    {
        $ingreso = Ingreso::with('detalles.articulo')->findOrFail($id);
        $todosArticulos = Articulo::with('unidad')->get();
        $proveedores = Proveedor::all();
        $config = Config::first();
        return view('admin.ingreso.edit', compact('ingreso', 'todosArticulos', 'proveedores', 'config'));
    }

    public function update(IngresoEditFormRequest $request, $id)
    {
        $validated = $request->validated();

        $ingreso = Ingreso::findOrFail($id);
        $ingreso->update($request->only('numero_factura', 'fecha', 'proveedor_id', 'tipo_compra'));

        foreach ($validated['detalles'] as $detalleId => $detalleData) {
            if (is_numeric($detalleId)) {
                $detalle = DetalleIngreso::findOrFail($detalleId);
                $articulo = Articulo::find($detalle->articulo_id);

                if (isset($detalleData['eliminar']) && $detalleData['eliminar'] == 1) {
                    // Restar el stock usando la cantidad actual
                    $articulo->stock -= $detalle->cantidad;
                    $articulo->save();
                    $detalle->delete();
                } else {
                    // Restar la cantidad anterior del stock
                    $articulo->stock -= $detalle->cantidad;

                    // Actualizar el detalle
                    $detalle->update($detalleData);

                    // Sumar la nueva cantidad al stock
                    $articulo->stock += $detalleData['cantidad'];
                    $articulo->precio_compra = $detalleData['precio_compra'];
                    $articulo->precio_venta = $detalleData['precio_venta'];
                    $articulo->save();
                }
            } else {
                // Agregar nuevos detalles
                $detalle = $ingreso->detalles()->create($detalleData);
                $articulo = Articulo::find($detalle->articulo_id);

                // Sumar la cantidad al stock
                $articulo->stock += $detalle->cantidad;
                $articulo->precio_compra = $detalle->precio_compra;
                $articulo->precio_venta = $detalle->precio_venta;
                $articulo->save();
            }
        }

        return redirect('ingresos')->with('status', __('Ingreso actualizado exitosamente.'));
    }

    public function destroy($id)
    {
        $ingreso = Ingreso::with('detalles.articulo')->findOrFail($id);

        foreach ($ingreso->detalles as $detalle) {
            $articulo = $detalle->articulo;
            if ($articulo) {
                // Devolver el stock del artículo
                $articulo->stock -= $detalle->cantidad;
                $articulo->save();
            }
            // Eliminar el detalle del ingreso
            $detalle->delete();
        }

        // Eliminar el ingreso
        $ingreso->delete();

        return redirect('ingresos')->with('status', __('Ingreso eliminado exitosamente.'));
    }

    public function exportPdf(Request $request)
    {
        $ingresos = $this->getFilteredIngresos($request)->load(['proveedor', 'usuario', 'detalles.articulo']);
        $config = Config::first();
        $proveedores = Proveedor::all();
        $usuarios = User::all();
        $filters = [
            'fecha_desde' => $request->input('fecha_desde') ?? \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'),
            'fecha_hasta' => $request->input('fecha_hasta') ?? \Carbon\Carbon::now()->format('Y-m-d'),
            'numero_factura' => $request->input('numero_factura'),
            'proveedor' => $request->input('proveedor'),
            'tipo_compra' => $request->input('tipo_compra'),
            'usuario' => $request->input('usuario'),
        ];
        $pdf = Pdf::loadView('admin.ingreso.pdf', compact('ingresos', 'config', 'filters', 'proveedores', 'usuarios'));
        return $pdf->stream('ingresos.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new IngresosExport($request), 'ingresos.xlsx');
    }

    public function exportSinglePdf($id)
    {
        $ingreso = Ingreso::with(['detalles.articulo', 'proveedor', 'usuario'])->findOrFail($id);
        $config = Config::first();
        $pdf = Pdf::loadView('admin.ingreso.single_pdf', compact('ingreso', 'config'));
        return $pdf->stream('ingreso_' . $ingreso->id . '.pdf');
    }

    private function getFilteredIngresos(Request $request)
    {
        $query = Ingreso::query();

        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        } else {
            $query->where('fecha', '>=', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        } else {
            $query->where('fecha', '<=', \Carbon\Carbon::now()->format('Y-m-d'));
        }

        if ($request->filled('numero_factura')) {
            $query->where('numero_factura', 'like', '%' . $request->numero_factura . '%');
        }

        if ($request->filled('proveedor')) {
            $query->where('proveedor_id', $request->proveedor);
        }

        if ($request->filled('tipo_compra')) {
            $query->where('tipo_compra', $request->tipo_compra);
        }

        if ($request->filled('usuario')) {
            $query->where('usuario_id', $request->usuario);
        }

        return $query->with(['detalles.articulo'])->get();
    }
}
