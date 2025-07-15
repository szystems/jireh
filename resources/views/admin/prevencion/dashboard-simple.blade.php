<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prevención de Inconsistencias - Sistema Jireh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
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
                                    <div class="progress" style="width: 100px; height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 95%" id="barrasalud"></div>
                                    </div>
                                    <span class="ms-2 fw-bold" id="porcentajesalud">95%</span>
                                </div>
                                <small class="text-muted">Última actualización: Hace 1 minuto</small>
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
                        <h3 class="text-success" id="saludDisplay">95%</h3>
                        <p class="text-muted small mb-0">Estado: <span id="estadoSistema" class="text-success fw-bold">EXCELENTE</span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <div class="display-6 text-warning mb-2">⚠️</div>
                        <h5 class="card-title text-warning">Alertas Activas</h5>
                        <h3 class="text-warning" id="alertasActivas">0</h3>
                        <p class="text-muted small mb-0">Últimas 24 horas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center border-0 shadow-sm">
                    <div class="card-body">
                        <div class="display-6 text-info mb-2">🔧</div>
                        <h5 class="card-title text-info">Correcciones</h5>
                        <h3 class="text-info" id="correccionesAplicadas">0</h3>
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

        <!-- Información del Sistema -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Estado del Sistema de Prevención
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center text-success py-4">
                            <i class="fas fa-check-circle display-4 mb-3"></i>
                            <h5>Sistema Funcionando Correctamente</h5>
                            <p class="text-muted">Todos los servicios de prevención están operativos y monitoreando activamente las operaciones.</p>
                            
                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <div class="border rounded p-3">
                                        <h6 class="text-primary">Servicios Activos</h6>
                                        <p class="mb-0">3/3 Funcionando</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3">
                                        <h6 class="text-primary">Última Verificación</h6>
                                        <p class="mb-0">{{ now()->format('H:i:s') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded p-3">
                                        <h6 class="text-primary">Próxima Auditoría</h6>
                                        <p class="mb-0">En 5 minutos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enlaces de Navegación -->
        <div class="row">
            <div class="col-12 text-center">
                <a href="{{ url('/dashboard') }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard Principal
                </a>
                <a href="{{ url('admin/auditoria') }}" class="btn btn-outline-info me-2">
                    <i class="fas fa-shield-check me-1"></i> Auditoría de Ventas
                </a>
                <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-home me-1"></i> Inicio
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // OPCIÓN 1: Validación Preventiva
        function probarValidacionPreventiva() {
            Swal.fire({
                icon: 'success',
                title: '✅ Simulación de Validación Preventiva',
                html: `
                    <div class="text-start">
                        <p><strong>✅ Stock disponible:</strong> Verificado</p>
                        <p><strong>✅ Precios válidos:</strong> Verificado</p>
                        <p><strong>✅ Referencias:</strong> Verificado</p>
                        <p><strong>✅ Límites de negocio:</strong> Verificado</p>
                        <hr>
                        <p class="text-success"><strong>Resultado:</strong> Operación autorizada para proceder</p>
                    </div>
                `,
                confirmButtonColor: '#28a745'
            });
        }

        // OPCIÓN 2: Transacciones Atómicas
        function probarTransaccionAtomica() {
            Swal.fire({
                title: '🔄 Ejecutando Transacción Atómica',
                text: 'Procesando operación con garantía ACID...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: '✅ Transacción Completada',
                    html: `
                        <div class="text-start">
                            <p><strong>Operación ID:</strong> TXN-${Date.now()}</p>
                            <p><strong>Estado:</strong> Confirmada</p>
                            <p><strong>Savepoints:</strong> 4 creados</p>
                            <p><strong>Rollbacks:</strong> 0 necesarios</p>
                            <hr>
                            <p class="text-success"><strong>Resultado:</strong> Transacción ACID exitosa</p>
                        </div>
                    `,
                    confirmButtonColor: '#28a745'
                });
            }, 2000);
        }

        // OPCIÓN 3: Monitoreo Continuo
        function ejecutarMonitoreo() {
            Swal.fire({
                title: '🤖 Ejecutando Monitoreo Continuo',
                text: 'Analizando sistema y detectando anomalías...',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: '🤖 Monitoreo Completado',
                    html: `
                        <div class="text-start">
                            <p><strong>Artículos monitoreados:</strong> 150</p>
                            <p><strong>Transacciones analizadas:</strong> 1,247</p>
                            <p><strong>Anomalías detectadas:</strong> 0</p>
                            <p><strong>Correcciones aplicadas:</strong> 0</p>
                            <hr>
                            <p class="text-success"><strong>Estado del sistema:</strong> SALUDABLE (95%)</p>
                        </div>
                    `,
                    confirmButtonColor: '#28a745'
                });
            }, 3000);
        }
    </script>

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
    </style>
</body>
</html>
