<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Config;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use PDF;
use Excel;
use App\Exports\ArticulosExport;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Articulo::query();

        // Aplicar filtros a la consulta
        if ($request->filled('articulo')) {
            // Verificar si el valor de artículo es un ID numérico
            if (is_numeric($request->articulo)) {
                // Si es numérico, buscamos por ID exacto
                $query->where('id', $request->articulo);
            } else {
                // Si no es numérico, mantenemos la búsqueda por nombre o código
                $query->where(function($q) use ($request) {
                    $q->where('nombre', 'like', '%' . $request->articulo . '%')
                      ->orWhere('codigo', 'like', '%' . $request->articulo . '%');
                });
            }
        }

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        if ($request->filled('stock')) {
            if ($request->stock == 'con_stock') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock == 'sin_stock') {
                $query->where('stock', '=', 0);
            }
        }

        if ($request->filled('stock_minimo')) {
            if ($request->stock_minimo == '<=') {
                $query->whereColumn('stock', '<=', 'stock_minimo');
            } elseif ($request->stock_minimo == '>') {
                $query->whereColumn('stock', '>', 'stock_minimo');
            }
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('ordenar')) {
            $query->orderBy($request->ordenar);
        }

        $articulos = $query->get();
        $categorias = Categoria::all();
        $config = Config::first();

        // Agregar todos los artículos para el select2
        $todosArticulos = Articulo::select('id', 'codigo', 'nombre')
            ->orderBy('nombre')
            ->get();

        // Preparar datos para gráficas según los filtros aplicados
        $chartData = $this->prepareChartData($articulos, $request);

        return view('admin.inventario.index', compact('articulos', 'categorias', 'config', 'todosArticulos', 'chartData'));
    }

    /**
     * Prepara los datos para las gráficas según los filtros aplicados
     */
    private function prepareChartData($articulos, $request)
    {
        $chartData = [];

        // Datos para gráfica de valor de inventario por categoría
        $valorPorCategoria = [];
        foreach ($categorias = Categoria::all() as $categoria) {
            $valor = $articulos->where('categoria_id', $categoria->id)
                ->sum(function($articulo) {
                    return $articulo->precio_compra * $articulo->stock;
                });

            // Incluir todas las categorías que tienen artículos, aunque el valor sea 0
            if ($articulos->where('categoria_id', $categoria->id)->count() > 0) {
                $valorPorCategoria[$categoria->nombre] = $valor;
            }
        }

        // Si no hay datos, agregar datos de muestra para evitar errores
        if (empty($valorPorCategoria)) {
            $valorPorCategoria['Sin datos'] = 0;
        }

        // Ordenar por valor y limitar a las 5 principales categorías
        arsort($valorPorCategoria);
        $chartData['valorCategoria'] = [
            'labels' => array_keys(array_slice($valorPorCategoria, 0, 5, true)),
            'data' => array_values(array_slice($valorPorCategoria, 0, 5, true))
        ];

        // Si se filtra por categoría, agregar gráfica de precio promedio vs stock
        if ($request->filled('categoria')) {
            $categoria = Categoria::find($request->categoria);
            if ($categoria) {
                $chartData['categoriaNombre'] = $categoria->nombre;

                // Artículos con sus precios y stock para esta categoría
                $articulosCategoria = $articulos->where('categoria_id', $request->categoria)
                    ->sortBy('precio_venta')
                    ->take(10);

                $chartData['precioStock'] = [
                    'labels' => $articulosCategoria->pluck('nombre')->map(function($nombre) {
                        return \Illuminate\Support\Str::limit($nombre, 20);
                    })->toArray(),
                    'precios' => $articulosCategoria->pluck('precio_venta')->toArray(),
                    'stock' => $articulosCategoria->pluck('stock')->toArray()
                ];
            }
        }

        // Si se filtra por proveedor, agregar gráfica de distribución de productos
        if ($request->filled('proveedor_id')) {
            $proveedor = Proveedor::find($request->proveedor_id);
            if ($proveedor) {
                $chartData['proveedorNombre'] = $proveedor->nombre;

                // Distribución por tipo de artículo para este proveedor
                $tiposArticulos = $articulos->where('proveedor_id', $request->proveedor_id)
                    ->groupBy('tipo')
                    ->map(function($grupo) {
                        return $grupo->count();
                    })
                    ->toArray();

                $chartData['tiposProveedor'] = [
                    'labels' => array_keys($tiposArticulos),
                    'data' => array_values($tiposArticulos)
                ];
            }
        }

        // Si se filtra por stock, mostrar análisis detallado de stock
        if ($request->filled('stock') || $request->filled('stock_minimo')) {
            // Top 10 artículos por ratio stock/stock_minimo
            $articulosStockRatio = $articulos->filter(function($articulo) {
                    return $articulo->stock_minimo > 0; // Evitar división por cero
                })
                ->map(function($articulo) {
                    $articulo->ratio = $articulo->stock / $articulo->stock_minimo;
                    return $articulo;
                })
                ->sortBy('ratio')
                ->take(10);

            $chartData['stockRatio'] = [
                'labels' => $articulosStockRatio->pluck('nombre')->map(function($nombre) {
                    return \Illuminate\Support\Str::limit($nombre, 20);
                })->toArray(),
                'ratios' => $articulosStockRatio->pluck('ratio')->toArray(),
                'minimos' => $articulosStockRatio->pluck('stock_minimo')->toArray(),
                'actuales' => $articulosStockRatio->pluck('stock')->toArray()
            ];
        }

        // Si se filtra por tipo, mostrar análisis de precios por tipo
        if ($request->filled('tipo')) {
            $tipo = $request->tipo;
            $chartData['tipoSeleccionado'] = $tipo;

            // Promedio, mínimo y máximo de precios para este tipo
            $precioStats = [
                'promedio' => $articulos->where('tipo', $tipo)->avg('precio_venta'),
                'minimo' => $articulos->where('tipo', $tipo)->min('precio_venta'),
                'maximo' => $articulos->where('tipo', $tipo)->max('precio_venta')
            ];

            $chartData['precioStats'] = $precioStats;

            // Distribución de artículos por rango de precios
            $precioMin = floor($precioStats['minimo']);
            $precioMax = ceil($precioStats['maximo']);
            $rango = max(1, ($precioMax - $precioMin) / 5); // 5 rangos o 1 si son iguales

            $rangosPrecios = [];
            for ($i = 0; $i < 5; $i++) {
                $rangoInicio = $precioMin + ($i * $rango);
                $rangoFin = $precioMin + (($i + 1) * $rango);
                $etiqueta = number_format($rangoInicio, 2) . ' - ' . number_format($rangoFin, 2);

                $rangosPrecios[$etiqueta] = $articulos->where('tipo', $tipo)
                    ->filter(function($articulo) use ($rangoInicio, $rangoFin) {
                        return $articulo->precio_venta >= $rangoInicio &&
                               $articulo->precio_venta < $rangoFin;
                    })
                    ->count();
            }

            $chartData['distribucionPrecios'] = [
                'labels' => array_keys($rangosPrecios),
                'data' => array_values($rangosPrecios)
            ];
        }

        return $chartData;
    }

    public function printinventario(Request $request)
    {
        // Obtener artículos filtrados
        $articulos = $this->filterArticulos($request);
        $config = Config::first();

        // Obtener parámetros de configuración del PDF desde el modal
        $pdfTamano = $request->input('pdftamaño', 'Letter');
        $pdfOrientacion = $request->input('pdfhorientacion', 'portrait');
        $pdfArchivo = $request->input('pdfarchivo', 'stream');

        // Cargar la vista PDF con los artículos
        $pdf = PDF::loadView('admin.inventario.pdf', compact('articulos', 'config'));

        // Establecer el tamaño del papel
        $pdf->setPaper($pdfTamano, $pdfOrientacion);

        // Retornar el PDF según la opción seleccionada
        if ($pdfArchivo == 'download') {
            return $pdf->download('inventario_' . date('Y-m-d_H-i-s') . '.pdf');
        } else {
            return $pdf->stream('inventario_' . date('Y-m-d_H-i-s') . '.pdf');
        }
    }

    public function exportinventario(Request $request)
    {
        $articulos = $this->filterArticulos($request);
        return Excel::download(new ArticulosExport($articulos), 'inventario.xlsx');
    }

    private function filterArticulos(Request $request)
    {
        $query = Articulo::query();

        if ($request->filled('articulo')) {
            // Verificar si el valor de artículo es un ID numérico
            if (is_numeric($request->articulo)) {
                // Si es numérico, buscamos por ID exacto
                $query->where('id', $request->articulo);
            } else {
                // Si no es numérico, mantenemos la búsqueda por nombre o código
                $query->where(function($q) use ($request) {
                    $q->where('nombre', 'like', '%' . $request->articulo . '%')
                      ->orWhere('codigo', 'like', '%' . $request->articulo . '%');
                });
            }
        }

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->filled('stock')) {
            if ($request->stock == 'con_stock') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock == 'sin_stock') {
                $query->where('stock', '=', 0);
            }
        }

        if ($request->filled('stock_minimo')) {
            if ($request->stock_minimo == '<=') {
                $query->whereColumn('stock', '<=', 'stock_minimo');
            } elseif ($request->stock_minimo == '>') {
                $query->whereColumn('stock', '>', 'stock_minimo');
            }
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('ordenar')) {
            $query->orderBy($request->ordenar);
        }

        return $query->get();
    }

    /**
     * Buscar artículos para Select2 en el filtro
     */
    public function buscarArticulos(Request $request)
    {
        try {
            $busqueda = $request->input('q');

            \Log::info('Búsqueda de artículos: ' . $busqueda);

            if (empty($busqueda) || strlen($busqueda) < 2) {
                return response()->json([]);
            }

            $articulos = Articulo::where(function($query) use ($busqueda) {
                    $query->where('nombre', 'like', '%' . $busqueda . '%')
                          ->orWhere('codigo', 'like', '%' . $busqueda . '%');
                })
                ->select('id', 'codigo', 'nombre')
                ->orderBy('nombre')
                ->limit(10)
                ->get();

            \Log::info('Resultados encontrados: ' . $articulos->count());

            // Retornar los resultados directamente como array JSON
            return response()->json($articulos->toArray());
        } catch (\Exception $e) {
            \Log::error('Error en buscarArticulos: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
