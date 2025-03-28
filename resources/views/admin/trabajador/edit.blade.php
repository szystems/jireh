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
                    <h5>Trabajadores</h5>
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
                        <div class="row align-items-end">
                            <div class="col-auto">
                                <div class="avatar-circle bg-primary">
                                    <i class="bi bi-person-circle text-white display-4"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6>Trabajador</h6>
                                <h4 class="m-0 d-flex align-items-center">
                                    {{ $trabajador->nombre }}
                                    @if($trabajador->estado !== 'activo')
                                        <span class="badge bg-danger ms-2">Inactivo</span>
                                    @else
                                        <span class="badge bg-success ms-2">Activo</span>
                                    @endif
                                </h4>
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
                                <div class="custom-tabs-container">
                                    <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA" role="tab"
                                                aria-controls="oneA" aria-selected="true">Editar Información</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="oneA" role="tabpanel">
                                            <!-- Row start -->
                                            <div class="row gx-3">
                                                <div class="col-sm-12 col-12">
                                                    @if (count($errors)>0)
                                                        <div class="alert alert-danger" role="alert">
                                                            <ul class="mb-0">
                                                                @foreach ($errors->all() as $error)
                                                                    <li>{{$error}}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    <form action="{{ url('update-trabajador/'.$trabajador->id) }}" method="POST" id="trabajadorEditForm" class="needs-validation" novalidate>
                                                        @csrf
                                                        @method('PUT')

                                                        <div class="card mb-3">
                                                            <div class="card-header bg-light">
                                                                <h6 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Información Personal</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row gx-3">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                                                        <div class="input-group">
                                                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                                                            <input name="nombre" type="text" class="form-control" id="nombre"
                                                                                placeholder="Nombre completo" value="{{ $trabajador->nombre }}" required />
                                                                            <div class="invalid-feedback">
                                                                                Por favor ingrese el nombre del trabajador.
                                                                            </div>
                                                                        </div>
                                                                        @if ($errors->has('nombre'))
                                                                            <span class="text-danger small">{{ $errors->first('nombre') }}</span>
                                                                        @endif
                                                                    </div>

                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                                                        <div class="input-group">
                                                                            <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                                                                            <input name="fecha_nacimiento" type="date" class="form-control" id="fecha_nacimiento"
                                                                                value="{{ $trabajador->fecha_nacimiento }}" />
                                                                        </div>
                                                                        @if ($errors->has('fecha_nacimiento'))
                                                                            <span class="text-danger small">{{ $errors->first('fecha_nacimiento') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="card mb-3">
                                                            <div class="card-header bg-light">
                                                                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información de Contacto</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row gx-3">
                                                                    <div class="col-md-4 mb-3">
                                                                        <label for="email" class="form-label">Email</label>
                                                                        <div class="input-group">
                                                                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                                                            <input name="email" type="email" class="form-control" id="email"
                                                                                placeholder="correo@ejemplo.com" value="{{ $trabajador->email }}" />
                                                                            <div class="invalid-feedback">
                                                                                Por favor ingrese un email válido.
                                                                            </div>
                                                                        </div>
                                                                        @if ($errors->has('email'))
                                                                            <span class="text-danger small">{{ $errors->first('email') }}</span>
                                                                        @endif
                                                                    </div>

                                                                    <div class="col-md-4 mb-3">
                                                                        <label for="telefono" class="form-label">Teléfono</label>
                                                                        <div class="input-group">
                                                                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                                                            <input name="telefono" type="text" class="form-control" id="telefono"
                                                                                placeholder="Teléfono" value="{{ $trabajador->telefono }}" />
                                                                        </div>
                                                                        @if ($errors->has('telefono'))
                                                                            <span class="text-danger small">{{ $errors->first('telefono') }}</span>
                                                                        @endif
                                                                    </div>

                                                                    <div class="col-md-4 mb-3">
                                                                        <label for="no_documento" class="form-label">No. Documento</label>
                                                                        <div class="input-group">
                                                                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                                                            <input name="no_documento" type="text" class="form-control" id="no_documento"
                                                                                placeholder="No. Documento" value="{{ $trabajador->no_documento }}" />
                                                                        </div>
                                                                        @if ($errors->has('no_documento'))
                                                                            <span class="text-danger small">{{ $errors->first('no_documento') }}</span>
                                                                        @endif
                                                                    </div>

                                                                    <div class="col-md-12 mb-3">
                                                                        <label for="direccion" class="form-label">Dirección</label>
                                                                        <div class="input-group">
                                                                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                                                            <input name="direccion" type="text" class="form-control" id="direccion"
                                                                                placeholder="Dirección completa" value="{{ $trabajador->direccion }}" />
                                                                        </div>
                                                                        @if ($errors->has('direccion'))
                                                                            <span class="text-danger small">{{ $errors->first('direccion') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="card mb-3">
                                                            <div class="card-header bg-light">
                                                                <h6 class="mb-0"><i class="bi bi-toggle-on me-2"></i>Estado del Trabajador</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <label class="form-label">Estado <span class="text-danger">*</span></label>
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" name="estado" id="estadoActivo"
                                                                                    value="activo" {{ $trabajador->estado === 'activo' ? 'checked' : '' }} required>
                                                                                <label class="form-check-label" for="estadoActivo">
                                                                                    <span class="badge bg-success">Activo</span>
                                                                                </label>
                                                                            </div>
                                                                            <div class="form-check form-check-inline">
                                                                                <input class="form-check-input" type="radio" name="estado" id="estadoInactivo"
                                                                                    value="inactivo" {{ $trabajador->estado === 'inactivo' ? 'checked' : '' }} required>
                                                                                <label class="form-check-label" for="estadoInactivo">
                                                                                    <span class="badge bg-danger">Inactivo</span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                        @if ($errors->has('estado'))
                                                                            <span class="text-danger small">{{ $errors->first('estado') }}</span>
                                                                        @endif

                                                                        <div class="alert alert-info mt-2 small">
                                                                            <i class="bi bi-info-circle me-1"></i>
                                                                            Los trabajadores inactivos no aparecerán en las listas por defecto.
                                                                            Si desactiva un trabajador, podrá reactivarlo posteriormente.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex gap-2 justify-content-center mt-4">
                                                            <a href="{{ url('show-trabajador/'.$trabajador->id) }}" class="btn btn-danger">
                                                                <i class="bi bi-x-circle me-1"></i> Cancelar
                                                            </a>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="bi bi-check2-square me-1"></i> Actualizar Trabajador
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <!-- Row end -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row end -->
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
        // Validación del lado del cliente
        var form = document.getElementById('trabajadorEditForm');

        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        }, false);
    });
</script>
@endsection
