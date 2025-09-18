<!-- Sección: Primeros Pasos -->
<div class="help-section">
    <div class="row">
        <div class="col-12">
            <div class="quick-access">
                <h4 class="text-center mb-4">
                    <i class="bi bi-rocket-takeoff text-primary"></i>
                    ¡Bienvenido al Sistema Jireh!
                </h4>
                <p class="text-center text-muted mb-4">
                    Sigue esta guía paso a paso para configurar tu sistema y comenzar a trabajar de manera eficiente.
                </p>
                
                <!-- Acceso rápido a secciones -->
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="#config-inicial" class="quick-access-item d-block">
                            <i class="bi bi-gear-fill module-icon"></i>
                            <h6>Configuración</h6>
                            <small>Datos básicos</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#flujo-trabajo" class="quick-access-item d-block">
                            <i class="bi bi-diagram-3-fill module-icon"></i>
                            <h6>Flujo de Trabajo</h6>
                            <small>Orden correcto</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#datos-maestros" class="quick-access-item d-block">
                            <i class="bi bi-database-fill module-icon"></i>
                            <h6>Datos Maestros</h6>
                            <small>Información base</small>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#primera-venta" class="quick-access-item d-block">
                            <i class="bi bi-cash-stack module-icon"></i>
                            <h6>Primera Venta</h6>
                            <small>¡Empezar a vender!</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Accordion para los pasos detallados -->
    <div class="accordion" id="primerosPassosAccordion">
        
        <!-- Paso 1: Configuración Inicial -->
        <div class="accordion-item" id="config-inicial">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseConfig">
                    <span class="step-number me-3">1</span>
                    <div>
                        <strong>Configuración Inicial del Sistema</strong>
                        <br><small class="text-muted">Establece los datos básicos de tu empresa y sistema</small>
                    </div>
                </button>
            </h2>
            <div id="collapseConfig" class="accordion-collapse collapse show" data-bs-parent="#primerosPassosAccordion">
                <div class="accordion-body">
                    <div class="help-card">
                        <h5><i class="bi bi-building text-primary"></i> Datos de tu Empresa</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <ol>
                                    <li>Ve a <strong>Administración → Configuración</strong></li>
                                    <li>Completa los datos básicos:
                                        <ul>
                                            <li><strong>Nombre de la empresa:</strong> Ej: "Jireh Automotriz"</li>
                                            <li><strong>Moneda:</strong> Quetzales (Q) o Dólares ($)</li>
                                            <li><strong>Símbolo de moneda:</strong> Q o $</li>
                                            <li><strong>Dirección y contactos</strong></li>
                                        </ul>
                                    </li>
                                    <li>Guarda los cambios</li>
                                </ol>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-info">
                                    <i class="bi bi-lightbulb-fill"></i>
                                    <strong>Consejo:</strong> Esta información aparecerá en todas las cotizaciones y reportes.
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($isAdmin)
                    <div class="help-card">
                        <h5><i class="bi bi-people text-success"></i> Usuarios del Sistema</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <ol>
                                    <li>Ve a <strong>Administración → Seguridad → Usuarios</strong></li>
                                    <li>Crea usuarios para tu equipo:
                                        <ul>
                                            <li><strong>Administradores:</strong> Acceso total al sistema</li>
                                            <li><strong>Vendedores:</strong> Acceso a ventas, inventario, compras y trabajadores (sin eliminación)</li>
                                        </ul>
                                    </li>
                                    <li>Asigna roles apropiados</li>
                                    <li>Proporciona credenciales a cada usuario</li>
                                </ol>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-warning">
                                    <i class="bi bi-shield-exclamation"></i>
                                    <strong>Importante:</strong> Los vendedores tienen acceso a inventario, compras y trabajadores, pero no pueden eliminar registros ni ver costos/ganancias.
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Paso 2: Flujo de Trabajo -->
        <div class="accordion-item" id="flujo-trabajo">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFlujo">
                    <span class="step-number me-3">2</span>
                    <div>
                        <strong>Entender el Flujo de Trabajo</strong>
                        <br><small class="text-muted">Orden recomendado para configurar todos los módulos</small>
                    </div>
                </button>
            </h2>
            <div id="collapseFlujo" class="accordion-collapse collapse" data-bs-parent="#primerosPassosAccordion">
                <div class="accordion-body">
                    <div class="config-flow">
                        <div class="text-center mb-4">
                            <h5>🔄 Secuencia Recomendada de Configuración</h5>
                            <p class="text-muted">Sigue este orden para evitar problemas de dependencias</p>
                        </div>

                        <div class="config-step completed">
                            <div class="d-flex align-items-start">
                                <span class="step-number me-3">1</span>
                                <div>
                                    <h6><strong>Configuración Básica</strong> ✅</h6>
                                    <p class="mb-1">Datos de empresa, moneda, usuarios</p>
                                    <small class="text-muted">Ya completado en el paso anterior</small>
                                </div>
                            </div>
                        </div>

                        @if($isAdmin)
                        <div class="config-step active">
                            <div class="d-flex align-items-start">
                                <span class="step-number me-3">2</span>
                                <div>
                                    <h6><strong>Unidades de Medida</strong> 📏</h6>
                                    <p class="mb-1">kg, litros, piezas, metros, galones, onzas</p>
                                    <small class="text-success">Requerido antes de crear productos</small>
                                </div>
                            </div>
                        </div>

                        <div class="config-step">
                            <div class="d-flex align-items-start">
                                <span class="step-number me-3">3</span>
                                <div>
                                    <h6><strong>Categorías</strong> 📂</h6>
                                    <p class="mb-1">Aceites, Filtros, Llantas, Servicios, Repuestos</p>
                                    <small class="text-info">Organiza tus productos</small>
                                </div>
                            </div>
                        </div>

                        <div class="config-step">
                            <div class="d-flex align-items-start">
                                <span class="step-number me-3">4</span>
                                <div>
                                    <h6><strong>Proveedores</strong> 🏢</h6>
                                    <p class="mb-1">Empresas que te suministran productos</p>
                                    <small class="text-info">Necesario para registrar compras</small>
                                </div>
                            </div>
                        </div>

                        <div class="config-step">
                            <div class="d-flex align-items-start">
                                <span class="step-number me-3">5</span>
                                <div>
                                    <h6><strong>Artículos y Servicios</strong> 🛠️</h6>
                                    <p class="mb-1">Productos físicos y servicios que ofreces</p>
                                    <small class="text-warning">Base de tu inventario y catálogo de servicios</small>
                                    
                                    <div class="mt-2">
                                        <small class="d-block"><i class="bi bi-box text-primary"></i> <strong>Artículos:</strong> Productos con inventario (aceite, filtros, repuestos)</small>
                                        <small class="d-block"><i class="bi bi-tools text-success"></i> <strong>Servicios:</strong> Trabajos realizados (cambio aceite, lavado, reparación)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="config-step">
                            <div class="d-flex align-items-start">
                                <span class="step-number me-3">6</span>
                                <div>
                                    <h6><strong>Trabajadores</strong> 👷</h6>
                                    <p class="mb-1">Mecánicos, personal de car wash, vendedores</p>
                                    <small class="text-info">Para calcular comisiones</small>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="config-step">
                            <div class="d-flex align-items-start">
                                <span class="step-number me-3">{{ $isAdmin ? '7' : '2' }}</span>
                                <div>
                                    <h6><strong>Clientes y Vehículos</strong> 👥🚗</h6>
                                    <p class="mb-1">Base de datos de clientes y sus vehículos</p>
                                    <small class="text-success">Listos para vender</small>
                                </div>
                            </div>
                        </div>

                        <div class="config-step">
                            <div class="d-flex align-items-start">
                                <span class="step-number me-3">{{ $isAdmin ? '8' : '3' }}</span>
                                <div>
                                    <h6><strong>¡Empezar a Operar!</strong> 🚀</h6>
                                    <p class="mb-1">Ventas, cotizaciones, inventario</p>
                                    <small class="text-primary">¡Tu sistema está listo!</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paso 3: Datos Maestros -->
        <div class="accordion-item" id="datos-maestros">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDatos">
                    <span class="step-number me-3">3</span>
                    <div>
                        <strong>Configurar Datos Maestros</strong>
                        <br><small class="text-muted">Información base necesaria para operar</small>
                    </div>
                </button>
            </h2>
            <div id="collapseDatos" class="accordion-collapse collapse" data-bs-parent="#primerosPassosAccordion">
                <div class="accordion-body">
                    @if($isAdmin)
                    <!-- Unidades de Medida -->
                    <div class="help-card">
                        <h5><i class="bi bi-rulers text-primary"></i> Unidades de Medida</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Ruta:</strong> Inventario y Catálogos → Almacén → Unidades de Medida</p>
                                
                                <div class="alert alert-warning">
                                    <h6><i class="bi bi-exclamation-triangle"></i> Tipos importantes:</h6>
                                    <ul class="mb-0">
                                        <li><strong>Unidad:</strong> Solo números enteros (1, 2, 3, 5...)</li>
                                        <li><strong>Decimal:</strong> Permite decimales (1.5, 2.75, 0.5...)</li>
                                    </ul>
                                </div>
                                
                                <p><strong>Ejemplos recomendados:</strong></p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Unidades Enteras:</h6>
                                        <ul>
                                            <li><strong>Pieza</strong> - Para repuestos individuales</li>
                                            <li><strong>Par</strong> - Para llantas, zapatas</li>
                                            <li><strong>Servicio</strong> - Para servicios</li>
                                            <li><strong>Hora</strong> - Para mano de obra</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Unidades Decimales:</h6>
                                        <ul>
                                            <li><strong>Litro</strong> - Para aceites y líquidos</li>
                                            <li><strong>Galón</strong> - Para líquidos en mayor cantidad</li>
                                            <li><strong>Kilogramo</strong> - Para productos por peso</li>
                                            <li><strong>Metro</strong> - Para cables, mangueras</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Tip:</strong> Crea todas las unidades que usarás antes de crear productos.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Categorías -->
                    <div class="help-card">
                        <h5><i class="bi bi-diagram-3 text-success"></i> Categorías de Productos</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Ruta:</strong> Inventario y Catálogos → Almacén → Categorías</p>
                                <p><strong>Categorías recomendadas para talleres automotrices:</strong></p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul>
                                            <li><strong>Aceites y Lubricantes</strong></li>
                                            <li><strong>Filtros</strong></li>
                                            <li><strong>Llantas y Rines</strong></li>
                                            <li><strong>Frenos</strong></li>
                                            <li><strong>Suspensión</strong></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul>
                                            <li><strong>Servicios Car Wash</strong></li>
                                            <li><strong>Servicios Mecánicos</strong></li>
                                            <li><strong>Repuestos Motor</strong></li>
                                            <li><strong>Accesorios</strong></li>
                                            <li><strong>Químicos</strong></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle"></i>
                                    <strong>Beneficio:</strong> Organiza mejor tu inventario y reportes.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Proveedores -->
                    <div class="help-card">
                        <h5><i class="bi bi-building text-warning"></i> Proveedores</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Ruta:</strong> Operaciones → Compras → Proveedores</p>
                                <p><strong>Datos importantes a registrar:</strong></p>
                                <ul>
                                    <li><strong>Nombre o razón social</strong></li>
                                    <li><strong>NIT o documento de identificación</strong></li>
                                    <li><strong>Teléfonos y email de contacto</strong></li>
                                    <li><strong>Dirección de entrega</strong></li>
                                    <li><strong>Contacto principal</strong></li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <strong>Importante:</strong> Necesarios para registrar compras e ingresos al inventario.
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Clientes (Visible para todos) -->
                    <div class="help-card">
                        <h5><i class="bi bi-people text-info"></i> Clientes</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Ruta:</strong> Gestión de Clientes → Clientes</p>
                                <p><strong>Información a recopilar:</strong></p>
                                <ul>
                                    <li><strong>Datos personales:</strong> Nombre, DPI, NIT</li>
                                    <li><strong>Contacto:</strong> Teléfonos, email, dirección</li>
                                    <li><strong>Fecha de nacimiento</strong> (para promociones)</li>
                                </ul>
                                <p><strong>Después registra los vehículos:</strong></p>
                                <ul>
                                    <li><strong>Marca, modelo, año</strong></li>
                                    <li><strong>Placa del vehículo</strong></li>
                                    <li><strong>Color y detalles adicionales</strong></li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-info">
                                    <i class="bi bi-lightbulb"></i>
                                    <strong>Consejo:</strong> Un cliente puede tener múltiples vehículos registrados.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paso 4: Primera Venta -->
        <div class="accordion-item" id="primera-venta">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVenta">
                    <span class="step-number me-3">4</span>
                    <div>
                        <strong>¡Realizar tu Primera Venta!</strong>
                        <br><small class="text-muted">Ya tienes todo listo para empezar a vender</small>
                    </div>
                </button>
            </h2>
            <div id="collapseVenta" class="accordion-collapse collapse" data-bs-parent="#primerosPassosAccordion">
                <div class="accordion-body">
                    <div class="help-card">
                        <h5><i class="bi bi-cash-stack text-success"></i> Proceso de Venta Completo</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Ruta:</strong> Ventas → Ventas → Agregar Venta</p>
                                <ol>
                                    <li><strong>Selecciona el cliente y vehículo</strong>
                                        <ul><li>Si no existe, créalo desde el botón "Nuevo Cliente"</li></ul>
                                    </li>
                                    <li><strong>Agrega productos o servicios</strong>
                                        <ul><li>Busca por nombre o categoría</li>
                                        <li>Ajusta cantidad según necesidad</li></ul>
                                    </li>
                                    <li><strong>Asigna trabajadores</strong>
                                        <ul><li>Para calcular comisiones automáticamente</li></ul>
                                    </li>
                                    <li><strong>Aplica descuentos si es necesario</strong></li>
                                    <li><strong>Registra los pagos</strong>
                                        <ul><li>Efectivo, tarjeta, crédito</li></ul>
                                    </li>
                                    <li><strong>Guarda la venta</strong></li>
                                </ol>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle"></i>
                                    <strong>¡Felicidades!</strong><br>
                                    El sistema automáticamente:
                                    <ul class="mt-2 mb-0">
                                        <li>Actualiza el inventario</li>
                                        <li>Calcula comisiones</li>
                                        <li>Genera reportes</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-file-earmark-text text-primary"></i> Cotizaciones (Opcional)</h5>
                        <p>Si el cliente quiere una cotización antes de decidir:</p>
                        <ol>
                            <li>Ve a <strong>Cotizaciones</strong></li>
                            <li>Crea una cotización con los productos</li>
                            <li>Genera el PDF para entregar al cliente</li>
                            <li>Cuando el cliente apruebe, puedes regenerar la cotización</li>
                        </ol>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Funcionalidad avanzada:</strong> Las cotizaciones tienen estados (Generado/Aprobado) y vigencia automática de 15 días.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensaje de finalización -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-success">
                <div class="d-flex align-items-center">
                    <i class="bi bi-trophy-fill fs-3 me-3 text-warning"></i>
                    <div>
                        <h5 class="mb-1">🎉 ¡Sistema Configurado Correctamente!</h5>
                        <p class="mb-0">
                            Siguiendo estos pasos, tu Sistema Jireh está listo para administrar tu taller automotriz de manera profesional. 
                            Explora los otros tabs para aprender sobre módulos específicos.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>