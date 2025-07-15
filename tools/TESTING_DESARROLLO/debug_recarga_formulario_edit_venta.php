<?php
/**
 * Script de debugging para el problema de recarga de página en edit venta
 * 
 * Analiza posibles causas del problema intermitente donde:
 * - A veces guarda correctamente
 * - A veces solo recarga sin guardar
 */

echo "=== ANÁLISIS DEL PROBLEMA DE RECARGA EN EDIT VENTA ===\n\n";

// 1. Verificar el archivo de vista
$archivo_vista = 'resources/views/admin/venta/edit.blade.php';
if (!file_exists($archivo_vista)) {
    echo "❌ ERROR: No se encuentra el archivo $archivo_vista\n";
    exit;
}

$contenido = file_get_contents($archivo_vista);

echo "=== VERIFICACIÓN DE ELEMENTOS CRÍTICOS DEL FORMULARIO ===\n";

// Verificar elementos críticos del formulario
$elementos_criticos = [
    'method="POST"' => 'Método POST del formulario',
    '@csrf' => 'Token CSRF',
    '@method(\'PUT\')' => 'Method spoofing para PUT',
    'id="forma-editar-venta"' => 'ID del formulario',
    'required' => 'Campos requeridos',
    'name="cliente_id"' => 'Campo cliente_id',
    'name="vehiculo_id"' => 'Campo vehiculo_id'
];

foreach ($elementos_criticos as $elemento => $descripcion) {
    $count = substr_count($contenido, $elemento);
    if ($count > 0) {
        echo "✅ $descripcion (encontrado $count veces)\n";
    } else {
        echo "❌ FALTA: $descripcion\n";
    }
}

echo "\n=== VERIFICACIÓN DE VALIDACIONES JAVASCRIPT ===\n";

// Verificar validaciones JavaScript que pueden causar el problema
$validaciones_js = [
    'e.preventDefault()' => 'Prevención de envío por defecto',
    'return false' => 'Retorno falso que bloquea envío',
    'prop(\'disabled\', true)' => 'Deshabilitación del botón',
    'cliente_id\').val()' => 'Validación de cliente',
    'vehiculo_id\').val()' => 'Validación de vehículo',
    'select2(\'close\')' => 'Cierre de select2',
    'submit' => 'Evento submit'
];

foreach ($validaciones_js as $validacion => $descripcion) {
    $count = substr_count($contenido, $validacion);
    if ($count > 0) {
        echo "📝 $descripcion (encontrado $count veces)\n";
    } else {
        echo "⚠️  No encontrado: $descripcion\n";
    }
}

echo "\n=== POSIBLES CAUSAS DEL PROBLEMA ===\n";

// Analizar posibles causas
$posibles_causas = [];

// 1. Verificar si hay múltiples eventos submit
if (substr_count($contenido, '.on(\'submit\'') > 1) {
    $posibles_causas[] = "MÚLTIPLES EVENTOS SUBMIT - puede causar conflictos";
}

// 2. Verificar si hay validaciones que pueden fallar silenciosamente
if (strpos($contenido, '!clienteId') !== false && strpos($contenido, '!vehiculoId') !== false) {
    $posibles_causas[] = "VALIDACIONES ESTRICTAS - pueden fallar si los campos están undefined";
}

// 3. Verificar si hay problemas con select2
if (strpos($contenido, 'select2') !== false && strpos($contenido, 'hasClass(\'select2-hidden-accessible\')') !== false) {
    $posibles_causas[] = "VERIFICACIONES SELECT2 - pueden fallar si select2 no está completamente inicializado";
}

// 4. Verificar timeout de seguridad
if (strpos($contenido, 'setTimeout') !== false && strpos($contenido, '30000') !== false) {
    $posibles_causas[] = "TIMEOUT DE SEGURIDAD - puede interferir con el envío normal";
}

if (empty($posibles_causas)) {
    echo "✅ No se detectaron causas obvias en el código\n";
} else {
    foreach ($posibles_causas as $causa) {
        echo "⚠️  $causa\n";
    }
}

echo "\n=== VERIFICACIÓN DEL CONTROLADOR ===\n";

// Verificar el controlador
$archivo_controlador = 'app/Http/Controllers/Admin/VentaController.php';
if (file_exists($archivo_controlador)) {
    echo "✅ Controlador encontrado: $archivo_controlador\n";
    
    $contenido_controlador = file_get_contents($archivo_controlador);
    
    // Verificar método update
    if (strpos($contenido_controlador, 'function update') !== false) {
        echo "✅ Método update encontrado\n";
    } else {
        echo "❌ Método update NO encontrado\n";
    }
    
    // Verificar transacciones DB
    if (strpos($contenido_controlador, 'DB::beginTransaction') !== false) {
        echo "✅ Transacciones DB utilizadas\n";
    } else {
        echo "⚠️  No se usan transacciones DB\n";
    }
    
    // Verificar manejo de errores
    if (strpos($contenido_controlador, 'try {') !== false && strpos($contenido_controlador, 'catch') !== false) {
        echo "✅ Manejo de errores try-catch implementado\n";
    } else {
        echo "⚠️  No se detectó manejo de errores try-catch\n";
    }
    
} else {
    echo "❌ Controlador NO encontrado: $archivo_controlador\n";
}

echo "\n=== VERIFICACIÓN DE RUTAS ===\n";

// Verificar rutas
$archivo_web = 'routes/web.php';
if (file_exists($archivo_web)) {
    $contenido_rutas = file_get_contents($archivo_web);
    
    if (strpos($contenido_rutas, 'update-venta') !== false) {
        echo "✅ Ruta update-venta encontrada en web.php\n";
    } else {
        echo "⚠️  Ruta update-venta no encontrada en web.php\n";
    }
} else {
    echo "⚠️  Archivo de rutas web.php no encontrado\n";
}

echo "\n=== RECOMENDACIONES PARA DEBUGGING ===\n";

echo "1. 🔍 VERIFICAR EN EL NAVEGADOR:\n";
echo "   - Abrir DevTools (F12)\n";
echo "   - Ir a la pestaña Console\n";
echo "   - Intentar enviar el formulario y verificar:\n";
echo "     * ¿Aparecen errores JavaScript?\n";
echo "     * ¿Se ejecuta el evento submit?\n";
echo "     * ¿Se muestran los logs de debugging?\n\n";

echo "2. 🌐 VERIFICAR EN LA PESTAÑA NETWORK:\n";
echo "   - ¿Se envía la petición HTTP?\n";
echo "   - ¿Cuál es el código de respuesta?\n";
echo "   - ¿Qué datos se envían en el payload?\n";
echo "   - ¿La respuesta es un redirect o devuelve contenido?\n\n";

echo "3. 📋 VERIFICAR EN EL SERVIDOR:\n";
echo "   - Revisar logs de Laravel (storage/logs/)\n";
echo "   - Verificar si llegan las peticiones al controlador\n";
echo "   - Verificar si hay errores de validación\n\n";

echo "4. 🧪 PRUEBAS ESPECÍFICAS:\n";
echo "   - Probar con diferentes navegadores\n";
echo "   - Probar deshabilitando JavaScript temporalmente\n";
echo "   - Probar con diferentes datos de formulario\n\n";

echo "=== SCRIPT DE DEBUGGING MEJORADO ===\n";
echo "Se agregará logging detallado al formulario para capturar el problema...\n\n";

?>
