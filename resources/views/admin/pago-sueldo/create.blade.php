@extends('layouts.admin')
@section('content')

    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-wallet-fill"></i>
                </div>
                <div class="page-title">
                    <h5>Crear Lote de Sueldos</h5>
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
                                    <i class="bi bi-wallet2 text-white display-4"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6>Nuevo Lote de Sueldos</h6>
                                <small class="text-muted">Próximo número de lote: <strong class="text-primary">{{ $proximoNumero }}</strong></small>
                            </div>
                        </div>
                        <!-- Row end -->
                    </div>
                </div>
                <!-- Row end -->

                @if (session('validation_failed') && $errors->any())
                    <div class="row justify-content-center mt-3">
                        <div class="col-lg-12">
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <div>
                                        <strong>¡Formulario incompleto!</strong> 
                                        Se han detectado errores en la información ingresada. 
                                        Los datos que ingresó anteriormente se han mantenido para su corrección.
                                        
                                        @if (!empty($oldEmpleados))
                                            <div class="mt-2">
                                                <small class="text-success">
                                                    <i class="bi bi-arrow-clockwise me-1"></i>
                                                    <strong>Restaurando datos:</strong> Se recuperarán {{ count($oldEmpleados) }} empleados.
                                                </small>
                                            </div>
                                        @endif
                                        
                                        <ul class="mb-0 mt-2">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Row start -->
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-12">
                        <div class="card light">
                            <div class="card-body">
                                <div class="custom-tabs-container">
                                    <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA" role="tab"
                                                aria-controls="oneA" aria-selected="true">Información del Lote</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="tab-twoA" data-bs-toggle="tab" href="#twoA" role="tab"
                                                aria-controls="twoA" aria-selected="false">Empleados y Sueldos</a>
                                        </li>
                                    </ul>
                                    
                                    <form action="{{ route('admin.pago-sueldo.store') }}" method="POST" id="pagoSueldoForm" class="needs-validation" novalidate>
                                        @csrf
                                        
                                    <div class="tab-content">
                                        <!-- Tab 1: Información del Lote -->
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

                                                        <div class="card mb-3">
                                                            <div class="card-header bg-light">
                                                                <h6 class="mb-0"><i class="bi bi-calendar-range"></i> Información del Período</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="periodo_mes" class="form-label">Mes del Período <span class="text-danger">*</span></label>
                                                                        <select class="form-select" id="periodo_mes" name="periodo_mes" required>
                                                                            <option value="">Seleccione el mes</option>
                                                                            @php
                                                                                $meses = [
                                                                                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                                                                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                                                                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                                                                ];
                                                                            @endphp
                                                                            @for($i = 1; $i <= 12; $i++)
                                                                                <option value="{{ $i }}" {{ old('periodo_mes', date('n')) == $i ? 'selected' : '' }}>
                                                                                    {{ $meses[$i] }}
                                                                                </option>
                                                                            @endfor
                                                                        </select>
                                                                        <div class="invalid-feedback">
                                                                            Por favor seleccione el mes.
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="periodo_anio" class="form-label">Año del Período <span class="text-danger">*</span></label>
                                                                        <select class="form-select" id="periodo_anio" name="periodo_anio" required>
                                                                            <option value="">Seleccione el año</option>
                                                                            @for($year = date('Y') - 1; $year <= date('Y') + 1; $year++)
                                                                                <option value="{{ $year }}" {{ old('periodo_anio', date('Y')) == $year ? 'selected' : '' }}>
                                                                                    {{ $year }}
                                                                                </option>
                                                                            @endfor
                                                                        </select>
                                                                        <div class="invalid-feedback">
                                                                            Por favor seleccione el año.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="card mb-3">
                                                            <div class="card-header bg-light">
                                                                <h6 class="mb-0"><i class="bi bi-credit-card"></i> Información de Pago</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="metodo_pago" class="form-label">Método de Pago <span class="text-danger">*</span></label>
                                                                        <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                                                                            <option value="">Seleccione método de pago</option>
                                                                            <option value="transferencia" {{ old('metodo_pago', 'transferencia') == 'transferencia' ? 'selected' : '' }}>
                                                                                <i class="bi bi-bank"></i> Transferencia Bancaria
                                                                            </option>
                                                                            <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>
                                                                                <i class="bi bi-cash"></i> Efectivo
                                                                            </option>
                                                                            <option value="cheque" {{ old('metodo_pago') == 'cheque' ? 'selected' : '' }}>
                                                                                <i class="bi bi-credit-card"></i> Cheque
                                                                            </option>
                                                                        </select>
                                                                        <div class="invalid-feedback">
                                                                            Por favor seleccione el método de pago.
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="fecha_pago" class="form-label">Fecha de Pago <span class="text-danger">*</span></label>
                                                                        <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" 
                                                                               value="{{ old('fecha_pago', date('Y-m-d')) }}" required>
                                                                        <div class="invalid-feedback">
                                                                            Por favor ingrese la fecha de pago.
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <!-- Estado oculto: todos los lotes nuevos empiezan como pendiente -->
                                                                    <input type="hidden" id="estado" name="estado" value="pendiente">
                                                                    
                                                                    <div class="col-md-12 mb-3">
                                                                        <div class="alert alert-info d-flex align-items-center" role="alert">
                                                                            <i class="bi bi-info-circle-fill me-2"></i>
                                                                            <div>
                                                                                <strong>Estado automático:</strong> Todos los lotes nuevos se crean con estado <span class="badge bg-warning">Pendiente</span>. 
                                                                                El estado cambiará automáticamente a <span class="badge bg-success">Pagado</span> 
                                                                                cuando todos los empleados del lote hayan sido marcados como pagados individualmente.
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-12 mb-3">
                                                                        <label for="observaciones" class="form-label">Observaciones</label>
                                                                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                                                                                  placeholder="Observaciones adicionales del lote de sueldos...">{{ old('observaciones') }}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between">
                                                            <a href="{{ route('admin.pago-sueldo.index') }}" class="btn btn-secondary">
                                                                <i class="bi bi-arrow-left"></i> Cancelar
                                                            </a>
                                                            <button type="button" class="btn btn-primary" onclick="nextTab()">
                                                                Siguiente: Empleados <i class="bi bi-arrow-right"></i>
                                                            </button>
                                                        </div>
                                                </div>
                                            </div>
                                            <!-- Row end -->
                                        </div>

                                        <!-- Tab 2: Empleados y Sueldos -->
                                        <div class="tab-pane fade" id="twoA" role="tabpanel">
                                            <div class="row gx-3">
                                                <div class="col-sm-12 col-12">
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                            <h6 class="mb-0"><i class="bi bi-people"></i> Selección de Empleados</h6>
                                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarEmpleado()">
                                                                <i class="bi bi-plus-circle"></i> Agregar Empleado
                                                            </button>
                                                        </div>
                                                        <div class="card-body">
                                                            <!-- Input hidden para asegurar que el array empleados se envíe -->
                                                            <input type="hidden" name="empleados_count" value="0" id="empleados-count">
                                                            
                                                            <div id="empleados-container">
                                                                <!-- Los empleados se agregarán dinámicamente aquí -->
                                                            </div>
                                                            
                                                            <div class="alert alert-info" id="no-empleados">
                                                                <i class="bi bi-info-circle"></i>
                                                                <strong>Información:</strong> Haga clic en "Agregar Empleado" para comenzar a seleccionar los empleados para este lote de sueldos.
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Resumen del lote -->
                                                    <div class="card mb-3" id="resumen-card" style="display: none;">
                                                        <div class="card-header bg-success text-white">
                                                            <h6 class="mb-0"><i class="bi bi-calculator"></i> Resumen del Lote</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row text-center">
                                                                <div class="col-md-4">
                                                                    <h6>Total Empleados</h6>
                                                                    <h4 id="total-empleados" class="text-primary">0</h4>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <h6>Total Sueldo Base</h6>
                                                                    <h4 id="total-sueldo" class="text-info">Q0.00</h4>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <h6>Total General</h6>
                                                                    <h4 id="total-general" class="text-success">Q0.00</h4>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="d-flex justify-content-between">
                                                        <button type="button" class="btn btn-secondary" onclick="prevTab()">
                                                            <i class="bi bi-arrow-left"></i> Anterior
                                                        </button>
                                                        <button type="button" class="btn btn-success" id="submit-btn" style="display: block;" onclick="enviarFormularioReal()">
                                                            <i class="bi bi-check-circle"></i> Crear Lote de Sueldos
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row end -->
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
let empleadoIndex = 0;
const trabajadores = @json($trabajadores);
const usuarios = @json($usuarios);

function enviarFormulario() {
    // Validar formulario antes de enviar
    if (validarFormulario()) {
        document.getElementById('pagoSueldoForm').submit();
    }
}

function enviarFormularioReal() {
    // Validar formulario con datos reales
    if (validarFormularioReal()) {
        document.getElementById('pagoSueldoForm').submit();
    }
}

function validarFormularioReal() {
    const empleados = document.querySelectorAll('.empleado-item');
    const form = document.getElementById('pagoSueldoForm');
    
    if (!form) {
        console.error('ERROR: No se encontró el formulario pagoSueldoForm');
        return false;
    }
    
    // Validar que haya al menos un empleado
    if (empleados.length === 0) {
        alert('Debe agregar al menos un empleado al lote de sueldos.');
        return false;
    }
    
    // Validar campos básicos del formulario
    const periodoMes = document.getElementById('periodo_mes');
    const periodoAnio = document.getElementById('periodo_anio'); 
    const metodoPago = document.getElementById('metodo_pago');
    const fechaPago = document.getElementById('fecha_pago');
    
    if (!periodoMes.value || !periodoAnio.value || !metodoPago.value || !fechaPago.value) {
        alert('Por favor complete todos los campos obligatorios en la información del lote.');
        return false;
    }
    
    // Validar que todos los empleados tengan datos completos
    let valid = true;
    empleados.forEach(function(empleado, index) {
        const tipoSelect = empleado.querySelector('select[name*="[tipo]"]');
        const idSelect = empleado.querySelector('select[name*="[id]"]');
        const sueldoInput = empleado.querySelector('input[name*="[sueldo_base]"]');
        
        if (!tipoSelect || !tipoSelect.value || !idSelect || !idSelect.value || !sueldoInput || !sueldoInput.value || parseFloat(sueldoInput.value) <= 0) {
            alert(`El empleado #${index + 1} tiene datos incompletos o inválidos.`);
            valid = false;
            return false;
        }
    });
    
    return valid;
}

function validarFormulario() {
    const empleados = document.querySelectorAll('.empleado-item');
    const form = document.getElementById('pagoSueldoForm');
    
    if (!form) {
        console.error('ERROR: No se encontró el formulario pagoSueldoForm');
        return false;
    }
    
    // BYPASS TEMPORAL - deshabilitar validación de empleados vacía
    /*
    if (empleados.length === 0) {
        alert('Debe agregar al menos un empleado al lote de sueldos.');
        return false;
    }
    */
    
    // Validar que todos los empleados tengan datos completos
    let valid = true;
    empleados.forEach(function(empleado, index) {
        const tipoSelect = empleado.querySelector('select[name*="[tipo]"]');
        const idSelect = empleado.querySelector('select[name*="[id]"]');
        const sueldoInput = empleado.querySelector('input[name*="[sueldo_base]"]');
        
        if (!tipoSelect || !tipoSelect.value || !idSelect || !idSelect.value || !sueldoInput || !sueldoInput.value || parseFloat(sueldoInput.value) <= 0) {
            alert(`El empleado #${index + 1} tiene datos incompletos o inválidos.`);
            valid = false;
            return false;
        }
    });
    
    return valid;
}

function nextTab() {
    // Validar primera pestaña
    const form = document.getElementById('pagoSueldoForm');
    const firstTabInputs = form.querySelectorAll('#oneA input[required], #oneA select[required]');
    let valid = true;
    
    firstTabInputs.forEach(input => {
        if (!input.value) {
            input.classList.add('is-invalid');
            valid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    if (valid) {
        document.getElementById('tab-twoA').click();
    }
}

function prevTab() {
    document.getElementById('tab-oneA').click();
}

function agregarEmpleado() {
    const container = document.getElementById('empleados-container');
    const noEmpleados = document.getElementById('no-empleados');
    
    const empleadoHtml = `
        <div class="empleado-item border rounded p-3 mb-3" data-index="${empleadoIndex}">
            <div class="row">
                <div class="col-md-12 d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Empleado #${empleadoIndex + 1}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerEmpleado(${empleadoIndex})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tipo de Empleado <span class="text-danger">*</span></label>
                    <select class="form-select" name="empleados[${empleadoIndex}][tipo]" onchange="cargarEmpleados(${empleadoIndex})" required>
                        <option value="">Seleccione tipo</option>
                        <option value="trabajador">Trabajador</option>
                        <option value="vendedor">Usuario/Vendedor</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Empleado <span class="text-danger">*</span></label>
                    <select class="form-select" name="empleados[${empleadoIndex}][id]" required disabled>
                        <option value="">Primero seleccione el tipo</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Sueldo Base <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="empleados[${empleadoIndex}][sueldo_base]" 
                           step="0.01" min="0" placeholder="0.00" onchange="calcularTotal(${empleadoIndex})" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Horas Extra</label>
                    <input type="number" class="form-control" name="empleados[${empleadoIndex}][horas_extra]" 
                           step="0.01" min="0" placeholder="0" onchange="calcularTotal(${empleadoIndex})">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Valor Hora Extra</label>
                    <input type="number" class="form-control" name="empleados[${empleadoIndex}][valor_hora_extra]" 
                           step="0.01" min="0" placeholder="0.00" onchange="calcularTotal(${empleadoIndex})">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Comisiones</label>
                    <input type="number" class="form-control" name="empleados[${empleadoIndex}][comisiones]" 
                           step="0.01" min="0" placeholder="0.00" onchange="calcularTotal(${empleadoIndex})">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Bonificaciones</label>
                    <input type="number" class="form-control" name="empleados[${empleadoIndex}][bonificaciones]" 
                           step="0.01" min="0" placeholder="0.00" onchange="calcularTotal(${empleadoIndex})">
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Descuentos</label>
                    <input type="number" class="form-control" name="empleados[${empleadoIndex}][descuentos]" 
                           step="0.01" min="0" placeholder="0.00" onchange="calcularTotal(${empleadoIndex})">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Observaciones</label>
                    <input type="text" class="form-control" name="empleados[${empleadoIndex}][observaciones]" 
                           placeholder="Observaciones específicas del empleado">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Total a Pagar</label>
                    <input type="text" class="form-control total-empleado bg-light" 
                           id="total-${empleadoIndex}" readonly value="Q0.00">
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', empleadoHtml);
    noEmpleados.style.display = 'none';
    empleadoIndex++;
    
    // Actualizar contador
    document.getElementById('empleados-count').value = document.querySelectorAll('.empleado-item').length;
    
    actualizarResumen();
}

function removerEmpleado(index) {
    const empleado = document.querySelector(`[data-index="${index}"]`);
    empleado.remove();
    
    const container = document.getElementById('empleados-container');
    if (container.children.length === 0) {
        document.getElementById('no-empleados').style.display = 'block';
    }
    
    // Actualizar contador
    document.getElementById('empleados-count').value = document.querySelectorAll('.empleado-item').length;
    
    actualizarResumen();
}

function cargarEmpleados(index) {
    const tipoSelect = document.querySelector(`select[name="empleados[${index}][tipo]"]`);
    const empleadoSelect = document.querySelector(`select[name="empleados[${index}][id]"]`);
    
    empleadoSelect.innerHTML = '<option value="">Seleccione empleado</option>';
    empleadoSelect.disabled = false;
    
    if (tipoSelect.value === 'trabajador') {
        trabajadores.forEach(trabajador => {
            empleadoSelect.innerHTML += `<option value="${trabajador.id}">${trabajador.nombre} ${trabajador.apellido}</option>`;
        });
    } else if (tipoSelect.value === 'vendedor') {
        usuarios.forEach(usuario => {
            empleadoSelect.innerHTML += `<option value="${usuario.id}">${usuario.name}</option>`;
        });
    }
}

function calcularTotal(index) {
    const sueldoBase = parseFloat(document.querySelector(`input[name="empleados[${index}][sueldo_base]"]`).value) || 0;
    const horasExtra = parseFloat(document.querySelector(`input[name="empleados[${index}][horas_extra]"]`).value) || 0;
    const valorHoraExtra = parseFloat(document.querySelector(`input[name="empleados[${index}][valor_hora_extra]"]`).value) || 0;
    const comisiones = parseFloat(document.querySelector(`input[name="empleados[${index}][comisiones]"]`).value) || 0;
    const bonificaciones = parseFloat(document.querySelector(`input[name="empleados[${index}][bonificaciones]"]`).value) || 0;
    const descuentos = parseFloat(document.querySelector(`input[name="empleados[${index}][descuentos]"]`).value) || 0;
    
    const totalHorasExtra = horasExtra * valorHoraExtra;
    const total = sueldoBase + totalHorasExtra + comisiones + bonificaciones - descuentos;
    
    document.getElementById(`total-${index}`).value = `Q${total.toFixed(2)}`;
    
    actualizarResumen();
}

function formatearMoneda(numero) {
    return new Intl.NumberFormat('es-GT', {
        style: 'currency',
        currency: 'GTQ',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(numero).replace('GTQ', 'Q');
}

function actualizarResumen() {
    const empleados = document.querySelectorAll('.empleado-item');
    const resumenCard = document.getElementById('resumen-card');
    const submitBtn = document.getElementById('submit-btn');
    
    if (empleados.length === 0) {
        resumenCard.style.display = 'none';
        submitBtn.style.display = 'none';
        return;
    }
    
    let totalEmpleados = empleados.length;
    let totalSueldo = 0;
    let totalGeneral = 0;
    
    empleados.forEach((empleado, index) => {
        const sueldoBase = parseFloat(empleado.querySelector(`input[name*="[sueldo_base]"]`).value) || 0;
        const totalInput = empleado.querySelector('.total-empleado');
        const totalValue = parseFloat(totalInput.value.replace('Q', '')) || 0;
        
        totalSueldo += sueldoBase;
        totalGeneral += totalValue;
    });
    
    document.getElementById('total-empleados').textContent = totalEmpleados;
    document.getElementById('total-sueldo').textContent = formatearMoneda(totalSueldo);
    document.getElementById('total-general').textContent = formatearMoneda(totalGeneral);
    
    resumenCard.style.display = 'block';
    submitBtn.style.display = 'inline-block';
}

// Reconstituir empleados si hay datos old (errores de validación)
document.addEventListener('DOMContentLoaded', function() {
    const oldEmpleados = @json($oldEmpleados);
    
    console.log('OldEmpleados recibidos:', oldEmpleados);
    console.log('Tipo de oldEmpleados:', typeof oldEmpleados);
    console.log('Es array?', Array.isArray(oldEmpleados));
    console.log('Keys de objeto:', oldEmpleados ? Object.keys(oldEmpleados) : 'N/A');
    
    if (oldEmpleados && Array.isArray(oldEmpleados) && oldEmpleados.length > 0) {
        console.log('Reconstituyendo como array con', oldEmpleados.length, 'empleados');
        
        // Usar setTimeout para asegurar que el DOM esté completamente cargado
        setTimeout(function() {
            oldEmpleados.forEach(function(empleadoData, index) {
                console.log('Procesando empleado', index, ':', empleadoData);
                
                // Agregar empleado
                agregarEmpleado();
                
                // Usar el índice actual para llenar datos
                const currentIndex = empleadoIndex - 1;
                console.log('Índice actual para empleado:', currentIndex);
                
                // Establecer tipo de empleado inmediatamente
                const tipoSelect = document.querySelector(`select[name="empleados[${currentIndex}][tipo]"]`);
                console.log('Tipo select encontrado:', tipoSelect);
                
                if (tipoSelect && empleadoData.tipo) {
                    tipoSelect.value = empleadoData.tipo;
                    console.log('Tipo establecido a:', empleadoData.tipo);
                    
                    // Llamar función existente para cargar empleados
                    cargarEmpleados(currentIndex);
                    
                    // Esperar a que se carguen las opciones y luego llenar datos
                    setTimeout(function() {
                        // Seleccionar empleado específico
                        const empleadoSelect = document.querySelector(`select[name="empleados[${currentIndex}][id]"]`);
                        console.log('Empleado select encontrado:', empleadoSelect);
                        
                        if (empleadoSelect && empleadoData.id) {
                            empleadoSelect.value = empleadoData.id;
                            console.log('Empleado ID establecido:', empleadoData.id);
                        }
                        
                        // Llenar campos numéricos y de texto
                        const campos = ['sueldo_base', 'horas_extra', 'valor_hora_extra', 'comisiones', 'bonificaciones', 'descuentos'];
                        campos.forEach(function(campo) {
                            const input = document.querySelector(`input[name="empleados[${currentIndex}][${campo}]"]`);
                            if (input && empleadoData[campo]) {
                                input.value = empleadoData[campo];
                                console.log('Campo', campo, 'establecido:', empleadoData[campo]);
                            }
                        });
                        
                        // Llenar campo de observaciones
                        const observacionesTextarea = document.querySelector(`textarea[name="empleados[${currentIndex}][observaciones]"]`);
                        if (observacionesTextarea && empleadoData.observaciones) {
                            observacionesTextarea.value = empleadoData.observaciones;
                            console.log('Observaciones establecidas');
                        }
                        
                        // Recalcular totales después de un momento
                        setTimeout(function() {
                            calcularTotal(currentIndex);
                            console.log('Total calculado para empleado', currentIndex);
                        }, 100);
                        
                    }, 300 * (index + 1)); // Incrementar tiempo para cada empleado
                }
            });
            
            // Actualizar resumen final
            setTimeout(function() {
                actualizarResumen();
                console.log('Resumen actualizado');
                
                // Mostrar mensaje de éxito
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show mt-3';
                alertDiv.innerHTML = `
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>¡Datos restaurados!</strong> Se han recuperado ${oldEmpleados.length} empleados que había agregado anteriormente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                
                const container = document.querySelector('.subscriber-body .row').firstElementChild;
                if (container) {
                    container.insertBefore(alertDiv, container.firstChild);
                }
                
            }, 1000 + (oldEmpleados.length * 300));
            
        }, 500); // Dar tiempo para que se cargue todo el DOM
        
    } else if (oldEmpleados && typeof oldEmpleados === 'object' && Object.keys(oldEmpleados).length > 0) {
        console.log('Reconstituyendo como objeto con keys:', Object.keys(oldEmpleados));
        
        // Manejar caso donde oldEmpleados es un objeto con keys numéricas
        setTimeout(function() {
            Object.keys(oldEmpleados).forEach(function(key, index) {
                const empleadoData = oldEmpleados[key];
                console.log('Procesando empleado objeto', key, ':', empleadoData);
                
                // Usar la misma lógica de arriba
                agregarEmpleado();
                const currentIndex = empleadoIndex - 1;
                
                const tipoSelect = document.querySelector(`select[name="empleados[${currentIndex}][tipo]"]`);
                if (tipoSelect && empleadoData.tipo) {
                    tipoSelect.value = empleadoData.tipo;
                    cargarEmpleados(currentIndex);
                    
                    setTimeout(function() {
                        const empleadoSelect = document.querySelector(`select[name="empleados[${currentIndex}][id]"]`);
                        if (empleadoSelect && empleadoData.id) {
                            empleadoSelect.value = empleadoData.id;
                        }
                        
                        const campos = ['sueldo_base', 'horas_extra', 'valor_hora_extra', 'comisiones', 'bonificaciones', 'descuentos'];
                        campos.forEach(function(campo) {
                            const input = document.querySelector(`input[name="empleados[${currentIndex}][${campo}]"]`);
                            if (input && empleadoData[campo]) {
                                input.value = empleadoData[campo];
                            }
                        });
                        
                        const observacionesTextarea = document.querySelector(`textarea[name="empleados[${currentIndex}][observaciones]"]`);
                        if (observacionesTextarea && empleadoData.observaciones) {
                            observacionesTextarea.value = empleadoData.observaciones;
                        }
                        
                        setTimeout(function() {
                            calcularTotal(currentIndex);
                        }, 100);
                        
                    }, 300 * (index + 1));
                }
            });
            
            setTimeout(function() {
                actualizarResumen();
                console.log('Resumen objeto actualizado');
            }, 1000 + (Object.keys(oldEmpleados).length * 300));
            
        }, 500);
    } else {
        console.log('No hay datos para reconstituir');
    }
});
</script>
@endsection
