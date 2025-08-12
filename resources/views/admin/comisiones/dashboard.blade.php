@extends('layouts.admin')

@section('title', 'Dashboard de Comisiones')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-graph-up"></i> Dashboard de Comisiones
                    </h4>
                    <div class="card-tools">
                        <div class="row">
                            <div class="col-auto">
                                <select name="periodo" id="periodo-filter" class="form-select form-select-sm">
                                    <option value="hoy" {{ $periodo == 'hoy' ? 'selected' : '' }}>Hoy</option>
                                    <option value="semana_actual" {{ $periodo == 'semana_actual' ? 'selected' : '' }}>Esta Semana</option>
                                    <option value="mes_actual" {{ $periodo == 'mes_actual' ? 'selected' : '' }}>Este Mes</option>
                                    <option value="mes_anterior" {{ $periodo == 'mes_anterior' ? 'selected' : '' }}>Mes Anterior</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <select name="periodo_meta" id="periodo-meta-filter" class="form-select form-select-sm">
                                    <option value="mensual" {{ $periodoMeta == 'mensual' ? 'selected' : '' }}>Metas Mensuales</option>
                                    <option value="trimestral" {{ $periodoMeta == 'trimestral' ? 'selected' : '' }}>Metas Trimestrales</option>
                                    <option value="semestral" {{ $periodoMeta == 'semestral' ? 'selected' : '' }}>Metas Semestrales</option>
                                    <option value="anual" {{ $periodoMeta == 'anual' ? 'selected' : '' }}>Metas Anuales</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <select name="tipo" id="tipo-filter" class="form-select form-select-sm">
                                    <option value="todas" {{ $tipoComision == 'todas' ? 'selected' : '' }}>Todas las Comisiones</option>
                                    <option value="vendedores" {{ $tipoComision == 'vendedores' ? 'selected' : '' }}>Solo Vendedores</option>
                                    <option value="mecanicos" {{ $tipoComision == 'mecanicos' ? 'selected' : '' }}>Solo Mecánicos</option>
                                    <option value="carwash" {{ $tipoComision == 'carwash' ? 'selected' : '' }}>Solo Car Wash</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-primary btn-sm" onclick="aplicarFiltros()">
                                    <i class="bi bi-funnel"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Resumen de Período -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="alert alert-info">
                                <i class="bi bi-calendar3"></i>
                                <strong>Período de Cálculo:</strong> {{ $fechas['inicio']->format('d/m/Y') }} - {{ $fechas['fin']->format('d/m/Y') }}
                                <br>
                                <i class="bi bi-target"></i>
                                <strong>Período de Metas:</strong> {{ ucfirst($periodoMeta) }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            @if($tipoComision == 'todas' || $tipoComision == 'vendedores')
                                <form action="{{ route('comisiones.procesar_vendedores') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="periodo_meta" value="{{ $periodoMeta }}">
                                    <button type="submit" class="btn btn-success btn-sm w-100" 
                                            onclick="return confirm('¿Procesar comisiones de vendedores? Esto creará/actualizará los registros en la base de datos.')">
                                        <i class="bi bi-calculator"></i> Procesar Comisiones de Vendedores
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- Tarjetas de Resumen -->
                    <div class="row mb-4">
                        <!-- Comisiones Vendedores -->
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="bi bi-person-check"></i> Comisiones por Metas de Ventas
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h5 class="text-primary">{{ count($comisiones['vendedores']) }}</h5>
                                                <small class="text-muted">Vendedores</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h5 class="text-success">
                                                    {{ $config->currency_simbol }}{{ number_format($comisiones['vendedores']->sum('comision_calculada'), 2) }}
                                                </h5>
                                                <small class="text-muted">Total Comisiones</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="#vendedores-detail" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i> Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Comisiones Mecánicos -->
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-warning h-100">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0">
                                        <i class="bi bi-tools"></i> Comisiones Mecánicos
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h5 class="text-warning">{{ count($comisiones['mecanicos']) }}</h5>
                                                <small class="text-muted">Mecánicos</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h5 class="text-success">
                                                    {{ $config->currency_simbol }}{{ number_format($comisiones['mecanicos']->sum('comision_calculada'), 2) }}
                                                </h5>
                                                <small class="text-muted">Total Comisiones</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="#mecanicos-detail" class="btn btn-outline-warning btn-sm">
                                        <i class="bi bi-eye"></i> Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Comisiones Car Wash -->
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card border-info h-100">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">
                                        <i class="bi bi-car-front"></i> Comisiones Car Wash
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h5 class="text-info">{{ count($comisiones['carwash']) }}</h5>
                                                <small class="text-muted">Carwasheros</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-center">
                                                <h5 class="text-success">
                                                    {{ $config->currency_simbol }}{{ number_format($comisiones['carwash']->sum('comision_calculada'), 2) }}
                                                </h5>
                                                <small class="text-muted">Total Comisiones</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <a href="#carwash-detail" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-eye"></i> Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles por Tipo -->
                    
                    <!-- Vendedores -->
                    @if($tipoComision == 'todas' || $tipoComision == 'vendedores')
                    <div class="row mb-4" id="vendedores-detail">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-person-tie"></i> Detalle de Comisiones por Metas de Ventas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if(count($comisiones['vendedores']) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th>Vendedor</th>
                                                    <th class="text-center">Total Ventas</th>
                                                    <th class="text-end">Total Vendido</th>
                                                    <th class="text-center">Meta Alcanzada</th>
                                                    <th class="text-center">Rango Meta</th>
                                                    <th class="text-end">Comisión</th>
                                                    <th class="text-center">Estado</th>
                                                    <th class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($comisiones['vendedores'] as $vendedor)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $vendedor->nombre }}</strong>
                                                        <br><small class="text-muted">ID: {{ $vendedor->id }}</small>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary">{{ $vendedor->total_ventas }}</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <strong>{{ $config->currency_simbol }}{{ number_format($vendedor->total_vendido, 2) }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($vendedor->meta_alcanzada != 'Sin meta aplicable')
                                                            <span class="badge bg-success">{{ $vendedor->meta_alcanzada }}</span>
                                                            <br><small class="text-muted">{{ $vendedor->porcentaje_aplicado }}% comisión</small>
                                                        @else
                                                            <span class="badge bg-secondary">Sin meta</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($vendedor->meta_detalles)
                                                            <small class="text-muted">
                                                                {{ $config->currency_simbol }}{{ number_format($vendedor->meta_detalles['rango_minimo'], 0) }}
                                                                @if($vendedor->meta_detalles['rango_maximo'])
                                                                    - {{ $config->currency_simbol }}{{ number_format($vendedor->meta_detalles['rango_maximo'], 0) }}
                                                                @else
                                                                    +
                                                                @endif
                                                            </small>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <strong class="text-success">{{ $config->currency_simbol }}{{ number_format($vendedor->comision_calculada, 2) }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($vendedor->estado == 'calculado')
                                                            <span class="badge bg-info">Calculado</span>
                                                        @elseif($vendedor->estado == 'pendiente')
                                                            <span class="badge bg-warning">Pendiente</span>
                                                        @elseif($vendedor->estado == 'pagado')
                                                            <span class="badge bg-success">Pagado</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ ucfirst($vendedor->estado) }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($vendedor->meta_detalles)
                                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#metaModal{{ $vendedor->id }}">
                                                                <i class="bi bi-info-circle"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="text-center py-4">
                                        <i class="bi bi-info-circle display-1 text-muted mb-3"></i>
                                        <p class="text-muted">No hay comisiones de vendedores en este período</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Mecánicos -->
                    @if($tipoComision == 'todas' || $tipoComision == 'mecanicos')
                    <div class="row mb-4" id="mecanicos-detail">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">
                                        <i class="bi bi-tools"></i> Detalle de Comisiones de Mecánicos
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if(count($comisiones['mecanicos']) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-warning">
                                                <tr>
                                                    <th>Mecánico</th>
                                                    <th class="text-center">Total Servicios</th>
                                                    <th class="text-end">Comisión Total</th>
                                                    <th class="text-center">Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($comisiones['mecanicos'] as $mecanico)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $mecanico->nombre }} {{ $mecanico->apellido }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-warning text-dark">{{ $mecanico->total_servicios }}</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <strong class="text-success">{{ $config->currency_simbol }}{{ number_format($mecanico->comision_calculada, 2) }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-warning">{{ ucfirst($mecanico->estado) }}</span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="text-center py-4">
                                        <i class="bi bi-info-circle display-1 text-muted mb-3"></i>
                                        <p class="text-muted">No hay comisiones de mecánicos en este período</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Car Wash -->
                    @if($tipoComision == 'todas' || $tipoComision == 'carwash')
                    <div class="row mb-4" id="carwash-detail">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="bi bi-car-front"></i> Detalle de Comisiones Car Wash
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if(count($comisiones['carwash']) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-info">
                                                <tr>
                                                    <th>Trabajador Car Wash</th>
                                                    <th class="text-center">Total Servicios</th>
                                                    <th class="text-end">Comisión Total</th>
                                                    <th class="text-center">Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($comisiones['carwash'] as $carwash)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $carwash->nombre }} {{ $carwash->apellido }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-info">{{ $carwash->total_servicios }}</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <strong class="text-success">{{ $config->currency_simbol }}{{ number_format($carwash->comision_calculada, 2) }}</strong>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-warning">{{ ucfirst($carwash->estado) }}</span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="text-center py-4">
                                        <i class="bi bi-info-circle display-1 text-muted mb-3"></i>
                                        <p class="text-muted">No hay comisiones de carwash en este período</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Acciones Adicionales -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="bi bi-gear"></i> Acciones Adicionales
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-2">
                                            <a href="{{ route('comisiones.index') }}" class="btn btn-outline-primary w-100">
                                                <i class="bi bi-list-check"></i> Ver Todas las Comisiones
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <a href="{{ route('comisiones.resumen') }}" class="btn btn-outline-info w-100">
                                                <i class="bi bi-bar-chart"></i> Resumen Detallado
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <button type="button" class="btn btn-outline-success w-100" onclick="exportarReporte()">
                                                <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                                            </button>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <button type="button" class="btn btn-outline-danger w-100" onclick="exportarPDF()">
                                                <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modales de Detalles de Metas -->
@if($tipoComision == 'todas' || $tipoComision == 'vendedores')
    @foreach($comisiones['vendedores'] as $vendedor)
        @if($vendedor->meta_detalles)
            <div class="modal fade" id="metaModal{{ $vendedor->id }}" tabindex="-1" aria-labelledby="metaModalLabel{{ $vendedor->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="metaModalLabel{{ $vendedor->id }}">
                                <i class="bi bi-target"></i> Detalles de Meta: {{ $vendedor->nombre }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary">Información de la Meta</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Nombre:</strong></td>
                                            <td>{{ $vendedor->meta_detalles['nombre'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Descripción:</strong></td>
                                            <td>{{ $vendedor->meta_detalles['descripcion'] ?? 'Sin descripción' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Período:</strong></td>
                                            <td><span class="badge bg-info">{{ ucfirst($vendedor->meta_detalles['periodo']) }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Monto Mínimo:</strong></td>
                                            <td>{{ $config->currency_simbol }}{{ number_format($vendedor->meta_detalles['rango_minimo'], 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Monto Máximo:</strong></td>
                                            <td>
                                                @if($vendedor->meta_detalles['rango_maximo'])
                                                    {{ $config->currency_simbol }}{{ number_format($vendedor->meta_detalles['rango_maximo'], 2) }}
                                                @else
                                                    <span class="text-muted">Sin límite</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Porcentaje Comisión:</strong></td>
                                            <td><span class="badge bg-success">{{ $vendedor->meta_detalles['porcentaje'] }}%</span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-success">Rendimiento del Vendedor</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Total Ventas:</strong></td>
                                            <td><span class="badge bg-primary">{{ $vendedor->total_ventas }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Vendido:</strong></td>
                                            <td><strong>{{ $config->currency_simbol }}{{ number_format($vendedor->total_vendido, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Comisión Calculada:</strong></td>
                                            <td><strong class="text-success">{{ $config->currency_simbol }}{{ number_format($vendedor->comision_calculada, 2) }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Estado:</strong></td>
                                            <td>
                                                @if($vendedor->estado == 'calculado')
                                                    <span class="badge bg-info">Calculado</span>
                                                @elseif($vendedor->estado == 'pendiente')
                                                    <span class="badge bg-warning">Pendiente</span>
                                                @elseif($vendedor->estado == 'pagado')
                                                    <span class="badge bg-success">Pagado</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($vendedor->estado) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                    
                                    <!-- Progreso hacia la meta -->
                                    @if($vendedor->meta_detalles['rango_maximo'])
                                        @php
                                            $progreso = ($vendedor->total_vendido / $vendedor->meta_detalles['rango_maximo']) * 100;
                                            $progreso = min($progreso, 100);
                                        @endphp
                                        <div class="mt-3">
                                            <label class="form-label">Progreso hacia el límite superior:</label>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: {{ $progreso }}%" 
                                                     aria-valuenow="{{ $progreso }}" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    {{ round($progreso, 1) }}%
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif

<script>
function aplicarFiltros() {
    const periodo = document.getElementById('periodo-filter').value;
    const periodoMeta = document.getElementById('periodo-meta-filter').value;
    const tipo = document.getElementById('tipo-filter').value;
    
    const url = new URL(window.location.href);
    url.searchParams.set('periodo', periodo);
    url.searchParams.set('periodo_meta', periodoMeta);
    url.searchParams.set('tipo', tipo);
    
    window.location.href = url.toString();
}

function exportarReporte() {
    const periodo = document.getElementById('periodo-filter').value;
    const tipo = document.getElementById('tipo-filter').value;
    
    window.open(`/comisiones/exportar?periodo=${periodo}&tipo=${tipo}&formato=excel`, '_blank');
}

function exportarPDF() {
    const periodo = document.getElementById('periodo-filter').value;
    const tipo = document.getElementById('tipo-filter').value;
    
    window.open(`/comisiones/exportar?periodo=${periodo}&tipo=${tipo}&formato=pdf`, '_blank');
}

// Auto-actualizar cada 30 segundos si está en "hoy"
@if($periodo == 'hoy')
setInterval(function() {
    window.location.reload();
}, 30000);
@endif
</script>
@endsection
