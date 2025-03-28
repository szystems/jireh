<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DetalleVenta;
use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Venta;
use App\Models\Trabajador;
use App\Models\User;
use App\Models\Config;
use App\Models\Cliente; // Añadir importación de Cliente
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Corregido: "." cambiado a "\"

class ReporteArticuloController extends Controller
{
    public function index(Request $request)
    {
        // Query base para detalles de venta con todas las relaciones necesarias
        $query = DetalleVenta::query()
            ->with([
                'articulo.categoria',
                'articulo.unidad',
                'venta.cliente',
                'venta.usuario',
                'trabajador',
                'usuario',
                'descuento'
            ])
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('articulos', 'articulos.id', '=', 'detalle_ventas.articulo_id');

        // Aplicar filtros
        // Filtro por rango de fechas
        if ($request->filled('fecha_desde')) {
            $query->where('ventas.fecha', '>=', $request->fecha_desde);
        } else {
            $query->where('ventas.fecha', '>=', Carbon::now()->subDays(30)->format('Y-m-d'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('ventas.fecha', '<=', $request->fecha_hasta);
        } else {
            $query->where('ventas.fecha', '<=', Carbon::now()->format('Y-m-d'));
        }

        // Filtro por artículo
        if ($request->filled('articulo')) {
            $query->where('detalle_ventas.articulo_id', $request->articulo);
        }

        // Filtro por categoría de artículo
        if ($request->filled('categoria')) {
            $query->where('articulos.categoria_id', $request->categoria);
        }

        // Filtro por trabajador (comisiones)
        if ($request->filled('trabajador')) {
            $query->where('detalle_ventas.trabajador_id', $request->trabajador);
        }

        // Filtro por usuario/vendedor
        if ($request->filled('usuario')) {
            $query->where('ventas.usuario_id', $request->usuario);
        }

        // Filtro por estado de venta
        if ($request->filled('estado')) {
            $query->where('ventas.estado', $request->estado);
        } else {
            // Por defecto solo mostrar ventas activas
            $query->where('ventas.estado', true);
        }

        // Filtro por tipo de venta
        if ($request->filled('tipo_venta')) {
            $query->where('ventas.tipo_venta', $request->tipo_venta);
        }

        // Filtro por cliente
        if ($request->filled('cliente')) {
            $query->where('ventas.cliente_id', $request->cliente);
        }

        // Filtro por código de artículo
        if ($request->filled('codigo')) {
            $query->where('articulos.codigo', 'like', '%' . $request->codigo . '%');
        }

        // Ordenar por fecha de venta (de más reciente a más antigua)
        $query->orderBy('ventas.fecha', 'desc')
              ->orderBy('articulos.nombre', 'asc');

        // Ejecutar la consulta y obtener los resultados
        $detallesVenta = $query->get();

        // Cargar datos para los filtros
        $articulos = Articulo::orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();
        $trabajadores = Trabajador::where('estado', 'activo')->orderBy('nombre')->get();
        $usuarios = User::orderBy('name')->get();
        $config = Config::first();
        $clientes = Cliente::orderBy('nombre')->get(); // Añadir carga de clientes

        // Calcular estadísticas
        $totalArticulosVendidos = $detallesVenta->sum('cantidad');
        $totalVentas = $detallesVenta->sum(function ($detalle) {
            $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

            $montoDescuento = 0;
            if ($detalle->descuento_id && $detalle->descuento) {
                $montoDescuento = $subtotalSinDescuento * ($detalle->descuento->porcentaje_descuento / 100);
            }

            return $subtotalSinDescuento - $montoDescuento;
        });

        $totalDescuentos = $detallesVenta->sum(function ($detalle) {
            $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

            $montoDescuento = 0;
            if ($detalle->descuento_id && $detalle->descuento) {
                $montoDescuento = $subtotalSinDescuento * ($detalle->descuento->porcentaje_descuento / 100);
            }

            return $montoDescuento;
        });

        // Calcular impuestos
        $totalImpuestos = $detallesVenta->sum(function ($detalle) {
            $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

            // Aplicar descuento si existe
            $montoDescuento = 0;
            if ($detalle->descuento_id && $detalle->descuento) {
                $montoDescuento = $subtotalSinDescuento * ($detalle->descuento->porcentaje_descuento / 100);
            }

            $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;

            // Calcular impuesto usando el porcentaje específico de este detalle
            return $subtotalConDescuento * ($detalle->porcentaje_impuestos ?? 0) / 100;
        });

        $totalComisionesTrabajador = $detallesVenta->sum(function ($detalle) {
            $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

            $montoDescuento = 0;
            if ($detalle->descuento_id && $detalle->descuento) {
                $montoDescuento = $subtotalSinDescuento * ($detalle->descuento->porcentaje_descuento / 100);
            }

            $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;
            $comision = 0;

            if ($detalle->tipo_comision_trabajador_id) {
                $tipoComision = \App\Models\TipoComision::find($detalle->tipo_comision_trabajador_id);
                if ($tipoComision) {
                    $comision = $subtotalConDescuento * ($tipoComision->porcentaje / 100);
                }
            }

            return $comision;
        });

        $totalComisionesVendedor = $detallesVenta->sum(function ($detalle) {
            $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

            $montoDescuento = 0;
            if ($detalle->descuento_id && $detalle->descuento) {
                $montoDescuento = $subtotalSinDescuento * ($detalle->descuento->porcentaje_descuento / 100);
            }

            $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;
            $comision = 0;

            if ($detalle->tipo_comision_usuario_id) {
                $tipoComision = \App\Models\TipoComision::find($detalle->tipo_comision_usuario_id);
                if ($tipoComision) {
                    $comision = $subtotalConDescuento * ($tipoComision->porcentaje / 100);
                }
            }

            return $comision;
        });

        $totalCostos = $detallesVenta->sum(function ($detalle) {
            return $detalle->precio_costo * $detalle->cantidad;
        });

        // Top 10 artículos más vendidos
        $topArticulos = $detallesVenta
            ->groupBy('articulo_id')
            ->map(function ($items, $key) {
                return [
                    'articulo' => $items->first()->articulo,
                    'cantidad' => $items->sum('cantidad'),
                    'total' => $items->sum(function ($item) {
                        $precioUnitario = $item->articulo ? $item->articulo->precio_venta : ($item->sub_total / $item->cantidad);
                        $subtotal = $precioUnitario * $item->cantidad;
                        $descuento = 0;
                        if ($item->descuento_id && $item->descuento) {
                            $descuento = $subtotal * ($item->descuento->porcentaje_descuento / 100);
                        }
                        return $subtotal - $descuento;
                    }),
                ];
            })
            ->sortByDesc('cantidad')
            ->take(10)
            ->values();

        // Agrupar por categoría para las estadísticas
        $ventasPorCategoria = $detallesVenta
            ->groupBy(function ($detalle) {
                return $detalle->articulo ? $detalle->articulo->categoria->nombre : 'Sin categoría';
            })
            ->map(function ($items, $categoria) {
                return [
                    'categoria' => $categoria,
                    'cantidad' => $items->sum('cantidad'),
                    'total' => $items->sum(function ($item) {
                        $precioUnitario = $item->articulo ? $item->articulo->precio_venta : ($item->sub_total / $item->cantidad);
                        $subtotal = $precioUnitario * $item->cantidad;
                        $descuento = 0;
                        if ($item->descuento_id && $item->descuento) {
                            $descuento = $subtotal * ($item->descuento->porcentaje_descuento / 100);
                        }
                        return $subtotal - $descuento;
                    }),
                    'porcentaje' => 0 // Se calculará después
                ];
            })
            ->sortByDesc('total')
            ->values();

        // Calcular porcentajes para el gráfico
        $totalVentasCategoria = $ventasPorCategoria->sum('total');
        $ventasPorCategoria = $ventasPorCategoria->map(function ($item) use ($totalVentasCategoria) {
            $item['porcentaje'] = $totalVentasCategoria > 0 ? round(($item['total'] / $totalVentasCategoria) * 100, 2) : 0;
            return $item;
        });

        return view('admin.reportearticulo.index', compact(
            'detallesVenta',
            'articulos',
            'categorias',
            'trabajadores',
            'usuarios',
            'config',
            'totalArticulosVendidos',
            'totalVentas',
            'totalDescuentos',
            'totalComisionesTrabajador',
            'totalComisionesVendedor',
            'totalCostos',
            'totalImpuestos',
            'topArticulos',
            'ventasPorCategoria',
            'request',
            'clientes' // Esta variable es necesaria para el filtro de clientes en search.blade.php
        ));
    }

    public function exportPdf(Request $request)
    {
        // Implementar generación de PDF con los mismos filtros de index
        $query = DetalleVenta::query()
            ->with([
                'articulo.categoria',
                'articulo.unidad',
                'venta.cliente',
                'venta.usuario',
                'trabajador',
                'usuario',
                'descuento'
            ])
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('articulos', 'articulos.id', '=', 'detalle_ventas.articulo_id');

        // Aplicar los mismos filtros que en index()
        if ($request->filled('fecha_desde')) {
            $query->where('ventas.fecha', '>=', $request->fecha_desde);
        } else {
            $query->where('ventas.fecha', '>=', Carbon::now()->subDays(30)->format('Y-m-d'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('ventas.fecha', '<=', $request->fecha_hasta);
        } else {
            $query->where('ventas.fecha', '<=', Carbon::now()->format('Y-m-d'));
        }

        if ($request->filled('articulo')) {
            $query->where('detalle_ventas.articulo_id', $request->articulo);
        }

        if ($request->filled('categoria')) {
            $query->where('articulos.categoria_id', $request->categoria);
        }

        if ($request->filled('trabajador')) {
            $query->where('detalle_ventas.trabajador_id', $request->trabajador);
        }

        if ($request->filled('usuario')) {
            $query->where('ventas.usuario_id', $request->usuario);
        }

        if ($request->filled('estado')) {
            $query->where('ventas.estado', $request->estado);
        } else {
            $query->where('ventas.estado', true);
        }

        if ($request->filled('tipo_venta')) {
            $query->where('ventas.tipo_venta', $request->tipo_venta);
        }

        if ($request->filled('cliente')) {
            $query->where('ventas.cliente_id', $request->cliente);
        }

        if ($request->filled('codigo')) {
            $query->where('articulos.codigo', 'like', '%' . $request->codigo . '%');
        }

        // Ordenar resultados
        $query->orderBy('ventas.fecha', 'desc')
              ->orderBy('articulos.nombre', 'asc');

        $detallesVenta = $query->get();
        $config = Config::first();

        // Agregar importación de Cliente igual que en el método index
        $clientes = Cliente::all(); // Añadir esta línea

        // Preparar filtros para mostrar en el PDF
        $filtros = [
            'fecha_desde' => $request->input('fecha_desde', Carbon::now()->subDays(30)->format('Y-m-d')),
            'fecha_hasta' => $request->input('fecha_hasta', Carbon::now()->format('Y-m-d')),
            'articulo' => $request->filled('articulo') ? Articulo::find($request->articulo)->nombre : null,
            'categoria' => $request->filled('categoria') ? Categoria::find($request->categoria)->nombre : null,
            'trabajador' => $request->filled('trabajador') ? Trabajador::find($request->trabajador)->nombre : null,
            'usuario' => $request->filled('usuario') ? User::find($request->usuario)->name : null,
            'estado' => $request->filled('estado') ? ($request->estado == '1' ? 'Activa' : 'Cancelada') : 'Activa',
            'tipo_venta' => $request->input('tipo_venta'),
            'cliente' => $request->filled('cliente') ? $clientes->find($request->cliente)->nombre ?? 'No encontrado' : null, // Actualizado
            'codigo' => $request->input('codigo'),
        ];

        // Calcular totales para el reporte
        $totalArticulosVendidos = $detallesVenta->sum('cantidad');
        $totalVentas = $detallesVenta->sum(function ($detalle) {
            $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

            $montoDescuento = 0;
            if ($detalle->descuento_id && $detalle->descuento) {
                $montoDescuento = $subtotalSinDescuento * ($detalle->descuento->porcentaje_descuento / 100);
            }

            return $subtotalSinDescuento - $montoDescuento;
        });

        $totalDescuentos = $detallesVenta->sum(function ($detalle) {
            $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

            $montoDescuento = 0;
            if ($detalle->descuento_id && $detalle->descuento) {
                $montoDescuento = $subtotalSinDescuento * ($detalle->descuento->porcentaje_descuento / 100);
            }

            return $montoDescuento;
        });

        $totalComisionesTrabajador = $detallesVenta->sum(function ($detalle) {
            $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

            $montoDescuento = 0;
            if ($detalle->descuento_id && $detalle->descuento) {
                $montoDescuento = $subtotalSinDescuento * ($detalle->descuento->porcentaje_descuento / 100);
            }

            $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;
            $comision = 0;

            if ($detalle->tipo_comision_trabajador_id) {
                $tipoComision = \App\Models\TipoComision::find($detalle->tipo_comision_trabajador_id);
                if ($tipoComision) {
                    $comision = $subtotalConDescuento * ($tipoComision->porcentaje / 100);
                }
            }

            return $comision;
        });

        $totalComisionesVendedor = $detallesVenta->sum(function ($detalle) {
            $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

            $montoDescuento = 0;
            if ($detalle->descuento_id && $detalle->descuento) {
                $montoDescuento = $subtotalSinDescuento * ($detalle->descuento->porcentaje_descuento / 100);
            }

            $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;
            $comision = 0;

            if ($detalle->tipo_comision_usuario_id) {
                $tipoComision = \App\Models\TipoComision::find($detalle->tipo_comision_usuario_id);
                if ($tipoComision) {
                    $comision = $subtotalConDescuento * ($tipoComision->porcentaje / 100);
                }
            }

            return $comision;
        });

        $totalCostos = $detallesVenta->sum(function ($detalle) {
            return $detalle->precio_costo * $detalle->cantidad;
        });

        // Agregar un nuevo cálculo específico para los impuestos
        $totalImpuestos = $detallesVenta->sum(function ($detalle) {
            $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

            // Aplicar descuento si existe
            $montoDescuento = 0;
            if ($detalle->descuento_id && $detalle->descuento) {
                $montoDescuento = $subtotalSinDescuento * ($detalle->descuento->porcentaje_descuento / 100);
            }

            $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;

            // Calcular impuesto usando el porcentaje específico de este detalle
            return $subtotalConDescuento * ($detalle->porcentaje_impuestos ?? 0) / 100;
        });

        // Actualizar el array de totales para incluir los impuestos calculados
        $totales = compact(
            'totalArticulosVendidos',
            'totalVentas',
            'totalDescuentos',
            'totalComisionesTrabajador',
            'totalComisionesVendedor',
            'totalCostos'
        );

        // Agregar el total de impuestos al array de totales
        $totales['totalImpuestos'] = $totalImpuestos;

        // Generar PDF
        $pdf = PDF::loadView('admin.reportearticulo.pdf', compact('detallesVenta', 'config', 'filtros', 'totales'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('reporte_articulos_'.date('Y-m-d').'.pdf');
    }
}
