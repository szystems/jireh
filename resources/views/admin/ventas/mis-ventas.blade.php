@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-graph-up-arrow text-primary"></i>
            Mis Ventas
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.pro') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Mis Ventas</li>
            </ol>
        </nav>
    </div>

    <!-- Métricas Personales -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-currency-dollar fs-1 text-primary me-2"></i>
                        <div>
                            <h4 class="card-title mb-0">Total Mis Ventas</h4>
                            <h2 class="text-primary mb-0">{{ $config->currency_simbol }}{{ number_format($totalVentas, 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-calendar-month fs-1 text-success me-2"></i>
                        <div>
                            <h4 class="card-title mb-0">Este Mes</h4>
                            <h2 class="text-success mb-0">{{ $config->currency_simbol }}{{ number_format($ventasEstesMes, 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-funnel"></i>
                Filtros
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('vendedor.mis_ventas') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="fecha_desde" class="form-label">Fecha Desde</label>
                        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" 
                               value="{{ request('fecha_desde', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" 
                               value="{{ request('fecha_hasta', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="numero_factura" class="form-label">Número Factura</label>
                        <input type="text" class="form-control" id="numero_factura" name="numero_factura" 
                               value="{{ request('numero_factura') }}" placeholder="Buscar por factura...">
                    </div>
                    <div class="col-md-3">
                        <label for="cliente" class="form-label">Cliente</label>
                        <input type="text" class="form-control" id="cliente" name="cliente" 
                               value="{{ request('cliente') }}" placeholder="Buscar por cliente...">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <a href="{{ route('vendedor.mis_ventas') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Ventas -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-list-ul"></i>
                Mis Ventas Recientes
                <span class="badge bg-primary ms-2">{{ $ventas->total() }} ventas</span>
            </h5>
        </div>
        <div class="card-body">
            @if($ventas->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>Factura #</th>
                                <th>Cliente</th>
                                <th>Vehículo</th>
                                <th>Total</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ventas as $venta)
                            <tr>
                                <td>
                                    <i class="bi bi-calendar3 text-muted"></i>
                                    {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $venta->numero_factura }}</span>
                                </td>
                                <td>
                                    <i class="bi bi-person text-primary"></i>
                                    {{ $venta->cliente->nombre ?? 'Sin cliente' }}
                                </td>
                                <td>
                                    @if($venta->vehiculo)
                                        <i class="bi bi-car-front text-success"></i>
                                        {{ $venta->vehiculo->placa }} ({{ $venta->vehiculo->modelo }})
                                    @else
                                        <span class="text-muted">Sin vehículo</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-success">
                                        {{ $config->currency_simbol }}{{ number_format($venta->detalleVentas->sum('sub_total'), 2) }}
                                    </strong>
                                </td>
                                <td>
                                    @if($venta->estado)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Activa
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Inactiva
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" data-bs-target="#detalleModal{{ $venta->id }}">
                                            <i class="bi bi-eye"></i> Ver
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $ventas->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">No tienes ventas registradas</h4>
                    <p class="text-muted">Con los filtros aplicados no se encontraron ventas.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modales de Detalle (fuera de la tabla) -->
@if($ventas->count() > 0)
    @foreach($ventas as $venta)
    <div class="modal fade" id="detalleModal{{ $venta->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-receipt"></i>
                        Detalle de Venta #{{ $venta->numero_factura }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}
                        </div>
                        <div class="col-md-6">
                            <strong>Cliente:</strong> {{ $venta->cliente->nombre ?? 'Sin cliente' }}
                        </div>
                    </div>
                    
                    <h6>Productos Vendidos:</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venta->detalleVentas as $detalle)
                                <tr>
                                    <td>{{ $detalle->articulo->nombre ?? 'Producto eliminado' }}</td>
                                    <td>{{ $detalle->cantidad }}</td>
                                    <td>{{ $config->currency_simbol }}{{ number_format($detalle->precio, 2) }}</td>
                                    <td><strong>{{ $config->currency_simbol }}{{ number_format($detalle->sub_total, 2) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-end mt-3">
                        <h5>
                            <strong>Total: {{ $config->currency_simbol }}{{ number_format($venta->detalleVentas->sum('sub_total'), 2) }}</strong>
                        </h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endif
@endsection
