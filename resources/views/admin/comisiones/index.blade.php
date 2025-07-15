@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="card mt-4">
        <div class="card-header">
            <h4>Administración de Comisiones</h4>
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
                            <form action="{{ url('comisiones') }}" method="GET" class="row">
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
                                    <label for="tipo_receptor" class="form-label">Tipo de Receptor</label>
                                    <select name="tipo_receptor" id="tipo_receptor" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="trabajador" {{ request('tipo_receptor') == 'trabajador' ? 'selected' : '' }}>
                                            Trabajadores
                                        </option>
                                        <option value="usuario" {{ request('tipo_receptor') == 'usuario' ? 'selected' : '' }}>
                                            Vendedores
                                        </option>
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
                                    <a href="{{ url('comisiones') }}" class="btn btn-secondary ms-2">Limpiar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12 d-flex justify-content-between">
                    <h4>Listado de Comisiones</h4>
                    <div>
                        <a href="{{ url('comisiones/por-trabajador') }}" class="btn btn-info">
                            <i class="bi bi-people"></i> Ver por Trabajador
                        </a>
                        <a href="{{ url('comisiones/por-vendedor') }}" class="btn btn-success">
                            <i class="bi bi-person-tie"></i> Ver por Vendedor
                        </a>
                        <a href="{{ url('comisiones/resumen') }}" class="btn btn-warning">
                            <i class="bi bi-pie-chart"></i> Resumen
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Receptor</th>
                            <th>Monto</th>
                            <th>Venta</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($comisiones as $comision)
                            <tr>
                                <td>{{ $comision->id }}</td>
                                <td>{{ \Carbon\Carbon::parse($comision->fecha_calculo)->format('d/m/Y') }}</td>
                                <td>{{ ucfirst($comision->tipo_comision) }}</td>
                                <td>
                                    @if ($comision->commissionable_type == 'App\Models\Trabajador')
                                        <span class="badge bg-secondary">Trabajador</span>
                                        {{ $comision->commissionable->nombre }} {{ $comision->commissionable->apellido }}
                                    @elseif ($comision->commissionable_type == 'App\Models\User')
                                        <span class="badge bg-primary">Vendedor</span>
                                        {{ $comision->commissionable->name }}
                                    @endif
                                </td>
                                <td>Q. {{ number_format($comision->monto, 2) }}</td>
                                <td>
                                    @if ($comision->venta)
                                        <a href="{{ url('show-venta/'.$comision->venta_id) }}">
                                            #{{ $comision->venta_id }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
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
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No hay comisiones que coincidan con los filtros seleccionados</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $comisiones->appends(request()->query())->links() }}
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
