<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trabajador;
use App\Models\TipoTrabajador;
use Illuminate\Http\Request;

class TrabajadorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Iniciar la consulta
        $query = Trabajador::with('tipoTrabajador');

        // Filtro por texto de búsqueda
        if ($request->has('buscar') && $request->buscar != '') {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('apellido', 'LIKE', "%{$buscar}%")
                  ->orWhere('telefono', 'LIKE', "%{$buscar}%");
            });
        }

        // Filtro por tipo de trabajador
        if ($request->has('tipo_trabajador') && $request->tipo_trabajador != '') {
            $query->where('tipo_trabajador_id', $request->tipo_trabajador);
        }

        // Filtro para mostrar inactivos o solo activos
        if ($request->has('mostrar_inactivos') && $request->mostrar_inactivos == '1') {
            // No aplicar filtro de estado para mostrar todos
        } else {
            $query->where('estado', 1); // Solo activos por defecto
        }

        // Ordenar y paginar resultados
        $trabajadores = $query->orderBy('nombre', 'asc')->paginate(10);

        // Obtener todos los tipos de trabajador para el filtro
        $tipoTrabajadores = \App\Models\TipoTrabajador::activos()->get();

        return view('admin.trabajador.index', compact('trabajadores', 'tipoTrabajadores'));
    }

    public function add()
    {
        $tipoTrabajadores = TipoTrabajador::where('estado', 'activo')->get();
        return view('admin.trabajador.add', compact('tipoTrabajadores'));
    }

    public function insert(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string|max:255', // Cambiado de required a nullable
            'email' => 'nullable|email|max:255',
            'nit' => 'nullable|string|max:20',
            'dpi' => 'nullable|string|max:20',
            'tipo_trabajador_id' => 'nullable|exists:tipo_trabajadors,id',
            // Removido 'estado' de la validación ya que será predeterminado
        ]);

        // Establecer estado predeterminado como 1 (activo)
        $validatedData['estado'] = 1;

        $trabajador = Trabajador::create($validatedData);

        return redirect('trabajadores')->with('status', 'Trabajador añadido correctamente');
    }

    public function edit($id)
    {
        $trabajador = Trabajador::find($id);
        $tipoTrabajadores = TipoTrabajador::where('estado', 'activo')->get();
        return view('admin.trabajador.edit', compact('trabajador', 'tipoTrabajadores'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string|max:255', // Cambiado de required a nullable
            'email' => 'nullable|email|max:255',
            'nit' => 'nullable|string|max:20',
            'dpi' => 'nullable|string|max:20',
            'tipo_trabajador_id' => 'nullable|exists:tipo_trabajadors,id',
            'estado' => 'required|in:1,0', // Mantenemos esto en edición para permitir cambio de estado
        ]);

        $trabajador = Trabajador::find($id);
        $trabajador->update($validatedData);

        return redirect('trabajadores')->with('status', 'Trabajador actualizado correctamente');
    }

    public function show($id)
    {
        $trabajador = Trabajador::with('tipoTrabajador')->find($id);
        return view('admin.trabajador.show', compact('trabajador'));
    }

    /**
     * Cambia el estado del trabajador (activa/desactiva)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($id)
    {
        $trabajador = Trabajador::find($id);

        if (!$trabajador) {
            return redirect('trabajadores')->with('error', 'Trabajador no encontrado.');
        }

        // Si está activo (1), lo desactivamos (0). Si está inactivo (0), lo activamos (1)
        $trabajador->estado = $trabajador->estado == 1 ? 0 : 1;
        $trabajador->save();

        $statusMessage = $trabajador->estado == 1
            ? 'Trabajador activado correctamente.'
            : 'Trabajador desactivado correctamente.';

        return redirect('trabajadores')->with('status', $statusMessage);
    }

    /**
     * Remove the specified resource from storage.
     * Esta función ahora se utiliza para cambiar el estado del trabajador (activar/desactivar)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $trabajador = Trabajador::find($id);

        if (!$trabajador) {
            return redirect('trabajadores')->with('error', 'Trabajador no encontrado.');
        }

        // Cambiar el estado: Si está activo (1), desactivarlo (0), y viceversa
        if ($trabajador->estado == 1) {
            $trabajador->estado = 0;
            $mensaje = 'Trabajador desactivado correctamente.';
        } else {
            $trabajador->estado = 1;
            $mensaje = 'Trabajador activado correctamente.';
        }

        $trabajador->save();

        return redirect('trabajadores')->with('status', $mensaje);
    }
}
