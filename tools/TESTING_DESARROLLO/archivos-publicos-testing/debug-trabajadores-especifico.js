// DEBUG ESPECÍFICO: Diagnóstico de trabajadores Car Wash
console.log('🔍 INICIANDO DIAGNÓSTICO ESPECÍFICO DE TRABAJADORES');

$(document).ready(function() {
    // Función para diagnosticar el estado actual de trabajadores
    function diagnosticarTrabajadores() {
        console.log('🔍 === DIAGNÓSTICO DE TRABAJADORES ===');
        
        // 1. Verificar variable global
        console.log('📊 Variable global trabajadoresPorDetalle:', window.trabajadoresPorDetalle);
        
        // 2. Verificar todos los inputs de trabajadores en el formulario
        console.log('📊 Inputs de trabajadores en el formulario:');
        $('input[name*="trabajadores"]').each(function() {
            console.log(`  - ${$(this).attr('name')}: ${$(this).val()}`);
        });
        
        // 3. Verificar contenedores de trabajadores
        console.log('📊 Contenedores de trabajadores:');
        $('[id*="trabajadores-"]').each(function() {
            const id = $(this).attr('id');
            const content = $(this).html().trim();
            console.log(`  - ${id}: ${content.substring(0, 100)}...`);
        });
        
        // 4. Verificar botones de editar trabajadores
        console.log('📊 Botones de editar trabajadores:');
        $('.editar-trabajadores').each(function() {
            const detalleId = $(this).data('detalle-id');
            console.log(`  - Botón para detalle: ${detalleId}`);
        });
    }
    
    // Ejecutar diagnóstico al cargar
    setTimeout(diagnosticarTrabajadores, 2000);
    
    // También diagnosticar antes del envío del formulario
    $('#forma-editar-venta').on('submit', function() {
        console.log('🔍 DIAGNÓSTICO ANTES DEL ENVÍO:');
        diagnosticarTrabajadores();
    });
    
    // Agregar a ventana global para llamada manual
    window.diagnosticarTrabajadores = diagnosticarTrabajadores;
});

console.log('✅ Diagnóstico de trabajadores cargado. Use window.diagnosticarTrabajadores() para ejecutar manualmente.');
