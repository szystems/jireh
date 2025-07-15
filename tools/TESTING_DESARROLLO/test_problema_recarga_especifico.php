<?php
/**
 * Script para probar específicamente el problema de recarga del formulario
 * 
 * Este script simula el envío del formulario para detectar problemas
 */

echo "=== PRUEBA ESPECÍFICA DEL PROBLEMA DE RECARGA ===\n\n";

// 1. Verificar la ruta específica
echo "1. VERIFICANDO RUTA DEL FORMULARIO:\n";
$rutasWeb = file_get_contents('routes/web.php');

if (strpos($rutasWeb, 'update-venta') !== false) {
    echo "✅ Ruta 'update-venta' encontrada\n";
    
    // Extraer la definición de la ruta
    $lineas = explode("\n", $rutasWeb);
    foreach ($lineas as $linea) {
        if (strpos($linea, 'update-venta') !== false) {
            echo "   Definición: " . trim($linea) . "\n";
        }
    }
} else {
    echo "❌ Ruta 'update-venta' NO encontrada\n";
}

echo "\n2. VERIFICANDO MIDDLEWARE Y CONFIGURACIÓN:\n";

// 2. Verificar middleware
if (strpos($rutasWeb, 'auth') !== false) {
    echo "✅ Middleware de autenticación detectado\n";
} else {
    echo "⚠️  No se detectó middleware de autenticación\n";
}

echo "\n3. VERIFICANDO POSIBLES PROBLEMAS EN VALIDACIONES:\n";

// 3. Verificar el FormRequest
$formRequest = 'app/Http/Requests/VentaEditFormRequest.php';
if (file_exists($formRequest)) {
    echo "✅ FormRequest encontrado\n";
    
    $contenidoRequest = file_get_contents($formRequest);
    
    // Verificar validaciones complejas
    if (strpos($contenidoRequest, 'withValidator') !== false) {
        echo "⚠️  Validaciones personalizadas detectadas (pueden causar problemas silenciosos)\n";
    }
    
    if (strpos($contenidoRequest, 'validateDetalles') !== false) {
        echo "⚠️  Validación custom de detalles (posible causa del problema)\n";
    }
    
    // Verificar si authorize retorna true
    if (strpos($contenidoRequest, 'return true') !== false) {
        echo "✅ Autorización habilitada\n";
    } else {
        echo "❌ PROBLEMA POTENCIAL: Autorización puede estar bloqueando\n";
    }
} else {
    echo "❌ FormRequest NO encontrado\n";
}

echo "\n4. ANÁLISIS DE POSIBLES CAUSAS:\n";

$posiblesCausas = [
    "🔍 CAUSA 1: VALIDACIONES COMPLEJAS",
    "   - El FormRequest tiene validaciones personalizadas que pueden fallar silenciosamente",
    "   - La validación de detalles es compleja y puede no encontrar detalles válidos",
    "   - Solución: Simplificar validaciones o agregar más logging",
    "",
    "🔍 CAUSA 2: PROBLEMAS CON TOKEN CSRF",
    "   - El token CSRF puede estar expirando entre la carga y el envío",
    "   - Solución: Verificar que el token se genera correctamente",
    "",
    "🔍 CAUSA 3: PROBLEMAS CON EL METHOD SPOOFING",
    "   - Laravel necesita @method('PUT') para convertir POST a PUT",
    "   - Si no está presente, puede causar problemas de routing",
    "   - Solución: Verificar que @method('PUT') está en el formulario",
    "",
    "🔍 CAUSA 4: ERRORES JAVASCRIPT QUE PREVIENEN ENVÍO",
    "   - Algún error JavaScript puede estar cancelando el envío",
    "   - Los eventos preventDefault() pueden activarse incorrectamente",
    "   - Solución: Revisar la consola del navegador",
    "",
    "🔍 CAUSA 5: PROBLEMAS DE SESSION/AUTENTICACIÓN",
    "   - La sesión puede estar expirando",
    "   - Middleware de autenticación puede estar bloqueando",
    "   - Solución: Verificar logs de autenticación"
];

foreach ($posiblesCausas as $causa) {
    echo "$causa\n";
}

echo "\n5. PRUEBA MANUAL RECOMENDADA:\n";
echo "Ejecute estos pasos en el navegador:\n\n";

echo "PASO 1: Abrir DevTools (F12)\n";
echo "PASO 2: Ir a Console y ejecutar:\n";
echo "   window.debugFormulario()\n\n";

echo "PASO 3: Intentar enviar el formulario\n";
echo "PASO 4: En Network tab, verificar:\n";
echo "   - ¿Se envía la petición HTTP?\n";
echo "   - ¿Cuál es el status code de respuesta?\n";
echo "   - ¿Hay redirección?\n\n";

echo "PASO 5: En Console, verificar:\n";
echo "   - ¿Hay errores JavaScript?\n";
echo "   - ¿Se ejecutan los logs del evento submit?\n";
echo "   - ¿Se muestra 'FORMULARIO VÁLIDO - PROCEDIENDO CON ENVÍO'?\n\n";

echo "6. SOLUCIÓN TEMPORAL PARA DEBUGGING:\n";
echo "Si el problema persiste, puede agregar temporalmente al inicio del método update():\n";
echo "Log::info('LLEGÓ AL CONTROLADOR UPDATE', ['request_all' => \$request->all()]);\n\n";

echo "7. VERIFICACIÓN DE LOGS:\n";
echo "Revisar los logs en storage/logs/laravel.log para:\n";
echo "   - Errores de validación\n";
echo "   - Errores de base de datos\n";
echo "   - Mensajes de debug del controlador\n\n";

echo "=== FIN DE LA PRUEBA ===\n";
?>
