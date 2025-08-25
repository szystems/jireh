@extends('layouts.admin')

@section('content')
<div class="content-wrapper-scroll">
    <div class="main-header d-flex align-items-center justify-content-between position-relative">
        <div class="d-flex align-items-center justify-content-center">
            <div class="page-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="page-title">
                <h5>Alertas de Stock</h5>
            </div>
        </div>
        <div>
            <a href="{{ url('admin/auditoria') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Volver al Dashboard
            </a>
            <button class="btn btn-danger" onclick="enviarNotificacionesUrgentes()">
                <i class="bi bi-bell"></i> Notificar Urgentes
            </button>
        </div>
    </div>

    <div class="content-wrapper">
        <!-- Resumen de Alertas -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="mb-0">{{ collect($alertas)->where('tipo_alerta', 'CRITICA')->count() }}</h3>
                                <p class="mb-0">Alertas Críticas</p>
                                <small>Stock negativo</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-exclamation-triangle fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="mb-0">{{ collect($alertas)->where('tipo_alerta', 'ADVERTENCIA')->count() }}</h3>
                                <p class="mb-0">Advertencias</p>
                                <small>Stock bajo</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-exclamation-circle fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="mb-0">{{ collect($alertas)->where('consistente', false)->count() }}</h3>
                                <p class="mb-0">Inconsistencias</p>
                                <small>Stock vs ventas</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-bug fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertas Críticas -->
        @if(collect($alertas)->where('tipo_alerta', 'CRITICA')->count() > 0)
        <div class="card border-danger mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle"></i> Alertas Críticas - Stock Negativo
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Artículo</th>
                                <th>Categoría</th>
                                <th>Stock Actual</th>
                                <th>Stock Teórico</th>
                                <th>Diferencia</th>
                                <th>Ventas Recientes</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(collect($alertas)->where('tipo_alerta', 'CRITICA') as $alerta)
                                <tr class="table-danger">
                                    <td>
                                        <strong>{{ $alerta['articulo']->codigo }}</strong><br>
                                        {{ $alerta['articulo']->nombre }}
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $alerta['articulo']->categoria->nombre ?? 'Sin categoría' }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger fs-6">{{ $alerta['stock_actual'] }}</span>
                                    </td>
                                    <td>{{ $alerta['stock_teorico'] }}</td>
                                    <td>
                                        <span class="badge bg-danger">{{ $alerta['diferencia'] }}</span>
                                    </td>
                                    <td>
                                        @if($alerta['ventas_recientes']->count() > 0)
                                            <small>
                                                {{ $alerta['ventas_recientes']->count() }} ventas en 7 días<br>
                                                Última: {{ $alerta['ventas_recientes']->first()->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        @else
                                            <span class="text-muted">Sin ventas recientes</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-info" 
                                                    onclick="verHistorial({{ $alerta['articulo']->id }})"
                                                    title="Ver historial">
                                                <i class="bi bi-clock-history"></i>
                                            </button>
                                            <button class="btn btn-outline-warning" 
                                                    onclick="corregirStock({{ $alerta['articulo']->id }})"
                                                    title="Corregir stock">
                                                <i class="bi bi-wrench"></i>
                                            </button>
                                            <button class="btn btn-outline-success" 
                                                    onclick="ajusteManual({{ $alerta['articulo']->id }})"
                                                    title="Ajuste manual">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Alertas de Advertencia -->
        @if(collect($alertas)->where('tipo_alerta', 'ADVERTENCIA')->count() > 0)
        <div class="card border-warning mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="bi bi-exclamation-circle"></i> Advertencias - Stock Bajo
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Artículo</th>
                                <th>Categoría</th>
                                <th>Stock Actual</th>
                                <th>Stock Teórico</th>
                                <th>Consistente</th>
                                <th>Ventas Recientes</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(collect($alertas)->where('tipo_alerta', 'ADVERTENCIA') as $alerta)
                                <tr class="table-warning">
                                    <td>
                                        <strong>{{ $alerta['articulo']->codigo }}</strong><br>
                                        {{ $alerta['articulo']->nombre }}
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $alerta['articulo']->categoria->nombre ?? 'Sin categoría' }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $alerta['stock_actual'] }}</span>
                                    </td>
                                    <td>{{ $alerta['stock_teorico'] }}</td>
                                    <td>
                                        @if($alerta['consistente'])
                                            <span class="badge bg-success">Sí</span>
                                        @else
                                            <span class="badge bg-danger">No ({{ $alerta['diferencia'] }})</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($alerta['ventas_recientes']->count() > 0)
                                            <small>
                                                {{ $alerta['ventas_recientes']->count() }} ventas en 7 días<br>
                                                Última: {{ $alerta['ventas_recientes']->first()->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        @else
                                            <span class="text-muted">Sin ventas recientes</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-info" 
                                                    onclick="verHistorial({{ $alerta['articulo']->id }})"
                                                    title="Ver historial">
                                                <i class="bi bi-clock-history"></i>
                                            </button>
                                            @if(!$alerta['consistente'])
                                                <button class="btn btn-outline-warning" 
                                                        onclick="corregirStock({{ $alerta['articulo']->id }})"
                                                        title="Corregir stock">
                                                    <i class="bi bi-wrench"></i>
                                                </button>
                                            @endif
                                            <button class="btn btn-outline-success" 
                                                    onclick="reabastecer({{ $alerta['articulo']->id }})"
                                                    title="Reabastecer">
                                                <i class="bi bi-plus-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if(collect($alertas)->count() == 0)
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-success">¡Excelente!</h4>
                <p class="text-muted">No hay alertas de stock en este momento. Todos los artículos tienen stock suficiente.</p>
                <a href="{{ url('admin/auditoria') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal de Historial -->
<div class="modal fade" id="historialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Historial de Movimientos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="historialContent">
                    <!-- Se llena con AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Ajuste Manual -->
<div class="modal fade" id="ajusteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajuste Manual de Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="ajusteForm">
                    <input type="hidden" id="ajuste_articulo_id" name="articulo_id">
                    <div class="mb-3">
                        <label for="nuevo_stock" class="form-label">Nuevo Stock:</label>
                        <input type="number" class="form-control" id="nuevo_stock" name="nuevo_stock" required>
                    </div>
                    <div class="mb-3">
                        <label for="motivo_ajuste" class="form-label">Motivo del Ajuste:</label>
                        <textarea class="form-control" id="motivo_ajuste" name="motivo" rows="3" required 
                                  placeholder="Ej: Inventario físico, corrección de error, etc."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="realizarAjuste()">
                    <i class="bi bi-check"></i> Aplicar Ajuste
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function verHistorial(articuloId) {
    fetch(`{{ url('admin/auditoria/historial-movimientos') }}/${articuloId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('historialContent').innerHTML = data.html;
            new bootstrap.Modal(document.getElementById('historialModal')).show();
        })
        .catch(error => {
            alert('Error al cargar historial: ' + error.message);
        });
}

function corregirStock(articuloId) {
    if (confirm('¿Está seguro de corregir el stock de este artículo basado en las ventas registradas?')) {
        fetch(`{{ url('admin/auditoria/corregir-stock') }}/${articuloId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Stock corregido exitosamente: ' + data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error de conexión: ' + error.message);
        });
    }
}

function ajusteManual(articuloId) {
    document.getElementById('ajuste_articulo_id').value = articuloId;
    document.getElementById('nuevo_stock').value = '';
    document.getElementById('motivo_ajuste').value = '';
    new bootstrap.Modal(document.getElementById('ajusteModal')).show();
}

function realizarAjuste() {
    const form = document.getElementById('ajusteForm');
    const formData = new FormData(form);
    
    fetch('{{ url("admin/auditoria/ajuste-manual") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Ajuste realizado exitosamente');
            bootstrap.Modal.getInstance(document.getElementById('ajusteModal')).hide();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error de conexión: ' + error.message);
    });
}

function reabastecer(articuloId) {
    const cantidad = prompt('¿Cuántas unidades desea agregar al inventario?');
    if (cantidad && parseInt(cantidad) > 0) {
        fetch(`{{ url('admin/auditoria/reabastecer') }}/${articuloId}`, {
            method: 'POST',
            body: JSON.stringify({ cantidad: parseInt(cantidad) }),
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Reabastecimiento registrado exitosamente');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error de conexión: ' + error.message);
        });
    }
}

function enviarNotificacionesUrgentes() {
    const alertasCriticas = {{ collect($alertas)->where('tipo_alerta', 'CRITICA')->count() }};
    
    if (alertasCriticas === 0) {
        alert('No hay alertas críticas para notificar');
        return;
    }
    
    if (confirm(`¿Enviar notificaciones de ${alertasCriticas} alertas críticas?`)) {
        fetch('{{ url("admin/auditoria/enviar-notificaciones") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Notificaciones enviadas exitosamente');
            } else {
                alert('Error al enviar notificaciones: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error de conexión: ' + error.message);
        });
    }
}
</script>
@endsection
