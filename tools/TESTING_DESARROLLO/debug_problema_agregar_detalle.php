<?php
/**
 * Script de debugging para el formulario de agregar nuevo detalle
 * Este script verifica la estructura del código y identifica posibles problemas
 */

echo "🔧 DEBUGGING: Formulario Agregar Nuevo Detalle\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Ruta del archivo edit.blade.php
$archivoEdit = __DIR__ . '/resources/views/admin/venta/edit.blade.php';

if (!file_exists($archivoEdit)) {
    echo "❌ ERROR: No se encuentra el archivo edit.blade.php\n";
    echo "Ruta buscada: $archivoEdit\n";
    exit(1);
}

$contenido = file_get_contents($archivoEdit);

echo "✅ Archivo encontrado: edit.blade.php\n";
echo "📊 Tamaño del archivo: " . number_format(strlen($contenido)) . " caracteres\n\n";

// 1. Verificar elementos HTML importantes
echo "1️⃣ VERIFICACIÓN DE ELEMENTOS HTML\n";
echo str_repeat("-", 40) . "\n";

$elementosImportantes = [
    'Botón agregar detalle' => 'id="agregar-detalle"',
    'Select artículo' => 'id="articulo"',
    'Input cantidad' => 'id="cantidad-nuevo"',
    'Select descuento' => 'id="descuento-nuevo"',
    'Select trabajadores' => 'id="trabajadores-carwash-nuevo"',
    'Container trabajadores' => 'id="trabajadores-carwash-container"',
    'Tbody nuevos detalles' => 'id="nuevos-detalles"',
    'Container nuevos detalles' => 'id="nuevos-detalles-container"'
];

foreach ($elementosImportantes as $nombre => $patron) {
    $encontrado = strpos($contenido, $patron) !== false;
    echo ($encontrado ? "✅" : "❌") . " $nombre: " . ($encontrado ? "Encontrado" : "NO ENCONTRADO") . "\n";
    
    if ($encontrado) {
        // Contar ocurrencias
        $ocurrencias = substr_count($contenido, $patron);
        if ($ocurrencias > 1) {
            echo "   ⚠️  Advertencia: $ocurrencias ocurrencias encontradas\n";
        }
    }
}

echo "\n";

// 2. Verificar eventos JavaScript
echo "2️⃣ VERIFICACIÓN DE EVENTOS JAVASCRIPT\n";
echo str_repeat("-", 40) . "\n";

$eventosJS = [
    'Click agregar detalle' => "'#agregar-detalle').on('click'",
    'Selección artículo' => "'#articulo').on('select2:select'",
    'Eliminar nuevo detalle' => "eliminar-nuevo-detalle",
    'Inicialización Select2 artículo' => "'#articulo').select2("
];

foreach ($eventosJS as $nombre => $patron) {
    $encontrado = strpos($contenido, $patron) !== false;
    echo ($encontrado ? "✅" : "❌") . " $nombre: " . ($encontrado ? "Encontrado" : "NO ENCONTRADO") . "\n";
}

echo "\n";

// 3. Verificar estructura del JavaScript del evento click
echo "3️⃣ ANÁLISIS DEL EVENTO CLICK\n";
echo str_repeat("-", 40) . "\n";

if (strpos($contenido, "'#agregar-detalle').on('click'") !== false) {
    echo "✅ Evento click encontrado\n";
    
    // Buscar el patrón del evento completo
    $patronEvento = "/'#agregar-detalle'\.on\('click'.*?}\);/s";
    if (preg_match($patronEvento, $contenido, $matches)) {
        $eventoCompleto = $matches[0];
        $lineasEvento = count(explode("\n", $eventoCompleto));
        echo "📏 Líneas del evento: $lineasEvento\n";
        
        // Verificar elementos clave dentro del evento
        $verificaciones = [
            'Obtención articuloId' => 'articuloId = $(\'#articulo\').val()',
            'Obtención cantidad' => 'cantidad = parseFloat($(\'#cantidad-nuevo\').val())',
            'Validación artículo' => 'if (!articuloId)',
            'Validación cantidad' => 'if (!cantidad',
            'Creación nueva fila' => 'nuevaFila = `',
            'Append a tabla' => '$(\'#nuevos-detalles\').append(',
            'Limpiar formulario' => '$(\'#articulo\').val(\'\').trigger(\'change\')'
        ];
        
        foreach ($verificaciones as $nombre => $buscar) {
            $encontrado = strpos($eventoCompleto, $buscar) !== false;
            echo ($encontrado ? "  ✅" : "  ❌") . " $nombre\n";
        }
    } else {
        echo "⚠️  No se pudo extraer el evento completo\n";
    }
} else {
    echo "❌ Evento click NO encontrado\n";
}

echo "\n";

// 4. Verificar inicialización de Select2
echo "4️⃣ VERIFICACIÓN DE SELECT2\n";
echo str_repeat("-", 40) . "\n";

$select2Checks = [
    'Inicialización general' => "('.select2').select2(",
    'Inicialización artículo' => "'#articulo').select2(",
    'Clase select2-no-auto' => 'class="form-control select2-no-auto"'
];

foreach ($select2Checks as $nombre => $patron) {
    $encontrado = strpos($contenido, $patron) !== false;
    echo ($encontrado ? "✅" : "❌") . " $nombre: " . ($encontrado ? "Encontrado" : "NO ENCONTRADO") . "\n";
}

echo "\n";

// 5. Verificar manejo de errores
echo "5️⃣ VERIFICACIÓN DE MANEJO DE ERRORES\n";
echo str_repeat("-", 40) . "\n";

$manejoErrores = [
    'SweetAlert error' => "Swal.fire('Error'",
    'Console.log debug' => 'console.log(',
    'Console.error' => 'console.error(',
    'Try-catch' => 'try {'
];

foreach ($manejoErrores as $nombre => $patron) {
    $ocurrencias = substr_count($contenido, $patron);
    echo ($ocurrencias > 0 ? "✅" : "❌") . " $nombre: $ocurrencias ocurrencia(s)\n";
}

echo "\n";

// 6. Buscar posibles problemas comunes
echo "6️⃣ BÚSQUEDA DE PROBLEMAS COMUNES\n";
echo str_repeat("-", 40) . "\n";

$problemasComunes = [
    'Comillas no cerradas' => function($contenido) {
        $lineas = explode("\n", $contenido);
        $problemas = 0;
        foreach ($lineas as $numLinea => $linea) {
            $conteoComillasSimples = substr_count($linea, "'") - substr_count($linea, "\\'");
            $conteoComillasDobles = substr_count($linea, '"') - substr_count($linea, '\\"');
            
            if ($conteoComillasSimples % 2 !== 0 || $conteoComillasDobles % 2 !== 0) {
                if (strpos($linea, 'console.log') !== false || strpos($linea, 'agregar-detalle') !== false) {
                    $problemas++;
                    echo "    Línea " . ($numLinea + 1) . ": $linea\n";
                }
            }
        }
        return $problemas;
    },
    
    'Funciones sin cerrar' => function($contenido) {
        $aperturas = substr_count($contenido, 'function(');
        $cierresFunction = substr_count($contenido, '});');
        $cierresGeneral = substr_count($contenido, '}');
        
        echo "    Aperturas 'function(': $aperturas\n";
        echo "    Cierres '});': $cierresFunction\n";
        echo "    Total cierres '}': $cierresGeneral\n";
        
        return abs($aperturas - $cierresFunction);
    }
];

foreach ($problemasComunes as $nombre => $verificador) {
    echo "🔍 $nombre:\n";
    $resultado = $verificador($contenido);
    if ($resultado > 0) {
        echo "  ⚠️  Posibles problemas encontrados: $resultado\n";
    } else {
        echo "  ✅ Sin problemas detectados\n";
    }
}

echo "\n";

// 7. Resumen y recomendaciones
echo "7️⃣ RESUMEN Y RECOMENDACIONES\n";
echo str_repeat("-", 40) . "\n";

echo "📋 ACCIONES RECOMENDADAS:\n\n";

echo "1. Abrir la página de edición de venta en el navegador\n";
echo "2. Abrir las herramientas de desarrollador (F12)\n";
echo "3. Ir a la pestaña Console\n";
echo "4. Ejecutar estos comandos para debugging:\n\n";

echo "   // Verificar si el botón existe y tiene eventos\n";
echo "   console.log('Botón existe:', \$('#agregar-detalle').length);\n";
echo "   console.log('Eventos:', \$._data(\$('#agregar-detalle')[0], 'events'));\n\n";

echo "   // Verificar si Select2 está inicializado\n";
echo "   console.log('Select2 artículo:', \$('#articulo').hasClass('select2-hidden-accessible'));\n\n";

echo "   // Simular selección y click\n";
echo "   \$('#articulo').val(\$('#articulo option:first').val()).trigger('change');\n";
echo "   \$('#cantidad-nuevo').val('1');\n";
echo "   \$('#agregar-detalle').click();\n\n";

echo "5. Verificar la consola para ver qué mensajes aparecen\n";
echo "6. Si hay errores JavaScript, copiarlos y reportarlos\n\n";

echo "🎯 ARCHIVO DE DEBUG CREADO:\n";
echo "   debug_formulario_agregar_detalle.html\n";
echo "   Abrir este archivo para ver scripts de debugging adicionales\n\n";

echo "✅ Debugging completado\n";
echo "=" . str_repeat("=", 50) . "\n";
?>
