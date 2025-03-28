<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrabajadorFormRequest;
use App\Models\Trabajador;
use Illuminate\Http\Request;

class TrabajadorController extends Controller
{
    public function index(Request $request)
    {
        $query = Trabajador::query();

        // Aplicar búsqueda si se proporciona
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%')
                  ->orWhere('no_documento', 'like', '%' . $search . '%')
                  ->orWhere('telefono', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filtrar por estado activo a menos que se solicite mostrar inactivos
        if (!$request->has('mostrar_inactivos')) {
            $query->where('estado', 'activo');
        }

        // Ordenamiento
        $sort = $request->input('sort', 'nombre');
        $direction = $request->input('direction', 'asc');
        $allowedSorts = ['nombre', 'fecha_nacimiento', 'no_documento'];

        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $direction);
        }

        // Número fijo de 20 elementos por página
        $trabajadores = $query->paginate(20);

        // Mantener los parámetros en la paginación
        $trabajadores->appends($request->except('page'));

        return view('admin.trabajador.index', compact('trabajadores'));
    }

    public function show($id)
    {
        $trabajador = Trabajador::findOrFail($id);
        return view('admin.trabajador.show', compact('trabajador'));
    }

    public function add()
    {
        return view('admin.trabajador.add');
    }

    public function insert(TrabajadorFormRequest $request)
    {
        $validated = $request->validated();
        Trabajador::create($validated);
        return redirect('trabajadores')->with('status', __('Trabajador agregado exitosamente.'));
    }

    public function edit($id)
    {
        $trabajador = Trabajador::findOrFail($id);
        return view('admin.trabajador.edit', compact('trabajador'));
    }

    public function update(TrabajadorFormRequest $request, $id)
    {
        $validated = $request->validated();
        $trabajador = Trabajador::findOrFail($id);

        // Si está reactivando un trabajador, verificar si necesitamos restaurar el email
        if ($trabajador->estado === 'inactivo' && $validated['estado'] === 'activo' && empty($validated['email'])) {
            // No hagas nada con el email si no se proporcionó uno nuevo
            unset($validated['email']);
        }

        $trabajador->update($validated);
        return redirect('trabajadores')->with('status', __('Trabajador actualizado exitosamente.'));
    }

    public function destroy($id)
    {
        $trabajador = Trabajador::findOrFail($id);

        // Almacenar el email en algún lugar temporal si es necesario restaurarlo después
        // Podrías crear un campo email_backup en la base de datos

        $trabajador->update([
            'estado' => 'inactivo',
            'email' => null, // Eliminar el email para que pueda ser usado por otro trabajador
        ]);
        return redirect('trabajadores')->with('status', __('Trabajador eliminado exitosamente.'));
    }
}
