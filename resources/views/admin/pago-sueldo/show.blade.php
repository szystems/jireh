@extends('layouts.admin')
@section('content')

    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-eye"></i>
                </div>
                <div class="page-title">
                    <h5>Detalle del Lote de Sueldos</h5>
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
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Información del Lote -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-wallet2"></i> {{ $pagoSueldo->numero_lote }}
                                    </h5>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.pago-sueldo.index') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-left"></i> Volver al Listado
                                        </a>
                                        
                                        @if (Auth::user()->role_as == 0)
                                            @if($pagoSueldo->estado == 'pendiente')
                                                <a href="{{ route('admin.pago-sueldo.edit', $pagoSueldo->id) }}" class="btn btn-outline-warning">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </a>
                                            @endif
                                            
                                            <a href="{{ route('admin.pago-sueldo.pdf', $pagoSueldo->id) }}" class="btn btn-outline-info" target="_blank">
                                                <i class="bi bi-file-earmark-pdf"></i> PDF
                                            </a>
                                            
                                            @if($pagoSueldo->estado == 'pendiente')
                                                <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#cambiarEstadoModal">
                                                    <i class="bi bi-check-circle"></i> Marcar como Pagado
                                                </button>
                                                
                                                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#cancelarLoteModal">
                                                    <i class="bi bi-x-circle"></i> Cancelar Lote
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Número de Lote:</strong></td>
                                            <td><span class="text-primary">{{ $pagoSueldo->numero_lote }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Período:</strong></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ $pagoSueldo->periodo_completo }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Fecha de Pago:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($pagoSueldo->fecha_pago)->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Método de Pago:</strong></td>
                                            <td>
                                                @switch($pagoSueldo->metodo_pago)
                                                    @case('efectivo')
                                                        <span class="badge bg-success"><i class="bi bi-cash"></i> Efectivo</span>
                                                        @break
                                                    @case('transferencia')
                                                        <span class="badge bg-primary"><i class="bi bi-bank"></i> Transferencia</span>
                                                        @break
                                                    @case('cheque')
                                                        <span class="badge bg-warning"><i class="bi bi-credit-card"></i> Cheque</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($pagoSueldo->metodo_pago) }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Estado:</strong></td>
                                            <td>
                                                @switch($pagoSueldo->estado)
                                                    @case('pendiente')
                                                        <span class="badge bg-warning"><i class="bi bi-clock"></i> Pendiente</span>
                                                        @break
                                                    @case('pagado')
                                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Pagado</span>
                                                        @break
                                                    @case('cancelado')
                                                        <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Cancelado</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($pagoSueldo->estado) }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total de Empleados:</strong></td>
                                            <td><span class="badge bg-secondary">{{ $pagoSueldo->detalles->count() }} empleados</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total del Lote:</strong></td>
                                            <td><h5 class="text-success mb-0">Q{{ number_format($pagoSueldo->total_monto, 2) }}</h5></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Creado por:</strong></td>
                                            <td>{{ $pagoSueldo->usuario->name ?? 'Usuario eliminado' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            @if($pagoSueldo->observaciones)
                                <div class="row">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <strong><i class="bi bi-info-circle"></i> Observaciones:</strong><br>
                                            {{ $pagoSueldo->observaciones }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Resumen de Progreso de Pagos -->
                    @php
                        $resumen = $pagoSueldo->getResumenPagos();
                    @endphp
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-bar-chart"></i> Progreso de Pagos
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body py-2">
                                            <h4 class="text-info mb-1">{{ $resumen['total_empleados'] }}</h4>
                                            <small class="text-muted">Total Empleados</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body py-2">
                                            <h4 class="text-success mb-1">{{ $resumen['empleados_pagados'] }}</h4>
                                            <small class="text-muted">Pagados</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body py-2">
                                            <h4 class="text-warning mb-1">{{ $resumen['empleados_pendientes'] }}</h4>
                                            <small class="text-muted">Pendientes</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body py-2">
                                            <h4 class="text-danger mb-1">{{ $resumen['empleados_cancelados'] }}</h4>
                                            <small class="text-muted">Cancelados</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span><strong>Progreso de Pagos:</strong></span>
                                        <span class="badge bg-primary">{{ number_format($resumen['progreso_porcentaje'], 1) }}%</span>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ $resumen['progreso_porcentaje'] }}%"
                                             aria-valuenow="{{ $resumen['progreso_porcentaje'] }}" 
                                             aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <strong>Monto Pagado:</strong> 
                                        <span class="text-success">Q{{ number_format($resumen['monto_pagado'], 2) }}</span>
                                    </small>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <small class="text-muted">
                                        <strong>Monto Pendiente:</strong> 
                                        <span class="text-warning">Q{{ number_format($resumen['monto_pendiente'], 2) }}</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalle de Empleados -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-people"></i> Detalle de Empleados
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="align-middle">Empleado</th>
                                            <th rowspan="2" class="text-center align-middle">Tipo</th>
                                            <th rowspan="2" class="text-end align-middle">Sueldo Base</th>
                                            <th colspan="4" class="text-center">Bonificaciones Detalladas</th>
                                            <th rowspan="2" class="text-end align-middle">Descuentos</th>
                                            <th rowspan="2" class="text-end align-middle">Estado</th>
                                            <th rowspan="2" class="text-end align-middle">Total</th>
                                        </tr>
                                        <tr>
                                            <th class="text-end" style="font-size: 0.8em;">H. Extra</th>
                                            <th class="text-end" style="font-size: 0.8em;">Valor/H</th>
                                            <th class="text-end" style="font-size: 0.8em;">Comisiones</th>
                                            <th class="text-end" style="font-size: 0.8em;">Bonificaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pagoSueldo->detalles as $detalle)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            @if($detalle->tipo_empleado === 'trabajador')
                                                                <i class="bi bi-person-workspace fs-4 text-primary"></i>
                                                            @else
                                                                <i class="bi bi-person-badge fs-4 text-success"></i>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">
                                                                @if($detalle->tipo_empleado === 'trabajador')
                                                                    {{ $detalle->trabajador ? $detalle->trabajador->nombre . ' ' . $detalle->trabajador->apellido : 'Trabajador eliminado' }}
                                                                @else
                                                                    {{ $detalle->usuario ? $detalle->usuario->name : 'Usuario eliminado' }}
                                                                @endif
                                                            </h6>
                                                            @if($detalle->observaciones)
                                                                <small class="text-muted">{{ $detalle->observaciones }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if($detalle->tipo_empleado === 'trabajador')
                                                        <span class="badge bg-primary">Trabajador</span>
                                                    @else
                                                        <span class="badge bg-success">Usuario</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">Q{{ number_format($detalle->sueldo_base, 2) }}</td>
                                                
                                                <!-- Desglose de bonificaciones -->
                                                <td class="text-end">
                                                    @if($detalle->horas_extra > 0)
                                                        <span class="text-info">{{ number_format($detalle->horas_extra, 1) }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if($detalle->valor_hora_extra > 0)
                                                        <span class="text-info">Q{{ number_format($detalle->valor_hora_extra, 2) }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if($detalle->comisiones > 0)
                                                        <span class="text-success">Q{{ number_format($detalle->comisiones, 2) }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if($detalle->bonificaciones > 0)
                                                        <span class="text-success">Q{{ number_format($detalle->bonificaciones, 2) }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                
                                                <td class="text-end">
                                                    @if($detalle->deducciones > 0)
                                                        <span class="text-danger">Q{{ number_format($detalle->deducciones, 2) }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                
                                                <td class="text-center">
                                                    <div class="d-flex align-items-center justify-content-center">
                                                        <span class="badge bg-{{ $detalle->estado_color }} me-2">
                                                            {{ $detalle->estado_texto }}
                                                        </span>
                                                        
                                                        @if(Auth::user()->role_as == 0 && $detalle->estado !== 'pagado')
                                                            <div class="btn-group btn-group-sm">
                                                                @if($detalle->estado === 'pendiente')
                                                                    <button type="button" 
                                                                            class="btn btn-success btn-sm cambiar-estado-detalle" 
                                                                            data-detalle-id="{{ $detalle->id }}" 
                                                                            data-estado="pagado"
                                                                            title="Marcar como pagado">
                                                                        <i class="bi bi-check"></i>
                                                                    </button>
                                                                    <button type="button" 
                                                                            class="btn btn-danger btn-sm cambiar-estado-detalle" 
                                                                            data-detalle-id="{{ $detalle->id }}" 
                                                                            data-estado="cancelado"
                                                                            title="Cancelar">
                                                                        <i class="bi bi-x"></i>
                                                                    </button>
                                                                @elseif($detalle->estado === 'cancelado')
                                                                    <button type="button" 
                                                                            class="btn btn-warning btn-sm cambiar-estado-detalle" 
                                                                            data-detalle-id="{{ $detalle->id }}" 
                                                                            data-estado="pendiente"
                                                                            title="Restaurar a pendiente">
                                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @if($detalle->fecha_pago)
                                                        <small class="text-muted d-block">{{ $detalle->fecha_pago->format('d/m/Y H:i') }}</small>
                                                    @endif
                                                    @if($detalle->observaciones_pago)
                                                        <small class="text-muted d-block" title="{{ $detalle->observaciones_pago }}">
                                                            <i class="bi bi-chat-square-text"></i>
                                                        </small>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <strong class="text-success">Q{{ number_format($detalle->total_pagar, 2) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active">
                                            <th colspan="9" class="text-end">Total del Lote:</th>
                                            <th class="text-end">
                                                <h5 class="text-success mb-0">Q{{ number_format($pagoSueldo->total_monto, 2) }}</h5>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Row end -->
        </div>
    </div>

    <!-- Modal para cambiar estado -->
    @if (Auth::user()->role_as == 0 && $pagoSueldo->estado == 'pendiente')
        <div class="modal fade" id="cambiarEstadoModal" tabindex="-1" aria-labelledby="cambiarEstadoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cambiarEstadoModalLabel">
                            <i class="bi bi-check-circle text-success"></i> Marcar como Pagado
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.pago-sueldo.cambiar-estado', $pagoSueldo->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>Confirme el pago del lote {{ $pagoSueldo->numero_lote }}</strong><br>
                                Total: <strong>Q{{ number_format($pagoSueldo->total_monto, 2) }}</strong><br>
                                Empleados: <strong>{{ $pagoSueldo->detalles->count() }}</strong>
                            </div>
                            
                            <input type="hidden" name="estado" value="pagado">
                            
                            <div class="mb-3">
                                <label for="observaciones_estado" class="form-label">Observaciones del pago</label>
                                <textarea class="form-control" id="observaciones_estado" name="observaciones_estado" rows="3" 
                                          placeholder="Observaciones adicionales sobre el pago realizado..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Confirmar Pago
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal para cambiar estado de detalle individual -->
    <div class="modal fade" id="cambiarEstadoDetalleModal" tabindex="-1" aria-labelledby="cambiarEstadoDetalleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cambiarEstadoDetalleModalLabel">
                        <i class="bi bi-person-check text-success"></i> Cambiar Estado Individual
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="detalle-empleado-info"></p>
                    <div class="mb-3">
                        <label for="observaciones_detalle" class="form-label">Observaciones del pago individual</label>
                        <textarea class="form-control" id="observaciones_detalle" rows="3" 
                                  placeholder="Observaciones específicas para este empleado..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn" id="confirmar-cambio-estado">
                        <i class="bi bi-check-circle"></i> <span id="texto-boton-confirmacion">Confirmar</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de cancelación del lote -->
    @if (Auth::user()->role_as == 0 && $pagoSueldo->estado == 'pendiente')
        <div class="modal fade" id="cancelarLoteModal" tabindex="-1" aria-labelledby="cancelarLoteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelarLoteModalLabel">
                            <i class="bi bi-exclamation-triangle text-warning"></i> Confirmar Cancelación del Lote
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>¿Está seguro de que desea cancelar el lote {{ $pagoSueldo->numero_lote }}?</strong>
                        </div>
                        <p>El lote se marcará como <strong class="text-danger">"Cancelado"</strong> y se mantendrá en el historial.</p>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Información del lote:</strong><br>
                            • Total: <strong>Q{{ number_format($pagoSueldo->total_monto, 2) }}</strong><br>
                            • Empleados: <strong>{{ $pagoSueldo->detalles->count() }}</strong><br>
                            • Período: <strong>{{ $pagoSueldo->periodo_completo }}</strong>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, mantener</button>
                        <form action="{{ route('admin.pago-sueldo.destroy', $pagoSueldo->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-x-circle"></i> Sí, cancelar lote
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar cambio de estado de detalles individuales
            const botonesEstado = document.querySelectorAll('.cambiar-estado-detalle');
            const modal = new bootstrap.Modal(document.getElementById('cambiarEstadoDetalleModal'));
            const modalTitle = document.getElementById('cambiarEstadoDetalleModalLabel');
            const empleadoInfo = document.getElementById('detalle-empleado-info');
            const observacionesInput = document.getElementById('observaciones_detalle');
            const botonConfirmar = document.getElementById('confirmar-cambio-estado');
            const textoBoton = document.getElementById('texto-boton-confirmacion');
            
            let detalleActual = null;
            let estadoActual = null;
            
            botonesEstado.forEach(boton => {
                boton.addEventListener('click', function() {
                    detalleActual = this.dataset.detalleId;
                    estadoActual = this.dataset.estado;
                    
                    // Obtener nombre del empleado de la fila
                    const fila = this.closest('tr');
                    const nombreEmpleado = fila.querySelector('h6').textContent.trim();
                    
                    // Configurar modal según el estado
                    switch(estadoActual) {
                        case 'pagado':
                            modalTitle.innerHTML = '<i class="bi bi-check-circle text-success"></i> Marcar como Pagado';
                            empleadoInfo.innerHTML = `¿Está seguro que desea marcar como <strong class="text-success">PAGADO</strong> el sueldo de:<br><strong>${nombreEmpleado}</strong>?`;
                            textoBoton.textContent = 'Marcar como Pagado';
                            botonConfirmar.className = 'btn btn-success';
                            break;
                        case 'cancelado':
                            modalTitle.innerHTML = '<i class="bi bi-x-circle text-danger"></i> Cancelar Pago';
                            empleadoInfo.innerHTML = `¿Está seguro que desea <strong class="text-danger">CANCELAR</strong> el pago de:<br><strong>${nombreEmpleado}</strong>?`;
                            textoBoton.textContent = 'Cancelar Pago';
                            botonConfirmar.className = 'btn btn-danger';
                            break;
                        case 'pendiente':
                            modalTitle.innerHTML = '<i class="bi bi-arrow-counterclockwise text-warning"></i> Restaurar a Pendiente';
                            empleadoInfo.innerHTML = `¿Está seguro que desea restaurar a <strong class="text-warning">PENDIENTE</strong> el pago de:<br><strong>${nombreEmpleado}</strong>?`;
                            textoBoton.textContent = 'Restaurar a Pendiente';
                            botonConfirmar.className = 'btn btn-warning';
                            break;
                    }
                    
                    observacionesInput.value = '';
                    modal.show();
                });
            });
            
            // Manejar confirmación del cambio de estado
            botonConfirmar.addEventListener('click', function() {
                if (!detalleActual || !estadoActual) return;
                
                const observaciones = observacionesInput.value.trim();
                
                // Mostrar loading
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
                
                // Enviar petición AJAX
                fetch(`/pagos-sueldos/detalle/${detalleActual}/estado`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        estado: estadoActual,
                        observaciones_pago: observaciones
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar la interfaz
                        actualizarFilaDetalle(detalleActual, data.detalle);
                        
                        // Mostrar mensaje de éxito
                        mostrarMensaje('success', data.message);
                        
                        modal.hide();
                    } else {
                        mostrarMensaje('danger', data.message || 'Error al cambiar el estado');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarMensaje('danger', 'Error de conexión. Intente nuevamente.');
                })
                .finally(() => {
                    // Restaurar botón
                    this.disabled = false;
                    this.innerHTML = `<i class="bi bi-check-circle"></i> ${textoBoton.textContent}`;
                });
            });
            
            function actualizarFilaDetalle(detalleId, detalle) {
                // Encontrar la fila correspondiente y actualizar el badge de estado
                const fila = document.querySelector(`[data-detalle-id="${detalleId}"]`).closest('tr');
                const estadoCell = fila.querySelector('.badge');
                const botonesCell = fila.querySelector('.btn-group');
                
                // Actualizar badge
                estadoCell.className = `badge bg-${detalle.estado_color} me-2`;
                estadoCell.textContent = detalle.estado_texto;
                
                // Actualizar fecha si existe
                let fechaElement = estadoCell.parentElement.querySelector('small');
                if (detalle.fecha_pago) {
                    if (!fechaElement) {
                        fechaElement = document.createElement('small');
                        fechaElement.className = 'text-muted d-block';
                        estadoCell.parentElement.appendChild(fechaElement);
                    }
                    fechaElement.textContent = detalle.fecha_pago;
                } else if (fechaElement) {
                    fechaElement.remove();
                }
                
                // Actualizar botones
                if (botonesCell) {
                    actualizarBotonesEstado(botonesCell, detalleId, detalle.estado);
                }
                
                // Recargar página para actualizar totales si es necesario
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
            
            function actualizarBotonesEstado(container, detalleId, estado) {
                container.innerHTML = '';
                
                if (estado === 'pendiente') {
                    container.innerHTML = `
                        <button type="button" class="btn btn-success btn-sm cambiar-estado-detalle" 
                                data-detalle-id="${detalleId}" data-estado="pagado" title="Marcar como pagado">
                            <i class="bi bi-check"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm cambiar-estado-detalle" 
                                data-detalle-id="${detalleId}" data-estado="cancelado" title="Cancelar">
                            <i class="bi bi-x"></i>
                        </button>
                    `;
                } else if (estado === 'cancelado') {
                    container.innerHTML = `
                        <button type="button" class="btn btn-warning btn-sm cambiar-estado-detalle" 
                                data-detalle-id="${detalleId}" data-estado="pendiente" title="Restaurar a pendiente">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    `;
                }
                
                // Re-agregar event listeners
                container.querySelectorAll('.cambiar-estado-detalle').forEach(btn => {
                    btn.addEventListener('click', function() {
                        // Reutilizar la lógica del event listener principal
                        document.querySelector(`[data-detalle-id="${this.dataset.detalleId}"]`).click();
                    });
                });
            }
            
            function mostrarMensaje(tipo, mensaje) {
                const alertContainer = document.querySelector('.col-sm-12');
                const alertHTML = `
                    <div class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                        ${mensaje}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `;
                alertContainer.insertAdjacentHTML('afterbegin', alertHTML);
                
                // Auto-dismiss después de 3 segundos
                setTimeout(() => {
                    const alert = alertContainer.querySelector('.alert');
                    if (alert) {
                        bootstrap.Alert.getInstance(alert)?.close();
                    }
                }, 3000);
            }
        });
    </script>

@endsection
