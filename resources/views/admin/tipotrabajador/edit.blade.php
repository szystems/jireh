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
                    <h5>Editar Tipo de Trabajador</h5>
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
                            <h5 class="card-title">Editar "{{ $tipoTrabajador->nombre }}"</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('update-tipo-trabajador/'.$tipoTrabajador->id) }}" method="POST" class="needs-validation" novalidate>
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $tipoTrabajador->nombre) }}" required>
                                        @error('nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                                        <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                                            <option value="activo" {{ old('estado', $tipoTrabajador->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                                            <option value="inactivo" {{ old('estado', $tipoTrabajador->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                        </select>
                                        @error('estado')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $tipoTrabajador->descripcion) }}</textarea>
                                        @error('descripcion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="aplica_comision" name="aplica_comision" value="1" {{ old('aplica_comision', $tipoTrabajador->aplica_comision) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="aplica_comision">Aplica para comisiones</label>
                                        </div>
                                        <small class="text-muted">Los trabajadores de este tipo podrán recibir comisiones</small>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="requiere_asignacion" name="requiere_asignacion" value="1" {{ old('requiere_asignacion', $tipoTrabajador->requiere_asignacion) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="requiere_asignacion">Requiere asignación</label>
                                        </div>
                                        <small class="text-muted">Los trabajadores de este tipo requieren ser asignados a servicios específicos</small>
                                    </div>

                                    <!-- Sección de configuración de comisiones -->
                                    <div id="seccion-comisiones" class="col-12 mt-3" style="display: {{ old('aplica_comision', $tipoTrabajador->aplica_comision) ? 'block' : 'none' }}">
                                        <div class="card border-secondary">
                                            <div class="card-header bg-secondary text-white">
                                                <h5 class="card-title mb-0">Configuración de Comisiones</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="tipo_comision" class="form-label">Tipo de Comisión</label>
                                                        <select class="form-select @error('tipo_comision') is-invalid @enderror" id="tipo_comision" name="tipo_comision">
                                                            <option value="">Seleccione un tipo</option>
                                                            @foreach($tiposComision as $value => $label)
                                                                <option value="{{ $value }}" {{ old('tipo_comision', $tipoTrabajador->tipo_comision) == $value ? 'selected' : '' }}>
                                                                    {{ $label }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('tipo_comision')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-check form-switch mt-4">
                                                            <input class="form-check-input" type="checkbox" id="permite_multiples_trabajadores" name="permite_multiples_trabajadores" value="1" {{ old('permite_multiples_trabajadores', $tipoTrabajador->permite_multiples_trabajadores) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="permite_multiples_trabajadores">
                                                                <span class="fw-bold">Permite múltiples trabajadores</span>
                                                            </label>
                                                        </div>
                                                        <small class="text-muted">Para servicios donde pueden participar varios trabajadores (ej. Car Wash)</small>
                                                    </div>
                                                </div>

                                                <div class="row comision-fijo-porcentaje" id="config-comision" style="display: {{ old('tipo_comision', $tipoTrabajador->tipo_comision) ? 'flex' : 'none' }};">
                                                    <div class="col-md-6 mb-3" id="valor-comision-container" style="display: {{ in_array(old('tipo_comision', $tipoTrabajador->tipo_comision), ['fijo', 'por_servicio', 'personalizado']) ? 'block' : 'none' }};">
                                                        <label for="valor_comision" class="form-label">Monto por Comisión (Q)</label>
                                                        <input type="number" step="0.01" min="0" class="form-control @error('valor_comision') is-invalid @enderror" id="valor_comision" name="valor_comision" value="{{ old('valor_comision', $tipoTrabajador->valor_comision) }}">
                                                        @error('valor_comision')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-6 mb-3" id="porcentaje-comision-container" style="display: {{ in_array(old('tipo_comision', $tipoTrabajador->tipo_comision), ['porcentaje_venta', 'porcentaje_ganancia', 'personalizado']) ? 'block' : 'none' }};">
                                                        <label for="porcentaje_comision" class="form-label">Porcentaje de Comisión (%)</label>
                                                        <input type="number" step="0.01" min="0" max="100" class="form-control @error('porcentaje_comision') is-invalid @enderror" id="porcentaje_comision" name="porcentaje_comision" value="{{ old('porcentaje_comision', $tipoTrabajador->porcentaje_comision) }}">
                                                        @error('porcentaje_comision')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 justify-content-end mt-4">
                                    <a href="{{ url('tipo-trabajador') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle me-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-circle me-1"></i> Actualizar
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener los elementos del DOM
        const aplicaComision = document.getElementById('aplica_comision');
        const seccionComisiones = document.getElementById('seccion-comisiones');
        const tipoComision = document.getElementById('tipo_comision');
        const configComision = document.getElementById('config-comision');
        const valorComisionContainer = document.getElementById('valor-comision-container');
        const porcentajeComisionContainer = document.getElementById('porcentaje-comision-container');

        // Verificación de elementos (para depuración)
        console.log('Elementos encontrados:', {
            aplicaComision: aplicaComision !== null,
            seccionComisiones: seccionComisiones !== null,
            tipoComision: tipoComision !== null,
            configComision: configComision !== null,
            valorComisionContainer: valorComisionContainer !== null,
            porcentajeComisionContainer: porcentajeComisionContainer !== null
        });

        // Función para mostrar/ocultar sección de comisiones
        function toggleComisiones() {
            console.log('Toggle comisiones, checked:', aplicaComision.checked);
            if(aplicaComision.checked) {
                seccionComisiones.style.display = 'block';
            } else {
                seccionComisiones.style.display = 'none';
            }
        }

        // Función para mostrar/ocultar campos según tipo de comisión
        function toggleCamposComision() {
            const tipo = tipoComision.value;
            console.log('Toggle campos, tipo:', tipo);

            if (tipo) {
                configComision.style.display = 'flex';

                if (tipo === 'fijo' || tipo === 'por_servicio') {
                    valorComisionContainer.style.display = 'block';
                    porcentajeComisionContainer.style.display = 'none';
                } else if (tipo === 'porcentaje_venta' || tipo === 'porcentaje_ganancia') {
                    valorComisionContainer.style.display = 'none';
                    porcentajeComisionContainer.style.display = 'block';
                } else if (tipo === 'personalizado') {
                    valorComisionContainer.style.display = 'block';
                    porcentajeComisionContainer.style.display = 'block';
                }
            } else {
                configComision.style.display = 'none';
            }
        }

        // Agregar los eventos manualmente con un pequeño retraso para asegurar que se apliquen correctamente
        setTimeout(function() {
            // Eventos
            aplicaComision.addEventListener('change', toggleComisiones);
            tipoComision.addEventListener('change', toggleCamposComision);

            // Inicializar estados
            toggleComisiones();
            toggleCamposComision();

            console.log('Eventos inicializados');
        }, 100);
    });
</script>
@endsection
