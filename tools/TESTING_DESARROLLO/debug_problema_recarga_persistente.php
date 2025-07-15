<?php
// debug_problema_recarga_persistente.php - Diagnóstico específico del problema de recarga

require_once 'vendor/autoload.php';

echo "=== DIAGNÓSTICO ESPECÍFICO - PROBLEMA DE RECARGA PERSISTENTE ===\n\n";

// 1. Verificar logs recientes del servidor
echo "1. 🔍 VERIFICANDO LOGS DEL SERVIDOR\n";
$logPath = 'storage/logs/laravel.log';
if (file_exists($logPath)) {
    $lines = file($logPath);
    $recentLines = array_slice($lines, -50); // Últimas 50 líneas
    
    $errorsFound = 0;
    $validationErrors = 0;
    $bladeErrors = 0;
    
    foreach ($recentLines as $line) {
        if (strpos($line, 'ERROR') !== false || strpos($line, 'Exception') !== false) {
            $errorsFound++;
            if (strpos($line, 'validation') !== false || strpos($line, 'Validation') !== false) {
                $validationErrors++;
            }
        }
        if (strpos($line, 'method_field') !== false || strpos($line, 'Blade') !== false) {
            $bladeErrors++;
        }
    }
    
    echo "   - Errores totales encontrados: $errorsFound\n";
    echo "   - Errores de validación: $validationErrors\n";
    echo "   - Errores de Blade: $bladeErrors\n";
    
    if ($errorsFound > 0) {
        echo "\n   📋 ERRORES RECIENTES:\n";
        foreach ($recentLines as $line) {
            if (strpos($line, 'ERROR') !== false || strpos($line, 'Exception') !== false || strpos($line, 'method_field') !== false) {
                echo "   " . trim($line) . "\n";
            }
        }
    } else {
        echo "   ✅ No se encontraron errores recientes en logs\n";
    }
} else {
    echo "   ❌ Archivo de log no encontrado\n";
}

echo "\n2. 🔍 VERIFICANDO RUTAS Y CONTROLADOR\n";

// Verificar ruta de actualización
$routesContent = file_get_contents('routes/web.php');
if (strpos($routesContent, 'update-venta') !== false) {
    echo "   ✅ Ruta 'update-venta' encontrada en web.php\n";
} else {
    echo "   ❌ Ruta 'update-venta' NO encontrada en web.php\n";
}

// Verificar controlador
$controllerPath = 'app/Http/Controllers/Admin/VentaController.php';
if (file_exists($controllerPath)) {
    $controllerContent = file_get_contents($controllerPath);
    if (strpos($controllerContent, 'public function update') !== false) {
        echo "   ✅ Método 'update' encontrado en VentaController\n";
    } else {
        echo "   ❌ Método 'update' NO encontrado en VentaController\n";
    }
    
    // Verificar si hay logging en el método update
    if (strpos($controllerContent, 'Log::') !== false) {
        echo "   ✅ Logging implementado en VentaController\n";
    } else {
        echo "   ⚠️  No hay logging en VentaController\n";
    }
} else {
    echo "   ❌ VentaController no encontrado\n";
}

echo "\n3. 🔍 VERIFICANDO FORMREQUEST\n";
$formRequestPath = 'app/Http/Requests/VentaEditFormRequest.php';
if (file_exists($formRequestPath)) {
    $content = file_get_contents($formRequestPath);
    
    if (strpos($content, 'use Illuminate\Support\Facades\Log;') !== false) {
        echo "   ✅ Import de Log presente\n";
    } else {
        echo "   ❌ Import de Log faltante\n";
    }
    
    if (strpos($content, 'Log::info') !== false || strpos($content, 'Log::error') !== false) {
        echo "   ✅ Logging activo en FormRequest\n";
    } else {
        echo "   ⚠️  No hay logging en FormRequest\n";
    }
} else {
    echo "   ❌ FormRequest no encontrado\n";
}

echo "\n4. 🔍 ANALIZANDO FORMULARIO EN BLADE\n";
$bladeContent = file_get_contents('resources/views/admin/venta/edit.blade.php');

// Verificar elementos críticos del formulario
$checks = [
    'id="forma-editar-venta"' => 'ID del formulario',
    'method="POST"' => 'Método POST',
    '@csrf' => 'Token CSRF',
    '@method(\'PUT\')' => 'Método PUT',
    'id="btn-guardar-cambios"' => 'Botón de guardar',
    'type="submit"' => 'Tipo submit del botón'
];

foreach ($checks as $pattern => $description) {
    if (strpos($bladeContent, $pattern) !== false) {
        echo "   ✅ $description: Presente\n";
    } else {
        echo "   ❌ $description: FALTANTE\n";
    }
}

// Verificar JavaScript de submit
if (strpos($bladeContent, '$(\'#forma-editar-venta\').on(\'submit\'') !== false) {
    echo "   ✅ Evento submit de JavaScript: Presente\n";
} else {
    echo "   ❌ Evento submit de JavaScript: FALTANTE\n";
}

echo "\n5. 💡 DIAGNÓSTICO AUTOMATIZADO\n";

// Análisis del problema más probable
$probableIssues = [];

if (strpos($bladeContent, 'e.preventDefault()') !== false) {
    $probableIssues[] = "JavaScript puede estar bloqueando el envío del formulario";
}

if (!strpos($routesContent, 'Route::put') && !strpos($routesContent, 'Route::patch')) {
    $probableIssues[] = "Puede faltar ruta PUT/PATCH para actualización";
}

if ($errorsFound > 0) {
    $probableIssues[] = "Hay errores en el servidor que pueden estar causando redirecciones";
}

if (empty($probableIssues)) {
    $probableIssues[] = "El problema puede estar en validaciones silenciosas o en el flujo del controlador";
}

echo "   🎯 POSIBLES CAUSAS IDENTIFICADAS:\n";
foreach ($probableIssues as $issue) {
    echo "   - $issue\n";
}

echo "\n6. 🔧 SOLUCIONES RECOMENDADAS\n";
echo "   1. Abrir herramientas de desarrollador en navegador (F12)\n";
echo "   2. Ir a pestaña 'Console' y verificar errores JavaScript\n";
echo "   3. Ir a pestaña 'Network' y intentar guardar cambios\n";
echo "   4. Verificar si la petición se envía y qué respuesta recibe\n";
echo "   5. Revisar logs del servidor en tiempo real:\n";
echo "      tail -f storage/logs/laravel.log\n\n";

echo "7. 🧪 SCRIPT DE PRUEBA DIRECTO\n";
echo "   Para verificar si el problema es del formulario o del backend,\n";
echo "   ejecuta este comando para probar la ruta directamente:\n\n";
echo "   curl -X PUT http://localhost:8000/update-venta/[ID_VENTA] \\\n";
echo "        -H \"Content-Type: application/x-www-form-urlencoded\" \\\n";
echo "        -d \"_token=[TOKEN]&fecha=2025-01-01&cliente_id=1&vehiculo_id=1&tipo_venta=Car Wash&estado_pago=pendiente\"\n\n";

echo "=== FIN DIAGNÓSTICO ===\n";
echo "⚠️  SIGUIENTE PASO: Verificar en navegador la pestaña Network y Console\n";
echo "    cuando presiones 'Guardar Cambios' para ver qué sucede exactamente.\n";
?>
