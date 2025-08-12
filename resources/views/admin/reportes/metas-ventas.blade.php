@extends('layouts.admin')

@section('title', 'Reporte de Metas de Ventas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0"><i class="bi bi-graph-up-arrow"></i> Reporte de Metas de Ventas</h4>
                        <small class="text-muted">
                            Período: {{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }}
                        </small>
                    </div>
                    <div>
                        <!-- Selector de período y botón PDF -->
                        <form method="GET" class="d-flex gap-2 align-items-center">
                            <select name="periodo" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="mes" {{ $periodo == 'mes' ? 'selected' : '' }}>Este Mes</option>
                                <option value="trimestre" {{ $periodo == 'trimestre' ? 'selected' : '' }}>Este Trimestre</option>
                                <option value="semestre" {{ $periodo == 'semestre' ? 'selected' : '' }}>Este Semestre</option>
                                <option value="año" {{ $periodo == 'año' ? 'selected' : '' }}>Este Año</option>
                            </select>
                            <a href="{{ route('reportes.metas.pdf', ['periodo' => $periodo]) }}" 
                               class="btn btn-danger btn-sm" target="_blank" title="Generar PDF">
                                <i class="bi bi-file-earmark-pdf"></i> PDF
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Resumen de Metas -->
            <div class="row mb-4">
                @if($metas->count() > 0)
                    @foreach($metas as $index => $meta)
                    @php
                        $trabajadoresEnMeta = $trabajadores->filter(function($trabajador) use ($meta) {
                            return $trabajador['meta_actual'] && $trabajador['meta_actual']->id == $meta->id;
                        });
                        
                        // Generar color consistente basado en ID de meta
                        $colores = ['primary', 'success', 'warning', 'info', 'secondary', 'danger', 'dark'];
                        $colorMeta = $colores[$meta->id % count($colores)];
                    @endphp
                    <div class="col-md-{{ $metas->count() <= 4 ? (12 / $metas->count()) : '3' }} mb-3">
                        <div class="card border-{{ $colorMeta }}">
                            <div class="card-body text-center">
                                <h5 class="card-title text-{{ $colorMeta }}">
                                    <i class="bi bi-award"></i> {{ $meta->nombre }}
                                </h5>
                                <p class="mb-1"><strong>{{ $config->currency_simbol }}{{ number_format($meta->monto_minimo) }}+</strong></p>
                                <p class="mb-1"><small class="text-muted">{{ ucfirst($meta->periodo) }}</small></p>
                                <p class="mb-0">
                                    <span class="badge bg-{{ $colorMeta }}">
                                        {{ $trabajadoresEnMeta->count() }} trabajadores
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>No hay metas configuradas para el período "{{ ucfirst($periodo) }}"</strong>
                            <br>
                            <small>Configure metas de ventas para este período en el módulo de administración.</small>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tabla de Trabajadores -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-people"></i> Progreso de Trabajadores</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Trabajador</th>
                                    <th>Total Vendido</th>
                                    <th>Ventas</th>
                                    <th>Meta Actual</th>
                                    <th>Progreso Siguiente</th>
                                    <th>Proyección vs Meta</th>
                                    <th>Promedio/Día</th>
                                    <th>Proyección</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trabajadores as $data)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($data['trabajador']->fotografia)
                                                <img src="{{ asset('assets/imgs/users/'.$data['trabajador']->fotografia) }}" 
                                                     class="rounded-circle me-2" width="32" height="32">
                                            @else
                                                <img src="{{ asset('assets/imgs/users/usericon4.png') }}" 
                                                     class="rounded-circle me-2" width="32" height="32">
                                            @endif
                                            <div>
                                                <strong>{{ $data['trabajador']->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $data['trabajador']->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-success">
                                            {{ $config->currency_simbol }}{{ number_format($data['total_vendido'], 2) }}
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $data['cantidad_ventas'] }}</span>
                                    </td>
                                    <td>
                                        @if($data['meta_actual'])
                                            @php
                                                // Generar color basado en ID de meta para consistencia
                                                $colores = ['primary', 'success', 'warning', 'info', 'secondary', 'danger', 'dark'];
                                                $colorMetaActual = $colores[$data['meta_actual']->id % count($colores)];
                                            @endphp
                                            <span class="badge bg-{{ $colorMetaActual }}">
                                                {{ $data['meta_actual']->nombre }}
                                            </span>
                                        @else
                                            <span class="text-muted">Sin meta</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($data['meta_siguiente'])
                                            <div>
                                                <small class="text-muted">Hacia {{ $data['meta_siguiente']->nombre }}</small>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar" style="width: {{ min($data['porcentaje_progreso'], 100) }}%"></div>
                                                </div>
                                                <small>{{ number_format($data['porcentaje_progreso'], 1) }}%</small>
                                                <br>
                                                <small class="text-info">
                                                    Faltan {{ $config->currency_simbol }}{{ number_format($data['meta_siguiente']->monto_minimo - $data['total_vendido'], 2) }}
                                                </small>
                                            </div>
                                        @else
                                            <span class="text-success">¡Meta máxima alcanzada!</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            // Calcular progreso basado en proyección hacia metas
                                            $porcentajeRendimiento = 0;
                                            $colorClase = 'progress-sin-meta';
                                            $textoProgreso = '0%';
                                            $metaObjetivo = null;
                                            
                                            if($data['proyeccion_total'] > 0 && $metasDelPeriodo->count() > 0) {
                                                // Determinar qué meta está intentando alcanzar basado en su proyección
                                                $metasOrdenadas = $metasDelPeriodo->sortBy('monto_minimo');
                                                
                                                // Encontrar la meta que puede alcanzar con su proyección
                                                $metaAlcanzable = null;
                                                $metaSiguiente = null;
                                                
                                                foreach($metasOrdenadas as $meta) {
                                                    if($data['proyeccion_total'] >= $meta->monto_minimo) {
                                                        $metaAlcanzable = $meta; // La meta que alcanzará
                                                    } else {
                                                        $metaSiguiente = $meta; // La siguiente meta que podría alcanzar
                                                        break;
                                                    }
                                                }
                                                
                                                // Determinar contra qué meta mostrar el progreso
                                                if($metaSiguiente) {
                                                    // Mostrar progreso hacia la siguiente meta
                                                    $metaObjetivo = $metaSiguiente;
                                                    $porcentajeRendimiento = ($data['proyeccion_total'] / $metaObjetivo->monto_minimo) * 100;
                                                    $colorClase = 'progress-pendiente';
                                                } elseif($metaAlcanzable) {
                                                    // Ya alcanzó la meta máxima
                                                    $metaObjetivo = $metaAlcanzable;
                                                    $porcentajeRendimiento = 100;
                                                    
                                                    // Generar color consistente basado en ID de meta
                                                    $clases = ['progress-primary', 'progress-success', 'progress-warning', 'progress-info', 'progress-secondary', 'progress-danger', 'progress-dark'];
                                                    $colorClase = $clases[$metaAlcanzable->id % count($clases)];
                                                } else {
                                                    // No alcanza ninguna meta, mostrar progreso hacia la primera
                                                    $metaObjetivo = $metasOrdenadas->first();
                                                    $porcentajeRendimiento = ($data['proyeccion_total'] / $metaObjetivo->monto_minimo) * 100;
                                                    $colorClase = 'progress-pendiente';
                                                }
                                                
                                                // Limitar porcentaje máximo para barras
                                                $porcentajeParaBarra = min($porcentajeRendimiento, 100);
                                                
                                                // Texto del progreso
                                                if($porcentajeRendimiento >= 100 && $metaAlcanzable) {
                                                    $textoProgreso = '✓ ' . $metaAlcanzable->nombre;
                                                } else {
                                                    $textoProgreso = number_format($porcentajeRendimiento, 1) . '%';
                                                }
                                            } else {
                                                $porcentajeParaBarra = 0;
                                            }
                                        @endphp
                                        <div class="progress-container">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar {{ $colorClase }}" 
                                                     style="width: 0%" 
                                                     data-width="{{ $porcentajeParaBarra ?? 0 }}">
                                                    <small class="progress-text">{{ $textoProgreso }}</small>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">
                                                @if($data['proyeccion_total'] > 0 && $metaObjetivo)
                                                    @if($porcentajeRendimiento >= 100)
                                                        ¡Alcanzará {{ $metaObjetivo->nombre }}!
                                                    @else
                                                        Hacia {{ $metaObjetivo->nombre }}: {{ $config->currency_simbol }}{{ number_format($data['proyeccion_total'], 0) }}/{{ $config->currency_simbol }}{{ number_format($metaObjetivo->monto_minimo, 0) }}
                                                    @endif
                                                @elseif($data['proyeccion_total'] == 0)
                                                    Sin actividad este período
                                                @else
                                                    Sin metas disponibles para "{{ ucfirst($periodo) }}"
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-primary">
                                            {{ $config->currency_simbol }}{{ number_format($data['promedio_diario'], 2) }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            {{ $data['dias_transcurridos'] }}/{{ $data['total_dias_periodo'] }} días
                                        </small>
                                    </td>
                                    <td>
                                        <span class="text-{{ $data['proyeccion_total'] > $data['total_vendido'] ? 'success' : 'warning' }}">
                                            {{ $config->currency_simbol }}{{ number_format($data['proyeccion_total'], 2) }}
                                        </span>
                                        @if($data['meta_siguiente'] && $data['proyeccion_total'] >= $data['meta_siguiente']->monto_minimo)
                                            <br>
                                            <small class="text-success">
                                                <i class="bi bi-arrow-up"></i> Alcanzará {{ $data['meta_siguiente']->nombre }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('reportes.metas.trabajador', ['trabajador' => $data['trabajador']->id, 'periodo' => $periodo]) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Ver Detalle
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                        <br>
                                        <span class="text-muted">No hay trabajadores para mostrar</span>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.progress {
    background-color: #e9ecef;
}
.card-body .badge {
    font-size: 0.75rem;
}

/* Estilos para barras de progreso comparativas */
.progress-container {
    min-width: 100px;
}

.progress-sin-meta {
    background: linear-gradient(90deg, #e9ecef, #f8f9fa);
    color: #6c757d;
}

.progress-pendiente {
    background: linear-gradient(90deg, #fd7e14, #ff8c42);
    color: #fff;
}

.progress-primary {
    background: linear-gradient(90deg, #0d6efd, #6ea8fe);
    color: #fff;
}

.progress-success {
    background: linear-gradient(90deg, #198754, #75b798);
    color: #fff;
}

.progress-warning {
    background: linear-gradient(90deg, #ffc107, #ffda6a);
    color: #000;
}

.progress-info {
    background: linear-gradient(90deg, #0dcaf0, #6edff6);
    color: #000;
}

.progress-secondary {
    background: linear-gradient(90deg, #6c757d, #adb5bd);
    color: #fff;
}

.progress-danger {
    background: linear-gradient(90deg, #dc3545, #ea868f);
    color: #fff;
}

.progress-dark {
    background: linear-gradient(90deg, #212529, #6c757d);
    color: #fff;
}

.progress-text {
    position: absolute;
    width: 100%;
    text-align: center;
    line-height: 20px;
    font-weight: bold;
}

.progress-bar {
    transition: width 1s ease-in-out;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Animar las barras de progreso comparativas
    setTimeout(function() {
        $('.progress-bar').each(function() {
            var targetWidth = $(this).attr('data-width');
            $(this).css('width', targetWidth + '%');
        });
    }, 500);
});
</script>
@endsection
