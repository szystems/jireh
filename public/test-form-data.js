// Test para verificar que los datos del formulario de edición se envían correctamente
console.log('=== TEST DATOS FORMULARIO EDICIÓN ===');

// Función para inspeccionar el formulario cuando el usuario haga click en "Actualizar"
function interceptFormSubmit() {
    const form = document.querySelector('form[method="POST"]');
    if (!form) {
        console.log('❌ No se encontró el formulario');
        return;
    }

    console.log('✅ Formulario encontrado');
    
    // Interceptar el submit
    form.addEventListener('submit', function(e) {
        console.log('🔍 INTERCEPTANDO ENVÍO DEL FORMULARIO...');
        
        // Obtener todos los datos del formulario
        const formData = new FormData(form);
        
        console.log('📋 DATOS ENCONTRADOS EN EL FORMULARIO:');
        for (let [key, value] of formData.entries()) {
            if (key.includes('trabajador') || key.includes('carwash')) {
                console.log(`🔧 ${key}: ${value}`);
            }
        }
        
        // Verificar inputs específicos de trabajadores
        const trabajadoresInputs = form.querySelectorAll('input[name*="trabajadores_carwash"]');
        console.log(`👥 Inputs de trabajadores_carwash encontrados: ${trabajadoresInputs.length}`);
        
        trabajadoresInputs.forEach((input, index) => {
            console.log(`  Input ${index + 1}: name="${input.name}", value="${input.value}", type="${input.type}"`);
        });
        
        const nuevosInputs = form.querySelectorAll('input[name*="nuevos_trabajadores_carwash"]');
        console.log(`➕ Inputs de nuevos_trabajadores_carwash encontrados: ${nuevosInputs.length}`);
        
        nuevosInputs.forEach((input, index) => {
            console.log(`  Input ${index + 1}: name="${input.name}", value="${input.value}", type="${input.type}"`);
        });
        
        console.log('✅ Test completado. Enviando formulario...');
        // No prevenir el envío, solo inspeccionar
    });
    
    console.log('🎯 Interceptor de formulario configurado. Ahora edita algo y haz click en "Actualizar".');
}

// Ejecutar cuando la página esté lista
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', interceptFormSubmit);
} else {
    interceptFormSubmit();
}
