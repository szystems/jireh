<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Http\Requests\ProveedorFormRequest;
use DB;
use App\Models\Config;
use Carbon\Carbon;
use PDF;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        if ($request)
        {
            $queryProveedor=$request->input('fproveedor');
            $proveedores = DB::table('proveedors')
            ->where('estado', '=', 1)
            ->where(function ($query) use ($queryProveedor) {
                $query->where('nombre', 'LIKE', '%' . $queryProveedor . '%')
                    ->orWhere('nit', 'LIKE', '%' . $queryProveedor . '%')
                    ->orWhere('contacto', 'LIKE', '%' . $queryProveedor . '%')
                    ->orWhere('email', 'LIKE', '%' . $queryProveedor . '%')
                    ->orWhere('telefono', 'LIKE', '%' . $queryProveedor . '%')
                    ->orWhere('celular', 'LIKE', '%' . $queryProveedor . '%')
                    ->orWhere('numero_cuenta', 'LIKE', '%' . $queryProveedor . '%')
                    ->orWhere('nombre_cuenta', 'LIKE', '%' . $queryProveedor . '%');
                })
            ->orderBy('nombre' , 'asc')
            ->paginate(20);
            return view('admin.proveedor.index', compact('proveedores','queryProveedor'));
        }
    }

    public function show($id)
    {
        $proveedor = Proveedor::find($id);
        return view('admin.proveedor.show', compact('proveedor'));
    }

    public function add()
    {
        return view('admin.proveedor.add');
    }

    public function insert(ProveedorFormRequest $request)
    {
        $proveedor = new Proveedor();
        $proveedor->nombre = $request->input('nombre');
        $proveedor->nit = $request->input('nit');
        $proveedor->contacto = $request->input('contacto');
        $proveedor->telefono = $request->input('telefono');
        $proveedor->celular = $request->input('celular');
        $proveedor->direccion = $request->input('direccion');
        $proveedor->email = $request->input('email');
        $proveedor->banco = $request->input('banco');
        $proveedor->nombre_cuenta = $request->input('nombre_cuenta');
        $proveedor->tipo_cuenta = $request->input('tipo_cuenta');
        $proveedor->numero_cuenta = $request->input('numero_cuenta');
        $proveedor->save();

        // return redirect('proveedores')->with('status', __('Proveedor agregada exitosamente.'));
        return redirect('show-proveedor/'.$proveedor->id)->with('status',__('Proveedor agregada exitosamente.'));
    }

    public function edit($id)
    {
        $proveedor = Proveedor::find($id);
        return view('admin.proveedor.edit', \compact('proveedor'));
    }

    public function update(ProveedorFormRequest $request, $id)
    {
        $proveedor = Proveedor::find($id);
        $proveedor->nombre = $request->input('nombre');
        $proveedor->nit = $request->input('nit');
        $proveedor->contacto = $request->input('contacto');
        $proveedor->telefono = $request->input('telefono');
        $proveedor->celular = $request->input('celular');
        $proveedor->direccion = $request->input('direccion');
        $proveedor->email = $request->input('email');
        $proveedor->banco = $request->input('banco');
        $proveedor->nombre_cuenta = $request->input('nombre_cuenta');
        $proveedor->tipo_cuenta = $request->input('tipo_cuenta');
        $proveedor->numero_cuenta = $request->input('numero_cuenta');
        $proveedor->update();
        return redirect('show-proveedor/'.$id)->with('status',__('Proveedor actualizada correctamente.'));

    }

    public function destroy($id)
    {
        $proveedor = Proveedor::find($id);
        $proveedor->estado = 0;
        $proveedor->update();
        return redirect('proveedores')->with('status',__('Proveedor eliminada correctamente.'));
    }

    public function pdf(Request $request)
    {
        if ($request)
        {

            $proveedores = Proveedor::where('estado',1)->orderBy('nombre','asc')->get();
            $verpdf = "Browser";
            $nompdf = date('m/d/Y g:ia');
            $path = public_path('assets/imgs/');

            $config = Config::first();

            $currency = $config->currency_simbol;

            if ($config->logo == null)
            {
                $logo = null;
                $imagen = null;
            }
            else
            {
                    $logo = $config->logo;
                    $imagen = public_path('assets/imgs/logos/'.$logo);
            }


            $config = Config::first();

            if ( $verpdf == "Download" )
            {
                $pdf = PDF::loadView('admin.proveedor.pdf',['proveedores'=>$proveedores,'path'=>$path,'config'=>$config,'imagen'=>$imagen,'currency'=>$currency]);

                return $pdf->download ('Listado Proveedores '.$nompdf.'.pdf');
            }
            if ( $verpdf == "Browser" )
            {
                $pdf = PDF::loadView('admin.proveedor.pdf',['proveedores'=>$proveedores,'path'=>$path,'config'=>$config,'imagen'=>$imagen,'currency'=>$currency]);

                return $pdf->stream ('Listado Proveedores '.$nompdf.'.pdf');
            }
        }
    }

    public function pdfproveedor($id)
    {

        $proveedor = Proveedor::find($id);
        $verpdf = "Browser";
        $nompdf = date('m/d/Y g:ia');
        $path = public_path('assets/imgs/');
        $pathproveedor = public_path('assets/imgs/proveedores/');

        $config = Config::first();

        $currency = $config->currency_simbol;

        if ($config->logo == null)
        {
            $logo = null;
            $imagen = null;
        }
        else
        {
                $logo = $config->logo;
                $imagen = public_path('assets/imgs/logos/'.$logo);
        }


        $config = Config::first();

        if ( $verpdf == "Download" )
        {
            $pdf = PDF::loadView('admin.proveedor.pdfproveedor',['proveedor'=>$proveedor,'path'=>$path,'config'=>$config,'imagen'=>$imagen,'currency'=>$currency,'pathproveedor'=>$pathproveedor]);

            return $pdf->download ('Proveedor: '.$proveedor->nombre.'-'.$nompdf.'.pdf');
        }
        if ( $verpdf == "Browser" )
        {
            $pdf = PDF::loadView('admin.proveedor.pdfproveedor',['proveedor'=>$proveedor,'path'=>$path,'config'=>$config,'imagen'=>$imagen,'currency'=>$currency,'pathproveedor'=>$pathproveedor]);

            return $pdf->stream ('Proveedor: '.$proveedor->proveedor.'-'.$nompdf.'.pdf');
        }
    }
}
