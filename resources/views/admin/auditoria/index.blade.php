@extends('layouts.admin')

@section('content')
<div class="content-wrapper-scroll">
    <div class="main-header d-flex align-items-center justify-content-between position-relative">
        <div class="d-flex align-items-center justify-content-center">
            <div class="page-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="page-title">
                <h5>Auditoría de Ventas e Inventario</h5>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <!-- Estadísticas Generales -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $estadisticas['ventas_ultimos_30_dias'] }}</h4>
                                <p class="mb-0">Ventas (30 días)</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-graph-up-arrow fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $estadisticas['articulos_stock_negativo'] }}</h4>
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
                                <h4 class="mb-0">{{ $estadisticas['articulos_stock_bajo'] }}</h4>
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
                                <h4 class="mb-0">{{ $estadisticas['ventas_hoy'] }}</h4>
                                <p class="mb-0">Ventas Hoy</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-calendar-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Auditorías y Reportes</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <button class="btn btn-primary w-100" onclick="mostrarModalAuditoria()">
                                    <i class="bi bi-search"></i> Ejecutar Auditoría Completa
                                </button>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ url('admin/auditoria/stock-tiempo-real') }}" class="btn btn-info w-100">
                                    <i class="bi bi-clock"></i> Stock en Tiempo Real
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="{{ url('admin/auditoria/alertas-stock') }}" class="btn btn-warning w-100">
                                    <i class="bi bi-bell"></i> Alertas de Stock
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <a href="{{ url('admin/auditoria/inconsistencias-ventas') }}" class="btn btn-danger w-100">
                                    <i class="bi bi-exclamation-triangle"></i> Inconsistencias de Ventas
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <button class="btn btn-secondary w-100" onclick="exportarReporteAuditoria()">
                                    <i class="bi bi-download"></i> Exportar Reporte Completo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Alertas de Stock -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-exclamation-triangle"></i> Alertas de Stock
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($alertasStock->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Artículo</th>
                                            <th>Stock</th>
                                            <th>Alerta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($alertasStock as $alerta)
                                            <tr>
                                                <td>
                                                    <strong>{{ $alerta['articulo']->codigo }}</strong><br>
                                                    <small>{{ $alerta['articulo']->nombre }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $alerta['tipo_alerta'] === 'CRITICA' ? 'bg-danger' : 'bg-warning' }}">
                                                        {{ $alerta['articulo']->stock }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $alerta['mensaje'] }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ url('admin/auditoria/alertas-stock') }}" class="btn btn-sm btn-warning">
                                    Ver Todas las Alertas
                                </a>
                            </div>
                        @else
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle"></i> No hay alertas de stock críticas
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Últimos Reportes -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-file-text"></i> Últimos Reportes de Auditoría
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($ultimosReportes) > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Inconsistencias</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ultimosReportes as $reporte)
                                            <tr>
                                                <td>
                                                    <small>{{ $reporte['fecha_formateada'] }}</small>
                                                </td>
                                                <td>
                                                    @if($reporte['total_inconsistencias'] > 0)
                                                        <span class="badge bg-danger">{{ $reporte['total_inconsistencias'] }}</span>
                                                    @else
                                                        <span class="badge bg-success">0</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ url('admin/auditoria/reporte/' . $reporte['fecha']) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> No hay reportes de auditoría disponibles
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ejecutar auditoría -->
<div class="modal fade" id="auditoriaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ejecutar Auditoría de Ventas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="auditoriaForm">
                    <div class="mb-3">
                        <label for="dias" class="form-label">Días hacia atrás para auditar:</label>
                        <select class="form-select" id="dias" name="dias">
                            <option value="7">7 días</option>
                            <option value="15">15 días</option>
                            <option value="30" selected>30 días</option>
                            <option value="60">60 días</option>
                            <option value="90">90 días</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="articulo_id" class="form-label">Artículo específico (opcional):</label>
                        <select class="form-select" id="articulo_id" name="articulo_id">
                            <option value="">Todos los artículos</option>
                            <!-- Se puede poblar con AJAX si es necesario -->
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="aplicar_correcciones" name="aplicar_correcciones">
                            <label class="form-check-label" for="aplicar_correcciones">
                                <strong class="text-warning">Aplicar correcciones automáticamente</strong>
                                <br><small class="text-muted">⚠️ Esto modificará los datos. Usar con precaución.</small>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="ejecutarAuditoria()">
                    <i class="bi bi-play"></i> Ejecutar Auditoría
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de resultados -->
<div class="modal fade" id="resultadosModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resultados de la Auditoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="resultadosContent">
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
function mostrarModalAuditoria() {
    new bootstrap.Modal(document.getElementById('auditoriaModal')).show();
}

function ejecutarAuditoria() {
    const form = document.getElementById('auditoriaForm');
    const formData = new FormData(form);
    
    // Mostrar loading
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-hourglass-split"></i> Ejecutando...';
    button.disabled = true;
    
    fetch('{{ url("admin/auditoria/ejecutar") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        button.innerHTML = originalText;
        button.disabled = false;
        
        if (data.success) {
            // Cerrar modal de configuración
            bootstrap.Modal.getInstance(document.getElementById('auditoriaModal')).hide();
            
            // Mostrar resultados
            document.getElementById('resultadosContent').innerHTML = 
                '<div class="alert alert-success">' +
                '<h6>Auditoría completada exitosamente</h6>' +
                '<pre>' + data.output + '</pre>' +
                '</div>';
            
            new bootstrap.Modal(document.getElementById('resultadosModal')).show();
            
            // Recargar la página después de unos segundos
            setTimeout(() => {
                location.reload();
            }, 3000);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        button.innerHTML = originalText;
        button.disabled = false;
        alert('Error de conexión: ' + error.message);
    });
}

function exportarReporteAuditoria() {
    // Implementar exportación de reporte
    alert('Función de exportación en desarrollo');
}
</script>
@endsection
