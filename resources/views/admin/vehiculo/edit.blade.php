@extends('layouts.admin')
@section('content')

    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-car-front"></i>
                </div>
                <div class="page-title">
                    <h5>Editar Vehículo</h5>
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
            <!-- Breadcrumb start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('vehiculos') }}">Vehículos</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('show-vehiculo/'.$vehiculo->id) }}">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
            <!-- Breadcrumb end -->

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-light">
                            <h5 class="card-title"><i class="bi bi-pencil-square text-warning"></i> Modificar Vehículo</h5>
                        </div>
                        <div class="card-body">
                            @if (count($errors)>0)
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{$error}}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ url('update-vehiculo/'.$vehiculo->id) }}" method="POST" enctype="multipart/form-data" id="vehiculoForm">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-8">
                                        <!-- Información del Vehículo -->
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="bi bi-car-front-fill me-2"></i>Información del Vehículo</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="marca" class="form-label">Marca <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="bi bi-car-front-fill"></i></span>
                                                            <input name="marca" id="marca" type="text" class="form-control" placeholder="Ej: Toyota, Honda, Ford..." value="{{ old('marca', $vehiculo->marca) }}" required />
                                                        </div>
                                                        @if ($errors->has('marca'))
                                                            <div class="text-danger mt-1">{{ $errors->first('marca') }}</div>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="modelo" class="form-label">Modelo <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="bi bi-car-front"></i></span>
                                                            <input name="modelo" id="modelo" type="text" class="form-control" placeholder="Ej: Corolla, Civic, Ranger..." value="{{ old('modelo', $vehiculo->modelo) }}" required />
                                                        </div>
                                                        @if ($errors->has('modelo'))
                                                            <div class="text-danger mt-1">{{ $errors->first('modelo') }}</div>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label for="ano" class="form-label">Año <span class="text-danger">*</span></label>
                                                        <select name="ano" id="ano" class="form-select" required>
                                                            <option value="">Seleccione el año...</option>
                                                            @for ($year = date('Y') + 1; $year >= 1980; $year--)
                                                                <option value="{{ $year }}" {{ old('ano', $vehiculo->ano) == $year ? 'selected' : '' }}>{{ $year }}</option>
                                                            @endfor
                                                        </select>
                                                        @if ($errors->has('ano'))
                                                            <div class="text-danger mt-1">{{ $errors->first('ano') }}</div>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label for="color" class="form-label">Color <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="bi bi-palette-fill"></i></span>
                                                            <input name="color" id="color" type="text" class="form-control" placeholder="Ej: Rojo, Negro, Plata..." value="{{ old('color', $vehiculo->color) }}" required />
                                                        </div>
                                                        @if ($errors->has('color'))
                                                            <div class="text-danger mt-1">{{ $errors->first('color') }}</div>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label for="tipo_vehiculo" class="form-label">Tipo de Vehículo</label>
                                                        <select name="tipo_vehiculo" id="tipo_vehiculo" class="form-select">
                                                            <option value="">Seleccione...</option>
                                                            <option value="Sedan" {{ old('tipo_vehiculo', $vehiculo->tipo_vehiculo ?? '') == 'Sedan' ? 'selected' : '' }}>Sedán</option>
                                                            <option value="SUV" {{ old('tipo_vehiculo', $vehiculo->tipo_vehiculo ?? '') == 'SUV' ? 'selected' : '' }}>SUV</option>
                                                            <option value="Pickup" {{ old('tipo_vehiculo', $vehiculo->tipo_vehiculo ?? '') == 'Pickup' ? 'selected' : '' }}>Pickup</option>
                                                            <option value="Hatchback" {{ old('tipo_vehiculo', $vehiculo->tipo_vehiculo ?? '') == 'Hatchback' ? 'selected' : '' }}>Hatchback</option>
                                                            <option value="Minivan" {{ old('tipo_vehiculo', $vehiculo->tipo_vehiculo ?? '') == 'Minivan' ? 'selected' : '' }}>Minivan</option>
                                                            <option value="Deportivo" {{ old('tipo_vehiculo', $vehiculo->tipo_vehiculo ?? '') == 'Deportivo' ? 'selected' : '' }}>Deportivo</option>
                                                            <option value="Otro" {{ old('tipo_vehiculo', $vehiculo->tipo_vehiculo ?? '') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="placa" class="form-label">Placa <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                                            <input name="placa" id="placa" type="text" class="form-control" placeholder="Número de placa..." value="{{ old('placa', $vehiculo->placa) }}" required />
                                                        </div>
                                                        @if ($errors->has('placa'))
                                                            <div class="text-danger mt-1">{{ $errors->first('placa') }}</div>
                                                        @endif
                                                        <div class="form-text">Formato: P123ABC (sin guiones ni espacios)</div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="vin" class="form-label">VIN/Chasis <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="bi bi-upc-scan"></i></span>
                                                            <input name="vin" id="vin" type="text" class="form-control" placeholder="Número VIN o chasis..." value="{{ old('vin', $vehiculo->vin) }}" required data-bs-toggle="tooltip" data-bs-placement="top" title="Número de Identificación del Vehículo - Normalmente 17 caracteres" />
                                                        </div>
                                                        @if ($errors->has('vin'))
                                                            <div class="text-danger mt-1">{{ $errors->first('vin') }}</div>
                                                        @endif
                                                        <div class="form-text">El VIN es un código único de 17 caracteres</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Cliente asociado -->
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="bi bi-person-fill me-2"></i>Cliente Asociado</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-12">
                                                        <label for="fcliente" class="form-label">Seleccione Cliente <span class="text-danger">*</span></label>
                                                        <select name="cliente_id" id="fcliente" class="form-select select2-clientes" required>
                                                            <option value="">Seleccione un cliente...</option>
                                                            @foreach($clientes as $cliente)
                                                                <option value="{{ $cliente->id }}" {{ old('cliente_id', $vehiculo->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                                                    {{ $cliente->nombre }} - Tel: {{ $cliente->telefono }} - DPI: {{ $cliente->dpi }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('cliente_id'))
                                                            <div class="text-danger mt-1">{{ $errors->first('cliente_id') }}</div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="d-flex">
                                                            <a href="{{ url('add-cliente') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                                <i class="bi bi-person-plus-fill"></i> Registrar Nuevo Cliente
                                                            </a>
                                                            <button type="button" id="refreshClientesBtn" class="btn btn-sm btn-outline-secondary ms-2">
                                                                <i class="bi bi-arrow-clockwise"></i> Actualizar Lista
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <!-- Fotografía del vehículo -->
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="bi bi-image me-2"></i>Fotografía del Vehículo</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="text-center mb-3">
                                                    <img id="preview-image" src="{{ $vehiculo->fotografia ? asset('assets/imgs/vehiculos/'.$vehiculo->fotografia) : asset('assets/imgs/vehiculos/vehiculoicon.png') }}"
                                                        class="rounded img-fluid mb-3" alt="Vista previa de imagen" style="max-height: 200px; object-fit: contain;">
                                                </div>
                                                <div class="input-group">
                                                    <input type="file" name="fotografia" id="fotografia" class="form-control" accept="image/*">
                                                    <button type="button" class="btn btn-outline-secondary" id="clear-image">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </div>
                                                @if ($errors->has('fotografia'))
                                                    <div class="text-danger mt-1">{{ $errors->first('fotografia') }}</div>
                                                @endif
                                                <div class="form-text mt-2">
                                                    <ul class="ps-3 mb-0">
                                                        <li>Formatos permitidos: JPG, PNG, JPEG</li>
                                                        <li>Tamaño máximo: 2MB</li>
                                                        <li>Dimensión recomendada: 800x600 px</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Historial de cambios -->
                                        <div class="card mt-4">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Información del Registro</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                                        <span>Creado:</span>
                                                        <span class="badge bg-light text-dark">{{ $vehiculo->created_at->format('d/m/Y H:i') }}</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                                        <span>Última modificación:</span>
                                                        <span class="badge bg-light text-dark">{{ $vehiculo->updated_at->format('d/m/Y H:i') }}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="d-flex gap-2 justify-content-center mt-4">
                                    <a href="{{ url('show-vehiculo/'.$vehiculo->id) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-check2-square me-1"></i> Actualizar Vehículo
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

    <script>
        $(document).ready(function() {
            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Inicializar Select2 para el selector de clientes
            $('.select2-clientes').select2({
                placeholder: 'Buscar cliente por nombre, teléfono o DPI...',
                allowClear: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                },
                dropdownParent: $('.select2-clientes').parent()
            });

            // Vista previa de la imagen
            $('#fotografia').change(function(){
                if (this.files && this.files[0]) {
                    let reader = new FileReader();
                    reader.onload = (e) => {
                        $('#preview-image').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Botón para borrar la selección de imagen
            $('#clear-image').click(function(){
                $('#fotografia').val('');
                $('#preview-image').attr('src', '{{ $vehiculo->fotografia ? asset("assets/imgs/vehiculos/".$vehiculo->fotografia) : asset("assets/imgs/vehiculos/vehiculoicon.png") }}');
            });

            // Botón para actualizar lista de clientes
            $('#refreshClientesBtn').click(function() {
                var btn = $(this);
                btn.html('<i class="bi bi-arrow-repeat"></i> Actualizando...').prop('disabled', true);

                // Guardar el valor seleccionado actualmente
                var selectedClienteId = $('#fcliente').val();

                // Hacer una petición AJAX para obtener la lista actualizada de clientes
                $.ajax({
                    url: '{{ url("get-clientes") }}',
                    method: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Limpiar el select
                        $('#fcliente').empty().append('<option value="">Seleccione un cliente...</option>');

                        // Añadir los clientes actualizados
                        $.each(data, function(index, cliente) {
                            var selected = (cliente.id == selectedClienteId) ? ' selected' : '';
                            $('#fcliente').append('<option value="' + cliente.id + '"' + selected + '>' +
                                                cliente.nombre + ' - Tel: ' + cliente.telefono + ' - DPI: ' + cliente.dpi + '</option>');
                        });

                        // Reiniciar Select2
                        $('#fcliente').trigger('change');

                        // Notificar al usuario
                        swal("Lista de clientes actualizada correctamente");
                    },
                    error: function() {
                        swal("Error", "No se pudo actualizar la lista de clientes", "error");
                    },
                    complete: function() {
                        btn.html('<i class="bi bi-arrow-clockwise"></i> Actualizar Lista').prop('disabled', false);
                    }
                });
            });

            // Validación en tiempo real
            $('#placa').on('input', function() {
                var placa = $(this).val().toUpperCase();
                $(this).val(placa);
            });

            $('#vin').on('input', function() {
                var vin = $(this).val().toUpperCase();
                $(this).val(vin);

                if (vin.length > 0 && vin.length < 17) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            // Validación del formulario
            $('#vehiculoForm').submit(function(e) {
                let valid = true;
                const required = ['marca', 'modelo', 'ano', 'color', 'placa', 'vin', 'fcliente'];

                required.forEach(function(field) {
                    if (!$('#' + field).val()) {
                        $('#' + field).addClass('is-invalid');
                        valid = false;
                    } else {
                        $('#' + field).removeClass('is-invalid');
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: $('.is-invalid:first').offset().top - 100
                    }, 500);
                    swal("Error", "Por favor complete todos los campos obligatorios", "error");
                }
            });
        });
    </script>
@endsection
