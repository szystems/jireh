<!-- Sección: Módulos del Sistema -->
<div class="help-section">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <h5><i class="bi bi-info-circle"></i> Guía Completa de Módulos</h5>
                <p class="mb-0">Aprende a usar cada módulo del sistema para maximizar tu productividad. El contenido se adapta a tu rol de usuario.</p>
            </div>
        </div>
    </div>

    <!-- Accordion para módulos -->
    <div class="accordion" id="modulosAccordion">
        
        <!-- Dashboard -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDashboard">
                    <i class="bi bi-speedometer2 text-primary me-3"></i>
                    <div>
                        <strong>Dashboard Principal</strong>
                        <br><small class="text-muted">Tu centro de control y vista general del negocio</small>
                    </div>
                </button>
            </h2>
            <div id="collapseDashboard" class="accordion-collapse collapse show" data-bs-parent="#modulosAccordion">
                <div class="accordion-body">
                    <div class="help-card">
                        <h5><i class="bi bi-graph-up text-success"></i> Información Clave del Dashboard</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Acceso:</strong> Automático al iniciar sesión</p>
                                
                                @if($isAdmin)
                                <h6>Vista de Administrador:</h6>
                                <ul>
                                    <li><strong>Resumen de ventas</strong> - Totales del día/mes</li>
                                    <li><strong>Inventario bajo</strong> - Productos que necesitan restock</li>
                                    <li><strong>Comisiones pendientes</strong> - Pagos por procesar</li>
                                    <li><strong>Métricas de trabajadores</strong> - Rendimiento del equipo</li>
                                    <li><strong>Accesos rápidos</strong> - Enlaces a funciones principales</li>
                                </ul>
                                @else
                                <h6>Vista de Vendedor:</h6>
                                <ul>
                                    <li><strong>Mis ventas</strong> - Ventas realizadas por ti</li>
                                    <li><strong>Mis comisiones</strong> - Comisiones ganadas</li>
                                    <li><strong>Clientes atendidos</strong> - Tu base de clientes</li>
                                    <li><strong>Accesos rápidos</strong> - Funciones que puedes usar</li>
                                </ul>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-primary">
                                    <i class="bi bi-lightbulb"></i>
                                    <strong>Tip:</strong> El dashboard se actualiza en tiempo real. Úsalo para monitorear el rendimiento diario.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestión de Clientes -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseClientes">
                    <i class="bi bi-people text-info me-3"></i>
                    <div>
                        <strong>Gestión de Clientes y Vehículos</strong>
                        <br><small class="text-muted">Administra tu base de datos de clientes y sus vehículos</small>
                    </div>
                </button>
            </h2>
            <div id="collapseClientes" class="accordion-collapse collapse" data-bs-parent="#modulosAccordion">
                <div class="accordion-body">
                    <div class="help-card">
                        <h5><i class="bi bi-person-plus text-info"></i> Módulo de Clientes</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Ruta:</strong> Gestión de Clientes → Clientes</p>
                                
                                <h6>Funciones principales:</h6>
                                <ul>
                                    <li><strong>Agregar cliente nuevo:</strong> Datos personales completos</li>
                                    <li><strong>Buscar cliente:</strong> Por nombre, DPI o teléfono</li>
                                    <li><strong>Historial de servicios:</strong> Ver todas las ventas del cliente</li>
                                    <li><strong>Datos de contacto:</strong> Teléfonos con enlaces directos a WhatsApp</li>
                                </ul>

                                <h6>Campos importantes:</h6>
                                <ul>
                                    <li><strong>DPI/Cédula:</strong> Identificación oficial</li>
                                    <li><strong>NIT:</strong> Para facturación fiscal</li>
                                    <li><strong>Fecha de nacimiento:</strong> Para promociones de cumpleaños</li>
                                    <li><strong>Dirección completa:</strong> Para entregas a domicilio</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-info">
                                    <i class="bi bi-search"></i>
                                    <strong>Búsqueda inteligente:</strong> Puedes buscar por nombre parcial, número de DPI o teléfono.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-car-front text-primary"></i> Módulo de Vehículos</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Ruta:</strong> Gestión de Clientes → Vehículos</p>
                                
                                <h6>Gestión de vehículos:</h6>
                                <ul>
                                    <li><strong>Vincular a cliente:</strong> Un cliente puede tener varios vehículos</li>
                                    <li><strong>Datos del vehículo:</strong> Marca, modelo, año, color</li>
                                    <li><strong>Placa:</strong> Identificación única del vehículo</li>
                                    <li><strong>Historial de servicios:</strong> Mantenimientos realizados</li>
                                </ul>

                                <h6>Flujo recomendado:</h6>
                                <ol>
                                    <li>Primero crea el cliente</li>
                                    <li>Luego agrega sus vehículos</li>
                                    <li>En las ventas, selecciona cliente + vehículo específico</li>
                                </ol>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-success">
                                    <i class="bi bi-check-circle"></i>
                                    <strong>Ventaja:</strong> Mantén historial detallado de servicios por vehículo específico.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($isAdmin)
        <!-- Inventario y Catálogos - Solo Administradores con permisos completos -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInventario">
                    <i class="bi bi-boxes text-warning me-3"></i>
                    <div>
                        <strong>Inventario y Catálogos</strong>
                        <span class="badge badge-role bg-danger ms-2">Administrador - Control Total</span>
                        <br><small class="text-muted">Control completo de productos, categorías y stock</small>
                    </div>
                </button>
            </h2>
            <div id="collapseInventario" class="accordion-collapse collapse" data-bs-parent="#modulosAccordion">
                <div class="accordion-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-shield-check"></i>
                        <strong>Vista de Administrador:</strong> Tienes control total sobre inventario, incluyendo eliminación de registros y gestión de precios de costo.
                    </div>
        @else
        <!-- Inventario y Catálogos - Vendedores con acceso limitado -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInventario">
                    <i class="bi bi-boxes text-warning me-3"></i>
                    <div>
                        <strong>Inventario y Catálogos</strong>
                        <span class="badge badge-role bg-info ms-2">Vendedor - Acceso Limitado</span>
                        <br><small class="text-muted">Consulta y gestión de productos, categorías y stock (sin eliminación)</small>
                    </div>
                </button>
            </h2>
            <div id="collapseInventario" class="accordion-collapse collapse" data-bs-parent="#modulosAccordion">
                <div class="accordion-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Vista de Vendedor:</strong> Puedes consultar, crear y editar productos, categorías y unidades, pero NO puedes eliminar registros existentes.
                    </div>
        @endif
                    <div class="help-card">
                        <h5><i class="bi bi-diagram-3 text-info"></i> Categorías</h5>
                        <p><strong>Ruta:</strong> Inventario y Catálogos → Almacén → Categorías</p>
                        <ul>
                            <li><strong>Propósito:</strong> Organizar productos por tipo</li>
                            <li><strong>Ejemplos:</strong> Aceites, Filtros, Llantas, Servicios</li>
                            <li><strong>Beneficio:</strong> Facilita búsquedas y reportes organizados</li>
                        </ul>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-rulers text-secondary"></i> Unidades de Medida</h5>
                        <p><strong>Ruta:</strong> Inventario y Catálogos → Almacén → Unidades de Medida</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Tipos de unidades disponibles:</h6>
                                <ul>
                                    <li><strong>Unidad (Enteros):</strong> Piezas, servicios, productos únicos</li>
                                    <li><strong>Decimal:</strong> Litros, kilogramos, metros, galones</li>
                                </ul>
                                
                                <h6>Ejemplos comunes:</h6>
                                <ul>
                                    <li><strong>Pieza, Unidad:</strong> Para productos individuales</li>
                                    <li><strong>Litro, Galón:</strong> Para líquidos (aceite, gasolina)</li>
                                    <li><strong>Kilogramo, Libra:</strong> Para peso</li>
                                    <li><strong>Metro, Yarda:</strong> Para medidas lineales</li>
                                    <li><strong>Servicio:</strong> Para trabajos de mecánica/carwash</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6><i class="bi bi-calculator"></i> Diferencias importantes:</h6>
                                    <ul class="mb-0">
                                        <li><strong>Unidad:</strong> Solo números enteros (1, 2, 3, 5...)</li>
                                        <li><strong>Decimal:</strong> Permite decimales (1.5, 2.75, 0.5...)</li>
                                    </ul>
                                </div>
                                
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <strong>Importante:</strong> Crear las unidades antes que los productos que las van a usar.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-box text-primary"></i> Artículos y Servicios</h5>
                        <p><strong>Ruta:</strong> Inventario y Catálogos → Almacén → Artículos y Servicios</p>
                        
                        <div class="row">
                            <div class="col-12">
                                <h6>🔧 Diferencias entre Artículos y Servicios:</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card border-primary">
                                            <div class="card-header bg-primary text-white">
                                                <h6 class="mb-0"><i class="bi bi-box"></i> Artículos/Productos</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="mb-0">
                                                    <li>Productos físicos con inventario</li>
                                                    <li>Se controla stock y existencias</li>
                                                    <li>Ejemplos: Aceite, filtros, repuestos</li>
                                                    <li>Precio fijo por unidad</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-success">
                                            <div class="card-header bg-success text-white">
                                                <h6 class="mb-0"><i class="bi bi-tools"></i> Servicios</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="mb-0">
                                                    <li>Trabajos o labores realizadas</li>
                                                    <li>No tienen stock físico</li>
                                                    <li>Ejemplos: Cambio aceite, lavado, reparación</li>
                                                    <li>Pueden incluir componentes</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6>📝 Campos principales del formulario:</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul>
                                        <li><strong>Nombre del producto/servicio</strong></li>
                                        <li><strong>Código/SKU</strong> (opcional)</li>
                                        <li><strong>Categoría</strong></li>
                                        <li><strong>Unidad de medida</strong></li>
                                        <li><strong>Precio de costo</strong></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul>
                                        <li><strong>Precio de venta</strong></li>
                                        <li><strong>Stock actual</strong> (solo artículos)</li>
                                        <li><strong>Stock mínimo</strong> (solo artículos)</li>
                                        <li><strong>Descripción</strong></li>
                                        <li><strong>Estado</strong> (Activo/Inactivo)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h6>🔧 Configuración Especial para Servicios:</h6>
                            
                            <div class="card border-warning mb-3">
                                <div class="card-header bg-warning">
                                    <h6 class="mb-0"><i class="bi bi-wrench"></i> Servicios de Mecánico</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Configuración de comisión por mecánico:</strong></p>
                                    <ol>
                                        <li>Al crear el servicio, marca <strong>"Tiene comisión de mecánico"</strong></li>
                                        <li>Define el <strong>porcentaje de comisión</strong> para el mecánico</li>
                                        <li>En la venta, selecciona qué <strong>mecánico realizó el trabajo</strong></li>
                                        <li>El sistema calculará automáticamente la comisión</li>
                                    </ol>
                                    <div class="alert alert-info mb-0">
                                        <i class="bi bi-info-circle"></i> La comisión se asigna al mecánico específico que realizó el trabajo.
                                    </div>
                                </div>
                            </div>

                            <div class="card border-info mb-3">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0"><i class="bi bi-droplet"></i> Servicios de Car Wash</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Configuración de comisión para car wash:</strong></p>
                                    <ol>
                                        <li>Al crear el servicio, marca <strong>"Tiene comisión de car wash"</strong></li>
                                        <li>Define el <strong>porcentaje de comisión</strong> para car wash</li>
                                        <li>En la venta, los trabajadores de car wash se asignan en el <strong>proceso de venta</strong></li>
                                        <li>La comisión se distribuye entre los trabajadores asignados</li>
                                    </ol>
                                    <div class="alert alert-warning mb-0">
                                        <i class="bi bi-exclamation-triangle"></i> Los trabajadores de car wash se asignan durante la venta, no en el producto.
                                    </div>
                                </div>
                            </div>

                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="bi bi-puzzle"></i> Componentes de Servicios</h6>
                                </div>
                                <div class="card-body">
                                    <p><strong>Para servicios complejos:</strong></p>
                                    <ol>
                                        <li>Después de crear el servicio principal, ve a <strong>"Gestionar Componentes"</strong></li>
                                        <li>Agrega los <strong>artículos necesarios</strong> para realizar el servicio</li>
                                        <li>Define la <strong>cantidad requerida</strong> de cada componente</li>
                                        <li>Al vender el servicio, se descontarán automáticamente los componentes del inventario</li>
                                    </ol>
                                    <div class="alert alert-danger mb-0">
                                        <i class="bi bi-exclamation-octagon"></i> <strong>Obligatorio:</strong> Todo servicio debe tener al menos un componente para funcionar correctamente.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-shield-lock"></i>
                            <strong>Seguridad:</strong> Los precios de costo son sensibles - solo los administradores pueden verlos.
                        </div>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-inboxes text-success"></i> Control de Inventario</h5>
                        <p><strong>Ruta:</strong> Ventas → Inventario</p>
                        
                        <h6>Funciones de control:</h6>
                        <ul>
                            <li><strong>Ver stock actual:</strong> Productos disponibles</li>
                            <li><strong>Productos bajo mínimo:</strong> Alertas de restock</li>
                            <li><strong>Movimientos de stock:</strong> Historial de cambios</li>
                            <li><strong>Valorización:</strong> Valor total del inventario</li>
                        </ul>

                        <div class="alert alert-info">
                            <i class="bi bi-gear"></i>
                            <strong>Automático:</strong> El stock se actualiza automáticamente con cada venta e ingreso.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ventas y Cotizaciones -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVentas">
                    <i class="bi bi-cash-stack text-success me-3"></i>
                    <div>
                        <strong>Ventas y Cotizaciones</strong>
                        <br><small class="text-muted">Procesos principales de facturación y cotización</small>
                    </div>
                </button>
            </h2>
            <div id="collapseVentas" class="accordion-collapse collapse" data-bs-parent="#modulosAccordion">
                <div class="accordion-body">
                    <div class="help-card">
                        <h5><i class="bi bi-receipt text-success"></i> Módulo de Ventas</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Ruta:</strong> Ventas → Ventas</p>
                                
                                <h6>Proceso paso a paso:</h6>
                                <ol>
                                    <li><strong>Seleccionar cliente y vehículo</strong>
                                        <ul><li>Busca el cliente existente o crea uno nuevo</li></ul>
                                    </li>
                                    <li><strong>Agregar productos/servicios</strong>
                                        <ul><li>Busca por nombre o navega por categorías</li>
                                        <li>Ajusta cantidades según necesidad</li></ul>
                                    </li>
                                    <li><strong>Asignar trabajadores</strong>
                                        <ul><li>Para el cálculo automático de comisiones</li></ul>
                                    </li>
                                    <li><strong>Aplicar descuentos (opcional)</strong></li>
                                    <li><strong>Registrar pagos</strong>
                                        <ul><li>Efectivo, tarjeta, crédito o combinaciones</li></ul>
                                    </li>
                                    <li><strong>Finalizar venta</strong></li>
                                </ol>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-success">
                                    <i class="bi bi-magic"></i>
                                    <strong>Automático:</strong>
                                    <ul class="mt-2 mb-0">
                                        <li>Actualiza inventario</li>
                                        <li>Calcula comisiones</li>
                                        <li>Genera comprobantes</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        @if($isAdmin)
                        <h6>Información visible para administradores:</h6>
                        <ul>
                            <li><strong>Costos de productos</strong> - Para calcular ganancias</li>
                            <li><strong>Márgenes de ganancia</strong> - Rentabilidad por venta</li>
                            <li><strong>Reportes financieros</strong> - Análisis completo</li>
                        </ul>
                        @else
                        <div class="alert alert-info">
                            <i class="bi bi-shield-check"></i>
                            <strong>Como vendedor:</strong> No verás información de costos ni ganancias por políticas de seguridad de la empresa.
                        </div>
                        @endif
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-file-earmark-text text-primary"></i> Módulo de Cotizaciones ⭐</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Ruta:</strong> Cotizaciones</p>
                                
                                <h6>Sistema avanzado de cotizaciones:</h6>
                                <ul>
                                    <li><strong>Estados inteligentes:</strong> Generado → Aprobado</li>
                                    <li><strong>Vigencia automática:</strong> 15 días desde creación</li>
                                    <li><strong>5 pestañas organizadas:</strong>
                                        <ul>
                                            <li>Todas - Vista general</li>
                                            <li>Generadas - Cotizaciones nuevas</li>
                                            <li>Vigentes - Aún válidas</li>
                                            <li>Vencidas - Requieren regeneración</li>
                                            <li>Aprobadas - Confirmadas por cliente</li>
                                        </ul>
                                    </li>
                                    <li><strong>Regeneración:</strong> Renueva vigencia sin perder datos</li>
                                    <li><strong>PDF profesional:</strong> Con datos de empresa dinámicos</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-primary">
                                    <i class="bi bi-star-fill"></i>
                                    <strong>Nuevo y avanzado:</strong> Sistema completo implementado en septiembre 2025.
                                </div>
                            </div>
                        </div>

                        <h6>Flujo de trabajo con cotizaciones:</h6>
                        <ol>
                            <li>Cliente solicita cotización</li>
                            <li>Creas cotización (Estado: Generado)</li>
                            <li>Generas PDF para entregar</li>
                            <li>Cliente revisa (15 días de vigencia)</li>
                            <li>Si aprueba: cambias estado a Aprobado</li>
                            <li>Si necesita más tiempo: Regeneras (nuevos 15 días)</li>
                        </ol>
                    </div>

                    @if($isAdmin)
                    <div class="help-card">
                        <h5><i class="bi bi-graph-up text-info"></i> Reportes de Ventas</h5>
                        <p><strong>Ruta:</strong> Ventas → Artículos Vendidos</p>
                        <ul>
                            <li><strong>Productos más vendidos</strong></li>
                            <li><strong>Ventas por período</strong></li>
                            <li><strong>Rendimiento por trabajador</strong></li>
                            <li><strong>Análisis de rentabilidad</strong></li>
                        </ul>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-shield-check text-warning"></i> Auditoría de Ventas</h5>
                        <p><strong>Ruta:</strong> Ventas → Auditoría de Ventas</p>
                        <ul>
                            <li><strong>Seguimiento de cambios</strong> - Quién modificó qué</li>
                            <li><strong>Log de actividades</strong> - Historial completo</li>
                            <li><strong>Control de calidad</strong> - Verificar datos</li>
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($isAdmin)
        <!-- Personal y Comisiones - Solo Administradores -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePersonal">
                    <i class="bi bi-people-fill text-info me-3"></i>
                    <div>
                        <strong>Gestión de Personal y Comisiones</strong>
                        <span class="badge badge-role bg-danger ms-2">Administrador - Control Total</span>
                        <br><small class="text-muted">Control completo de empleados, sueldos y comisiones</small>
                    </div>
                </button>
            </h2>
            <div id="collapsePersonal" class="accordion-collapse collapse" data-bs-parent="#modulosAccordion">
                <div class="accordion-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-shield-check"></i>
                        <strong>Vista de Administrador:</strong> Tienes acceso completo a gestión de personal, incluyendo comisiones, sueldos y eliminación de registros.
                    </div>
        @endif

        <!-- Trabajadores - Acceso tanto para Administradores como Vendedores -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTrabajadores">
                    <i class="bi bi-person-badge text-info me-3"></i>
                    <div>
                        <strong>Módulo de Trabajadores</strong>
                        @if($isAdmin)
                        <span class="badge badge-role bg-danger ms-2">Administrador - Control Total</span>
                        @else
                        <span class="badge badge-role bg-info ms-2">Vendedor - Acceso Limitado</span>
                        @endif
                        <br><small class="text-muted">@if($isAdmin)Gestión completa de trabajadores@else Consulta y gestión de trabajadores (sin eliminación)@endif</small>
                    </div>
                </button>
            </h2>
            <div id="collapseTrabajadores" class="accordion-collapse collapse" data-bs-parent="#modulosAccordion">
                <div class="accordion-body">
                    @if($isAdmin)
                    <div class="alert alert-warning">
                        <i class="bi bi-shield-check"></i>
                        <strong>Vista de Administrador:</strong> Tienes control total sobre trabajadores, incluyendo eliminación de registros.
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Vista de Vendedor:</strong> Puedes consultar, crear y editar trabajadores, pero NO puedes eliminar registros existentes.
                    </div>
                    @endif

                    <div class="help-card">
                        <h5><i class="bi bi-person-badge text-primary"></i> Gestión de Trabajadores</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <p><strong>Ruta:</strong> Gestión de Personal → Trabajadores → Trabajadores</p>
                                
                                <h6>Tipos de trabajadores en el sistema:</h6>
                                <ul>
                                    <li><strong>Mecánico:</strong> Personal de taller mecánico</li>
                                    <li><strong>Car Wash:</strong> Personal de lavado y detallado</li>
                                </ul>

                                <h6>Información a registrar:</h6>
                                <ul>
                                    <li><strong>Datos personales:</strong> Nombre, DPI, contactos</li>
                                    <li><strong>Datos laborales:</strong> Fecha de ingreso, salario</li>
                                    <li><strong>Porcentaje de comisión:</strong> Para cálculo automático</li>
                                    <li><strong>Estado:</strong> Activo/Inactivo</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-info">
                                    <i class="bi bi-calculator"></i>
                                    <strong>Automático:</strong> Las comisiones se calculan automáticamente según las ventas asignadas.
                                </div>
                            </div>
                        </div>
                    </div>

        @if($isAdmin)
                    <div class="help-card">
                        <h5><i class="bi bi-currency-dollar text-success"></i> Sistema de Comisiones</h5>
                        <p><strong>Ruta:</strong> Sistema de Comisiones → [Varios submódulos]</p>
                        
                        <h6>Módulos del sistema:</h6>
                        <ul>
                            <li><strong>Dashboard:</strong> Vista general de comisiones</li>
                            <li><strong>Gestión y Pagos:</strong> Procesar pagos individuales</li>
                            <li><strong>Lotes de Pago:</strong> Agrupar pagos por período</li>
                            <li><strong>Metas de Ventas:</strong> Establecer objetivos</li>
                            <li><strong>Reporte de Metas:</strong> Seguimiento de cumplimiento</li>
                        </ul>

                        <h6>Proceso de comisiones:</h6>
                        <ol>
                            <li>Se realiza una venta con trabajadores asignados</li>
                            <li>Sistema calcula comisión automáticamente</li>
                            <li>Comisiones se acumulan hasta la fecha de pago</li>
                            <li>Se crea lote de pago para el período</li>
                            <li>Se procesan pagos individuales</li>
                        </ol>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-wallet2 text-warning"></i> Pagos de Sueldos</h5>
                        <p><strong>Ruta:</strong> Gestión de Personal → Trabajadores → Pagos de Sueldos</p>
                        
                        <h6>Funcionalidades:</h6>
                        <ul>
                            <li><strong>Registro de pagos:</strong> Sueldos base mensuales</li>
                            <li><strong>Deducciones:</strong> IGSS, ISR, anticipos</li>
                            <li><strong>Bonificaciones:</strong> Extras y bonos</li>
                            <li><strong>Reportes:</strong> Comprobantes de pago</li>
                            <li><strong>Historial:</strong> Seguimiento por empleado</li>
                        </ul>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Separado de comisiones:</strong> Los sueldos base son independientes del sistema de comisiones por ventas.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
                </div>
            </div>
        </div>
        @endif

        <!-- Compras y Proveedores - Acceso para Administradores y Vendedores -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCompras">
                    <i class="bi bi-cart4 text-secondary me-3"></i>
                    <div>
                        <strong>Compras y Proveedores</strong>
                        @if($isAdmin)
                        <span class="badge badge-role bg-danger ms-2">Administrador - Control Total</span>
                        @else
                        <span class="badge badge-role bg-info ms-2">Vendedor - Acceso Limitado</span>
                        @endif
                        <br><small class="text-muted">@if($isAdmin)Control completo de compras e ingresos@else Gestión de compras e ingresos (sin eliminación)@endif</small>
                    </div>
                </button>
            </h2>
            <div id="collapseCompras" class="accordion-collapse collapse" data-bs-parent="#modulosAccordion">
                <div class="accordion-body">
                    @if($isAdmin)
                    <div class="alert alert-warning">
                        <i class="bi bi-shield-check"></i>
                        <strong>Vista de Administrador:</strong> Tienes control total sobre compras y proveedores, incluyendo eliminación de registros.
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Vista de Vendedor:</strong> Puedes consultar, crear y editar compras y proveedores, pero NO puedes eliminar registros existentes.
                    </div>
                    @endif

                    <div class="help-card">
                        <h5><i class="bi bi-building text-info"></i> Módulo de Proveedores</h5>
                        <p><strong>Ruta:</strong> Operaciones → Compras → Proveedores</p>
                        
                        <h6>Gestión de proveedores:</h6>
                        <ul>
                            <li><strong>Datos básicos:</strong> Nombre, NIT, contactos</li>
                            <li><strong>Información comercial:</strong> Términos de pago, descuentos</li>
                            <li><strong>Historial de compras:</strong> Relación comercial</li>
                        </ul>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-cart-plus text-primary"></i> Módulo de Ingresos</h5>
                        <p><strong>Ruta:</strong> Operaciones → Compras → Ingresos</p>
                        
                        <h6>Proceso de ingreso de mercadería:</h6>
                        <ol>
                            <li><strong>Seleccionar proveedor</strong></li>
                            <li><strong>Agregar productos comprados</strong>
                                <ul><li>Cantidad recibida</li>
                                @if($isAdmin)<li>Precio de costo unitario</li>@endif
                                </ul>
                            </li>
                            <li><strong>Registrar factura del proveedor</strong></li>
                            <li><strong>Confirmar ingreso</strong></li>
                        </ol>

                        <div class="alert alert-success">
                            <i class="bi bi-arrow-up-circle"></i>
                            <strong>Actualización automática:</strong> El stock se incrementa automáticamente al confirmar el ingreso.
                        </div>

                        @if(!$isAdmin)
                        <div class="alert alert-warning">
                            <i class="bi bi-eye-slash"></i>
                            <strong>Restricción de Vendedor:</strong> No puedes ver ni editar precios de costo en los ingresos.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Administración -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdmin">
                    <i class="bi bi-shield-shaded text-danger me-3"></i>
                    <div>
                        <strong>Administración y Seguridad</strong>
                        <span class="badge badge-role bg-danger ms-2">Solo Administradores</span>
                        <br><small class="text-muted">Control de usuarios, configuraciones y seguridad</small>
                    </div>
                </button>
            </h2>
            <div id="collapseAdmin" class="accordion-collapse collapse" data-bs-parent="#modulosAccordion">
                <div class="accordion-body">
                    <div class="help-card">
                        <h5><i class="bi bi-people text-primary"></i> Módulo de Usuarios</h5>
                        <p><strong>Ruta:</strong> Administración → Seguridad → Usuarios</p>
                        
                        <h6>Gestión de usuarios del sistema:</h6>
                        <ul>
                            <li><strong>Crear nuevos usuarios</strong> con roles específicos</li>
                            <li><strong>Roles disponibles:</strong>
                                <ul>
                                    <li><strong>Administrador (role_as = 0):</strong> Acceso total</li>
                                    <li><strong>Vendedor (role_as = 1):</strong> Acceso limitado</li>
                                </ul>
                            </li>
                            <li><strong>Gestionar permisos</strong> y accesos</li>
                            <li><strong>Activar/desactivar</strong> cuentas de usuario</li>
                        </ul>

                        <div class="alert alert-warning">
                            <i class="bi bi-shield-exclamation"></i>
                            <strong>Seguridad:</strong> Los vendedores no pueden ver información de costos, ganancias ni funciones administrativas.
                        </div>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-gear text-success"></i> Configuración del Sistema</h5>
                        <p><strong>Ruta:</strong> Administración → Configuración</p>
                        
                        <h6>Configuraciones principales:</h6>
                        <ul>
                            <li><strong>Datos de la empresa:</strong> Nombre, dirección, contactos</li>
                            <li><strong>Configuración monetaria:</strong> Moneda y símbolo</li>
                            <li><strong>Parámetros del sistema:</strong> Configuraciones operativas</li>
                            <li><strong>Información para documentos:</strong> Aparece en PDFs y reportes</li>
                        </ul>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Importante:</strong> Estos datos aparecen en todas las cotizaciones y documentos oficiales.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen final -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-{{ $isAdmin ? 'primary' : 'success' }}">
                <h5><i class="bi bi-check-circle-fill"></i> 
                    @if($isAdmin)
                        Control Total del Sistema
                    @else
                        Herramientas de Venta Disponibles
                    @endif
                </h5>
                <p class="mb-0">
                    @if($isAdmin)
                        Como administrador, tienes acceso a todos los módulos del sistema. Utiliza esta guía para aprovechar al máximo cada funcionalidad y administrar tu taller de manera eficiente.
                    @else
                        Como vendedor, tienes acceso a las herramientas necesarias para atender clientes, realizar ventas y consultar inventario. El sistema protege la información sensible mientras te da todo lo necesario para ser productivo.
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>