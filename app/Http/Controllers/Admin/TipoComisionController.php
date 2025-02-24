<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TipoComision;
use App\Http\Requests\TipoComisionFormRequest;
use DB;

class TipoComisionController extends Controller
{
    public function index(Request $request)
    {
        if ($request)
        {
            $queryTipoComision=$request->input('ftipocomision');
            $tipocomisiones = DB::table('tipo_comisions')
            ->where('estado', '=', true)
            ->where('nombre', 'LIKE', '%' . $queryTipoComision . '%')
            ->orderBy('nombre' , 'asc')
            ->paginate(20);
            return view('admin.tipocomision.index', compact('tipocomisiones','queryTipoComision'));
        }

    }

    public function add()
    {
        return view('admin.tipocomision.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\TipoComisionFormRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function insert(TipoComisionFormRequest $request)
    {
        $tipocomision = new TipoComision();
        $tipocomision->nombre = $request->input('nombre');
        $tipocomision->descripcion = $request->input('descripcion');
        $tipocomision->porcentaje = $request->input('porcentaje');
        $tipocomision->save();
        return redirect('tipo-comisiones')->with('status',__('Tipo de comision creada exitosamente.'));
    }

    public function show($id)
    {
        $tipocomision = TipoComision::find($id);
        return view('admin.tipocomision.show', compact('tipocomision'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TipoComision  $tipocomision
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tipocomision = TipoComision::findOrFail($id);
        return view('admin.tipocomision.edit', compact('tipocomision'));
    }

    public function update(TipoComisionFormRequest $request, $id)
    {
        // $tipocomision = TipoComision::find($id);
        // $tipocomision->nombre = $request->input('nombre');
        // $tipocomision->abreviatura = $request->input('abreviatura');
        // $tipocomision->tipo = $request->input('tipo');
        // $tipocomision->update();
        $tipocomision = TipoComision::findOrFail($id);
        $tipocomision->update($request->validated());
        return redirect('show-tipo-comision/'.$id)->with('status',__('Tipo de comision actualizada correctamente.'));
    }

    public function destroy($id)
    {
        $tipocomision = TipoComision::find($id);
        $tipocomision->estado = false;
        $tipocomision->update();
        return redirect('tipo-comisiones')->with('status',__('Tipo de comision eliminada correctamente.'));
    }
}
