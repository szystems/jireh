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
                    <h5>Editar Cliente</h5>
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
            <!-- Barra de navegación de cliente -->
            <div class="card bg-primary bg-opacity-10 border-0 mb-3">
                <div class="card-body py-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ url('clientes') }}">Clientes</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('show-cliente/'.$cliente->id) }}">{{ $cliente->nombre }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Editar</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Row start -->
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div id="image-preview-container">
                                        @if ($cliente->fotografia != null)
                                            <img id="preview-image" src="{{ asset('assets/imgs/clientes/'.$cliente->fotografia) }}" class="img-7x rounded-circle shadow-sm border border-2 border-light" />
                                        @else
                                            <img id="preview-image" src="{{ asset('assets/imgs/clientes/usericon4.png') }}" class="img-7x rounded-circle shadow-sm border border-2 border-light" />
                                        @endif
                                    </div>
                                </div>
                                <div class="col">
                                    <h4 class="card-title mb-0">{{ $cliente->nombre }}</h4>
                                    <p class="text-muted small">Actualizar información del cliente</p>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            @if (count($errors)>0)
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>Error:</strong> Por favor corrija los siguientes errores:
                                    <ul class="mt-2 mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{$error}}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form id="clienteForm" action="{{ url('update-cliente/'.$cliente->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <!-- Sección de información personal -->
                                    <div class="col-12">
                                        <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-person-vcard"></i> Información Personal</h5>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input name="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror"
                                                placeholder="Nombre completo del cliente" value="{{ $cliente->nombre }}" required />
                                        </div>
                                        @error('nombre')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                        <div class="input-group">
                                            @php
                                                $fnacimiento = date("d-m-Y", strtotime($cliente->fecha_nacimiento));
                                            @endphp
                                            <input type="text" name="fecha_nacimiento" class="form-control datepicker @error('fecha_nacimiento') is-invalid @enderror"
                                                id="fnacimiento" value="{{ $fnacimiento }}" placeholder="DD-MM-AAAA"/>
                                            <span class="input-group-text">
                                                <i class="bi bi-calendar4"></i>
                                            </span>
                                        </div>
                                        @error('fecha_nacimiento')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Cambiar Fotografía</label>
                                        <div class="input-group">
                                            <input type="file" id="fotografia" name="fotografia" class="form-control @error('fotografia') is-invalid @enderror"
                                                accept="image/*" value="{{ $cliente->fotografia }}">
                                            <span class="input-group-text"><i class="bi bi-camera"></i></span>
                                        </div>
                                        @error('fotografia')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Formatos: JPG, PNG (Max: 2MB)</div>
                                    </div>

                                    <!-- Sección de documentos -->
                                    <div class="col-12 mt-3">
                                        <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-card-heading"></i> Documentos de Identificación</h5>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="dpi" class="form-label">DPI</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                            <input name="dpi" type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                                class="form-control @error('dpi') is-invalid @enderror" placeholder="Número de DPI" value="{{ $cliente->dpi }}"/>
                                        </div>
                                        @error('dpi')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="nit" class="form-label">NIT</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-receipt"></i></span>
                                            <input name="nit" type="text" class="form-control @error('nit') is-invalid @enderror"
                                                placeholder="Número de NIT" value="{{ $cliente->nit }}" />
                                        </div>
                                        @error('nit')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Sección de contacto -->
                                    <div class="col-12 mt-3">
                                        <h5 class="border-bottom pb-2 mb-3"><i class="bi bi-telephone"></i> Información de Contacto</h5>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">+502</span>
                                            <input name="telefono" type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                                class="form-control @error('telefono') is-invalid @enderror" placeholder="12345678"
                                                value="{{ $cliente->telefono }}" required />
                                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        </div>
                                        @error('telefono')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="celular" class="form-label">Celular / WhatsApp</label>
                                        <div class="input-group">
                                            <span class="input-group-text">+502</span>
                                            <input name="celular" type="text" oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                                class="form-control @error('celular') is-invalid @enderror" placeholder="12345678"
                                                value="{{ $cliente->celular }}"/>
                                            <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                                        </div>
                                        @error('celular')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="email" class="form-label">Correo Electrónico</label>
                                        <div class="input-group">
                                            <span class="input-group-text">@</span>
                                            <input name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                                placeholder="ejemplo@correo.com" value="{{ $cliente->email }}" />
                                        </div>
                                        @error('email')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Dirección</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                            <textarea name="direccion" class="form-control @error('direccion') is-invalid @enderror"
                                                rows="2" placeholder="Dirección completa...">{{ $cliente->direccion }}</textarea>
                                        </div>
                                        @error('direccion')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ url('show-cliente/'.$cliente->id) }}" class="btn btn-secondary">
                                                <i class="bi bi-arrow-left"></i> Volver a Detalles
                                            </a>
                                            <div>
                                                <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $cliente->id }}">
                                                    <i class="bi bi-trash"></i> Eliminar Cliente
                                                </button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-check2-circle"></i> Actualizar Cliente
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row end -->

            @include('admin.cliente.deletemodal')
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->

    <script>
        // Configuración del datepicker
        document.addEventListener('DOMContentLoaded', function() {
            var date = new Date();
            var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

            var optSimple = {
                language: "es",
                format: "dd-mm-yyyy",
                autoclose: true,
                todayHighlight: true,
                todayBtn: "linked",
                orientation: "bottom auto",
                startDate: "01-01-1900",
                endDate: today,
            };

            $('#fnacimiento').datepicker(optSimple);

            // Vista previa de la imagen
            $("#fotografia").change(function() {
                const file = this.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(event) {
                        $("#preview-image").attr("src", event.target.result);
                    };
                    reader.readAsDataURL(file);
                } else {
                    @if ($cliente->fotografia != null)
                        $("#preview-image").attr("src", "{{ asset('assets/imgs/clientes/'.$cliente->fotografia) }}");
                    @else
                        $("#preview-image").attr("src", "{{ asset('assets/imgs/clientes/usericon4.png') }}");
                    @endif
                }
            });

            // Validación del formulario
            $("#clienteForm").submit(function(e) {
                let isValid = true;

                // Validar nombre
                if ($("input[name='nombre']").val().trim() === "") {
                    $("input[name='nombre']").addClass("is-invalid");
                    isValid = false;
                } else {
                    $("input[name='nombre']").removeClass("is-invalid");
                }

                // Validar teléfono
                if ($("input[name='telefono']").val().trim() === "") {
                    $("input[name='telefono']").addClass("is-invalid");
                    isValid = false;
                } else {
                    $("input[name='telefono']").removeClass("is-invalid");
                }

                return isValid;
            });
        });
    </script>
@endsection
