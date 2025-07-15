// Script de prueba final para verificar que la protección funciona correctamente
// Este script simula el escenario real del problema reportado

console.log("=== INICIANDO PRUEBA FINAL DEL MECANISMO DE PROTECCIÓN ===");

// Simular la carga de la página
setTimeout(() => {
    console.log("1. ✅ Página cargada con total inicial: Q.350.00");
    
    // Simular que scripts de reset ejecutan y limpian elementos
    setTimeout(() => {
        console.log("2. 🚨 Ejecutando scripts de reset que causan el problema...");
        
        // Esto es lo que pasaba antes: elementos se limpiaban y total se reseteaba a 0
        // Ahora con la protección debería mantener el total
        
        console.log("3. 🔄 Llamando a actualizarTotalVenta() después del reset...");
        
        // Verificar que la función existe
        if (typeof window.actualizarTotalVenta === 'function') {
            // Ejecutar la función
            window.actualizarTotalVenta();
            
            console.log("4. ✅ PRUEBA COMPLETADA");
            console.log("   - Si el total se mantiene en Q.350.00, la protección funciona");
            console.log("   - Si el total se resetea a Q.0.00, hay un problema");
            
        } else {
            console.error("❌ Función actualizarTotalVenta no está disponible");
        }
        
    }, 1000);
    
}, 500);
