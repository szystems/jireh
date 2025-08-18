@extends('layouts.admin')
@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="page-title">
                    <h5>Pagos de Sueldos</h5>
                </div>
            </div>
            <!-- Date range start -->
            <div class="d-flex align-items-end d-none d-sm-block">
                <h6 class="float-end text-light" id="reloj"></h6>
            </div>
        </div>
        <!-- Main header ends -->

        <!-- Content wrapper start -->
        <div class="content-wrapper">

            <!-- Sección de filtros -->
            <div class="row gx-3 mb-3">
                <div class="col-xl-12">
                    <div class="card card-background-mask-info">
                        <div class="card-body">
                            <form action="{{ route('admin.pago-sueldo.index') }}" method="GET" id="search-form">
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <div class="input-group">
                                            <input class="form-control" placeholder="Buscar por número de lote..." name="buscar" value="{{ request('buscar') }}"/>
                                            <button class="btn btn-outline-secondary" type="submit">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <select class="form-select" name="periodo" onchange="this.form.submit()">
                                            <option value="">Todos los meses</option>
                                            @php
                                                $meses = [
                                                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                                ];
                                            @endphp
                                            @foreach($periodos as $per)
                                                <option value="{{ $per }}" {{ request('periodo') == $per ? 'selected' : '' }}>
                                                    {{ $meses[$per] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <select class="form-select" name="anio" onchange="this.form.submit()">
                                            <option value="">Todos los años</option>
                                            @foreach($anios as $anio)
                                                <option value="{{ $anio }}" {{ request('anio') == $anio ? 'selected' : '' }}>
                                                    {{ $anio }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <select class="form-select" name="estado" onchange="this.form.submit()">
                                            <option value="">Todos los estados</option>
                                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                            <option value="pagado" {{ request('estado') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                                            <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <select class="form-select" name="metodo_pago" onchange="this.form.submit()">
                                            <option value="">Todos los métodos</option>
                                            <option value="efectivo" {{ request('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                                            <option value="transferencia" {{ request('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                                            <option value="cheque" {{ request('metodo_pago') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1 mb-2 text-end">
                                        <a href="{{ route('admin.pago-sueldo.index') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-x-circle"></i> Limpiar
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="row gx-3 mb-3">
                <div class="col-xl-3 col-lg-6 col-sm-6 col-12">
                    <div class="card border-warning h-100">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="bi bi-clock"></i> Pendientes</h6>
                        </div>
                        <div class="card-body text-center">
                            <h4 class="text-warning">{{ $pagosSueldos->where('estado', 'pendiente')->count() }}</h4>
                            <small class="text-muted">Lotes Pendientes</small>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-sm-6 col-12">
                    <div class="card border-success h-100">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="bi bi-check-circle"></i> Pagados</h6>
                        </div>
                        <div class="card-body text-center">
                            <h4 class="text-success">{{ $pagosSueldos->where('estado', 'pagado')->count() }}</h4>
                            <small class="text-muted">Lotes Pagados</small>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-sm-6 col-12">
                    <div class="card border-primary h-100">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="bi bi-wallet2"></i> Total</h6>
                        </div>
                        <div class="card-body text-center">
                            <h4 class="text-primary">Q{{ number_format($pagosSueldos->sum('total_monto'), 2) }}</h4>
                            <small class="text-muted">Total General</small>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 col-sm-6 col-12">
                    <div class="card border-info h-100">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="bi bi-calendar-month"></i> Meses</h6>
                        </div>
                        <div class="card-body text-center">
                            <h4 class="text-info">{{ $pagosSueldos->groupBy('periodo_mes')->count() }}</h4>
                            <small class="text-muted">Meses Activos</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="card-title">Lista de Lotes de Sueldos</h5>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    @if (Auth::user()->role_as == 0)
                                        <a href="{{ route('admin.pago-sueldo.create') }}" class="btn btn-primary">
                                            <i class="bi bi-plus-circle"></i> Crear Lote de Sueldos
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Número de Lote</th>
                                            <th class="text-center">Período</th>
                                            <th class="text-center">Fecha de Pago</th>
                                            <th class="text-center">Empleados</th>
                                            <th class="text-center">Método de Pago</th>
                                            <th class="text-center">Total</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($pagosSueldos as $pagoSueldo)
                                            <tr>
                                                <td class="text-center">
                                                    <strong class="text-primary">{{ $pagoSueldo->numero_lote }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-info">
                                                        {{ $pagoSueldo->periodo_corto }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    {{ \Carbon\Carbon::parse($pagoSueldo->fecha_pago)->format('d/m/Y') }}
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary">{{ $pagoSueldo->detalles->count() }} empleados</span>
                                                </td>
                                                <td class="text-center">
                                                    @switch($pagoSueldo->metodo_pago)
                                                        @case('efectivo')
                                                            <span class="badge bg-success"><i class="bi bi-cash"></i> Efectivo</span>
                                                            @break
                                                        @case('transferencia')
                                                            <span class="badge bg-primary"><i class="bi bi-bank"></i> Transferencia</span>
                                                            @break
                                                        @case('cheque')
                                                            <span class="badge bg-warning"><i class="bi bi-credit-card"></i> Cheque</span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary">{{ ucfirst($pagoSueldo->metodo_pago) }}</span>
                                                    @endswitch
                                                </td>
                                                <td class="text-center">
                                                    <strong class="text-success">Q{{ number_format($pagoSueldo->total_monto, 2) }}</strong>
                                                </td>
                                                <td class="text-center">
                                                    @switch($pagoSueldo->estado)
                                                        @case('pendiente')
                                                            <span class="badge bg-warning"><i class="bi bi-clock"></i> Pendiente</span>
                                                            @break
                                                        @case('pagado')
                                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Pagado</span>
                                                            @break
                                                        @case('cancelado')
                                                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Cancelado</span>
                                                            @break
                                                        @default
                                                            <span class="badge bg-secondary">{{ ucfirst($pagoSueldo->estado) }}</span>
                                                    @endswitch
                                                </td>
                                                <td class="text-center">
                                                    <div class="actions">
                                                        <a href="{{ route('admin.pago-sueldo.show', $pagoSueldo->id) }}" class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        
                                                        @if (Auth::user()->role_as == 0)
                                                            @if($pagoSueldo->estado == 'pendiente')
                                                                <a href="{{ route('admin.pago-sueldo.edit', $pagoSueldo->id) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                                                    <i class="bi bi-pencil"></i>
                                                                </a>
                                                            @endif
                                                            
                                                            <a href="{{ route('admin.pago-sueldo.pdf', $pagoSueldo->id) }}" class="btn btn-sm btn-outline-info" title="PDF" target="_blank">
                                                                <i class="bi bi-file-earmark-pdf"></i>
                                                            </a>
                                                            
                                                            @if($pagoSueldo->estado == 'pendiente')
                                                                <button type="button" class="btn btn-sm btn-outline-danger" title="Cancelar lote" 
                                                                        onclick="confirmDelete({{ $pagoSueldo->id }}, '{{ $pagoSueldo->numero_lote }}')">
                                                                    <i class="bi bi-x-circle"></i>
                                                                </button>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">
                                                    <div class="empty-state">
                                                        <i class="bi bi-wallet2 display-1 text-muted"></i>
                                                        <h5 class="text-muted mt-3">No hay lotes de sueldos registrados</h5>
                                                        <p class="text-muted">Comienza creando el primer lote de sueldos</p>
                                                        @if (Auth::user()->role_as == 0)
                                                            <a href="{{ route('admin.pago-sueldo.create') }}" class="btn btn-primary">
                                                                <i class="bi bi-plus-circle"></i> Crear Primer Lote
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($pagosSueldos->hasPages())
                                <div class="pagination-container">
                                    {{ $pagosSueldos->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de cancelación -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="bi bi-exclamation-triangle text-warning"></i> Confirmar Cancelación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de que desea cancelar el lote de sueldo <strong id="loteNumber"></strong>?</p>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Información:</strong> El lote se marcará como "Cancelado" y se mantendrá en el historial. Solo se pueden cancelar lotes en estado "Pendiente".
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, mantener</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-x-circle"></i> Sí, cancelar lote
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function confirmDelete(id, numeroLote) {
        document.getElementById('loteNumber').textContent = numeroLote;
        document.getElementById('deleteForm').action = '{{ route("admin.pago-sueldo.destroy", ":id") }}'.replace(':id', id);
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // Auto-submit formulario cuando cambian los filtros
    document.addEventListener('DOMContentLoaded', function() {
        const filters = ['periodo', 'anio', 'estado', 'metodo_pago'];
        filters.forEach(filterId => {
            const element = document.querySelector(`select[name="${filterId}"]`);
            if (element) {
                element.addEventListener('change', function() {
                    this.form.submit();
                });
            }
        });
    });
</script>
@endsection
