<?php

namespace App\Exports;

use App\Models\Venta;
use App\Models\Config;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VentasExport implements FromCollection, WithHeadings
{
    protected $request;
    protected $config;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->config = Config::first();
    }

    public function collection()
    {
        $ventas = $this->getFilteredVentas($this->request);

        return $ventas->map(function ($venta) {
            return [
                'Fecha' => \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y'),
                'Número de Factura' => $venta->numero_factura,
                'Cliente' => $venta->cliente ? $venta->cliente->nombre : 'N/A',
                'Vehículo' => $venta->vehiculo ? $venta->vehiculo->marca . ' ' . $venta->vehiculo->modelo . ' (' . $venta->vehiculo->placa . ')' : 'N/A',
                'Tipo de Venta' => $venta->tipo_venta,
                'Estado' => $venta->estado ? 'Activa' : 'Cancelada',
                'Estado de Pago' => ucfirst($venta->estado_pago),
                'Usuario' => $venta->usuario ? $venta->usuario->name : 'N/A',
                'Total' => $this->config->simbolo_moneda . ' ' . number_format($this->calcularTotalVenta($venta), $this->config->numero_decimales_precio, '.', ','),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Número de Factura',
            'Cliente',
            'Vehículo',
            'Tipo de Venta',
            'Estado',
            'Estado de Pago',
            'Usuario',
            'Total',
        ];
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

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('estado_pago')) {
            $query->where('estado_pago', $request->estado_pago);
        }

        return $query->with(['cliente', 'vehiculo', 'usuario', 'detalleVentas'])->get();
    }

    private function calcularTotalVenta($venta)
    {
        $total = 0;
        foreach ($venta->detalleVentas as $detalle) {
            $subtotal = $detalle->cantidad * $detalle->precio_venta;
            
            // Aplicar descuento si existe
            if ($detalle->descuento_id && $detalle->descuento) {
                $subtotal = $subtotal * (1 - ($detalle->descuento->porcentaje_descuento / 100));
            }
            
            $total += $subtotal;
        }
        return $total;
    }
}
