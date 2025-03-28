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

                                <div class="d-flex justify-content-between mt-2">
                                    <a target="_blank" href="{{ url('pdf-proveedores') }}" type="button" class="btn btn-danger btn-sm">
                                        <i class="bi bi-file-pdf"></i> PDF
                                    </a>
                                    <a href="{{ url('add-proveedor') }}" type="button" class="btn btn-success btn-sm">
                                        <i class="bi bi-plus-square"></i> Agregar
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong><i class="bi bi-check-circle"></i> ¡Éxito!</strong> {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table id="highlightRowColumn" class="table custom-table">
                                    <thead>
                                        <tr>
                                            <th width="12%" class="text-center">Acciones</th>
                                            <th width="23%">Proveedor</th>
                                            <th width="10%">NIT</th>
                                            <th width="30%">Contacto</th>
                                            <th width="25%">Banco</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($proveedores as $proveedor)
                                        <tr>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ url('show-proveedor/'.$proveedor->id) }}" class="btn btn-sm btn-info" title="Ver información">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </a>
                                                    <a href="{{ url('edit-proveedor/'.$proveedor->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $proveedor->id }}" title="Eliminar">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <p class="m-0">
                                                        <a class="text-primary fw-bold" href="{{ url('show-proveedor/'.$proveedor->id) }}">{{ $proveedor->nombre }}</a>
                                                    </p>
                                                </div>
                                            </td>
                                            <td>{{ $proveedor->nit }}</td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold">{{ $proveedor->contacto }}</span>
                                                    <small>
                                                        <a class="text-info" href="mailto:{{ $proveedor->email }}">{{ $proveedor->email }}</a>
                                                    </small>
                                                    <small>
                                                        <a class="text-secondary" href="tel:+502{{ $proveedor->telefono }}">{{ $proveedor->telefono }}</a>
                                                        @if ($proveedor->celular != null)
                                                        / <a class="text-secondary" href="tel:+502{{ $proveedor->celular }}">{{ $proveedor->celular }}</a>
                                                        <a class="text-success ms-1" href="https://wa.me/502{{ $proveedor->celular }}" target="_blank"><i class="bi bi-whatsapp"></i></a>
                                                        @endif
                                                    </small>
                                                    <small class="text-muted">{{ $proveedor->direccion }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold">{{ $proveedor->banco }}</span>
                                                    <small>{{ $proveedor->nombre_cuenta }}</small>
                                                    <small>{{ $proveedor->tipo_cuenta }}</small>
                                                    <small class="text-muted">{{ $proveedor->numero_cuenta }}</small>
                                                </div>
                                            </td>
                                        </tr>
                                        @include('admin.proveedor.deletemodal')
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $proveedores->links() }}
                                </div>
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

