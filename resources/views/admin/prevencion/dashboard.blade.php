@extends('layouts.admin')

@section('titulo', 'Prevención de Inconsistencias - Sistema Jireh')

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <!-- Header del Dashboard -->
        <div class="col-12">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1 text-primary">🛡️ Centro de Prevención de Inconsistencias</h3>
                            <p class="text-muted mb-0">Sistema integral para prevenir errores, manipulaciones y problemas de integridad</p>
                        </div>
                        <div class="text-end">
                            <div class="d-flex align-items-center">
                                <span class="me-2">Salud del Sistema:</span>
                                <div class="progress" style="width: 100px; height: 8px;">                                    <div class="progress-bar" role="progressbar" style="width: {{ $saludSistema['porcentaje'] ?? 100 }}%"
                                         id="barrasalud"></div>
                                </div>
                                <span class="ms-2 fw-bold" id="porcentajesalud">{{ $saludSistema['porcentaje'] ?? 100 }}%</span>
                            </div>
                            <small class="text-muted">Última actualización: {{ isset($saludSistema['timestamp']) ? $saludSistema['timestamp']->diffForHumans() : 'Ahora' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Métricas Principales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <div class="display-6 text-success mb-2">✅</div>
                    <h5 class="card-title text-success">Salud del Sistema</h5>
                    <h3 class="text-success" id="saludDisplay">{{ $saludSistema['porcentaje'] ?? 100 }}%</h3>
                    <p class="text-muted small mb-0">Estado: <span id="estadoSistema"></span></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <div class="display-6 text-warning mb-2">⚠️</div>
                    <h5 class="card-title text-warning">Alertas Activas</h5>
                    <h3 class="text-warning" id="alertasActivas">{{ count($alertasRecientes ?? []) }}</h3>
                    <p class="text-muted small mb-0">Últimas 24 horas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <div class="display-6 text-info mb-2">🔧</div>
                    <h5 class="card-title text-info">Correcciones</h5>
                    <h3 class="text-info" id="correccionesAplicadas">{{ $saludSistema['correcciones_aplicadas'] ?? 0 }}</h3>
                    <p class="text-muted small mb-0">Automáticas hoy</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center border-0 shadow-sm">
                <div class="card-body">
                    <div class="display-6 text-primary mb-2">📊</div>
                    <h5 class="card-title text-primary">Monitoreo</h5>
                    <h3 class="text-primary">
                        <span class="badge bg-success" id="statusMonitoreo">ACTIVO</span>
                    </h3>
                    <p class="text-muted small mb-0">Tiempo real</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Las 3 Opciones Principales -->
    <div class="row mb-5">
        <div class="col-12">
            <h4 class="mb-4 text-center">🚀 Estrategias de Prevención de Inconsistencias</h4>
        </div>

        <!-- OPCIÓN 1: Validación Preventiva -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100 border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white text-center">
                    <div class="display-4 mb-2">🛡️</div>
                    <h5 class="mb-0">OPCIÓN 1: Validación Preventiva</h5>
                    <small>Prevenir errores ANTES de que ocurran</small>
                </div>
                <div class="card-body">
                    <h6 class="text-primary mb-3">🎯 Características:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Validación en tiempo real
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-shield-alt text-success me-2"></i>
                            Bloqueo de operaciones erróneas
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-lock text-success me-2"></i>
                            Control de concurrencia
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-balance-scale text-success me-2"></i>
                            Validación de límites de negocio
                        </li>
                    </ul>
                    
                    <h6 class="text-info mb-3">⚡ Previene:</h6>
                    <div class="row">
                        <div class="col-12">
                            <span class="badge bg-danger me-1 mb-1">Stock Insuficiente</span>
                            <span class="badge bg-warning me-1 mb-1">Precios Incorrectos</span>
                            <span class="badge bg-info me-1 mb-1">Referencias Inválidas</span>
                            <span class="badge bg-secondary me-1 mb-1">Límites Excedidos</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary btn-sm w-100" onclick="probarValidacionPreventiva()">
                        <i class="fas fa-play me-1"></i> Probar Validación
                    </button>
                    <small class="text-muted d-block mt-2 text-center">
                        Modo: Tiempo Real | Estado: <span class="text-success">ACTIVO</span>
                    </small>
                </div>
            </div>
        </div>

        <!-- OPCIÓN 2: Transacciones Atómicas -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100 border-0 shadow-lg">
                <div class="card-header bg-gradient-success text-white text-center">
                    <div class="display-4 mb-2">🔄</div>
                    <h5 class="mb-0">OPCIÓN 2: Transacciones Atómicas</h5>
                    <small>Todo o nada - Sin estados inconsistentes</small>
                </div>
                <div class="card-body">
                    <h6 class="text-success mb-3">🎯 Características:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-undo text-success me-2"></i>
                            Rollback automático
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-database text-success me-2"></i>
                            Savepoints inteligentes
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-bolt text-success me-2"></i>
                            Operaciones atómicas
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-history text-success me-2"></i>
                            Trazabilidad completa
                        </li>
                    </ul>
                    
                    <h6 class="text-info mb-3">⚡ Garantiza:</h6>
                    <div class="row">
                        <div class="col-12">
                            <span class="badge bg-success me-1 mb-1">Consistencia Total</span>
                            <span class="badge bg-primary me-1 mb-1">Recuperación Automática</span>
                            <span class="badge bg-info me-1 mb-1">Integridad ACID</span>
                            <span class="badge bg-dark me-1 mb-1">Logs Detallados</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-success btn-sm w-100" onclick="probarTransaccionAtomica()">
                        <i class="fas fa-cogs me-1"></i> Probar Transacción
                    </button>
                    <small class="text-muted d-block mt-2 text-center">
                        Modo: ACID | Estado: <span class="text-success">ACTIVO</span>
                    </small>
                </div>
            </div>
        </div>

        <!-- OPCIÓN 3: Monitoreo Continuo -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100 border-0 shadow-lg">
                <div class="card-header bg-gradient-warning text-white text-center">
                    <div class="display-4 mb-2">🤖</div>
                    <h5 class="mb-0">OPCIÓN 3: Monitoreo Continuo</h5>
                    <small>Detección y corrección automática 24/7</small>
                </div>
                <div class="card-body">
                    <h6 class="text-warning mb-3">🎯 Características:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-robot text-warning me-2"></i>
                            IA para detección de patrones
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-magic text-warning me-2"></i>
                            Auto-corrección inteligente
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-bell text-warning me-2"></i>
                            Alertas instantáneas
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-chart-line text-warning me-2"></i>
                            Métricas en tiempo real
                        </li>
                    </ul>
                    
                    <h6 class="text-info mb-3">⚡ Detecta:</h6>
                    <div class="row">
                        <div class="col-12">
                            <span class="badge bg-danger me-1 mb-1">Manipulaciones</span>
                            <span class="badge bg-warning me-1 mb-1">Anomalías</span>
                            <span class="badge bg-info me-1 mb-1">Inconsistencias</span>
                            <span class="badge bg-secondary me-1 mb-1">Errores Sistema</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-warning btn-sm w-100" onclick="ejecutarMonitoreo()">
                        <i class="fas fa-search me-1"></i> Ejecutar Monitoreo
                    </button>
                    <small class="text-muted d-block mt-2 text-center">
                        Modo: Continuo | Estado: <span class="text-success">MONITOREANDO</span>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas Recientes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Alertas Recientes
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($alertasRecientes ?? []) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Severidad</th>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(($alertasRecientes ?? []) as $alerta)
                                    <tr>
                                        <td>
                                            @if($alerta['severidad'] == 'CRITICA')
                                                <span class="badge bg-danger">{{ $alerta['severidad'] }}</span>
                                            @elseif($alerta['severidad'] == 'ALTA')
                                                <span class="badge bg-warning">{{ $alerta['severidad'] }}</span>
                                            @elseif($alerta['severidad'] == 'MEDIA')
                                                <span class="badge bg-info">{{ $alerta['severidad'] }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $alerta['severidad'] }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $alerta['tipo'] }}</td>
                                        <td>
                                            <small>{{ isset($alerta['datos'][0]['descripcion']) ? $alerta['datos'][0]['descripcion'] : 'Ver detalles' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ \Carbon\Carbon::parse($alerta['timestamp'])->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="verDetalleAlerta('{{ $alerta['id'] }}')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-check-circle text-success display-4 mb-3"></i>
                            <h5>No hay alertas activas</h5>
                            <p>El sistema está funcionando correctamente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico de Salud del Sistema -->
    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        Historial de Salud del Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="graficoSalud" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-cog text-secondary me-2"></i>
                        Configuración
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="monitoreoAutomatico" checked>
                        <label class="form-check-label" for="monitoreoAutomatico">
                            Monitoreo Automático
                        </label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="correccionAutomatica" checked>
                        <label class="form-check-label" for="correccionAutomatica">
                            Corrección Automática
                        </label>
                    </div>
                    <div class="mb-3">
                        <label for="intervaloMonitoreo" class="form-label">Intervalo (minutos)</label>
                        <input type="number" class="form-control" id="intervaloMonitoreo" value="5" min="1" max="60">
                    </div>
                    <button class="btn btn-primary btn-sm w-100" onclick="guardarConfiguracion()">
                        <i class="fas fa-save me-1"></i> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles de alertas -->
<div class="modal fade" id="modalDetalleAlerta" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalle de Alerta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoDetalleAlerta">
                <!-- Contenido dinámico -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Configuración inicial
let chartSalud;
let intervalActualizacion;

$(document).ready(function() {
    inicializarDashboard();
    actualizarEstadoSistema();
    inicializarGraficoSalud();
    
    // Actualizar cada 30 segundos
    intervalActualizacion = setInterval(actualizarEstadoSistema, 30000);
});

function inicializarDashboard() {
    // Configurar color de barra de salud
    const porcentaje = {{ $saludSistema['porcentaje'] }};
    actualizarBarraSalud(porcentaje);
    
    console.log('Dashboard de Prevención inicializado');
}

function actualizarBarraSalud(porcentaje) {
    const barra = document.getElementById('barrasalud');
    const display = document.getElementById('saludDisplay');
    const estado = document.getElementById('estadoSistema');
    
    // Actualizar porcentaje
    barra.style.width = porcentaje + '%';
    display.textContent = porcentaje + '%';
    
    // Actualizar color y estado
    if (porcentaje >= 90) {
        barra.className = 'progress-bar bg-success';
        estado.textContent = 'EXCELENTE';
        estado.className = 'text-success fw-bold';
    } else if (porcentaje >= 70) {
        barra.className = 'progress-bar bg-warning';
        estado.textContent = 'BUENO';
        estado.className = 'text-warning fw-bold';
    } else if (porcentaje >= 50) {
        barra.className = 'progress-bar bg-orange';
        estado.textContent = 'REGULAR';
        estado.className = 'text-orange fw-bold';
    } else {
        barra.className = 'progress-bar bg-danger';
        estado.textContent = 'CRÍTICO';
        estado.className = 'text-danger fw-bold';
    }
}

function actualizarEstadoSistema() {
    fetch('{{ route("admin.prevencion.estado_sistema") }}')
        .then(response => response.json())
        .then(data => {
            actualizarBarraSalud(data.salud_sistema);
            document.getElementById('alertasActivas').textContent = data.total_alertas;
            
            // Actualizar gráfico si existe
            if (chartSalud) {
                agregarPuntoGrafico(data.salud_sistema);
            }
        })
        .catch(error => {
            console.error('Error actualizando estado:', error);
        });
}

function inicializarGraficoSalud() {
    const ctx = document.getElementById('graficoSalud').getContext('2d');
    
    // Datos iniciales (últimas 24 mediciones)
    const labels = [];
    const datos = [];
    const ahora = new Date();
    
    for (let i = 23; i >= 0; i--) {
        const fecha = new Date(ahora.getTime() - (i * 60 * 60 * 1000)); // Cada hora
        labels.push(fecha.getHours() + ':00');
        datos.push(Math.random() * 20 + 80); // Datos simulados
    }
    
    chartSalud = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Salud del Sistema (%)',
                data: datos,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

function agregarPuntoGrafico(nuevoPorcentaje) {
    if (!chartSalud) return;
    
    const ahora = new Date();
    const nuevaLabel = ahora.getHours() + ':' + String(ahora.getMinutes()).padStart(2, '0');
    
    chartSalud.data.labels.push(nuevaLabel);
    chartSalud.data.datasets[0].data.push(nuevoPorcentaje);
    
    // Mantener solo últimos 24 puntos
    if (chartSalud.data.labels.length > 24) {
        chartSalud.data.labels.shift();
        chartSalud.data.datasets[0].data.shift();
    }
    
    chartSalud.update();
}

// OPCIÓN 1: Validación Preventiva
function probarValidacionPreventiva() {
    // Datos de prueba
    const datosVenta = {
        fecha: new Date().toISOString().split('T')[0],
        user_id: {{ auth()->id() ?? 1 }},
        cliente_id: 1
    };
    
    const detalles = [
        {
            articulo_id: 1,
            cantidad: 2,
            precio_unitario: 100.00
        }
    ];
    
    fetch('{{ route("admin.prevencion.validacion_preventiva") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            venta: datosVenta,
            detalles: detalles
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.resultado.valido) {
            Swal.fire({
                icon: 'success',
                title: '✅ Validación Exitosa',
                text: 'Todos los validaciones preventivas pasaron correctamente',
                confirmButtonColor: '#28a745'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: '⚠️ Errores Detectados',
                html: '<ul class="text-start">' + data.resultado.errores.map(error => '<li>' + error + '</li>').join('') + '</ul>',
                confirmButtonColor: '#dc3545'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error ejecutando validación preventiva'
        });
    });
}

// OPCIÓN 2: Transacciones Atómicas
function probarTransaccionAtomica() {
    // Datos de prueba
    const datosVenta = {
        fecha: new Date().toISOString().split('T')[0],
        user_id: {{ auth()->id() ?? 1 }},
        observaciones: 'Prueba de transacción atómica'
    };
    
    const detalles = [
        {
            articulo_id: 1,
            cantidad: 1,
            precio_unitario: 50.00
        }
    ];
    
    Swal.fire({
        title: '🔄 Ejecutando Transacción Atómica',
        text: 'Procesando venta con garantía ACID...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('{{ route("admin.prevencion.venta_atomica") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            fecha: datosVenta.fecha,
            user_id: datosVenta.user_id,
            observaciones: datosVenta.observaciones,
            detalles: detalles
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.exito) {
            Swal.fire({
                icon: 'success',
                title: '✅ Transacción Completada',
                html: `
                    <div class="text-start">
                        <p><strong>Venta ID:</strong> ${data.venta_id}</p>
                        <p><strong>Total:</strong> $${data.total}</p>
                        <p><strong>Estado:</strong> Transacción ACID exitosa</p>
                    </div>
                `,
                confirmButtonColor: '#28a745'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: '🔄 Rollback Ejecutado',
                html: `
                    <div class="text-start">
                        <p><strong>Error:</strong> ${data.error}</p>
                        <p><strong>Operaciones revertidas:</strong></p>
                        <ul>${data.operaciones_revertidas.map(op => '<li>' + op + '</li>').join('')}</ul>
                    </div>
                `,
                confirmButtonColor: '#dc3545'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error ejecutando transacción atómica'
        });
    });
}

// OPCIÓN 3: Monitoreo Continuo
function ejecutarMonitoreo() {
    Swal.fire({
        title: '🤖 Ejecutando Monitoreo Continuo',
        text: 'Analizando sistema y aplicando correcciones...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    const configuracion = {
        correccion_automatica: document.getElementById('correccionAutomatica').checked,
        max_correcciones_automaticas: 10
    };
    
    fetch('{{ route("admin.prevencion.monitoreo_continuo") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(configuracion)
    })
    .then(response => response.json())
    .then(data => {
        Swal.fire({
            icon: data.exito ? 'success' : 'error',
            title: '🤖 Monitoreo Completado',
            html: `
                <div class="text-start">
                    <p><strong>Alertas detectadas:</strong> ${data.alertas}</p>
                    <p><strong>Correcciones aplicadas:</strong> ${data.correcciones}</p>
                    <p><strong>Salud del sistema:</strong> ${data.salud_sistema}%</p>
                    <p><strong>Estado:</strong> <span class="badge bg-${data.salud_sistema > 80 ? 'success' : 'warning'}">${data.salud_sistema > 80 ? 'BUENO' : 'REQUIERE ATENCIÓN'}</span></p>
                </div>
            `,
            confirmButtonColor: '#28a745'
        });
        
        // Actualizar dashboard
        actualizarEstadoSistema();
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error ejecutando monitoreo continuo'
        });
    });
}

function guardarConfiguracion() {
    const configuracion = {
        habilitado: document.getElementById('monitoreoAutomatico').checked,
        intervalo_minutos: parseInt(document.getElementById('intervaloMonitoreo').value),
        correccion_automatica: document.getElementById('correccionAutomatica').checked,
        max_correcciones_por_ciclo: 10,
        alertas_email: false
    };
    
    fetch('{{ route("admin.prevencion.configurar_monitoreo") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(configuracion)
    })
    .then(response => response.json())
    .then(data => {
        if (data.exito) {
            Swal.fire({
                icon: 'success',
                title: 'Configuración Guardada',
                text: 'Los cambios se han aplicado correctamente',
                timer: 2000,
                showConfirmButton: false
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error guardando configuración'
        });
    });
}

function verDetalleAlerta(alertaId) {
    // Mostrar modal con detalles de la alerta
    $('#modalDetalleAlerta').modal('show');
    document.getElementById('contenidoDetalleAlerta').innerHTML = '<p>Cargando detalles de alerta...</p>';
}

// Limpiar intervalo al salir
window.addEventListener('beforeunload', function() {
    if (intervalActualizacion) {
        clearInterval(intervalActualizacion);
    }
});
</script>
@endsection

@section('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
}

.card {
    transition: transform 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
}

.badge {
    font-size: 0.75em;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.text-orange {
    color: #fd7e14 !important;
}

.bg-orange {
    background-color: #fd7e14 !important;
}
</style>
@endsection
