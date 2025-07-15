// CORRECTOR DE TRABAJADORES: Solucionador definitivo para el envío de trabajadores
console.log('🛠️ CARGANDO CORRECTOR DEFINITIVO DE TRABAJADORES');

$(document).ready(function() {
    
    // Interceptar el envío del formulario para asegurar que los trabajadores se envíen correctamente
    $('#forma-editar-venta').on('submit', function(e) {
        console.log('🛠️ CORRECTOR: Interceptando envío del formulario');
        
        // 1. CORREGIR TRABAJADORES DE DETALLES EXISTENTES
        if (window.trabajadoresPorDetalle) {
            Object.keys(window.trabajadoresPorDetalle).forEach(key => {
                const trabajadores = window.trabajadoresPorDetalle[key];
                
                if (key.startsWith('nuevo-')) {
                    // Es un detalle nuevo
                    const indice = key.replace('nuevo-', '');
                    console.log(`🛠️ Corrigiendo trabajadores para NUEVO detalle ${indice}:`, trabajadores);
                    
                    // Eliminar inputs existentes
                    $(`input[name*="nuevos_trabajadores_carwash[${indice}]"]`).remove();
                    $(`input[name*="nuevos_detalles[${indice}][trabajadores_carwash]"]`).remove();
                    
                    // Agregar inputs con el formato correcto que espera el backend
                    trabajadores.forEach(trabajadorId => {
                        $('#forma-editar-venta').append(
                            `<input type="hidden" name="nuevos_trabajadores_carwash[${indice}][]" value="${trabajadorId}">`
                        );
                    });
                    
                } else {
                    // Es un detalle existente
                    const detalleId = key;
                    console.log(`🛠️ Corrigiendo trabajadores para detalle EXISTENTE ${detalleId}:`, trabajadores);
                    
                    // Eliminar inputs existentes
                    $(`input[name*="trabajadores_carwash[${detalleId}]"]`).remove();
                    
                    // Agregar inputs con el formato correcto que espera el backend
                    trabajadores.forEach(trabajadorId => {
                        $('#forma-editar-venta').append(
                            `<input type="hidden" name="trabajadores_carwash[${detalleId}][]" value="${trabajadorId}">`
                        );
                    });
                }
            });
        }
        
        // 2. VERIFICAR TRABAJADORES EN NUEVOS DETALLES (contenedor visible)
        $('#tabla-nuevos-detalles .nuevo-detalle-row').each(function() {
            const $row = $(this);
            const indice = $row.data('nuevo-indice');
            const $selectTrabajadores = $row.find('select[name*="trabajadores_carwash"]');
            
            if ($selectTrabajadores.length > 0) {
                const trabajadoresSeleccionados = $selectTrabajadores.val() || [];
                console.log(`🛠️ Encontrados trabajadores en select para nuevo detalle ${indice}:`, trabajadoresSeleccionados);
                
                // Eliminar inputs duplicados
                $(`input[name*="nuevos_trabajadores_carwash[${indice}]"]`).remove();
                
                // Agregar inputs ocultos
                trabajadoresSeleccionados.forEach(trabajadorId => {
                    $('#forma-editar-venta').append(
                        `<input type="hidden" name="nuevos_trabajadores_carwash[${indice}][]" value="${trabajadorId}">`
                    );
                });
            }
        });
        
        // 3. MOSTRAR TODOS LOS INPUTS FINALES QUE SE VAN A ENVIAR
        console.log('🛠️ === INPUTS FINALES DE TRABAJADORES ===');
        $('input[name*="trabajadores_carwash"]').each(function() {
            console.log(`  ${$(this).attr('name')} = ${$(this).val()}`);
        });
        $('input[name*="nuevos_trabajadores_carwash"]').each(function() {
            console.log(`  ${$(this).attr('name')} = ${$(this).val()}`);
        });
        
        console.log('🛠️ CORRECTOR: Formulario listo para envío');
        // Permitir que el formulario se envíe
        return true;
    });
    
    // También corregir cuando se hace click en cualquier botón de guardar
    $('button[type="submit"], .btn-actualizar').on('click', function() {
        console.log('🛠️ CORRECTOR: Botón de envío detectado, aplicando correcciones...');
        $('#forma-editar-venta').trigger('submit');
    });
});

console.log('✅ CORRECTOR DEFINITIVO DE TRABAJADORES CARGADO');
