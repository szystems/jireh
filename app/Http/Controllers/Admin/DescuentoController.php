<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Descuento;
use App\Http\Requests\DescuentoFormRequest;
use Illuminate\Http\Request;

class DescuentoController extends Controller
{
    public function index(Request $request)
    {
        $queryDescuento = $request->input('fdescuento');

        $query = Descuento::query();

        // Filtrar por nombre si se proporciona un término de búsqueda
        if ($queryDescuento) {
            $query->where('nombre', 'LIKE', "%$queryDescuento%");
        }

        // Mostrar solo activos a menos que se solicite mostrar inactivos
        if (!$request->has('mostrar_inactivos')) {
            $query->where('estado', '=', 1);
        }

        // Ordenar por nombre
        $query->orderBy('nombre', 'asc');

        $descuentos = $query->paginate(20);

        // Mantener parámetros en paginación
        $descuentos->appends($request->all());

        return view('admin.descuento.index', compact('descuentos', 'queryDescuento'));
    }

    public function show($id)
    {
        $descuento = Descuento::findOrFail($id);
        return view('admin.descuento.show', compact('descuento'));
    }

    public function add()
    {
        return view('admin.descuento.add');
    }

    public function insert(DescuentoFormRequest $request)
    {
        $data = $request->validated();
        Descuento::create($data);
        return redirect('descuentos')->with('status', __('Descuento agregado exitosamente.'));
    }

    public function edit($id)
    {
        $descuento = Descuento::findOrFail($id);
        return view('admin.descuento.edit', compact('descuento'));
    }

    public function update(DescuentoFormRequest $request, $id)
    {
        $data = $request->validated();
        $descuento = Descuento::findOrFail($id);
        $descuento->update($data);
        return redirect('descuentos')->with('status', __('Descuento actualizado exitosamente.'));
    }

    public function destroy($id)
    {
        $descuento = Descuento::findOrFail($id);

        // Si está activo, desactivarlo, si está inactivo, activarlo
        $nuevoEstado = !$descuento->estado;

        $descuento->update(['estado' => $nuevoEstado]);

        $mensaje = $nuevoEstado ?
            __('Descuento activado exitosamente.') :
            __('Descuento desactivado exitosamente.');

        return redirect('descuentos')->with('status', $mensaje);
    }
}
