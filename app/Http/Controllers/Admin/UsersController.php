<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserFormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\Config;
use Carbon\Carbon;
use PDF;
use DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserMail;

class UsersController extends Controller
{
    public function users(Request $request)
    {
        if ($request)
        {
            $queryUser=$request->input('fuser');
            $users = DB::table('users')
            ->where('estado', '=', 1)
            ->where('role_as', '=', 0)
            ->where(function ($query) use ($queryUser) {
            $query->where('name', 'LIKE', '%' . $queryUser . '%')
                ->orWhere('email', 'LIKE', '%' . $queryUser . '%')
                ->orWhere('telefono', 'LIKE', '%' . $queryUser . '%')
                ->orWhere('celular', 'LIKE', '%' . $queryUser . '%');
            })
            ->orderBy('name','asc')
            ->paginate(20);
            $filterUsers = User::all();
            return view('admin.user.index', compact('users','queryUser','filterUsers'));
        }
    }

    public function showuser(Request $request, $id)
    {
        $user = User::find($id);
        $hoy = Carbon::now('America/Guatemala');
        $fechaVista = $hoy->format('d-m-Y');
        $fecha = date("Y-m-d", strtotime($fechaVista));
        $filtros = $request->all();

        return view('admin.user.show', compact('user','fecha','filtros','fechaVista'));
    }

    public function adduser()
    {
        return view('admin.user.add');
    }

    public function insertuser(UserFormRequest $request)
    {
        $user = new User();
        $fecha_nacimiento = $request->get('fecha_nacimiento');
        if($request->hasFile('fotografia'))
        {
            $file = $request->file('fotografia');
            $ext = $file->getClientOriginalExtension();
            $filename = time().'.'.$ext;
            $file->move('assets/imgs/users',$filename);
            $user->fotografia = $filename;
        }
        $user->role_as = 0;
        $user->estado = 1;
        $user->principal = 0;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = 'Flebo'.rand(1111,9999);
        $user->telefono = $request->input('telefono');
        $user->celular = $request->input('celular');
        $user->direccion = $request->input('direccion');
        $user->fecha_nacimiento = $fecha_nacimiento;
        $user->save();

        // Mail::to($user->email)->send(new UserMail($user));

        return redirect('users')->with('status', __('Usuario agregado correctamente'));
    }

    public function edituser($id)
    {
        $user = User::find($id);
        return view('admin.user.edit', \compact('user'));
    }

    public function updateuser(UserFormRequest $request, $id)
    {
        $user = User::find($id);
        // $emailrepeat = User::where('id', '<>', $id)->where('email', $request->input('email'))->count();
        $fecha_nacimiento = $request->get('fecha_nacimiento');
        if($request->hasFile('fotografia'))
        {
            $path = 'assets/imgs/users/'.$user->fotografia;
            if(File::exists($path))
            {
                File::delete($path);
            }
            $file = $request->file('fotografia');
            $ext = $file->getClientOriginalExtension();
            $filename = time().'.'.$ext;
            $file->move('assets/imgs/users',$filename);
            $user->fotografia = $filename;
        }
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->telefono = $request->input('telefono');
        $user->celular = $request->input('celular');
        $user->direccion = $request->input('direccion');
        $user->fecha_nacimiento = $fecha_nacimiento;
        $user->update();

        return redirect('show-user/'.$id)->with('status',__('Usuario actualizado correctamente.'));

    }

    public function destroyuser($id)
    {
        $user = User::find($id);
        if ($user->fotografia)
        {
            $path = 'assets/img/users/'.$user->fotografia;
            if (File::exists($path))
            {
                File::delete($path);

            }
        }
        $user->estado = 0;
        $user->email = $user->email.'-Deleted'.$user->id;
        $user->update();
        return redirect('users')->with('status',__('Usuario eliminado correctamente.'));
    }

    public function pdf(Request $request)
    {
        if ($request)
        {

            $usuarios = User::where('estado',1)->orderBy('name','asc')->get();
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
                $pdf = PDF::loadView('admin.user.pdf',['usuarios'=>$usuarios,'path'=>$path,'config'=>$config,'imagen'=>$imagen,'currency'=>$currency]);

                return $pdf->download ('Listado Usuarios '.$nompdf.'.pdf');
            }
            if ( $verpdf == "Browser" )
            {
                $pdf = PDF::loadView('admin.user.pdf',['usuarios'=>$usuarios,'path'=>$path,'config'=>$config,'imagen'=>$imagen,'currency'=>$currency]);

                return $pdf->stream ('Listado Usuarios '.$nompdf.'.pdf');
            }
        }
    }

    public function pdfuser($id)
    {

        $usuario = User::find($id);
        $verpdf = "Browser";
        $nompdf = date('m/d/Y g:ia');
        $path = public_path('assets/imgs/');
        $pathusuario = public_path('assets/imgs/users/');

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
            $pdf = PDF::loadView('admin.user.pdfuser',['usuario'=>$usuario,'path'=>$path,'config'=>$config,'imagen'=>$imagen,'currency'=>$currency,'pathusuario'=>$pathusuario]);

            return $pdf->download ('Usuario: '.$usuario->name.'-'.$nompdf.'.pdf');
        }
        if ( $verpdf == "Browser" )
        {
            $pdf = PDF::loadView('admin.user.pdfuser',['usuario'=>$usuario,'path'=>$path,'config'=>$config,'imagen'=>$imagen,'currency'=>$currency,'pathusuario'=>$pathusuario]);

            return $pdf->stream ('Usuario: '.$usuario->name.'-'.$nompdf.'.pdf');
        }
    }
}
