<!-- Sección: Preguntas Frecuentes -->
<div class="help-section">
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning">
                <h5><i class="bi bi-question-circle-fill"></i> Preguntas Frecuentes</h5>
                <p class="mb-0">Soluciones rápidas a los problemas más comunes. Si no encuentras la respuesta, revisa la sección de Soporte.</p>
            </div>
        </div>
    </div>

    <!-- Accordion para FAQ -->
    <div class="accordion" id="faqAccordion">
        
        <!-- Problemas Técnicos -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProblemas">
                    <i class="bi bi-tools text-danger me-3"></i>
                    <div>
                        <strong>Problemas Técnicos Comunes</strong>
                        <br><small class="text-muted">Soluciones a errores frecuentes del sistema</small>
                    </div>
                </button>
            </h2>
            <div id="collapseProblemas" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    <div class="help-card">
                        <h5><i class="bi bi-exclamation-triangle text-warning"></i> "No puedo acceder al sistema"</h5>
                        <div class="row">
                            <div class="col-md-8">
                                <h6>Posibles causas y soluciones:</h6>
                                <ol>
                                    <li><strong>Credenciales incorrectas:</strong>
                                        <ul><li>Verifica email y contraseña</li>
                                        <li>Contacta al administrador para resetear contraseña</li></ul>
                                    </li>
                                    <li><strong>Cuenta desactivada:</strong>
                                        <ul><li>Solo el administrador puede reactivar tu cuenta</li></ul>
                                    </li>
                                    <li><strong>Problema de conexión:</strong>
                                        <ul><li>Verifica tu conexión a internet</li>
                                        <li>Prueba desde otro dispositivo</li></ul>
                                    </li>
                                </ol>
                            </div>
                            <div class="col-md-4">
                                <div class="alert alert-info">
                                    <i class="bi bi-lightbulb"></i>
                                    <strong>Tip:</strong> Borra cache del navegador si el problema persiste.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-search text-primary"></i> "No encuentro un cliente/producto"</h5>
                        <h6>Soluciones de búsqueda:</h6>
                        <ul>
                            <li><strong>Búsqueda parcial:</strong> Escribe solo parte del nombre</li>
                            <li><strong>Sin acentos:</strong> Prueba sin tildes (José → Jose)</li>
                            <li><strong>Mayúsculas/minúsculas:</strong> No importan en la búsqueda</li>
                            <li><strong>Por categoría:</strong> Usa filtros de categoría</li>
                            <li><strong>Estado inactivo:</strong> Verifica que no esté desactivado</li>
                        </ul>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-exclamation-circle text-danger"></i> "Error al guardar datos"</h5>
                        <h6>Pasos para resolver:</h6>
                        <ol>
                            <li><strong>Campos obligatorios:</strong> Llena todos los campos marcados con *</li>
                            <li><strong>Formato de datos:</strong> 
                                <ul>
                                    <li>Teléfonos: Solo números</li>
                                    <li>Email: Formato válido (usuario@dominio.com)</li>
                                    <li>Fechas: Formato correcto</li>
                                </ul>
                            </li>
                            <li><strong>Conexión:</strong> Verifica que no se haya perdido la conexión</li>
                            <li><strong>Sesión expirada:</strong> Vuelve a iniciar sesión</li>
                        </ol>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-printer text-secondary"></i> "Problemas con PDFs"</h5>
                        <h6>Soluciones comunes:</h6>
                        <ul>
                            <li><strong>No se genera:</strong> Verifica que existan datos para el reporte</li>
                            <li><strong>Se ve mal:</strong> Usa Chrome o Firefox actualizado</li>
                            <li><strong>No descarga:</strong> Verifica permisos de descarga del navegador</li>
                            <li><strong>Información faltante:</strong> Completa configuración de empresa</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Uso del Sistema -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUso">
                    <i class="bi bi-question-circle text-info me-3"></i>
                    <div>
                        <strong>Dudas sobre el Uso del Sistema</strong>
                        <br><small class="text-muted">Respuestas sobre funcionalidades y procesos</small>
                    </div>
                </button>
            </h2>
            <div id="collapseUso" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    <div class="help-card">
                        <h5><i class="bi bi-person-plus text-success"></i> "¿Cómo registro un cliente nuevo durante una venta?"</h5>
                        <h6>Proceso rápido:</h6>
                        <ol>
                            <li>En la pantalla de venta, busca al cliente</li>
                            <li>Si no existe, haz clic en "Nuevo Cliente"</li>
                            <li>Llena los datos básicos (mínimo nombre y teléfono)</li>
                            <li>Agrega el vehículo si es necesario</li>
                            <li>Continúa con la venta normalmente</li>
                        </ol>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-boxes text-warning"></i> "¿Qué hago si un producto no tiene stock?"</h5>
                        <h6>Opciones disponibles:</h6>
                        <ul>
                            <li><strong>Consultar stock real:</strong> Ve a Inventario para verificar</li>
                            @if($isAdmin)
                            <li><strong>Hacer ingreso:</strong> Registra nueva compra del producto</li>
                            <li><strong>Ajuste de inventario:</strong> Corrige stock si hay diferencias</li>
                            @endif
                            <li><strong>Venta en negativo:</strong> El sistema permite vender sin stock (se marca en rojo)</li>
                            <li><strong>Cotizar:</strong> Crea cotización y compra después</li>
                        </ul>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-currency-dollar text-primary"></i> "¿Cómo funcionan las comisiones?"</h5>
                        <h6>Sistema automático:</h6>
                        <ul>
                            <li><strong>Asignación:</strong> Al realizar venta, asignas trabajadores</li>
                            <li><strong>Cálculo:</strong> Se calcula automáticamente según porcentaje configurado</li>
                            <li><strong>Acumulación:</strong> Las comisiones se acumulan hasta la fecha de pago</li>
                            @if($isAdmin)
                            <li><strong>Pago:</strong> El administrador crea lotes de pago periódicamente</li>
                            @else
                            <li><strong>Consulta:</strong> Puedes ver tus comisiones en "Mis Comisiones"</li>
                            @endif
                        </ul>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-file-earmark-text text-info"></i> "¿Cuál es la diferencia entre cotización y venta?"</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <h6><strong>Cotización:</strong></h6>
                                <ul>
                                    <li>Presupuesto para el cliente</li>
                                    <li>No afecta inventario</li>
                                    <li>Tiene vigencia (15 días)</li>
                                    <li>Se puede regenerar</li>
                                    <li>Estados: Generado/Aprobado</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Venta:</strong></h6>
                                <ul>
                                    <li>Transacción confirmada</li>
                                    <li>Reduce inventario</li>
                                    <li>Genera comisiones</li>
                                    <li>Registra pagos</li>
                                    <li>Final e irreversible</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    @if(!$isAdmin)
                    <div class="help-card">
                        <h5><i class="bi bi-eye-slash text-warning"></i> "¿Por qué no veo precios de costo ni ganancias?"</h5>
                        <p>Como vendedor, esta información está protegida por políticas de seguridad de la empresa:</p>
                        <ul>
                            <li><strong>Información confidencial:</strong> Solo administradores ven costos</li>
                            <li><strong>Enfoque en ventas:</strong> Tu trabajo es vender, no analizar rentabilidad</li>
                            <li><strong>Seguridad:</strong> Evita que información sensible se comparta inadecuadamente</li>
                        </ul>
                        <div class="alert alert-info">
                            <i class="bi bi-shield-check"></i>
                            <strong>Tranquilo:</strong> Tienes acceso a todo lo necesario para ser un vendedor exitoso.
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Consejos y Trucos -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseConsejos">
                    <i class="bi bi-lightbulb text-warning me-3"></i>
                    <div>
                        <strong>Consejos y Trucos</strong>
                        <br><small class="text-muted">Tips para usar el sistema de manera más eficiente</small>
                    </div>
                </button>
            </h2>
            <div id="collapseConsejos" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    <div class="help-card">
                        <h5><i class="bi bi-lightning text-primary"></i> Atajos para Ser Más Rápido</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>En ventas:</h6>
                                <ul>
                                    <li><strong>Tab:</strong> Navega entre campos</li>
                                    <li><strong>Enter:</strong> Confirma selección</li>
                                    <li><strong>Escape:</strong> Cancela ventanas emergentes</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>En búsquedas:</h6>
                                <ul>
                                    <li><strong>Letras iniciales:</strong> Escribe primeras letras</li>
                                    <li><strong>Números:</strong> Busca por códigos o placas</li>
                                    <li><strong>Filtros:</strong> Usa filtros para reducir resultados</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-bookmark text-success"></i> Organización Recomendada</h5>
                        <h6>Para mantener orden:</h6>
                        <ul>
                            <li><strong>Códigos consistentes:</strong> Usa un patrón para códigos de productos</li>
                            <li><strong>Categorías claras:</strong> No mezcles productos de diferentes tipos</li>
                            <li><strong>Nombres descriptivos:</strong> Incluye marca y modelo en productos</li>
                            <li><strong>Stock mínimo:</strong> Configura alertas antes de quedarte sin producto</li>
                        </ul>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-clock text-info"></i> Flujo de Trabajo Eficiente</h5>
                        <h6>Rutina diaria recomendada:</h6>
                        <ol>
                            <li><strong>Inicio del día:</strong> Revisa dashboard para alertas</li>
                            <li><strong>Preparación:</strong> Verifica productos que se podrían agotar</li>
                            <li><strong>Durante ventas:</strong> Registra inmediatamente, no acumules</li>
                            <li><strong>Final del día:</strong> Revisa ventas del día</li>
                            @if($isAdmin)
                            <li><strong>Semanal:</strong> Procesa comisiones y revisa reportes</li>
                            @endif
                        </ol>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-shield-check text-warning"></i> Buenas Prácticas de Seguridad</h5>
                        <h6>Protege la información:</h6>
                        <ul>
                            <li><strong>Cerrar sesión:</strong> Al terminar tu turno</li>
                            <li><strong>Contraseña segura:</strong> Cámbiala periódicamente</li>
                            <li><strong>No compartir credenciales:</strong> Cada usuario debe tener su cuenta</li>
                            <li><strong>Verificar datos:</strong> Antes de guardar ventas importantes</li>
                        </ul>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-people text-success"></i> Atención al Cliente</h5>
                        <h6>Usa el sistema para brindar mejor servicio:</h6>
                        <ul>
                            <li><strong>Historial:</strong> Revisa compras anteriores del cliente</li>
                            <li><strong>Datos completos:</strong> Mantén información actualizada</li>
                            <li><strong>Cotizaciones:</strong> Para presupuestos detallados</li>
                            <li><strong>Seguimiento:</strong> Registra observaciones importantes</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Solución de Errores -->
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseErrores">
                    <i class="bi bi-bug text-danger me-3"></i>
                    <div>
                        <strong>Solución de Errores Específicos</strong>
                        <br><small class="text-muted">Guías para resolver errores puntuales</small>
                    </div>
                </button>
            </h2>
            <div id="collapseErrores" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                    <div class="help-card">
                        <h5><i class="bi bi-exclamation-triangle text-danger"></i> Error: "Token CSRF inválido"</h5>
                        <h6>Solución:</h6>
                        <ol>
                            <li>Recarga la página (F5)</li>
                            <li>Vuelve a intentar la operación</li>
                            <li>Si persiste, cierra y abre el navegador</li>
                            <li>Como último recurso, borra cache del navegador</li>
                        </ol>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-wifi-off text-warning"></i> Error: "No se puede conectar al servidor"</h5>
                        <h6>Pasos a seguir:</h6>
                        <ol>
                            <li><strong>Verifica internet:</strong> Abre otra página web</li>
                            <li><strong>Prueba en otro dispositivo:</strong> Para confirmar si es local</li>
                            <li><strong>Espera unos minutos:</strong> Puede ser mantenimiento temporal</li>
                            <li><strong>Contacta soporte:</strong> Si el problema persiste</li>
                        </ol>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-x-circle text-danger"></i> Error: "Producto no encontrado"</h5>
                        <h6>Posibles causas:</h6>
                        <ul>
                            <li><strong>Producto eliminado:</strong> Ya no existe en el sistema</li>
                            <li><strong>Producto inactivo:</strong> Fue desactivado</li>
                            @if($isAdmin)
                            <li><strong>Solución:</strong> Reactiva el producto o crea uno nuevo</li>
                            @else
                            <li><strong>Solución:</strong> Contacta al administrador</li>
                            @endif
                        </ul>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-calculator text-warning"></i> Error: "Cálculos incorrectos"</h5>
                        <h6>Verificaciones:</h6>
                        <ul>
                            <li><strong>Precios:</strong> Confirma que los precios sean correctos</li>
                            <li><strong>Cantidades:</strong> Verifica las cantidades ingresadas</li>
                            <li><strong>Descuentos:</strong> Revisa descuentos aplicados</li>
                            <li><strong>Configuración:</strong> Verifica configuración de moneda</li>
                        </ul>
                    </div>

                    <div class="help-card">
                        <h5><i class="bi bi-hourglass text-info"></i> Sistema Lento</h5>
                        <h6>Optimizaciones:</h6>
                        <ul>
                            <li><strong>Cierra tabs:</strong> Demasiadas pestañas abiertas</li>
                            <li><strong>Actualiza navegador:</strong> Usa la versión más reciente</li>
                            <li><strong>Limpia cache:</strong> Borra archivos temporales</li>
                            <li><strong>Conexión:</strong> Verifica velocidad de internet</li>
                            <li><strong>Hora pico:</strong> El sistema puede estar más lento en horarios de mucho uso</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mensaje de ayuda adicional -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info">
                <div class="d-flex align-items-center">
                    <i class="bi bi-question-circle-fill fs-3 me-3 text-primary"></i>
                    <div>
                        <h5 class="mb-1">¿No encontraste la respuesta?</h5>
                        <p class="mb-0">
                            Si tu problema no está listado aquí, revisa la sección de <strong>Soporte</strong> 
                            para contactar al equipo técnico o reportar el inconveniente.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>