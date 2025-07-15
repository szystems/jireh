// Archivo para validar y depurar el sistema de cálculo de totales
$(document).ready(function() {
    console.log("=== VALIDADOR DE TOTALES INICIADO ===");
    
    // Función para validar la estructura del DOM
    function validarEstructuraDOM() {
        const checks = {
            'total-venta': $('#total-venta').length > 0,
            'tabla-detalles-existentes': $('#tabla-detalles-existentes').length > 0,
            'nuevos-detalles': $('#nuevos-detalles').length > 0,
            'actualizarTotalVenta': typeof window.actualizarTotalVenta === 'function',
            'APP_CURRENCY_SYMBOL': typeof window.APP_CURRENCY_SYMBOL !== 'undefined'
        };
        
        console.log("=== VALIDACIÓN DE ESTRUCTURA DOM ===");
        for (const [check, result] of Object.entries(checks)) {
            console.log(`${check}: ${result ? '✅' : '❌'}`);
        }
        
        return Object.values(checks).every(Boolean);
    }
    
    // Función para contar elementos de subtotal
    function contarElementosSubtotal() {
        const existentes = $('#tabla-detalles-existentes tbody tr:not(.detalle-oculto-por-eliminacion) input.subtotal-input').length;
        const nuevos = $('#nuevos-detalles tr input.subtotal-input').length;
        
        console.log("=== ELEMENTOS DE SUBTOTAL ===");
        console.log(`Detalles existentes: ${existentes}`);
        console.log(`Nuevos detalles: ${nuevos}`);
        console.log(`Total elementos: ${existentes + nuevos}`);
        
        return { existentes, nuevos, total: existentes + nuevos };
    }
    
    // Función para calcular total manualmente (para comparación)
    function calcularTotalManual() {
        let total = 0;
        
        // Detalles existentes
        $('#tabla-detalles-existentes tbody tr:not(.detalle-oculto-por-eliminacion)').each(function() {
            const subtotalInput = $(this).find('input.subtotal-input');
            if (subtotalInput.length > 0) {
                const subtotal = parseFloat(subtotalInput.val());
                if (!isNaN(subtotal)) {
                    total += subtotal;
                    console.log(`Detalle existente: ${subtotal}`);
                }
            }
        });
        
        // Nuevos detalles
        $('#nuevos-detalles tr').each(function() {
            const subtotalInput = $(this).find('input.subtotal-input');
            if (subtotalInput.length > 0) {
                const subtotal = parseFloat(subtotalInput.val());
                if (!isNaN(subtotal)) {
                    total += subtotal;
                    console.log(`Nuevo detalle: ${subtotal}`);
                }
            }
        });
        
        console.log(`=== TOTAL MANUAL CALCULADO: ${total} ===`);
        return total;
    }
    
    // Función para probar la actualización del total
    function probarActualizacionTotal() {
        console.log("=== PROBANDO ACTUALIZACIÓN DE TOTAL ===");
        
        if (typeof window.actualizarTotalVenta === 'function') {
            const totalAntes = $('#total-venta').text();
            console.log(`Total antes: ${totalAntes}`);
            
            window.actualizarTotalVenta();
            
            const totalDespues = $('#total-venta').text();
            console.log(`Total después: ${totalDespues}`);
            
            return totalAntes !== totalDespues;
        } else {
            console.error("❌ Función actualizarTotalVenta no está disponible");
            return false;
        }
    }
    
    // Ejecutar validaciones
    setTimeout(function() {
        console.log("=== INICIANDO VALIDACIONES ===");
        
        const estructuraOK = validarEstructuraDOM();
        const elementos = contarElementosSubtotal();
        const totalManual = calcularTotalManual();
        const actualizacionOK = probarActualizacionTotal();
        
        console.log("=== RESUMEN DE VALIDACIONES ===");
        console.log(`Estructura DOM: ${estructuraOK ? '✅' : '❌'}`);
        console.log(`Elementos encontrados: ${elementos.total}`);
        console.log(`Total manual: ${totalManual}`);
        console.log(`Actualización funcionando: ${actualizacionOK ? '✅' : '❌'}`);
        
        if (estructuraOK && elementos.total > 0) {
            console.log("✅ Sistema de totales parece estar funcionando correctamente");
        } else {
            console.warn("⚠️ Pueden haber problemas con el sistema de totales");
        }
    }, 1000);
      // Observador para cambios en el DOM (con retraso para evitar interferir con la carga inicial)
    let observerEnabled = false;
    
    // Habilitar el observer después de que la página esté completamente cargada
    setTimeout(() => {
        observerEnabled = true;
        console.log("🔄 Observer de totales habilitado");
    }, 3000); // Esperar 3 segundos para permitir que todos los scripts de inicialización terminen
    
    const observer = new MutationObserver(function(mutations) {
        if (!observerEnabled) return; // No hacer nada si el observer no está habilitado
        
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                const addedNodes = Array.from(mutation.addedNodes);
                const removedNodes = Array.from(mutation.removedNodes);
                
                // Verificar si se agregaron o removieron detalles
                const detalleChanges = [...addedNodes, ...removedNodes].some(node => 
                    node.nodeType === 1 && (
                        node.classList && (
                            node.classList.contains('detalle-existente') ||
                            node.id && node.id.includes('nuevo-detalle-row')
                        )
                    )
                );
                
                if (detalleChanges) {
                    console.log("🔄 Cambio detectado en detalles, actualizando total...");
                    setTimeout(() => {
                        if (window.actualizarTotalVenta) {
                            window.actualizarTotalVenta();
                        }
                    }, 100);
                }
            }
        });
    });
    
    // Observar cambios en las tablas de detalles
    const tablasAObservar = ['#tabla-detalles-existentes tbody', '#nuevos-detalles'];
    tablasAObservar.forEach(selector => {
        const elemento = $(selector)[0];
        if (elemento) {
            observer.observe(elemento, {
                childList: true,
                subtree: true
            });
        }
    });
    
    console.log("=== VALIDADOR DE TOTALES CONFIGURADO ===");
});
