@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="card mt-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4>Comisiones por Trabajador</h4>
                <div>
                    <a href="{{ url('comisiones') }}" class="btn btn-primary">
                        <i class="bi bi-list-check"></i> Ver Todas las Comisiones
                    </a>
                    <a href="{{ url('comisiones/por-vendedor') }}" class="btn btn-success">
                        <i class="bi bi-person-tie"></i> Ver por Vendedor
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Filtros</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('comisiones/por-trabajador') }}" method="GET" class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="tipo_comision" class="form-label">Tipo de Comisión</label>
                                    <select name="tipo_comision" id="tipo_comision" class="form-select">
                                        <option value="">Todos</option>
                                        @foreach ($tiposComision as $tipo)
                                            <option value="{{ $tipo }}" {{ request('tipo_comision') == $tipo ? 'selected' : '' }}>
                                                {{ ucfirst($tipo) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select name="estado" id="estado" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>
                                            Pendiente
                                        </option>
                                        <option value="pagado" {{ request('estado') == 'pagado' ? 'selected' : '' }}>
                                            Pagado
                                        </option>
                                        <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>
                                            Cancelado
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="periodo" class="form-label">Periodo</label>
                                    <select name="periodo" id="periodo" class="form-select">
                                        <option value="">Personalizado</option>
                                        <option value="mes_actual" {{ request('periodo') == 'mes_actual' ? 'selected' : '' }}>
                                            Mes Actual
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3 fecha-personalizada">
                                    <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
                                </div>
                                <div class="col-md-3 mb-3 fecha-personalizada">
                                    <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
                                </div>
                                <div class="col-md-3 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">Filtrar</button>
                                    <a href="{{ url('comisiones/por-trabajador') }}" class="btn btn-secondary ms-2">Limpiar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                @forelse ($trabajadores as $trabajador)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5>{{ $trabajador->nombre }} {{ $trabajador->apellido }}</h5>
                                <div class="small text-muted">{{ $trabajador->tipoTrabajador ? $trabajador->tipoTrabajador->nombre : 'Sin tipo asignado' }}</div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <i class="bi bi-telephone display-6 text-primary me-3"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted">Teléfono</div>
                                                <div>{{ $trabajador->telefono ?: 'No registrado' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <i class="bi bi-currency-dollar display-6 text-success me-3"></i>
                                            </div>
                                            <div>
                                                <div class="small text-muted">Total Comisiones</div>
                                                <div class="fw-bold">Q. {{ number_format($trabajador->comisiones->sum('monto'), 2) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Resumen de comisiones por tipo -->
                                <div class="mb-3">
                                    <h6>Comisiones por Tipo</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th>Pendientes</th>
                                                    <th>Pagadas</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $tiposAgrupados = $trabajador->comisiones->groupBy('tipo_comision');
                                                @endphp

                                                @foreach ($tiposAgrupados as $tipo => $grupo)
                                                    <tr>
                                                        <td>{{ ucfirst($tipo) }}</td>
                                                        <td>
                                                            Q. {{ number_format($grupo->where('estado', 'pendiente')->sum('monto'), 2) }}
                                                            <span class="badge bg-warning">{{ $grupo->where('estado', 'pendiente')->count() }}</span>
                                                        </td>
                                                        <td>
                                                            Q. {{ number_format($grupo->where('estado', 'pagado')->sum('monto'), 2) }}
                                                            <span class="badge bg-success">{{ $grupo->where('estado', 'pagado')->count() }}</span>
                                                        </td>
                                                        <td>
                                                            Q. {{ number_format($grupo->sum('monto'), 2) }}
                                                            <span class="badge bg-primary">{{ $grupo->count() }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="fw-bold">
                                                    <td>Total</td>
                                                    <td>Q. {{ number_format($trabajador->comisiones->where('estado', 'pendiente')->sum('monto'), 2) }}</td>
                                                    <td>Q. {{ number_format($trabajador->comisiones->where('estado', 'pagado')->sum('monto'), 2) }}</td>
                                                    <td>Q. {{ number_format($trabajador->comisiones->sum('monto'), 2) }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <!-- Últimas 5 comisiones -->
                                <div>
                                    <h6>Últimas Comisiones</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Tipo</th>
                                                    <th>Monto</th>
                                                    <th>Estado</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($trabajador->comisiones->sortByDesc('fecha_calculo')->take(5) as $comision)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($comision->fecha_calculo)->format('d/m/Y') }}</td>
                                                        <td>{{ ucfirst($comision->tipo_comision) }}</td>
                                                        <td>Q. {{ number_format($comision->monto, 2) }}</td>
                                                        <td>
                                                            @if ($comision->estado == 'pendiente')
                                                                <span class="badge bg-warning">Pendiente</span>
                                                            @elseif ($comision->estado == 'pagado')
                                                                <span class="badge bg-success">Pagado</span>
                                                            @elseif ($comision->estado == 'cancelado')
                                                                <span class="badge bg-danger">Cancelado</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <a href="{{ url('comisiones/show/'.$comision->id) }}" class="btn btn-sm btn-primary">
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
                            <div class="card-footer d-flex justify-content-between">
                                <a href="{{ url('show-trabajador/'.$trabajador->id) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-person"></i> Ver Perfil
                                </a>
                                <a href="{{ url('comisiones?tipo_receptor=trabajador&commissionable_id='.$trabajador->id) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-list-check"></i> Ver Todas las Comisiones
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            No hay trabajadores con comisiones que coincidan con los filtros seleccionados.
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $trabajadores->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const periodoSelect = document.getElementById('periodo');
        const fechaPersonalizada = document.querySelectorAll('.fecha-personalizada');

        // Función para mostrar/ocultar campos de fecha personalizada
        function toggleFechaPersonalizada() {
            if (periodoSelect.value === 'mes_actual') {
                fechaPersonalizada.forEach(el => el.style.display = 'none');
            } else {
                fechaPersonalizada.forEach(el => el.style.display = 'block');
            }
        }

        // Inicializar
        toggleFechaPersonalizada();

        // Evento change
        periodoSelect.addEventListener('change', toggleFechaPersonalizada);
    });
</script>
@endsection
