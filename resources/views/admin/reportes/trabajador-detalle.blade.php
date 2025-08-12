@extends('layouts.admin')

@section('title', 'Detalle del Trabajador - ' . $trabajador->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('reportes.metas.index', ['periodo' => $periodo]) }}" 
                           class="btn btn-outline-secondary btn-sm me-3">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                        <div>
                            @if($trabajador->fotografia)
                                <img src="{{ asset('assets/imgs/users/'.$trabajador->fotografia) }}" 
                                     class="rounded-circle me-3" width="48" height="48">
                            @else
                                <img src="{{ asset('assets/imgs/users/usericon4.png') }}" 
                                     class="rounded-circle me-3" width="48" height="48">
                            @endif
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $trabajador->name }}</h4>
                            <small class="text-muted">
                                {{ $trabajador->email }} | Período: {{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas Generales -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1">{{ $config->currency_simbol }}{{ number_format($totalVendidoPeriodo, 2) }}</h3>
                            <p class="mb-0">Total Vendido (Período)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1">{{ $cantidadVentas }}</h3>
                            <p class="mb-0">Ventas Realizadas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3 class="mb-1">{{ $config->currency_simbol }}{{ number_format($totalVendidoPeriodo / max(1, $cantidadVentas), 2) }}</h3>
                            <p class="mb-0">Promedio por Venta</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            @php
                                $diasTranscurridos = $fechaInicio->diffInDays(\Carbon\Carbon::now()) + 1;
                                $promedioDiario = $diasTranscurridos > 0 ? $totalVendidoPeriodo / $diasTranscurridos : 0;
                            @endphp
                            <h3 class="mb-1">{{ $config->currency_simbol }}{{ number_format($promedioDiario, 2) }}</h3>
                            <p class="mb-0">Promedio Diario</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progreso en Metas -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bullseye"></i> Progreso en Metas (Por Tipo Específico)</h5>
                    <small class="text-muted">Cada meta se evalúa según su período específico (mensual, semestral, anual)</small>
                </div>
                <div class="card-body">
                    @if($metasConProgreso->count() > 0)
                        @foreach($metasConProgreso as $metaData)
                        @php
                            $meta = $metaData['meta'];
                            $alcanzada = $metaData['alcanzada'];
                            $porcentaje = $metaData['porcentaje'];
                            $ventasParaMeta = $metaData['ventas_para_meta'];
                            $color = $metaData['color'];
                            $faltante = $metaData['faltante'];
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold">
                                    <i class="bi bi-award text-{{ $color }}"></i>
                                    {{ $meta->nombre }}
                                </span>
                                <span class="text-muted">
                                    {{ $config->currency_simbol }}{{ number_format($ventasParaMeta, 2) }} / {{ $config->currency_simbol }}{{ number_format($meta->monto_minimo, 2) }}
                                </span>
                            </div>
                            <div class="progress mb-1" style="height: 10px;">
                                <div class="progress-bar bg-{{ $alcanzada ? 'success' : $color }}" 
                                     style="width: {{ $porcentaje }}%"></div>
                            </div>
                            <small class="text-muted">
                                {{ number_format($porcentaje, 1) }}% 
                                @if($alcanzada)
                                    <span class="text-success"><i class="bi bi-check-circle"></i> ¡Alcanzada!</span>
                                    <span class="text-info">(Comisión: {{ $meta->porcentaje_comision }}%)</span>
                                @else
                                    <span class="text-info">Faltan {{ $config->currency_simbol }}{{ number_format($faltante, 2) }}</span>
                                @endif
                            </small>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">No hay metas configuradas.</p>
                    @endif
                </div>
            </div>

            <!-- Gráfico de Ventas por Día -->
            @if($estadisticasDiarias->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Evolución de Ventas {{ date('Y') }}</h5>
                    <small class="text-muted">Gráfica completa del año - Días sin ventas aparecen en cero para mostrar el contexto completo</small>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 400px; width: 100%;">
                        <canvas id="ventasDiariasChart"></canvas>
                    </div>
                </div>
            </div>
            @endif

            <!-- Lista de Ventas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Detalle de Ventas ({{ $cantidadVentas }})</h5>
                </div>
                <div class="card-body">
                    @if($ventas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Productos</th>
                                        <th>Total</th>
                                        <th>Venta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ventas as $venta)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</span>
                                            <br>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($venta->fecha)->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $venta->cliente->nombre ?? 'Cliente no encontrado' }}</strong>
                                                @if($venta->cliente)
                                                    <br>
                                                    <small class="text-muted">{{ $venta->cliente->celular ?: $venta->cliente->telefono }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if($venta->detalleVentas->count() > 0)
                                                @foreach($venta->detalleVentas->take(2) as $detalle)
                                                    <small class="d-block">
                                                        {{ $detalle->cantidad }}x {{ $detalle->articulo->nombre ?? 'Producto eliminado' }}
                                                    </small>
                                                @endforeach
                                                @if($venta->detalleVentas->count() > 2)
                                                    <small class="text-muted">+{{ $venta->detalleVentas->count() - 2 }} más...</small>
                                                @endif
                                            @else
                                                <small class="text-muted">Sin productos</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">
                                                {{ $config->currency_simbol }}{{ number_format($venta->detalleVentas->sum('sub_total'), 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('ventas.show', $venta->id) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               target="_blank">
                                                #{{ $venta->id }}
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <h5 class="text-muted mt-3">No hay ventas en este período</h5>
                            <p class="text-muted">El trabajador no ha realizado ventas en el período seleccionado.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($estadisticasDiarias->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('ventasDiariasChart').getContext('2d');
    
    const labels = @json($estadisticasDiarias->keys());
    const data = @json($estadisticasDiarias->pluck('total'));
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.map((date, index) => {
                const d = new Date(date);
                const day = d.getDate();
                const month = d.getMonth() + 1;
                
                // Mostrar etiquetas cada 15 días aproximadamente para evitar saturación
                if (index % 15 === 0 || day === 1) {
                    return day + '/' + month;
                }
                return '';
            }),
            datasets: [{
                label: 'Ventas Diarias {{ date("Y") }} ({{ $config->currency_simbol }})',
                data: data,
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                pointBackgroundColor: 'rgb(54, 162, 235)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                tension: 0.4,
                fill: true
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
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    },
                    ticks: {
                        maxTicksLimit: 25,
                        autoSkip: true,
                        maxRotation: 45
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Total: {{ $config->currency_simbol }}' + context.parsed.y.toLocaleString();
                        }
                    }
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            elements: {
                point: {
                    radius: 2,
                    hoverRadius: 4,
                    hitRadius: 6
                },
                line: {
                    borderWidth: 2
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
});
</script>
@endif

<style>
.progress {
    background-color: #e9ecef;
}

/* Estilos para la gráfica */
#ventasDiariasChart {
    max-width: 100%;
    height: 400px !important;
}

.card-body canvas {
    border-radius: 8px;
}

/* Mejorar la visualización en dispositivos móviles */
@media (max-width: 768px) {
    #ventasDiariasChart {
        height: 300px !important;
    }
}
</style>
@endsection
