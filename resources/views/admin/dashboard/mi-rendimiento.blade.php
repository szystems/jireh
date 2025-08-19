@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-trophy text-warning"></i>
            Mi Rendimiento Personal
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.pro') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Mi Rendimiento</li>
            </ol>
        </nav>
    </div>

    <!-- Métricas Principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-info">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-day fs-1 text-info mb-2"></i>
                    <h5 class="card-title">Ventas Hoy</h5>
                    <h3 class="text-info">{{ $config->currency_simbol }}{{ number_format($misVentasHoy, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-month fs-1 text-primary mb-2"></i>
                    <h5 class="card-title">Ventas Este Mes</h5>
                    <h3 class="text-primary">{{ $config->currency_simbol }}{{ number_format($misVentasMes, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-range fs-1 text-success mb-2"></i>
                    <h5 class="card-title">Ventas Este Año</h5>
                    <h3 class="text-success">{{ $config->currency_simbol }}{{ number_format($misVentasAño, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <i class="bi bi-currency-dollar fs-1 text-warning mb-2"></i>
                    <h5 class="card-title">Mis Comisiones</h5>
                    <h3 class="text-warning">{{ $config->currency_simbol }}{{ number_format($misComisionesTotales, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Progreso de Meta y Comisiones -->
    <div class="row mb-4">
        <div class="col-md-8">
            <!-- Progreso de Meta -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-bullseye text-primary"></i>
                        Progreso de Meta del Mes
                    </h5>
                </div>
                <div class="card-body">
                    @if($siguienteMeta)
                        <h6>Meta Objetivo: {{ $config->currency_simbol }}{{ number_format($siguienteMeta->monto_minimo, 2) }}</h6>
                        <div class="progress mb-3" style="height: 25px;">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: {{ min(100, $progresoMeta) }}%" 
                                 aria-valuenow="{{ $progresoMeta }}" aria-valuemin="0" aria-valuemax="100">
                                {{ number_format($progresoMeta, 1) }}%
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <small class="text-muted">Vendido</small>
                                <div class="h5 text-success">{{ $config->currency_simbol }}{{ number_format($misVentasMes, 2) }}</div>
                            </div>
                            <div class="col-md-4 text-center">
                                <small class="text-muted">Falta</small>
                                <div class="h5 text-warning">{{ $config->currency_simbol }}{{ number_format($siguienteMeta->monto_minimo - $misVentasMes, 2) }}</div>
                            </div>
                            <div class="col-md-4 text-center">
                                <small class="text-muted">Comisión Esperada</small>
                                <div class="h5 text-info">{{ $config->currency_simbol }}{{ number_format($siguienteMeta->comision_porcentaje * $siguienteMeta->monto_minimo / 100, 2) }}</div>
                            </div>
                        </div>
                    @elseif($metaAlcanzada)
                        <div class="alert alert-success">
                            <i class="bi bi-trophy"></i>
                            <strong>¡Felicitaciones!</strong> Has alcanzado la meta de <strong>{{ $config->currency_simbol }}{{ number_format($metaAlcanzada->monto_minimo, 2) }}</strong>
                            <br>Comisión ganada: <strong>{{ $config->currency_simbol }}{{ number_format($metaAlcanzada->comision_porcentaje * $misVentasMes / 100, 2) }}</strong>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            No hay metas activas configuradas en el sistema.
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Estado de Comisiones -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-wallet text-success"></i>
                        Estado de Comisiones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Total Ganado:</span>
                            <strong class="text-success">{{ $config->currency_simbol }}{{ number_format($misComisionesTotales, 2) }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Pendiente de Pago:</span>
                            <strong class="text-warning">{{ $config->currency_simbol }}{{ number_format($misComisionesPendientes, 2) }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Ya Pagado:</span>
                            <strong class="text-info">{{ $config->currency_simbol }}{{ number_format($misComisionesTotales - $misComisionesPendientes, 2) }}</strong>
                        </div>
                    </div>
                    <hr>
                    <a href="{{ route('vendedor.mis_comisiones') }}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-eye"></i> Ver Mis Comisiones
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Ventas por Mes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up text-primary"></i>
                        Mi Evolución de Ventas (Últimos 12 Meses)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="ventasChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Mis Clientes Más Frecuentes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-people text-success"></i>
                        Mis Clientes Más Frecuentes
                    </h5>
                </div>
                <div class="card-body">
                    @if($misClientesFrecuentes->count() > 0)
                        <div class="row">
                            @foreach($misClientesFrecuentes->take(5) as $cliente)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="bi bi-person-circle fs-1 text-primary mb-2"></i>
                                        <h6 class="card-title">{{ $cliente->nombre }}</h6>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                {{ $cliente->telefono ?? 'Sin teléfono' }}
                                                <br>
                                                <span class="badge bg-primary">{{ $cliente->ventas_count }} ventas</span>
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-person-x display-4 text-muted"></i>
                            <h5 class="text-muted mt-3">Sin clientes registrados</h5>
                            <p class="text-muted">Aún no tienes clientes con ventas registradas.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de Ventas por Mes
const ctx = document.getElementById('ventasChart').getContext('2d');
const ventasChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($mesesLabels),
        datasets: [{
            label: 'Mis Ventas ($)',
            data: @json($ventasPorMes),
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
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
                        return '{{ $config->currency_simbol }}' + new Intl.NumberFormat().format(value);
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Ventas: {{ $config->currency_simbol }}' + new Intl.NumberFormat().format(context.parsed.y);
                    }
                }
            },
            legend: {
                display: true,
                position: 'top'
            }
        }
    }
});
</script>
@endsection
