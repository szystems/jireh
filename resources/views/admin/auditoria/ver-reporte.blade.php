@extends('layouts.admin')

@section('content')
<div class="content-wrapper-scroll">
    <div class="main-header d-flex align-items-center justify-content-between position-relative">
        <div class="d-flex align-items-center justify-content-center">
            <div class="page-icon">
                <i class="bi bi-file-text"></i>
            </div>
            <div class="page-title">
                <h5>Reporte de Auditoría de Ventas</h5>
                <small class="text-muted">Generado el {{ \Carbon\Carbon::parse($contenido['fecha_auditoria'])->format('d/m/Y H:i:s') }}</small>
            </div>
        </div>
        <div>
            <a href="{{ url('admin/auditoria') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left"></i> Volver al Dashboard
            </a>
            <button class="btn btn-success" onclick="exportarReporte()">
                <i class="bi bi-download"></i> Descargar Reporte
            </button>
            <button class="btn btn-info" onclick="window.print()">
                <i class="bi bi-printer"></i> Imprimir
            </button>
        </div>
    </div>

    <div class="content-wrapper">
        
        <!-- Estadísticas Generales -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $contenido['estadisticas']['ventas_auditadas'] ?? 0 }}</h3>
                                <p class="mb-0">Ventas Auditadas</p>
                            </div>
                            <i class="bi bi-receipt fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $contenido['estadisticas']['detalles_auditados'] ?? 0 }}</h3>
                                <p class="mb-0">Detalles Auditados</p>
                            </div>
                            <i class="bi bi-list-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card {{ count($contenido['inconsistencias']) > 0 ? 'bg-warning' : 'bg-success' }} text-white">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ count($contenido['inconsistencias']) }}</h3>
                                <p class="mb-0">Inconsistencias</p>
                            </div>
                            <i class="bi bi-{{ count($contenido['inconsistencias']) > 0 ? 'exclamation-triangle' : 'check-circle' }} fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body text-center">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-0">{{ $contenido['estadisticas']['articulos_con_problemas'] ?? 0 }}</h3>
                                <p class="mb-0">Artículos Afectados</p>
                            </div>
                            <i class="bi bi-boxes fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Reporte -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle text-primary"></i> Parámetros de Auditoría
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-light me-3">
                                <i class="bi bi-calendar-date text-primary"></i>
                            </div>
                            <div>
                                <small class="text-muted">Período Auditado</small>
                                <div><strong>{{ $contenido['parametros']['dias'] ?? $contenido['parametros']['dias_auditados'] ?? 'N/A' }} días</strong></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-light me-3">
                                <i class="bi bi-box text-info"></i>
                            </div>
                            <div>
                                <small class="text-muted">Artículo Específico</small>
                                <div><strong>
                                    @if(isset($contenido['parametros']['articulo_especifico']) && $contenido['parametros']['articulo_especifico'])
                                        ID: {{ $contenido['parametros']['articulo_especifico'] }}
                                    @else
                                        Todos los artículos
                                    @endif
                                </strong></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-light me-3">
                                <i class="bi bi-gear text-success"></i>
                            </div>
                            <div>
                                <small class="text-muted">Correcciones</small>
                                <div><strong>{{ ($contenido['parametros']['correcciones_aplicadas'] ?? $contenido['parametros']['aplicar_correcciones'] ?? false) ? 'Habilitadas' : 'Solo detección' }}</strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(count($contenido['inconsistencias']) == 0)
        <!-- Estado Sin Inconsistencias -->
        <div class="card border-success">
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                </div>
                <h3 class="text-success mb-3">¡Auditoría Exitosa!</h3>
                <p class="lead text-muted mb-3">No se encontraron inconsistencias en el período auditado</p>
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-shield-check"></i>
                            <strong>Sistema Íntegro:</strong> El inventario y las ventas están perfectamente sincronizados.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else        
        <!-- Resumen de Inconsistencias por Severidad -->
        <div class="row mb-4">
            @php
                $tiposInconsistencias = collect($contenido['inconsistencias'])->groupBy('severidad');
                $coloresCards = [
                    'ALTA' => ['bg' => 'danger', 'icon' => 'exclamation-triangle-fill', 'color' => 'danger'],
                    'MEDIA' => ['bg' => 'warning', 'icon' => 'exclamation-circle-fill', 'color' => 'warning'],
                    'BAJA' => ['bg' => 'info', 'icon' => 'info-circle-fill', 'color' => 'info']
                ];
            @endphp
            
            @foreach($tiposInconsistencias as $severidad => $items)
                @php
                    $config = $coloresCards[$severidad] ?? ['bg' => 'secondary', 'icon' => 'question-circle', 'color' => 'secondary'];
                @endphp
                <div class="col-lg-4 col-md-6 mb-3">
                    <div class="card border-{{ $config['color'] }} h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="text-{{ $config['color'] }} mb-1">{{ count($items) }}</h3>
                                    <h6 class="text-muted mb-2">Severidad: {{ $severidad }}</h6>
                                    <small class="text-muted">
                                        {{ count($items) == 1 ? 'problema detectado' : 'problemas detectados' }}
                                    </small>
                                </div>
                                <div class="text-{{ $config['color'] }}">
                                    <i class="bi bi-{{ $config['icon'] }}" style="font-size: 2.5rem;"></i>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-{{ $config['color'] }} bg-opacity-10 text-center">
                            <small class="text-{{ $config['color'] }} fw-bold">
                                {{ $severidad }}
                            </small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Detalles de Inconsistencias -->
        @foreach($tiposInconsistencias as $severidad => $items)
            @php
                $config = $coloresCards[$severidad] ?? ['bg' => 'secondary', 'icon' => 'question-circle', 'color' => 'secondary'];
            @endphp
            <div class="card mb-4">
                <div class="card-header bg-{{ $config['color'] }} text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-{{ $config['icon'] }}"></i>
                            Inconsistencias de Stock - Severidad {{ $severidad }}
                        </h5>
                        <span class="badge bg-light text-dark">{{ count($items) }} {{ count($items) == 1 ? 'caso' : 'casos' }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="bi bi-box"></i> Artículo</th>
                                        <th><i class="bi bi-upc"></i> Código</th>
                                        <th><i class="bi bi-stack"></i> Stock Actual</th>
                                        <th><i class="bi bi-calculator"></i> Stock Teórico</th>
                                        <th><i class="bi bi-arrow-up-down"></i> Diferencia</th>
                                        <th><i class="bi bi-flag"></i> Severidad</th>
                                        <th><i class="bi bi-tools"></i> Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <i class="bi bi-box text-warning"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $item['articulo']['nombre'] ?? 'N/A' }}</div>
                                                    <small class="text-muted">ID: {{ $item['articulo']['id'] ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><code class="bg-light px-2 py-1 rounded">{{ $item['articulo']['codigo'] ?? 'N/A' }}</code></td>
                                        <td>
                                            <span class="badge bg-primary fs-6">{{ $item['stock_actual'] ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary fs-6">{{ $item['stock_teorico'] ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @php $diferencia = $item['diferencia'] ?? 0; @endphp
                                            <span class="badge bg-{{ $diferencia < 0 ? 'danger' : ($diferencia > 0 ? 'success' : 'warning') }} fs-6">
                                                {{ $diferencia > 0 ? '+' : '' }}{{ $diferencia }}
                                            </span>
                                        </td>
                                        <td>
                                            @php $severidad = $item['severidad'] ?? 'BAJA'; @endphp
                                            <span class="badge bg-{{ $severidad == 'ALTA' ? 'danger' : ($severidad == 'MEDIA' ? 'warning' : 'info') }} fs-6">
                                                {{ $severidad }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-success" 
                                                        onclick="corregirStock({{ $item['articulo']['id'] ?? 0 }}, {{ $item['stock_teorico'] ?? 0 }})"
                                                        title="Corregir automáticamente">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning" 
                                                        onclick="mostrarModalCorreccionManual({{ $item['articulo']['id'] ?? 0 }}, '{{ $item['articulo']['nombre'] ?? 'N/A' }}', {{ $item['stock_actual'] ?? 0 }}, {{ $item['stock_teorico'] ?? 0 }})"
                                                        title="Corregir manualmente">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <a href="{{ url('articulos/show-articulo/' . ($item['articulo']['id'] ?? 0)) }}" 
                                                   class="btn btn-sm btn-info" title="Ver artículo" target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                </div>
            </div>
        @endforeach

        @endif

        <!-- Información de Correcciones -->
        @if(isset($contenido['correcciones']) && count($contenido['correcciones']) > 0)
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-check-circle"></i> Correcciones Aplicadas
                    <span class="badge bg-light text-dark ms-2">{{ count($contenido['correcciones']) }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Resultado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contenido['correcciones'] as $correccion)
                            <tr class="table-success">
                                <td>
                                    <span class="badge bg-success">{{ $correccion['tipo'] ?? 'CORRECCIÓN' }}</span>
                                </td>
                                <td>{{ $correccion['descripcion'] ?? 'Corrección aplicada' }}</td>
                                <td>{{ $correccion['resultado'] ?? 'Exitoso' }}</td>
                                <td>{{ $correccion['fecha'] ?? $contenido['fecha_auditoria'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Pie del Reporte -->
        <div class="card mt-4">
            <div class="card-body bg-light text-center">
                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted">
                            <i class="bi bi-calendar"></i>
                            Generado el {{ \Carbon\Carbon::parse($contenido['fecha_auditoria'])->format('d/m/Y H:i:s') }}
                        </small>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">
                            <i class="bi bi-gear"></i>
                            Sistema de Auditoría Jireh v1.0
                        </small>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">
                            <i class="bi bi-shield-check"></i>
                            Informe automatizado
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos para impresión y mejor visualización -->
<style>
    .icon-box {
        width: 3rem;
        height: 3rem;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    @media print {
        .btn, .main-header .d-flex > div:last-child {
            display: none !important;
        }
        
        .card {
            break-inside: avoid;
            margin-bottom: 1rem;
        }
        
        .table {
            font-size: 0.8rem;
        }
        
        .page-wrapper {
            margin: 0;
            padding: 0;
        }
    }
    
    .table th {
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .card-header h5 i {
        margin-right: 0.5rem;
    }
    
    .badge.fs-6 {
        font-size: 0.9rem !important;
    }
    
    code {
        font-size: 0.85rem;
    }
</style>

<script>
function exportarReporte() {
    const fecha = '{{ $fecha }}';
    window.open(`{{ url('admin/auditoria/exportar-reporte') }}/${fecha}`, '_blank');
}

// Función para mostrar/ocultar detalles
function toggleDetails(element) {
    const details = element.nextElementSibling;
    if (details.style.display === 'none') {
        details.style.display = 'block';
        element.innerHTML = element.innerHTML.replace('chevron-down', 'chevron-up');
    } else {
        details.style.display = 'none';
        element.innerHTML = element.innerHTML.replace('chevron-up', 'chevron-down');
    }
}

// Función para corrección automática de stock
function corregirStock(articuloId, stockTeorico) {
    if (confirm('¿Está seguro de que desea corregir automáticamente el stock de este artículo?')) {
        fetch(`{{ url('admin/auditoria/corregir-stock') }}/${articuloId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message,
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message,
                    confirmButtonColor: '#dc3545'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al procesar la solicitud',
                confirmButtonColor: '#dc3545'
            });
        });
    }
}

// Función para mostrar modal de corrección manual
function mostrarModalCorreccionManual(articuloId, articuloNombre, stockActual, stockTeorico) {
    const modalHtml = `
        <div class="modal fade" id="modalCorreccionManual" tabindex="-1" aria-labelledby="modalCorreccionManualLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title" id="modalCorreccionManualLabel">
                            <i class="bi bi-pencil-square"></i> Corrección Manual de Stock
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formCorreccionManual">
                            <input type="hidden" name="articulo_id" value="${articuloId}">
                            
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-title text-warning">
                                        <i class="bi bi-box"></i> Información del Artículo
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Nombre:</strong> ${articuloNombre}<br>
                                            <strong>ID:</strong> ${articuloId}
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Stock Actual:</strong> <span class="badge bg-primary">${stockActual}</span><br>
                                            <strong>Stock Teórico:</strong> <span class="badge bg-secondary">${stockTeorico}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="nuevo_stock" class="form-label">
                                    <i class="bi bi-arrow-up-circle"></i> Nuevo Stock <span class="text-danger">*</span>
                                </label>
                                <input type="number" class="form-control" id="nuevo_stock" name="nuevo_stock" 
                                       min="0" required value="${stockTeorico}">
                                <div class="form-text">
                                    Ingrese el stock correcto para este artículo. 
                                    Se sugiere el stock teórico calculado (${stockTeorico}).
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="motivo" class="form-label">
                                    <i class="bi bi-chat-text"></i> Motivo de la Corrección <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="motivo" name="motivo" rows="3" required 
                                          placeholder="Describa el motivo de esta corrección manual...">Corrección manual desde auditoría - Inconsistencia detectada</textarea>
                                <div class="form-text">
                                    Este motivo quedará registrado en el historial de auditoría.
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>Importante:</strong> Esta acción modificará permanentemente el stock del artículo 
                                y quedará registrada en el sistema de auditoría.
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-warning" onclick="ejecutarCorreccionManual()">
                            <i class="bi bi-check-circle"></i> Aplicar Corrección
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Remover modal anterior si existe
    const modalExistente = document.getElementById('modalCorreccionManual');
    if (modalExistente) {
        modalExistente.remove();
    }

    // Agregar modal al DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('modalCorreccionManual'));
    modal.show();
}

// Función para ejecutar la corrección manual
function ejecutarCorreccionManual() {
    const form = document.getElementById('formCorreccionManual');
    const formData = new FormData(form);

    // Validar formulario
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const datos = {
        articulo_id: formData.get('articulo_id'),
        nuevo_stock: formData.get('nuevo_stock'),
        motivo: formData.get('motivo')
    };

    fetch('{{ url('admin/auditoria/ajuste-manual') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(datos)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById('modalCorreccionManual')).hide();
            
            Swal.fire({
                icon: 'success',
                title: '¡Corrección Aplicada!',
                text: data.message,
                confirmButtonColor: '#28a745'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonColor: '#dc3545'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al procesar la corrección',
            confirmButtonColor: '#dc3545'
        });
    });
}

// Función para mostrar modal de corrección de stock negativo
function mostrarModalCorreccionStockNegativo(articuloId, articuloNombre, stockActual) {
    mostrarModalCorreccionManual(articuloId, articuloNombre, stockActual, 0);
}

// Función para corregir ventas duplicadas
function corregirVentaDuplicada(venta1Id, venta2Id) {
    Swal.fire({
        title: '¿Qué acción desea tomar?',
        text: `Se detectó una posible duplicación entre las ventas #${venta1Id} y #${venta2Id}`,
        icon: 'question',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonText: 'Eliminar Venta #' + venta2Id,
        denyButtonText: 'Marcar como No Duplicada',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545',
        denyButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            // Lógica para eliminar la venta duplicada
            eliminarVentaDuplicada(venta2Id);
        } else if (result.isDenied) {
            // Lógica para marcar como no duplicada
            marcarComoNoDuplicada(venta1Id, venta2Id);
        }
    });
}

function eliminarVentaDuplicada(ventaId) {
    // Esta funcionalidad requeriría un método adicional en el controlador
    console.log('Funcionalidad de eliminación de venta duplicada pendiente de implementar');
    Swal.fire({
        icon: 'info',
        title: 'Funcionalidad Pendiente',
        text: 'La eliminación de ventas duplicadas será implementada en una próxima versión.',
        confirmButtonColor: '#17a2b8'
    });
}

function marcarComoNoDuplicada(venta1Id, venta2Id) {
    // Esta funcionalidad requeriría un método adicional en el controlador
    console.log('Funcionalidad de marcar como no duplicada pendiente de implementar');
    Swal.fire({
        icon: 'info',
        title: 'Funcionalidad Pendiente',
        text: 'El marcado de ventas como no duplicadas será implementado en una próxima versión.',
        confirmButtonColor: '#17a2b8'
    });
}
</script>

<!-- SweetAlert2 para notificaciones elegantes -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
