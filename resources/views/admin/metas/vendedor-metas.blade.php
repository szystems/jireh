@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-bullseye text-primary"></i>
            Metas Disponibles
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.pro') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Metas Disponibles</li>
            </ol>
        </nav>
    </div>

    <!-- Mi Situación Actual -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <i class="bi bi-graph-up-arrow fs-1 text-primary mb-2"></i>
                    <h5 class="card-title">Mis Ventas Este Mes</h5>
                    <h2 class="text-primary">{{ $config->currency_simbol }}{{ number_format($misVentasMes, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-body text-center">
                    <i class="bi bi-trophy fs-1 text-success mb-2"></i>
                    <h5 class="card-title">Meta Alcanzada</h5>
                    @if($miMetaAlcanzada)
                        <h3 class="text-success">{{ $miMetaAlcanzada->nombre }}</h3>
                        <p class="text-muted">{{ $config->currency_simbol }}{{ number_format($miMetaAlcanzada->monto_minimo, 2) }}</p>
                    @else
                        <h3 class="text-muted">Ninguna</h3>
                        <p class="text-muted">¡Sigue vendiendo!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Información sobre Metas -->
    <div class="alert alert-info mb-4">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle fs-4 me-3"></i>
            <div>
                <h5 class="alert-heading mb-1">¿Cómo funcionan las metas?</h5>
                <p class="mb-0">
                    Las metas se basan en tus ventas mensuales. Cada meta alcanzada te otorga una comisión adicional. 
                    ¡Mientras más vendas, mayores serán tus comisiones!
                </p>
            </div>
        </div>
    </div>

    <!-- Lista de Metas Disponibles -->
    <div class="row">
        @foreach($metasConProgreso as $meta)
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card h-100 {{ $meta->alcanzada ? 'border-success' : 'border-secondary' }}">
                <div class="card-header {{ $meta->alcanzada ? 'bg-success text-white' : 'bg-light' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            @if($meta->alcanzada)
                                <i class="bi bi-check-circle-fill"></i>
                            @else
                                <i class="bi bi-circle"></i>
                            @endif
                            {{ $meta->nombre }}
                        </h6>
                        @if($meta->alcanzada)
                            <span class="badge bg-light text-success">¡ALCANZADA!</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Requisito de Venta -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Requisito de Venta:</span>
                            <strong>{{ $config->currency_simbol }}{{ number_format($meta->monto_minimo, 2) }}</strong>
                        </div>
                        @if($meta->monto_maximo)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Límite Máximo:</span>
                                <strong>{{ $config->currency_simbol }}{{ number_format($meta->monto_maximo, 2) }}</strong>
                            </div>
                        @endif
                    </div>

                    <!-- Progreso -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Tu Progreso:</span>
                            <span class="fw-bold">{{ number_format($meta->progreso, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar {{ $meta->alcanzada ? 'bg-success' : 'bg-primary' }}" 
                                 role="progressbar" 
                                 style="width: {{ min(100, $meta->progreso) }}%" 
                                 aria-valuenow="{{ $meta->progreso }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                            </div>
                        </div>
                        @if(!$meta->alcanzada && $meta->faltante > 0)
                            <small class="text-muted">
                                Te faltan {{ $config->currency_simbol }}{{ number_format($meta->faltante, 2) }} para alcanzar esta meta
                            </small>
                        @endif
                    </div>

                    <!-- Comisión -->
                    <div class="mb-3">
                        <div class="bg-light p-3 rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Comisión:</span>
                                <div class="text-end">
                                    <div class="h6 text-warning mb-0">{{ $meta->comision_porcentaje }}%</div>
                                    <small class="text-muted">
                                        ≈ {{ $config->currency_simbol }}{{ number_format($meta->comision_porcentaje * $meta->monto_minimo / 100, 2) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estado -->
                    @if($meta->alcanzada)
                        <div class="alert alert-success py-2 mb-0">
                            <i class="bi bi-trophy"></i>
                            <strong>¡Meta Alcanzada!</strong>
                            <br>
                            <small>Comisión ganada: {{ $config->currency_simbol }}{{ number_format($meta->comision_porcentaje * $misVentasMes / 100, 2) }}</small>
                        </div>
                    @else
                        <div class="alert alert-info py-2 mb-0">
                            <i class="bi bi-rocket"></i>
                            <strong>En Progreso</strong>
                            <br>
                            <small>¡Sigue vendiendo para alcanzar esta meta!</small>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    @if($meta->descripcion)
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i>
                            {{ $meta->descripcion }}
                        </small>
                    @else
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i>
                            Meta activa - Válida todo el mes
                        </small>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($metasConProgreso->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-bullseye display-1 text-muted"></i>
            <h4 class="text-muted mt-3">No hay metas activas</h4>
            <p class="text-muted">Actualmente no hay metas configuradas en el sistema.</p>
            <p class="text-muted">Contacta con tu supervisor para más información.</p>
        </div>
    @endif

    <!-- Botones de Acción -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="{{ route('vendedor.mis_ventas') }}" class="btn btn-primary me-2">
                <i class="bi bi-graph-up-arrow"></i>
                Ver Mis Ventas
            </a>
            <a href="{{ route('vendedor.mi_rendimiento') }}" class="btn btn-outline-success">
                <i class="bi bi-trophy"></i>
                Mi Rendimiento
            </a>
        </div>
    </div>
</div>
@endsection
