@extends('layouts.admin')

@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-piggy-bank"></i>
                </div>
                <div class="page-title">
                    <h5>Tipos de Comisiones</h5>
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

            @include('admin.tipocomision.search')

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">

                        <div class="card-header">
                            <div class="card-title">
                                Listado de tipos de comisiones
                                <a href="{{ url('add-tipo-comision') }}" type="button" class="btn btn-success float-end">
                                    <i class="bi bi-plus-square"></i> Agregar
                                </a>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-striped flex-column">
                                    <thead>
                                        <tr>
                                            <td align="center"><i class="bi bi-list-task"></i></td>
                                            <td>Nombre</td>
                                            <td>Descripción</td>
                                            <td>Porcentaje</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tipocomisiones as $tipocomision)
                                        <tr>
                                            <td align="center">
                                                <div class="btn-group dropend">
                                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                                        <i class="bi bi-list-task"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ url('show-tipo-comision/'.$tipocomision->id) }}"><i class="bi bi-eye-fill text-blue"></i> Información</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ url('edit-tipo-comision/'.$tipocomision->id) }}"><i class="bi bi-pencil-fill text-warning"></i> Editar</a>
                                                        </li>
                                                        <li>
                                                            <a type="button" class="btn bg-gradient-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $tipocomision->id }}">
                                                                <i class="bi bi-trash-fill text-danger"></i> Eliminar
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    {{-- @if ($user->fotografia != null)
                                                        <img src="{{ asset('assets/imgs/users/'.$user->fotografia) }}" class="img-4x rounded-5 me-3" alt="Doctores" />
                                                    @else
                                                        <img src="{{ asset('assets/imgs/users/doctoricon.png') }}" class="img-4x rounded-5 me-3" alt="Doctores" />
                                                    @endif --}}

                                                    <p class="m-0">
                                                        <a class="text-primary" href="{{ url('show-tipo-comision/'.$tipocomision->id) }}"><b>{{ $tipocomision->nombre }}</a>
                                                    </p>
                                                    <br>
                                                </div>
                                            </td>
                                            <td><small>{{ $tipocomision->descripcion }}</small></td>
                                            <td><small>{{ $tipocomision->porcentaje }} %</small></td>

                                        </tr>
                                        @include('admin.tipocomision.deletemodal')
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $tipocomisiones->links() }}
                            </div>
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

