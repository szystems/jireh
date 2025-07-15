// Script temporal para depurar el problema del reset del total
$(document).ready(function() {
    console.log("🐛 DEBUG: Script de depuración del total iniciado");
    
    // Monitorear el elemento del total
    const $totalElement = $('#total-venta');
    if ($totalElement.length) {
        console.log("🐛 DEBUG: Elemento total encontrado:", $totalElement.text());
        
        // Crear un observer para el elemento total
        const totalObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' || mutation.type === 'characterData') {
                    const nuevoTexto = $totalElement.text();
                    console.log("🐛 DEBUG: Total cambió a:", nuevoTexto);
                    
                    // Si el total se vuelve 0, registrar el stack trace
                    if (nuevoTexto.includes('0.00') || nuevoTexto.includes('0,00')) {
                        console.error("❌ DEBUG: ¡El total se reseteó a 0!");
                        console.trace("Stack trace del reseteo:");
                    }
                }
            });
        });
        
        // Observar cambios en el texto del total
        totalObserver.observe($totalElement[0], {
            childList: true,
            subtree: true,
            characterData: true
        });
        
    } else {
        console.error("🐛 DEBUG: No se encontró el elemento #total-venta");
    }
    
    // Monitorear llamadas a actualizarTotalVenta
    if (window.actualizarTotalVenta) {
        const originalFunction = window.actualizarTotalVenta;
        window.actualizarTotalVenta = function() {
            console.log("🐛 DEBUG: actualizarTotalVenta() llamado");
            console.trace("Stack trace de la llamada:");
            return originalFunction.apply(this, arguments);
        };
        console.log("🐛 DEBUG: Función actualizarTotalVenta interceptada");
    }
    
    // Monitorear changes en el select de artículos
    $('#articulo').on('change', function() {
        const valor = $(this).val();
        console.log("🐛 DEBUG: Select de artículo cambió a:", valor || 'vacío');
    });
    
    console.log("🐛 DEBUG: Monitoreo configurado");
});
