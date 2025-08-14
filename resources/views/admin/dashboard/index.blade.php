@extends('layouts.admin')

@section('content')
<div class="content-wrapper">
    <!-- Dashboard Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="page-title mb-0">Dashboard Ejecutivo Unificado</h3>
                    <p class="text-muted mb-0">Análisis integral de Jireh Automotriz - Ventas, Comisiones, Inventario</p>
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

    <!-- Accesos Rápidos -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning-charge text-primary me-2"></i>
                        Accesos Rápidos
                    </h5>
                </div>
                <div class="card-body py-3">
                    <div class="row g-3">
                        <!-- Nueva Venta -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <a href="/add-venta" class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3 text-decoration-none">
                                <i class="bi bi-cart-plus fs-3 mb-2"></i>
                                <span class="fw-semibold">Nueva Venta</span>
                                <small class="text-muted">Registrar venta</small>
                            </a>
                        </div>

                        <!-- Gestión de Ventas -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <a href="/ventas" class="btn btn-outline-success w-100 d-flex flex-column align-items-center py-3 text-decoration-none">
                                <i class="bi bi-graph-up fs-3 mb-2"></i>
                                <span class="fw-semibold">Ventas</span>
                                <small class="text-muted">Gestionar ventas</small>
                            </a>
                        </div>

                        <!-- Inventario -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <a href="/inventario" class="btn btn-outline-warning w-100 d-flex flex-column align-items-center py-3 text-decoration-none">
                                <i class="bi bi-boxes fs-3 mb-2"></i>
                                <span class="fw-semibold">Inventario</span>
                                <small class="text-muted">Stock productos</small>
                            </a>
                        </div>

                        <!-- Comisiones -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <a href="/comisiones" class="btn btn-outline-info w-100 d-flex flex-column align-items-center py-3 text-decoration-none">
                                <i class="bi bi-currency-dollar fs-3 mb-2"></i>
                                <span class="fw-semibold">Comisiones</span>
                                <small class="text-muted">Gestión pagos</small>
                            </a>
                        </div>

                        <!-- Clientes -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <a href="/clientes" class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center py-3 text-decoration-none">
                                <i class="bi bi-people fs-3 mb-2"></i>
                                <span class="fw-semibold">Clientes</span>
                                <small class="text-muted">Base clientes</small>
                            </a>
                        </div>

                        <!-- Reportes -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <a href="/reportes/metas" class="btn btn-outline-dark w-100 d-flex flex-column align-items-center py-3 text-decoration-none">
                                <i class="bi bi-file-text fs-3 mb-2"></i>
                                <span class="fw-semibold">Reportes</span>
                                <small class="text-muted">Metas y análisis</small>
                            </a>
                        </div>
                    </div>

                    <!-- Segunda fila de accesos adicionales -->
                    <div class="row g-3 mt-2">
                        <!-- Artículos -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <a href="/articulos" class="btn btn-light w-100 d-flex flex-column align-items-center py-2 text-decoration-none">
                                <i class="bi bi-tag fs-5 mb-1"></i>
                                <span class="fw-medium">Artículos</span>
                            </a>
                        </div>

                        <!-- Trabajadores -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <a href="/trabajadores" class="btn btn-light w-100 d-flex flex-column align-items-center py-2 text-decoration-none">
                                <i class="bi bi-person-badge fs-5 mb-1"></i>
                                <span class="fw-medium">Trabajadores</span>
                            </a>
                        </div>

                        <!-- Proveedores -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <a href="/proveedores" class="btn btn-light w-100 d-flex flex-column align-items-center py-2 text-decoration-none">
                                <i class="bi bi-building fs-5 mb-1"></i>
                                <span class="fw-medium">Proveedores</span>
                            </a>
                        </div>

                        <!-- Auditoría -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <a href="/admin/auditoria" class="btn btn-light w-100 d-flex flex-column align-items-center py-2 text-decoration-none">
                                <i class="bi bi-shield-check fs-5 mb-1"></i>
                                <span class="fw-medium">Auditoría</span>
                            </a>
                        </div>

                        <!-- Lotes de Pago -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <a href="/lotes-pago" class="btn btn-light w-100 d-flex flex-column align-items-center py-2 text-decoration-none">
                                <i class="bi bi-cash-stack fs-5 mb-1"></i>
                                <span class="fw-medium">Lotes Pago</span>
                            </a>
                        </div>

                        <!-- Configuración -->
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                            <a href="/config" class="btn btn-light w-100 d-flex flex-column align-items-center py-2 text-decoration-none">
                                <i class="bi bi-gear fs-5 mb-1"></i>
                                <span class="fw-medium">Configuración</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas del Sistema -->
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
                    @if(count($data['alertas']) > 0)
                        <div class="row">
                            @foreach($data['alertas'] as $alerta)
                            <div class="col-md-6 mb-2">
                                <div class="alert alert-{{ $alerta['tipo'] }} alert-dismissible fade show py-2 mb-2" role="alert">
                                    <small>
                                        <i class="bi bi-{{ $alerta['icono'] }} me-1"></i>
                                        <strong>{{ $alerta['mensaje'] }}</strong>
                                        <a href="{{ $alerta['accion'] }}" class="alert-link ms-1">Ver</a>
                                    </small>
                                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" style="font-size: 0.7rem;"></button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-check-circle text-success fs-2"></i>
                            <p class="text-muted mt-2 mb-0">No hay alertas del sistema en este momento</p>
                            <small class="text-muted">Todo funcionando correctamente</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Cards Unificados -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card stats-card bg-primary-gradient">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div class="stats-content">
                            <h4 class="stats-title">Ventas del Mes</h4>
                            <div class="stats-number" id="ventas-mes">
                                {{ $config->currency_simbol }} {{ number_format($data['kpis']['ventas_mes'], 2) }}
                            </div>
                            <div class="stats-change">
                                <i class="bi bi-arrow-up"></i>
                                <span id="cambio-ventas">Este mes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card stats-card bg-warning-gradient">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <div class="stats-content">
                            <h4 class="stats-title">Comisiones Pendientes</h4>
                            <div class="stats-number" id="comisiones-pendientes">
                                {{ $config->currency_simbol }} {{ number_format($data['kpis']['comisiones_pendientes'], 2) }}
                            </div>
                            <div class="stats-change">
                                <i class="bi bi-exclamation-circle"></i>
                                <span>{{ $data['comisiones']['pendientes']['cantidad'] }} pendientes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card stats-card bg-success-gradient">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon">
                            <i class="bi bi-percent"></i>
                        </div>
                        <div class="stats-content">
                            <h4 class="stats-title">Efect. Cobranza</h4>
                            <div class="stats-number" id="efectividad-cobranza">
                                {{ number_format($data['kpis']['efectividad_cobranza'], 1) }}%
                            </div>
                            <div class="stats-change">
                                <i class="bi bi-graph-up"></i>
                                <span>Rendimiento</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card stats-card bg-warning-gradient">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div class="stats-content">
                            <h4 class="stats-title">Stock Crítico</h4>
                            <div class="stats-number" id="stock-critico">
                                {{ $data['stock']['critico'] }}
                            </div>
                            <div class="stats-change">
                                <i class="bi bi-exclamation-triangle"></i>
                                <span>Artículos</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-4 col-md-6">
            <div class="card stats-card bg-info-gradient">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon">
                            <i class="bi bi-trophy"></i>
                        </div>
                        <div class="stats-content">
                            <h4 class="stats-title">Metas Alcanzadas</h4>
                            <div class="stats-number" id="metas-alcanzadas">
                                {{ $data['metas']['alcanzadas'] }}%
                            </div>
                            <div class="stats-change">
                                <i class="bi bi-target"></i>
                                <span>Este mes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen Financiero Unificado -->
    <div class="row mb-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Resumen Financiero Unificado
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="financial-metric">
                                <h6 class="text-muted">Ingresos Totales</h6>
                                <h4 class="text-success">{{ $config->currency_simbol }} {{ number_format($data['resumen_financiero']['ingresos_totales'], 2) }}</h4>
                                <small class="text-muted">Este mes</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="financial-metric">
                                <h6 class="text-muted">Comisiones Pagadas</h6>
                                <h4 class="text-primary">{{ $config->currency_simbol }} {{ number_format($data['resumen_financiero']['comisiones_pagadas'], 2) }}</h4>
                                <small class="text-muted">{{ $data['comisiones']['pagadas']['cantidad'] }} pagos</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="financial-metric">
                                <h6 class="text-muted">Pendientes por Pagar</h6>
                                <h4 class="text-warning">{{ $config->currency_simbol }} {{ number_format($data['resumen_financiero']['pendientes_pago'], 2) }}</h4>
                                <small class="text-muted">{{ $data['comisiones']['pendientes']['cantidad'] }} pendientes</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Distribución de Comisiones</h6>
                            <div class="progress-stack">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Pagadas</span>
                                    <span>{{ number_format($data['resumen_financiero']['porcentaje_pagadas'], 1) }}%</span>
                                </div>
                                <div class="progress mb-2">
                                    <div class="progress-bar bg-success" style="width: {{ $data['resumen_financiero']['porcentaje_pagadas'] }}%"></div>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Pendientes</span>
                                    <span>{{ number_format($data['resumen_financiero']['porcentaje_pendientes'], 1) }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" style="width: {{ $data['resumen_financiero']['porcentaje_pendientes'] }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Métricas de Rendimiento</h6>
                            <div class="metrics-list">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>ROI Comisiones</span>
                                    <span class="badge bg-info">{{ number_format($data['resumen_financiero']['roi_comisiones'], 1) }}%</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Eficiencia Pago</span>
                                    <span class="badge bg-success">{{ number_format($data['kpis']['efectividad_cobranza'], 1) }}%</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Tiempo Prom. Pago</span>
                                    <span class="badge bg-secondary">{{ $data['resumen_financiero']['tiempo_promedio_pago'] }} días</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-cash-coin me-2"></i>
                        Estado de Comisiones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="comisiones-overview">
                        <div class="commission-stat mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Total Generadas</span>
                                <span class="badge bg-primary-transparent">{{ $data['comisiones']['total']['cantidad'] }}</span>
                            </div>
                            <h5 class="mt-1">{{ $config->currency_simbol }} {{ number_format($data['comisiones']['total']['monto'], 2) }}</h5>
                        </div>
                        
                        <div class="commission-stat mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-success">Pagadas</span>
                                <span class="badge bg-success-transparent">{{ $data['comisiones']['pagadas']['cantidad'] }}</span>
                            </div>
                            <h6 class="mt-1 text-success">{{ $config->currency_simbol }} {{ number_format($data['comisiones']['pagadas']['monto'], 2) }}</h6>
                        </div>
                        
                        <div class="commission-stat mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-warning">Pendientes</span>
                                <span class="badge bg-warning-transparent">{{ $data['comisiones']['pendientes']['cantidad'] }}</span>
                            </div>
                            <h6 class="mt-1 text-warning">{{ $config->currency_simbol }} {{ number_format($data['comisiones']['pendientes']['monto'], 2) }}</h6>
                        </div>

                        <hr>

                        <div class="quick-actions">
                            <h6 class="text-muted mb-3">Acciones Rápidas</h6>
                            <div class="d-grid gap-2">
                                <a href="{{ route('comisiones.index') }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i>
                                    Ver Todas las Comisiones
                                </a>
                                <a href="{{ route('comisiones.index', ['estado' => 'pendiente']) }}" class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-clock me-1"></i>
                                    Revisar Pendientes
                                </a>
                                <a href="{{ route('lotes-pago.index') }}" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-credit-card me-1"></i>
                                    Gestionar Lotes de Pago
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <!-- Gráficos y Análisis -->
    <div class="row mb-4">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-graph-up text-primary me-2"></i>
                        Tendencia de Ventas - Últimos 12 meses
                    </h5>
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
    font-size: 2rem;
    margin-right: 0.75rem;
    opacity: 0.8;
}

@media (min-width: 1200px) {
    .stats-icon {
        font-size: 2.2rem;
        margin-right: 1rem;
    }
}

.stats-content {
    flex: 1;
    min-width: 0; /* Permite que el texto se ajuste */
}

.stats-title {
    font-size: 0.8rem;
    margin-bottom: 0.4rem;
    opacity: 0.9;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

@media (min-width: 1200px) {
    .stats-title {
        font-size: 0.85rem;
    }
}

.stats-number {
    font-size: 1.4rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
    line-height: 1.2;
}

@media (min-width: 1200px) {
    .stats-number {
        font-size: 1.6rem;
    }
}

.stats-change {
    font-size: 0.75rem;
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

/* Estilos para Dashboard Unificado */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #4fc3f7 0%, #29b6f6 100%);
}

.financial-metric {
    text-align: center;
    padding: 1rem;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.05);
    margin-bottom: 1rem;
}

.financial-metric h6 {
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.financial-metric h4 {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.progress-stack .progress {
    height: 8px;
    border-radius: 4px;
}

.metrics-list .badge {
    font-size: 0.85rem;
    padding: 0.5rem 0.75rem;
}

.commission-stat {
    padding: 1rem;
    border-radius: 8px;
    background: rgba(0, 0, 0, 0.02);
    border-left: 4px solid #007bff;
}

.commission-stat.success {
    border-left-color: #28a745;
}

.commission-stat.warning {
    border-left-color: #ffc107;
}

.comisiones-overview {
    max-height: 500px;
    overflow-y: auto;
}

.quick-actions .btn {
    border-radius: 6px;
    font-size: 0.85rem;
    padding: 0.5rem 1rem;
}

.badge.bg-primary-transparent {
    background-color: rgba(0, 123, 255, 0.1) !important;
    color: #007bff;
}

.badge.bg-success-transparent {
    background-color: rgba(40, 167, 69, 0.1) !important;
    color: #28a745;
}

.badge.bg-warning-transparent {
    background-color: rgba(255, 193, 7, 0.1) !important;
    color: #ffc107;
}

/* Animaciones suaves */
.financial-metric:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.commission-stat:hover {
    transform: translateX(5px);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

/* Estilos para Accesos Rápidos */
.btn-outline-primary:hover,
.btn-outline-success:hover,
.btn-outline-warning:hover,
.btn-outline-info:hover,
.btn-outline-secondary:hover,
.btn-outline-dark:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.btn-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    background-color: #f8f9fa;
    border-color: #dee2e6;
    transition: all 0.3s ease;
}

/* Iconos de accesos rápidos */
.btn i.fs-3 {
    transition: all 0.3s ease;
}

.btn:hover i.fs-3 {
    transform: scale(1.1);
}

.btn i.fs-5 {
    transition: all 0.3s ease;
}

.btn:hover i.fs-5 {
    transform: scale(1.05);
}

/* Responsive para accesos rápidos */
@media (max-width: 768px) {
    .btn-outline-primary,
    .btn-outline-success,
    .btn-outline-warning,
    .btn-outline-info,
    .btn-outline-secondary,
    .btn-outline-dark {
        padding: 1rem 0.5rem;
    }
    
    .btn-light {
        padding: 0.75rem 0.5rem;
    }
    
    .btn i.fs-3 {
        font-size: 1.5rem !important;
    }
    
    .btn i.fs-5 {
        font-size: 1rem !important;
    }
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

    // Función para formatear números como en PHP number_format()
    function formatCurrency(amount) {
        // Convertir a número si es string
        const num = parseFloat(amount);
        
        // Formatear con 2 decimales, coma como separador de miles
        return num.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Función para formatear porcentajes
    function formatPercentage(value) {
        return parseFloat(value).toFixed(1);
    }

    // Actualizar métricas en tiempo real cada 30 segundos
    setInterval(function() {
        fetch('/api/dashboard/metricas-vivo')
            .then(response => response.json())
            .then(data => {
                // Actualizar ventas del mes
                if (document.getElementById('ventas-mes')) {
                    document.getElementById('ventas-mes').textContent = 
                        '{{ $config->currency_simbol }} ' + formatCurrency(data.ventas_mes);
                }
                
                // Actualizar comisiones pendientes
                if (document.getElementById('comisiones-pendientes')) {
                    document.getElementById('comisiones-pendientes').textContent = 
                        '{{ $config->currency_simbol }} ' + formatCurrency(data.comisiones_pendientes);
                }
                
                // Actualizar efectividad de cobranza
                if (document.getElementById('efectividad-cobranza')) {
                    document.getElementById('efectividad-cobranza').textContent = 
                        formatPercentage(data.efectividad_cobranza) + '%';
                }
                
                // Actualizar stock crítico
                if (document.getElementById('stock-critico')) {
                    document.getElementById('stock-critico').textContent = data.stock_critico;
                }
                
                // Actualizar metas alcanzadas
                if (document.getElementById('metas-alcanzadas')) {
                    document.getElementById('metas-alcanzadas').textContent = 
                        data.metas_alcanzadas + '%';
                }
            })
            .catch(error => console.log('Error al actualizar métricas:', error));
    }, 30000);

    // Animaciones de entrada para las tarjetas
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Aplicar animaciones a las tarjetas
    document.querySelectorAll('.stats-card, .card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });

    // Actualizar alertas dinámicamente
    function checkAlerts() {
        fetch('/api/dashboard/alertas')
            .then(response => response.json())
            .then(data => {
                const alertsContainer = document.querySelector('.alerts-container');
                if (alertsContainer && data.alertas && data.alertas.length > 0) {
                    let alertsHtml = '';
                    data.alertas.forEach(alerta => {
                        alertsHtml += `
                            <div class="alert alert-${alerta.tipo} alert-dismissible fade show" role="alert">
                                <i class="bi bi-${alerta.icono} me-2"></i>
                                <strong>${alerta.modulo}:</strong> ${alerta.mensaje}
                                <a href="${alerta.accion}" class="btn btn-sm btn-outline-${alerta.tipo} ms-2">Ver</a>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                    });
                    alertsContainer.innerHTML = alertsHtml;
                }
            })
            .catch(error => console.log('Error al verificar alertas:', error));
    }

    // Verificar alertas cada 2 minutos
    setInterval(checkAlerts, 120000);
});
</script>
@endsection
