<?php
/**
 * Script de verificación para las correcciones del formulario de edición de ventas
 * 
 * Verifica:
 * 1. Botón de eliminar detalle existente
 * 2. Modal de trabajadores 
 * 3. Envío del formulario
 */

echo "=== VERIFICACIÓN DE CORRECCIONES EDIT VENTA ===\n\n";

$archivos_verificar = [
    'resources/views/admin/venta/edit.blade.php',
    'public/js/venta/edit-venta-main-simplified.js'
];

foreach ($archivos_verificar as $archivo) {
    if (file_exists($archivo)) {
        echo "✅ Archivo encontrado: $archivo\n";
    } else {
        echo "❌ Archivo NO encontrado: $archivo\n";
    }
}

echo "\n=== VERIFICACIÓN DE CORRECCIONES EN edit.blade.php ===\n";

$contenido_edit = file_get_contents('resources/views/admin/venta/edit.blade.php');

// Verificar corrección del botón eliminar
$patrones_verificar = [
    'eliminarDetalleExistente' => 'Función eliminarDetalleExistente implementada',
    'e.preventDefault()' => 'Prevención de eventos por defecto',
    'e.stopPropagation()' => 'Detención de propagación de eventos',
    'detalle-oculto-por-eliminacion' => 'Clase para detalles eliminados',
    'fadeOut(300' => 'Animación de eliminación',
    'SweetAlert' => 'Integración con SweetAlert',
    'confirmar eliminación' => 'Confirmación de eliminación',
    'Inputs de trabajadores creados' => 'Debug de inputs de trabajadores',
    'Container de trabajadores limpiado' => 'Limpieza de container',
    'trabajadores-carwash-edit' => 'Select de trabajadores del modal',
    '=== INICIANDO ENVÍO DEL FORMULARIO ===' => 'Debug mejorado del formulario',
    'evitar envíos duplicados' => 'Prevención de envíos duplicados',
    'timeout de seguridad' => 'Timeout de seguridad',
    '@if (count($errors) > 0)' => 'Manejo de errores de validación'
];

foreach ($patrones_verificar as $patron => $descripcion) {
    if (strpos($contenido_edit, $patron) !== false) {
        echo "✅ $descripcion\n";
    } else {
        echo "❌ FALTA: $descripcion\n";
    }
}

echo "\n=== VERIFICACIÓN DE ELEMENTOS CRÍTICOS ===\n";

// Verificar elementos críticos del formulario
$elementos_criticos = [
    'eliminar-detalle' => 'Clase de botones eliminar',
    'editar-trabajadores' => 'Clase de botones editar trabajadores',
    'editar-trabajadores-modal' => 'ID del modal de trabajadores',
    'guardar-trabajadores' => 'ID del botón guardar trabajadores',
    'forma-editar-venta' => 'ID del formulario principal',
    'btn-guardar-cambios' => 'ID del botón guardar cambios'
];

foreach ($elementos_criticos as $elemento => $descripcion) {
    $count = substr_count($contenido_edit, $elemento);
    if ($count > 0) {
        echo "✅ $descripcion (encontrado $count veces)\n";
    } else {
        echo "❌ FALTA: $descripcion\n";
    }
}

echo "\n=== VERIFICACIÓN DE INPUTS HIDDEN ===\n";

// Verificar inputs hidden críticos
$inputs_hidden = [
    'name="detalles[{{ $detalle->id }}][eliminar]"' => 'Input de eliminación de detalles',
    'name="trabajadores_carwash[{{ $detalle->id }}][]"' => 'Input de trabajadores existentes',
    'name="trabajadores_carwash[${detalleActualEditando}][]"' => 'Input de trabajadores en modal'
];

foreach ($inputs_hidden as $input => $descripcion) {
    if (strpos($contenido_edit, $input) !== false) {
        echo "✅ $descripcion\n";
    } else {
        echo "❌ FALTA: $descripcion\n";
    }
}

echo "\n=== VERIFICACIÓN DE EVENTOS JAVASCRIPT ===\n";

// Verificar eventos JavaScript
$eventos_js = [
    "$(document).on('click', '.eliminar-detalle'" => 'Evento eliminar detalle',
    "$(document).on('click', '.editar-trabajadores'" => 'Evento editar trabajadores',
    "$('#guardar-trabajadores').on('click'" => 'Evento guardar trabajadores',
    "$('#forma-editar-venta').on('submit'" => 'Evento envío formulario',
    "select2:select" => 'Eventos Select2'
];

foreach ($eventos_js as $evento => $descripcion) {
    if (strpos($contenido_edit, $evento) !== false) {
        echo "✅ $descripcion\n";
    } else {
        echo "❌ FALTA: $descripcion\n";
    }
}

echo "\n=== RESUMEN DE VERIFICACIÓN ===\n";

$errores_encontrados = 0;
foreach ($patrones_verificar as $patron => $descripcion) {
    if (strpos($contenido_edit, $patron) === false) {
        $errores_encontrados++;
    }
}

foreach ($elementos_criticos as $elemento => $descripcion) {
    if (strpos($contenido_edit, $elemento) === false) {
        $errores_encontrados++;
    }
}

foreach ($eventos_js as $evento => $descripcion) {
    if (strpos($contenido_edit, $evento) === false) {
        $errores_encontrados++;
    }
}

if ($errores_encontrados == 0) {
    echo "✅ TODAS LAS CORRECCIONES ESTÁN IMPLEMENTADAS CORRECTAMENTE\n";
    echo "✅ El formulario de edición debería funcionar correctamente\n";
} else {
    echo "❌ SE ENCONTRARON $errores_encontrados ELEMENTOS FALTANTES\n";
    echo "❌ Revise las correcciones antes de probar\n";
}

echo "\n=== INSTRUCCIONES DE PRUEBA ===\n";
echo "1. Abra una venta para editar en el navegador\n";
echo "2. Verifique en la consola del navegador (F12) que aparezcan los mensajes de debug\n";
echo "3. Pruebe eliminar un detalle existente - debe mostrar confirmación y ocultar la fila\n";
echo "4. Pruebe editar trabajadores de un servicio - debe abrir el modal y guardar cambios\n";
echo "5. Pruebe enviar el formulario - debe validar y procesar correctamente\n";
echo "6. Verifique que no hay errores de JavaScript en la consola\n";

echo "\n=== FIN DE VERIFICACIÓN ===\n";
?>
