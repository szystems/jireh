// 🧪 SCRIPT DE PRUEBA RÁPIDA PARA TOTALES
// Copiar y pegar en la consola del navegador en la página de edición

console.clear();
console.log("🧪 INICIANDO PRUEBA RÁPIDA DE TOTALES");
console.log("===================================");

// Función para obtener información del estado actual
function obtenerEstadoActual() {
    const $total = $('#total-venta');
    const totalTexto = $total.text();
    const totalMatch = totalTexto.match(/[\d,]+\.?\d*/);
    const totalActual = totalMatch ? parseFloat(totalMatch[0].replace(/,/g, '')) : 0;
    
    // Múltiples estrategias para encontrar inputs
    const $inputs1 = $('input[name*="sub_total"], input.subtotal-input, input[name*="[sub_total]"]');
    const $inputs2 = $('#tabla-detalles-existentes input, #tabla-nuevos-detalles input').filter(function() {
        const nombre = $(this).attr('name') || '';
        return nombre.includes('sub_total') || nombre.includes('subtotal');
    });
    
    let sumaManual = 0;
    let conteoVisibles = 0;
    
    // Usar la estrategia que encuentre más inputs
    const $inputs = $inputs1.length > 0 ? $inputs1 : $inputs2;
    
    $inputs.each(function() {
        const $input = $(this);
        const esVisible = $input.is(':visible') && !$input.closest('tr').hasClass('d-none');
        if (esVisible) {
            const valor = parseFloat($input.val()) || 0;
            if (valor > 0) {
                sumaManual += valor;
                conteoVisibles++;
            }
        }
    });
    
    return {
        totalMostrado: totalActual,
        sumaManual: sumaManual,
        inputsVisibles: conteoVisibles,
        totalInputs: $inputs.length,
        inputsEstrategia1: $inputs1.length,
        inputsEstrategia2: $inputs2.length,
        coincide: Math.abs(totalActual - sumaManual) < 0.01
    };
}

// Función de diagnóstico estructural
function diagnosticoEstructural() {
    console.log("\n🔍 DIAGNÓSTICO ESTRUCTURAL:");
    console.log("===========================");
    
    const $tablaExistentes = $('#tabla-detalles-existentes');
    const $tablaNuevos = $('#tabla-nuevos-detalles, #nuevos-detalles');
    
    console.log(`📋 Tabla existentes: ${$tablaExistentes.length > 0 ? '✅' : '❌'}`);
    if ($tablaExistentes.length > 0) {
        console.log(`   Filas: ${$tablaExistentes.find('tr').length}`);
        console.log(`   Inputs: ${$tablaExistentes.find('input').length}`);
        
        // Mostrar algunos inputs de muestra
        $tablaExistentes.find('input').slice(0, 5).each(function(i) {
            const $input = $(this);
            const nombre = $input.attr('name') || 'sin-name';
            const valor = $input.val();
            console.log(`     Input ${i+1}: ${nombre} = "${valor}"`);
        });
    }
    
    console.log(`📋 Tabla nuevos: ${$tablaNuevos.length > 0 ? '✅' : '❌'}`);
    if ($tablaNuevos.length > 0) {
        console.log(`   Filas: ${$tablaNuevos.find('tr').length}`);
        console.log(`   Inputs: ${$tablaNuevos.find('input').length}`);
    }
    
    // Buscar inputs que podrían ser subtotales
    console.log(`\n🔍 BÚSQUEDA DE INPUTS SOSPECHOSOS:`);
    let inputsSospechosos = 0;
    $('input').each(function() {
        const $input = $(this);
        const nombre = $input.attr('name') || '';
        const valor = $input.val();
        
        if (nombre.includes('total') || nombre.includes('precio') || nombre.includes('subtotal')) {
            inputsSospechosos++;
            if (inputsSospechosos <= 5) { // Solo mostrar los primeros 5
                console.log(`     Sospechoso ${inputsSospechosos}: ${nombre} = "${valor}"`);
            }
        }
    });
    console.log(`   Total de inputs sospechosos: ${inputsSospechosos}`);
}

// Ejecutar prueba inicial
console.log("📊 ESTADO INICIAL:");
const estadoInicial = obtenerEstadoActual();
console.log(`   Total mostrado: ${estadoInicial.totalMostrado}`);
console.log(`   Suma manual: ${estadoInicial.sumaManual}`);
console.log(`   Inputs visibles: ${estadoInicial.inputsVisibles}/${estadoInicial.totalInputs}`);
console.log(`   Estrategia 1: ${estadoInicial.inputsEstrategia1} inputs`);
console.log(`   Estrategia 2: ${estadoInicial.inputsEstrategia2} inputs`);
console.log(`   ¿Coincide?: ${estadoInicial.coincide ? '✅ SÍ' : '❌ NO'}`);

// Ejecutar diagnóstico estructural
diagnosticoEstructural();
// Ejecutar función de depuración manual si existe
if (typeof window.debugTotalManual === 'function') {
    console.log("\n🐛 EJECUTANDO DEPURACIÓN MANUAL:");
    window.debugTotalManual();
} else {
    console.log("⚠️ Función debugTotalManual no encontrada");
}

// Ejecutar actualización manual del total
if (typeof window.actualizarTotalVenta === 'function') {
    console.log("\n🔄 EJECUTANDO ACTUALIZACIÓN MANUAL:");
    window.actualizarTotalVenta();
    
    // Verificar estado después de la actualización
    setTimeout(() => {
        const estadoFinal = obtenerEstadoActual();
        console.log("\n📊 ESTADO DESPUÉS DE ACTUALIZACIÓN:");
        console.log(`   Total mostrado: ${estadoFinal.totalMostrado}`);
        console.log(`   Suma manual: ${estadoFinal.sumaManual}`);
        console.log(`   Inputs encontrados: ${estadoFinal.totalInputs}`);
        console.log(`   ¿Coincide?: ${estadoFinal.coincide ? '✅ SÍ' : '❌ NO'}`);
        
        if (estadoFinal.coincide) {
            console.log("\n🎉 ¡PRUEBA EXITOSA! El total se calcula correctamente.");
        } else {
            console.log("\n❌ PRUEBA FALLIDA: Hay una discrepancia en el cálculo.");
            console.log("💡 Sugerencia: Ejecutar el script de diagnóstico completo:");
            console.log("   Copiar y pegar el contenido de diagnose-subtotal-inputs.js");
        }
    }, 200);
} else {
    console.log("❌ Función actualizarTotalVenta no encontrada");
}

console.log("\n🧪 PRUEBA COMPLETADA");
console.log("===================");
