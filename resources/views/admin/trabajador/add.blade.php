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
                                <h6>Nuevo Trabajador</h6>
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
                                                aria-controls="oneA" aria-selected="true">Crear Trabajador</a>
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
                                                    <form action="{{ url('insert-trabajador') }}" method="POST" id="trabajadorForm" class="needs-validation" novalidate>
                                                        @csrf
                                                        <input type="hidden" name="estado" value="activo" />

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
                                                                                placeholder="Nombre completo" value="{{ old('nombre') }}" required />
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
                                                                                value="{{ old('fecha_nacimiento') }}" />
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
                                                                <h6 class="mb-0"><i class="bi bi-tags me-2"></i>Tipo de Trabajador</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row gx-3">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="tipo" class="form-label">Tipo de Trabajador <span class="text-danger">*</span></label>
                                                                        <div class="input-group">
                                                                            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                                                            <select name="tipo" id="tipo" class="form-control" required>
                                                                                <option value="">Seleccione un tipo</option>
                                                                                <option value="mecanico">Mecánico</option>
                                                                                <option value="carwash">Lavador Car Wash</option>
                                                                                <option value="general">General</option>
                                                                            </select>
                                                                            <div class="invalid-feedback">
                                                                                Por favor seleccione el tipo de trabajador.
                                                                            </div>
                                                                        </div>
                                                                        @if ($errors->has('tipo'))
                                                                            <span class="text-danger small">{{ $errors->first('tipo') }}</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="tipo_trabajador_id" class="form-label">Tipo de Trabajador</label>
                                                                        <div class="input-group">
                                                                            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                                                            <select class="form-select" id="tipo_trabajador_id" name="tipo_trabajador_id">
                                                                                <option value="">Seleccione un tipo</option>
                                                                                @foreach($tipoTrabajadores as $tipo)
                                                                                    <option value="{{ $tipo->id }}" {{ old('tipo_trabajador_id') == $tipo->id ? 'selected' : '' }}>
                                                                                        {{ $tipo->nombre }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        @error('tipo_trabajador_id')
                                                                            <span class="text-danger">{{ $message }}</span>
                                                                        @enderror
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
                                                                                placeholder="correo@ejemplo.com" value="{{ old('email') }}" />
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
                                                                                placeholder="Teléfono" value="{{ old('telefono') }}" />
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
                                                                                placeholder="No. Documento" value="{{ old('no_documento') }}" />
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
                                                                                placeholder="Dirección completa" value="{{ old('direccion') }}" />
                                                                        </div>
                                                                        @if ($errors->has('direccion'))
                                                                            <span class="text-danger small">{{ $errors->first('direccion') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex gap-2 justify-content-center mt-4">
                                                            <a href="{{ url('trabajadores') }}" class="btn btn-danger">
                                                                <i class="bi bi-x-circle me-1"></i> Cancelar
                                                            </a>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="bi bi-check2-square me-1"></i> Guardar Trabajador
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
        var form = document.getElementById('trabajadorForm');

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
