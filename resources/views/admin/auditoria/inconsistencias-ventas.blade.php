@extends('layouts.admin')

@section('content')
<div class="content-wrapper-scroll">
    <div class="main-header d-flex align-items-center justify-content-between position-relative">
        <div class="d-flex align-items-center justify-content-center">
            <div class="page-icon">
                <i class="bi bi-bug"></i>
            </div>
            <div class="page-title">
                <h5>Inconsistencias de Ventas</h5>
                <small class="text-muted">Últimos {{ $dias }} días</small>
            </div>
        </div>
        <div>
            <a href="{{ url('admin/auditoria') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Volver al Dashboard
            </a>
            <div class="btn-group">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-calendar"></i> {{ $dias }} días
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="?dias=1">1 día</a></li>
                    <li><a class="dropdown-item" href="?dias=7">7 días</a></li>
                    <li><a class="dropdown-item" href="?dias=15">15 días</a></li>
                    <li><a class="dropdown-item" href="?dias=30">30 días</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <!-- Resumen de Inconsistencias -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="mb-0">{{ collect($inconsistencias)->where('tipo', 'DETALLE_SOSPECHOSO')->count() }}</h3>
                                <p class="mb-0">Detalles Sospechosos</p>
                                <small>Cantidades anómalas</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-exclamation-triangle fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h3 class="mb-0">{{ collect($inconsistencias)->where('tipo', 'VENTA_DUPLICADA')->count() }}</h3>
                                <p class="mb-0">Ventas Duplicadas</p>
                                <small>Posibles duplicaciones</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-files fa-3x"></i>
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
                                <h3 class="mb-0">{{ count($inconsistencias) }}</h3>
                                <p class="mb-0">Total Inconsistencias</p>
                                <small>En período seleccionado</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-list-check fa-3x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(count($inconsistencias) == 0)
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-success">¡Excelente!</h4>
                <p class="text-muted">No se encontraron inconsistencias en las ventas de los últimos {{ $dias }} días.</p>
                <a href="{{ url('admin/auditoria') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Volver al Dashboard
                </a>
            </div>
        </div>
        @else
        
        <!-- Detalles Sospechosos -->
        @if(collect($inconsistencias)->where('tipo', 'DETALLE_SOSPECHOSO')->count() > 0)
        <div class="card border-warning mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle"></i> Detalles de Venta Sospechosos
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Venta</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Artículo</th>
                                <th>Cantidad</th>
                                <th>Problema</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(collect($inconsistencias)->where('tipo', 'DETALLE_SOSPECHOSO') as $inconsistencia)
                                <tr class="table-warning">
                                    <td>
                                        <strong>#{{ $inconsistencia['detalle']->venta->id }}</strong>
                                    </td>
                                    <td>
                                        {{ $inconsistencia['detalle']->venta->cliente->nombre ?? 'Sin cliente' }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($inconsistencia['detalle']->venta->fecha)->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        @if($inconsistencia['detalle']->articulo)
                                            <strong>{{ $inconsistencia['detalle']->articulo->codigo }}</strong><br>
                                            {{ $inconsistencia['detalle']->articulo->nombre }}
                                        @else
                                            <span class="text-danger">Artículo no válido</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $inconsistencia['detalle']->cantidad }}</span>
                                    </td>
                                    <td>
                                        <small class="text-danger">{{ $inconsistencia['problema'] }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ url('admin/venta/' . $inconsistencia['detalle']->venta->id) }}" 
                                               class="btn btn-outline-primary" target="_blank" title="Ver venta">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button class="btn btn-outline-warning" 
                                                    onclick="corregirDetalle({{ $inconsistencia['detalle']->id }})"
                                                    title="Corregir detalle">
                                                <i class="bi bi-wrench"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" 
                                                    onclick="eliminarDetalle({{ $inconsistencia['detalle']->id }})"
                                                    title="Eliminar detalle">
                                                <i class="bi bi-trash"></i>
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

        <!-- Ventas Duplicadas -->
        @if(collect($inconsistencias)->where('tipo', 'VENTA_DUPLICADA')->count() > 0)
        <div class="card border-danger mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-files"></i> Posibles Ventas Duplicadas
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Ventas Relacionadas</th>
                                <th>Cliente</th>
                                <th>Fecha</th>
                                <th>Coincidencias</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(collect($inconsistencias)->where('tipo', 'VENTA_DUPLICADA') as $inconsistencia)
                                <tr class="table-danger">
                                    <td>
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-primary">#{{ $inconsistencia['venta1']->id }}</span>
                                            <span class="badge bg-secondary">#{{ $inconsistencia['venta2']->id }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $inconsistencia['venta1']->cliente->nombre ?? 'Sin cliente' }}
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($inconsistencia['venta1']->fecha)->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $inconsistencia['coincidencias'] }} elementos</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-info" 
                                                    onclick="compararVentas({{ $inconsistencia['venta1']->id }}, {{ $inconsistencia['venta2']->id }})"
                                                    title="Comparar ventas">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-warning" 
                                                    onclick="fusionarVentas({{ $inconsistencia['venta1']->id }}, {{ $inconsistencia['venta2']->id }})"
                                                    title="Fusionar ventas">
                                                <i class="bi bi-arrow-down-up"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" 
                                                    onclick="eliminarVentaDuplicada({{ $inconsistencia['venta2']->id }})"
                                                    title="Eliminar duplicada">
                                                <i class="bi bi-trash"></i>
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

        @endif
    </div>
</div>

<!-- Modal de Comparación de Ventas -->
<div class="modal fade" id="comparacionModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Comparación de Ventas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="comparacionContent">
                    <!-- Se llena con AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Corrección de Detalle -->
<div class="modal fade" id="correccionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Corregir Detalle de Venta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="correccionForm">
                    <input type="hidden" id="detalle_id" name="detalle_id">
                    <div class="mb-3">
                        <label for="nueva_cantidad" class="form-label">Nueva Cantidad:</label>
                        <input type="number" class="form-control" id="nueva_cantidad" name="cantidad" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="motivo_correccion" class="form-label">Motivo de la Corrección:</label>
                        <textarea class="form-control" id="motivo_correccion" name="motivo" rows="3" required
                                  placeholder="Explique el motivo de la corrección..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="aplicarCorreccion()">
                    <i class="bi bi-check"></i> Aplicar Corrección
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function compararVentas(venta1Id, venta2Id) {
    fetch(`{{ url('admin/auditoria/comparar-ventas') }}/${venta1Id}/${venta2Id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('comparacionContent').innerHTML = data.html;
            new bootstrap.Modal(document.getElementById('comparacionModal')).show();
        })
        .catch(error => {
            alert('Error al cargar comparación: ' + error.message);
        });
}

function corregirDetalle(detalleId) {
    document.getElementById('detalle_id').value = detalleId;
    document.getElementById('nueva_cantidad').value = '';
    document.getElementById('motivo_correccion').value = '';
    new bootstrap.Modal(document.getElementById('correccionModal')).show();
}

function aplicarCorreccion() {
    const form = document.getElementById('correccionForm');
    const formData = new FormData(form);
    
    fetch('{{ url("admin/auditoria/corregir-detalle") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Corrección aplicada exitosamente');
            bootstrap.Modal.getInstance(document.getElementById('correccionModal')).hide();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error de conexión: ' + error.message);
    });
}

function eliminarDetalle(detalleId) {
    if (confirm('¿Está seguro de eliminar este detalle de venta? Esta acción no se puede deshacer.')) {
        fetch(`{{ url('admin/auditoria/eliminar-detalle') }}/${detalleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Detalle eliminado exitosamente');
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

function fusionarVentas(venta1Id, venta2Id) {
    if (confirm('¿Está seguro de fusionar estas ventas? Los detalles de la segunda venta se moverán a la primera y la segunda venta será eliminada.')) {
        fetch(`{{ url('admin/auditoria/fusionar-ventas') }}/${venta1Id}/${venta2Id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Ventas fusionadas exitosamente');
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

function eliminarVentaDuplicada(ventaId) {
    if (confirm('¿Está seguro de eliminar esta venta duplicada? Esta acción no se puede deshacer.')) {
        fetch(`{{ url('admin/auditoria/eliminar-venta') }}/${ventaId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Venta eliminada exitosamente');
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
</script>
@endsection
