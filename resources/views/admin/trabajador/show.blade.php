@extends('layouts.admin')
@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="page-title">
                    <h5>Trabajadores</h5>
                </div>
            </div>
            <!-- Date range start -->
            <div class="d-flex align-items-end  d-none d-sm-block">
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
                                <div class="avatar-circle bg-primary">
                                    <i class="bi bi-person-circle text-white display-4"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6>Trabajador</h6>
                                <div class="d-flex align-items-center">
                                    <h4 class="m-0">{{ $trabajador->nombre }}</h4>
                                    @if($trabajador->estado !== 'activo')
                                        <span class="badge bg-danger ms-2">Inactivo</span>
                                    @else
                                        <span class="badge bg-success ms-2">Activo</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ url('edit-trabajador/'.$trabajador->id) }}" class="btn btn-warning">
                                        <i class="bi bi-pencil me-1"></i> Editar
                                    </a>
                                    <button type="button" class="btn {{ $trabajador->estado === 'activo' ? 'btn-danger' : 'btn-success' }}"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $trabajador->id }}">
                                        @if($trabajador->estado === 'activo')
                                            <i class="bi bi-person-dash me-1"></i> Desactivar
                                        @else
                                            <i class="bi bi-person-check me-1"></i> Activar
                                        @endif
                                    </button>
                                    <a href="{{ url('trabajadores') }}" class="btn btn-secondary">
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
                    <div class="col-lg-12">
                        <div class="card light">
                            <div class="card-body">
                                @if (count($errors)>0)
                                    <div class="alert alert-danger" role="alert">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{$error}}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Información Personal</h6>
                                            </div>
                                            <div class="card-body">
                                                <dl class="row mb-0">
                                                    <dt class="col-sm-4">Nombre:</dt>
                                                    <dd class="col-sm-8">{{ $trabajador->nombre }}</dd>

                                                    <dt class="col-sm-4">Fecha de Nacimiento:</dt>
                                                    <dd class="col-sm-8">
                                                        @if ($trabajador->fecha_nacimiento)
                                                            {{ \Carbon\Carbon::parse($trabajador->fecha_nacimiento)->format('d/m/Y') }}
                                                            <span class="badge bg-secondary">
                                                                {{ \Carbon\Carbon::parse($trabajador->fecha_nacimiento)->age }} años
                                                            </span>
                                                        @else
                                                            <span class="text-muted">No registrada</span>
                                                        @endif
                                                    </dd>

                                                    <dt class="col-sm-4">No. Documento:</dt>
                                                    <dd class="col-sm-8">
                                                        {{ $trabajador->no_documento ?: 'No registrado' }}
                                                    </dd>

                                                    <dt class="col-sm-4">Estado:</dt>
                                                    <dd class="col-sm-8">
                                                        @if($trabajador->estado === 'activo')
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
                                                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información de Contacto</h6>
                                            </div>
                                            <div class="card-body">
                                                <dl class="row mb-0">
                                                    <dt class="col-sm-4">Email:</dt>
                                                    <dd class="col-sm-8">
                                                        @if($trabajador->email)
                                                            <a href="mailto:{{ $trabajador->email }}" class="d-flex align-items-center">
                                                                <i class="bi bi-envelope me-1 text-primary"></i>
                                                                {{ $trabajador->email }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">No registrado</span>
                                                        @endif
                                                    </dd>

                                                    <dt class="col-sm-4">Teléfono:</dt>
                                                    <dd class="col-sm-8">
                                                        @if($trabajador->telefono)
                                                            <div class="d-flex align-items-center">
                                                                <a href="tel:+502{{ preg_replace('/[^0-9]/', '', $trabajador->telefono) }}" class="me-2">
                                                                    <i class="bi bi-telephone me-1 text-primary"></i>
                                                                    {{ $trabajador->telefono }}
                                                                </a>
                                                                <a href="https://wa.me/502{{ preg_replace('/[^0-9]/', '', $trabajador->telefono) }}"
                                                                   target="_blank" class="btn btn-sm btn-success">
                                                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                                                </a>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">No registrado</span>
                                                        @endif
                                                    </dd>

                                                    <dt class="col-sm-4">Dirección:</dt>
                                                    <dd class="col-sm-8">
                                                        @if($trabajador->direccion)
                                                            <div class="d-flex align-items-start">
                                                                <i class="bi bi-geo-alt me-1 text-primary mt-1"></i>
                                                                <span>{{ $trabajador->direccion }}</span>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">No registrada</span>
                                                        @endif
                                                    </dd>
                                                </dl>
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
                                                        {{ $trabajador->created_at->format('d/m/Y H:i') }}
                                                    </dd>

                                                    <dt class="col-sm-3">Última Actualización:</dt>
                                                    <dd class="col-sm-9">
                                                        {{ $trabajador->updated_at->format('d/m/Y H:i') }}
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

                @include('admin.trabajador.deletemodal')
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
        // Inicializar tooltips si se utilizan
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection
