@extends('layouts.admin')
@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-person-video2"></i>
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

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header bg-primary bg-gradient">
                            <h5 class="card-title text-white mb-3">
                                <i class="bi bi-person-vcard-fill me-2"></i>Información del Proveedor: {{ $proveedor->nombre }}
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong><i class="bi bi-check-circle"></i> ¡Éxito!</strong> {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="custom-tabs-container">
                                <div class="col-12 col-md-auto mb-3">
                                    <div class="btn-group float-end">
                                        <a target="_blank" href="{{ url('pdf-proveedor/'.$proveedor->id) }}" type="button" class="btn btn-danger">
                                            <i class="bi bi-file-pdf"></i> PDF
                                        </a>
                                        <a href="{{ url('edit-proveedor/'.$proveedor->id) }}" class="btn btn-warning" aria-current="page">
                                            <i class="bi bi-pencil-fill"></i> Editar
                                        </a>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $proveedor->id }}">
                                            <i class="bi bi-trash-fill"></i> Eliminar
                                        </button>
                                    </div>
                                </div>

                                <div class="tab-content">
                                    <!-- Información Principal -->
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-8">
                                                            <h6 class="text-primary"><i class="bi bi-building me-2"></i>Datos Generales</h6>
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <p class="mb-1"><strong class="text-dark">Nombre:</strong></p>
                                                                    <p class="text-muted">{{ $proveedor->nombre }}</p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p class="mb-1"><strong class="text-dark">NIT:</strong></p>
                                                                    <p class="text-muted">{{ $proveedor->nit }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Información de Contacto -->
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6 class="text-info"><i class="bi bi-person-lines-fill me-2"></i>Información de Contacto</h6>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <p class="mb-1"><strong class="text-dark">Contacto:</strong></p>
                                                            <p class="text-muted">{{ $proveedor->contacto }}</p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <p class="mb-1"><strong class="text-dark">Teléfono / Celular:</strong></p>
                                                            <p>
                                                                <a class="text-primary" href="tel:+502{{ $proveedor->telefono }}">
                                                                    <i class="bi bi-telephone"></i> {{ $proveedor->telefono }}
                                                                </a>
                                                                @if ($proveedor->celular != null)
                                                                <br>
                                                                <a class="text-primary" href="tel:+502{{ $proveedor->celular }}">
                                                                    <i class="bi bi-phone"></i> {{ $proveedor->celular }}
                                                                </a>
                                                                <a class="text-success ms-1" href="https://wa.me/502{{ $proveedor->celular }}" target="_blank">
                                                                    <i class="bi bi-whatsapp"></i>
                                                                </a>
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <p class="mb-1"><strong class="text-dark">Email:</strong></p>
                                                            <p>
                                                                <a class="text-info" href="mailto:{{ $proveedor->email }}">
                                                                    <i class="bi bi-envelope"></i> {{ $proveedor->email }}
                                                                </a>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <p class="mb-1"><strong class="text-dark">Dirección:</strong></p>
                                                            <p class="text-muted">
                                                                <i class="bi bi-geo-alt"></i> {{ $proveedor->direccion }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Información Bancaria -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6 class="text-success"><i class="bi bi-bank me-2"></i>Información Bancaria</h6>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <p class="mb-1"><strong class="text-dark">Banco:</strong></p>
                                                            <p class="text-muted">{{ $proveedor->banco }}</p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <p class="mb-1"><strong class="text-dark">Nombre Cuenta:</strong></p>
                                                            <p class="text-muted">{{ $proveedor->nombre_cuenta }}</p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <p class="mb-1"><strong class="text-dark">Tipo Cuenta:</strong></p>
                                                            <p class="text-muted">{{ $proveedor->tipo_cuenta }}</p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <p class="mb-1"><strong class="text-dark">Número Cuenta:</strong></p>
                                                            <p class="text-muted">{{ $proveedor->numero_cuenta }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                <a href="{{ url('proveedores') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Volver al listado
                                </a>
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

    @include('admin.proveedor.deletemodal')
@endsection
