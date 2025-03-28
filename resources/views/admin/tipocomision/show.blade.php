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
            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Información del Tipo de Comisión</h5>
                                <div class="btn-group">
                                    <a href="{{ url('tipo-comisiones') }}" class="btn btn-secondary me-2">
                                        <i class="bi bi-arrow-left"></i> Volver
                                    </a>
                                    <a href="{{ url('edit-tipo-comision/'.$tipocomision->id) }}" class="btn btn-warning">
                                        <i class="bi bi-pencil-fill"></i> Editar
                                    </a>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $tipocomision->id }}">
                                        <i class="bi bi-trash-fill"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Mensaje de estado -->
                            @if(session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item mb-4">
                                        <h6 class="text-muted mb-2">Nombre</h6>
                                        <h5 class="mb-0 text-primary">{{ $tipocomision->nombre }}</h5>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item mb-4">
                                        <h6 class="text-muted mb-2">Porcentaje</h6>
                                        <h5 class="mb-0">
                                            <span class="badge bg-success fs-6">{{ $tipocomision->porcentaje }}%</span>
                                        </h5>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="info-item">
                                        <h6 class="text-muted mb-2">Descripción</h6>
                                        <p class="mb-0">{{ $tipocomision->descripcion ?: 'Sin descripción' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="text-muted mb-2">Información adicional</h6>
                                            <div class="row">
                                                {{-- <div class="col-md-6">
                                                    <small class="text-muted">ID:</small>
                                                    <p class="mb-0">{{ $tipocomision->id }}</p>
                                                </div> --}}
                                                <div class="col-md-6">
                                                    <small class="text-muted">Estado:</small>
                                                    <p class="mb-0">
                                                        @if($tipocomision->estado)
                                                            <span class="badge bg-success">Activo</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactivo</span>
                                                        @endif
                                                    </p>
                                                </div>
                                                {{-- <div class="col-md-6">
                                                    <small class="text-muted">Fecha de creación:</small>
                                                    <p class="mb-0">
                                                        {{ $tipocomision->created_at ? $tipocomision->created_at->format('d/m/Y H:i') : 'No disponible' }}
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <small class="text-muted">Última modificación:</small>
                                                    <p class="mb-0">
                                                        {{ $tipocomision->updated_at ? $tipocomision->updated_at->format('d/m/Y H:i') : 'No disponible' }}
                                                    </p>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row end -->
            @include('admin.tipocomision.deletemodal')
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->
@endsection
