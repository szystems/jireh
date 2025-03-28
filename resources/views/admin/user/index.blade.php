@extends('layouts.admin')

@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="page-title">
                    <h5>Usuarios</h5>
                </div>
            </div>
            <!-- Date range start -->
            <div class="d-flex align-items-end d-none d-sm-block">
                <h6 class="float-end text-light" id="reloj"></h6>
            </div>
        </div>
        <!-- Main header ends -->

        <!-- Content wrapper start -->
        <div class="content-wrapper">

            @include('admin.user.search')

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title d-flex justify-content-between align-items-center">
                                <div>
                                    Listado de Usuarios
                                    <span class="badge bg-secondary">{{ $users->total() }} usuarios</span>
                                </div>
                                <div class="d-flex">
                                    <a target="_blank" href="{{ url('pdf-users') }}{{ $queryUser ? '?fuser='.$queryUser : '' }}{{ isset($role_filter) && $role_filter != '' ? '&role_filter='.$role_filter : '' }}" type="button" class="btn btn-danger me-2">
                                        <i class="bi bi-file-pdf"></i> PDF
                                    </a>
                                    <a href="{{ url('add-user') }}" type="button" class="btn btn-success">
                                        <i class="bi bi-plus-square"></i> Agregar
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($users->count() > 0)
                            <div class="table-responsive">
                                <table class="table align-middle table-striped flex-column">
                                    <thead>
                                        <tr>
                                            <td align="center" width="15%">Acciones</td>
                                            <td width="40%">Usuario</td>
                                            <td align="center" width="10%">Tipo</td>
                                            <td align="center" width="15%">Fecha de Nacimiento</td>
                                            <td width="20%">Dirección</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                        <tr>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ url('show-user/'.$user->id) }}" class="btn btn-info">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </a>
                                                    <a href="{{ url('edit-user/'.$user->id) }}" class="btn btn-warning">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                    @if ($user->principal == "1")
                                                        <button disabled type="button" class="btn btn-secondary">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $user->id }}">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    @endif
                                                    <a href="{{ url('pdf-user/'.$user->id) }}" target="_blank" class="btn btn-primary">
                                                        <i class="bi bi-file-pdf"></i>
                                                    </a>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if ($user->fotografia != null)
                                                        <img src="{{ asset('assets/imgs/users/'.$user->fotografia) }}" class="img-4x rounded-5 me-3" alt="Usuario" />
                                                    @else
                                                        <img src="{{ asset('assets/imgs/users/usericon4.png') }}" class="img-4x rounded-5 me-3" alt="Usuario" />
                                                    @endif

                                                    <p class="m-0">
                                                        <a class="text-primary" href="{{ url('show-user/'.$user->id) }}"><b>{{ $user->name }}</b></a>
                                                        @php
                                                            $fnacimiento = null;
                                                            $edad = 0;
                                                            if ($user->fecha_nacimiento != null) {
                                                                $fnacimiento = date("d-m-Y", strtotime($user->fecha_nacimiento));
                                                                //calcular edad
                                                                $fecha_nacimiento = date("d-m-Y", strtotime($user->fecha_nacimiento));
                                                                $cumpleanos = new DateTime($user->fecha_nacimiento);
                                                                $hoy = new DateTime();
                                                                $annos = $hoy->diff($cumpleanos);
                                                                $edad = $annos->y;
                                                            }
                                                        @endphp
                                                        @if ($edad > 0)
                                                            <small>
                                                                ({{ $edad }} años)
                                                            </small>
                                                        @endif
                                                        <br>
                                                        <small>
                                                            <a class="text-info" href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                                            <br>
                                                            <a class="text-light" href="tel:+502{{ $user->telefono }}">{{ $user->telefono }}</a>
                                                            @if ($user->celular != null)
                                                            / <a class="text-light" href="tel:+502{{ $user->celular }}">{{ $user->celular }}</a>
                                                            / <a class="text-success" href="https://wa.me/502{{ $user->celular }}" target="_blank"><i class="bi bi-whatsapp"></i></a>
                                                            @endif
                                                        </small>
                                                    </p>
                                                </div>
                                            </td>
                                            <td align="center">
                                                <span class="badge {{ $user->role_as == 0 ? 'bg-danger' : 'bg-success' }}">
                                                    {{ $user->role_as == 0 ? 'Administrador' : 'Vendedor' }}
                                                </span>
                                            </td>
                                            <td align="center">
                                                <small>
                                                    {{ $fnacimiento }}
                                                </small>
                                            </td>
                                            <td><small>{{ $user->direccion }}</small></td>
                                        </tr>
                                        @include('admin.user.deletemodal')
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $users->appends(request()->query())->links() }}
                                </div>
                            </div>
                            @else
                            <div class="alert alert-info text-center" role="alert">
                                <i class="bi bi-info-circle me-2"></i> No se encontraron usuarios con los criterios de búsqueda aplicados.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row end -->
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->
@endsection

