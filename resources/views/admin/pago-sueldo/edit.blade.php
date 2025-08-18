@extends('layouts.admin')
@section('content')

    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="page-title">
                    <h5>Editar Lote de Sueldos</h5>
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
                                <div class="avatar-circle bg-warning">
                                    <i class="bi bi-pencil-square text-white display-4"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6>Editar Lote: {{ $pagoSueldo->numero_lote }}</h6>
                                <small class="text-muted">
                                    Estado: 
                                    @switch($pagoSueldo->estado)
                                        @case('pendiente')
                                            <span class="badge bg-warning">Pendiente</span>
                                            @break
                                        @case('pagado')
                                            <span class="badge bg-success">Pagado</span>
                                            @break
                                        @case('cancelado')
                                            <span class="badge bg-danger">Cancelado</span>
                                            @break
                                    @endswitch
                                </small>
                            </div>
                        </div>
                        <!-- Row end -->
                    </div>
                </div>
                <!-- Row end -->

                @if($pagoSueldo->estado !== 'pendiente')
                    <div class="row justify-content-center mt-4">
                        <div class="col-lg-12">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Advertencia:</strong> Solo se pueden editar lotes en estado "Pendiente". 
                                Este lote está en estado "{{ ucfirst($pagoSueldo->estado) }}".
                                <div class="mt-2">
                                    <a href="{{ route('admin.pago-sueldo.show', $pagoSueldo->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Ver Detalle
                                    </a>
                                    <a href="{{ route('admin.pago-sueldo.index') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-arrow-left"></i> Volver al Listado
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
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

                                                        <form action="{{ route('admin.pago-sueldo.update-post', $pagoSueldo->id) }}" method="POST" id="pagoSueldoForm" class="needs-validation" novalidate>
                                                            @csrf

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
                                                                                    <option value="{{ $i }}" {{ old('periodo_mes', $pagoSueldo->periodo_mes) == $i ? 'selected' : '' }}>
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
                                                                                    <option value="{{ $year }}" {{ old('periodo_anio', $pagoSueldo->periodo_anio) == $year ? 'selected' : '' }}>
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
                                                                                <option value="transferencia" {{ old('metodo_pago', $pagoSueldo->metodo_pago) == 'transferencia' ? 'selected' : '' }}>
                                                                                    Transferencia Bancaria
                                                                                </option>
                                                                                <option value="efectivo" {{ old('metodo_pago', $pagoSueldo->metodo_pago) == 'efectivo' ? 'selected' : '' }}>
                                                                                    Efectivo
                                                                                </option>
                                                                                <option value="cheque" {{ old('metodo_pago', $pagoSueldo->metodo_pago) == 'cheque' ? 'selected' : '' }}>
                                                                                    Cheque
                                                                                </option>
                                                                            </select>
                                                                            <div class="invalid-feedback">
                                                                                Por favor seleccione el método de pago.
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label for="fecha_pago" class="form-label">Fecha de Pago <span class="text-danger">*</span></label>
                                                                            <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" 
                                                                                   value="{{ old('fecha_pago', $pagoSueldo->fecha_pago ? $pagoSueldo->fecha_pago->format('Y-m-d') : '') }}" required>
                                                                            <div class="invalid-feedback">
                                                                                Por favor ingrese la fecha de pago.
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- Campo estado oculto - se actualiza automáticamente -->
                                                                    <input type="hidden" name="estado" value="{{ $pagoSueldo->estado }}">
                                                                    
                                                                    <div class="row">
                                                                        <div class="col-12 mb-3">
                                                                            <div class="alert alert-info">
                                                                                <i class="bi bi-info-circle"></i>
                                                                                <strong>Gestión Automática de Estados:</strong>
                                                                                El estado del lote se actualiza automáticamente basado en el estado individual de cada empleado. 
                                                                                No es necesario cambiarlo manualmente.
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-12 mb-3">
                                                                            <label for="observaciones" class="form-label">Observaciones</label>
                                                                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3" 
                                                                                      placeholder="Observaciones adicionales del lote de sueldos...">{{ old('observaciones', $pagoSueldo->observaciones) }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="d-flex justify-content-between">
                                                                <a href="{{ route('admin.pago-sueldo.show', $pagoSueldo->id) }}" class="btn btn-secondary">
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
                                                                <h6 class="mb-0"><i class="bi bi-people"></i> Empleados del Lote</h6>
                                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarEmpleado()">
                                                                    <i class="bi bi-plus-circle"></i> Agregar Empleado
                                                                </button>
                                                            </div>
                                                            <div class="card-body">
                                                                <div id="empleados-container">
                                                                    <!-- Los empleados existentes se cargarán aquí -->
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Resumen del lote -->
                                                        <div class="card mb-3" id="resumen-card">
                                                            <div class="card-header bg-success text-white">
                                                                <h6 class="mb-0"><i class="bi bi-calculator"></i> Resumen del Lote</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row text-center">
                                                                    <div class="col-md-4">
                                                                        <h6>Total Empleados</h6>
                                                                        <h4 id="total-empleados" class="text-primary">{{ $pagoSueldo->detalles->count() }}</h4>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <h6>Total Sueldo Base</h6>
                                                                        <h4 id="total-sueldo" class="text-info">Q{{ number_format($pagoSueldo->detalles->sum('sueldo_base'), 2) }}</h4>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <h6>Total General</h6>
                                                                        <h4 id="total-general" class="text-success">Q{{ number_format($pagoSueldo->total_monto, 2) }}</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between">
                                                            <button type="button" class="btn btn-secondary" onclick="prevTab()">
                                                                <i class="bi bi-arrow-left"></i> Anterior
                                                            </button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="bi bi-check-circle"></i> Actualizar Lote de Sueldos
                                                            </button>
                                                        </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Row end -->
                @endif
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
let empleadoIndex = 0;
const trabajadores = @json($trabajadores);
const usuarios = @json($usuarios);
const empleadosExistentes = @json($empleadosExistentes);

console.log('=== EDIT PAGE LOADED ===');
console.log('Trabajadores:', trabajadores.length);
console.log('Usuarios:', usuarios.length);
console.log('Empleados existentes:', empleadosExistentes);

// Agregar interceptor del formulario para debugging - SERIALIZAR MANUALMENTE
document.getElementById('pagoSueldoForm').addEventListener('submit', function(e) {
    console.log('=== FORM SUBMIT INTERCEPTED ===');
    
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Crear objeto con empleados serializados manualmente
    const empleados = [];
    const empleadosItems = document.querySelectorAll('.empleado-item');
    
    empleadosItems.forEach((item, index) => {
        const tipo = item.querySelector(`select[name*="[tipo]"]`).value;
        const id = item.querySelector(`select[name*="[id]"]`).value;
        const sueldoBase = item.querySelector(`input[name*="[sueldo_base]"]`).value;
        const horasExtra = item.querySelector(`input[name*="[horas_extra]"]`).value;
        const valorHoraExtra = item.querySelector(`input[name*="[valor_hora_extra]"]`).value;
        const comisiones = item.querySelector(`input[name*="[comisiones]"]`).value;
        const bonificaciones = item.querySelector(`input[name*="[bonificaciones]"]`).value;
        const descuentos = item.querySelector(`input[name*="[descuentos]"]`).value;
        const observaciones = item.querySelector(`input[name*="[observaciones]"]`).value;
        
        // Buscar el detalle_id si existe (empleado existente)
        const detalleIdInput = item.querySelector(`input[name*="[detalle_id]"]`);
        const detalleId = detalleIdInput ? detalleIdInput.value : null;
        
        const empleado = {
            tipo: tipo,
            id: id,
            sueldo_base: sueldoBase,
            horas_extra: horasExtra || '0',
            valor_hora_extra: valorHoraExtra || '0',
            comisiones: comisiones || '0',
            bonificaciones: bonificaciones || '0',
            descuentos: descuentos || '0',
            observaciones: observaciones || ''
        };
        
        // Si tiene detalle_id, es un empleado existente
        if (detalleId) {
            empleado.detalle_id = detalleId;
        }
        
        empleados.push(empleado);
    });
    
    console.log('Empleados serializados manualmente:', empleados);
    
    // Agregar empleados como JSON al FormData
    formData.append('empleados_json', JSON.stringify(empleados));
    
    // Enviar con fetch
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        return response.json().then(data => {
            if (response.ok) {
                return data;
            } else {
                throw new Error(data.message || 'Error en el envío');
            }
        });
    })
    .then(data => {
        if (data.success) {
            // Redirigir a la URL que el servidor especifica
            window.location.href = data.redirect;
        } else {
            throw new Error(data.message || 'Error desconocido');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar el lote de sueldos: ' + error.message);
    });
    
    return false;
});

const detallesExistentes = @json($pagoSueldo->detalles);

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

function agregarEmpleado(esExistente = false, datosEmpleado = null) {
    const container = document.getElementById('empleados-container');
    
    // Determinar si el empleado está pagado (solo lectura)
    const esPagado = esExistente && datosEmpleado && datosEmpleado.estado === 'pagado';
    const readonly = esPagado ? 'readonly' : '';
    const disabled = esPagado ? 'disabled' : '';
    const bgClass = esPagado ? 'bg-light' : '';
    
    const empleadoHtml = `
        <div class="empleado-item border rounded p-3 mb-3 ${esPagado ? 'border-success' : ''}" data-index="${empleadoIndex}" ${esPagado ? 'data-estado="pagado"' : ''}>
            ${esPagado ? `
            <div class="alert alert-success alert-sm mb-2">
                <i class="bi bi-check-circle-fill"></i> 
                <strong>EMPLEADO PAGADO</strong> - Los datos no pueden modificarse para mantener la integridad del pago
                ${datosEmpleado.fecha_pago ? `<br><small>Fecha de pago: ${new Date(datosEmpleado.fecha_pago).toLocaleDateString()}</small>` : ''}
            </div>
            ` : ''}
            <div class="row">
                <div class="col-md-12 d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">
                        Empleado #${empleadoIndex + 1}
                        ${esPagado ? '<span class="badge bg-success ms-2">PAGADO</span>' : ''}
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removerEmpleado(${empleadoIndex}, ${esPagado})">
                        <i class="bi bi-trash"></i>
                        ${esPagado ? ' Eliminar (Pagado)' : ''}
                    </button>
                </div>
            </div>
            ${esExistente && datosEmpleado ? `<input type="hidden" name="empleados[${empleadoIndex}][detalle_id]" value="${datosEmpleado.detalle_id}">` : ''}
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tipo de Empleado <span class="text-danger">*</span></label>
                    <select class="form-select ${bgClass}" name="empleados[${empleadoIndex}][tipo]" onchange="cargarEmpleados(${empleadoIndex})" required ${disabled}>
                        <option value="">Seleccione tipo</option>
                        <option value="trabajador">Trabajador</option>
                        <option value="vendedor">Usuario/Vendedor</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Empleado <span class="text-danger">*</span></label>
                    <select class="form-select ${bgClass}" name="empleados[${empleadoIndex}][id]" required ${disabled}>
                        <option value="">Primero seleccione el tipo</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Sueldo Base <span class="text-danger">*</span></label>
                    <input type="number" class="form-control ${bgClass}" name="empleados[${empleadoIndex}][sueldo_base]" 
                           step="0.01" min="0" placeholder="0.00" onchange="calcularTotal(${empleadoIndex})" required ${readonly}>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Horas Extra</label>
                    <input type="number" class="form-control ${bgClass}" name="empleados[${empleadoIndex}][horas_extra]" 
                           step="0.01" min="0" placeholder="0.00" onchange="calcularTotal(${empleadoIndex})" ${readonly}>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Valor por Hora</label>
                    <input type="number" class="form-control ${bgClass}" name="empleados[${empleadoIndex}][valor_hora_extra]" 
                           step="0.01" min="0" placeholder="0.00" onchange="calcularTotal(${empleadoIndex})" ${readonly}>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Comisiones</label>
                    <input type="number" class="form-control ${bgClass}" name="empleados[${empleadoIndex}][comisiones]" 
                           step="0.01" min="0" placeholder="0.00" onchange="calcularTotal(${empleadoIndex})" ${readonly}>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Bonificaciones</label>
                    <input type="number" class="form-control ${bgClass}" name="empleados[${empleadoIndex}][bonificaciones]" 
                           step="0.01" min="0" placeholder="0.00" onchange="calcularTotal(${empleadoIndex})" ${readonly}>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Descuentos</label>
                    <input type="number" class="form-control ${bgClass}" name="empleados[${empleadoIndex}][descuentos]" 
                           step="0.01" min="0" placeholder="0.00" onchange="calcularTotal(${empleadoIndex})" ${readonly}>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Total a Pagar</label>
                    <input type="text" class="form-control total-empleado bg-light" 
                           id="total-${empleadoIndex}" readonly value="Q0.00">
                </div>
                <div class="col-md-4 mb-3">
                    ${esPagado && datosEmpleado.observaciones_pago ? `
                    <label class="form-label">Observaciones del Pago</label>
                    <input type="text" class="form-control bg-light" value="${datosEmpleado.observaciones_pago}" readonly>
                    ` : ''}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Observaciones</label>
                    <input type="text" class="form-control ${bgClass}" name="empleados[${empleadoIndex}][observaciones]" 
                           placeholder="Observaciones del empleado" ${readonly}>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', empleadoHtml);
    empleadoIndex++;
    
    actualizarResumen();
}

function removerEmpleado(index, esPagado = false) {
    const empleado = document.querySelector(`[data-index="${index}"]`);
    if (!empleado) return;
    
    let confirmacion = false;
    
    if (esPagado) {
        // Confirmación especial para empleados pagados
        confirmacion = confirm(
            '⚠️ ATENCIÓN: EMPLEADO PAGADO ⚠️\n\n' +
            'Está eliminando un empleado que ya fue pagado.\n' +
            'Esta acción:\n' +
            '• Eliminará el registro del pago realizado\n' +
            '• Permitirá agregar un nuevo detalle corregido\n' +
            '• Debe ser justificada para auditoría\n\n' +
            '¿Está seguro de continuar?'
        );
        
        if (confirmacion) {
            confirmacion = confirm(
                '🔴 CONFIRMACIÓN FINAL 🔴\n\n' +
                'Confirme que desea eliminar este empleado pagado.\n' +
                'Esta acción no se puede deshacer.\n\n' +
                '¿Continuar con la eliminación?'
            );
        }
    } else {
        // Confirmación normal para empleados pendientes
        confirmacion = confirm('¿Está seguro de eliminar este empleado del lote?');
    }
    
    if (confirmacion) {
        empleado.remove();
        actualizarResumen();
        
        if (esPagado) {
            // Mostrar mensaje informativo
            alert('✅ Empleado pagado eliminado.\n\nPuede agregar un nuevo empleado con los datos correctos.');
        }
    }
}

function cargarEmpleados(index) {
    const tipoSelect = document.querySelector(`select[name="empleados[${index}][tipo]"]`);
    const empleadoSelect = document.querySelector(`select[name="empleados[${index}][id]"]`);
    
    if (!tipoSelect || !empleadoSelect) return;
    
    // Si los selects están deshabilitados (empleado pagado), no hacer nada
    if (tipoSelect.disabled || empleadoSelect.disabled) return;
    
    const tipo = tipoSelect.value;
    empleadoSelect.innerHTML = '<option value="">Cargando...</option>';
    empleadoSelect.disabled = true;
    
    if (tipo === 'trabajador') {
        empleadoSelect.innerHTML = '<option value="">Seleccione trabajador</option>';
        trabajadores.forEach(trabajador => {
            empleadoSelect.innerHTML += `<option value="${trabajador.id}">${trabajador.nombre} ${trabajador.apellido}</option>`;
        });
    } else if (tipo === 'vendedor') {
        empleadoSelect.innerHTML = '<option value="">Seleccione vendedor</option>';
        usuarios.forEach(usuario => {
            empleadoSelect.innerHTML += `<option value="${usuario.id}">${usuario.name}</option>`;
        });
    } else {
        empleadoSelect.innerHTML = '<option value="">Primero seleccione el tipo</option>';
    }
    
    empleadoSelect.disabled = false;
}

function calcularTotal(index) {
    const sueldoBaseInput = document.querySelector(`input[name="empleados[${index}][sueldo_base]"]`);
    
    // Si el input está en modo readonly, no calcular (empleado pagado)
    if (sueldoBaseInput && sueldoBaseInput.readOnly) return;
    
    const sueldoBase = parseFloat(sueldoBaseInput.value) || 0;
    const horasExtra = parseFloat(document.querySelector(`input[name="empleados[${index}][horas_extra]"]`).value) || 0;
    const valorHora = parseFloat(document.querySelector(`input[name="empleados[${index}][valor_hora_extra]"]`).value) || 0;
    const comisiones = parseFloat(document.querySelector(`input[name="empleados[${index}][comisiones]"]`).value) || 0;
    const bonificaciones = parseFloat(document.querySelector(`input[name="empleados[${index}][bonificaciones]"]`).value) || 0;
    const descuentos = parseFloat(document.querySelector(`input[name="empleados[${index}][descuentos]"]`).value) || 0;
    
    const totalHorasExtra = horasExtra * valorHora;
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
    
    let totalEmpleados = empleados.length;
    let totalSueldo = 0;
    let totalGeneral = 0;
    
    empleados.forEach((empleado, index) => {
        const sueldoBaseInput = empleado.querySelector(`input[name*="[sueldo_base]"]`);
        const totalInput = empleado.querySelector('.total-empleado');
        
        const sueldoBase = sueldoBaseInput ? parseFloat(sueldoBaseInput.value) || 0 : 0;
        const totalValue = totalInput ? parseFloat(totalInput.value.replace('Q', '')) || 0 : 0;
        
        totalSueldo += sueldoBase;
        totalGeneral += totalValue;
    });
    
    const totalEmpleadosEl = document.getElementById('total-empleados');
    const totalSueldoEl = document.getElementById('total-sueldo');
    const totalGeneralEl = document.getElementById('total-general');
    
    if (totalEmpleadosEl) totalEmpleadosEl.textContent = totalEmpleados;
    if (totalSueldoEl) totalSueldoEl.textContent = formatearMoneda(totalSueldo);
    if (totalGeneralEl) totalGeneralEl.textContent = formatearMoneda(totalGeneral);
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('Empleados existentes:', empleadosExistentes);
    console.log('Cantidad de empleados existentes:', empleadosExistentes.length);
    
    empleadosExistentes.forEach((detalle, index) => {
        console.log(`Agregando empleado existente ${index}:`, detalle);
        // Usar la función actualizada con los datos del empleado
        agregarEmpleado(true, detalle);
        
        // El empleadoIndex se incrementa en agregarEmpleado(), así que el índice actual es empleadoIndex - 1
        const currentIndex = empleadoIndex - 1;
        
        // Llenar datos existentes
        const tipoSelect = document.querySelector(`select[name="empleados[${currentIndex}][tipo]"]`);
        let tipo = detalle.tipo;
        console.log(`Estableciendo tipo: ${tipo} para empleado ${currentIndex}`);
        if (tipoSelect && !tipoSelect.disabled) {
            tipoSelect.value = tipo;
        }
        
        // Solo cargar empleados si no está pagado
        if (detalle.estado !== 'pagado') {
            cargarEmpleados(currentIndex);
        }
        
        setTimeout(() => {
            console.log(`Llenando campos para empleado ${currentIndex}:`, detalle);
            
            const empleadoSelect = document.querySelector(`select[name="empleados[${currentIndex}][id]"]`);
            console.log('Select de empleado encontrado:', !!empleadoSelect);
            if (empleadoSelect && !empleadoSelect.disabled) {
                empleadoSelect.value = detalle.id;
                console.log('ID establecido:', detalle.id);
            }
            
            // Llenar todos los campos si no están readonly
            const campos = [
                { name: 'sueldo_base', value: detalle.sueldo_base },
                { name: 'horas_extra', value: detalle.horas_extra || 0 },
                { name: 'valor_hora_extra', value: detalle.valor_hora_extra || 0 },
                { name: 'comisiones', value: detalle.comisiones || 0 },
                { name: 'bonificaciones', value: detalle.bonificaciones || 0 },
                { name: 'descuentos', value: detalle.descuentos || 0 },
                { name: 'observaciones', value: detalle.observaciones || '' }
            ];
            
            campos.forEach(campo => {
                const input = document.querySelector(`input[name="empleados[${currentIndex}][${campo.name}]"]`);
                if (input) {
                    input.value = campo.value;
                    console.log(`${campo.name} establecido:`, campo.value);
                }
            });
            
            // Mostrar el total calculado
            const totalInput = document.getElementById(`total-${currentIndex}`);
            if (totalInput && detalle.total_pagar) {
                totalInput.value = `Q${parseFloat(detalle.total_pagar).toFixed(2)}`;
                console.log('Total establecido:', detalle.total_pagar);
            } else {
                // Si no hay total guardado, calculamos
                calcularTotal(currentIndex);
            }
        }, 500 + (currentIndex * 100)); // Incrementar delay por empleado
    });
    
    // Actualizar resumen después de cargar todos los empleados
    setTimeout(() => {
        actualizarResumen();
        console.log('Empleados cargados completamente, resumen actualizado');
        
        // Debug final: verificar que todos los empleados estén en el formulario
        console.log('=== VERIFICACIÓN FINAL ===');
        console.log('Empleados en DOM:', document.querySelectorAll('.empleado-item').length);
        console.log('Inputs de empleados:', document.querySelectorAll('input[name^="empleados"]').length);
        console.log('Selects de empleados:', document.querySelectorAll('select[name^="empleados"]').length);
    }, 800);
});
</script>
@endsection
