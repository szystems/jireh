<?php
/**
 * Script de debugging para trabajadores de carwash en edición de ventas
 * Verifica que los inputs se estén creando correctamente y se envíen en el formulario
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "=== DEBUG TRABAJADORES CARWASH - JIREH AUTOMOTRIZ ===\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n\n";

// Verificar archivo de vista
$archivoVista = __DIR__ . '/resources/views/admin/venta/edit.blade.php';

if (!file_exists($archivoVista)) {
    echo "❌ ERROR: No se encontró el archivo de vista: $archivoVista\n";
    exit(1);
}

echo "📄 Analizando archivo de vista para funcionalidad de trabajadores...\n";
$contenidoVista = file_get_contents($archivoVista);

// Verificar elementos clave para trabajadores
$verificaciones = [
    'modal_trabajadores' => [
        'patron' => '/id=["\']editar-trabajadores-modal["\']/',
        'nombre' => 'Modal de editar trabajadores',
        'descripcion' => 'Modal existe en la vista'
    ],
    'select_trabajadores_edit' => [
        'patron' => '/id=["\']trabajadores-carwash-edit["\']/',
        'nombre' => 'Select para editar trabajadores',
        'descripcion' => 'Select para modaleditar trabajadores'
    ],
    'boton_guardar_trabajadores' => [
        'patron' => '/id=["\']guardar-trabajadores["\']/',
        'nombre' => 'Botón guardar trabajadores',
        'descripcion' => 'Botón para guardar cambios en modal'
    ],
    'contenedor_trabajadores' => [
        'patron' => '/id=["\']trabajadores-\{\{.*?\}\}["\']/',
        'nombre' => 'Contenedor de trabajadores',
        'descripcion' => 'Div contenedor para inputs de trabajadores'
    ],
    'input_trabajadores_template' => [
        'patron' => '/name=["\']trabajadores_carwash\[/',
        'nombre' => 'Template de inputs trabajadores',
        'descripcion' => 'Formato de name para inputs'
    ],
    'evento_guardar_trabajadores' => [
        'patron' => '/\$\(["\']#guardar-trabajadores["\']\)\.on\(["\']click["\']/',
        'nombre' => 'Evento click guardar trabajadores',
        'descripcion' => 'Event listener para guardar trabajadores'
    ],
    'creacion_inputs' => [
        'patron' => '/trabajadores_carwash\[\$\{.*?\}\]\[\]/',
        'nombre' => 'Creación dinámica de inputs',
        'descripcion' => 'Template string para crear inputs dinámicamente'
    ]
];

$resultados = [];
$todoOk = true;

echo "\n🔍 VERIFICANDO COMPONENTES DE TRABAJADORES:\n";
echo str_repeat("-", 60) . "\n";

foreach ($verificaciones as $clave => $verificacion) {
    $encontrado = preg_match($verificacion['patron'], $contenidoVista);
    $resultados[$clave] = $encontrado;
    
    $icono = $encontrado ? "✅" : "❌";
    echo sprintf("%-35s %s %s\n", 
        $verificacion['nombre'] . ":", 
        $icono, 
        $verificacion['descripcion']
    );
    
    if (!$encontrado) {
        $todoOk = false;
    }
}

echo "\n🧩 VERIFICANDO LÓGICA JAVASCRIPT:\n";
echo str_repeat("-", 60) . "\n";

// Verificar partes específicas del JavaScript
$logicaJS = [
    'function eliminarDetalleExistente' => 'Función para eliminar detalles',
    'trabajadoresSeleccionados.forEach' => 'Loop para crear inputs',
    'trabajadorId.toString().trim()' => 'Validación de ID trabajador',
    'append(inputHtml)' => 'Agregar input al DOM',
    'trabajadores_carwash[${detalleActualEditando}][]' => 'Template de nombre de input',
    'detalleActualEditando = null' => 'Reset de variable de estado',
    '$containerTrabajadores.empty()' => 'Limpiar contenedor antes de agregar'
];

foreach ($logicaJS as $codigo => $descripcion) {
    $encontrado = strpos($contenidoVista, $codigo) !== false;
    $icono = $encontrado ? "✅" : "❌";
    echo sprintf("%-40s %s %s\n", $codigo . ":", $icono, $descripcion);
    
    if (!$encontrado) {
        $todoOk = false;
    }
}

echo "\n📝 INSTRUCCIONES DE DEBUGGING MANUAL:\n";
echo str_repeat("-", 60) . "\n";

echo "1. ABRIR HERRAMIENTAS DE DESARROLLADOR (F12)\n";
echo "2. IR A LA PESTAÑA CONSOLE\n";
echo "3. REALIZAR ESTAS VERIFICACIONES:\n\n";

echo "🔧 VERIFICAR MODAL Y ELEMENTOS:\n";
echo "   console.log('Modal existe:', $('#editar-trabajadores-modal').length > 0);\n";
echo "   console.log('Select existe:', $('#trabajadores-carwash-edit').length > 0);\n";
echo "   console.log('Botón existe:', $('#guardar-trabajadores').length > 0);\n\n";

echo "🔧 VERIFICAR EVENTO DE GUARDAR:\n";
echo "   $._data($('#guardar-trabajadores')[0], 'events');\n\n";

echo "🔧 VERIFICAR INPUTS ANTES DE GUARDAR:\n";
echo "   // Seleccionar un detalle y abrir modal\n";
echo "   // En el modal, seleccionar trabajadores\n";
echo "   // Antes de hacer clic en 'Aplicar cambios', ejecutar:\n";
echo "   var detalleId = 'DETALLE_ID_AQUI'; // Reemplazar con ID real\n";
echo "   console.log('Container existe:', $('#trabajadores-' + detalleId).length > 0);\n";
echo "   console.log('Inputs existentes:', $('#trabajadores-' + detalleId + ' input').length);\n\n";

echo "🔧 VERIFICAR DESPUÉS DE GUARDAR:\n";
echo "   // Después de hacer clic en 'Aplicar cambios', ejecutar:\n";
echo "   var detalleId = 'DETALLE_ID_AQUI'; // Reemplazar con ID real\n";
echo "   console.log('Inputs creados:', $('#trabajadores-' + detalleId + ' input').length);\n";
echo "   $('#trabajadores-' + detalleId + ' input').each(function() {\n";
echo "       console.log('Input:', this.name, '=', this.value);\n";
echo "   });\n\n";

echo "🔧 VERIFICAR AL ENVIAR FORMULARIO:\n";
echo "   $('form').on('submit', function() {\n";
echo "       var inputs = $('input[name*=\"trabajadores_carwash\"]');\n";
echo "       console.log('Total inputs trabajadores:', inputs.length);\n";
echo "       inputs.each(function() {\n";
echo "           console.log('Enviando:', this.name, '=', this.value);\n";
echo "       });\n";
echo "   });\n\n";

echo "📊 POSIBLES PROBLEMAS Y SOLUCIONES:\n";
echo str_repeat("-", 60) . "\n";

echo "❌ PROBLEMA: Modal no se abre\n";
echo "   CAUSA: Event listener no asignado o elemento no existe\n";
echo "   SOLUCIÓN: Verificar que $('.editar-trabajadores').on('click') esté funcionando\n\n";

echo "❌ PROBLEMA: Inputs no se crean\n";
echo "   CAUSA: Variable detalleActualEditando es null o container no existe\n";
echo "   SOLUCIÓN: Verificar que detalleActualEditando se asigne correctamente\n\n";

echo "❌ PROBLEMA: Inputs se crean pero no se envían\n";
echo "   CAUSA: Inputs están fuera del formulario o se eliminan antes del envío\n";
echo "   SOLUCIÓN: Verificar que el container esté dentro del <form>\n\n";

echo "❌ PROBLEMA: Backend no recibe los datos\n";
echo "   CAUSA: Nombre de inputs incorrecto o formato no esperado\n";
echo "   SOLUCIÓN: Verificar en Network tab que se envían trabajadores_carwash[ID][]\n\n";

echo "🔨 SCRIPT DE VERIFICACIÓN AUTOMÁTICA:\n";
echo str_repeat("-", 60) . "\n";

echo "Ejecutar en consola del navegador:\n\n";

echo "// FUNCIÓN DE VERIFICACIÓN COMPLETA\n";
echo "function debugTrabajadores() {\n";
echo "    console.log('=== DEBUG TRABAJADORES CARWASH ===');\n";
echo "    \n";
echo "    // Verificar elementos básicos\n";
echo "    console.log('Modal:', $('#editar-trabajadores-modal').length);\n";
echo "    console.log('Select:', $('#trabajadores-carwash-edit').length);\n";
echo "    console.log('Botón:', $('#guardar-trabajadores').length);\n";
echo "    \n";
echo "    // Verificar detalles existentes\n";
echo "    var detalles = $('.detalle-existente');\n";
echo "    console.log('Detalles existentes:', detalles.length);\n";
echo "    \n";
echo "    detalles.each(function(index, detalle) {\n";
echo "        var id = $(detalle).attr('id').replace('detalle-row-', '');\n";
echo "        var container = $('#trabajadores-' + id);\n";
echo "        var inputs = container.find('input');\n";
echo "        console.log('Detalle ' + id + ': container=' + container.length + ', inputs=' + inputs.length);\n";
echo "        \n";
echo "        inputs.each(function() {\n";
echo "            console.log('  Input:', this.name, '=', this.value);\n";
echo "        });\n";
echo "    });\n";
echo "    \n";
echo "    // Verificar eventos\n";
echo "    var eventos = $._data($('#guardar-trabajadores')[0], 'events');\n";
echo "    console.log('Eventos en botón guardar:', eventos);\n";
echo "    \n";
echo "    return 'Verificación completa';\n";
echo "}\n\n";

echo "// Ejecutar la función\n";
echo "debugTrabajadores();\n\n";

if ($todoOk) {
    echo "✅ ESTRUCTURA BÁSICA: Todos los componentes necesarios están presentes\n";
} else {
    echo "⚠️  ESTRUCTURA BÁSICA: Faltan algunos componentes importantes\n";
}

echo "\n🎯 PRÓXIMOS PASOS:\n";
echo "1. Ejecutar el debugging manual en el navegador\n";
echo "2. Verificar que los inputs se crean correctamente\n";
echo "3. Comprobar que los datos se envían al backend\n";
echo "4. Revisar logs del servidor para confirmar recepción\n";

echo "\nFin del análisis: " . date('Y-m-d H:i:s') . "\n";
