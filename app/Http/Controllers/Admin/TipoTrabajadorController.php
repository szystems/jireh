<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoTrabajador;
use App\Models\Trabajador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoTrabajadorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tipoTrabajadors = TipoTrabajador::paginate(10);
        return view('admin.tipotrabajador.index', compact('tipoTrabajadors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.tipotrabajador.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:tipo_trabajadors,nombre',
            'descripcion' => 'nullable|string',
            'aplica_comision' => 'boolean',
            'requiere_asignacion' => 'boolean',
            'estado' => 'required|in:activo,inactivo'
        ]);

        $tipoTrabajador = TipoTrabajador::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'aplica_comision' => $request->has('aplica_comision'),
            'requiere_asignacion' => $request->has('requiere_asignacion'),
            'estado' => $request->estado
        ]);

        return redirect('tipo-trabajador')->with('status', 'Tipo de trabajador creado exitosamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tipoTrabajador = TipoTrabajador::findOrFail($id);
        return view('admin.tipotrabajador.show', compact('tipoTrabajador'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tipoTrabajador = TipoTrabajador::findOrFail($id);
        return view('admin.tipotrabajador.edit', compact('tipoTrabajador'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $tipoTrabajador = TipoTrabajador::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255|unique:tipo_trabajadors,nombre,' . $id,
            'descripcion' => 'nullable|string',
            'aplica_comision' => 'boolean',
            'requiere_asignacion' => 'boolean',
            'estado' => 'required|in:activo,inactivo'
        ]);

        $tipoTrabajador->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'aplica_comision' => $request->has('aplica_comision'),
            'requiere_asignacion' => $request->has('requiere_asignacion'),
            'estado' => $request->estado
        ]);

        return redirect('tipo-trabajador')->with('status', 'Tipo de trabajador actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $tipoTrabajador = TipoTrabajador::findOrFail($id);

            // Verificar si hay trabajadores usando este tipo antes de eliminar
            if($tipoTrabajador->trabajadores()->count() > 0) {
                return redirect('tipo-trabajador')->with('error', 'No se puede eliminar este tipo de trabajador porque está siendo utilizado por trabajadores');
            }

            $tipoTrabajador->delete();

            return redirect('tipo-trabajador')->with('status', 'Tipo de trabajador eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect('tipo-trabajador')->with('error', 'Error al eliminar el tipo de trabajador: ' . $e->getMessage());
        }
    }
}
