document.addEventListener('DOMContentLoaded', function () {
    // Variables para control de estado
    window.nuevoDetalleCount = 0; // Global para ser accesible por otros módulos si es necesario
    window.hayCambios = false;
    window.hayCambiosTrabajadores = {}; // Para rastrear cambios específicos en trabajadores por detalle
    // Asegúrate de que el DOM esté completamente cargado y el formulario inicializado antes de serializar
    window.originalFormData = $('#forma-editar-venta').serialize();

    // Acceder a la configuración global proporcionada por Blade
    // Estas variables DEBEN ser definidas en un <script> en tu archivo Blade antes de incluir este JS
    window.APP_CURRENCY_SYMBOL = (window.jirehVentaConfig && typeof window.jirehVentaConfig.currencySymbol !== 'undefined') ? window.jirehVentaConfig.currencySymbol : '$';
    window.APP_VEHICULO_ID_ORIGINAL = (window.jirehVentaConfig && typeof window.jirehVentaConfig.vehiculoIdOriginal !== 'undefined') ? window.jirehVentaConfig.vehiculoIdOriginal : '';
    window.APP_CSRF_TOKEN = (window.jirehVentaConfig && typeof window.jirehVentaConfig.csrfToken !== 'undefined') ? window.jirehVentaConfig.csrfToken : '';


    // Función para formatear moneda
    window.formatCurrency = function(value) {
        const val = parseFloat(value);
        if (isNaN(val)) return window.APP_CURRENCY_SYMBOL + ' 0.00';
        return window.APP_CURRENCY_SYMBOL + ' ' + val.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&$&'); // Corregido: $& en lugar de $&,
    }

    // Función para marcar cambios y activar advertencia de salida
    window.marcarCambio = function() {
        window.hayCambios = true;
    }

    // Inicialización general de Select2 para elementos estándar
    $('.select2').select2({
        language: {
            noResults: () => "No se encontraron resultados",
            searching: () => "Buscando..."
        },
        width: '100%',
        selectOnClose: true
    }).on('select2:open', function() {
        // Asegurar visibilidad sobre otros elementos, útil si hay modales u otros componentes z-indexed
        let dropdown = $(this).data('select2').$dropdown;
        if (dropdown) {
            dropdown.css('z-index', 1100);
        }
    });

    // Inicialización de Select2 para selects dentro de modales
    $('.select2-modal').each(function() {
        $(this).select2({
            dropdownParent: $(this).closest('.modal'),
            width: '100%',
            closeOnSelect: false, // Permitir selección múltiple sin cerrar
            language: {
                noResults: () => "No se encontraron resultados",
                searching: () => "Buscando..."
            }
        }).on('select2:open', function() {
             let dropdown = $(this).data('select2').$dropdown;
             if (dropdown) {
                dropdown.css('z-index', 1056); // Asegurar visibilidad sobre el modal
             }
        });
    });

    // Registrar cambios generales en el formulario
    $('#forma-editar-venta').on('change', 'input, select, textarea', function() {
        window.marcarCambio();
    });

    // --- ALERTA DE SALIDA ---
    window.addEventListener('beforeunload', function (e) {
        if (window.hayCambios) {
            const confirmationMessage = 'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?';
            e.returnValue = confirmationMessage; // Gecko + WebKit, Safari, Chrome
            return confirmationMessage; // Gecko + WebKit, Safari, Chrome
        }
    });    // Función para actualizar el total de la venta (definición completamente reescrita)
    window.actualizarTotalVenta = function() {
        const currencySymbol = window.APP_CURRENCY_SYMBOL || 'Q';

        // Verificar que los elementos necesarios existen
        const $totalElement = $('#total-venta');
        if (!$totalElement.length) {
            console.warn("⚠️ No se encontró elemento #total-venta");
            return;
        }

        console.log("🔄 ===============================================");
        console.log("🔄 INICIANDO CÁLCULO DE TOTAL DE VENTA");
        console.log("🔄 ===============================================");

        let total = 0;
        let elementosEncontrados = 0;
        let detallesExistentesCount = 0;
        let detallesNuevosCount = 0;

        // ESTRATEGIA MEJORADA Y ROBUSTA: Múltiples métodos para encontrar inputs de subtotal
        
        console.log("📊 PASO 1: Buscando TODOS los inputs de subtotal con múltiples estrategias...");
        
        // Estrategia 1: Selector original
        let todosLosInputsSubtotal = $('input[name*="sub_total"], input.subtotal-input, input[name*="[sub_total]"]');
        console.log(`📊 Estrategia 1 (selector original): ${todosLosInputsSubtotal.length} inputs`);
        
        // Log de todos los inputs encontrados para debugging
        if (todosLosInputsSubtotal.length > 0) {
            console.log("📊 Inputs encontrados en estrategia 1:");
            todosLosInputsSubtotal.each(function(i) {
                const $input = $(this);
                console.log(`   ${i+1}. name="${$input.attr('name')}", value="${$input.val()}", visible=${$input.is(':visible')}`);
            });
        }
        
        // Estrategia 2: Si no encuentra nada, buscar en contextos específicos
        if (todosLosInputsSubtotal.length === 0) {
            console.log("⚠️ Estrategia 1 falló, probando estrategia 2...");
            todosLosInputsSubtotal = $('#tabla-detalles-existentes input, #tabla-nuevos-detalles input, #nuevos-detalles input').filter(function() {
                const nombre = $(this).attr('name') || '';
                return nombre.includes('sub_total') || nombre.includes('subtotal') || $(this).hasClass('subtotal-input');
            });
            console.log(`📊 Estrategia 2 (contexto específico): ${todosLosInputsSubtotal.length} inputs`);
        }
        
        // Estrategia 3: Si aún no encuentra, buscar todos los inputs y filtrar manualmente
        if (todosLosInputsSubtotal.length === 0) {
            console.log("⚠️ Estrategia 2 falló, probando estrategia 3 (búsqueda exhaustiva)...");
            const inputsEncontrados = [];
            $('input').each(function() {
                const $input = $(this);
                const nombre = $input.attr('name') || '';
                const clases = $input.attr('class') || '';
                
                if (nombre.includes('sub_total') || 
                    nombre.includes('subtotal') || 
                    clases.includes('subtotal') ||
                    nombre.match(/\[sub_total\]/) ||
                    nombre.match(/detalles\[\d+\]\[sub_total\]/) ||
                    nombre.match(/nuevos_detalles\[\d+\]\[sub_total\]/)) {
                    inputsEncontrados.push($input[0]);
                }
            });
            todosLosInputsSubtotal = $(inputsEncontrados);
            console.log(`📊 Estrategia 3 (búsqueda exhaustiva): ${todosLosInputsSubtotal.length} inputs`);
        }
        
        console.log(`📊 Total de inputs de subtotal encontrados: ${todosLosInputsSubtotal.length}`);
        
        todosLosInputsSubtotal.each(function(index) {
            const $input = $(this);
            const $row = $input.closest('tr');
            const inputName = $input.attr('name') || '';
            const inputValue = parseFloat($input.val()) || 0;
            
            console.log(`📊 Analizando input ${index + 1}:`, {
                name: inputName,
                value: inputValue,
                rowId: $row.attr('id') || 'sin-id',
                rowClasses: $row.attr('class') || 'sin-clases'
            });
            
            // Determinar si es un detalle existente o nuevo
            let esDetalleExistente = false;
            let esDetalleNuevo = false;
            let debeContarse = true;
            
            // Verificar si está en la tabla de detalles existentes
            if ($input.closest('#tabla-detalles-existentes').length > 0) {
                esDetalleExistente = true;
                console.log(`   → Es detalle existente`);
                
                // Verificar si está marcado para eliminar
                const $eliminarInput = $row.find('input[name*="[eliminar]"]');
                if ($eliminarInput.length > 0 && $eliminarInput.val() === '1') {
                    debeContarse = false;
                    console.log(`   → ❌ Marcado para eliminar, no se cuenta`);
                }
                
                // Verificar si la fila está oculta por eliminación
                if ($row.hasClass('detalle-oculto-por-eliminacion') || $row.is(':hidden')) {
                    debeContarse = false;
                    console.log(`   → ❌ Fila oculta, no se cuenta`);
                }
            }
            // Verificar si está en la tabla de nuevos detalles
            else if ($input.closest('#tabla-nuevos-detalles').length > 0 || inputName.includes('nuevos_detalles')) {
                esDetalleNuevo = true;
                console.log(`   → Es detalle nuevo`);
            }
            // Si no está en ninguna tabla conocida, intentar determinar por el name
            else if (inputName.includes('detalles[') && !inputName.includes('nuevos_detalles')) {
                esDetalleExistente = true;
                console.log(`   → Es detalle existente (determinado por name)`);
            }
            else {
                console.log(`   → ⚠️ Tipo indeterminado, se contará por defecto`);
            }
            
            // Contar si debe contarse y tiene valor válido
            if (debeContarse && !isNaN(inputValue) && inputValue > 0) {
                total += inputValue;
                elementosEncontrados++;
                
                if (esDetalleExistente) {
                    detallesExistentesCount++;
                    console.log(`   → ✅ Sumado como existente: ${inputValue}`);
                } else if (esDetalleNuevo) {
                    detallesNuevosCount++;
                    console.log(`   → ✅ Sumado como nuevo: ${inputValue}`);
                } else {
                    console.log(`   → ✅ Sumado como indeterminado: ${inputValue}`);
                }
            } else if (!debeContarse) {
                console.log(`   → ❌ No contado (marcado para eliminar o oculto)`);
            } else if (isNaN(inputValue) || inputValue <= 0) {
                console.log(`   → ❌ No contado (valor inválido: ${inputValue})`);
            }
        });

        console.log("📊 ===============================================");
        console.log(`📊 RESUMEN DEL CÁLCULO:`);
        console.log(`📊    Total calculado: ${total.toFixed(2)}`);
        console.log(`📊    Elementos encontrados: ${elementosEncontrados}`);
        console.log(`📊    Detalles existentes: ${detallesExistentesCount}`);
        console.log(`📊    Detalles nuevos: ${detallesNuevosCount}`);
        console.log("📊 ===============================================");

        // Obtener el total actual para comparación
        const totalActualText = $totalElement.text();
        const totalActualMatch = totalActualText.match(/[\d,]+\.?\d*/);
        const totalActual = totalActualMatch ? parseFloat(totalActualMatch[0].replace(/,/g, '')) : 0;
        
        console.log(`📊 Total actual en pantalla: ${totalActual}`);

        // PROTECCIÓN MEJORADA: Solo preservar el total actual si claramente no hay elementos
        // pero había un total válido antes
        if (elementosEncontrados === 0 && totalActual > 0) {
            console.warn("⚠️ ===============================================");
            console.warn("⚠️ NO SE ENCONTRARON ELEMENTOS DE SUBTOTAL");
            console.warn("⚠️ PERO HAY UN TOTAL VÁLIDO EXISTENTE");
            console.warn("⚠️ REALIZANDO DIAGNÓSTICO ADICIONAL...");
            console.warn("⚠️ ===============================================");
            
            // Diagnóstico adicional para entender por qué no se encuentran elementos
            const $tablaExistentes = $('#tabla-detalles-existentes');
            const $tablaNuevos = $('#tabla-nuevos-detalles, #nuevos-detalles');
            
            console.warn(`🔍 Tabla existentes encontrada: ${$tablaExistentes.length > 0}`);
            if ($tablaExistentes.length > 0) {
                const $filasExistentes = $tablaExistentes.find('tr');
                console.warn(`🔍 Filas en tabla existentes: ${$filasExistentes.length}`);
                console.warn(`🔍 Inputs en tabla existentes: ${$tablaExistentes.find('input').length}`);
                
                // Mostrar algunos inputs de muestra
                $tablaExistentes.find('input').slice(0, 3).each(function(i) {
                    const $input = $(this);
                    console.warn(`🔍 Input muestra ${i+1}: name="${$input.attr('name')}", value="${$input.val()}"`);
                });
            }
            
            console.warn(`🔍 Tabla nuevos encontrada: ${$tablaNuevos.length > 0}`);
            if ($tablaNuevos.length > 0) {
                console.warn(`🔍 Inputs en tabla nuevos: ${$tablaNuevos.find('input').length}`);
            }
            
            // Buscar todos los inputs en el DOM
            const $todosInputs = $('input');
            console.warn(`🔍 Total de inputs en el DOM: ${$todosInputs.length}`);
            
            // Buscar inputs que podrían ser subtotales
            let inputsPosiblesSubtotal = 0;
            $todosInputs.each(function() {
                const nombre = $(this).attr('name') || '';
                if (nombre.includes('total') || nombre.includes('sub_total') || nombre.includes('precio')) {
                    inputsPosiblesSubtotal++;
                }
            });
            console.warn(`🔍 Inputs posibles subtotal: ${inputsPosiblesSubtotal}`);
            
            console.warn("⚠️ PRESERVANDO TOTAL ACTUAL POR SEGURIDAD");
            console.warn("⚠️ ===============================================");
            return;
        }

        // Formatear el total con separadores de miles
        const totalFormateado = total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        const nuevoTextoTotal = `Total: ${currencySymbol}.${totalFormateado}`;
        
        console.log(`📊 ACTUALIZANDO TOTAL EN PANTALLA: ${nuevoTextoTotal}`);
        $totalElement.html(nuevoTextoTotal);
        
        console.log("🔄 ===============================================");
        console.log("🔄 CÁLCULO DE TOTAL COMPLETADO");
        console.log("🔄 ===============================================");
    }

    // Calcular total inicial de la venta con múltiples intentos para asegurar precisión
    console.log("🔄 Programando cálculo inicial del total...");
    
    // Función mejorada para el cálculo inicial
    function calcularTotalInicial() {
        console.log("🔄 Ejecutando cálculo inicial del total...");
        
        if (typeof window.actualizarTotalVenta === 'function') {
            // Verificar si hay elementos de subtotal disponibles antes de calcular
            const inputsSubtotal = $('input[name*="sub_total"], input.subtotal-input, input[name*="[sub_total]"]');
            console.log(`🔄 Inputs de subtotal encontrados para cálculo inicial: ${inputsSubtotal.length}`);
            
            if (inputsSubtotal.length > 0) {
                console.log("✅ Hay elementos de subtotal, procediendo con el cálculo");
                window.actualizarTotalVenta();
            } else {
                console.log("⚠️ No se encontraron elementos de subtotal en el cálculo inicial");
                // Intentar una vez más después de un breve delay
                setTimeout(() => {
                    const inputsSubtotalRetry = $('input[name*="sub_total"], input.subtotal-input, input[name*="[sub_total]"]');
                    console.log(`🔄 Retry - Inputs de subtotal encontrados: ${inputsSubtotalRetry.length}`);
                    if (inputsSubtotalRetry.length > 0) {
                        console.log("✅ Elementos encontrados en retry, calculando total");
                        window.actualizarTotalVenta();
                    } else {
                        console.log("❌ No se encontraron elementos de subtotal después del retry");
                    }
                }, 1000);
            }
        } else {
            console.error("❌ Función actualizarTotalVenta no está disponible");
        }
    }
    
    // Ejecutar el cálculo inicial con múltiples estrategias
    // 1. Inmediato (para casos donde todo ya está listo)
    setTimeout(calcularTotalInicial, 100);
    
    // 2. Después de un delay moderado (para casos donde hay algunos scripts corriendo)
    setTimeout(calcularTotalInicial, 1000);
    
    // 3. Después de un delay más largo (para casos donde hay muchos scripts o datos complejos)
    setTimeout(calcularTotalInicial, 3000);
});

// 🐛 FUNCIÓN DE DEPURACIÓN MANUAL PARA PRUEBAS
window.debugTotalManual = function() {
    console.log("🐛 ===============================================");
    console.log("🐛 DEPURACIÓN MANUAL DEL CÁLCULO DE TOTALES");
    console.log("🐛 ===============================================");
    
    // 1. Verificar elementos de total
    const $totalElement = $('#total-venta');
    console.log(`🐛 Elemento total encontrado: ${$totalElement.length > 0}`);
    if ($totalElement.length > 0) {
        console.log(`🐛 Texto actual del total: "${$totalElement.text()}"`);
    }
    
    // 2. Buscar todos los inputs de subtotal
    const $allSubtotalInputs = $('input[name*="sub_total"], input.subtotal-input, input[name*="[sub_total]"]');
    console.log(`🐛 Total de inputs de subtotal encontrados: ${$allSubtotalInputs.length}`);
    
    let detalleExistente = 0;
    let detalleNuevo = 0;
    let suma = 0;
    
    $allSubtotalInputs.each(function(index) {
        const $input = $(this);
        const valor = parseFloat($input.val()) || 0;
        const nombre = $input.attr('name') || '';
        const esVisible = $input.is(':visible');
        const filaOculta = $input.closest('tr').hasClass('d-none');
        
        console.log(`🐛 Input ${index + 1}:`);
        console.log(`    Nombre: ${nombre}`);
        console.log(`    Valor: ${valor}`);
        console.log(`    Visible: ${esVisible}`);
        console.log(`    Fila oculta: ${filaOculta}`);
        
        if (esVisible && !filaOculta && valor > 0) {
            suma += valor;
            if (nombre.includes('detalles[') && !nombre.includes('nuevos_detalles')) {
                detalleExistente++;
                console.log(`    → Clasificado como: EXISTENTE`);
            } else if (nombre.includes('nuevos_detalles')) {
                detalleNuevo++;
                console.log(`    → Clasificado como: NUEVO`);
            } else {
                console.log(`    → Clasificado como: INDETERMINADO`);
            }
        } else {
            console.log(`    → No contado (visible:${esVisible}, oculto:${filaOculta}, valor:${valor})`);
        }
    });
    
    console.log("🐛 ===============================================");
    console.log(`🐛 RESUMEN:`);
    console.log(`🐛 Detalles existentes: ${detalleExistente}`);
    console.log(`🐛 Detalles nuevos: ${detalleNuevo}`);
    console.log(`🐛 Suma total: ${suma}`);
    console.log("🐛 ===============================================");
    
    // 3. Ejecutar la función de actualización y comparar
    console.log("🐛 Ejecutando actualizarTotalVenta()...");
    if (typeof window.actualizarTotalVenta === 'function') {
        window.actualizarTotalVenta();
    } else {
        console.error("🐛 ❌ Función actualizarTotalVenta no encontrada");
    }
    
    return {
        totalElemento: $totalElement.length > 0,
        inputsEncontrados: $allSubtotalInputs.length,
        detallesExistentes: detalleExistente,
        detallesNuevos: detalleNuevo,
        sumaCalculada: suma
    };
};
