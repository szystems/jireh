<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VentaFormRequest;
use App\Http\Requests\VentaEditFormRequest;
use App\Models\Venta;
use Illuminate\Http\Request;
use App\Models\Articulo;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\DetalleVenta;
use App\Models\Descuento;
use App\Models\Config;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VentasExport;
use PDF;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::query();

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

        if ($request->filled('cliente')) {
            $query->where('cliente_id', $request->cliente);
        }

        if ($request->filled('vehiculo')) {
            $query->where('vehiculo_id', $request->vehiculo);
        }

        if ($request->filled('tipo_venta')) {
            $query->where('tipo_venta', $request->tipo_venta);
        }

        if ($request->filled('usuario')) {
            $query->where('usuario_id', $request->usuario);
        }

        // Nuevo filtro para estado de la venta
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Nuevo filtro para estado de pago
        if ($request->filled('estado_pago')) {
            $query->where('estado_pago', $request->estado_pago);
        }

        $ventas = $query->with(['detalleVentas.articulo'])->orderBy('fecha', 'desc')->get();

        $clientes = Cliente::all();
        $vehiculos = Vehiculo::all();
        $usuarios = User::all();
        $config = Config::first();

        return view('admin.venta.index', compact('ventas', 'clientes', 'vehiculos', 'usuarios', 'config'));
    }

    public function create(Request $request)
    {
        $config = Config::first();
        $todosArticulos = Articulo::with('unidad')->get();
        $clientes = Cliente::all();
        $descuentos = Descuento::where('estado', 1)->get();

        // Obtener cliente_id de la URL si existe
        $cliente_id = $request->query('cliente_id');
        $cliente_seleccionado = null;

        if ($cliente_id) {
            $cliente_seleccionado = Cliente::find($cliente_id);
        }

        return view('admin.venta.create', compact(
            'todosArticulos',
            'clientes',
            'config',
            'descuentos',
            'cliente_id',
            'cliente_seleccionado'
        ));
    }

    /**
     * Actualiza el stock de un artículo y sus componentes si es un servicio
     *
     * @param int $articuloId ID del artículo
     * @param float $cantidad Cantidad a restar del stock
     * @param bool $restar True para restar, false para sumar al stock
     * @return void
     */
    private function actualizarStockArticulo($articuloId, $cantidad, $restar = true)
    {
        $articulo = Articulo::findOrFail($articuloId);

        // Actualizar el stock del artículo principal
        if ($restar) {
            $articulo->stock -= $cantidad;
        } else {
            $articulo->stock += $cantidad;
        }
        $articulo->save();

        // Si es un servicio, actualizar el stock de sus componentes
        if ($articulo->tipo === 'servicio') {
            $componentes = DB::table('servicio_articulo')
                ->where('servicio_id', $articuloId)
                ->get();

            foreach ($componentes as $componente) {
                $articuloComponente = Articulo::findOrFail($componente->articulo_id);
                $cantidadComponente = $componente->cantidad * $cantidad;

                if ($restar) {
                    $articuloComponente->stock -= $cantidadComponente;
                } else {
                    $articuloComponente->stock += $cantidadComponente;
                }

                $articuloComponente->save();
            }
        }
    }

    public function store(VentaFormRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            //datos de configuracion
            $config = Config::first();

            $venta = Venta::create(array_merge(
                $request->only('cliente_id', 'vehiculo_id', 'numero_factura', 'fecha', 'tipo_venta', 'estado', 'estado_pago'),
                ['usuario_id' => Auth::user()->id]
            ));

            foreach ($validated['detalles'] as $detalle) {
                // Agregar el porcentaje de impuestos desde la configuración
                $detalle['porcentaje_impuestos'] = $config->impuesto;

                // Obtener artículo y guardar precios
                $articulo = Articulo::find($detalle['articulo_id']);
                if ($articulo) {
                    // Agregar precios del artículo
                    $detalle['precio_costo'] = $articulo->precio_compra;
                    $detalle['precio_venta'] = $articulo->precio_venta;

                    // Actualizar el stock del artículo y sus componentes si es servicio
                    $this->actualizarStockArticulo($detalle['articulo_id'], $detalle['cantidad'], true);
                }

                // Se crea el detalle de la venta
                $venta->detalleVentas()->create($detalle);
            }

            DB::commit();
            // return redirect('ventas')->with('status', 'Venta registrada exitosamente.');
            return redirect('show-venta/'.$venta->id)->with('status',__('Usuario actualizado correctamente.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al registrar la venta: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $venta = Venta::with('pagos')->findOrFail($id);
        $config = Config::first();
        return view('admin.venta.show', compact('venta', 'config'));
    }

    public function edit($id)
    {
        $venta = Venta::findOrFail($id);
        $todosArticulos = Articulo::with('unidad')->get();
        $clientes = Cliente::all();
        $config = Config::first();
        $descuentos = Descuento::where('estado', 1)->get();
        return view('admin.venta.edit', compact('venta', 'todosArticulos', 'clientes', 'config', 'descuentos'));
    }

    public function update(VentaEditFormRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $venta = Venta::findOrFail($id);
            $venta->update($request->only('cliente_id', 'vehiculo_id', 'numero_factura', 'fecha', 'tipo_venta', 'estado', 'estado_pago'));

            //datos de configuracion
            $config = Config::first();

            // 1. Procesar detalles a eliminar
            $detallesAEliminar = $request->input('detalles_a_eliminar', []);
            if (is_array($detallesAEliminar) && count($detallesAEliminar) > 0) {
                \Log::info('Eliminando detalles de venta:', ['detalles_ids' => $detallesAEliminar]);

                foreach ($detallesAEliminar as $detalleId) {
                    $detalle = DetalleVenta::find($detalleId);
                    if ($detalle) {
                        \Log::info("Procesando eliminación detalle ID: {$detalleId}");

                        // Restaurar el stock del artículo y sus componentes si es servicio
                        $this->actualizarStockArticulo($detalle->articulo_id, $detalle->cantidad, false);
                        \Log::info("Stock actualizado para artículo ID: {$detalle->articulo_id}");

                        // Eliminar el detalle
                        $detalle->delete();
                        \Log::info("Detalle ID: {$detalleId} eliminado");
                    } else {
                        \Log::warning("Detalle ID: {$detalleId} no encontrado");
                    }
                }
            } else {
                \Log::info('No hay detalles a eliminar');
            }

            // 2. Procesar detalles a mantener (actualizar)
            if ($request->has('detalles_a_mantener')) {
                foreach ($request->detalles_a_mantener as $detalleId => $detalleData) {
                    $detalle = DetalleVenta::findOrFail($detalleId);
                    $cantidadAnterior = $detalle->cantidad;
                    $articuloIdAnterior = $detalle->articulo_id;

                    // Restaurar stock anterior
                    $this->actualizarStockArticulo($articuloIdAnterior, $cantidadAnterior, false);

                    // Actualizar el detalle
                    $detalle->update($detalleData);

                    // Descontar nuevo stock
                    $this->actualizarStockArticulo($detalleData['articulo_id'], $detalleData['cantidad'], true);
                }
            }

            // 3. Procesar nuevos detalles
            if ($request->has('nuevos_detalles')) {
                foreach ($request->nuevos_detalles as $nuevoDetalle) {
                    // Agregar el porcentaje de impuestos desde la configuración
                    $nuevoDetalle['porcentaje_impuestos'] = $config->impuesto;

                    // Obtener artículo y guardar precios
                    $articulo = Articulo::find($nuevoDetalle['articulo_id']);
                    if ($articulo) {
                        // Agregar precios del artículo
                        $nuevoDetalle['precio_costo'] = $articulo->precio_compra;
                        $nuevoDetalle['precio_venta'] = $articulo->precio_venta;

                        // Actualizar el stock del artículo y sus componentes si es servicio
                        $this->actualizarStockArticulo($nuevoDetalle['articulo_id'], $nuevoDetalle['cantidad'], true);
                    }

                    // Crear el nuevo detalle
                    $detalle = $venta->detalleVentas()->create($nuevoDetalle);
                }
            }

            DB::commit();
            return redirect('ventas')->with('status', 'Venta actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar la venta: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $venta = Venta::findOrFail($id);

            // Restaurar stock de todos los detalles antes de eliminar
            foreach ($venta->detalleVentas as $detalle) {
                // Restaurar stock del artículo y sus componentes si es servicio
                $this->actualizarStockArticulo($detalle->articulo_id, $detalle->cantidad, false);
            }

            // Eliminar detalles y marcar venta como eliminada
            $venta->update(['estado' => false]);

            DB::commit();

            return redirect('ventas')->with('status', 'Venta eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al eliminar la venta: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        // Obtener ventas con todos los datos relacionados necesarios
        $ventas = $this->getFilteredVentas($request)->load([
            'cliente',
            'vehiculo',
            'usuario',
            'detalleVentas.articulo.unidad',
            'detalleVentas.descuento',
            'pagos.usuario'
        ]);

        $config = Config::first();
        $clientes = Cliente::all();
        $usuarios = User::all();

        // Preparar datos de filtros para mostrarlos en el reporte
        $filters = [
            'fecha_desde' => $request->input('fecha_desde') ?? \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'),
            'fecha_hasta' => $request->input('fecha_hasta') ?? \Carbon\Carbon::now()->format('Y-m-d'),
            'numero_factura' => $request->input('numero_factura'),
            'cliente' => $request->filled('cliente') ? $clientes->find($request->cliente)->nombre : null,
            'tipo_venta' => $request->input('tipo_venta'),
            'usuario' => $request->filled('usuario') ? $usuarios->find($request->usuario)->name : null,
            'estado' => $request->filled('estado') ? ($request->estado == '1' ? 'Activa' : 'Cancelada') : null,
            'estado_pago' => $request->filled('estado_pago') ? ucfirst($request->estado_pago) : null,
        ];

        // Generar PDF
        $pdf = PDF::loadView('admin.venta.pdf', compact('ventas', 'config', 'filters'));
        $pdf->setPaper('a4', 'landscape'); // Usar formato apaisado para incluir más datos

        return $pdf->stream('reporte_ventas_'.date('Y-m-d').'.pdf');
    }

    public function exportSinglePdf($id)
    {
        // Cargar todos los datos relacionados necesarios para el PDF
        $venta = Venta::with([
            'detalleVentas.articulo.unidad',
            'detalleVentas.descuento',
            'cliente',
            'vehiculo',
            'usuario',
            'pagos.usuario'
        ])->findOrFail($id);

        $config = Config::first();

        // Calcular totales para el reporte
        $totales = $this->calcularTotalesVenta($venta);

        // Generar PDF
        $pdf = PDF::loadView('admin.venta.single_pdf', compact('venta', 'config', 'totales'));

        return $pdf->stream('venta_' . $venta->id . '_' . date('Y-m-d') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filename = 'reporte_ventas_'.date('Y-m-d').'.xlsx';
        return Excel::download(new VentasExport($request), $filename);
    }

    private function getFilteredVentas(Request $request)
    {
        $query = Venta::query();

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

        if ($request->filled('cliente')) {
            $query->where('cliente_id', $request->cliente);
        }

        if ($request->filled('vehiculo')) {
            $query->where('vehiculo_id', $request->vehiculo);
        }

        if ($request->filled('tipo_venta')) {
            $query->where('tipo_venta', $request->tipo_venta);
        }

        if ($request->filled('usuario')) {
            $query->where('usuario_id', $request->usuario);
        }

        // Filtros adicionales que hemos agregado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('estado_pago')) {
            $query->where('estado_pago', $request->estado_pago);
        }

        return $query->orderBy('fecha', 'desc')->get();
    }

    /**
     * Función auxiliar para calcular todos los totales de una venta
     */
    private function calcularTotalesVenta($venta)
    {
        $totalDescuentos = 0;
        $totalVenta = 0;
        $subtotalSinDescuentoTotal = 0;
        $totalImpuestos = 0;
        $totalCostoCompra = 0;
        $totalPagado = 0;

        // Calcular totales de los detalles
        foreach ($venta->detalleVentas as $detalle) {
            $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;
            $subtotalSinDescuentoTotal += $subtotalSinDescuento;

            // Calcular descuento
            $montoDescuento = 0;
            if ($detalle->descuento_id) {
                $descuento = $detalle->descuento;
                if ($descuento) {
                    $montoDescuento = $subtotalSinDescuento * ($descuento->porcentaje_descuento / 100);
                }
            }
            $totalDescuentos += $montoDescuento;

            // Subtotal con descuento
            $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;
            $totalVenta += $subtotalConDescuento;

            // Impuestos
            $impuestoDetalle = $subtotalConDescuento * ($detalle->porcentaje_impuestos ?? 0) / 100;
            $totalImpuestos += $impuestoDetalle;

            // Costo de compra
            $costoCompra = $detalle->precio_costo * $detalle->cantidad;
            $totalCostoCompra += $costoCompra;
        }

        // Calcular total pagado
        if ($venta->pagos) {
            $totalPagado = $venta->pagos->sum('monto');
        }

        // Ganancia neta
        $gananciaNeta = $totalVenta - $totalImpuestos - $totalCostoCompra;

        return compact(
            'totalDescuentos',
            'totalVenta',
            'subtotalSinDescuentoTotal',
            'totalImpuestos',
            'totalCostoCompra',
            'totalPagado',
            'gananciaNeta'
        );
    }
}
