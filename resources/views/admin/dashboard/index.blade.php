@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="page-title mb-0">Dashboard Ejecutivo</h3>
                    <p class="text-muted mb-0">Análisis en tiempo real de Jireh Automotriz</p>
                </div>
                <div class="d-flex align-items-center">
                    <div class="badge bg-success-transparent me-3 px-3 py-2">
                        <i class="bi bi-shield-check text-success me-1"></i>
                        Sistema Activo
                    </div>
                    <span class="badge bg-primary-transparent px-3 py-2" id="reloj-dashboard"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card bg-primary-gradient">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div class="stats-content">
                            <h4 class="stats-title">Ventas Hoy</h4>
                            <div class="stats-number" id="ventas-hoy">
                                {{ $config->currency_simbol }}.{{ number_format($data['ventas']['hoy'], 2) }}
                            </div>
                            <div class="stats-change">
                                <i class="bi bi-arrow-up"></i>
                                <span id="cambio-hoy">+15.2%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card bg-success-gradient">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon">
                            <i class="bi bi-calendar-week"></i>
                        </div>
                        <div class="stats-content">
                            <h4 class="stats-title">Ventas Semana</h4>
                            <div class="stats-number">
                                {{ $config->currency_simbol }}.{{ number_format($data['ventas']['semana'], 2) }}
                            </div>
                            <div class="stats-change">
                                <i class="bi bi-arrow-{{ $data['ventas']['comparacion']['crecimiento_semanal'] >= 0 ? 'up' : 'down' }}"></i>
                                <span>{{ number_format($data['ventas']['comparacion']['crecimiento_semanal'], 1) }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card bg-warning-gradient">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div class="stats-content">
                            <h4 class="stats-title">Ticket Promedio</h4>
                            <div class="stats-number">
                                {{ $config->currency_simbol }}.{{ number_format($data['kpis']['ticket_promedio'], 2) }}
                            </div>
                            <div class="stats-change">
                                <i class="bi bi-arrow-up"></i>
                                <span>+5.8%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card bg-info-gradient">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="stats-content">
                            <h4 class="stats-title">Clientes Nuevos</h4>
                            <div class="stats-number">
                                {{ $data['kpis']['clientes_nuevos_mes'] }}
                            </div>
                            <div class="stats-change">
                                <i class="bi bi-arrow-up"></i>
                                <span>+12.3%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @if(count($data['alertas']) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-bell text-warning me-2"></i>
                        Alertas del Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alerts-container">
                        @foreach($data['alertas'] as $alerta)
                        <div class="alert alert-{{ $alerta['tipo'] }} alert-dismissible fade show" role="alert">
                            <i class="bi bi-{{ $alerta['icono'] }} me-2"></i>
                            <strong>{{ $alerta['mensaje'] }}</strong>
                            <a href="{{ $alerta['url'] }}" class="alert-link">Ver detalles</a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Gráficos y Análisis -->
    <div class="row mb-4">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tendencia de Ventas</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Últimos 12 meses
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Últimos 7 días</a></li>
                            <li><a class="dropdown-item" href="#">Últimos 30 días</a></li>
                            <li><a class="dropdown-item" href="#">Últimos 12 meses</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div id="sales-chart" style="height: 350px;"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actividad Reciente</h5>
                </div>
                <div class="card-body">
                    <div class="activity-list">
                        @foreach($data['actividad_reciente'] as $actividad)
                        <div class="activity-item">
                            <div class="activity-icon bg-{{ $actividad['tipo'] == 'venta' ? 'success' : 'primary' }}-transparent">
                                <i class="bi bi-{{ $actividad['icono'] }}"></i>
                            </div>
                            <div class="activity-content">
                                <p class="activity-text">{{ $actividad['mensaje'] }}</p>
                                <small class="activity-time">{{ $actividad['fecha']->diffForHumans() }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inventario y Productos -->
    <div class="row mb-4">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Artículos con Stock Bajo</h5>
                    <a href="{{ url('inventario') }}" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-box-seam me-1"></i>
                        Gestionar Inventario
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Stock Actual</th>
                                    <th>Stock Mínimo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['inventario']['stock_bajo'] as $articulo)
                                <tr>
                                    <td>{{ $articulo->nombre }}</td>
                                    <td>{{ $articulo->stock }}</td>
                                    <td>{{ $articulo->stock_minimo }}</td>
                                    <td>
                                        @if($articulo->stock <= 0)
                                            <span class="badge bg-danger">Sin Stock</span>
                                        @else
                                            <span class="badge bg-warning">Stock Bajo</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        No hay artículos con stock bajo
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Productos Más Vendidos</h5>
                </div>
                <div class="card-body">
                    <div class="products-list">
                        @foreach($data['inventario']['articulos_mas_vendidos'] as $articulo)
                        <div class="product-item">
                            <div class="product-info">
                                <h6 class="product-name">{{ $articulo->nombre }}</h6>
                                <p class="product-sales">{{ number_format($articulo->total_vendido, 2) }} unidades vendidas</p>
                            </div>
                            <div class="product-chart">
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: {{ ($articulo->total_vendido / $data['inventario']['articulos_mas_vendidos']->max('total_vendido')) * 100 }}%"></div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ventas Recientes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Ventas Recientes</h5>
                    <a href="{{ url('ventas') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-eye me-1"></i>
                        Ver Todas
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['ventas']['recientes'] as $venta)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                                    <td>{{ $venta->cliente->nombre ?? 'Cliente no especificado' }}</td>
                                    <td>{{ $config->currency_simbol }}.{{ number_format($venta->total_calculado, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $venta->estado ? 'bg-success' : 'bg-danger' }}">
                                            {{ $venta->estado ? 'Completada' : 'Cancelada' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ url('show-venta/'.$venta->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos personalizados -->
<style>
.stats-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.bg-primary-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.bg-success-gradient {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
}

.bg-warning-gradient {
    background: linear-gradient(135deg, #fcb045 0%, #fd1d1d 100%);
    color: white;
}

.bg-info-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stats-icon {
    font-size: 2.5rem;
    margin-right: 1rem;
    opacity: 0.8;
}

.stats-content {
    flex: 1;
}

.stats-title {
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    opacity: 0.9;
}

.stats-number {
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 0.3rem;
}

.stats-change {
    font-size: 0.85rem;
    opacity: 0.8;
}

.activity-list {
    max-height: 350px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-text {
    margin: 0;
    font-size: 0.9rem;
    color: #333;
}

.activity-time {
    color: #666;
    font-size: 0.8rem;
}

.products-list {
    max-height: 350px;
    overflow-y: auto;
}

.product-item {
    padding: 1rem 0;
    border-bottom: 1px solid #eee;
}

.product-item:last-child {
    border-bottom: none;
}

.product-name {
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.product-sales {
    font-size: 0.8rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.alerts-container .alert {
    margin-bottom: 0.5rem;
}

.alerts-container .alert:last-child {
    margin-bottom: 0;
}
</style>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reloj en tiempo real
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('reloj-dashboard').textContent = timeString;
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Gráfico de ventas
    const salesChart = new ApexCharts(document.querySelector("#sales-chart"), {
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: false
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            }
        },
        series: [{
            name: 'Ventas',
            data: @json($data['ventas']['por_mes'])
        }],
        xaxis: {
            categories: @json($data['ventas']['meses']),
            labels: {
                style: {
                    colors: '#666'
                }
            }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return '{{ $config->currency_simbol }}.' + val.toLocaleString();
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
                stops: [0, 90, 100]
            }
        },
        colors: ['#667eea'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        grid: {
            borderColor: '#e7e7e7',
            strokeDashArray: 4
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return '{{ $config->currency_simbol }}.' + val.toLocaleString();
                }
            }
        }
    });

    salesChart.render();

    // Actualizar métricas en tiempo real cada 30 segundos
    setInterval(function() {
        fetch('/api/dashboard/metricas-vivo')
            .then(response => response.json())
            .then(data => {
                document.getElementById('ventas-hoy').textContent = 
                    '{{ $config->currency_simbol }}.' + data.ventas_hoy.toLocaleString();
                
                // Actualizar porcentaje de cambio
                const cambio = data.porcentaje_cambio;
                const cambioElement = document.getElementById('cambio-hoy');
                if (cambioElement) {
                    cambioElement.textContent = (cambio >= 0 ? '+' : '') + cambio.toFixed(1) + '%';
                }
            })
            .catch(error => console.log('Error al actualizar métricas:', error));
    }, 30000);
});
</script>
@endsection
