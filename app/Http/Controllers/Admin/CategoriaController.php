<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Http\Requests\CategoriaFormRequest;
use DB;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        if ($request)
        {
            $queryCategoria=$request->input('fcategoria');
            $categorias = DB::table('categorias')
            ->where('estado', '=', 1)
            ->where('nombre', 'LIKE', '%' . $queryCategoria . '%')
            ->orderBy('nombre' , 'asc')
            ->paginate(20);
            return view('admin.categoria.index', compact('categorias','queryCategoria'));
        }
    }

    public function show($id)
    {
        $categoria = Categoria::find($id);
        return view('admin.categoria.show', compact('categoria'));
    }

    public function add()
    {
        return view('admin.categoria.add');
    }

    public function insert(CategoriaFormRequest $request)
    {
        $categoria = new Categoria();
        $categoria->nombre = $request->input('nombre');
        $categoria->descripcion = $request->input('descripcion');
        $categoria->save();

        // return redirect('categorias')->with('status', __('Categoría agregada exitosamente.'));
        return redirect('show-categoria/'.$categoria->id)->with('status',__('Categoría agregada exitosamente.'));
    }

    public function edit($id)
    {
        $categoria = Categoria::find($id);
        return view('admin.categoria.edit', \compact('categoria'));
    }

    public function update(CategoriaFormRequest $request, $id)
    {
        $categoria = Categoria::find($id);
        $categoria->nombre = $request->input('nombre');
        $categoria->descripcion = $request->input('descripcion');
        $categoria->update();
        return redirect('show-categoria/'.$id)->with('status',__('Categoría actualizada correctamente.'));

    }

    public function destroy($id)
    {
        $categoria = Categoria::find($id);
        $categoria->estado = 0;
        $categoria->update();
        return redirect('categorias')->with('status',__('Categoría eliminada correctamente.'));
    }
}
