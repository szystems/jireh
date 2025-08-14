// Test específico para reproducir el problema del total que se resetea
console.log("🧪 INICIANDO TEST ESPECÍFICO DEL PROBLEMA");

// Simular el estado inicial de la página
function simularEstadoInicial() {
    console.log("1. 📋 Simulando estado inicial...");
    
    // El total debe mostrar el valor correcto inicialmente
    const totalInicial = "Total: Q.350.00";
    $('#total-venta').html(totalInicial);
    console.log("   Total establecido:", totalInicial);
    
    // Los elementos de subtotal deben existir
    console.log("   Elementos subtotal encontrados:", $('.subtotal-input').length);
}

// Simular el problema: scripts que resetean el select
function simularProblema() {
    console.log("2. 🚨 Simulando scripts de reset...");
    
    // Esto es lo que hacen los scripts de reset
    $('#articulo').val('').trigger('change');
    console.log("   Select de artículo reseteado");
    
    // Los scripts problemáticos podrían estar llamando a actualizarTotalVenta
    if (window.actualizarTotalVenta) {
        console.log("   Llamando a actualizarTotalVenta()...");
        window.actualizarTotalVenta();
        
        // Verificar el resultado
        const totalDespues = $('#total-venta').text();
        console.log("   Total después de actualizarTotalVenta():", totalDespues);
        
        if (totalDespues.includes('0.00') || totalDespues.includes('0,00')) {
            console.error("❌ ¡PROBLEMA REPRODUCIDO! El total se reseteó a 0");
            return true;
        } else {
            console.log("✅ El total se mantuvo correcto");
            return false;
        }
    }
}

// Ejecutar test cuando la página esté lista
$(document).ready(function() {
    setTimeout(() => {
        simularEstadoInicial();
        
        setTimeout(() => {
            const problemaReproducido = simularProblema();
            
            if (problemaReproducido) {
                console.log("🔧 Intentando solución...");
                
                // La solución sería no llamar actualizarTotalVenta() desde scripts de reset
                // o hacer que actualizarTotalVenta() preserve el total existente si no hay cambios reales
                
                // Solución temporal: restaurar total
                $('#total-venta').html("Total: Q.350.00");
                console.log("✅ Total restaurado temporalmente");
            }
            
        }, 1000);
    }, 2000);
});
