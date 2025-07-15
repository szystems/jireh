/**
 * Script de prueba automatizada para validar el funcionamiento completo
 * del sistema de comisiones Car Wash
 */

console.log('🧪 INICIANDO PRUEBA AUTOMATIZADA DEL SISTEMA DE COMISIONES');

// Función para esperar un tiempo determinado
function wait(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

// Función para simular click
function simulateClick(element) {
    if (!element) return false;
    const event = new MouseEvent('click', {
        view: window,
        bubbles: true,
        cancelable: true
    });
    element.dispatchEvent(event);
    return true;
}

// Función para simular selección en select múltiple
function simulateSelectMultiple(select, values) {
    if (!select) return false;
    
    // Limpiar selecciones previas
    for (let option of select.options) {
        option.selected = false;
    }
    
    // Seleccionar valores especificados
    values.forEach(value => {
        for (let option of select.options) {
            if (option.value == value) {
                option.selected = true;
                break;
            }
        }
    });
    
    // Disparar evento change
    const event = new Event('change', { bubbles: true });
    select.dispatchEvent(event);
    return true;
}

// Función para verificar estado inicial
function verificarEstadoInicial() {
    console.log('\n📋 VERIFICANDO ESTADO INICIAL...');
    
    const checks = {
        modal: !!document.getElementById('editarTrabajadoresModal'),
        botonAgregar: !!document.querySelector('button[onclick="agregarDetalle()"]'),
        inputsTrabajadores: document.querySelectorAll('input[name*="trabajadores_carwash"]').length,
        botoneEditarExistentes: document.querySelectorAll('button[onclick*="editarTrabajadores"]').length
    };
    
    console.log('Estado inicial:', checks);
    return checks;
}

// Función para probar edición de trabajadores existentes
async function probarEdicionTrabajadores() {
    console.log('\n🔧 PROBANDO EDICIÓN DE TRABAJADORES EXISTENTES...');
    
    // Buscar botón de editar trabajadores
    const botonEditar = document.querySelector('button[onclick*="editarTrabajadores"]');
    if (!botonEditar) {
        console.log('❌ No se encontró botón de editar trabajadores');
        return false;
    }
    
    console.log('✅ Botón de editar encontrado, simulando click...');
    simulateClick(botonEditar);
    
    await wait(500);
    
    // Verificar que el modal se abrió
    const modal = document.getElementById('editarTrabajadoresModal');
    if (!modal || !modal.classList.contains('show')) {
        console.log('❌ Modal no se abrió');
        return false;
    }
    
    console.log('✅ Modal abierto correctamente');
    
    // Seleccionar trabajadores en el modal
    const selectTrabajadores = modal.querySelector('select[multiple]');
    if (selectTrabajadores && selectTrabajadores.options.length > 0) {
        const valoresASeleccionar = [];
        
        // Seleccionar hasta 3 trabajadores
        for (let i = 0; i < Math.min(3, selectTrabajadores.options.length); i++) {
            if (selectTrabajadores.options[i].value) {
                valoresASeleccionar.push(selectTrabajadores.options[i].value);
            }
        }
        
        console.log(`🎯 Seleccionando trabajadores: ${valoresASeleccionar.join(', ')}`);
        simulateSelectMultiple(selectTrabajadores, valoresASeleccionar);
        
        await wait(300);
        
        // Guardar selección
        const botonGuardar = modal.querySelector('button[onclick="guardarTrabajadores()"]');
        if (botonGuardar) {
            console.log('💾 Guardando selección de trabajadores...');
            simulateClick(botonGuardar);
            await wait(300);
        }
    }
    
    return true;
}

// Función para probar agregar nuevo detalle
async function probarNuevoDetalle() {
    console.log('\n➕ PROBANDO AGREGAR NUEVO DETALLE...');
    
    const botonAgregar = document.querySelector('button[onclick="agregarDetalle()"]');
    if (!botonAgregar) {
        console.log('❌ No se encontró botón de agregar detalle');
        return false;
    }
    
    console.log('✅ Botón agregar encontrado, simulando click...');
    simulateClick(botonAgregar);
    
    await wait(500);
    
    // Buscar la nueva fila agregada
    const filas = document.querySelectorAll('tr[id*="fila_detalle_"]');
    const ultimaFila = filas[filas.length - 1];
    
    if (!ultimaFila) {
        console.log('❌ No se encontró nueva fila agregada');
        return false;
    }
    
    console.log('✅ Nueva fila agregada');
    
    // Seleccionar un artículo/servicio
    const selectArticulo = ultimaFila.querySelector('select[name*="articulo_id"]');
    if (selectArticulo && selectArticulo.options.length > 1) {
        // Buscar un servicio (tipo 'servicio')
        let servicioEncontrado = false;
        for (let i = 1; i < selectArticulo.options.length; i++) {
            const option = selectArticulo.options[i];
            if (option.text.toLowerCase().includes('servicio') || option.text.toLowerCase().includes('carwash')) {
                selectArticulo.value = option.value;
                const event = new Event('change', { bubbles: true });
                selectArticulo.dispatchEvent(event);
                console.log(`🎯 Servicio seleccionado: ${option.text}`);
                servicioEncontrado = true;
                break;
            }
        }
        
        if (!servicioEncontrado) {
            // Seleccionar primer artículo disponible
            selectArticulo.value = selectArticulo.options[1].value;
            const event = new Event('change', { bubbles: true });
            selectArticulo.dispatchEvent(event);
            console.log(`🎯 Artículo seleccionado: ${selectArticulo.options[1].text}`);
        }
        
        await wait(500);
    }
    
    // Seleccionar trabajadores en la nueva fila
    const selectTrabajadores = ultimaFila.querySelector('select[multiple]');
    if (selectTrabajadores) {
        const valoresASeleccionar = [];
        
        // Seleccionar hasta 2 trabajadores
        for (let i = 0; i < Math.min(2, selectTrabajadores.options.length); i++) {
            if (selectTrabajadores.options[i].value) {
                valoresASeleccionar.push(selectTrabajadores.options[i].value);
            }
        }
        
        if (valoresASeleccionar.length > 0) {
            console.log(`🎯 Seleccionando trabajadores en nuevo detalle: ${valoresASeleccionar.join(', ')}`);
            simulateSelectMultiple(selectTrabajadores, valoresASeleccionar);
            await wait(300);
        }
    }
    
    return true;
}

// Función para validar inputs finales
async function validarInputsFinales() {
    console.log('\n🔍 VALIDANDO INPUTS FINALES...');
    
    // Ejecutar corrector si existe
    if (window.corregirTrabajadoresDefinitivo) {
        console.log('🔧 Ejecutando corrector definitivo...');
        window.corregirTrabajadoresDefinitivo();
        await wait(200);
    }
    
    // Contar inputs de trabajadores
    const inputsTrabajadores = document.querySelectorAll('input[name*="trabajadores_carwash"]');
    console.log(`📊 Total de inputs de trabajadores: ${inputsTrabajadores.length}`);
    
    const trabajadoresData = {};
    inputsTrabajadores.forEach(input => {
        if (input.value) {
            if (!trabajadoresData[input.name]) {
                trabajadoresData[input.name] = [];
            }
            trabajadoresData[input.name].push(input.value);
        }
    });
    
    console.log('📋 Datos de trabajadores a enviar:', trabajadoresData);
    
    // Mostrar variable global si existe
    if (window.trabajadoresGlobal) {
        console.log('🌐 Variable global de trabajadores:', window.trabajadoresGlobal);
    }
    
    return Object.keys(trabajadoresData).length > 0;
}

// Función para simular envío (SIN ENVIAR REALMENTE)
function simularEnvio() {
    console.log('\n📤 SIMULANDO ENVÍO DEL FORMULARIO...');
    
    const form = document.getElementById('forma-editar-venta');
    if (!form) {
        console.log('❌ Formulario no encontrado');
        return false;
    }
    
    const formData = new FormData(form);
    const trabajadoresEnviados = {};
    
    for (let [key, value] of formData.entries()) {
        if (key.includes('trabajadores')) {
            if (!trabajadoresEnviados[key]) {
                trabajadoresEnviados[key] = [];
            }
            trabajadoresEnviados[key].push(value);
        }
    }
    
    console.log('📊 Datos de trabajadores que se enviarían:', trabajadoresEnviados);
    
    const totalTrabajadores = Object.keys(trabajadoresEnviados).length;
    console.log(`📈 Total de campos de trabajadores a enviar: ${totalTrabajadores}`);
    
    return totalTrabajadores > 0;
}

// Función principal de prueba
async function ejecutarPruebaCompleta() {
    console.log('🚀 EJECUTANDO PRUEBA COMPLETA DEL SISTEMA');
    console.log('==========================================\n');
    
    const resultados = {
        estadoInicial: false,
        edicionTrabajadores: false,
        nuevoDetalle: false,
        inputsFinales: false,
        simulacionEnvio: false
    };
    
    try {
        // 1. Verificar estado inicial
        const estadoInicial = verificarEstadoInicial();
        resultados.estadoInicial = estadoInicial.modal && estadoInicial.botonAgregar;
        
        // 2. Probar edición de trabajadores existentes
        if (estadoInicial.botoneEditarExistentes > 0) {
            resultados.edicionTrabajadores = await probarEdicionTrabajadores();
        } else {
            console.log('⚠️ No hay detalles existentes para editar trabajadores');
            resultados.edicionTrabajadores = true; // No es un error si no hay detalles
        }
        
        // 3. Probar agregar nuevo detalle
        resultados.nuevoDetalle = await probarNuevoDetalle();
          // 4. Validar inputs finales
        await wait(500);
        resultados.inputsFinales = await validarInputsFinales();
        
        // 5. Simular envío
        resultados.simulacionEnvio = simularEnvio();
        
        // Mostrar resultados finales
        console.log('\n📊 RESULTADOS DE LA PRUEBA:');
        console.log('============================');
        console.log(`Estado inicial: ${resultados.estadoInicial ? '✅' : '❌'}`);
        console.log(`Edición trabajadores: ${resultados.edicionTrabajadores ? '✅' : '❌'}`);
        console.log(`Nuevo detalle: ${resultados.nuevoDetalle ? '✅' : '❌'}`);
        console.log(`Inputs finales: ${resultados.inputsFinales ? '✅' : '❌'}`);
        console.log(`Simulación envío: ${resultados.simulacionEnvio ? '✅' : '❌'}`);
        
        const exito = Object.values(resultados).every(resultado => resultado);
        
        if (exito) {
            console.log('\n🎉 ¡TODAS LAS PRUEBAS PASARON EXITOSAMENTE!');
            console.log('✅ El sistema de comisiones Car Wash está funcionando correctamente');
        } else {
            console.log('\n⚠️ ALGUNAS PRUEBAS FALLARON');
            console.log('❌ Revisar los elementos que no funcionaron correctamente');
        }
        
        return exito;
        
    } catch (error) {
        console.error('💥 Error durante la prueba:', error);
        return false;
    }
}

// Auto-ejecutar la prueba cuando la página esté lista
document.addEventListener('DOMContentLoaded', () => {
    console.log('📄 Página cargada, esperando 3 segundos antes de iniciar prueba automática...');
    setTimeout(ejecutarPruebaCompleta, 3000);
});

// Exportar para uso manual
window.pruebaAutomatizada = {
    ejecutarPruebaCompleta,
    probarEdicionTrabajadores,
    probarNuevoDetalle,
    validarInputsFinales,
    simularEnvio
};

console.log('🔧 Script de prueba automatizada cargado. Use window.pruebaAutomatizada para ejecutar pruebas manuales.');
