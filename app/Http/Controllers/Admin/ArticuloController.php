<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticuloFormRequest;
use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Unidad;
use App\Models\TipoComision;
use Illuminate\Http\Request;
use App\Models\Config;
use Illuminate\Support\Str; // Añadir esta importación
use PDF;

class ArticuloController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $queryArticulo = $request->input('farticulo');
        $queryTipo = $request->input('ftipo');
        $queryCategoria = $request->input('fcategoria');
        $queryStock = $request->input('fstock');
        $queryOrden = $request->input('forden');

        $query = Articulo::where('estado', 1);

        // Aplicar filtro de búsqueda por nombre o código
        if ($queryArticulo) {
            $query->where(function($q) use ($queryArticulo) {
                $q->where('nombre', 'LIKE', "%{$queryArticulo}%")
                  ->orWhere('codigo', 'LIKE', "%{$queryArticulo}%");
            });
        }

        // Filtrar por tipo (artículo o servicio)
        if ($queryTipo) {
            $query->where('tipo', $queryTipo);
        }

        // Filtrar por categoría
        if ($queryCategoria) {
            $query->where('categoria_id', $queryCategoria);
        }

        // Filtrar por estado de stock
        if ($queryStock) {
            if ($queryStock == 'disponible') {
                $query->where('stock', '>', function ($q) {
                    $q->select('stock_minimo')
                      ->from('articulos')
                      ->whereColumn('id', 'articulos.id');
                });
            } elseif ($queryStock == 'bajo') {
                $query->where('stock', '>', 0)
                     ->where('stock', '<=', function ($q) {
                         $q->select('stock_minimo')
                           ->from('articulos')
                           ->whereColumn('id', 'articulos.id');
                     });
            } elseif ($queryStock == 'agotado') {
                $query->where('stock', '<=', 0);
            }
        }

        // Aplicar ordenamiento
        if ($queryOrden) {
            switch ($queryOrden) {
                case 'nombre_asc':
                    $query->orderBy('nombre', 'asc');
                    break;
                case 'nombre_desc':
                    $query->orderBy('nombre', 'desc');
                    break;
                case 'precio_asc':
                    $query->orderBy('precio_venta', 'asc');
                    break;
                case 'precio_desc':
                    $query->orderBy('precio_venta', 'desc');
                    break;
                case 'stock_asc':
                    $query->orderBy('stock', 'asc');
                    break;
                case 'stock_desc':
                    $query->orderBy('stock', 'desc');
                    break;
                default:
                    $query->orderBy('nombre', 'asc');
                    break;
            }
        } else {
            // Ordenamiento predeterminado
            $query->orderBy('nombre', 'asc');
        }

        $articulos = $query->paginate(20);

        // Obtener estadísticas para el dashboard
        $estadisticas = [
            'total_articulos' => Articulo::where('estado', 1)->where('tipo', 'articulo')->count(),
            'total_servicios' => Articulo::where('estado', 1)->where('tipo', 'servicio')->count(),
            'stock_bajo' => Articulo::where('estado', 1)
                                    ->where('tipo', 'articulo')
                                    ->where('stock', '>', 0)
                                    ->whereRaw('stock <= stock_minimo')
                                    ->count(),
            'sin_stock' => Articulo::where('estado', 1)
                                  ->where('tipo', 'articulo')
                                  ->where('stock', '<=', 0)
                                  ->count(),
        ];

        $config = Config::first();
        $categorias = Categoria::where('estado', 1)->get();

        // Preferencia de visualización (tabla/tarjetas)
        $viewMode = $request->session()->get('articulos_view_mode', 'table');

        // Filtros aplicados para mantenerlos en la vista
        $filtros = [
            'ftipo' => $queryTipo,
            'fcategoria' => $queryCategoria,
            'fstock' => $queryStock,
            'forden' => $queryOrden
        ];

        return view('admin.articulo.index', compact(
            'articulos',
            'queryArticulo',
            'config',
            'estadisticas',
            'categorias',
            'viewMode',
            'filtros'
        ));
    }

    /**
     * Guarda la preferencia de visualización del usuario
     */
    public function setViewPreference(Request $request)
    {
        $preference = $request->input('preference');
        $value = $request->input('value');

        if ($preference && $value) {
            $request->session()->put($preference, $value);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add()
    {
        $categorias = Categoria::where('estado', 1)->get();
        $articulos = Articulo::where('estado', 1)->get();
        $unidades = Unidad::where('estado', 1)->get();
        $tipoComisiones = TipoComision::where('estado', 1)->get();
        $config = Config::first();
        return view('admin.articulo.add', compact('articulos','categorias', 'unidades', 'tipoComisiones', 'config'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ArticuloFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function insert(Request $request)
    {
        $request->validate([
            'codigo' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $exists = \App\Models\Articulo::where('codigo', $value)->exists();
                        if ($exists) {
                            $fail('El campo código ya ha sido tomado.');
                        }
                    }
                },
            ],
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_compra' => 'required|numeric',
            'precio_venta' => 'required|numeric',
            'stock' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'unidad_id' => 'required|exists:unidads,id',
            'tipo_comision_vendedor_id' => 'required|exists:tipo_comisions,id',
            'tipo_comision_trabajador_id' => 'required|exists:tipo_comisions,id',
            'tipo' => 'required|in:articulo,servicio',
            'estado' => 'boolean',
            'cantidades.*' => 'required|numeric|min:0',
            'articulos_servicio' => [
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->tipo == 'servicio' && empty($value)) {
                        $fail('Debe agregar al menos un artículo al servicio.');
                    }
                },
            ],
        ]);

        // Si el tipo es servicio, verificar que el array de artículos del servicio no esté vacío
        if ($request->tipo == 'servicio' && empty($request->articulos_servicio)) {
            return back()->withErrors(['articulos_servicio' => 'Debe agregar al menos un artículo al servicio.'])->withInput();
        }

        // Crear el artículo
        $articulo = Articulo::create($request->all());

        // Si el tipo es servicio, guardar los artículos del servicio
        if ($request->tipo == 'servicio') {
            foreach ($request->articulos_servicio as $articulo_id => $cantidad) {
                $articulo->articulos()->attach($articulo_id, ['cantidad' => $cantidad]);
            }
        }

        return redirect('articulos')->with('status', __('Artículo agregado exitosamente.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Articulo  $articulo
     * @return \Illuminate\Http\Response
     */
    // public function show(Articulo $articulo)
    // {
    //     dd($articulo);
    //     return view('admin.articulo.show', compact('articulo'));
    // }
    public function show($id)
    {
        $articulo = Articulo::find($id);
        $config = Config::first();
        return view('admin.articulo.show', compact('articulo', 'config'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Articulo  $articulo
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        $articulo = Articulo::with('articulos')->find($id);
        $categorias = Categoria::where('estado', 1)->get();
        $unidades = Unidad::where('estado', 1)->get();
        $todosArticulos = Articulo::where('tipo', 'articulo')->get(); // Obtener todos los artículos que no son servicios
        $tipoComisiones = TipoComision::where('estado', 1)->get();
        $config = Config::first();

        return view('admin.articulo.edit', compact('articulo', 'categorias', 'unidades', 'todosArticulos', 'tipoComisiones', 'config'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'codigo' => [
                'nullable',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($id) {
                    if (!empty($value)) {
                        $exists = \App\Models\Articulo::where('codigo', $value)
                            ->where('id', '!=', $id)
                            ->exists();
                        if ($exists) {
                            $fail('El campo código ya ha sido tomado.');
                        }
                    }
                },
            ],
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'unidad_id' => 'required|exists:unidads,id',
            'tipo' => 'required|in:articulo,servicio',
        ]);

        $articulo = Articulo::find($id);
        $articulo->update($request->all());

        // Si el tipo es servicio, actualizar los artículos del servicio
        if ($request->tipo == 'servicio') {
            $articulosData = [];

            // Actualizar artículos existentes
            if ($request->has('articulos_servicio_existentes')) {
                foreach ($request->articulos_servicio_existentes as $data) {
                    if (isset($data['id']) && isset($data['cantidad'])) {
                        $articulo_id = $data['id'];
                        $cantidad = $data['cantidad'];
                        if (is_numeric($articulo_id)) {
                            // Agregar al array de artículos con su cantidad
                            $articulosData[(int)$articulo_id] = ['cantidad' => $cantidad];
                        }
                    }
                }
            }

            // Agregar nuevos artículos
            if ($request->has('articulos_servicio')) {
                foreach ($request->articulos_servicio['new']['id'] as $index => $articulo_id) {
                    $cantidad = $request->articulos_servicio['new']['cantidad'][$index] ?? null;
                    if ($cantidad !== null && is_numeric($articulo_id)) {
                        // Agregar al array de artículos con su cantidad
                        $articulosData[(int)$articulo_id] = ['cantidad' => $cantidad];
                    }
                }
            }

            // Sincronizar artículos
            $articulo->articulos()->sync($articulosData);
        }

        return redirect('show-articulo/'.$id)->with('status', __('Artículo actualizado exitosamente.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Articulo  $articulo
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $articulo = Articulo::find($id);
        $articulo->codigo = null;
        $articulo->estado = 0;
        $articulo->update();
        return redirect('articulos')->with('status',__('Artículo eliminado correctamente.'));
    }

    /**
     * Exporta los artículos filtrados a PDF
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Request $request)
    {
        $queryArticulo = $request->input('farticulo');
        $queryTipo = $request->input('ftipo');
        $queryCategoria = $request->input('fcategoria');
        $queryStock = $request->input('fstock');
        $queryOrden = $request->input('forden');

        $query = Articulo::where('estado', 1);

        // Aplicar filtro de búsqueda por nombre o código
        if ($queryArticulo) {
            $query->where(function($q) use ($queryArticulo) {
                $q->where('nombre', 'LIKE', "%{$queryArticulo}%")
                  ->orWhere('codigo', 'LIKE', "%{$queryArticulo}%");
            });
        }

        // Filtrar por tipo (artículo o servicio)
        if ($queryTipo) {
            $query->where('tipo', $queryTipo);
        }

        // Filtrar por categoría
        if ($queryCategoria) {
            $query->where('categoria_id', $queryCategoria);
        }

        // Filtrar por estado de stock
        if ($queryStock) {
            if ($queryStock == 'disponible') {
                $query->where('stock', '>', function ($q) {
                    $q->select('stock_minimo')
                      ->from('articulos')
                      ->whereColumn('id', 'articulos.id');
                });
            } elseif ($queryStock == 'bajo') {
                $query->where('stock', '>', 0)
                     ->where('stock', '<=', function ($q) {
                         $q->select('stock_minimo')
                           ->from('articulos')
                           ->whereColumn('id', 'articulos.id');
                     });
            } elseif ($queryStock == 'agotado') {
                $query->where('stock', '<=', 0);
            }
        }

        // Aplicar ordenamiento
        if ($queryOrden) {
            switch ($queryOrden) {
                case 'nombre_asc':
                    $query->orderBy('nombre', 'asc');
                    break;
                case 'nombre_desc':
                    $query->orderBy('nombre', 'desc');
                    break;
                case 'precio_asc':
                    $query->orderBy('precio_venta', 'asc');
                    break;
                case 'precio_desc':
                    $query->orderBy('precio_venta', 'desc');
                    break;
                case 'stock_asc':
                    $query->orderBy('stock', 'asc');
                    break;
                case 'stock_desc':
                    $query->orderBy('stock', 'desc');
                    break;
                default:
                    $query->orderBy('nombre', 'asc');
                    break;
            }
        } else {
            // Ordenamiento predeterminado
            $query->orderBy('nombre', 'asc');
        }

        $articulos = $query->get();
        $config = Config::first();

        // Preparamos los filtros para mostrarlos en el PDF
        $filtrosAplicados = [];
        if ($queryArticulo) {
            $filtrosAplicados[] = "Búsqueda: '{$queryArticulo}'";
        }
        if ($queryTipo) {
            $tipoTexto = $queryTipo == 'articulo' ? 'Artículo' : 'Servicio';
            $filtrosAplicados[] = "Tipo: {$tipoTexto}";
        }
        if ($queryCategoria) {
            $categoria = Categoria::find($queryCategoria);
            if ($categoria) {
                $filtrosAplicados[] = "Categoría: {$categoria->nombre}";
            }
        }
        if ($queryStock) {
            $stockTexto = '';
            switch ($queryStock) {
                case 'disponible':
                    $stockTexto = 'Disponible';
                    break;
                case 'bajo':
                    $stockTexto = 'Stock bajo';
                    break;
                case 'agotado':
                    $stockTexto = 'Sin stock';
                    break;
            }
            $filtrosAplicados[] = "Stock: {$stockTexto}";
        }

        $fechaActual = now()->format('d/m/Y H:i:s');
        $data = [
            'articulos' => $articulos,
            'config' => $config,
            'filtrosAplicados' => $filtrosAplicados,
            'fechaGeneracion' => $fechaActual,
            'totalArticulos' => $articulos->count(),
        ];

        $pdf = PDF::loadView('admin.articulo.pdfarticulos', $data);
        return $pdf->stream('articulos_' . now()->format('YmdHis') . '.pdf');
    }

    /**
     * Exporta un artículo individual a PDF
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function exportPdfSingle($id)
    {
        $articulo = Articulo::with(['categoria', 'unidad', 'tipoComisionVendedor', 'tipoComisionTrabajador', 'articulos.categoria', 'articulos.unidad'])
            ->findOrFail($id);
        $config = Config::first();

        // Preparar datos para la vista PDF
        $data = [
            'articulo' => $articulo,
            'config' => $config,
            'fechaGeneracion' => now()->format('d/m/Y H:i:s'),
        ];

        // Si es un servicio, calcular el costo total de los componentes
        if ($articulo->tipo == 'servicio') {
            $totalCosto = 0;
            foreach ($articulo->articulos as $articuloServicio) {
                $totalCosto += $articuloServicio->precio_compra * $articuloServicio->pivot->cantidad;
            }
            $data['totalCosto'] = $totalCosto;
        }

        // Cálculo de comisiones e impuestos
        $comisionVendedor = $articulo->tipoComisionVendedor->porcentaje ?? 0;
        $comisionTrabajador = $articulo->tipoComisionTrabajador->porcentaje ?? 0;
        $impuesto = $config->impuesto ?? 0;

        $valorComisionVendedor = $articulo->precio_venta * ($comisionVendedor / 100);
        $valorComisionTrabajador = $articulo->precio_venta * ($comisionTrabajador / 100);
        $valorImpuesto = $articulo->precio_venta * ($impuesto / 100);

        $ganancia = $articulo->precio_venta - $articulo->precio_compra;
        $gananciaReal = $ganancia - $valorComisionVendedor - $valorComisionTrabajador - $valorImpuesto;
        $margen = $articulo->precio_compra > 0 ? (($gananciaReal) / $articulo->precio_compra) * 100 : 0;

        $data['comisionVendedor'] = $comisionVendedor;
        $data['comisionTrabajador'] = $comisionTrabajador;
        $data['impuesto'] = $impuesto;
        $data['valorComisionVendedor'] = $valorComisionVendedor;
        $data['valorComisionTrabajador'] = $valorComisionTrabajador;
        $data['valorImpuesto'] = $valorImpuesto;
        $data['gananciaReal'] = $gananciaReal;
        $data['margen'] = $margen;

        $pdf = PDF::loadView('admin.articulo.pdfarticulo', $data);

        // Configurar opciones del PDF
        $pdf->setPaper('a4');

        // Generar un nombre de archivo descriptivo
        $filename = 'articulo_' . Str::slug($articulo->nombre) . '_' . $articulo->id . '.pdf';

        return $pdf->stream($filename);
    }
}
