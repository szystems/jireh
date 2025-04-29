@extends('layouts.admin')
@section('content')

    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-tags"></i>
                </div>
                <div class="page-title">
                    <h5>Detalle de Tipo de Trabajador</h5>
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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title">{{ $tipoTrabajador->nombre }}</h5>
                            <div>
                                <span class="badge {{ $tipoTrabajador->estado == 'activo' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $tipoTrabajador->estado == 'activo' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <h6 class="fw-bold mb-3">Descripción</h6>
                                    <p>{{ $tipoTrabajador->descripcion ?: 'No hay descripción disponible' }}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Configuración de Comisiones</h6>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                                                    <span>Aplica para comisiones:</span>
                                                    @if($tipoTrabajador->aplica_comision)
                                                        <span class="badge bg-success">Sí</span>
                                                    @else
                                                        <span class="badge bg-secondary">No</span>
                                                    @endif
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0">
                                                    <span>Requiere asignación a servicios:</span>
                                                    @if($tipoTrabajador->requiere_asignacion)
                                                        <span class="badge bg-info">Sí</span>
                                                    @else
                                                        <span class="badge bg-secondary">No</span>
                                                    @endif
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">Trabajadores</h6>
                                            <p>
                                                <strong>Total de trabajadores con este tipo:</strong>
                                                <span class="badge bg-primary">{{ $tipoTrabajador->trabajadores()->count() }}</span>
                                            </p>
                                            <a href="{{ url('trabajadores') }}?tipo_trabajador={{ $tipoTrabajador->id }}" class="btn btn-sm btn-outline-primary">
                                                Ver trabajadores
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-2 justify-content-center mt-4">
                                <a href="{{ url('tipo-trabajador') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Volver
                                </a>
                                <a href="{{ url('edit-tipo-trabajador/'.$tipoTrabajador->id) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil-fill me-1"></i> Editar
                                </a>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $tipoTrabajador->id }}">
                                    <i class="bi bi-trash-fill me-1"></i> Eliminar
                                </button>
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

    @include('admin.tipotrabajador.deletemodal')
@endsection
