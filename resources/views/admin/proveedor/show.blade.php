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
                        <div class="card-body">
                            <div class="custom-tabs-container">
                                <div class="col-12 col-md-auto float-end">
                                    <div class="btn-group-sm m-3">
                                        <a target="_blank" href="{{ url('pdf-proveedor/'.$proveedor->id) }}" type="button" class="btn btn-info">
                                            <i class="bi bi-printer"></i> Imprimir
                                        </a>
                                        <a href="{{ url('edit-proveedor/'.$proveedor->id) }}" class="btn btn-warning" aria-current="page"><i class="bi bi-pencil"></i> Editar</a>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $proveedor->id }}">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                        @include('admin.proveedor.deletemodal')
                                    </div>
                                </div>
                                <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA" role="tab"
                                            aria-controls="oneA" aria-selected="true">Información</a>
                                    </li>
                                </ul>
                                <div class="tab-content h-350">
                                    <div class="tab-pane fade show active" id="oneA" role="tabpanel">
                                        <!-- Row start -->
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <div class="row gx-3">

                                                    <div class="col-md-8 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label for="fullName" class="form-label">Nombre</label>
                                                            <p>{{ $proveedor->nombre }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label for="fullName" class="form-label">NIT</label>
                                                            <p>{{ $proveedor->nit }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Contacto</label>
                                                            <p>{{ $proveedor->contacto }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label for="contactNumber" class="form-label">Teléfono / Celular / Whatsapp</label>
                                                            <p>
                                                                <a class="text-info" href="tel:+502{{ $proveedor->telefono }}">{{ $proveedor->telefono }}</a>
                                                                @if ($proveedor->celular != null)
                                                                    <a class="text-info" href="tel:+502{{ $proveedor->celular }}">/ {{ $proveedor->celular }}</a>
                                                                    <a class="text-success" href="https://wa.me/502{{ $proveedor->celular }}" target="_blank">/ <i class="bi bi-whatsapp"></i></a>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label for="emailId" class="form-label">Email</label>
                                                            <p><a class="link-info" href="mailto:{{ $proveedor->email }}">{{ $proveedor->email }}</a></p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Contacto</label>
                                                            <p>{{ $proveedor->direccion }}</p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="col-md-3 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Banco</label>
                                                            <p>{{ $proveedor->banco }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Nombre Cuenta</label>
                                                            <p>{{ $proveedor->nombre_cuenta }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Tipo Cuenta</label>
                                                            <p>{{ $proveedor->tipo_cuenta }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Número Cuenta</label>
                                                            <p>{{ $proveedor->numero_cuenta }}</p>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <!-- Row end -->
                                    </div>

                                </div>
                                {{-- <div class="d-flex gap-2 justify-content-end">
                                    <button type="button" class="btn btn-outline-secondary">
                                        Cancel
                                    </button>
                                    <button type="button" class="btn btn-success">
                                        Update
                                    </button>
                                </div> --}}
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
