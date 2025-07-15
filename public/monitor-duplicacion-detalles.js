/**
 * Script para monitorear duplicación en nuevos detalles
 * Intercepta el envío y muestra exactamente qué se está enviando
 */

console.log('🔍 MONITOR DE DUPLICACIÓN: Script cargado');

$(document).ready(function() {
    let envioCount = 0;
    
    // Interceptar envío del formulario para ver datos exactos
    $('#forma-editar-venta').on('submit', function(e) {
        envioCount++;
        
        console.log(`\n🚨 ENVÍO ${envioCount}: Interceptando formulario de venta`);
        console.log('=====================================');
        
        const formData = new FormData(this);
        
        // Revisar todos los datos relacionados con nuevos detalles
        console.log('\n📦 DATOS DE NUEVOS DETALLES:');
        let nuevosDetallesCount = 0;
        let nuevosDetallesData = {};
        
        for (let [key, value] of formData.entries()) {
            if (key.includes('nuevos_detalles')) {
                nuevosDetallesCount++;
                
                // Extraer índice del detalle
                const match = key.match(/nuevos_detalles\[(\d+)\]/);
                const index = match ? match[1] : 'unknown';
                
                if (!nuevosDetallesData[index]) {
                    nuevosDetallesData[index] = {};
                }
                
                nuevosDetallesData[index][key] = value;
                console.log(`  ${key} = ${value}`);
            }
        }
        
        console.log(`\n📊 RESUMEN DE NUEVOS DETALLES:`);
        console.log(`Total de campos de nuevos_detalles: ${nuevosDetallesCount}`);
        console.log(`Número de detalles únicos:`, Object.keys(nuevosDetallesData));
        
        // Analizar cada detalle para buscar duplicación
        Object.keys(nuevosDetallesData).forEach(index => {
            console.log(`\n🔸 DETALLE NUEVO ${index}:`);
            const detalle = nuevosDetallesData[index];
            
            // Contar artículos para este índice
            const articulosCount = Object.keys(detalle).filter(key => 
                key.includes('articulo_id')).length;
                
            // Contar trabajadores para este índice
            const trabajadoresCount = Object.keys(detalle).filter(key => 
                key.includes('trabajadores_carwash')).length;
                
            console.log(`  - Artículos: ${articulosCount}`);
            console.log(`  - Trabajadores: ${trabajadoresCount}`);
            
            if (articulosCount > 1) {
                console.log('  ⚠️ POSIBLE DUPLICACIÓN DE ARTÍCULO');
            }
            
            Object.keys(detalle).forEach(key => {
                console.log(`    ${key}: ${detalle[key]}`);
            });
        });
        
        // Verificar otros campos relacionados
        console.log('\n🔧 OTROS CAMPOS RELACIONADOS:');
        for (let [key, value] of formData.entries()) {
            if (key.includes('trabajadores_carwash') && !key.includes('nuevos_detalles')) {
                console.log(`  ${key} = ${value}`);
            }
        }
        
        // Verificar variable global
        if (window.nuevoDetalleCount) {
            console.log(`\n📈 CONTADOR GLOBAL: ${window.nuevoDetalleCount}`);
        }
        
        // Verificar tabla visual
        const filasVisibles = $('#tabla-nuevos-detalles tbody tr').length;
        console.log(`🖼️ FILAS VISIBLES EN TABLA: ${filasVisibles}`);
        
        console.log('\n=====================================');
        
        // Si encontramos indicios de duplicación, pausar envío para revisión
        const totalIndices = Object.keys(nuevosDetallesData).length;
        if (totalIndices !== filasVisibles) {
            console.log(`🚨 DISCREPANCIA DETECTADA:`);
            console.log(`  - Índices en datos: ${totalIndices}`);
            console.log(`  - Filas visibles: ${filasVisibles}`);
            
            // Pausar por 3 segundos para permitir revisión
            e.preventDefault();
            setTimeout(() => {
                console.log('⏰ Timeout completado, reenviar formulario si es necesario');
                // Opcional: reenviar automáticamente
                // this.submit();
            }, 3000);
            
            return false;
        }
        
        console.log('✅ No se detectó duplicación aparente, permitiendo envío');
        return true;
    });
    
    // Monitorear clicks en el botón de agregar detalle
    let clickCount = 0;
    $(document).on('click', '#agregar-detalle', function() {
        clickCount++;
        console.log(`\n🖱️ CLICK ${clickCount} en agregar detalle`);
        
        // Verificar estado antes del click
        const filasAntes = $('#tabla-nuevos-detalles tbody tr').length;
        console.log(`📊 Filas antes del click: ${filasAntes}`);
        
        // Verificar después del procesamiento
        setTimeout(() => {
            const filasDespues = $('#tabla-nuevos-detalles tbody tr').length;
            console.log(`📊 Filas después del click: ${filasDespues}`);
            console.log(`📈 Diferencia: ${filasDespues - filasAntes}`);
            
            if (filasDespues - filasAntes > 1) {
                console.log('🚨 POSIBLE DUPLICACIÓN: Se agregaron más filas de las esperadas');
            }
        }, 100);
    });
    
    // Monitorear cambios en el contador global
    let lastCount = window.nuevoDetalleCount || 0;
    setInterval(() => {
        if (window.nuevoDetalleCount && window.nuevoDetalleCount !== lastCount) {
            console.log(`📊 CONTADOR GLOBAL cambió: ${lastCount} → ${window.nuevoDetalleCount}`);
            lastCount = window.nuevoDetalleCount;
        }
    }, 500);
});

console.log('🔍 MONITOR DE DUPLICACIÓN: Listo para detectar problemas');
