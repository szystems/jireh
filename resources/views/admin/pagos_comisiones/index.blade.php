@extends('layouts.admin')

@section('title', 'Gestión de Pagos de Comisiones')

@section('content')
<div class="container-fluid">
    <!-- Header con estadísticas -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-cash-coin"></i> Gestión de Pagos de Comisiones
                    </h4>
                    <div class="card-tools">
                        <div class="row">
                            <div class="col-auto">
                                <select name="periodo" id="periodo-filter" class="form-select form-select-sm">
                                    <option value="mes_actual" {{ $periodo == 'mes_actual' ? 'selected' : '' }}>Este Mes</option>
                                    <option value="mes_anterior" {{ $periodo == 'mes_anterior' ? 'selected' : '' }}>Mes Anterior</option>
                                    <option value="semana_actual" {{ $periodo == 'semana_actual' ? 'selected' : '' }}>Esta Semana</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <select name="tipo" id="tipo-filter" class="form-select form-select-sm">
                                    <option value="todas" {{ $tipoComision == 'todas' ? 'selected' : '' }}>Todas</option>
                                    <option value="vendedores" {{ $tipoComision == 'vendedores' ? 'selected' : '' }}>Vendedores</option>
                                    <option value="mecanicos" {{ $tipoComision == 'mecanicos' ? 'selected' : '' }}>Mecánicos</option>
                                    <option value="carwash" {{ $tipoComision == 'carwash' ? 'selected' : '' }}>Car Wash</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-primary btn-sm" onclick="aplicarFiltros()">
                                    <i class="bi bi-funnel"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen de Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-calculator"></i> Total Comisiones</h6>
                </div>
                <div class="card-body text-center">
                    <h4 class="text-info">Q{{ number_format($estadisticas['total_comisiones'], 2) }}</h4>
                    <small class="text-muted">Período: {{ $fechas['inicio']->format('d/m') }} - {{ $fechas['fin']->format('d/m') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="bi bi-clock"></i> Pendientes</h6>
                </div>
                <div class="card-body text-center">
                    <h4 class="text-warning">Q{{ number_format($estadisticas['comisiones_pendientes'], 2) }}</h4>
                    <small class="text-muted">{{ $estadisticas['cantidad_pendientes'] }} comisiones</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-check-circle"></i> Pagadas</h6>
                </div>
                <div class="card-body text-center">
                    <h4 class="text-success">Q{{ number_format($estadisticas['comisiones_pagadas'], 2) }}</h4>
                    <small class="text-muted">{{ $estadisticas['cantidad_pagadas'] }} comisiones</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-percent"></i> Progreso</h6>
                </div>
                <div class="card-body text-center">
                    @php
                        $porcentajePagado = $estadisticas['total_comisiones'] > 0 ? 
                            ($estadisticas['comisiones_pagadas'] / $estadisticas['total_comisiones']) * 100 : 0;
                    @endphp
                    <h4 class="text-primary">{{ number_format($porcentajePagado, 1) }}%</h4>
                    <small class="text-muted">Comisiones pagadas</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Procesamiento Masivo -->
    @if($comisionesPendientes->count() > 0)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Procesamiento Masivo de Pagos</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pagos_comisiones.procesar_masivos') }}" method="POST" id="form-pago-masivo">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <label for="fecha_pago" class="form-label">Fecha de Pago</label>
                                <input type="date" name="fecha_pago" id="fecha_pago" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="metodo_pago" class="form-label">Método de Pago</label>
                                <select name="metodo_pago" id="metodo_pago" class="form-select" required>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="efectivo">Efectivo</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <input type="text" name="observaciones" id="observaciones" class="form-control" placeholder="Pago mensual...">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-warning w-100" onclick="return confirmarPagoMasivo()">
                                    <i class="bi bi-lightning"></i> Procesar Seleccionadas
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabla de Comisiones Pendientes -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul"></i> Comisiones Pendientes de Pago
                        <span class="badge bg-warning ms-2">{{ $comisionesPendientes->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($comisionesPendientes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="select-all" class="form-check-input">
                                        </th>
                                        <th>Receptor</th>
                                        <th>Tipo</th>
                                        <th>Fecha Cálculo</th>
                                        <th>Monto Total</th>
                                        <th>Pagado</th>
                                        <th>Pendiente</th>
                                        <th>Estado</th>
                                        <th width="150">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($comisionesPendientes as $comision)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="comisiones[]" value="{{ $comision->id }}" 
                                                   class="form-check-input comision-checkbox" form="form-pago-masivo">
                                        </td>
                                        <td>
                                            <strong>
                                                @if($comision->commissionable_type === 'App\Models\User')
                                                    <i class="bi bi-person-check text-primary"></i> {{ $comision->commissionable->name }}
                                                @else
                                                    <i class="bi bi-person-workspace text-info"></i> {{ $comision->commissionable->nombre }} {{ $comision->commissionable->apellido }}
                                                @endif
                                            </strong>
                                            <br>
                                            <small class="text-muted">
                                                @if($comision->commissionable_type === 'App\Models\User')
                                                    Vendedor
                                                @else
                                                    {{ $comision->commissionable->tipoTrabajador->nombre ?? 'Trabajador' }}
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($comision->tipo_comision === 'venta_meta') bg-primary
                                                @elseif($comision->tipo_comision === 'mecanico') bg-info
                                                @elseif($comision->tipo_comision === 'carwash') bg-success
                                                @else bg-secondary @endif">
                                                {{ ucfirst($comision->tipo_comision) }}
                                            </span>
                                        </td>
                                        <td>{{ $comision->fecha_calculo->format('d/m/Y') }}</td>
                                        <td class="text-end">
                                            <strong>Q{{ number_format($comision->monto, 2) }}</strong>
                                        </td>
                                        <td class="text-end text-success">
                                            Q{{ number_format($comision->montoPagado(), 2) }}
                                        </td>
                                        <td class="text-end text-warning">
                                            <strong>Q{{ number_format($comision->montoPendiente(), 2) }}</strong>
                                        </td>
                                        <td>
                                            @if($comision->estado === 'pendiente')
                                                <span class="badge bg-warning">Pendiente</span>
                                            @elseif($comision->estado === 'pagado')
                                                <span class="badge bg-success">Pagado</span>
                                            @elseif($comision->estado === 'cancelado')
                                                <span class="badge bg-danger">Cancelado</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($comision->estado) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-primary" 
                                                        onclick="abrirModalPago({{ $comision->id }}, '{{ $comision->commissionable->name ?? $comision->commissionable->nombre }}', {{ $comision->montoPendiente() }})">
                                                    <i class="bi bi-cash"></i>
                                                </button>
                                                <a href="{{ route('comisiones.show', $comision->id) }}" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle"></i>
                            No hay comisiones pendientes de pago en el período seleccionado.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Pago Individual -->
<div class="modal fade" id="modalPagoIndividual" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Pago de Comisión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST" id="form-pago-individual" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="receptor_modal" class="form-label">Receptor</label>
                        <input type="text" id="receptor_modal" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="monto_modal" class="form-label">Monto a Pagar</label>
                        <input type="number" step="0.01" name="monto" id="monto_modal" class="form-control" required>
                        <div class="form-text">Monto pendiente: <span id="pendiente_modal"></span></div>
                    </div>
                    <div class="mb-3">
                        <label for="metodo_pago_modal" class="form-label">Método de Pago</label>
                        <select name="metodo_pago" id="metodo_pago_modal" class="form-select" required>
                            <option value="transferencia">Transferencia</option>
                            <option value="efectivo">Efectivo</option>
                            <option value="cheque">Cheque</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="fecha_pago_modal" class="form-label">Fecha de Pago</label>
                        <input type="date" name="fecha_pago" id="fecha_pago_modal" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="referencia_modal" class="form-label">Referencia</label>
                        <input type="text" name="referencia" id="referencia_modal" class="form-control" placeholder="Número de transferencia, cheque, etc.">
                    </div>
                    <div class="mb-3">
                        <label for="comprobante_modal" class="form-label">Comprobante</label>
                        <input type="file" name="comprobante_imagen" id="comprobante_modal" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="observaciones_modal" class="form-label">Observaciones</label>
                        <textarea name="observaciones" id="observaciones_modal" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cash"></i> Registrar Pago
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function aplicarFiltros() {
    const periodo = document.getElementById('periodo-filter').value;
    const tipo = document.getElementById('tipo-filter').value;
    
    const params = new URLSearchParams();
    params.append('periodo', periodo);
    params.append('tipo', tipo);
    
    window.location.href = '{{ route("pagos_comisiones.index") }}?' + params.toString();
}

function abrirModalPago(comisionId, receptor, montoPendiente) {
    document.getElementById('receptor_modal').value = receptor;
    document.getElementById('monto_modal').value = montoPendiente;
    document.getElementById('monto_modal').max = montoPendiente;
    document.getElementById('pendiente_modal').textContent = 'Q' + montoPendiente.toFixed(2);
    
    const form = document.getElementById('form-pago-individual');
    form.action = '{{ url("pagos_comisiones/registrar") }}/' + comisionId;
    
    new bootstrap.Modal(document.getElementById('modalPagoIndividual')).show();
}

function confirmarPagoMasivo() {
    const checkboxes = document.querySelectorAll('.comision-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Debe seleccionar al menos una comisión para procesar.');
        return false;
    }
    
    return confirm(`¿Está seguro de procesar el pago de ${checkboxes.length} comisiones seleccionadas?`);
}

// Seleccionar/deseleccionar todas las comisiones
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.comision-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});
</script>
@endsection
