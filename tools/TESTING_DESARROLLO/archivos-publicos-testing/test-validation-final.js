/**
 * Script de validación final para el sistema de comisiones Car Wash
 * Simula y valida las acciones del usuario en la interfaz
 */

console.log('🧪 INICIANDO VALIDACIÓN FINAL DEL SISTEMA DE COMISIONES');

// Función para esperar un tiempo determinado
function wait(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// Función para simular click en elemento
function simulateClick(element) {
    const event = new MouseEvent('click', {
        view: window,
        bubbles: true,
        cancelable: true
    });
    element.dispatchEvent(event);
}

// Función para simular cambio en select
function simulateSelectChange(select, value) {
    select.value = value;
    const event = new Event('change', { bubbles: true });
    select.dispatchEvent(event);
}

// Función para validar que los trabajadores están presentes en el DOM
function validateTrabajadoresInputs() {
    console.log('🔍 Validando inputs de trabajadores...');
    
    const trabajadoresInputs = document.querySelectorAll('input[name*="trabajadores_carwash"]');
    console.log(`Encontrados ${trabajadoresInputs.length} inputs de trabajadores`);
    
    trabajadoresInputs.forEach((input, index) => {
        console.log(`Input ${index + 1}:`, {
            name: input.name,
            value: input.value,
            type: input.type
        });
    });
    
    return trabajadoresInputs.length > 0;
}

// Función para validar modal de edición de trabajadores
function validateEditModal() {
    console.log('🔍 Validando modal de edición de trabajadores...');
    
    const modal = document.getElementById('editarTrabajadoresModal');
    if (!modal) {
        console.error('❌ Modal de edición no encontrado');
        return false;
    }
    
    const selectTrabajadores = modal.querySelector('select[multiple]');
    if (!selectTrabajadores) {
        console.error('❌ Select de trabajadores en modal no encontrado');
        return false;
    }
    
    console.log('✅ Modal de edición encontrado y funcional');
    return true;
}

// Función para probar edición de trabajadores en artículo existente
async function testEditTrabajadores() {
    console.log('🧪 PROBANDO EDICIÓN DE TRABAJADORES EN ARTÍCULO EXISTENTE');
    
    // Buscar primer botón de editar trabajadores
    const editButtons = document.querySelectorAll('button[onclick*="editarTrabajadores"]');
    if (editButtons.length === 0) {
        console.warn('⚠️ No se encontraron botones de editar trabajadores');
        return false;
    }
    
    console.log(`Encontrados ${editButtons.length} botones de editar trabajadores`);
    
    // Simular click en el primer botón
    const firstButton = editButtons[0];
    console.log('🖱️ Simulando click en botón de editar trabajadores...');
    simulateClick(firstButton);
    
    await wait(500);
    
    // Validar que el modal se abrió
    const modal = document.getElementById('editarTrabajadoresModal');
    if (!modal || !modal.classList.contains('show')) {
        console.error('❌ Modal no se abrió correctamente');
        return false;
    }
    
    console.log('✅ Modal se abrió correctamente');
    
    // Simular selección de trabajadores
    const selectTrabajadores = modal.querySelector('select[multiple]');
    if (selectTrabajadores && selectTrabajadores.options.length > 0) {
        // Seleccionar primera opción disponible
        selectTrabajadores.options[0].selected = true;
        if (selectTrabajadores.options.length > 1) {
            selectTrabajadores.options[1].selected = true;
        }
        
        const changeEvent = new Event('change', { bubbles: true });
        selectTrabajadores.dispatchEvent(changeEvent);
        
        console.log('✅ Trabajadores seleccionados en modal');
    }
    
    // Simular click en guardar
    const saveButton = modal.querySelector('button[onclick="guardarTrabajadores()"]');
    if (saveButton) {
        console.log('🖱️ Simulando click en guardar trabajadores...');
        simulateClick(saveButton);
        await wait(300);
        console.log('✅ Botón guardar presionado');
    }
    
    return true;
}

// Función para probar agregar nuevo artículo con trabajadores
async function testAddNewArticle() {
    console.log('🧪 PROBANDO AGREGAR NUEVO ARTÍCULO CON TRABAJADORES');
    
    // Buscar botón de agregar detalle
    const addButton = document.querySelector('button[onclick="agregarDetalle()"]');
    if (!addButton) {
        console.warn('⚠️ Botón de agregar detalle no encontrado');
        return false;
    }
    
    console.log('🖱️ Simulando click en agregar nuevo artículo...');
    simulateClick(addButton);
    
    await wait(500);
    
    // Buscar la nueva fila agregada
    const filas = document.querySelectorAll('tr[id*="fila_detalle_"]');
    const ultimaFila = filas[filas.length - 1];
    
    if (!ultimaFila) {
        console.error('❌ No se pudo encontrar la nueva fila agregada');
        return false;
    }
    
    console.log('✅ Nueva fila agregada correctamente');
    
    // Simular selección de servicio
    const selectServicio = ultimaFila.querySelector('select[name*="servicio_id"]');
    if (selectServicio && selectServicio.options.length > 1) {
        selectServicio.value = selectServicio.options[1].value;
        simulateSelectChange(selectServicio, selectServicio.options[1].value);
        console.log('✅ Servicio seleccionado');
        await wait(300);
    }
    
    // Simular selección de trabajadores
    const selectTrabajadores = ultimaFila.querySelector('select[multiple]');
    if (selectTrabajadores && selectTrabajadores.options.length > 0) {
        selectTrabajadores.options[0].selected = true;
        if (selectTrabajadores.options.length > 1) {
            selectTrabajadores.options[1].selected = true;
        }
        
        const changeEvent = new Event('change', { bubbles: true });
        selectTrabajadores.dispatchEvent(changeEvent);
        console.log('✅ Trabajadores seleccionados en nuevo artículo');
    }
    
    return true;
}

// Función para validar el estado final antes del envío
function validateFinalState() {
    console.log('🔍 VALIDANDO ESTADO FINAL ANTES DEL ENVÍO');
    
    // Ejecutar el corrector definitivo si existe
    if (window.corregirTrabajadoresDefinitivo) {
        console.log('🔧 Ejecutando corrector definitivo...');
        window.corregirTrabajadoresDefinitivo();
    }
    
    // Validar inputs finales
    const hasInputs = validateTrabajadoresInputs();
    
    // Mostrar variable global de trabajadores
    if (window.trabajadoresGlobal) {
        console.log('🌐 Variable global de trabajadores:', window.trabajadoresGlobal);
    }
    
    // Simular envío del formulario (sin realmente enviarlo)
    const form = document.getElementById('forma-editar-venta');
    if (form) {
        console.log('📋 Validando datos del formulario antes del envío...');
        const formData = new FormData(form);
        
        console.log('📊 Datos del formulario:');
        for (let [key, value] of formData.entries()) {
            if (key.includes('trabajadores')) {
                console.log(`${key}: ${value}`);
            }
        }
    }
    
    return hasInputs;
}

// Función principal de validación
async function runFullValidation() {
    console.log('🚀 INICIANDO VALIDACIÓN COMPLETA DEL SISTEMA');
    
    try {
        // Validaciones iniciales
        console.log('\n📋 FASE 1: VALIDACIONES INICIALES');
        const modalValid = validateEditModal();
        const inputsValid = validateTrabajadoresInputs();
        
        console.log('\n📋 FASE 2: PRUEBA DE EDICIÓN DE TRABAJADORES');
        const editValid = await testEditTrabajadores();
        
        console.log('\n📋 FASE 3: PRUEBA DE NUEVO ARTÍCULO');
        const addValid = await testAddNewArticle();
        
        console.log('\n📋 FASE 4: VALIDACIÓN FINAL');
        const finalValid = validateFinalState();
        
        // Resumen final
        console.log('\n📊 RESUMEN DE VALIDACIÓN:');
        console.log(`Modal de edición: ${modalValid ? '✅' : '❌'}`);
        console.log(`Inputs de trabajadores: ${inputsValid ? '✅' : '❌'}`);
        console.log(`Edición de trabajadores: ${editValid ? '✅' : '❌'}`);
        console.log(`Agregar nuevo artículo: ${addValid ? '✅' : '❌'}`);
        console.log(`Estado final: ${finalValid ? '✅' : '❌'}`);
        
        const allValid = modalValid && inputsValid && editValid && addValid && finalValid;
        
        if (allValid) {
            console.log('\n🎉 ¡TODAS LAS VALIDACIONES PASARON EXITOSAMENTE!');
            console.log('✅ El sistema de comisiones está funcionando correctamente');
        } else {
            console.log('\n⚠️ ALGUNAS VALIDACIONES FALLARON');
            console.log('❌ El sistema necesita ajustes adicionales');
        }
        
        return allValid;
        
    } catch (error) {
        console.error('💥 Error durante la validación:', error);
        return false;
    }
}

// Auto-ejecutar cuando se carga la página
document.addEventListener('DOMContentLoaded', () => {
    console.log('📄 Página cargada, esperando 2 segundos antes de iniciar validación...');
    setTimeout(runFullValidation, 2000);
});

// Exportar funciones para uso manual
window.testValidation = {
    runFullValidation,
    testEditTrabajadores,
    testAddNewArticle,
    validateFinalState,
    validateTrabajadoresInputs
};

console.log('🔧 Script de validación final cargado. Use window.testValidation para ejecutar pruebas manuales.');
