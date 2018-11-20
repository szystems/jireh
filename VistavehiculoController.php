<?php

namespace jireh\Http\Controllers;

use Illuminate\Http\Request;
use jireh\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use jireh\http\Requests\VistavehiculoFormRequest;
use jireh\Vistavehiculo;



use Carbon\Carbon; 
use DB;
use Response;
use Illuminate\Support\Collection;
use Auth;
use jireh\User;

use jireh\Http\Requests\MensajeFormRequest;
use Mail;

class VistavehiculoController extends Controller
{
    public function __contruct()
    {
        $this->middleware('auth');
    }

     public function index(Request $request)
    {
        if ($request)
        {
            $marca=trim($request->get('marca'));
            $modelo=trim($request->get('modelo'));
            $linea=trim($request->get('linea'));
            $tipo=trim($request->get('tipo'));
            $origen=trim($request->get('origen'));
            $desde=trim($request->get('desde'));
            $hasta=trim($request->get('hasta'));
            if ($hasta == 0)
            {
                $hasta='1000000';
            }

            $vehiculos=DB::table('vehiculo')
            ->where('marca','LIKE','%'.$marca.'%')
            ->where('modelo','LIKE','%'.$modelo.'%')
            ->where('linea','LIKE','%'.$linea.'%')
            ->where('tipo','LIKE','%'.$tipo.'%')
            ->where('origen','LIKE','%'.$origen.'%')
            ->whereBetween('precio', [$desde, $hasta])
            

            ->where('autorizado','=','SI')
            ->orderBy('fecha_actualizacion','desc')
            ->paginate(5);
            return view('vistavehiculos.index',["vehiculos"=>$vehiculos,"marca"=>$marca,"modelo"=>$modelo,"linea"=>$linea,"tipo"=>$tipo,"origen"=>$origen,"desde"=>$desde,"hasta"=>$hasta]);
        }
    }

    public function create()
    {
    	return view("vistavehiculos.create");
    }

    public function store(VistavehiculoFormRequest $request)
    {
    	$vehiculo=new Vistavehiculo;
    	$vehiculo->contacto=$request->get('contacto');
    	$vehiculo->tel_contacto=$request->get('tel_contacto');
    	$vehiculo->email_contacto=$request->get('email_contacto');
    	$vehiculo->nombre=$request->get('nombre');
    	$vehiculo->marca=$request->get('marca');
    	$vehiculo->modelo=$request->get('modelo');
    	$vehiculo->linea=$request->get('linea');
    	$vehiculo->tipo=$request->get('tipo');
    	$vehiculo->origen=$request->get('origen');
    	$vehiculo->precio=$request->get('precio');
    	$vehiculo->puertas=$request->get('puertas');
    	$vehiculo->motor=$request->get('motor');
    	$vehiculo->cilindros=$request->get('cilindros');
    	$vehiculo->combustible=$request->get('combustible');
    	$vehiculo->millas=$request->get('millas');
    	$vehiculo->descripcion=$request->get('descripcion');
    	$vehiculo->ac=$request->get('ac');
    	$vehiculo->full_equipo=$request->get('full_equipo');
    	$vehiculo->estado=$request->get('estado');
    	$vehiculo->autorizado='NO';
        $mytime = Carbon::now('America/Guatemala');
    	$vehiculo->fecha=$mytime->toDateTimeString();
    	$vehiculo->fecha_actualizacion=$mytime->toDateTimeString();
    	$vehiculo->save();


            
            $name = $request->get('contacto');
            $email = $request->get('email_contacto');
            $telefono = $request->get('tel_contacto');
            $subject = "Envio de formulario de nuevo vehiculo";
            $nomvehi = $request->get('nombre');
            $msg = "Un contacto agrego un nuevo vehículo, ve a tu cuenta en www.grupojireh.com para editarlo y contactarte con el encargado para autorizar la publicación.";

            $data = array(
                'name' => $name,
                'email' => $email,
                'telefono' => $telefono,
                'subject' => $subject,
                'nomvehi' => $nomvehi,
                'msg' => $msg
            );

            $fromEmail = 'ottoszarata@szystems.com';
            $fromName = 'Envio de Formulario de Vehiculo';

            Mail::send('emails.emailformulariovehiculo', $data, function($message) use($fromName, $fromEmail)
                {
                    $message->to($fromEmail, $fromName);
                    $message->from($fromEmail, $fromName);
                    $message->subject('Envio de Formulario de Vehiculo');
                } 
            );

    	return view("show");
    }

    public function show($id)
    {
        


        $vehiculo = Vistavehiculo::findOrFail($id);

        $imgvehiculo=DB::table('imgvehiculo')
            ->select('idimgvehiculo','idvehiculo','imagen')
            ->where('idvehiculo', '=', $id)
            ->groupBy('idvehiculo')
            ->get();

        return view("vistavehiculos.show",["vehiculo"=>$vehiculo,"imgvehiculo"=>$imgvehiculo]);
    }

    public function mensaje($id)
    {
        


        $vehiculo = Vistavehiculo::findOrFail($id);

        return view("vistavehiculos.mensaje",["vehiculo"=>$vehiculo]);
    }

      

    public function edit($id)
    {
        $imgvehiculo=DB::table('imgvehiculo')
            ->select('idimgvehiculo','idvehiculo','imagen')
            ->where('idvehiculo', '=', $id)
            ->groupBy('idvehiculo')
            ->get();

    	return view("vehiculos.edit",["vehiculo"=>Vehiculo::findOrFail($id),"imgvehiculo"=>$imgvehiculo]);
    }
    public function update(VehiculoFormRequest $request,$id)
    {
    	$vehiculo=Vehiculo::findOrFail($id);
    	$vehiculo->contacto=$request->get('contacto');
    	$vehiculo->tel_contacto=$request->get('tel_contacto');
    	$vehiculo->email_contacto=$request->get('email_contacto');
    	$vehiculo->nombre=$request->get('nombre');
    	$vehiculo->marca=$request->get('marca');
    	$vehiculo->modelo=$request->get('modelo');
    	$vehiculo->linea=$request->get('linea');
    	$vehiculo->tipo=$request->get('tipo');
    	$vehiculo->origen=$request->get('origen');
    	$vehiculo->precio=$request->get('precio');
    	$vehiculo->puertas=$request->get('puertas');
    	$vehiculo->motor=$request->get('motor');
    	$vehiculo->cilindros=$request->get('cilindros');
    	$vehiculo->combustible=$request->get('combustible');
    	$vehiculo->millas=$request->get('millas');
    	$vehiculo->descripcion=$request->get('descripcion');
    	$vehiculo->ac=$request->get('ac');
    	$vehiculo->full_equipo=$request->get('full_equipo');
    	$vehiculo->estado=$request->get('estado');
    	$vehiculo->autorizado=$request->get('autorizado');
        $mytime = Carbon::now('America/Guatemala');
    	$vehiculo->fecha_actualizacion=$mytime->toDateTimeString();
    	$vehiculo->update();
    	return Redirect::to('vehiculos');
    }

    public function destroy($id)
    {
        
            $vehiculo=Vehiculo::findOrFail($id);
            $vehiculo->delete();
            return Redirect::to('vehiculos');
        
    	
    }

    public function imgdestroy($idimgvehiculo)
    {
        $imgvehiculo=Imgvehiculo::findOrFail($idimgvehiculo);
        $imgvehiculo->delete();
        return back();
    }

    public function contactoEnviarVehi(MensajeFormRequest $request)
    {
        $mensaje = null;
        if ($request)
        {
       
            $id = $request->get('id');
            $nom= $request->get('nom');
            $name = $request->get('nombre');
            $email = $request->get('email');
            $subject = $request->get('asunto');
            $msg = $request->get('mensaje');
            $mensaje = 'Mensaje enviado correctamente'; 

            $data = array(
                'nom' => $nom,
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'msg' => $msg
            );

            $fromEmail = 'ottoszarata@szystems.com';
            $fromName = 'Solicitud de Información';

            Mail::send('emails.emailvehiculo', $data, function($message) use($fromName, $fromEmail)
                {
                    $message->to($fromEmail, $fromName);
                    $message->from($fromEmail, $fromName);
                    $message->subject('Solicitud de Información');
                } 
            );
             
            
        }
        $mensaje = 'Mensaje enviado correctamente, en breve un agente de Grupo Jireh se contactará contigo.';
        return view("show");

        
    }

    
}
