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
                        <div class="card-header bg-warning bg-gradient">
                            <h5 class="card-title text-white mb-3">
                                <i class="bi bi-pencil-square me-2"></i>Editar Proveedor: {{ $proveedor->nombre }}
                            </h5>
                        </div>
                        <div class="card-body">
                            @if (count($errors)>0)
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>¡Error!</strong> Corrija los siguientes errores:
                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{$error}}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ url('update-proveedor/'.$proveedor->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Datos Generales -->
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="text-primary mb-3"><i class="bi bi-building me-2"></i>Datos Generales</h6>
                                                <div class="row">
                                                    <div class="col-md-8 mb-3">
                                                        <label for="nombre" class="form-label">Nombre del Proveedor <span class="text-danger">*</span></label>
                                                        <input name="nombre" type="text" class="form-control" placeholder="Nombre del proveedor..." value="{{ $proveedor->nombre }}" />
                                                        @if ($errors->has('nombre'))
                                                            <span class="text-danger">{{ $errors->first('nombre') }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="nit" class="form-label"><i class="bi bi-upc me-1"></i>NIT</label>
                                                        <input name="nit" type="text" class="form-control" placeholder="# de NIT..." value="{{ $proveedor->nit }}" />
                                                        @if ($errors->has('nit'))
                                                            <span class="text-danger">{{ $errors->first('nit') }}</span>
                                                        @endif
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
                                                <h6 class="text-info mb-3"><i class="bi bi-person-lines-fill me-2"></i>Información de Contacto</h6>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="contacto" class="form-label"><i class="bi bi-person me-1"></i>Contacto</label>
                                                        <input name="contacto" type="text" class="form-control" placeholder="Nombre del contacto..." value="{{ $proveedor->contacto }}" />
                                                        @if ($errors->has('contacto'))
                                                            <span class="text-danger">{{ $errors->first('contacto') }}</span>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="email" class="form-label"><i class="bi bi-envelope me-1"></i>Email</label>
                                                        <input name="email" type="email" class="form-control" placeholder="Email del proveedor..." value="{{ $proveedor->email }}" />
                                                        @if ($errors->has('email'))
                                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="telefono" class="form-label"><i class="bi bi-telephone me-1"></i>Teléfono</label>
                                                        <input name="telefono" type="number" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control" placeholder="Teléfono del proveedor..." value="{{ $proveedor->telefono }}" />
                                                        @if ($errors->has('telefono'))
                                                            <span class="text-danger">{{ $errors->first('telefono') }}</span>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="celular" class="form-label"><i class="bi bi-phone me-1"></i>Celular</label>
                                                        <div class="input-group">
                                                            <input name="celular" type="number" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control" placeholder="Celular del proveedor..." value="{{ $proveedor->celular }}" />
                                                            <span class="input-group-text bg-success text-white"><i class="bi bi-whatsapp"></i></span>
                                                        </div>
                                                        @if ($errors->has('celular'))
                                                            <span class="text-danger">{{ $errors->first('celular') }}</span>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-12 mb-3">
                                                        <label for="direccion" class="form-label"><i class="bi bi-geo-alt me-1"></i>Dirección</label>
                                                        <input name="direccion" type="text" class="form-control" placeholder="Dirección del proveedor..." value="{{ $proveedor->direccion }}" />
                                                        @if ($errors->has('direccion'))
                                                            <span class="text-danger">{{ $errors->first('direccion') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información Bancaria -->
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="text-success mb-3"><i class="bi bi-bank me-2"></i>Información Bancaria</h6>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="banco" class="form-label">Banco</label>
                                                        <input name="banco" type="text" class="form-control" placeholder="Nombre del banco..." value="{{ $proveedor->banco }}" />
                                                        @if ($errors->has('banco'))
                                                            <span class="text-danger">{{ $errors->first('banco') }}</span>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="nombre_cuenta" class="form-label">Nombre de la Cuenta</label>
                                                        <input name="nombre_cuenta" type="text" class="form-control" placeholder="Nombre de la cuenta..." value="{{ $proveedor->nombre_cuenta }}" />
                                                        @if ($errors->has('nombre_cuenta'))
                                                            <span class="text-danger">{{ $errors->first('nombre_cuenta') }}</span>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="tipo_cuenta" class="form-label">Tipo de Cuenta</label>
                                                        <select name="tipo_cuenta" class="form-select">
                                                            <option value="">Seleccione Tipo</option>
                                                            <option value="Monetaria" {{ $proveedor->tipo_cuenta == 'Monetaria' ? 'selected' : '' }}>Monetaria</option>
                                                            <option value="Ahorro" {{ $proveedor->tipo_cuenta == 'Ahorro' ? 'selected' : '' }}>Ahorro</option>
                                                        </select>
                                                        @if ($errors->has('tipo_cuenta'))
                                                            <span class="text-danger">{{ $errors->first('tipo_cuenta') }}</span>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="numero_cuenta" class="form-label">Número de Cuenta</label>
                                                        <input name="numero_cuenta" type="text" class="form-control" placeholder="Número de la cuenta..." value="{{ $proveedor->numero_cuenta }}" />
                                                        @if ($errors->has('numero_cuenta'))
                                                            <span class="text-danger">{{ $errors->first('numero_cuenta') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ url('proveedores') }}" type="button" class="btn btn-danger">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check2-square"></i> Grabar
                                    </button>
                                </div>
                            </form>
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
