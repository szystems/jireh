@extends('layouts.admin')

@section('title', 'Dashboard de Comisiones')

@section('content')
<div class="container-fluid">
    <!-- Título y accesos rápidos -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Dashboard de Comisiones</h1>
            <p class="text-muted mb-0">Resumen general del sistema de comisiones</p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('comisiones.gestion') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-list-check"></i> Gestión
            </a>
            <a href="{{ route('lotes-pago.index') }}" class="btn btn-info btn-sm">
                <i class="bi bi-collection"></i> Lotes
            </a>
            <a href="{{ route('metas-ventas.index') }}" class="btn btn-success btn-sm">
                <i class="bi bi-bullseye"></i> Metas
            </a>
            <a href="{{ url('reporte-metas') }}" class="btn btn-warning btn-sm">
                <i class="bi bi-graph-up"></i> Reportes
            </a>
        </div>
    </div>

    <!-- Alertas y notificaciones -->
    @if(count($datos['alertas']) > 0)
    <div class="row mb-4">
        <div class="col-md-12">
            @foreach($datos['alertas'] as $alerta)
            <div class="alert alert-{{ $alerta['tipo'] }} alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Atención:</strong> {{ $alerta['mensaje'] }}
                <a href="{{ $alerta['accion'] }}" class="btn btn-sm btn-outline-{{ $alerta['tipo'] }} ms-2">Ver</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Tarjetas de estadísticas principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Comisiones Pendientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $config->currency_simbol }} {{ number_format($datos['estadisticas']['comisiones_pendientes'], 2) }}
                            </div>
                            <div class="text-xs text-muted">{{ $datos['estadisticas']['cantidad_pendientes'] }} comisiones</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock-history fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pagadas Este Mes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $config->currency_simbol }} {{ number_format($datos['estadisticas']['comisiones_pagadas_mes'], 2) }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $datos['estadisticas']['cantidad_pagadas_mes'] }} comisiones
                                <br><small>{{ date('F Y') }}</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
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
                                Lotes Recientes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $datos['estadisticas']['lotes_pendientes'] }}
                            </div>
                            <div class="text-xs text-muted">
                                {{ $config->currency_simbol }} {{ number_format($datos['estadisticas']['monto_lotes_pendientes'], 2) }}
                                <br><small>últimos 30 días</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-collection fa-2x text-gray-300"></i>
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
                                Metas Activas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $datos['estadisticas']['metas_proximas_vencer'] }}
                            </div>
                            <div class="text-xs text-muted">
                                <small>mensual • semestral • anual</small>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-bullseye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Acciones Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('comisiones.gestion') }}" class="btn btn-outline-primary btn-block">
                                <i class="bi bi-list-check"></i><br>
                                <span class="text-sm">Gestionar Comisiones</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('lotes-pago.create') }}" class="btn btn-outline-success btn-block">
                                <i class="bi bi-plus-circle"></i><br>
                                <span class="text-sm">Crear Lote de Pago</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('metas-ventas.index') }}" class="btn btn-outline-info btn-block">
                                <i class="bi bi-bullseye"></i><br>
                                <span class="text-sm">Configurar Metas</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('comisiones.pdf_listado') }}" class="btn btn-outline-danger btn-block" target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i><br>
                                <span class="text-sm">Generar Reporte</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos y tablas -->
    <div class="row">
        <!-- Distribución por tipos -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Comisiones por Tipo</h6>
                </div>
                <div class="card-body">
                    @forelse($datos['distribucion_tipos'] as $tipo)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-sm">
                                @if($tipo->tipo_comision === 'meta_venta')
                                    Meta de Ventas
                                @elseif($tipo->tipo_comision === 'mecanico')
                                    Mecánico
                                @elseif($tipo->tipo_comision === 'carwash')
                                    Car Wash
                                @else
                                    {{ ucfirst($tipo->tipo_comision) }}
                                @endif
                            </span>
                            <span class="text-sm font-weight-bold">
                                {{ $config->currency_simbol }} {{ number_format($tipo->total_monto, 2) }}
                            </span>
                        </div>
                        <div class="progress mt-1" style="height: 8px;">
                            @php
                                $totalGeneral = $datos['distribucion_tipos']->sum('total_monto');
                                $porcentaje = $totalGeneral > 0 ? ($tipo->total_monto / $totalGeneral) * 100 : 0;
                                $colorClasses = [
                                    'meta_venta' => 'bg-success',
                                    'mecanico' => 'bg-info', 
                                    'carwash' => 'bg-warning'
                                ];
                                $colorClass = $colorClasses[$tipo->tipo_comision] ?? 'bg-secondary';
                            @endphp
                            <div class="progress-bar {{ $colorClass }}" style="width: {{ $porcentaje }}%"></div>
                        </div>
                        <small class="text-muted">{{ $tipo->cantidad }} comisiones</small>
                    </div>
                    @empty
                    <p class="text-muted text-center">No hay comisiones pendientes</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top beneficiarios -->
        <div class="col-xl-4 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Beneficiarios (Histórico)</h6>
                </div>
                <div class="card-body">
                    <!-- Vendedores -->
                    <h6 class="text-sm font-weight-bold text-success mb-2">Vendedores - Total Acumulado</h6>
                    @forelse($datos['top_vendedores'] as $vendedor)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-sm">{{ $vendedor->commissionable->name ?? 'N/A' }}</span>
                        <span class="badge bg-success">{{ $config->currency_simbol }} {{ number_format($vendedor->total_comisiones, 2) }}</span>
                    </div>
                    @empty
                    <p class="text-muted text-sm">No hay comisiones de vendedores</p>
                    @endforelse

                    <hr class="my-3">

                    <!-- Trabajadores -->
                    <h6 class="text-sm font-weight-bold text-info mb-2">Trabajadores - Total Acumulado</h6>
                    @forelse($datos['top_trabajadores'] as $trabajador)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-sm">{{ $trabajador->commissionable->nombre ?? 'N/A' }}</span>
                        <span class="badge bg-info">{{ $config->currency_simbol }} {{ number_format($trabajador->total_comisiones, 2) }}</span>
                    </div>
                    @empty
                    <p class="text-muted text-sm">No hay comisiones de trabajadores</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Actividad reciente -->
        <div class="col-xl-4 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actividad Reciente</h6>
                </div>
                <div class="card-body">
                    <h6 class="text-sm font-weight-bold text-warning mb-2">Últimas Comisiones</h6>
                    @forelse($datos['ultimas_comisiones']->take(5) as $comision)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="text-sm">
                                @if($comision->commissionable_type === 'App\Models\User')
                                    {{ $comision->commissionable->name ?? 'Usuario' }}
                                @else
                                    {{ $comision->commissionable->nombre ?? 'Trabajador' }}
                                @endif
                            </span>
                            <br>
                            <small class="text-muted">{{ $comision->fecha_calculo ? \Carbon\Carbon::parse($comision->fecha_calculo)->format('d/m/Y') : 'N/A' }}</small>
                        </div>
                        <span class="badge 
                            @if($comision->estado === 'pendiente') bg-warning
                            @elseif($comision->estado === 'pagado') bg-success
                            @else bg-secondary @endif">
                            {{ $config->currency_simbol }} {{ number_format($comision->monto, 2) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-muted text-sm">No hay comisiones recientes</p>
                    @endforelse

                    <hr class="my-3">

                    <h6 class="text-sm font-weight-bold text-info mb-2">Últimos Lotes</h6>
                    @forelse($datos['ultimos_lotes']->take(3) as $lote)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="text-sm">Lote #{{ $lote->numero_lote }}</span>
                            <br>
                            <small class="text-muted">{{ $lote->created_at->format('d/m/Y') }}</small>
                        </div>
                        <span class="badge 
                            @if($lote->estado === 'pendiente') bg-warning
                            @elseif($lote->estado === 'procesado') bg-success
                            @else bg-secondary @endif">
                            {{ $config->currency_simbol }} {{ number_format($lote->monto_total, 2) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-muted text-sm">No hay lotes recientes</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}
.fa-2x {
    font-size: 2rem;
}
.btn-block {
    display: block;
    width: 100%;
    text-align: center;
    padding: 0.75rem;
    min-height: 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
</style>
@endsection
