@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="card mt-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4>Resumen de Comisiones</h4>
                <div>
                    <a href="{{ url('comisiones') }}" class="btn btn-primary">
                        <i class="bi bi-list-check"></i> Ver Todas las Comisiones
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
                            <h5>Seleccionar Periodo</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('comisiones/resumen') }}" method="GET" class="row">
                                <div class="col-md-4">
                                    <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control"
                                        value="{{ $fechaInicio->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control"
                                        value="{{ $fechaFin->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-funnel"></i> Actualizar
                                    </button>
                                    <a href="{{ url('comisiones/resumen') }}" class="btn btn-secondary ms-2">
                                        <i class="bi bi-arrow-clockwise"></i> Mes Actual
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <h5 class="mb-1">Periodo: {{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }}</h5>
                        <p class="mb-0">Resumen de comisiones generadas en el periodo seleccionado.</p>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <!-- Gráfico de comisiones por tipo -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5>Comisiones por Tipo</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Tipo de Comisión</th>
                                            <th>Total</th>
                                            <th>Porcentaje</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalGlobal = $totalesPorTipo->sum('total');
                                        @endphp
                                        @forelse ($totalesPorTipo as $tipo)
                                            <tr>
                                                <td>{{ ucfirst($tipo->tipo_comision) }}</td>
                                                <td>{{ $config->currency_simbol }} {{ number_format($tipo->total, 2) }}</td>
                                                <td>
                                                    @if($totalGlobal > 0)
                                                        {{ number_format(($tipo->total / $totalGlobal) * 100, 2) }}%
                                                    @else
                                                        0%
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No hay datos para mostrar</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-bold">
                                            <td>Total</td>
                                            <td>{{ $config->currency_simbol }} {{ number_format($totalGlobal, 2) }}</td>
                                            <td>100%</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de comisiones por estado -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5>Comisiones por Estado</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Estado</th>
                                            <th>Total</th>
                                            <th>Porcentaje</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalEstados = $totalesPorEstado->sum('total');
                                        @endphp
                                        @forelse ($totalesPorEstado as $estado)
                                            <tr>
                                                <td>
                                                    @if ($estado->estado == 'pendiente')
                                                        <span class="badge bg-warning">Pendiente</span>
                                                    @elseif ($estado->estado == 'pagado')
                                                        <span class="badge bg-success">Pagado</span>
                                                    @elseif ($estado->estado == 'cancelado')
                                                        <span class="badge bg-danger">Cancelado</span>
                                                    @endif
                                                </td>
                                                <td>{{ $config->currency_simbol }} {{ number_format($estado->total, 2) }}</td>
                                                <td>
                                                    @if($totalEstados > 0)
                                                        {{ number_format(($estado->total / $totalEstados) * 100, 2) }}%
                                                    @else
                                                        0%
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No hay datos para mostrar</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-bold">
                                            <td>Total</td>
                                            <td>{{ $config->currency_simbol }} {{ number_format($totalEstados, 2) }}</td>
                                            <td>100%</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Top 5 Trabajadores -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5>Top 5 Trabajadores con más Comisiones</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Trabajador</th>
                                            <th>Tipo</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($topTrabajadores as $trabajador)
                                            <tr>
                                                <td>
                                                    <a href="{{ url('show-trabajador/'.$trabajador->id) }}">
                                                        {{ $trabajador->nombre }} {{ $trabajador->apellido }}
                                                    </a>
                                                </td>
                                                <td>{{ $trabajador->tipoTrabajador ? $trabajador->tipoTrabajador->nombre : 'Sin tipo' }}</td>
                                                <td>{{ $config->currency_simbol }} {{ number_format($trabajador->comisiones_sum_monto, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No hay datos para mostrar</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top 5 Vendedores -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5>Top 5 Vendedores con más Comisiones</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Vendedor</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($topVendedores as $vendedor)
                                            <tr>
                                                <td>
                                                    <a href="{{ url('show-user/'.$vendedor->id) }}">
                                                        {{ $vendedor->name }}
                                                    </a>
                                                </td>
                                                <td>{{ $config->currency_simbol }} {{ number_format($vendedor->comisiones_sum_monto, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center">No hay datos para mostrar</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 d-flex justify-content-center">
                    <div class="btn-group">
                        <a href="{{ url('comisiones') }}" class="btn btn-outline-primary">
                            <i class="bi bi-list-check"></i> Ver Todas las Comisiones
                        </a>
                        <a href="{{ url('comisiones/por-trabajador') }}" class="btn btn-outline-info">
                            <i class="bi bi-people"></i> Ver por Trabajador
                        </a>
                        <a href="{{ url('comisiones/por-vendedor') }}" class="btn btn-outline-success">
                            <i class="bi bi-person-tie"></i> Ver por Vendedor
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
