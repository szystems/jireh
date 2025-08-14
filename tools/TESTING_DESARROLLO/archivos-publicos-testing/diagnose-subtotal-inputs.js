// 🔍 SCRIPT DE DIAGNÓSTICO AVANZADO PARA INPUTS DE SUBTOTAL
// Este script ayuda a identificar por qué los inputs de subtotal no se encuentran

console.clear();
console.log("🔍 ===============================================");
console.log("🔍 DIAGNÓSTICO AVANZADO - INPUTS DE SUBTOTAL");
console.log("🔍 ===============================================");

// Función para inspeccionar la estructura DOM completa
function inspeccionarEstructuraDOM() {
    console.log("\n📋 INSPECCIÓN COMPLETA DE LA ESTRUCTURA DOM:");
    console.log("==============================================");
    
    // 1. Verificar tabla de detalles existentes
    const $tablaExistentes = $('#tabla-detalles-existentes');
    console.log(`\n🔍 TABLA DE DETALLES EXISTENTES:`);
    console.log(`   Existe: ${$tablaExistentes.length > 0}`);
    
    if ($tablaExistentes.length > 0) {
        const $filas = $tablaExistentes.find('tr');
        console.log(`   Total de filas: ${$filas.length}`);
        
        $filas.each(function(index) {
            const $fila = $(this);
            const esVisible = $fila.is(':visible') && !$fila.hasClass('d-none');
            console.log(`   Fila ${index + 1}: visible=${esVisible}, classes="${$fila.attr('class') || 'ninguna'}"`);
            
            // Buscar inputs en esta fila
            const $inputsEnFila = $fila.find('input');
            console.log(`     Inputs en fila: ${$inputsEnFila.length}`);
            
            $inputsEnFila.each(function(i) {
                const $input = $(this);
                const nombre = $input.attr('name') || 'sin-name';
                const tipo = $input.attr('type') || 'sin-type';
                const valor = $input.val();
                const esVisible = $input.is(':visible');
                
                console.log(`       Input ${i + 1}: name="${nombre}", type="${tipo}", value="${valor}", visible=${esVisible}`);
                
                // Verificar si este input cumple nuestros criterios de subtotal
                const esSubtotal = nombre.includes('sub_total') || 
                                 $input.hasClass('subtotal-input') || 
                                 nombre.includes('[sub_total]');
                                 
                if (esSubtotal) {
                    console.log(`         ⭐ ESTE ES UN INPUT DE SUBTOTAL`);
                }
            });
        });
    }
    
    // 2. Verificar tabla de nuevos detalles
    const $tablaNuevos = $('#tabla-nuevos-detalles, #nuevos-detalles');
    console.log(`\n🔍 TABLA DE NUEVOS DETALLES:`);
    console.log(`   Existe: ${$tablaNuevos.length > 0}`);
    
    if ($tablaNuevos.length > 0) {
        const $filasNuevas = $tablaNuevos.find('tr');
        console.log(`   Total de filas: ${$filasNuevas.length}`);
        
        const $inputsNuevos = $tablaNuevos.find('input');
        console.log(`   Total de inputs: ${$inputsNuevos.length}`);
    }
    
    // 3. Buscar ALL inputs de subtotal con diferentes estrategias
    console.log(`\n🔍 BÚSQUEDA DE INPUTS DE SUBTOTAL (DIFERENTES ESTRATEGIAS):`);
    
    // Estrategia 1: Selector original
    const $inputs1 = $('input[name*="sub_total"], input.subtotal-input, input[name*="[sub_total]"]');
    console.log(`   Estrategia 1 (original): ${$inputs1.length} inputs`);
    
    // Estrategia 2: Solo por name que contenga sub_total
    const $inputs2 = $('input[name*="sub_total"]');
    console.log(`   Estrategia 2 (solo name sub_total): ${$inputs2.length} inputs`);
    
    // Estrategia 3: Solo por clase
    const $inputs3 = $('input.subtotal-input');
    console.log(`   Estrategia 3 (solo clase): ${$inputs3.length} inputs`);
    
    // Estrategia 4: Buscar en contexto específico
    const $inputs4 = $('#tabla-detalles-existentes input, #tabla-nuevos-detalles input, #nuevos-detalles input');
    console.log(`   Estrategia 4 (contexto tablas): ${$inputs4.length} inputs`);
    
    // Estrategia 5: Buscar todos los inputs y filtrar
    const $todosInputs = $('input');
    let inputsSubtotal = [];
    $todosInputs.each(function() {
        const $input = $(this);
        const nombre = $input.attr('name') || '';
        if (nombre.includes('sub_total') || nombre.includes('subtotal')) {
            inputsSubtotal.push($input);
        }
    });
    console.log(`   Estrategia 5 (todos filtrados): ${inputsSubtotal.length} inputs`);
    
    // Mostrar detalles de los inputs encontrados
    if ($inputs1.length > 0) {
        console.log(`\n📋 DETALLES DE INPUTS ENCONTRADOS (ESTRATEGIA 1):`);
        $inputs1.each(function(index) {
            const $input = $(this);
            const nombre = $input.attr('name') || 'sin-name';
            const valor = $input.val();
            const esVisible = $input.is(':visible');
            const $fila = $input.closest('tr');
            const filaOculta = $fila.hasClass('d-none') || $fila.css('display') === 'none';
            
            console.log(`     Input ${index + 1}:`);
            console.log(`       Name: ${nombre}`);
            console.log(`       Value: ${valor}`);
            console.log(`       Visible: ${esVisible}`);
            console.log(`       Fila oculta: ${filaOculta}`);
            console.log(`       Fila classes: ${$fila.attr('class') || 'ninguna'}`);
        });
    }
    
    return {
        estrategia1: $inputs1.length,
        estrategia2: $inputs2.length,
        estrategia3: $inputs3.length,
        estrategia4: $inputs4.length,
        estrategia5: inputsSubtotal.length
    };
}

// Función para monitorear cambios en tiempo real
function iniciarMonitoreoTiempoReal() {
    console.log("\n🔄 INICIANDO MONITOREO EN TIEMPO REAL:");
    console.log("====================================");
    
    let contadorLlamadas = 0;
    
    const monitoreo = setInterval(() => {
        contadorLlamadas++;
        console.log(`\n⏰ MONITOREO ${contadorLlamadas} (cada 2 segundos):`);
        
        const resultados = inspeccionarEstructuraDOM();
        
        if (resultados.estrategia1 > 0) {
            console.log("✅ ¡Inputs de subtotal encontrados!");
            if (contadorLlamadas >= 3) {
                console.log("🔄 Deteniendo monitoreo (inputs encontrados)");
                clearInterval(monitoreo);
            }
        } else {
            console.log("❌ No se encontraron inputs de subtotal");
            if (contadorLlamadas >= 10) {
                console.log("🔄 Deteniendo monitoreo (máximo alcanzado)");
                clearInterval(monitoreo);
            }
        }
    }, 2000);
    
    // También monitorear cambios del DOM
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' || mutation.type === 'attributes') {
                console.log("🔄 DOM modificado, verificando inputs...");
                const $inputs = $('input[name*="sub_total"], input.subtotal-input, input[name*="[sub_total]"]');
                console.log(`   Inputs después del cambio: ${$inputs.length}`);
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['class', 'style', 'name']
    });
    
    // Detener el observer después de 30 segundos
    setTimeout(() => {
        observer.disconnect();
        console.log("🔄 Observer de DOM desconectado");
    }, 30000);
}

// Ejecutar diagnóstico inicial
const resultadosIniciales = inspeccionarEstructuraDOM();

console.log("\n📊 RESUMEN DE RESULTADOS:");
console.log("========================");
Object.keys(resultadosIniciales).forEach(estrategia => {
    console.log(`${estrategia}: ${resultadosIniciales[estrategia]} inputs`);
});

// Iniciar monitoreo
iniciarMonitoreoTiempoReal();

console.log("\n🔍 DIAGNÓSTICO COMPLETO INICIADO");
console.log("=================================");
console.log("El monitoreo continuará por 20 segundos...");
