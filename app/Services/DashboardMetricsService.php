<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Agregaciones del dashboard en SQL.
 *
 * Antes el dashboard cargaba todas las ventas y detalles a memoria PHP
 * (20+ veces por request). Eso agotaba PHP-FPM y provocaba Gateway Timeout.
 */
class DashboardMetricsService
{
    public function sumVentasPorPeriodo(string $fechaInicio, string $fechaFin, ?int $usuarioId = null): float
    {
        $query = DB::table('ventas')
            ->join('detalle_ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->whereBetween('ventas.fecha', [$fechaInicio, $fechaFin])
            ->where('ventas.estado', 1);

        if ($usuarioId) {
            $query->where('ventas.usuario_id', $usuarioId);
        }

        return (float) $query->sum('detalle_ventas.sub_total');
    }

    public function sumIngresosPorPeriodo(string $fechaInicio, string $fechaFin): float
    {
        $total = DB::table('ingresos')
            ->join('detalle_ingresos', 'detalle_ingresos.ingreso_id', '=', 'ingresos.id')
            ->whereBetween('ingresos.fecha', [$fechaInicio, $fechaFin])
            ->selectRaw('COALESCE(SUM(detalle_ingresos.precio_compra * detalle_ingresos.cantidad), 0) as total')
            ->value('total');

        return (float) $total;
    }

    /**
     * Totales de los últimos N meses en una sola consulta.
     *
     * @return array{totales: float[], labels: string[]}
     */
    public function ventasAgrupadasPorMes(int $meses = 12, ?int $usuarioId = null): array
    {
        $inicio = Carbon::now()->subMonths($meses - 1)->startOfMonth();
        $fin = Carbon::now()->endOfMonth();
        $monthExpr = $this->sqlYearMonth('ventas.fecha');

        $query = DB::table('ventas')
            ->join('detalle_ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->whereBetween('ventas.fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->where('ventas.estado', 1)
            ->selectRaw("{$monthExpr} as periodo, COALESCE(SUM(detalle_ventas.sub_total), 0) as total")
            ->groupBy(DB::raw($monthExpr));

        if ($usuarioId) {
            $query->where('ventas.usuario_id', $usuarioId);
        }

        $rows = $query->pluck('total', 'periodo');

        $totales = [];
        $labels = [];
        for ($i = $meses - 1; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i)->startOfMonth();
            $totales[] = (float) ($rows[$fecha->format('Y-m')] ?? 0);
            $labels[] = $fecha->locale('es')->format('M Y');
        }

        return ['totales' => $totales, 'labels' => $labels];
    }

    /**
     * Totales diarios de los últimos N días en una sola consulta.
     *
     * @return array<int, array{fecha: string, dia: string, ventas: float}>
     */
    public function ventasAgrupadasPorDia(int $dias = 7, ?int $usuarioId = null): array
    {
        $inicio = Carbon::now()->subDays($dias - 1)->startOfDay();
        $fin = Carbon::now()->endOfDay();
        $dayExpr = $this->sqlYearMonthDay('ventas.fecha');

        $query = DB::table('ventas')
            ->join('detalle_ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->whereBetween('ventas.fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->where('ventas.estado', 1)
            ->selectRaw("{$dayExpr} as dia, COALESCE(SUM(detalle_ventas.sub_total), 0) as total")
            ->groupBy(DB::raw($dayExpr));

        if ($usuarioId) {
            $query->where('ventas.usuario_id', $usuarioId);
        }

        $rows = $query->pluck('total', 'dia');

        $resultado = [];
        for ($i = $dias - 1; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $key = $fecha->toDateString();
            $resultado[] = [
                'fecha' => $key,
                'dia' => $fecha->locale('es')->format('D'),
                'ventas' => (float) ($rows[$key] ?? 0),
            ];
        }

        return $resultado;
    }

    public function valorInventario(): float
    {
        $total = DB::table('articulos')
            ->where('stock', '>', 0)
            ->selectRaw('COALESCE(SUM(stock * precio_venta), 0) as total')
            ->value('total');

        return (float) $total;
    }

    /**
     * @return array<int|string, float>
     */
    public function ventasPorVendedorEnMes(int $year, int $month): array
    {
        $inicio = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
        $fin = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();

        return DB::table('ventas')
            ->join('detalle_ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->whereBetween('ventas.fecha', [$inicio, $fin])
            ->where('ventas.estado', 1)
            ->groupBy('ventas.usuario_id')
            ->selectRaw('ventas.usuario_id, COALESCE(SUM(detalle_ventas.sub_total), 0) as total')
            ->pluck('total', 'usuario_id')
            ->map(function ($total) {
                return (float) $total;
            })
            ->all();
    }

    private function sqlYearMonth(string $column): string
    {
        return DB::connection()->getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', {$column})"
            : "DATE_FORMAT({$column}, '%Y-%m')";
    }

    private function sqlYearMonthDay(string $column): string
    {
        return DB::connection()->getDriverName() === 'sqlite'
            ? "date({$column})"
            : "DATE_FORMAT({$column}, '%Y-%m-%d')";
    }
}
