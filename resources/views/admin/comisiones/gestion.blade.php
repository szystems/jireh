@extends('layouts.admin')

@section('title', 'Gestión de Comisiones')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-list-check"></i> Gestión de Comisiones
                    </h4>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filtrosAvanzados">
                            <i class="bi bi-funnel"></i> Filtros Avanzados
                        </button>
                        <a href="{{ route('comisiones.pdf_listado') }}" class="btn btn-danger btn-sm" target="_blank" id="btnGenerarPDF">
                            <i class="bi bi-file-earmark-pdf"></i> Generar PDF
                        </a>
                        <button type="button" class="btn btn-success btn-sm" id="btnPagarSeleccionadas" disabled>
                            <i class="bi bi-credit-card"></i> Pagar Seleccionadas
                        </button>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Resumen de Filtros Aplicados -->
                    <div class="row mb-3" id="resumenFiltros" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i>
                                <strong>Filtros aplicados:</strong> <span id="filtrosTexto"></span>
                                <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="limpiarFiltros()">
                                    <i class="bi bi-x-circle"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas Rápidas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="bi bi-calculator"></i> Total Comisiones</h6>
                                </div>
                                <div class="card-body text-center">
                                    <h4 class="text-primary" id="totalComisiones">{{ $config->currency_simbol }} 0.00</h4>
                                    <small class="text-muted" id="cantidadComisiones">0 comisiones</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="bi bi-check-circle"></i> Pagadas</h6>
                                </div>
                                <div class="card-body text-center">
                                    <h4 class="text-success" id="totalPagadas">{{ $config->currency_simbol }} 0.00</h4>
                                    <small class="text-muted" id="cantidadPagadas">0 pagadas</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="bi bi-clock"></i> Pendientes</h6>
                                </div>
                                <div class="card-body text-center">
                                    <h4 class="text-warning" id="totalPendientes">{{ $config->currency_simbol }} 0.00</h4>
                                    <small class="text-muted" id="cantidadPendientes">0 pendientes</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="bi bi-check-square"></i> Seleccionadas</h6>
                                </div>
                                <div class="card-body text-center">
                                    <h4 class="text-info" id="totalSeleccionadas">{{ $config->currency_simbol }} 0.00</h4>
                                    <small class="text-muted" id="cantidadSeleccionadas">0 seleccionadas</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pestañas de Navegación -->
                    <ul class="nav nav-tabs mb-3" id="gestionTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="todas-tab" data-bs-toggle="tab" data-bs-target="#todas" type="button" role="tab">
                                <i class="bi bi-list"></i> Todas las Comisiones
                            </button>
                        </li>
                    </ul>

                    <!-- Contenido de las Pestañas -->
                    <div class="tab-content" id="gestionTabContent">
                        <!-- Todas las Comisiones -->
                        <div class="tab-pane fade show active" id="todas" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="tablaTodasComisiones">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="5%">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>ID</th>
                                            <th>Beneficiario</th>
                                            <th>Tipo</th>
                                            <th>Monto</th>
                                            <th>Venta</th>
                                            <th>Estado</th>
                                            <th>Lote de Pago</th>
                                            <th>Fecha Cálculo</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyTodasComisiones">
                                        <!-- Cargado vía AJAX -->
                                    </tbody>
                                </table>
                            </div>
                            <div id="paginacionTodas"></div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Filtros Avanzados -->
<div class="modal fade" id="filtrosAvanzados" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-funnel"></i> Filtros Avanzados
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formFiltrosAvanzados">
                    <div class="row">
                        <!-- Filtros de Fecha -->
                        <div class="col-md-6">
                            <h6 class="text-primary"><i class="bi bi-calendar3"></i> Período</h6>
                            <div class="mb-3">
                                <label class="form-label">Tipo de Período</label>
                                <select class="form-select" id="tipoPeriodo" name="tipo_periodo">
                                    <option value="personalizado">Personalizado</option>
                                    <option value="hoy">Hoy</option>
                                    <option value="ayer">Ayer</option>
                                    <option value="esta_semana">Esta Semana</option>
                                    <option value="semana_pasada">Semana Pasada</option>
                                    <option value="este_mes">Este Mes</option>
                                    <option value="mes_pasado">Mes Pasado</option>
                                    <option value="ultimo_trimestre">Último Trimestre</option>
                                    <option value="este_año">Este Año</option>
                                    <option value="año_pasado">Año Pasado</option>
                                </select>
                            </div>
                            <div class="row" id="fechasPersonalizadas">
                                <div class="col-md-6">
                                    <label class="form-label">Fecha Inicio</label>
                                    <input type="date" class="form-control" id="fechaInicio" name="fecha_inicio">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Fecha Fin</label>
                                    <input type="date" class="form-control" id="fechaFin" name="fecha_fin">
                                </div>
                            </div>
                        </div>

                        <!-- Filtros de Comisión -->
                        <div class="col-md-6">
                            <h6 class="text-success"><i class="bi bi-currency-dollar"></i> Comisiones</h6>
                            <div class="mb-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select" id="estadoFiltro" name="estado">
                                    <option value="">Todos</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="pagado">Pagado</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo de Comisión</label>
                                <select class="form-select" id="tipoComisionFiltro" name="tipo_comision">
                                    <option value="">Todas</option>
                                    <option value="venta_meta">Por Meta de Ventas</option>
                                    <option value="mecanico">Mecánico</option>
                                    <option value="carwash">Car Wash</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Monto Mínimo</label>
                                    <input type="number" class="form-control" id="montoMinimo" name="monto_minimo" step="0.01" placeholder="0.00">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Monto Máximo</label>
                                    <input type="number" class="form-control" id="montoMaximo" name="monto_maximo" step="0.01" placeholder="999999.99">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <!-- Filtros específicos por trabajador -->
                        <div class="col-md-6">
                            <h6 class="text-info"><i class="bi bi-person-workspace"></i> Trabajadores</h6>
                            <div class="mb-3">
                                <label class="form-label">Trabajador Específico</label>
                                <select class="form-select" id="trabajadorEspecifico" name="trabajador_id">
                                    <option value="">Todos los trabajadores</option>
                                    <!-- Cargado vía AJAX -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo de Trabajador</label>
                                <select class="form-select" id="tipoTrabajadorFiltro" name="tipo_trabajador">
                                    <option value="">Todos</option>
                                    <option value="mecanico">Mecánicos</option>
                                    <option value="carwash">Car Wash</option>
                                    <option value="administrativo">Administrativo</option>
                                </select>
                            </div>
                        </div>

                        <!-- Filtros específicos por vendedor -->
                        <div class="col-md-6">
                            <h6 class="text-warning"><i class="bi bi-person-check"></i> Vendedores</h6>
                            <div class="mb-3">
                                <label class="form-label">Vendedor Específico</label>
                                <select class="form-select" id="vendedorEspecifico" name="vendedor_id">
                                    <option value="">Todos los vendedores</option>
                                    <!-- Cargado vía AJAX -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Período de Meta</label>
                                <select class="form-select" id="periodoMetaFiltro" name="periodo_meta">
                                    <option value="">Todos</option>
                                    <option value="mensual">Mensual</option>
                                    <option value="trimestral">Trimestral</option>
                                    <option value="semestral">Semestral</option>
                                    <option value="anual">Anual</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" onclick="limpiarFiltros()">Limpiar Filtros</button>
                <button type="button" class="btn btn-primary" onclick="aplicarFiltros()">Aplicar Filtros</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Pago Masivo -->
<div class="modal fade" id="modalPagoMasivo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-credit-card"></i> Pago Masivo de Comisiones
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Se procesarán <strong id="cantidadAPagar">0</strong> comisiones por un total de <strong id="montoTotalAPagar">{{ $config->currency_simbol }} 0.00</strong>
                </div>
                <form id="formPagoMasivo">
                    <div class="mb-3">
                        <label class="form-label">Método de Pago</label>
                        <select class="form-select" name="metodo_pago" required>
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="cheque">Cheque</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de Pago</label>
                        <input type="date" class="form-control" name="fecha_pago" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Referencia</label>
                        <input type="text" class="form-control" name="referencia" placeholder="Número de cheque, transferencia, etc.">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observaciones</label>
                        <textarea class="form-control" name="observaciones" rows="3" placeholder="Observaciones adicionales..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="procesarPagoMasivo()">
                    <i class="bi bi-check-circle"></i> Procesar Pago
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Variables globales
const currencySymbol = '{{ $config->currency_simbol }}';
let filtrosAplicados = {};
let comisionesSeleccionadas = [];

$(document).ready(function() {
    cargarDatosIniciales();
    configurarEventos();
    cargarComisiones();
});

function cargarDatosIniciales() {
    // Configurar fechas por defecto para mostrar desde enero 2024 hasta hoy
    const hoy = new Date();
    const inicioPeriodo = new Date('2024-01-01');
    
    $('#fechaInicio').val(inicioPeriodo.toISOString().split('T')[0]);
    $('#fechaFin').val(hoy.toISOString().split('T')[0]);
    
    // Cargar trabajadores en el dropdown
    let trabajadoresOptions = '<option value="">Todos los trabajadores</option>';
    @foreach($trabajadores as $trabajador)
        trabajadoresOptions += '<option value="{{ $trabajador->id }}">{{ $trabajador->nombre }} {{ $trabajador->apellido }} ({{ $trabajador->tipoTrabajador->nombre ?? "N/A" }})</option>';
    @endforeach
    $('#trabajadorEspecifico').html(trabajadoresOptions);

    // Cargar vendedores en el dropdown
    let vendedoresOptions = '<option value="">Todos los vendedores</option>';
    @foreach($vendedores as $vendedor)
        vendedoresOptions += '<option value="{{ $vendedor->id }}">{{ $vendedor->name }}</option>';
    @endforeach
    $('#vendedorEspecifico').html(vendedoresOptions);

    // Establecer fecha fin por defecto
    $('#fechaFin').val(new Date().toISOString().split('T')[0]);
}

function configurarEventos() {
    $('#tipoPeriodo').change(function() {
        if ($(this).val() === 'personalizado') {
            $('#fechasPersonalizadas').show();
        } else {
            $('#fechasPersonalizadas').hide();
            actualizarFechasPredefinidas();
        }
    });

    $('#selectAll').change(function() {
        $('input[name="comision_ids[]"]').prop('checked', this.checked);
        actualizarSeleccionadas();
        actualizarEstadoBotones();
    });
    
    // Event listeners para checkboxes de comisiones
    $(document).on('change', 'input[name="comision_ids[]"]', function() {
        actualizarSeleccionadas();
        actualizarEstadoBotones();
    });
    
    // Event listener para actualizar estado después de cargar datos
    $(document).on('DOMSubtreeModified', '#bodyTodasComisiones', function() {
        actualizarEstadoBotones();
    });
    
    // Event listener para botón de pagar seleccionadas
    $('#btnPagarSeleccionadas').click(function() {
        const checkboxes = $('input[name="comision_ids[]"]:checked:visible');
        if (checkboxes.length > 0) {
            const comisionIds = [];
            checkboxes.each(function() {
                comisionIds.push($(this).val());
            });
            
            if (confirm('¿Está seguro de marcar ' + checkboxes.length + ' comisiones como pagadas?')) {
                pagarMultiples(comisionIds);
            }
        }
    });
    
    // Event listener para botón de generar PDF
    $('#btnGenerarPDF').click(function(e) {
        e.preventDefault();
        const filtros = obtenerFiltrosAplicados();
        const params = new URLSearchParams(filtros);
        const url = '{{ route("comisiones.pdf_listado") }}?' + params.toString();
        window.open(url, '_blank');
    });
}

function cargarComisiones() {
    // Simplificado: solo cargar todas las comisiones ya que ahora tenemos una sola pestaña
    cargarTodasComisiones();
}

function cargarTodasComisiones() {
    console.log('Cargando comisiones...');
    const filtros = obtenerFiltrosAplicados();
    
    // Mostrar loading
    $('#bodyTodasComisiones').html('<tr><td colspan="10" class="text-center"><i class="bi bi-hourglass-split"></i> Cargando...</td></tr>');
    
    $.get('/comisiones/gestion/todas', filtros)
    .done(function(data) {
        console.log('Datos recibidos:', data);
        let html = '';
        
        if (data.comisiones && data.comisiones.length > 0) {
            data.comisiones.forEach(function(comision) {
                html += '<tr>';
                
                // Generar checkbox condicionalmente según el estado
                if (comision.estado === 'pendiente') {
                    html += '<td><input type="checkbox" name="comision_ids[]" value="' + comision.id + '" data-monto="' + comision.monto + '" class="form-check-input"></td>';
                } else {
                    html += '<td><span class="text-muted">-</span></td>';
                }
                
                html += '<td>' + comision.id + '</td>';
                html += '<td>' + (comision.beneficiario_nombre || 'N/A') + '</td>';
                html += '<td>';
                html += '<span class="badge bg-' + getBadgeColor(comision.tipo_comision) + '">' + (comision.tipo_comision_texto || 'N/A') + '</span>';
                
                // Si es meta_venta y hay información de meta, mostrar badge adicional
                if (comision.meta_info && comision.tipo_comision === 'meta_venta') {
                    html += '<br><small><span class="badge bg-' + comision.meta_info.color + ' mt-1" title="Meta alcanzada: ' + comision.meta_info.rango + '">';
                    html += comision.meta_info.nombre + '</span></small>';
                }
                
                html += '</td>';
                html += '<td>' + currencySymbol + ' ' + parseFloat(comision.monto || 0).toFixed(2) + '</td>';
                
                // Columna de Venta
                if (comision.venta_id) {
                    html += '<td><a href="{{ url("show-venta") }}/' + comision.venta_id + '" target="_blank" class="btn btn-sm btn-outline-primary" title="Ver Venta #' + comision.venta_id + '">';
                    html += '<i class="bi bi-receipt"></i> #' + comision.venta_id + '</a></td>';
                } else {
                    html += '<td><span class="text-muted">N/A</span></td>';
                }
                
                html += '<td><span class="badge bg-' + getEstadoBadge(comision.estado) + '">' + (comision.estado_texto || 'N/A') + '</span></td>';
                
                // Columna de Lote de Pago
                if (comision.lote_pago && comision.estado === 'pagado') {
                    html += '<td>';
                    html += '<a href="{{ url("lotes-pago") }}/' + comision.lote_pago.id + '" target="_blank" class="btn btn-sm btn-outline-success" title="Ver Lote ' + comision.lote_pago.numero_lote + '">';
                    html += '<i class="bi bi-file-earmark-check"></i> ' + comision.lote_pago.numero_lote;
                    html += '</a>';
                    html += '<br><small class="text-muted">' + formatearFecha(comision.lote_pago.fecha_pago) + '</small>';
                    html += '</td>';
                } else {
                    html += '<td><span class="text-muted">-</span></td>';
                }
                
                html += '<td>' + formatearFecha(comision.fecha_calculo) + '</td>';
                html += '<td>';
                html += '<div class="btn-group btn-group-sm">';
                html += '<button class="btn btn-info btn-sm" onclick="verDetalle(' + comision.id + ')" title="Ver detalle">';
                html += '<i class="bi bi-eye"></i>';
                html += '</button>';
                if (comision.puede_pagar) {
                    html += '<button class="btn btn-success btn-sm" onclick="pagarIndividual(' + comision.id + ')" title="Pagar">';
                    html += '<i class="bi bi-credit-card"></i>';
                    html += '</button>';
                }
                html += '</div>';
                html += '</td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="10" class="text-center text-muted">No se encontraron comisiones</td></tr>';
        }
        
        $('#bodyTodasComisiones').html(html);
        
        if (data.estadisticas) {
            actualizarCardsEstadisticas(data.estadisticas);
        }
        
        $('#selectAll').prop('checked', false);
        actualizarSeleccionadas();
        
        // Actualizar estado después de cargar datos
        setTimeout(function() {
            actualizarEstadoBotones();
        }, 100);
    })
    .fail(function(xhr, status, error) {
        console.error('Error al cargar comisiones:', error, xhr.responseText);
        $('#bodyTodasComisiones').html('<tr><td colspan="9" class="text-center text-danger">Error al cargar comisiones: ' + error + '</td></tr>');
    });
}

function actualizarCardsEstadisticas(estadisticas) {
    if (estadisticas.total !== undefined) {
        $('#totalComisiones').text(currencySymbol + ' ' + parseFloat(estadisticas.total).toFixed(2));
    }
    if (estadisticas.total_pagadas !== undefined) {
        $('#totalPagadas').text(currencySymbol + ' ' + parseFloat(estadisticas.total_pagadas).toFixed(2));
    }
    if (estadisticas.total_pendientes !== undefined) {
        $('#totalPendientes').text(currencySymbol + ' ' + parseFloat(estadisticas.total_pendientes).toFixed(2));
    }
    if (estadisticas.cantidad_total !== undefined) {
        $('#cantidadComisiones').text(estadisticas.cantidad_total + ' comisiones');
    }
    if (estadisticas.cantidad_pagadas !== undefined) {
        $('#cantidadPagadas').text(estadisticas.cantidad_pagadas + ' pagadas');
    }
    if (estadisticas.cantidad_pendientes !== undefined) {
        $('#cantidadPendientes').text(estadisticas.cantidad_pendientes + ' pendientes');
    }
}

function getBadgeColor(tipoComision) {
    switch(tipoComision) {
        case 'meta_venta':
        case 'venta_meta': 
            return 'primary';
        case 'mecanico': 
            return 'warning';
        case 'carwash': 
            return 'info';
        default: 
            return 'secondary';
    }
}

function getEstadoBadge(estado) {
    switch(estado) {
        case 'pendiente': return 'warning';
        case 'pagado': return 'success';
        case 'cancelado': return 'danger';
        default: return 'secondary';
    }
}

function formatearFecha(fecha) {
    if (!fecha) return '-';
    return new Date(fecha).toLocaleDateString('es-GT');
}

function actualizarSeleccionadas() {
    comisionesSeleccionadas = [];
    let montoTotal = 0;
    $('input[name="comision_ids[]"]:checked').each(function() {
        comisionesSeleccionadas.push($(this).val());
        montoTotal += parseFloat($(this).data('monto') || 0);
    });
    
    // Actualizar card de seleccionadas
    $('#totalSeleccionadas').text(currencySymbol + ' ' + montoTotal.toFixed(2));
    $('#cantidadSeleccionadas').text(comisionesSeleccionadas.length + ' seleccionadas');
}

function pagarIndividual(comisionId) {
    if (confirm('¿Está seguro de marcar esta comisión como pagada? Se creará un lote de pago.')) {
        $.post('/comisiones/pagar-individual', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            comision_id: comisionId
        })
        .done(function(response) {
            if (response.success) {
                alert('Comisión pagada exitosamente. Redirigiendo al lote creado...');
                // Redirigir al lote creado
                if (response.redirect_url) {
                    window.location.href = response.redirect_url;
                } else {
                    cargarComisiones();
                }
            } else {
                alert(response.message || 'Error al procesar el pago');
            }
        })
        .fail(function() {
            alert('Error al procesar el pago');
        });
    }
}

function pagarMultiples(comisionIds) {
    $.post('/comisiones/pagar-multiples', {
        _token: $('meta[name="csrf-token"]').attr('content'),
        tipo: 'todas',
        comision_ids: comisionIds
    })
    .done(function(response) {
        if (response.success) {
            alert('Comisiones pagadas exitosamente: ' + (response.cantidad_procesadas || comisionIds.length) + '. Redirigiendo al lote creado...');
            // Redirigir al lote creado
            if (response.redirect_url) {
                window.location.href = response.redirect_url;
            } else {
                cargarComisiones();
            }
        } else {
            alert(response.message || 'Error al procesar el pago');
        }
    })
    .fail(function(xhr, status, error) {
        console.error('Error al procesar pagos múltiples:', error);
        alert('Error al procesar los pagos: ' + error);
    });
}

function verDetalle(comisionId) {
    window.location.href = '/comisiones/show/' + comisionId;
}

function verDetallesTrabajador(trabajadorId) {
    window.location.href = `/trabajadores/${trabajadorId}/comisiones`;
}

function verDetallesVendedor(vendedorId) {
    window.location.href = `/vendedores/${vendedorId}/comisiones`;
}

function pagarTrabajador(trabajadorId) {
    if (confirm('¿Está seguro de pagar todas las comisiones pendientes de este trabajador? Se creará un lote de pago.')) {
        $.post('/comisiones/pagar-trabajador', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            trabajador_id: trabajadorId
        })
        .done(function(response) {
            if (response.success) {
                alert('Comisiones del trabajador pagadas exitosamente. Redirigiendo al lote creado...');
                // Redirigir al lote creado
                if (response.redirect_url) {
                    window.location.href = response.redirect_url;
                } else {
                    cargarComisiones();
                }
            } else {
                alert(response.message || 'Error al procesar el pago');
            }
        })
        .fail(function() {
            alert('Error al procesar el pago');
        });
    }
}

function pagarVendedor(vendedorId) {
    if (confirm('¿Está seguro de pagar todas las comisiones pendientes de este vendedor? Se creará un lote de pago.')) {
        $.post('/comisiones/pagar-vendedor', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            vendedor_id: vendedorId
        })
        .done(function(response) {
            if (response.success) {
                alert('Comisiones del vendedor pagadas exitosamente. Redirigiendo al lote creado...');
                // Redirigir al lote creado
                if (response.redirect_url) {
                    window.location.href = response.redirect_url;
                } else {
                    cargarComisiones();
                }
            } else {
                alert(response.message || 'Error al procesar el pago');
            }
        })
        .fail(function() {
            alert('Error al procesar el pago');
        });
    }
}

// Función para aplicar filtros avanzados
function aplicarFiltros() {
    // Cerrar el modal
    $('#filtrosAvanzados').modal('hide');
    
    // Recargar las comisiones con los filtros aplicados
    cargarTodasComisiones();
}

// Función para actualizar fechas predefinidas
function actualizarFechasPredefinidas() {
    const tipoPeriodo = $('#tipoPeriodo').val();
    const hoy = new Date();
    let fechaInicio = '';
    let fechaFin = '';
    
    switch(tipoPeriodo) {
        case 'hoy':
            fechaInicio = fechaFin = formatDate(hoy);
            break;
        case 'ayer':
            const ayer = new Date(hoy);
            ayer.setDate(ayer.getDate() - 1);
            fechaInicio = fechaFin = formatDate(ayer);
            break;
        case 'esta_semana':
            const inicioSemana = new Date(hoy);
            inicioSemana.setDate(hoy.getDate() - hoy.getDay());
            fechaInicio = formatDate(inicioSemana);
            fechaFin = formatDate(hoy);
            break;
        case 'semana_pasada':
            const inicioSemPasada = new Date(hoy);
            inicioSemPasada.setDate(hoy.getDate() - hoy.getDay() - 7);
            const finSemPasada = new Date(inicioSemPasada);
            finSemPasada.setDate(inicioSemPasada.getDate() + 6);
            fechaInicio = formatDate(inicioSemPasada);
            fechaFin = formatDate(finSemPasada);
            break;
        case 'este_mes':
            const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
            fechaInicio = formatDate(inicioMes);
            fechaFin = formatDate(hoy);
            break;
        case 'mes_pasado':
            const inicioMesPasado = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1);
            const finMesPasado = new Date(hoy.getFullYear(), hoy.getMonth(), 0);
            fechaInicio = formatDate(inicioMesPasado);
            fechaFin = formatDate(finMesPasado);
            break;
        case 'ultimo_trimestre':
            const mesActual = hoy.getMonth();
            const inicioTrimestre = new Date(hoy.getFullYear(), mesActual - (mesActual % 3) - 3, 1);
            const finTrimestre = new Date(hoy.getFullYear(), mesActual - (mesActual % 3), 0);
            fechaInicio = formatDate(inicioTrimestre);
            fechaFin = formatDate(finTrimestre);
            break;
        case 'este_año':
            const inicioAño = new Date(hoy.getFullYear(), 0, 1);
            fechaInicio = formatDate(inicioAño);
            fechaFin = formatDate(hoy);
            break;
        case 'año_pasado':
            const inicioAñoPasado = new Date(hoy.getFullYear() - 1, 0, 1);
            const finAñoPasado = new Date(hoy.getFullYear() - 1, 11, 31);
            fechaInicio = formatDate(inicioAñoPasado);
            fechaFin = formatDate(finAñoPasado);
            break;
        default:
            // Para "personalizado" no hacer nada, mantener campos editables
            return;
    }
    
    if (fechaInicio && fechaFin) {
        $('#fechaInicio').val(fechaInicio);
        $('#fechaFin').val(fechaFin);
    }
}

// Función para formatear fecha en formato YYYY-MM-DD
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return year + '-' + month + '-' + day;
}

// Función para limpiar filtros
function limpiarFiltros() {
    $('#formFiltrosAvanzados')[0].reset();
    $('#tipoPeriodo').val('personalizado');
    aplicarFiltros();
}

// Función para obtener filtros aplicados
function obtenerFiltrosAplicados() {
    const formData = new FormData($('#formFiltrosAvanzados')[0]);
    const filtros = {};
    
    for (let [key, value] of formData.entries()) {
        if (value && value.trim() !== '') {
            filtros[key] = value;
        }
    }
    
    // Debug temporal para verificar filtros
    console.log('Filtros aplicados:', filtros);
    
    return filtros;
}

// Función obsoleta - Ya no se necesita porque los checkboxes se generan condicionalmente
/*
function actualizarVisibilidadCheckboxes() {
    $('input[name="comision_ids[]"]').each(function() {
        const row = $(this).closest('tr');
        const estadoBadge = row.find('.badge');
        const estado = estadoBadge.text().toLowerCase();
        
        // Solo mostrar checkbox para comisiones pendientes
        if (estado === 'pagado' || estado === 'cancelado') {
            $(this).hide();
        } else {
            $(this).show();
        }
    });
}
*/

// Función para actualizar estado de botones según selección
function actualizarEstadoBotones() {
    const checkboxes = $('input[name="comision_ids[]"]:checked:visible');
    const btnPagarSeleccionadas = $('#btnPagarSeleccionadas');
    
    if (checkboxes.length > 0) {
        btnPagarSeleccionadas.prop('disabled', false);
        btnPagarSeleccionadas.text('Pagar Seleccionadas (' + checkboxes.length + ')');
    } else {
        btnPagarSeleccionadas.prop('disabled', true);
        btnPagarSeleccionadas.text('Pagar Seleccionadas');
    }
}

// Función para procesar pago masivo desde el modal
function procesarPagoMasivo() {
    const checkboxes = $('input[name="comision_ids[]"]:checked:visible');
    if (checkboxes.length === 0) {
        alert('No hay comisiones seleccionadas');
        return;
    }
    
    const comisionIds = [];
    checkboxes.each(function() {
        comisionIds.push($(this).val());
    });
    
    // Cerrar el modal
    $('#modalPagoMasivo').modal('hide');
    
    // Procesar el pago múltiple
    if (confirm('¿Está seguro de procesar el pago de ' + checkboxes.length + ' comisiones? Se creará un lote de pago.')) {
        pagarMultiples(comisionIds);
    }
}
</script>
@endsection
