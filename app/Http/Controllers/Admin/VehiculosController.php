<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Vehiculo;
use App\Models\Cliente;
use App\Models\Config;
use App\Http\Requests\VehiculoFormRequest;
use Illuminate\Support\Facades\File;
use PDF;
use DB;

class VehiculosController extends Controller
{
    public function index(Request $request)
	{
        if ($request)
        {
            $queryVehiculo=$request->input('fvehiculo');
            $vehiculos = DB::table('vehiculos')
            ->where('estado', '=', 1)
            ->where(function ($query) use ($queryVehiculo) {
            $query->where('marca', 'LIKE', '%' . $queryVehiculo . '%')
                ->orWhere('modelo', 'LIKE', '%' . $queryVehiculo . '%')
                ->orWhere('ano', 'LIKE', '%' . $queryVehiculo . '%')
                ->orWhere('color', 'LIKE', '%' . $queryVehiculo . '%')
                ->orWhere('placa', 'LIKE', '%' . $queryVehiculo . '%')
                ->orWhere('vin', 'LIKE', '%' . $queryVehiculo . '%');
            })
            ->orderBy('marca','asc')
            ->paginate(20);
            $filterVehiculos = Vehiculo::all();

            return view('admin.vehiculo.index', compact('vehiculos','queryVehiculo','filterVehiculos'));
        }
    }

    public function show($id)
    {
        $config = Config::first();
        $vehiculo = Vehiculo::find($id);
        return view('admin.vehiculo.show',compact("vehiculo","config"));
    }

    public function add()
    {
        $config = Config::first();
        $clientes = DB::table('clientes')->where('estado', 1)->get();
        return view('admin.vehiculo.add',compact('config','clientes'));
    }

    public function insert(VehiculoFormRequest $request)
    {
        $vehiculo = new Vehiculo();
        if($request->hasFile('fotografia'))
        {
            $file = $request->file('fotografia');
            $ext = $file->getClientOriginalExtension();
            $filename = time().'.'.$ext;
            $file->move('assets/imgs/vehiculos',$filename);
            $vehiculo->fotografia = $filename;
        }
        $vehiculo->cliente_id = $request->input('cliente_id');
        $vehiculo->marca = $request->input('marca');
        $vehiculo->modelo = $request->input('modelo');
        $vehiculo->ano = $request->input('ano');
        $vehiculo->color = $request->input('color');
        $vehiculo->placa = $request->input('placa');
        $vehiculo->vin = $request->input('vin');
        $vehiculo->estado = '1';
        $vehiculo->save();

        return redirect('vehiculos')->with('status', __('Vehiculo agregado exitosamente.'));
    }

    public function edit($id)
    {
        $config = Config::first();
        $vehiculo = Vehiculo::find($id);
        $clientes = DB::table('clientes')->where('estado', 1)->get();
        return view('admin.vehiculo.edit', \compact("vehiculo","config","clientes"));
    }

    public function update(VehiculoFormRequest $request, $id)
    {
        $vehiculo = Vehiculo::find($id);
        if($request->hasFile('fotografia'))
        {
            $path = 'assets/imgs/vehiculos/'.$vehiculo->fotografia;
            if(File::exists($path))
            {
                File::delete($path);
            }
            $file = $request->file('fotografia');
            $ext = $file->getClientOriginalExtension();
            $filename = time().'.'.$ext;
            $file->move('assets/imgs/vehiculos',$filename);
            $vehiculo->fotografia = $filename;
        }
        $vehiculo->cliente_id = $request->input('cliente_id');
        $vehiculo->marca = $request->input('marca');
        $vehiculo->modelo = $request->input('modelo');
        $vehiculo->ano = $request->input('ano');
        $vehiculo->color = $request->input('color');
        $vehiculo->placa = $request->input('placa');
        $vehiculo->vin = $request->input('vin');
        $vehiculo->update();

        return redirect('show-vehiculo/'.$id)->with('status',__('Vehiculo  actualizado correctamente!'));
    }

    public function destroy($id)
    {
        $vehiculo = Cliente::find($id);
        if ($vehiculo->fotografia)
        {
            $path = 'assets/img/vehiculos/'.$vehiculo->fotografia;
            if (File::exists($path))
            {
                File::delete($path);

            }
        }
        $vehiculo->estado = 0;
        $vehiculo->placa = $vehiculo->placa.'-Deleted'.$vehiculo->id;
        $vehiculo->vin = $vehiculo->vin.'-Deleted'.$vehiculo->id;
        $vehiculo->update();
        return redirect('vehiculos')->with('status',__('Vehículo eliminado correctamente!'));
    }

    public function printvehiculos(Request $request)
    {
        if ($request) {
            // Cargar vehículos con clientes, seleccionando solo los campos necesarios
            $vehiculos = \App\Models\Vehiculo::with('cliente:id,nombre,fecha_nacimiento,telefono,direccion,celular,email,dpi,nit')
                ->select('id', 'marca', 'modelo', 'ano', 'color', 'placa', 'vin', 'cliente_id')
                ->where('estado', 1)
                ->get();

            $config = Config::first();
            $nompdf = date('m/d/Y g:ia');
            $imagen = $config->logo ? public_path('assets/imgs/logos/' . $config->logo) : null;

            // Recibir detalles de la impresión
            $pdftamaño = $request->input('pdftamaño');
            $pdfhorientacion = $request->input('pdfhorientacion');
            $pdfarchivo = $request->input('pdfarchivo');

            $pdf = PDF::loadView('admin.vehiculo.pdfvehiculos', compact('imagen', 'vehiculos', 'request', 'config'));
            $pdf->getDomPDF()->set_option("enable_html5_parser", true);
            $pdf->setPaper($pdftamaño, $pdfhorientacion);

            if ($pdfarchivo == "download") {
                return $pdf->download('Listado de Vehiculos-' . $nompdf . '.pdf');
            }

            if ($pdfarchivo == "stream") {
                return $pdf->stream('Listado de Vehiculos-' . $nompdf . '.pdf');
            }
        }
    }

    public function printvehiculo(Request $request)
    {
        // Validar que se reciba el ID del vehículo
        $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'pdftamaño' => 'required|string',
            'pdfhorientacion' => 'required|string',
            'pdfarchivo' => 'required|string|in:download,stream',
        ]);

        // Cargar el vehículo y el cliente
        $vehiculo = Vehiculo::with('cliente')->find($request->input('vehiculo_id'));

        if (!$vehiculo) {
            return response()->json(['error' => 'Vehículo no encontrado'], 404);
        }

        $cliente = $vehiculo->cliente; // Ya cargado con Eloquent

        $config = Config::first();
        $nompdf = date('m/d/Y g:ia');
        $pathvehiculo = public_path('assets/imgs/vehiculos/');

        $currency = $config->currency_simbol;

        // Manejo del logo
        $imagen = $config->logo ? public_path('assets/imgs/logos/' . $config->logo) : null;

        // Recibir detalles de la impresión
        $pdftamaño = $request->input('pdftamaño');
        $pdfhorientacion = $request->input('pdfhorientacion');
        $pdfarchivo = $request->input('pdfarchivo');

        // Cargar la vista del PDF
        $pdf = PDF::loadView('admin.vehiculo.pdfvehiculo', compact('imagen', 'vehiculo', 'config', 'pathvehiculo', 'cliente'));
        $pdf->getDomPDF()->set_option("enable_html5_parser", true);
        $pdf->setPaper($pdftamaño, $pdfhorientacion);

        // Devolver el PDF
        if ($pdfarchivo == "download") {
            return $pdf->download('Vehiculo: ' . $vehiculo->vin . '.pdf');
        }

        return $pdf->stream('Vehiculo: ' . $vehiculo->vin . '.pdf');
    }
}
