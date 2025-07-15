// Script de depuración avanzada para el cálculo de totales
// Este script ayuda a diagnosticar problemas con el cálculo de totales

$(document).ready(function() {
    console.log("🐛 DEBUG-TOTAL-CALCULATION: Script de depuración iniciado");

    // Interceptar la función original para agregar logging adicional
    if (window.actualizarTotalVenta) {
        const funcionOriginal = window.actualizarTotalVenta;
        
        window.actualizarTotalVenta = function() {
            console.log("🐛 =================================================");
            console.log("🐛 DEBUG: actualizarTotalVenta() LLAMADA");
            console.log("🐛 Timestamp:", new Date().toISOString());
            
            // Obtener información del stack trace para saber quién la llamó
            try {
                throw new Error();
            } catch (e) {
                const stack = e.stack.split('\n').slice(2, 6); // Primeras 4 líneas del stack
                console.log("🐛 Llamada desde:", stack);
            }
            
            // Diagnosticar el estado actual del DOM antes del cálculo
            diagnosticarEstadoDOM();
            
            // Llamar a la función original
            const resultado = funcionOriginal.apply(this, arguments);
            
            // Diagnosticar el estado después del cálculo
            console.log("🐛 DEBUG: Función ejecutada, diagnóstico post-cálculo:");
            diagnosticarEstadoDOM();
            console.log("🐛 =================================================");
            
            return resultado;
        };
        
        console.log("🐛 DEBUG: Función actualizarTotalVenta interceptada exitosamente");
    } else {
        console.log("🐛 DEBUG: Función actualizarTotalVenta no encontrada para interceptar");
    }

    // Función para diagnosticar el estado del DOM
    function diagnosticarEstadoDOM() {
        console.log("🐛 DIAGNÓSTICO DEL DOM:");
        
        // 1. Verificar elementos de total
        const $totalElement = $('#total-venta');
        console.log(`🐛   Total element exists: ${$totalElement.length > 0}`);
        if ($totalElement.length > 0) {
            console.log(`🐛   Total current text: "${$totalElement.text()}"`);
        }
        
        // 2. Contar todos los inputs de subtotal
        const todosInputsSubtotal = $('input[name*="sub_total"], input.subtotal-input, input[name*="[sub_total]"]');
        console.log(`🐛   Total subtotal inputs found: ${todosInputsSubtotal.length}`);
        
        if (todosInputsSubtotal.length > 0) {
            console.log("🐛   Detalle de inputs de subtotal:");
            todosInputsSubtotal.each(function(index) {
                const $input = $(this);
                const name = $input.attr('name') || 'sin-name';
                const value = $input.val() || '0';
                const $row = $input.closest('tr');
                const rowId = $row.attr('id') || 'sin-id';
                const isHidden = $row.is(':hidden');
                const hasEliminationClass = $row.hasClass('detalle-oculto-por-eliminacion');
                
                console.log(`🐛     [${index + 1}] Name: ${name}, Value: ${value}, Row: ${rowId}, Hidden: ${isHidden}, EliminationClass: ${hasEliminationClass}`);
            });
        }
        
        // 3. Verificar tablas específicas
        const $tablaExistentes = $('#tabla-detalles-existentes');
        const $tablaNuevos = $('#tabla-nuevos-detalles');
        
        console.log(`🐛   Tabla existentes found: ${$tablaExistentes.length > 0}`);
        if ($tablaExistentes.length > 0) {
            const filasExistentes = $tablaExistentes.find('tbody tr').length;
            const filasVisibles = $tablaExistentes.find('tbody tr:visible').length;
            console.log(`🐛     Filas existentes: ${filasExistentes}, Visibles: ${filasVisibles}`);
        }
        
        console.log(`🐛   Tabla nuevos found: ${$tablaNuevos.length > 0}`);
        if ($tablaNuevos.length > 0) {
            const filasNuevas = $tablaNuevos.find('tbody tr').length;
            console.log(`🐛     Filas nuevas: ${filasNuevas}`);
        }
        
        // 4. Verificar inputs de eliminación
        const inputsEliminar = $('input[name*="[eliminar]"]');
        console.log(`🐛   Elimination inputs found: ${inputsEliminar.length}`);
        if (inputsEliminar.length > 0) {
            console.log("🐛   Detalles marcados para eliminar:");
            inputsEliminar.each(function() {
                const $input = $(this);
                if ($input.val() === '1') {
                    const name = $input.attr('name') || 'sin-name';
                    console.log(`🐛     ELIMINADO: ${name}`);
                }
            });
        }
    }

    // Monitorear cambios en el DOM que puedan afectar el cálculo
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                // Verificar si se agregaron o removieron filas de detalles
                const addedNodes = Array.from(mutation.addedNodes);
                const removedNodes = Array.from(mutation.removedNodes);
                
                const affectsDetails = [...addedNodes, ...removedNodes].some(node => {
                    if (node.nodeType === 1) { // Element node
                        return node.matches && (
                            node.matches('tr[id*="detalle-row"]') ||
                            node.matches('tr[id*="nuevo-detalle"]') ||
                            node.closest('#tabla-detalles-existentes') ||
                            node.closest('#tabla-nuevos-detalles')
                        );
                    }
                    return false;
                });
                
                if (affectsDetails) {
                    console.log("🐛 DOM CHANGE: Cambio detectado en detalles", {
                        added: addedNodes.length,
                        removed: removedNodes.length,
                        target: mutation.target.id || mutation.target.className
                    });
                }
            }
            
            if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                // Verificar cambios de visibilidad en filas de detalles
                const target = mutation.target;
                if (target.matches && target.matches('tr[id*="detalle"]')) {
                    console.log("🐛 STYLE CHANGE: Cambio de estilo en fila de detalle", {
                        rowId: target.id,
                        display: target.style.display,
                        hidden: target.hidden
                    });
                }
            }
        });
    });

    // Observar cambios en las tablas de detalles
    const tablasAObservar = ['#tabla-detalles-existentes', '#tabla-nuevos-detalles'];
    tablasAObservar.forEach(selector => {
        const tabla = document.querySelector(selector);
        if (tabla) {
            observer.observe(tabla, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['style', 'class']
            });
            console.log(`🐛 DEBUG: Observando cambios en ${selector}`);
        }
    });

    // Función para ejecutar diagnóstico manual
    window.debugTotalCalculation = function() {
        console.log("🐛 DIAGNÓSTICO MANUAL EJECUTADO:");
        diagnosticarEstadoDOM();
        
        if (window.actualizarTotalVenta) {
            console.log("🐛 Ejecutando actualizarTotalVenta() manualmente...");
            window.actualizarTotalVenta();
        }
    };

    console.log("🐛 DEBUG-TOTAL-CALCULATION: Configuración completa. Use window.debugTotalCalculation() para diagnóstico manual.");
});
