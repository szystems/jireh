@extends('layouts.admin')

@section('content')
    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-target"></i>
                </div>
                <div class="page-title">
                    <h5>Metas de Ventas</h5>
                </div>
            </div>
        </div>
        <div class="content-wrapper">
            <!-- Alertas -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Estadísticas en cards -->
            <div class="row mb-4">
                @php
                    $totalMetas = $metas->count();
                    $metasActivas = $metas->where('estado', true)->count();
                    $metasInactivas = $metas->where('estado', false)->count();
                    
                    $metasPorPeriodo = $metas->groupBy('periodo')->map(function($metas, $periodo) {
                        return [
                            'periodo' => ucfirst($periodo),
                            'cantidad' => $metas->count(),
                            'activas' => $metas->where('estado', true)->count()
                        ];
                    });
                    
                    $comisionPromedio = $metas->where('estado', true)->avg('porcentaje_comision') ?? 0;
                @endphp

                <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-primary">
                                <i class="bi bi-target"></i>
                            </div>
                            <div class="fs-6 fw-bold">Total Metas</div>
                            <div class="fs-4 text-primary">{{ number_format($totalMetas, 0, '.', ',') }}</div>
                            <div class="small text-muted">registradas</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="fs-6 fw-bold">Metas Activas</div>
                            <div class="fs-4 text-success">{{ number_format($metasActivas, 0, '.', ',') }}</div>
                            <div class="small text-muted">funcionando</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-danger">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <div class="fs-6 fw-bold">Metas Inactivas</div>
                            <div class="fs-4 text-danger">{{ number_format($metasInactivas, 0, '.', ',') }}</div>
                            <div class="small text-muted">deshabilitadas</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-warning">
                                <i class="bi bi-percent"></i>
                            </div>
                            <div class="fs-6 fw-bold">Comisión Promedio</div>
                            <div class="fs-4 text-warning">{{ number_format($comisionPromedio, 1) }}%</div>
                            <div class="small text-muted">de comisión</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros por período -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="card-title mb-0">
                                    <i class="bi bi-funnel me-2"></i>Filtrar por Período
                                </h6>
                                @isset($filtroAplicado)
                                    <div class="badge bg-info">
                                        <i class="bi bi-filter-circle me-1"></i>
                                        Mostrando: {{ $filtroAplicado['etiqueta'] }}
                                        <a href="{{ route('metas-ventas.index') }}" class="text-white ms-2" title="Quitar filtro">
                                            <i class="bi bi-x-lg"></i>
                                        </a>
                                    </div>
                                @endisset
                            </div>
                            <div class="btn-group" role="group" aria-label="Filtros de período">
                                <a href="{{ route('metas-ventas.index') }}" 
                                   class="btn {{ !isset($filtroAplicado) ? 'btn-primary' : 'btn-outline-primary' }}">
                                    <i class="bi bi-list me-1"></i> Todas
                                </a>
                                <a href="{{ route('metas-ventas.por-periodo', 'mensual') }}" 
                                   class="btn {{ (isset($filtroAplicado) && $filtroAplicado['valor'] == 'mensual') ? 'btn-info' : 'btn-outline-info' }}">
                                    <i class="bi bi-calendar-month me-1"></i> Mensuales
                                </a>
                                <a href="{{ route('metas-ventas.por-periodo', 'trimestral') }}" 
                                   class="btn {{ (isset($filtroAplicado) && $filtroAplicado['valor'] == 'trimestral') ? 'btn-warning' : 'btn-outline-warning' }}">
                                    <i class="bi bi-calendar3 me-1"></i> Trimestrales
                                </a>
                                <a href="{{ route('metas-ventas.por-periodo', 'semestral') }}" 
                                   class="btn {{ (isset($filtroAplicado) && $filtroAplicado['valor'] == 'semestral') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                    <i class="bi bi-calendar2-range me-1"></i> Semestrales
                                </a>
                                <a href="{{ route('metas-ventas.por-periodo', 'anual') }}" 
                                   class="btn {{ (isset($filtroAplicado) && $filtroAplicado['valor'] == 'anual') ? 'btn-success' : 'btn-outline-success' }}">
                                    <i class="bi bi-calendar-year me-1"></i> Anuales
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de metas -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center p-3">
                            <h5 class="card-title mb-0 text-white">
                                <i class="bi bi-table me-2"></i>Lista de Metas de Ventas
                            </h5>
                            <a href="{{ route('metas-ventas.create') }}" class="btn btn-light btn-sm">
                                <i class="bi bi-plus-circle me-1"></i> Nueva Meta
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th width="60">ID</th>
                                            <th>Meta</th>
                                            <th>Rango de Montos</th>
                                            <th>Comisión</th>
                                            <th>Período</th>
                                            <th>Estado</th>
                                            <th width="100">Toggle</th>
                                            <th width="150">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($metas as $meta)
                                            <tr class="{{ !$meta->estado ? 'table-secondary text-muted' : '' }}">
                                                <td>
                                                    <span class="badge bg-secondary">{{ $meta->id }}</span>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong class="text-primary">{{ $meta->nombre }}</strong>
                                                        @if($meta->descripcion)
                                                            <div class="small text-muted mt-1">{{ $meta->descripcion }}</div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        Q{{ number_format($meta->monto_minimo, 2) }} 
                                                        @if($meta->monto_maximo)
                                                            - Q{{ number_format($meta->monto_maximo, 2) }}
                                                        @else
                                                            en adelante
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success fs-6">{{ $meta->porcentaje_comision }}%</span>
                                                </td>
                                                <td>
                                                    <span class="badge 
                                                        @if($meta->periodo == 'mensual') bg-info 
                                                        @elseif($meta->periodo == 'trimestral') bg-warning 
                                                        @elseif($meta->periodo == 'anual') bg-success 
                                                        @else bg-secondary 
                                                        @endif">
                                                        {{ ucfirst($meta->periodo) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($meta->estado)
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle me-1"></i>Activa
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger">
                                                            <i class="bi bi-x-circle me-1"></i>Inactiva
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <!-- Botón Toggle Estado -->
                                                    <form action="{{ route('metas-ventas.toggle-estado', $meta) }}" 
                                                          method="POST" 
                                                          style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="btn btn-{{ $meta->estado ? 'success' : 'danger' }} btn-sm" 
                                                                title="{{ $meta->estado ? 'Meta activa - Clic para desactivar' : 'Meta inactiva - Clic para activar' }}"
                                                                onclick="return confirm('¿Estás seguro de {{ $meta->estado ? 'desactivar' : 'activar' }} esta meta?')">
                                                            <i class="bi bi-{{ $meta->estado ? 'toggle-on' : 'toggle-off' }}"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <!-- Botón Ver -->
                                                        <a href="{{ route('metas-ventas.show', $meta) }}" 
                                                           class="btn btn-info" 
                                                           title="Ver detalles">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        
                                                        <!-- Botón Editar -->
                                                        <a href="{{ route('metas-ventas.edit', $meta) }}" 
                                                           class="btn btn-warning {{ !$meta->estado ? 'disabled' : '' }}" 
                                                           title="Editar meta">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        
                                                        <!-- Botón Eliminar -->
                                                        <button type="button" 
                                                                class="btn btn-danger" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#deleteModal-{{ $meta->id }}"
                                                                title="Eliminar meta">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="bi bi-inbox fs-1"></i>
                                                        <div class="mt-2">No hay metas de ventas registradas</div>
                                                        <a href="{{ route('metas-ventas.create') }}" class="btn btn-primary mt-2">
                                                            <i class="bi bi-plus-circle me-1"></i> Crear primera meta
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación -->
                            @if($metas->hasPages())
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $metas->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales de confirmación para eliminar -->
    @foreach ($metas as $meta)
        <div class="modal fade" id="deleteModal-{{ $meta->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $meta->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteModalLabel-{{ $meta->id }}">
                            <i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que quieres eliminar la meta "<strong>{{ $meta->nombre }}</strong>"?</p>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Esta acción no se puede deshacer.</strong>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Período:</strong> {{ ucfirst($meta->periodo) }}
                            </div>
                            <div class="col-md-6">
                                <strong>Comisión:</strong> {{ $meta->porcentaje_comision }}%
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cancelar
                        </button>
                        <form action="{{ route('metas-ventas.destroy', $meta) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i>Eliminar Meta
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
