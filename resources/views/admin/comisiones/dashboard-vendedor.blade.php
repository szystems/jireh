@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-currency-dollar text-success"></i>
            Mis Comisiones
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.pro') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Mis Comisiones</li>
            </ol>
        </nav>
    </div>

    <!-- Métricas Personales -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-1">{{ $config->currency_simbol }}{{ number_format($totalComisiones, 2) }}</h4>
                            <p class="mb-0">Total Comisiones</p>
                        </div>
                        <i class="bi bi-wallet2 fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-1">{{ $config->currency_simbol }}{{ number_format($comisionesPendientes, 2) }}</h4>
                            <p class="mb-0">Pendientes</p>
                        </div>
                        <i class="bi bi-clock-history fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-1">{{ $config->currency_simbol }}{{ number_format($comisionesPagadas, 2) }}</h4>
                            <p class="mb-0">Pagadas</p>
                        </div>
                        <i class="bi bi-check-circle fs-2"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h4 class="mb-1">{{ $comisiones->total() }}</h4>
                            <p class="mb-0">Total Registros</p>
                        </div>
                        <i class="bi bi-list-ol fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Comisiones por Mes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up"></i>
                        Mis Comisiones - Últimos 6 Meses
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="comisionesChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Comisiones -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="bi bi-table"></i>
                Historial de Mis Comisiones
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Venta</th>
                            <th>Cliente</th>
                            <th>Tipo</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comisiones as $comision)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($comision->fecha_calculo)->format('d/m/Y') }}</td>
                            <td>
                                @if($comision->venta)
                                    #{{ $comision->venta->numero_factura ?? $comision->venta->id }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($comision->venta && $comision->venta->cliente)
                                    {{ $comision->venta->cliente->nombre }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $comision->tipo == 'venta' ? 'primary' : 'success' }}">
                                    {{ ucfirst($comision->tipo ?? 'Venta') }}
                                </span>
                            </td>
                            <td class="fw-bold text-success">
                                {{ $config->currency_simbol }}{{ number_format($comision->monto, 2) }}
                            </td>
                            <td>
                                @if($comision->estado == 'pendiente')
                                    <span class="badge bg-warning">Pendiente</span>
                                @elseif($comision->estado == 'pagada')
                                    <span class="badge bg-success">Pagada</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($comision->estado) }}</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-info" 
                                        onclick="verDetalles({{ $comision->id }})"
                                        data-bs-toggle="tooltip" 
                                        title="Ver detalles">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No tienes comisiones registradas aún
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($comisiones->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $comisiones->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de comisiones por mes
const ctx = document.getElementById('comisionesChart').getContext('2d');
const comisionesData = @json($comisionesPorMes);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: comisionesData.map(item => item.mes),
        datasets: [{
            label: 'Comisiones ($)',
            data: comisionesData.map(item => item.monto),
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '{{ $config->currency_simbol }}' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Comisiones: {{ $config->currency_simbol }}' + context.parsed.y.toLocaleString();
                    }
                }
            }
        }
    }
});

function verDetalles(comisionId) {
    // Implementar modal o redirección para ver detalles
    alert('Función de detalles en desarrollo para comisión ID: ' + comisionId);
}

// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
@endsection
