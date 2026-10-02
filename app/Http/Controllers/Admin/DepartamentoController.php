<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DepartamentoController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::orderBy('orden')->orderBy('nombre')->get();
        $usos = $this->usosPorNombre();

        return view('admin.departamento.index', compact('departamentos', 'usos'));
    }

    public function add()
    {
        return view('admin.departamento.add');
    }

    public function insert(Request $request)
    {
        $datos = $this->validar($request);
        $orden = (int) Departamento::max('orden') + 1;

        Departamento::create([
            'nombre' => $datos['nombre'],
            'etiqueta' => $datos['etiqueta'],
            'orden' => $orden,
            'activo' => true,
        ]);

        return redirect('departamentos')->with('status', 'Departamento agregado. Ya se puede elegir en ventas y cotizaciones.');
    }

    public function edit($id)
    {
        $departamento = Departamento::findOrFail($id);
        $usos = $this->contar($departamento->nombre);

        return view('admin.departamento.edit', compact('departamento', 'usos'));
    }

    public function update(Request $request, $id)
    {
        $departamento = Departamento::findOrFail($id);
        $usos = $this->contar($departamento->nombre);
        $datos = $this->validar($request, $departamento->id, $usos > 0 ? $departamento->nombre : null);

        if ($usos > 0 && $datos['nombre'] !== $departamento->nombre) {
            return back()->withInput()->with('error', 'Este departamento ya está en facturas. Se puede cambiar el nombre visible, no el valor guardado.');
        }

        $quedaActivo = $request->boolean('activo');
        if ($departamento->activo && !$quedaActivo) {
            $otros = Departamento::where('activo', true)->where('id', '!=', $departamento->id)->count();
            if ($otros < 1) {
                return back()->withInput()->with('error', 'Debe quedar al menos un departamento activo.');
            }
        }

        $departamento->update([
            'nombre' => $datos['nombre'],
            'etiqueta' => $datos['etiqueta'],
            'orden' => $datos['orden'],
            'activo' => $request->boolean('activo'),
        ]);

        return redirect('departamentos')->with('status', 'Departamento actualizado.');
    }

    public function destroy($id)
    {
        $departamento = Departamento::findOrFail($id);
        $usos = $this->contar($departamento->nombre);

        if ($usos > 0) {
            $departamento->update(['activo' => false]);

            return redirect('departamentos')->with('status', 'Tiene facturas o cotizaciones, así que se desactivó. Las ventas anteriores siguen igual.');
        }

        $activos = Departamento::where('activo', true)->where('id', '!=', $departamento->id)->count();
        if ($departamento->activo && $activos < 1) {
            return redirect('departamentos')->with('error', 'Debe quedar al menos un departamento activo.');
        }

        $departamento->delete();

        return redirect('departamentos')->with('status', 'Departamento eliminado.');
    }

    private function validar(Request $request, $ignorarId = null, ?string $nombreFijo = null): array
    {
        $reglasNombre = ['required', 'string', 'max:100', Rule::unique('departamentos', 'nombre')->ignore($ignorarId)];
        if ($nombreFijo !== null) {
            $reglasNombre[] = Rule::in([$nombreFijo]);
        }

        $datos = $request->validate([
            'nombre' => $reglasNombre,
            'etiqueta' => 'required|string|max:100',
            'orden' => 'nullable|integer|min:0|max:9999',
        ], [
            'nombre.unique' => 'Ya existe un departamento con ese nombre.',
            'nombre.in' => 'No se puede renombrar un departamento que ya tiene facturas.',
        ]);

        $datos['nombre'] = trim($datos['nombre']);
        $datos['etiqueta'] = trim($datos['etiqueta']);
        $datos['orden'] = isset($datos['orden']) ? (int) $datos['orden'] : (int) Departamento::max('orden');

        return $datos;
    }

    private function usosPorNombre(): array
    {
        $ventas = DB::table('ventas')
            ->select('tipo_venta', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo_venta')
            ->pluck('total', 'tipo_venta');

        $cotizaciones = DB::table('cotizaciones')
            ->select('tipo_cotizacion', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo_cotizacion')
            ->pluck('total', 'tipo_cotizacion');

        $nombres = $ventas->keys()->merge($cotizaciones->keys())->unique();
        $usos = [];
        foreach ($nombres as $nombre) {
            $usos[$nombre] = (int) ($ventas[$nombre] ?? 0) + (int) ($cotizaciones[$nombre] ?? 0);
        }

        return $usos;
    }

    private function contar(string $nombre): int
    {
        return (int) DB::table('ventas')->where('tipo_venta', $nombre)->count()
            + (int) DB::table('cotizaciones')->where('tipo_cotizacion', $nombre)->count();
    }
}
