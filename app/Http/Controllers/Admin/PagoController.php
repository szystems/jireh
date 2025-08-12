<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PagoFormRequest;
use App\Models\Pago;
use App\Models\Venta;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PagoController extends Controller
{
    /**
     * Muestra la lista de pagos con filtros y estadísticas.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Pago::with(['venta.cliente', 'usuario']);

        // Aplicar filtros
        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        // Obtener pagos paginados
        $pagos = $query->orderBy('fecha', 'desc')->paginate(15);

        // Calcular estadísticas
        $totalPagos = Pago::count();
        $montoTotal = Pago::sum('monto');
        
        // Pagos de esta semana
        $inicioSemana = Carbon::now()->startOfWeek();
        $finSemana = Carbon::now()->endOfWeek();
        $montoSemana = Pago::whereBetween('fecha', [$inicioSemana, $finSemana])->sum('monto');
        
        // Pagos de este mes
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();
        $montoMes = Pago::whereBetween('fecha', [$inicioMes, $finMes])->sum('monto');

        $config = Config::first();

        return view('admin.pago.index', compact(
            'pagos',
            'config',
            'totalPagos',
            'montoTotal',
            'montoSemana',
            'montoMes'
        ));
    }

    /**
     * Almacena un nuevo pago en la base de datos.
     *
     * @param  \App\Http\Requests\PagoFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PagoFormRequest $request)
    {
        // Validar datos usando PagoFormRequest
        $validated = $request->validated();

        // Manejar la imagen del comprobante si existe
        if ($request->hasFile('comprobante_imagen')) {
            $validated['comprobante_imagen'] = $this->handleComprobanteUpload($request);
        }

        // Si no se proporciona un usuario_id, usar el usuario autenticado
        if (!isset($validated['usuario_id'])) {
            $validated['usuario_id'] = Auth::id();
        }

        // Crear nuevo pago
        $pago = Pago::create($validated);

        // Actualizar el estado de pago de la venta si es necesario
        $this->actualizarEstadoPagoVenta($pago->venta_id);

        return redirect()->to('show-venta/' . $pago->venta_id)
            ->with('status', 'Pago registrado correctamente');
    }

    /**
     * Actualiza el pago especificado en la base de datos.
     *
     * @param  \App\Http\Requests\PagoFormRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PagoFormRequest $request, $id)
    {
        // Buscar el pago existente
        $pago = Pago::findOrFail($id);

        // Validar datos usando PagoFormRequest
        $validated = $request->validated();

        // Manejar la imagen del comprobante si existe
        if ($request->hasFile('comprobante_imagen')) {
            // Si hay un comprobante anterior, eliminarlo correctamente usando unlink
            if ($pago->comprobante_imagen) {
                $rutaCompleta = public_path($pago->comprobante_imagen);
                if (file_exists($rutaCompleta)) {
                    unlink($rutaCompleta);
                }
            }
            $validated['comprobante_imagen'] = $this->handleComprobanteUpload($request);
        }

        // Actualizar pago
        $pago->update($validated);

        // Actualizar el estado de pago de la venta si es necesario
        $this->actualizarEstadoPagoVenta($pago->venta_id);

        return redirect()->to('show-venta/' . $pago->venta_id)
            ->with('status', 'Pago actualizado correctamente');
    }

    /**
     * Elimina el pago especificado de la base de datos.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Buscar el pago
        $pago = Pago::findOrFail($id);
        $ventaId = $pago->venta_id;

        // Eliminar la imagen del comprobante si existe
        if ($pago->comprobante_imagen) {
            $rutaCompleta = public_path($pago->comprobante_imagen);
            if (file_exists($rutaCompleta)) {
                unlink($rutaCompleta);
            }
        }

        // Eliminar el pago
        $pago->delete();

        // Actualizar el estado de pago de la venta
        $this->actualizarEstadoPagoVenta($ventaId);

        return redirect()->to('show-venta/' . $ventaId)
            ->with('status', 'Pago eliminado correctamente');
    }

    /**
     * Actualiza el estado de pago de una venta basado en sus pagos.
     *
     * @param  int  $ventaId
     * @return void
     */
    private function actualizarEstadoPagoVenta($ventaId)
    {
        $venta = Venta::findOrFail($ventaId);

        // Calcular el total de la venta
        $totalVenta = 0;
        foreach ($venta->detalleVentas as $detalle) {
            $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

            $montoDescuento = 0;
            if ($detalle->descuento_id) {
                $descuento = \App\Models\Descuento::find($detalle->descuento_id);
                if ($descuento) {
                    $montoDescuento = $subtotalSinDescuento * ($descuento->porcentaje_descuento / 100);
                }
            }

            $totalVenta += ($subtotalSinDescuento - $montoDescuento);
        }

        // Calcular el total pagado
        $totalPagado = $venta->pagos()->sum('monto');

        // Actualizar el estado de pago - Solo usamos valores permitidos 'pagado' o 'pendiente'
        if ($totalPagado >= $totalVenta) {
            $venta->update(['estado_pago' => 'pagado']);
        } else {
            // Aquí cambiamos: incluso si hay pagos parciales, usamos 'pendiente'
            $venta->update(['estado_pago' => 'pendiente']);
        }
    }

    /**
     * Maneja la carga del archivo de comprobante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    private function handleComprobanteUpload(Request $request)
    {
        if ($request->hasFile('comprobante_imagen') && $request->file('comprobante_imagen')->isValid()) {
            // Asegurarse de que la carpeta existe
            $path = public_path('assets/imgs/pagos');
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }

            // Generar un nombre único para el archivo
            $fileName = time() . '_' . uniqid() . '.' . $request->comprobante_imagen->extension();

            // Mover el archivo a la ubicación deseada
            $request->comprobante_imagen->move($path, $fileName);

            // Retornar la ruta relativa para almacenar en la base de datos
            return 'assets/imgs/pagos/' . $fileName;
        }

        return null;
    }
}
