<div class="row">
    <div class="col-md-6">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-file-text"></i> Venta #{{ $venta1->id }}
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th width="30%">Cliente:</th>
                        <td>{{ $venta1->cliente->nombre ?? 'Sin cliente' }}</td>
                    </tr>
                    <tr>
                        <th>Fecha:</th>
                        <td>{{ \Carbon\Carbon::parse($venta1->fecha)->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Total:</th>
                        <td><strong>${{ number_format($venta1->total, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td>
                            @if($venta1->estado)
                                <span class="badge bg-success">Activa</span>
                            @else
                                <span class="badge bg-secondary">Anulada</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Creación:</th>
                        <td>{{ $venta1->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>
                
                <h6 class="mt-3 mb-2">Detalles de la Venta:</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Artículo</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venta1->detalleVenta as $detalle)
                            <tr>
                                <td>
                                    <small>
                                        <strong>{{ $detalle->articulo->codigo ?? 'N/A' }}</strong><br>
                                        {{ $detalle->articulo->nombre ?? 'Artículo eliminado' }}
                                    </small>
                                </td>
                                <td>{{ $detalle->cantidad }}</td>
                                <td>${{ number_format($detalle->precio, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-secondary">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-file-text"></i> Venta #{{ $venta2->id }}
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <th width="30%">Cliente:</th>
                        <td>{{ $venta2->cliente->nombre ?? 'Sin cliente' }}</td>
                    </tr>
                    <tr>
                        <th>Fecha:</th>
                        <td>{{ \Carbon\Carbon::parse($venta2->fecha)->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Total:</th>
                        <td><strong>${{ number_format($venta2->total, 2) }}</strong></td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td>
                            @if($venta2->estado)
                                <span class="badge bg-success">Activa</span>
                            @else
                                <span class="badge bg-secondary">Anulada</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Creación:</th>
                        <td>{{ $venta2->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>
                
                <h6 class="mt-3 mb-2">Detalles de la Venta:</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Artículo</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venta2->detalleVenta as $detalle)
                            <tr>
                                <td>
                                    <small>
                                        <strong>{{ $detalle->articulo->codigo ?? 'N/A' }}</strong><br>
                                        {{ $detalle->articulo->nombre ?? 'Artículo eliminado' }}
                                    </small>
                                </td>
                                <td>{{ $detalle->cantidad }}</td>
                                <td>${{ number_format($detalle->precio, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Análisis de Coincidencias -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle"></i> Análisis de Similitudes
                </h5>
            </div>
            <div class="card-body">
                @php
                    $coincidencias = [];
                    
                    // Analizar coincidencias de cliente
                    if($venta1->cliente_id === $venta2->cliente_id) {
                        $coincidencias[] = "Mismo cliente: " . ($venta1->cliente->nombre ?? 'Sin cliente');
                    }
                    
                    // Analizar coincidencias de fecha
                    $fecha1 = \Carbon\Carbon::parse($venta1->fecha);
                    $fecha2 = \Carbon\Carbon::parse($venta2->fecha);
                    if($fecha1->isSameDay($fecha2)) {
                        $coincidencias[] = "Misma fecha: " . $fecha1->format('d/m/Y');
                    }
                    
                    // Analizar coincidencias de artículos
                    $articulos1 = $venta1->detalleVenta->pluck('articulo_id')->toArray();
                    $articulos2 = $venta2->detalleVenta->pluck('articulo_id')->toArray();
                    $articulosComunes = array_intersect($articulos1, $articulos2);
                    
                    if(count($articulosComunes) > 0) {
                        $coincidencias[] = "Artículos en común: " . count($articulosComunes);
                    }
                    
                    // Analizar total similar
                    $diferenciaPorcentaje = abs($venta1->total - $venta2->total) / max($venta1->total, $venta2->total) * 100;
                    if($diferenciaPorcentaje < 5) {
                        $coincidencias[] = "Totales similares (diferencia < 5%)";
                    }
                @endphp
                
                @if(count($coincidencias) > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($coincidencias as $coincidencia)
                            <li class="list-group-item d-flex align-items-center">
                                <i class="bi bi-check-circle text-warning me-2"></i>
                                {{ $coincidencia }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        No se encontraron similitudes significativas entre estas ventas.
                    </div>
                @endif
                
                @if(count($articulosComunes) > 0)
                    <div class="mt-3">
                        <h6>Artículos en Común:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Artículo</th>
                                        <th>Cantidad Venta #{{ $venta1->id }}</th>
                                        <th>Cantidad Venta #{{ $venta2->id }}</th>
                                        <th>Diferencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($articulosComunes as $articuloId)
                                        @php
                                            $detalle1 = $venta1->detalleVenta->where('articulo_id', $articuloId)->first();
                                            $detalle2 = $venta2->detalleVenta->where('articulo_id', $articuloId)->first();
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $detalle1->articulo->codigo ?? 'N/A' }}</strong><br>
                                                <small>{{ $detalle1->articulo->nombre ?? 'N/A' }}</small>
                                            </td>
                                            <td>{{ $detalle1->cantidad ?? 0 }}</td>
                                            <td>{{ $detalle2->cantidad ?? 0 }}</td>
                                            <td>
                                                @php $diff = abs(($detalle1->cantidad ?? 0) - ($detalle2->cantidad ?? 0)); @endphp
                                                <span class="badge {{ $diff == 0 ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $diff }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
