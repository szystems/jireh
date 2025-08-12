@extends('admin.layout')
@section('titulo', 'Reporte de Pagos de Comisión')

@section('contenido')

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-bar"></i> Reporte de Pagos de Comisión
        </h6>
        <div class="btn-group">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#filtrosModal">
                <i class="fas fa-filter"></i> Filtros
            </button>
            <button type="button" class="btn btn-success btn-sm" id="exportarExcel">
                <i class="fas fa-file-excel"></i> Excel
            </button>
            <button type="button" class="btn btn-danger btn-sm" id="exportarPDF">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- Métricas de Resumen -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Pagado
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ${{ number_format($resumen['total_pagado'] ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Pendientes
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ${{ number_format($resumen['total_pendiente'] ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Pagos Realizados
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $resumen['total_pagos'] ?? 0 }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-receipt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Promedio por Pago
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ${{ number_format($resumen['promedio_pago'] ?? 0, 2) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calculator fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico de Tendencias -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tendencia de Pagos Mensuales</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="graficoTendencias" width="100" height="30"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla Detallada -->
        <div class="table-responsive">
            <table class="table table-bordered" id="tablaReporte" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Trabajador</th>
                        <th>Tipo</th>
                        <th>Monto</th>
                        <th>Estado</th>
                        <th>Método Pago</th>
                        <th>Referencia</th>
                        <th>Pagado Por</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pagos as $pago)
                    <tr>
                        <td>{{ $pago->fecha_pago ? $pago->fecha_pago->format('d/m/Y H:i') : 'No pagado' }}</td>
                        <td>
                            @if($pago->comision->commissionable_type === 'App\\Models\\User')
                                <span class="badge badge-info">Vendedor</span>
                                {{ $pago->comision->commissionable->nombre ?? 'Usuario eliminado' }}
                            @elseif($pago->comision->commissionable_type === 'App\\Models\\Trabajador')
                                @php
                                    $trabajador = $pago->comision->commissionable;
                                @endphp
                                <span class="badge badge-secondary">
                                    {{ ucfirst($trabajador->tipo ?? 'Trabajador') }}
                                </span>
                                {{ $trabajador->nombre ?? 'Trabajador eliminado' }}
                            @endif
                        </td>
                        <td>
                            <span class="badge 
                                @if($pago->comision->tipo === 'venta_meta') badge-success
                                @elseif($pago->comision->tipo === 'mecanico') badge-warning  
                                @elseif($pago->comision->tipo === 'carwash') badge-info
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $pago->comision->tipo)) }}
                            </span>
                        </td>
                        <td class="text-right">${{ number_format($pago->monto, 2) }}</td>
                        <td>
                            @if($pago->estado === 'pendiente')
                                <span class="badge badge-warning">Pendiente</span>
                            @elseif($pago->estado === 'completado')
                                <span class="badge badge-success">Completado</span>
                            @elseif($pago->estado === 'anulado')
                                <span class="badge badge-danger">Anulado</span>
                            @endif
                        </td>
                        <td>{{ $pago->metodo_pago ? ucfirst($pago->metodo_pago) : '-' }}</td>
                        <td>{{ $pago->referencia_pago ?? '-' }}</td>
                        <td>{{ $pago->pagado_por ? $pago->pagadoPor->nombre : '-' }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-info btn-sm" 
                                        onclick="verDetalle({{ $pago->id }})" 
                                        title="Ver Detalle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($pago->estado === 'completado')
                                <button type="button" class="btn btn-secondary btn-sm" 
                                        onclick="imprimirRecibo({{ $pago->id }})" 
                                        title="Imprimir Recibo">
                                    <i class="fas fa-print"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">No hay pagos para mostrar</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($pagos instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="d-flex justify-content-center">
            {{ $pagos->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal de Filtros -->
<div class="modal fade" id="filtrosModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtros de Reporte</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="filtrosForm" method="GET">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fecha Desde</label>
                                <input type="date" class="form-control" name="fecha_desde" 
                                       value="{{ request('fecha_desde') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fecha Hasta</label>
                                <input type="date" class="form-control" name="fecha_hasta" 
                                       value="{{ request('fecha_hasta') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Estado</label>
                                <select class="form-control" name="estado">
                                    <option value="">Todos</option>
                                    <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="completado" {{ request('estado') === 'completado' ? 'selected' : '' }}>Completado</option>
                                    <option value="anulado" {{ request('estado') === 'anulado' ? 'selected' : '' }}>Anulado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tipo de Comisión</label>
                                <select class="form-control" name="tipo_comision">
                                    <option value="">Todos</option>
                                    <option value="venta_meta" {{ request('tipo_comision') === 'venta_meta' ? 'selected' : '' }}>Venta Meta</option>
                                    <option value="mecanico" {{ request('tipo_comision') === 'mecanico' ? 'selected' : '' }}>Mecánico</option>
                                    <option value="carwash" {{ request('tipo_comision') === 'carwash' ? 'selected' : '' }}>Carwash</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Método de Pago</label>
                                <select class="form-control" name="metodo_pago">
                                    <option value="">Todos</option>
                                    <option value="efectivo" {{ request('metodo_pago') === 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                    <option value="transferencia" {{ request('metodo_pago') === 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                    <option value="cheque" {{ request('metodo_pago') === 'cheque' ? 'selected' : '' }}>Cheque</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Trabajador</label>
                                <input type="text" class="form-control" name="trabajador" 
                                       placeholder="Nombre del trabajador" 
                                       value="{{ request('trabajador') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning" onclick="limpiarFiltros()">Limpiar</button>
                    <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // DataTable
    $('#tablaReporte').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json"
        },
        "order": [[ 0, "desc" ]],
        "pageLength": 25,
        "responsive": true
    });

    // Gráfico de tendencias
    inicializarGrafico();
});

function inicializarGrafico() {
    const ctx = document.getElementById('graficoTendencias').getContext('2d');
    
    // Datos simulados - en producción vendrían del controlador
    const datosGrafico = @json($tendenciaMensual ?? []);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: datosGrafico.map(item => item.mes),
            datasets: [{
                label: 'Pagos Mensuales',
                data: datosGrafico.map(item => item.total),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Tendencia de Pagos por Mes'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

function verDetalle(pagoId) {
    // Implementar vista de detalle del pago
    window.open(`/admin/pagos-comisiones/${pagoId}/detalle`, '_blank');
}

function imprimirRecibo(pagoId) {
    // Implementar impresión de recibo
    window.open(`/admin/pagos-comisiones/${pagoId}/recibo`, '_blank');
}

function limpiarFiltros() {
    $('#filtrosForm')[0].reset();
    window.location.href = window.location.pathname;
}

// Exportar a Excel
$('#exportarExcel').click(function() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = window.location.pathname + '?' + params.toString();
});

// Exportar a PDF
$('#exportarPDF').click(function() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'pdf');
    window.open(window.location.pathname + '?' + params.toString(), '_blank');
});
</script>
@endsection
