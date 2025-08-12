@extends('layouts.admin')

@section('title', 'Historial de Pagos de Comisiones')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-clock-history"></i> Historial de Pagos de Comisiones
                    </h4>
                    <div class="card-tools">
                        <a href="{{ route('pagos_comisiones.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Volver a Pagos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Fecha -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-funnel"></i> Filtros de Búsqueda</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('pagos_comisiones.historial') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" 
                                       value="{{ $fechaInicio->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" 
                                       value="{{ $fechaFin->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Buscar
                                </button>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <a href="{{ route('pagos_comisiones.reporte') }}?fecha_inicio={{ $fechaInicio->format('Y-m-d') }}&fecha_fin={{ $fechaFin->format('Y-m-d') }}" 
                                   class="btn btn-success w-100">
                                    <i class="bi bi-file-earmark-excel"></i> Reporte
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-cash-coin"></i> Total Pagado</h6>
                </div>
                <div class="card-body text-center">
                    <h4 class="text-success">Q{{ number_format($estadisticasPagos['total_pagado'], 2) }}</h4>
                    <small class="text-muted">{{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-list-ol"></i> Cantidad de Pagos</h6>
                </div>
                <div class="card-body text-center">
                    <h4 class="text-info">{{ $estadisticasPagos['cantidad_pagos'] }}</h4>
                    <small class="text-muted">Pagos procesados</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Promedio por Pago</h6>
                </div>
                <div class="card-body text-center">
                    <h4 class="text-warning">Q{{ number_format($estadisticasPagos['promedio_pago'] ?? 0, 2) }}</h4>
                    <small class="text-muted">Monto promedio</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Historial -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-table"></i> Historial de Pagos Realizados
                        <span class="badge bg-success ms-2">{{ $pagos->total() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    @if($pagos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Fecha Pago</th>
                                        <th>Receptor</th>
                                        <th>Tipo Comisión</th>
                                        <th>Monto</th>
                                        <th>Método</th>
                                        <th>Referencia</th>
                                        <th>Estado</th>
                                        <th>Registrado por</th>
                                        <th width="120">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pagos as $pago)
                                    <tr>
                                        <td>
                                            <strong>#{{ $pago->id }}</strong>
                                        </td>
                                        <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                        <td>
                                            @if($pago->comision->commissionable_type === 'App\Models\User')
                                                <i class="bi bi-person-check text-primary"></i>
                                                {{ $pago->comision->commissionable->name }}
                                                <br><small class="text-muted">Vendedor</small>
                                            @else
                                                <i class="bi bi-person-workspace text-info"></i>
                                                {{ $pago->comision->commissionable->nombre }} {{ $pago->comision->commissionable->apellido }}
                                                <br><small class="text-muted">{{ $pago->comision->commissionable->tipoTrabajador->nombre ?? 'Trabajador' }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($pago->comision->tipo_comision === 'venta_meta') bg-primary
                                                @elseif($pago->comision->tipo_comision === 'mecanico') bg-info
                                                @elseif($pago->comision->tipo_comision === 'carwash') bg-success
                                                @else bg-secondary @endif">
                                                {{ ucfirst($pago->comision->tipo_comision) }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">Q{{ number_format($pago->monto, 2) }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($pago->metodo_pago) }}</span>
                                        </td>
                                        <td>
                                            @if($pago->referencia)
                                                <span class="text-muted">{{ $pago->referencia }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($pago->estado === 'completado')
                                                <span class="badge bg-success">Completado</span>
                                            @elseif($pago->estado === 'pendiente')
                                                <span class="badge bg-warning">Pendiente</span>
                                            @else
                                                <span class="badge bg-danger">Anulado</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $pago->usuario->name }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="verDetallePago({{ $pago->id }})" 
                                                        title="Ver detalles">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                @if($pago->comprobante_imagen)
                                                    <a href="{{ asset($pago->comprobante_imagen) }}" target="_blank" 
                                                       class="btn btn-sm btn-secondary" title="Ver comprobante">
                                                        <i class="bi bi-file-earmark-image"></i>
                                                    </a>
                                                @endif
                                                @if($pago->estado === 'completado')
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="anularPago({{ $pago->id }})" 
                                                            title="Anular pago">
                                                        <i class="bi bi-x-circle"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $pagos->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="bi bi-info-circle"></i>
                            No se encontraron pagos en el período seleccionado.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Detalles del Pago -->
<div class="modal fade" id="modalDetallePago" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenido-detalle-pago">
                <!-- Contenido cargado dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function verDetallePago(pagoId) {
    // Aquí podrías hacer una llamada AJAX para cargar los detalles
    // Por ahora mostraremos información básica
    const modal = new bootstrap.Modal(document.getElementById('modalDetallePago'));
    document.getElementById('contenido-detalle-pago').innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando detalles del pago #${pagoId}...</p>
        </div>
    `;
    modal.show();
    
    // Simular carga de datos (reemplazar con llamada AJAX real)
    setTimeout(() => {
        document.getElementById('contenido-detalle-pago').innerHTML = `
            <div class="alert alert-info">
                <h6>Información del Pago #${pagoId}</h6>
                <p>Los detalles completos del pago se mostrarán aquí.</p>
                <p><strong>Nota:</strong> Esta funcionalidad se puede expandir para mostrar información detallada de la comisión, venta asociada, etc.</p>
            </div>
        `;
    }, 1000);
}

function anularPago(pagoId) {
    if (confirm('¿Está seguro de que desea anular este pago? Esta acción no se puede deshacer.')) {
        // Enviar solicitud para anular el pago
        fetch(`{{ url('pagos_comisiones/anular') }}/${pagoId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error al anular el pago: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        });
    }
}
</script>
@endsection
