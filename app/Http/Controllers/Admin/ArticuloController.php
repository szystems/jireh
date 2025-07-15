<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticuloFormRequest;
use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Trabajador;
use App\Models\Unidad;
use Illuminate\Http\Request;
use App\Models\Config;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $config = Config::first();

        // Obtener mecánicos disponibles (trabajadores con tipo que aplica comisiones)
        $mecanicos = Trabajador::whereHas('tipoTrabajador', function($query) {
            $query->where('aplica_comision', true)
                  ->where('requiere_asignacion', true);
        })->where('estado', 1)->get();

        return view('admin.articulo.add', compact('articulos', 'categorias', 'unidades', 'config', 'mecanicos'));
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

        // Validación adicional para mecánico si es un servicio
        if ($request->tipo == 'servicio' && $request->mecanico_id) {
            // Verificar que el mecánico sea válido y su tipo aplique comisiones
            $mecanico = Trabajador::find($request->mecanico_id);
            if (!$mecanico) {
                return back()->withErrors(['mecanico_id' => 'El mecánico seleccionado no existe.'])->withInput();
            }

            if (!$mecanico->tipoTrabajador || !$mecanico->tipoTrabajador->aplica_comision || !$mecanico->tipoTrabajador->requiere_asignacion) {
                return back()->withErrors(['mecanico_id' => 'El trabajador seleccionado no es un mecánico o no puede recibir comisiones.'])->withInput();
            }
        }

        // Crear el artículo con los campos adicionales
        $articulo = new Articulo($request->all());

        // Si no es un servicio, asegurarse que los campos de mecánico estén vacíos
        if ($request->tipo != 'servicio') {
            $articulo->mecanico_id = null;
            $articulo->costo_mecanico = 0;
            $articulo->comision_carwash = 0;
        }

        $articulo->save();

        // Si el tipo es servicio, guardar los artículos del servicio
        if ($request->tipo == 'servicio' && isset($request->articulos_servicio)) {
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
        try {
            // Cargamos el artículo con todas las relaciones necesarias de manera explícita
            $articulo = Articulo::with([
                'categoria',
                'unidad',
                'articulos.categoria',
                'articulos.unidad',
                'mecanico' // Cargar relación con mecánico
            ])->findOrFail($id);

            $config = Config::first();

            // Depuración avanzada
            if ($articulo->tipo == 'servicio') {
                Log::info('Servicio encontrado: ' . $articulo->id . ' - ' . $articulo->nombre);
                Log::info('Cantidad de componentes: ' . count($articulo->articulos));

                foreach($articulo->articulos as $componente) {
                    Log::info('Componente: ' . $componente->id . ' - ' . $componente->nombre . ', Cantidad: ' . $componente->pivot->cantidad);
                }
            }

            return view('admin.articulo.show', compact('articulo', 'config'));
        } catch (\Exception $e) {
            Log::error('Error al mostrar artículo: ' . $e->getMessage());
            return redirect('articulos')->with('error', 'Error al mostrar el artículo: ' . $e->getMessage());
        }
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
        $config = Config::first();

        // Obtener mecánicos disponibles
        $mecanicos = Trabajador::whereHas('tipoTrabajador', function($query) {
            $query->where('aplica_comision', true)
                  ->where('requiere_asignacion', true);
        })->where('estado', 1)->get();

        return view('admin.articulo.edit', compact('articulo', 'categorias', 'unidades', 'todosArticulos', 'config', 'mecanicos'));
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

        // Validación adicional para mecánico si es un servicio
        if ($request->tipo == 'servicio' && $request->mecanico_id) {
            // Verificar que el mecánico sea válido y su tipo aplique comisiones
            $mecanico = Trabajador::find($request->mecanico_id);
            if (!$mecanico) {
                return back()->withErrors(['mecanico_id' => 'El mecánico seleccionado no existe.'])->withInput();
            }

            if (!$mecanico->tipoTrabajador || !$mecanico->tipoTrabajador->aplica_comision || !$mecanico->tipoTrabajador->requiere_asignacion) {
                return back()->withErrors(['mecanico_id' => 'El trabajador seleccionado no es un mecánico o no puede recibir comisiones.'])->withInput();
            }
        }

        $articulo = Articulo::find($id);
        // Guardar el tipo original antes de aplicar los cambios
        $tipoOriginal = $articulo->tipo;
        $articulo->fill($request->all());

        // Si no es un servicio, asegurarse que los campos de mecánico estén vacíos
        if ($request->tipo != 'servicio') {
            $articulo->mecanico_id = null;
            $articulo->costo_mecanico = 0;
            $articulo->comision_carwash = 0;

            // NUEVO: Si cambió de tipo servicio a artículo, eliminar las relaciones de componentes
            if ($tipoOriginal == 'servicio') {
                $articulo->articulos()->detach();
                Log::info("Se eliminaron los componentes del artículo ID {$id} al cambiar de tipo servicio a artículo");
            }
        }

        $articulo->save();

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
        ];        $pdf = Pdf::loadView('admin.articulo.pdfarticulos', $data);
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
        // Cargamos el artículo con todas las relaciones necesarias
        $articulo = Articulo::with([
            'categoria',
            'unidad',
            'articulos.categoria',
            'articulos.unidad',
            'mecanico'  // Añadir relación con el mecánico asignado
        ])->findOrFail($id);

        $config = Config::first();

        // Si se necesita procesar la ubicación del logo, hazlo aquí
        if ($config && $config->logo) {
            // Asegurarse de que la ruta al logo es correcta
            // No es necesario manipular $config->logo si ya contiene solo el nombre del archivo
        }

        // Preparar datos para la vista PDF
        $data = [
            'articulo' => $articulo,
            'config' => $config,
            'fechaGeneracion' => now()->format('d/m/Y H:i:s'),
        ];

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

            // Información de las comisiones para servicios
            $data['costosComisiones'] = ($articulo->costo_mecanico ?? 0) + ($articulo->comision_carwash ?? 0);
        }

        // Cálculo de impuestos
        $impuesto = $config->impuesto ?? 0;
        $valorImpuesto = $articulo->precio_venta * ($impuesto / 100);

        // Cálculos de rentabilidad considerando comisiones si es servicio
        $ganancia = $articulo->precio_venta - $articulo->precio_compra;
        $costosComisiones = $articulo->tipo == 'servicio' ?
                            ($articulo->costo_mecanico ?? 0) + ($articulo->comision_carwash ?? 0) : 0;
        $gananciaReal = $ganancia - $valorImpuesto - $costosComisiones;

        // El margen se calcula sobre el costo total (precio de compra + comisiones)
        $costoTotal = $articulo->precio_compra + $costosComisiones;
        $margen = $costoTotal > 0 ? ($gananciaReal / $costoTotal) * 100 : 0;

        // Información del estado del stock
        $data['estadoStock'] = 'Disponible';
        if ($articulo->stock <= 0) {
            $data['estadoStock'] = 'Agotado';
        } elseif ($articulo->stock <= $articulo->stock_minimo) {
            $data['estadoStock'] = 'Stock bajo';
        }

        $data['impuesto'] = $impuesto;
        $data['valorImpuesto'] = $valorImpuesto;
        $data['gananciaReal'] = $gananciaReal;
        $data['margen'] = $margen;
        $data['costosComisiones'] = $costosComisiones;        $pdf = Pdf::loadView('admin.articulo.pdfarticulo', $data);

        // Configurar opciones del PDF
        $pdf->setPaper('a4');

        // Generar un nombre de archivo descriptivo
        $filename = 'articulo_' . Str::slug($articulo->nombre) . '_' . $articulo->id . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Proporciona datos de artículos para Select2 en formato API.
     */
    public function getArticulosParaVentaApi(Request $request)
    {
        $term = $request->input('q');
        $page = $request->input('page', 1);
        $tipoVenta = $request->input('tipo_venta'); // Podría usarse para filtrar más adelante

        $query = Articulo::with('unidad', 'tipoArticulo') // Cargar relaciones necesarias
                         ->where('estado', 1); // Solo artículos activos

        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'LIKE', '%' . $term . '%')
                  ->orWhere('codigo', 'LIKE', '%' . $term . '%');
            });
        }

        $articulos = $query->orderBy('nombre')->paginate(15); // Paginar resultados

        $config = Config::first(); // Para el símbolo de moneda y decimales

        $formattedArticulos = $articulos->map(function ($articulo) use ($config) {
            return [
                'id' => $articulo->id,
                'text' => $articulo->nombre .
                            ($articulo->codigo ? ' (Cod: ' . $articulo->codigo . ')' : '') .
                            " (" . ($config->simbolo_moneda ?? '$') . number_format($articulo->precio_venta, $config->numero_decimales_precio ?? 2) . ")",
                'stock_disponible' => $articulo->stock_disponible_venta, // Usar el accesor
                'unidad_abreviatura' => $articulo->unidad->abreviatura ?? '',
                'unidad_tipo' => $articulo->unidad->tipo ?? 'decimal',
                'tipo_articulo_nombre' => $articulo->tipoArticulo->nombre ?? 'producto', // Nombre del tipo de artículo
                'precio_venta' => (float) $articulo->precio_venta,
                'es_servicio' => ($articulo->tipoArticulo && strtolower($articulo->tipoArticulo->nombre) === 'servicio')
            ];
        });

        return response()->json([
            'data' => $formattedArticulos,
            'next_page_url' => $articulos->nextPageUrl() // Para la paginación de Select2
        ]);
    }
}
