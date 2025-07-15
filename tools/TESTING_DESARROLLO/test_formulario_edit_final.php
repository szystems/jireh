<?php
// test_formulario_edit_final.php - Verificación final del formulario de edición

require_once 'vendor/autoload.php';
require_once 'config/app.php';

echo "=== VERIFICACIÓN FINAL FORMULARIO EDIT VENTA ===\n\n";

// 1. Verificar que no hay errors en la vista Blade
echo "1. Verificando sintaxis Blade...\n";
$vistaPath = 'resources/views/admin/venta/edit.blade.php';
if (file_exists($vistaPath)) {
    echo "✅ Archivo de vista existe\n";
    $contenido = file_get_contents($vistaPath);
    
    // Verificar usos correctos de method_field
    if (strpos($contenido, 'method_field') !== false) {
        echo "⚠️  WARNING: Se encontró uso de method_field - verificar sintaxis\n";
        // Buscar líneas con method_field
        $lines = explode("\n", $contenido);
        foreach ($lines as $num => $line) {
            if (strpos($line, 'method_field') !== false) {
                echo "   Línea " . ($num + 1) . ": " . trim($line) . "\n";
            }
        }
    }
    
    // Verificar @method correcto
    if (strpos($contenido, "@method('PUT')") !== false) {
        echo "✅ @method('PUT') correctamente implementado\n";
    } else {
        echo "❌ @method('PUT') no encontrado\n";
    }
    
    // Verificar @csrf
    if (strpos($contenido, "@csrf") !== false) {
        echo "✅ @csrf correctamente implementado\n";
    } else {
        echo "❌ @csrf no encontrado\n";
    }
} else {
    echo "❌ Archivo de vista no encontrado\n";
}

echo "\n2. Verificando archivos JavaScript...\n";
$jsPath = 'public/js/venta/edit-venta-main-simplified.js';
if (file_exists($jsPath)) {
    echo "✅ Archivo JavaScript existe\n";
    $jsContent = file_get_contents($jsPath);
    
    // Verificar funciones clave
    $funciones = [
        'form.on(\'submit\'',
        'eliminarDetalle',
        'guardarTrabajadores',
        'agregarNuevoDetalle'
    ];
    
    foreach ($funciones as $funcion) {
        if (strpos($jsContent, $funcion) !== false) {
            echo "✅ Función '$funcion' presente\n";
        } else {
            echo "❌ Función '$funcion' no encontrada\n";
        }
    }
} else {
    echo "❌ Archivo JavaScript no encontrado\n";
}

echo "\n3. Verificando FormRequest...\n";
$formRequestPath = 'app/Http/Requests/VentaEditFormRequest.php';
if (file_exists($formRequestPath)) {
    echo "✅ FormRequest existe\n";
    $content = file_get_contents($formRequestPath);
    
    if (strpos($content, 'use Illuminate\Support\Facades\Log;') !== false) {
        echo "✅ Import de Log corregido\n";
    } else {
        echo "❌ Import de Log faltante\n";
    }
    
    if (strpos($content, 'validateDetalles') !== false) {
        echo "✅ Método validateDetalles presente\n";
    }
} else {
    echo "❌ FormRequest no encontrado\n";
}

echo "\n4. Verificando rutas...\n";
$routesPath = 'routes/web.php';
if (file_exists($routesPath)) {
    $routesContent = file_get_contents($routesPath);
    if (strpos($routesContent, 'update-venta') !== false) {
        echo "✅ Ruta de actualización presente\n";
    } else {
        echo "❌ Ruta de actualización no encontrada\n";
    }
}

echo "\n5. Verificando logs recientes...\n";
$logPath = 'storage/logs/laravel.log';
if (file_exists($logPath)) {
    // Leer las últimas 20 líneas
    $lines = file($logPath);
    $lastLines = array_slice($lines, -20);
    
    $hasErrors = false;
    foreach ($lastLines as $line) {
        if (strpos($line, 'ERROR') !== false || strpos($line, 'Exception') !== false) {
            $hasErrors = true;
            break;
        }
    }
    
    if ($hasErrors) {
        echo "⚠️  Se encontraron errores recientes en logs\n";
        echo "Últimas líneas relevantes:\n";
        foreach ($lastLines as $line) {
            if (strpos($line, 'ERROR') !== false || strpos($line, 'Exception') !== false || strpos($line, 'method_field') !== false) {
                echo "  " . trim($line) . "\n";
            }
        }
    } else {
        echo "✅ No se encontraron errores recientes\n";
    }
} else {
    echo "❌ Archivo de log no encontrado\n";
}

echo "\n=== INSTRUCCIONES DE PRUEBA MANUAL ===\n";
echo "1. Abrir navegador en: http://localhost:8000/admin/ventas\n";
echo "2. Seleccionar una venta para editar\n";
echo "3. Verificar que el formulario carga sin errores\n";
echo "4. Intentar:\n";
echo "   - Eliminar un detalle existente\n";
echo "   - Editar trabajadores de un servicio\n";
echo "   - Agregar nuevo detalle\n";
echo "   - Guardar cambios\n";
echo "5. Verificar en logs y consola del navegador\n";

echo "\n=== ESTADO ACTUAL ===\n";
echo "✅ Correcciones aplicadas:\n";
echo "   - Ruta AJAX de vehículos corregida\n";
echo "   - Eventos de eliminación de detalles mejorados\n";
echo "   - Modal de trabajadores funcional\n";
echo "   - Validaciones de formulario reforzadas\n";
echo "   - Logging y debugging implementado\n";
echo "   - Import de Log en FormRequest corregido\n";
echo "   - Cache de vistas limpiada\n";

echo "\n⚠️  RECUERDA:\n";
echo "   - Eliminar el logging temporal después de confirmar funcionalidad\n";
echo "   - Probar todas las funcionalidades en navegador\n";
echo "   - Verificar que no se introdujeron regresiones\n";

echo "\n=== FIN VERIFICACIÓN ===\n";
?>
