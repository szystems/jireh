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

class ArticuloController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request)
        {
            $queryArticulo = $request->input('farticulo');

            $query = Articulo::where('estado', 1);

            if ($queryArticulo) {
                $query->where(function($q) use ($queryArticulo) {
                    $q->where('nombre', 'LIKE', "%{$queryArticulo}%")
                    ->orWhere('codigo', 'LIKE', "%{$queryArticulo}%");
                });
            }

            $articulos = $query->paginate(20);

            $config = Config::first();

            return view('admin.articulo.index', compact('articulos', 'queryArticulo', 'config'));
        }
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
        return view('admin.articulo.add', compact('articulos','categorias', 'unidades', 'tipoComisiones'));
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

        return view('admin.articulo.edit', compact('articulo', 'categorias', 'unidades', 'todosArticulos'));
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
}
