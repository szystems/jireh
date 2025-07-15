@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <h3 class="page-title">Sistema de Alertas y Monitoreo</h3>
                <p class="text-muted">Dashboard de monitoreo en tiempo real</p>
            </div>
        </div>
    </div>

    <!-- Estado del Sistema -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="status-indicator me-3">
                                    <div class="status-light status-success"></div>
                                </div>
                                <div>
                                    <h5 class="mb-0">Sistema Operativo</h5>
                                    <p class="text-muted mb-0">Todos los servicios funcionando correctamente</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="uptime-badge">
                                <i class="bi bi-clock me-1"></i>
                                <span id="uptime">99.9% Uptime</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6">
            <div class="metric-card">
                <div class="metric-icon bg-primary-transparent">
                    <i class="bi bi-speedometer2 text-primary"></i>
                </div>
                <div class="metric-content">
                    <h6 class="metric-title">Rendimiento</h6>
                    <div class="metric-value">98%</div>
                    <div class="metric-change positive">
                        <i class="bi bi-arrow-up"></i>
                        <span>+2.1%</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="metric-card">
                <div class="metric-icon bg-success-transparent">
                    <i class="bi bi-database text-success"></i>
                </div>
                <div class="metric-content">
                    <h6 class="metric-title">Base de Datos</h6>
                    <div class="metric-value">Normal</div>
                    <div class="metric-change positive">
                        <i class="bi bi-check-circle"></i>
                        <span>Estable</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="metric-card">
                <div class="metric-icon bg-warning-transparent">
                    <i class="bi bi-exclamation-triangle text-warning"></i>
                </div>
                <div class="metric-content">
                    <h6 class="metric-title">Alertas Activas</h6>
                    <div class="metric-value" id="alertas-activas">3</div>
                    <div class="metric-change">
                        <i class="bi bi-bell"></i>
                        <span>Requiere atención</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6">
            <div class="metric-card">
                <div class="metric-icon bg-info-transparent">
                    <i class="bi bi-activity text-info"></i>
                </div>
                <div class="metric-content">
                    <h6 class="metric-title">Transacciones</h6>
                    <div class="metric-value" id="transacciones">247</div>
                    <div class="metric-change positive">
                        <i class="bi bi-arrow-up"></i>
                        <span>+15%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Monitoreo -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Monitoreo en Tiempo Real</h5>
                </div>
                <div class="card-body">
                    <div id="monitoring-chart" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acceso Rápido -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Acciones Rápidas</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ url('/dashboard-pro') }}" class="btn btn-primary">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Dashboard Ejecutivo
                        </a>
                        <a href="{{ url('/notificaciones') }}" class="btn btn-warning">
                            <i class="bi bi-bell me-2"></i>
                            Gestionar Notificaciones
                        </a>
                        <a href="{{ url('/admin/auditoria') }}" class="btn btn-info">
                            <i class="bi bi-shield-check me-2"></i>
                            Sistema de Auditoría
                        </a>
                        <a href="{{ url('/admin/prevencion/dashboard') }}" class="btn btn-success">
                            <i class="bi bi-shield-exclamation me-2"></i>
                            Prevención de Inconsistencias
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Estadísticas del Sistema</h5>
                </div>
                <div class="card-body">
                    <div class="stat-item">
                        <div class="stat-label">Ventas procesadas hoy</div>
                        <div class="stat-value">23</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Integridad de datos</div>
                        <div class="stat-value">100%</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Última verificación</div>
                        <div class="stat-value">Hace 2 min</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Backups realizados</div>
                        <div class="stat-value">Diario</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.status-indicator {
    position: relative;
}

.status-light {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    position: relative;
    animation: pulse 2s infinite;
}

.status-success {
    background-color: #28a745;
}

.status-warning {
    background-color: #ffc107;
}

.status-danger {
    background-color: #dc3545;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
    }
}

.uptime-badge {
    background: #e8f5e8;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.9rem;
    color: #155724;
}

.metric-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.metric-card:hover {
    transform: translateY(-5px);
}

.metric-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.metric-title {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.metric-value {
    font-size: 2rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 0.5rem;
}

.metric-change {
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.metric-change.positive {
    color: #28a745;
}

.metric-change.negative {
    color: #dc3545;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #eee;
}

.stat-item:last-child {
    border-bottom: none;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

.stat-value {
    font-weight: bold;
    color: #333;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de monitoreo en tiempo real
    const monitoringChart = new ApexCharts(document.querySelector("#monitoring-chart"), {
        chart: {
            type: 'area',
            height: 300,
            animations: {
                enabled: true,
                easing: 'linear',
                dynamicAnimation: {
                    speed: 1000
                }
            },
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            }
        },
        series: [{
            name: 'Rendimiento',
            data: generateRandomData()
        }],
        xaxis: {
            type: 'datetime',
            range: 35000
        },
        yaxis: {
            max: 100,
            min: 0,
            labels: {
                formatter: function(val) {
                    return val.toFixed(0) + '%';
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
        colors: ['#00E396'],
        stroke: {
            width: 2
        },
        markers: {
            size: 0
        },
        legend: {
            show: false
        }
    });

    monitoringChart.render();

    // Simular datos en tiempo real
    function generateRandomData() {
        const data = [];
        const now = Date.now();
        for (let i = 0; i < 20; i++) {
            data.push({
                x: now - (19 - i) * 1000,
                y: Math.floor(Math.random() * 20) + 80
            });
        }
        return data;
    }

    // Actualizar gráfico cada 3 segundos
    setInterval(function() {
        monitoringChart.updateSeries([{
            data: generateRandomData()
        }]);
    }, 3000);
});
</script>
@endsection
