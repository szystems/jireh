{{-- Vista para mostrar detalles de meta de ventas en modal --}}

<div class="container-fluid">
    <!-- Información general de la meta -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-trophy"></i> 
                        Información de la Meta
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Vendedor:</strong><br>
                            <span class="text-primary">{{ $comision->commissionable->name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Meta Alcanzada:</strong><br>
                            <span class="badge bg-{{ $resumenMeta['meta_info']['color'] ?? 'secondary' }} fs-6">
                                {{ $resumenMeta['meta_info']['nombre'] ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="col-md-3">
                            <strong>Período:</strong><br>
                            <span class="text-info">{{ $resumenMeta['periodo'] ?? 'N/A' }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>Comisión:</strong><br>
                            <span class="text-success h6">{{ $config->currency_simbol }}{{ number_format($comision->monto, 2) }}</span>
                            <small class="text-muted">({{ $comision->porcentaje }}%)</small>
                        </div>
                    </div>
                    
                    @if(isset($resumenMeta['fecha_inicio']))
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <strong>Período de ventas:</strong><br>
                            <span class="text-muted">
                                <i class="bi bi-calendar-range"></i> 
                                {{ $resumenMeta['fecha_inicio'] }} - {{ $resumenMeta['fecha_fin'] }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <strong>Resumen:</strong><br>
                            <span class="text-muted">
                                <i class="bi bi-cart-check"></i> 
                                {{ $resumenMeta['cantidad_ventas'] }} ventas - 
                                {{ $config->currency_simbol }}{{ number_format($resumenMeta['total_vendido'], 2) }}
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de ventas -->
    <div class="row">
        <div class="col-md-12">
            @if($ventas->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-list-ul"></i> 
                            Ventas que conforman la meta ({{ $ventas->count() }})
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="10%">Fecha</th>
                                        <th width="15%">Venta #</th>
                                        <th width="25%">Cliente</th>
                                        <th width="15%">Productos</th>
                                        <th width="15%">Subtotal</th>
                                        <th width="10%">Estado</th>
                                        <th width="10%">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ventas as $venta)
                                        @php
                                            $subtotalVenta = $venta->detalleVentas->sum('sub_total');
                                        @endphp
                                        <tr>
                                            <td>
                                                <small>{{ $venta->fecha->format('d/m/Y') }}</small>
                                            </td>
                                            <td>
                                                <strong class="text-primary">#{{ $venta->id }}</strong>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold">{{ $venta->cliente->nombre ?? 'N/A' }}</span>
                                                    @if($venta->cliente && $venta->cliente->telefono)
                                                        <small class="text-muted">{{ $venta->cliente->telefono }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $venta->detalleVentas->count() }} 
                                                    {{ $venta->detalleVentas->count() == 1 ? 'producto' : 'productos' }}
                                                </small>
                                            </td>
                                            <td>
                                                <strong class="text-success">
                                                    {{ $config->currency_simbol }}{{ number_format($subtotalVenta, 2) }}
                                                </strong>
                                            </td>
                                            <td>
                                                @if($venta->estado == 1)
                                                    <span class="badge bg-success">Activa</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactiva</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('ventas.show', $venta->id) }}" 
                                                   target="_blank" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Ver detalles de la venta">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="4" class="text-end">Total de ventas:</th>
                                        <th class="text-success">
                                            {{ $config->currency_simbol }}{{ number_format($resumenMeta['total_vendido'], 2) }}
                                        </th>
                                        <th colspan="2"></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-end">Comisión por meta ({{ $comision->porcentaje }}%):</th>
                                        <th class="text-primary">
                                            {{ $config->currency_simbol }}{{ number_format($comision->monto, 2) }}
                                        </th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    <strong>Sin ventas registradas</strong><br>
                    No se encontraron ventas para el período de esta meta.
                </div>
            @endif
        </div>
    </div>
</div>
