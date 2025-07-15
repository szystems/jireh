@if($movimientos->count() > 0)
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Venta ID</th>
                    <th>Cliente</th>
                    <th>Cantidad</th>
                    <th>Tipo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movimientos as $detalle)
                <tr>
                    <td>{{ $detalle->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ url('admin/venta/' . $detalle->venta->id) }}" target="_blank" class="text-decoration-none">
                            <strong>#{{ $detalle->venta->id }}</strong>
                        </a>
                    </td>
                    <td>{{ $detalle->venta->cliente->nombre ?? 'Sin cliente' }}</td>
                    <td>
                        <span class="badge bg-primary">{{ $detalle->cantidad }}</span>
                    </td>
                    <td>
                        @if($detalle->venta->estado)
                            <span class="badge bg-success">Venta</span>
                        @else
                            <span class="badge bg-secondary">Anulada</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ url('admin/venta/' . $detalle->venta->id) }}" target="_blank" 
                           class="btn btn-sm btn-outline-primary" title="Ver venta">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="alert alert-info mt-3">
        <i class="bi bi-info-circle"></i>
        <strong>Resumen:</strong> Se muestran los últimos 20 movimientos de este artículo. 
        Total de movimientos mostrados: {{ $movimientos->count() }}
    </div>
@else
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i>
        <strong>Sin movimientos:</strong> No se encontraron movimientos para este artículo en el período consultado.
    </div>
@endif
