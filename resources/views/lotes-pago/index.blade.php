@extends('layouts.admin')

@section('title', 'Lotes de Pago - Comisiones')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-file-earmark-plus"></i> Lotes de Pago de Comisiones
                    </h4>
                    <div class="btn-group">
                        <a href="{{ route('lotes-pago.pdf', request()->all()) }}" class="btn btn-danger" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i> Generar PDF
                        </a>
                        <a href="{{ route('lotes-pago.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Nuevo Lote de Pago
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filtros -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" 
                                       value="{{ request('fecha_inicio') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" 
                                       value="{{ request('fecha_fin') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="estado" class="form-label">Estado</label>
                                <select name="estado" id="estado" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="procesando" {{ request('estado') == 'procesando' ? 'selected' : '' }}>Procesando</option>
                                    <option value="completado" {{ request('estado') == 'completado' ? 'selected' : '' }}>Completado</option>
                                    <option value="anulado" {{ request('estado') == 'anulado' ? 'selected' : '' }}>Anulado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="metodo_pago" class="form-label">Método Pago</label>
                                <select name="metodo_pago" id="metodo_pago" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="efectivo" {{ request('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                    <option value="transferencia" {{ request('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                    <option value="cheque" {{ request('metodo_pago') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-secondary me-2">
                                    <i class="bi bi-search"></i> Filtrar
                                </button>
                                <a href="{{ route('lotes-pago.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </form>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Tabla de lotes -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Número Lote</th>
                                    <th>Fecha Pago</th>
                                    <th>Método Pago</th>
                                    <th>Cantidad Comisiones</th>
                                    <th>Monto Total</th>
                                    <th>Estado</th>
                                    <th>Usuario</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lotesPago as $lote)
                                    <tr>
                                        <td>
                                            <strong>{{ $lote->numero_lote }}</strong>
                                        </td>
                                        <td>
                                            {{ $lote->fecha_pago->format('d/m/Y') }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ ucfirst($lote->metodo_pago) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ $lote->cantidad_comisiones }} comisiones
                                        </td>
                                        <td>
                                            <strong class="text-success">
                                                {{ $config->currency_simbol }}{{ number_format($lote->monto_total, 2) }}
                                            </strong>
                                        </td>
                                        <td>
                                            @if($lote->estado == 'procesando')
                                                <span class="badge bg-warning">Procesando</span>
                                            @elseif($lote->estado == 'completado')
                                                <span class="badge bg-success">Completado</span>
                                            @else
                                                <span class="badge bg-danger">Anulado</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $lote->usuario->name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('lotes-pago.show', $lote) }}" 
                                                   class="btn btn-sm btn-info" title="Ver detalles">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                
                                                @if($lote->estado != 'anulado')
                                                    <a href="{{ route('lotes-pago.edit', $lote) }}" 
                                                       class="btn btn-sm btn-warning" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    
                                                    <form method="POST" action="{{ route('lotes-pago.destroy', $lote) }}" 
                                                          style="display: inline;"
                                                          onsubmit="return confirm('¿Está seguro de anular este lote de pago? Esta acción anulará todos los pagos asociados.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Anular">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <div class="py-4">
                                                <i class="bi bi-inbox h1 text-muted mb-3"></i>
                                                <p class="text-muted">No hay lotes de pago registrados</p>
                                                <a href="{{ route('lotes-pago.create') }}" class="btn btn-primary">
                                                    <i class="bi bi-plus-circle"></i> Crear primer lote de pago
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($lotesPago->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $lotesPago->appends(request()->query())->links() }}
                        </div>
                    @endif

                    <!-- Resumen -->
                    @if($lotesPago->count() > 0)
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">Resumen de la página actual:</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <small class="text-muted">Total Lotes:</small>
                                                <div><strong>{{ $lotesPago->count() }}</strong></div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Lotes Completados:</small>
                                                <div><strong>{{ $lotesPago->where('estado', 'completado')->count() }}</strong></div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Total Comisiones:</small>
                                                <div><strong>{{ $lotesPago->sum('cantidad_comisiones') }}</strong></div>
                                            </div>
                                            <div class="col-md-3">
                                                <small class="text-muted">Monto Total:</small>
                                                <div><strong class="text-success">{{ $config->currency_simbol }}{{ number_format($lotesPago->sum('monto_total'), 2) }}</strong></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
