@extends('layouts.admin')

@section('content')
<div class="content-wrapper-scroll">
    <div class="main-header d-flex align-items-center justify-content-between position-relative">
        <div class="d-flex align-items-center justify-content-center">
            <div class="page-icon">
                <i class="bi bi-credit-card"></i>
            </div>
            <div class="page-title">
                <h5>Gestión de Pagos</h5>
            </div>
        </div>
    </div>
    <div class="content-wrapper">
        <!-- Estadísticas de pagos -->
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <div class="fs-5 text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="fs-6 fw-bold">Total Pagos</div>
                        <div class="fs-4 text-success">{{ $totalPagos }}</div>
                        <div class="small text-muted">registrados</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <div class="fs-5 text-primary">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div class="fs-6 fw-bold">Monto Total</div>
                        <div class="fs-4 text-primary">{{ $config->currency_simbol }}.{{ number_format($montoTotal, 2, '.', ',') }}</div>
                        <div class="small text-muted">recaudado</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <div class="fs-5 text-info">
                            <i class="bi bi-calendar-week"></i>
                        </div>
                        <div class="fs-6 fw-bold">Esta Semana</div>
                        <div class="fs-4 text-info">{{ $config->currency_simbol }}.{{ number_format($montoSemana, 2, '.', ',') }}</div>
                        <div class="small text-muted">recaudado</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card mb-3">
                    <div class="card-body text-center">
                        <div class="fs-5 text-warning">
                            <i class="bi bi-calendar-month"></i>
                        </div>
                        <div class="fs-6 fw-bold">Este Mes</div>
                        <div class="fs-4 text-warning">{{ $config->currency_simbol }}.{{ number_format($montoMes, 2, '.', ',') }}</div>
                        <div class="small text-muted">recaudado</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-funnel me-2"></i>Filtros de Búsqueda
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ url('pagos') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="fecha_desde" class="form-label">Fecha Desde</label>
                        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="metodo_pago" class="form-label">Método de Pago</label>
                        <select class="form-select" id="metodo_pago" name="metodo_pago">
                            <option value="">Todos los métodos</option>
                            <option value="efectivo" {{ request('metodo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                            <option value="tarjeta_credito" {{ request('metodo_pago') == 'tarjeta_credito' ? 'selected' : '' }}>Tarjeta de Crédito</option>
                            <option value="tarjeta_debito" {{ request('metodo_pago') == 'tarjeta_debito' ? 'selected' : '' }}>Tarjeta de Débito</option>
                            <option value="transferencia" {{ request('metodo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                            <option value="cheque" {{ request('metodo_pago') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                        <a href="{{ url('pagos') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de pagos -->
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-white">Lista de Pagos</h5>
                <div>
                    <a href="{{ url('pagos/export-pdf') }}?{{ http_build_query(request()->all()) }}" target="_blank" class="btn btn-light btn-sm">
                        <i class="bi bi-file-earmark-pdf"></i> PDF
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($pagos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Venta</th>
                                    <th>Cliente</th>
                                    <th>Monto</th>
                                    <th>Método</th>
                                    <th>Referencia</th>
                                    <th>Registrado por</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pagos as $pago)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ url('show-venta/'.$pago->venta_id) }}" class="text-decoration-none">
                                                {{ $pago->venta->numero_factura ?? 'Venta #'.$pago->venta_id }}
                                            </a>
                                        </td>
                                        <td>{{ $pago->venta->cliente->nombre ?? 'N/A' }}</td>
                                        <td class="fw-bold text-success">{{ $config->currency_simbol }}.{{ number_format($pago->monto, 2, '.', ',') }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $pago->nombre_metodo_pago }}</span>
                                        </td>
                                        <td>{{ $pago->referencia ?: 'N/A' }}</td>
                                        <td>{{ $pago->usuario->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                @if($pago->comprobante_imagen)
                                                    <a href="{{ asset($pago->comprobante_imagen) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Ver comprobante">
                                                        <i class="bi bi-file-earmark-image"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ url('show-venta/'.$pago->venta_id) }}" class="btn btn-sm btn-outline-info" title="Ver venta">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th colspan="3">Total en página:</th>
                                    <th class="text-success">{{ $config->currency_simbol }}.{{ number_format($pagos->sum('monto'), 2, '.', ',') }}</th>
                                    <th colspan="4"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $pagos->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        No se encontraron pagos con los criterios especificados.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
