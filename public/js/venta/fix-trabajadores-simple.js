// Solucionador simple para trabajadores Car Wash en edición de ventas
console.log('🔧 Cargando solucionador simple de trabajadores...');

$(document).ready(function() {
    // Función para asegurar que los trabajadores se envíen en el formulario
    function asegurarTrabajadoresEnFormulario() {
        console.log('🔧 Asegurando trabajadores en formulario...');
        
        // Para detalles existentes - verificar si hay trabajadores en la variable global
        if (window.trabajadoresPorDetalle) {
            Object.keys(window.trabajadoresPorDetalle).forEach(detalleId => {
                const trabajadores = window.trabajadoresPorDetalle[detalleId];
                
                if (detalleId.startsWith('nuevo-')) {
                    // Es un detalle nuevo
                    const indice = detalleId.replace('nuevo-', '');
                    console.log(`🔧 Procesando nuevo detalle ${indice} con trabajadores:`, trabajadores);
                    
                    // Eliminar inputs existentes
                    $(`input[name^="nuevos_trabajadores_carwash[${indice}]"]`).remove();
                    
                    // Agregar nuevos inputs
                    trabajadores.forEach(trabajadorId => {
                        $('#forma-editar-venta').append(
                            `<input type="hidden" name="nuevos_trabajadores_carwash[${indice}][]" value="${trabajadorId}">`
                        );
                    });
                } else {
                    // Es un detalle existente
                    console.log(`🔧 Procesando detalle existente ${detalleId} con trabajadores:`, trabajadores);
                    
                    // Eliminar inputs existentes
                    $(`input[name^="trabajadores_carwash[${detalleId}]"]`).remove();
                    
                    // Agregar nuevos inputs
                    trabajadores.forEach(trabajadorId => {
                        $('#forma-editar-venta').append(
                            `<input type="hidden" name="trabajadores_carwash[${detalleId}][]" value="${trabajadorId}">`
                        );
                    });
                }
            });
        }
        
        // Debug: mostrar todos los inputs de trabajadores que se van a enviar
        console.log('🔧 Inputs de trabajadores que se enviarán:');
        $('input[name*="trabajadores_carwash"]').each(function() {
            console.log(`  ${$(this).attr('name')} = ${$(this).val()}`);
        });
    }
    
    // Interceptar el envío del formulario para asegurar los trabajadores
    $('#forma-editar-venta').on('submit', function(e) {
        console.log('🔧 Formulario enviándose, asegurando trabajadores...');
        asegurarTrabajadoresEnFormulario();
        
        // Permitir que el formulario continúe
        return true;
    });
    
    // También ejecutar cuando se hace clic en el botón guardar
    $('button[type="submit"], input[type="submit"]').on('click', function() {
        console.log('🔧 Botón guardar clickeado, asegurando trabajadores...');
        setTimeout(asegurarTrabajadoresEnFormulario, 100);
    });
    
    // Ejecutar al cargar la página para asegurar estado inicial
    setTimeout(asegurarTrabajadoresEnFormulario, 1000);
});

console.log('✅ Solucionador simple de trabajadores cargado');
