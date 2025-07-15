<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Vehiculo;
use App\Models\Cliente;
use App\Models\Config;
use App\Http\Requests\VehiculoFormRequest;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class VehiculosController extends Controller
{
    public function index(Request $request)
	{
        // Inicializar variables para los filtros
        $queryVehiculo = $request->input('fvehiculo');
        $queryAno = $request->input('fano');
        $queryCliente = $request->input('fcliente');

        // Construir la consulta base
        $query = Vehiculo::where('estado', 1);

        // Aplicar filtro de búsqueda por texto si existe
        if ($queryVehiculo) {
            $query->where(function ($q) use ($queryVehiculo) {
                $q->where('marca', 'LIKE', '%' . $queryVehiculo . '%')
                  ->orWhere('modelo', 'LIKE', '%' . $queryVehiculo . '%')
                  ->orWhere('ano', 'LIKE', '%' . $queryVehiculo . '%')
                  ->orWhere('color', 'LIKE', '%' . $queryVehiculo . '%')
                  ->orWhere('placa', 'LIKE', '%' . $queryVehiculo . '%')
                  ->orWhere('vin', 'LIKE', '%' . $queryVehiculo . '%');
            });
        }

        // Aplicar filtro de año si existe
        if ($queryAno) {
            $query->where('ano', $queryAno);
        }

        // Aplicar filtro de cliente si existe
        if ($queryCliente) {
            $query->where('cliente_id', $queryCliente);
        }

        // Obtener resultados paginados
        $vehiculos = $query->orderBy('marca', 'asc')
                           ->paginate(20);

        // Obtener todos los vehículos para los filtros desplegables
        $filterVehiculos = Vehiculo::all();

        // Obtener los clientes para el filtro de clientes
        $clientes = Cliente::where('estado', 1)->get();

        // Pasar variables a la vista
        return view('admin.vehiculo.index', compact(
            'vehiculos',
            'queryVehiculo',
            'queryAno',
            'queryCliente',
            'filterVehiculos',
            'clientes'
        ));
    }

    public function show($id)
    {
        $config = Config::first();
        // Cargar el vehículo con todas sus relaciones necesarias para las pestañas
        $vehiculo = Vehiculo::with([
            'cliente',
            'ventas.cliente',
            'ventas.detalleVentas',
            'ventas.pagos',
            'ventas.usuario'
        ])->find($id);

        return view('admin.vehiculo.show', compact("vehiculo", "config"));
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
            // Inicializar variables para los filtros
            $queryVehiculo = $request->input('fvehiculo');
            $queryAno = $request->input('fano');
            $queryCliente = $request->input('fcliente');

            // Construir la consulta base con las relaciones necesarias
            $query = Vehiculo::with([
                'cliente:id,nombre,fecha_nacimiento,telefono,direccion,celular,email,dpi,nit',
                'ventas' => function($query) {
                    $query->where('estado', 1);
                },
                'ventas.detalleVentas',
                'ventas.pagos'
            ])
            ->select('id', 'marca', 'modelo', 'ano', 'color', 'placa', 'vin', 'cliente_id', 'fotografia', 'created_at')
            ->where('estado', 1);

            // Aplicar filtro de búsqueda por texto si existe
            if ($queryVehiculo) {
                $query->where(function ($q) use ($queryVehiculo) {
                    $q->where('marca', 'LIKE', '%' . $queryVehiculo . '%')
                      ->orWhere('modelo', 'LIKE', '%' . $queryVehiculo . '%')
                      ->orWhere('ano', 'LIKE', '%' . $queryVehiculo . '%')
                      ->orWhere('color', 'LIKE', '%' . $queryVehiculo . '%')
                      ->orWhere('placa', 'LIKE', '%' . $queryVehiculo . '%')
                      ->orWhere('vin', 'LIKE', '%' . $queryVehiculo . '%');
                });
            }

            // Aplicar filtro de año si existe
            if ($queryAno) {
                $query->where('ano', $queryAno);
            }

            // Aplicar filtro de cliente si existe
            if ($queryCliente) {
                $query->where('cliente_id', $queryCliente);
            }

            // Obtener resultados ordenados
            $vehiculos = $query->orderBy('marca', 'asc')->get();

            // Datos para el PDF
            $config = Config::first();
            $nompdf = date('m/d/Y g:ia');
            $imagen = $config->logo ? public_path('assets/imgs/logos/' . $config->logo) : null;

            // Pasar los filtros a la vista para mostrarlos en el PDF
            $filtros = [
                'queryVehiculo' => $queryVehiculo,
                'queryAno' => $queryAno,
                'queryCliente' => $queryCliente
            ];

            // Recibir detalles de la impresión
            $pdftamaño = $request->input('pdftamaño', 'Letter');
            $pdfhorientacion = $request->input('pdfhorientacion', 'landscape');
            $pdfarchivo = $request->input('pdfarchivo', 'stream');

            $pdf = Pdf::loadView('admin.vehiculo.pdfvehiculos', compact('imagen', 'vehiculos', 'request', 'config', 'filtros'));
            $pdf->getDomPDF()->set_option("enable_html5_parser", true);
            $pdf->setPaper($pdftamaño, $pdfhorientacion);

            if ($pdfarchivo == "download") {
                return $pdf->download('Listado de Vehiculos-' . $nompdf . '.pdf');
            }

            return $pdf->stream('Listado de Vehiculos-' . $nompdf . '.pdf');
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

        // Cargar el vehículo con todas las relaciones necesarias
        $vehiculo = Vehiculo::with([
            'cliente',
            'ventas' => function($query) {
                $query->where('estado', 1)->orderBy('fecha', 'desc');
            },
            'ventas.detalleVentas.articulo',
            'ventas.pagos',
            'ventas.usuario'
        ])->find($request->input('vehiculo_id'));

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
        $pdf = Pdf::loadView('admin.vehiculo.pdfvehiculo', compact('imagen', 'vehiculo', 'config', 'pathvehiculo', 'cliente'));
        $pdf->getDomPDF()->set_option("enable_html5_parser", true);
        $pdf->setPaper($pdftamaño, $pdfhorientacion);

        // Devolver el PDF
        if ($pdfarchivo == "download") {
            return $pdf->download('Vehiculo_' . $vehiculo->marca . '_' . $vehiculo->modelo . '_' . $vehiculo->placa . '.pdf');
        }

        return $pdf->stream('Vehiculo_' . $vehiculo->marca . '_' . $vehiculo->modelo . '_' . $vehiculo->placa . '.pdf');
    }
}
