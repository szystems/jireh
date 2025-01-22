@extends('layouts.admin')

@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi-person-video2"></i>
                </div>
                <div class="page-title">
                    <h5>Proveedores</h5>
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

            @include('admin.proveedor.search')

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">

                        <div class="card-header">
                            <div class="card-title">
                                Listado de proveedores
                                <br>
                                <a target="_blank" href="{{ url('pdf-proveedores') }}" type="button" class="btn btn-info btn-sm">
                                    <i class="bi bi-printer"></i> Imprimir
                                </a>
                                <a href="{{ url('add-proveedor') }}" type="button" class="btn btn-success float-end">
                                    <i class="bi bi-plus-square"></i> Agregar
                                </a>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="highlightRowColumn" class="table custom-table">
                                    <thead>
                                        <tr>
                                            <td align="center"><i class="bi bi-list-task"></i></td>
                                            <td>Proveedor</td>
                                            <td>NIT</td>
                                            <td>Contacto</td>
                                            <td>Banco</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($proveedores as $proveedor)
                                        <tr>
                                            <td>
                                                <div class="btn-group dropend">
                                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                                        <i class="bi bi-list-task"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ url('show-proveedor/'.$proveedor->id) }}"><i class="bi bi-eye-fill text-blue"></i> Información</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ url('edit-proveedor/'.$proveedor->id) }}"><i class="bi bi-pencil-fill text-warning"></i> Editar</a>
                                                        </li>
                                                        <li>
                                                            <a type="button" class="btn bg-gradient-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $proveedor->id }}">
                                                                <i class="bi bi-trash-fill text-danger"></i> Eliminar
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <p class="m-0">
                                                        <a class="text-primary" href="{{ url('show-proveedor/'.$proveedor->id) }}"><b>{{ $proveedor->nombre }}</b></a>
                                                    </p>
                                                </div>
                                            </td>
                                            <td><p>{{ $proveedor->nit }}</p></td>
                                            <td>
                                                <div class="d-flex align-items-center">

                                                    <p class="m-0">
                                                        <a class="text-primary" href="{{ url('show-proveedor/'.$proveedor->id) }}"><b>{{ $proveedor->contacto }}</b></a>
                                                        <br>
                                                        <small>
                                                            <a class="text-info" href="mailto:{{ $proveedor->email }}">{{ $proveedor->email }}</a>
                                                            <br>
                                                            <a class="text-light" href="tel:+502{{ $proveedor->telefono }}">{{ $proveedor->telefono }}</a>
                                                            @if ($proveedor->celular != null)
                                                            / <a class="text-light" href="tel:+502{{ $proveedor->celular }}">{{ $proveedor->celular }}</a>

                                                            / <a class="text-success" href="https://wa.me/502{{ $proveedor->celular }}" target="_blank"><i class="bi bi-whatsapp"></i></a>
                                                            @endif
                                                            <br>
                                                            {{$proveedor->direccion}}
                                                        </small>
                                                    </p>

                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">

                                                    <p class="m-0">
                                                        <a class="text-primary" href="{{ url('show-proveedor/'.$proveedor->id) }}"><b>{{ $proveedor->banco }}</b></a>
                                                        <br>
                                                        <small>
                                                            {{ $proveedor->nombre_cuenta }}
                                                            <br>
                                                            {{ $proveedor->tipo_cuenta }}
                                                            <br>
                                                            {{ $proveedor->numero_cuenta }}
                                                        </small>
                                                    </p>

                                                </div>
                                            </td>
                                        </tr>
                                        @include('admin.proveedor.deletemodal')
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $proveedores->links() }}
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

