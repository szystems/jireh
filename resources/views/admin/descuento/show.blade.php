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
                    <h5>Descuentos</h5>
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
            <div class="subscribe-header">
                <img src="{{ asset('dashboardtemplate/design/assets/images/bg.jpg') }}" class="img-fluid w-100" alt="Header" />
            </div>
            <div class="subscriber-body">
                <!-- Row start -->
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-12">
                        <!-- Row start -->
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar-circle bg-success">
                                    <i class="bi bi-percent text-white display-4"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6>Descuento</h6>
                                <div class="d-flex align-items-center">
                                    <h4 class="m-0">{{ $descuento->nombre }}</h4>
                                    @if(!$descuento->estado)
                                        <span class="badge bg-danger ms-2">Inactivo</span>
                                    @else
                                        <span class="badge bg-success ms-2">Activo</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ url('edit-descuento/'.$descuento->id) }}" class="btn btn-warning">
                                        <i class="bi bi-pencil me-1"></i> Editar
                                    </a>
                                    <button type="button" class="btn {{ $descuento->estado ? 'btn-danger' : 'btn-success' }}" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $descuento->id }}">
                                        @if($descuento->estado)
                                            <i class="bi bi-dash-circle me-1"></i> Desactivar
                                        @else
                                            <i class="bi bi-check-circle me-1"></i> Activar
                                        @endif
                                    </button>
                                    <a href="{{ url('descuentos') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left me-1"></i> Volver
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- Row end -->
                    </div>
                </div>
                <!-- Row end -->

                <!-- Row start -->
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-10">
                        <div class="card light">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información del Descuento</h6>
                                            </div>
                                            <div class="card-body">
                                                <dl class="row mb-0">
                                                    <dt class="col-sm-4">Nombre:</dt>
                                                    <dd class="col-sm-8">{{ $descuento->nombre }}</dd>

                                                    <dt class="col-sm-4">Estado:</dt>
                                                    <dd class="col-sm-8">
                                                        @if($descuento->estado)
                                                            <span class="badge bg-success">Activo</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactivo</span>
                                                        @endif
                                                    </dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="bi bi-percent me-2"></i>Porcentaje de Descuento</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="text-center">
                                                    <div class="display-3 fw-bold text-primary">
                                                        {{ $descuento->porcentaje_descuento }}%
                                                    </div>
                                                    <div class="progress mt-2" style="height: 20px;">
                                                        <div class="progress-bar bg-primary" role="progressbar"
                                                            style="width: {{ min($descuento->porcentaje_descuento, 100) }}%"
                                                            aria-valuenow="{{ $descuento->porcentaje_descuento }}"
                                                            aria-valuemin="0" aria-valuemax="100">
                                                            {{ $descuento->porcentaje_descuento }}%
                                                        </div>
                                                    </div>
                                                    <div class="text-muted mt-2">
                                                        Este descuento reducirá el precio de un producto en un {{ $descuento->porcentaje_descuento }}%.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Información Adicional</h6>
                                            </div>
                                            <div class="card-body">
                                                <dl class="row mb-0">
                                                    <dt class="col-sm-3">Fecha de Registro:</dt>
                                                    <dd class="col-sm-9">
                                                        {{ $descuento->created_at->format('d/m/Y H:i') }}
                                                    </dd>

                                                    <dt class="col-sm-3">Última Actualización:</dt>
                                                    <dd class="col-sm-9">
                                                        {{ $descuento->updated_at->format('d/m/Y H:i') }}
                                                    </dd>
                                                </dl>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row end -->

                @include('admin.descuento.deletemodal')
            </div>
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->
@endsection

@section('styles')
<style>
    .avatar-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips si los usas
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection
