<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UnidadFormRequest;
use App\Models\Unidad;
use Illuminate\Http\Request;
use DB;

class UnidadController extends Controller
{

    public function index(Request $request)
    {
        if ($request)
        {
            $queryUnidad=$request->input('funidad');
            $unidades = DB::table('unidads')
            ->where('estado', '=', 1)
            ->where('nombre', 'LIKE', '%' . $queryUnidad . '%')
            ->orderBy('nombre' , 'asc')
            ->paginate(20);
            return view('admin.unidad.index', compact('unidades','queryUnidad'));
        }

    }

    public function add()
    {
        return view('admin.unidad.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\UnidadFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function insert(UnidadFormRequest $request)
    {
        $unidad = new Unidad();
        $unidad->nombre = $request->input('nombre');
        $unidad->abreviatura = $request->input('abreviatura');
        $unidad->tipo = $request->input('tipo');
        $unidad->save();
        // Unidad::create($request->validated());
        return redirect('show-unidad/'.$unidad->id)->with('status',__('Unidad de medida creada exitosamente.'));
    }

    public function show($id)
    {
        $unidad = Unidad::find($id);
        return view('admin.unidad.show', compact('unidad'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Unidad  $unidad
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $unidad = Unidad::findOrFail($id);
        return view('admin.unidad.edit', compact('unidad'));
    }

    public function update(UnidadFormRequest $request, $id)
    {
        // $unidad = Unidad::find($id);
        // $unidad->nombre = $request->input('nombre');
        // $unidad->abreviatura = $request->input('abreviatura');
        // $unidad->tipo = $request->input('tipo');
        // $unidad->update();
        $unidad = Unidad::findOrFail($id);
        $unidad->update($request->validated());
        return redirect('show-unidad/'.$id)->with('status',__('Unidad de medida actualizada correctamente.'));
    }

    public function destroy($id)
    {
        $unidad = Unidad::find($id);
        $unidad->estado = 0;
        $unidad->update();
        return redirect('unidades')->with('status',__('Unidad de medida eliminada correctamente.'));
    }
}
