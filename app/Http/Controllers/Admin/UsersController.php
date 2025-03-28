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
        $queryUser = $request->input('fuser');
        $role_filter = $request->input('role_filter');

        $usersQuery = DB::table('users')
            ->where('estado', '=', 1);

        // Aplicar filtro de búsqueda por nombre, email, teléfono o celular
        if($queryUser) {
            $usersQuery->where(function ($query) use ($queryUser) {
                $query->where('name', 'LIKE', '%' . $queryUser . '%')
                    ->orWhere('email', 'LIKE', '%' . $queryUser . '%')
                    ->orWhere('telefono', 'LIKE', '%' . $queryUser . '%')
                    ->orWhere('celular', 'LIKE', '%' . $queryUser . '%');
            });
        }

        // Aplicar filtro por rol si está definido
        if($role_filter !== null && $role_filter !== '') {
            $usersQuery->where('role_as', '=', $role_filter);
        }

        $users = $usersQuery->orderBy('name', 'asc')->paginate(20);
        $filterUsers = User::all();

        return view('admin.user.index', compact('users', 'queryUser', 'filterUsers', 'role_filter'));
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
        // Verificar si el usuario autenticado es administrador
        $isAdmin = Auth::user()->role_as == 0;
        return view('admin.user.add', compact('isAdmin'));
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

        // Asignar role_as solo si el usuario autenticado es administrador
        if (Auth::user()->role_as == 0 && $request->has('role_as')) {
            $user->role_as = $request->input('role_as');
        } else {
            $user->role_as = 1; // Por defecto, asignar como Vendedor
        }

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
        // Verificar si el usuario autenticado es administrador
        $isAdmin = Auth::user()->role_as == 0;
        return view('admin.user.edit', compact('user', 'isAdmin'));
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

        // Actualizar role_as solo si el usuario autenticado es administrador
        if (Auth::user()->role_as == 0 && $request->has('role_as')) {
            $user->role_as = $request->input('role_as');
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
        $queryUser = $request->input('fuser');
        $role_filter = $request->input('role_filter');

        $usuariosQuery = User::where('estado', 1);

        // Aplicar filtros
        if($queryUser) {
            $usuariosQuery->where(function ($query) use ($queryUser) {
                $query->where('name', 'LIKE', '%' . $queryUser . '%')
                    ->orWhere('email', 'LIKE', '%' . $queryUser . '%')
                    ->orWhere('telefono', 'LIKE', '%' . $queryUser . '%')
                    ->orWhere('celular', 'LIKE', '%' . $queryUser . '%');
            });
        }

        if($role_filter !== null && $role_filter !== '') {
            $usuariosQuery->where('role_as', '=', $role_filter);
        }

        $usuarios = $usuariosQuery->orderBy('name', 'asc')->get();
        $verpdf = "Browser";
        $nompdf = date('m/d/Y g:ia');
        $path = public_path('assets/imgs/');

        $config = Config::first();
        $currency = $config->currency_simbol;

        if ($config->logo == null) {
            $logo = null;
            $imagen = null;
        } else {
            $logo = $config->logo;
            $imagen = public_path('assets/imgs/logos/'.$logo);
        }

        // Título del PDF
        $titulo = 'Listado de Usuarios';
        if($queryUser) {
            $titulo .= ' (Filtro: '.$queryUser.')';
        }
        if($role_filter !== null && $role_filter !== '') {
            $rolName = $role_filter == '0' ? 'Administradores' : 'Vendedores';
            $titulo .= ' - '.$rolName;
        }

        if ($verpdf == "Download") {
            $pdf = PDF::loadView('admin.user.pdf', [
                'usuarios' => $usuarios,
                'path' => $path,
                'config' => $config,
                'imagen' => $imagen,
                'currency' => $currency,
                'titulo' => $titulo
            ]);
            return $pdf->download($titulo.' '.$nompdf.'.pdf');
        }

        if ($verpdf == "Browser") {
            $pdf = PDF::loadView('admin.user.pdf', [
                'usuarios' => $usuarios,
                'path' => $path,
                'config' => $config,
                'imagen' => $imagen,
                'currency' => $currency,
                'titulo' => $titulo
            ]);
            return $pdf->stream($titulo.' '.$nompdf.'.pdf');
        }
    }

    public function pdfuser($id)
    {
        $usuario = User::find($id);
        $verpdf = "Browser";
        $nompdf = date('m/d/Y g:ia');

        // Configuración
        $config = Config::first();
        $currency = $config->currency_simbol;

        // Obtener rutas absolutas para las imágenes
        $pathuser = public_path('assets/imgs/users/');
        $defaultImagePath = public_path('assets/imgs/users/usericon4.png');

        // Imagen del logo
        $imagen = null;
        if ($config->logo && file_exists(public_path('assets/imgs/logos/'.$config->logo))) {
            $imagen = public_path('assets/imgs/logos/'.$config->logo);
        }

        // Establecer tamaño de papel y orientación
        $pdftamaño = 'Letter';
        $pdfhorientacion = 'portrait';

        if ($verpdf == "Download") {
            $pdf = PDF::loadView('admin.user.pdfuser', compact('usuario', 'pathuser', 'defaultImagePath', 'config', 'imagen', 'currency'));

            // Configuración adicional para DOMPDF
            $pdf->getDomPDF()->set_option("enable_html5_parser", true);
            $pdf->getDomPDF()->set_option("isHtml5ParserEnabled", true);
            $pdf->getDomPDF()->set_option("isRemoteEnabled", true);

            $pdf->setPaper($pdftamaño, $pdfhorientacion);

            return $pdf->download('Usuario_'.$usuario->name.'_'.$nompdf.'.pdf');
        }

        if ($verpdf == "Browser") {
            $pdf = PDF::loadView('admin.user.pdfuser', compact('usuario', 'pathuser', 'defaultImagePath', 'config', 'imagen', 'currency'));

            // Configuración adicional para DOMPDF
            $pdf->getDomPDF()->set_option("enable_html5_parser", true);
            $pdf->getDomPDF()->set_option("isHtml5ParserEnabled", true);
            $pdf->getDomPDF()->set_option("isRemoteEnabled", true);

            $pdf->setPaper($pdftamaño, $pdfhorientacion);

            return $pdf->stream('Usuario_'.$usuario->name.'_'.$nompdf.'.pdf');
        }
    }
}
