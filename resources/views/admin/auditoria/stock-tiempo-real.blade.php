@extends('layouts.admin')

@section('content')
<div class="content-wrapper-scroll">
    <div class="main-header d-flex align-items-center justify-content-between position-relative">
        <div class="d-flex align-items-center justify-content-center">
            <div class="page-icon">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="page-title">
                <h5>Stock en Tiempo Real</h5>
            </div>
        </div>
        <div>
            <a href="{{ url('admin/auditoria') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Volver al Dashboard
            </a>
            <button class="btn btn-success" onclick="actualizarStock()">
                <i class="bi bi-arrow-clockwise"></i> Actualizar
            </button>
        </div>
    </div>

    <div class="content-wrapper">
        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="filtrosForm" class="row g-3">
                            <div class="col-md-3">
                                <label for="filtro_categoria" class="form-label">Categoría</label>
                                <select class="form-select" id="filtro_categoria" name="categoria">
                                    <option value="">Todas las categorías</option>
                                    <!-- Se puede poblar dinámicamente -->
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtro_stock" class="form-label">Estado de Stock</label>
                                <select class="form-select" id="filtro_stock" name="estado_stock">
                                    <option value="">Todos</option>
                                    <option value="negativo">Stock Negativo</option>
                                    <option value="bajo">Stock Bajo (≤10)</option>
                                    <option value="normal">Stock Normal</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtro_busqueda" class="form-label">Buscar Artículo</label>
                                <input type="text" class="form-control" id="filtro_busqueda" 
                                       placeholder="Código o nombre..." name="busqueda">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="button" class="btn btn-primary w-100" onclick="aplicarFiltros()">
                                    <i class="bi bi-search"></i> Filtrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen de Stock -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $reporteStock['estadisticas']['stock_negativo'] }}</h4>
                                <p class="mb-0">Stock Negativo</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $reporteStock['estadisticas']['stock_bajo'] }}</h4>
                                <p class="mb-0">Stock Bajo</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-exclamation-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $reporteStock['estadisticas']['stock_normal'] }}</h4>
                                <p class="mb-0">Stock Normal</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $reporteStock['estadisticas']['total_articulos'] }}</h4>
                                <p class="mb-0">Total Artículos</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-box fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Stock -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ul"></i> Inventario Detallado
                    </h5>
                    <div>
                        <button class="btn btn-sm btn-outline-success" onclick="exportarExcel()">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="exportarPDF()">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" id="tabla-stock">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Artículo</th>
                                <th>Stock Actual</th>
                                <th>Stock Teórico</th>
                                <th>Diferencia</th>
                                <th>Estado</th>
                                <th>Última Venta</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reporteStock['articulos'] as $item)
                                <tr class="{{ $item['stock_actual'] < 0 ? 'table-danger' : ($item['stock_actual'] <= 10 ? 'table-warning' : '') }}">
                                    <td>
                                        <strong>{{ $item['articulo']->codigo }}</strong>
                                    </td>
                                    <td>
                                        {{ $item['articulo']->nombre }}
                                        @if($item['articulo']->marca)
                                            <br><small class="text-muted">{{ $item['articulo']->marca }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $item['stock_actual'] < 0 ? 'bg-danger' : ($item['stock_actual'] <= 10 ? 'bg-warning' : 'bg-success') }}">
                                            {{ $item['stock_actual'] }}
                                        </span>
                                    </td>
                                    <td>{{ $item['stock_teorico'] }}</td>
                                    <td>
                                        @if($item['diferencia'] != 0)
                                            <span class="badge bg-danger">{{ $item['diferencia'] }}</span>
                                        @else
                                            <span class="badge bg-success">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['consistente'])
                                            <span class="badge bg-success">Consistente</span>
                                        @else
                                            <span class="badge bg-danger">Inconsistente</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['ultima_venta'])
                                            {{ $item['ultima_venta']->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">Sin ventas</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" 
                                                    onclick="verDetalleArticulo({{ $item['articulo']->id }})"
                                                    title="Ver detalles">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            @if(!$item['consistente'])
                                                <button class="btn btn-outline-warning" 
                                                        onclick="corregirStock({{ $item['articulo']->id }})"
                                                        title="Corregir stock">
                                                    <i class="bi bi-wrench"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de detalle de artículo -->
<div class="modal fade" id="detalleArticuloModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Artículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detalleArticuloContent">
                    <!-- Se llena con AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
function actualizarStock() {
    location.reload();
}

function aplicarFiltros() {
    const form = document.getElementById('filtrosForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    window.location.href = window.location.pathname + '?' + params.toString();
}

function verDetalleArticulo(articuloId) {
    fetch(`{{ url('admin/auditoria/articulo-detalle') }}/${articuloId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('detalleArticuloContent').innerHTML = data.html;
            new bootstrap.Modal(document.getElementById('detalleArticuloModal')).show();
        })
        .catch(error => {
            alert('Error al cargar detalle: ' + error.message);
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
                alert('Stock corregido exitosamente');
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

function exportarExcel() {
    window.open('{{ url("admin/auditoria/exportar-stock/excel") }}', '_blank');
}

function exportarPDF() {
    window.open('{{ url("admin/auditoria/exportar-stock/pdf") }}', '_blank');
}

// Auto-actualizar cada 5 minutos
setInterval(function() {
    if (confirm('¿Desea actualizar el reporte de stock?')) {
        actualizarStock();
    }
}, 300000); // 5 minutos
</script>
@endsection
