<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReporteDepartamentoController extends Controller
{
    /**
     * Quién vendió cuánto en cada departamento.
     * Lee el departamento guardado en la factura. No recalcula comisiones.
     */
    public function index(Request $request)
    {
        $desde = $request->input('fecha_desde', Carbon::now()->startOfMonth()->toDateString());
        $hasta = $request->input('fecha_hasta', Carbon::now()->toDateString());
        $usuarioId = $request->input('usuario_id');

        $query = DB::table('ventas as v')
            ->leftJoin('users as u', 'u.id', '=', 'v.usuario_id')
            ->leftJoin('detalle_ventas as dv', 'dv.venta_id', '=', 'v.id')
            ->where('v.estado', 1)
            ->whereBetween('v.fecha', [$desde, $hasta]);

        if (Auth::user()->role_as == 1) {
            $query->where('v.usuario_id', Auth::id());
        } elseif ($usuarioId) {
            $query->where('v.usuario_id', $usuarioId);
        }

        $filas = $query
            ->groupBy('v.usuario_id', 'u.name', 'v.tipo_venta')
            ->orderBy('u.name')
            ->select(
                'v.usuario_id',
                'u.name as vendedor',
                'v.tipo_venta',
                DB::raw('COUNT(DISTINCT v.id) as facturas'),
                DB::raw('COALESCE(SUM(dv.sub_total), 0) as total')
            )
            ->get();

        $departamentos = Venta::departamentosActivos();
        $vendedores = [];

        foreach ($filas as $fila) {
            $id = $fila->usuario_id ?: 0;
            if (!isset($vendedores[$id])) {
                $vendedores[$id] = [
                    'nombre' => $fila->vendedor ?: 'Sin vendedor',
                    'totales' => [],
                    'facturas' => [],
                    'total' => 0.0,
                    'facturas_total' => 0,
                ];
            }

            $tipo = $fila->tipo_venta ?: '';
            if ($tipo !== '' && !array_key_exists($tipo, $departamentos)) {
                $departamentos[$tipo] = Venta::etiquetaDepartamento($tipo);
            }
            $vendedores[$id]['totales'][$tipo] = (float) $fila->total;
            $vendedores[$id]['facturas'][$tipo] = (int) $fila->facturas;
            $vendedores[$id]['total'] += (float) $fila->total;
            $vendedores[$id]['facturas_total'] += (int) $fila->facturas;
        }

        uasort($vendedores, function ($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        $totalesDepartamento = [];
        foreach (array_keys($departamentos) as $clave) {
            $totalesDepartamento[$clave] = 0.0;
        }
        $granTotal = 0.0;
        foreach ($vendedores as $vendedor) {
            $granTotal += $vendedor['total'];
            foreach ($vendedor['totales'] as $tipo => $monto) {
                if (!isset($totalesDepartamento[$tipo])) {
                    $totalesDepartamento[$tipo] = 0.0;
                }
                $totalesDepartamento[$tipo] += $monto;
            }
        }

        $usuarios = Auth::user()->role_as == 1
            ? collect()
            : User::orderBy('name')->get(['id', 'name']);

        $config = Config::first();

        return view('admin.reportes.por-departamento', compact(
            'desde',
            'hasta',
            'usuarioId',
            'departamentos',
            'vendedores',
            'totalesDepartamento',
            'granTotal',
            'usuarios',
            'config'
        ));
    }
}
