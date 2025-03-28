<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Http\Requests\ClienteFormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\Config;
use PDF;
use DB;

class ClienteController extends Controller
{
    public function clientes(Request $request)
    {
        if ($request)
        {
            $queryCliente=$request->input('fcliente');
            $clientes = DB::table('clientes')
            ->where('estado', '=', 1)
            ->where(function ($query) use ($queryCliente) {
            $query->where('nombre', 'LIKE', '%' . $queryCliente . '%')
                ->orWhere('email', 'LIKE', '%' . $queryCliente . '%')
                ->orWhere('telefono', 'LIKE', '%' . $queryCliente . '%')
                ->orWhere('celular', 'LIKE', '%' . $queryCliente . '%')
                ->orWhere('dpi', 'LIKE', '%' . $queryCliente . '%')
                ->orWhere('nit', 'LIKE', '%' . $queryCliente . '%');
            })
            ->orderBy('nombre','asc')
            ->paginate(20);

            $filterClientes = Cliente::all();

            return view('admin.cliente.index', compact('clientes','queryCliente','filterClientes'));
        }
    }

    public function show($id)
    {
        $cliente = Cliente::find($id);
        $vehiculos = Vehiculo::where('cliente_id',$id)->where('estado', 1)->orderBy('marca', 'desc')->get();

        // Obtener ventas del cliente usando el modelo Eloquent con relaciones
        $ventas = \App\Models\Venta::with(['vehiculo', 'detalleVentas', 'usuario', 'pagos'])
            ->where('cliente_id', $id)
            ->where('estado', '!=', 0)
            ->orderBy('fecha', 'desc')
            ->paginate(10);

        return view('admin.cliente.show', compact('cliente', 'vehiculos', 'ventas'));
    }

    public function add()
    {
        return view('admin.cliente.add');
    }

    public function insert(ClienteFormRequest $request)
    {
        $cliente = new Cliente();
        $fecha_nacimiento = $request->get('fecha_nacimiento');
        if($request->hasFile('fotografia'))
        {
            $file = $request->file('fotografia');
            $ext = $file->getClientOriginalExtension();
            $filename = time().'.'.$ext;
            $file->move('assets/imgs/clientes',$filename);
            $cliente->fotografia = $filename;
        }
        $cliente->estado = 1;
        $cliente->nombre = $request->input('nombre');
        $cliente->fecha_nacimiento = $fecha_nacimiento;
        $cliente->telefono = $request->input('telefono');
        $cliente->celular = $request->input('celular');
        $cliente->direccion = $request->input('direccion');
        $cliente->email = $request->input('email');
        $cliente->dpi = $request->input('dpi');
        $cliente->nit = $request->input('nit');
        $cliente->save();

        return redirect('show-cliente/'.$cliente->id)->with('status', __('Cliente agregado  correctamente!'));
    }

    public function edit($id)
    {
        $cliente = Cliente::find($id);
        return view('admin.cliente.edit', \compact('cliente'));
    }

    public function update(ClienteFormRequest $request, $id)
    {
        $cliente = Cliente::find($id);
        $fecha_nacimiento = $request->get('fecha_nacimiento');
        if($request->hasFile('fotografia'))
        {
            $path = 'assets/imgs/clientes/'.$cliente->fotografia;
            if(File::exists($path))
            {
                File::delete($path);
            }
            $file = $request->file('fotografia');
            $ext = $file->getClientOriginalExtension();
            $filename = time().'.'.$ext;
            $file->move('assets/imgs/clientes',$filename);
            $cliente->fotografia = $filename;
        }
        $cliente->nombre = $request->input('nombre');
        $cliente->fecha_nacimiento = $fecha_nacimiento;
        $cliente->telefono = $request->input('telefono');
        $cliente->celular = $request->input('celular');
        $cliente->direccion = $request->input('direccion');
        $cliente->email = $request->input('email');
        $cliente->dpi = $request->input('dpi');
        $cliente->nit = $request->input('nit');
        $cliente->update();
        return redirect('show-cliente/'.$id)->with('status',__('Cliente actualizado correctamente!'));

    }

    public function destroy($id)
    {
        $cliente = Cliente::find($id);
        if ($cliente->fotografia)
        {
            $path = 'assets/img/clientes/'.$cliente->fotografia;
            if (File::exists($path))
            {
                File::delete($path);

            }
        }
        $cliente->estado = 0;
        $cliente->email = $cliente->email.'-Deleted'.$cliente->id;
        $cliente->update();
        return redirect('clientes')->with('status',__('Cliente eliminado correctamente!'));
    }

    public function pdf(Request $request)
    {
        if ($request)
        {

            $clientes = Cliente::where('estado',1)->orderBy('nombre','asc')->get();
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
                $pdf = PDF::loadView('admin.cliente.pdf',['clientes'=>$clientes,'path'=>$path,'config'=>$config,'imagen'=>$imagen,'currency'=>$currency]);

                return $pdf->download ('Listado Clientes '.$nompdf.'.pdf');
            }
            if ( $verpdf == "Browser" )
            {
                $pdf = PDF::loadView('admin.cliente.pdf',['clientes'=>$clientes,'path'=>$path,'config'=>$config,'imagen'=>$imagen,'currency'=>$currency]);

                return $pdf->stream ('Listado Clientes '.$nompdf.'.pdf');
            }
        }
    }

    public function pdfcliente($id)
    {
        $cliente = Cliente::find($id);
        $vehiculos = Vehiculo::where('cliente_id', $id)->where('estado', 1)->orderBy('marca', 'desc')->get();

        // Cargar las ventas del cliente con sus relaciones
        $ventas = \App\Models\Venta::with(['vehiculo', 'detalleVentas', 'pagos'])
            ->where('cliente_id', $id)
            ->where('estado', '!=', 0)
            ->orderBy('fecha', 'desc')
            ->take(10)  // Limitar a 10 ventas para el PDF
            ->get();

        $verpdf = "Browser";
        $nompdf = date('m/d/Y g:ia');
        $path = public_path('assets/imgs/');
        $pathcliente = public_path('assets/imgs/clientes/');

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

        if ( $verpdf == "Download" )
        {
            $pdf = PDF::loadView('admin.cliente.pdfcliente', [
                'cliente' => $cliente,
                'path' => $path,
                'config' => $config,
                'imagen' => $imagen,
                'currency' => $currency,
                'pathcliente' => $pathcliente,
                'vehiculos' => $vehiculos,
                'ventas' => $ventas
            ]);

            return $pdf->download('Cliente: '.$cliente->nombre.'-'.$nompdf.'.pdf');
        }
        if ( $verpdf == "Browser" )
        {
            $pdf = PDF::loadView('admin.cliente.pdfcliente', [
                'cliente' => $cliente,
                'path' => $path,
                'config' => $config,
                'imagen' => $imagen,
                'currency' => $currency,
                'pathcliente' => $pathcliente,
                'vehiculos' => $vehiculos,
                'ventas' => $ventas
            ]);

            return $pdf->stream('Cliente: '.$cliente->nombre.'-'.$nompdf.'.pdf');
        }
    }
}
