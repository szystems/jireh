<?php

namespace App\Exports;

use App\Models\Ingreso;
use App\Models\Config;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class IngresosExport implements FromCollection, WithHeadings
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
        $ingresos = $this->getFilteredIngresos($this->request);

        return $ingresos->map(function ($ingreso) {
            return [
                'Fecha' => \Carbon\Carbon::parse($ingreso->fecha)->format('d/m/Y'),
                'Número de Factura' => $ingreso->numero_factura,
                'Proveedor' => $ingreso->proveedor->nombre,
                'Tipo de Compra' => $ingreso->tipo_compra,
                'Usuario' => $ingreso->usuario ? $ingreso->usuario->name : 'N/A',
                'Total' => $this->config->currency_simbol . ' ' . number_format($ingreso->detalles->sum('total'), 2, '.', ','),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Número de Factura',
            'Proveedor',
            'Tipo de Compra',
            'Usuario',
            'Total',
        ];
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

        return $query->with(['detalles' => function ($query) {
            $query->selectRaw('ingreso_id, SUM(cantidad * precio_compra) as total')
                  ->groupBy('ingreso_id');
        }])->get();
    }
}
