<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Información del Artículo</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="30%">Código:</th>
                        <td><strong>{{ $articulo->codigo }}</strong></td>
                    </tr>
                    <tr>
                        <th>Nombre:</th>
                        <td>{{ $articulo->nombre }}</td>
                    </tr>
                    @if($articulo->marca)
                    <tr>
                        <th>Marca:</th>
                        <td>{{ $articulo->marca }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Stock Actual:</th>
                        <td>
                            <span class="badge {{ $articulo->stock < 0 ? 'bg-danger' : ($articulo->stock <= 10 ? 'bg-warning' : 'bg-success') }} fs-6">
                                {{ $articulo->stock }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Stock Teórico:</th>
                        <td>{{ $consistencia['stock_teorico'] }}</td>
                    </tr>
                    <tr>
                        <th>Diferencia:</th>
                        <td>
                            @if($consistencia['diferencia'] != 0)
                                <span class="badge bg-danger">{{ $consistencia['diferencia'] }}</span>
                            @else
                                <span class="badge bg-success">0</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td>
                            @if($consistencia['consistente'])
                                <span class="badge bg-success">Consistente</span>
                            @else
                                <span class="badge bg-danger">Inconsistente</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">Ventas Recientes (7 días)</h5>
            </div>
            <div class="card-body">
                @if($ventasRecientes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Venta ID</th>
                                    <th>Cliente</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ventasRecientes as $detalle)
                                <tr>
                                    <td>{{ $detalle->created_at->format('d/m/Y H:i') }}</td>
                                    <td><a href="{{ url('admin/venta/' . $detalle->venta->id) }}" target="_blank">#{{ $detalle->venta->id }}</a></td>
                                    <td>{{ $detalle->venta->cliente->nombre ?? 'Sin cliente' }}</td>
                                    <td>{{ $detalle->cantidad }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No hay ventas recientes en los últimos 7 días
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(!$consistencia['consistente'])
<div class="alert alert-warning mt-3">
    <i class="bi bi-exclamation-triangle"></i>
    <strong>Inconsistencia detectada:</strong> El stock actual ({{ $articulo->stock }}) no coincide con el stock teórico ({{ $consistencia['stock_teorico'] }}) calculado en base a las ventas registradas.
</div>
@endif
