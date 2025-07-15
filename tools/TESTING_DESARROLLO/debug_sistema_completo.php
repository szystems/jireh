<?php
/**
 * Script de verificación completa del sistema para debugging del formulario edit venta
 * Ejecutar desde: php debug_sistema_completo.php
 */

echo "🔍 DEBUGGING COMPLETO DEL SISTEMA - FORMULARIO EDIT VENTA\n";
echo "=" . str_repeat("=", 60) . "\n\n";

// 1. Verificar archivos críticos
echo "📁 VERIFICACIÓN DE ARCHIVOS CRÍTICOS:\n";
echo "-" . str_repeat("-", 40) . "\n";

$archivos_criticos = [
    'Vista Edit' => 'resources/views/admin/venta/edit.blade.php',
    'Controlador' => 'app/Http/Controllers/Admin/VentaController.php',
    'FormRequest' => 'app/Http/Requests/VentaEditFormRequest.php',
    'Rutas Web' => 'routes/web.php',
    'JS Principal' => 'public/js/venta/edit-venta-main-simplified.js',
    'JS Debug' => 'public/js/debugging/form-debug-integrated.js'
];

foreach ($archivos_criticos as $nombre => $ruta) {
    $existe = file_exists($ruta);
    $tamaño = $existe ? filesize($ruta) : 0;
    $modificado = $existe ? date('Y-m-d H:i:s', filemtime($ruta)) : 'N/A';
    
    echo sprintf("%-15s: %s (Tamaño: %d bytes, Modificado: %s)\n", 
        $nombre, 
        $existe ? "✅ Existe" : "❌ No existe", 
        $tamaño, 
        $modificado
    );
}

echo "\n";

// 2. Verificar configuración de Laravel
echo "⚙️ VERIFICACIÓN DE CONFIGURACIÓN:\n";
echo "-" . str_repeat("-", 40) . "\n";

// Verificar que estamos en un proyecto Laravel
if (file_exists('artisan')) {
    echo "✅ Proyecto Laravel detectado\n";
    
    // Verificar .env
    if (file_exists('.env')) {
        echo "✅ Archivo .env existe\n";
        
        // Leer configuraciones importantes
        $env_content = file_get_contents('.env');
        preg_match('/APP_DEBUG=(.+)/', $env_content, $debug_match);
        preg_match('/APP_ENV=(.+)/', $env_content, $env_match);
        preg_match('/LOG_LEVEL=(.+)/', $env_content, $log_match);
        
        echo "📋 APP_DEBUG: " . (isset($debug_match[1]) ? trim($debug_match[1]) : 'No definido') . "\n";
        echo "📋 APP_ENV: " . (isset($env_match[1]) ? trim($env_match[1]) : 'No definido') . "\n";
        echo "📋 LOG_LEVEL: " . (isset($log_match[1]) ? trim($log_match[1]) : 'No definido') . "\n";
    } else {
        echo "❌ Archivo .env no existe\n";
    }
    
    // Verificar logs
    $log_path = 'storage/logs/laravel.log';
    if (file_exists($log_path)) {
        $log_size = filesize($log_path);
        $log_modified = date('Y-m-d H:i:s', filemtime($log_path));
        echo "📊 Log Laravel: Existe (Tamaño: {$log_size} bytes, Modificado: {$log_modified})\n";
        
        // Leer últimas líneas del log
        echo "📄 Últimas 5 líneas del log:\n";
        $log_lines = file($log_path);
        $last_lines = array_slice($log_lines, -5);
        foreach ($last_lines as $line) {
            echo "   " . trim($line) . "\n";
        }
    } else {
        echo "❌ Log Laravel no existe\n";
    }
} else {
    echo "❌ No es un proyecto Laravel (artisan no encontrado)\n";
}

echo "\n";

// 3. Verificar rutas específicas
echo "🛣️ VERIFICACIÓN DE RUTAS:\n";
echo "-" . str_repeat("-", 40) . "\n";

if (file_exists('routes/web.php')) {
    $routes_content = file_get_contents('routes/web.php');
    
    // Buscar rutas relacionadas con ventas
    $venta_routes = [];
    if (preg_match('/Route::.*update-venta/', $routes_content)) {
        echo "✅ Ruta update-venta encontrada\n";
    } else {
        echo "❌ Ruta update-venta NO encontrada\n";
    }
    
    if (preg_match('/Route::.*edit-venta/', $routes_content)) {
        echo "✅ Ruta edit-venta encontrada\n";
    } else {
        echo "❌ Ruta edit-venta NO encontrada\n";
    }
    
    // Verificar si hay middleware
    if (preg_match('/middleware.*auth/', $routes_content)) {
        echo "✅ Middleware auth detectado\n";
    }
    
    if (preg_match('/middleware.*csrf/', $routes_content)) {
        echo "✅ Middleware csrf detectado\n";
    }
} else {
    echo "❌ Archivo routes/web.php no encontrado\n";
}

echo "\n";

// 4. Verificar estructura del controlador
echo "🎮 VERIFICACIÓN DEL CONTROLADOR:\n";
echo "-" . str_repeat("-", 40) . "\n";

$controller_path = 'app/Http/Controllers/Admin/VentaController.php';
if (file_exists($controller_path)) {
    $controller_content = file_get_contents($controller_path);
    
    // Verificar métodos importantes
    $metodos = ['update', 'edit', 'show', 'store'];
    foreach ($metodos as $metodo) {
        if (preg_match("/function\s+{$metodo}\s*\(/", $controller_content)) {
            echo "✅ Método {$metodo}() encontrado\n";
        } else {
            echo "❌ Método {$metodo}() NO encontrado\n";
        }
    }
    
    // Verificar imports importantes
    $imports = ['Log', 'DB', 'Request'];
    foreach ($imports as $import) {
        if (preg_match("/use.*{$import}[;\s]/", $controller_content)) {
            echo "✅ Import {$import} encontrado\n";
        } else {
            echo "⚠️ Import {$import} NO encontrado\n";
        }
    }
    
    // Verificar logging en método update
    if (preg_match('/Log::info.*update/i', $controller_content)) {
        echo "✅ Logging en método update encontrado\n";
    } else {
        echo "⚠️ No se detectó logging en método update\n";
    }
} else {
    echo "❌ VentaController no encontrado\n";
}

echo "\n";

// 5. Verificar FormRequest
echo "📋 VERIFICACIÓN DEL FORM REQUEST:\n";
echo "-" . str_repeat("-", 40) . "\n";

$form_request_path = 'app/Http/Requests/VentaEditFormRequest.php';
if (file_exists($form_request_path)) {
    $form_request_content = file_get_contents($form_request_path);
    
    // Verificar métodos importantes
    if (preg_match('/function\s+rules\s*\(/', $form_request_content)) {
        echo "✅ Método rules() encontrado\n";
    } else {
        echo "❌ Método rules() NO encontrado\n";
    }
    
    if (preg_match('/function\s+authorize\s*\(/', $form_request_content)) {
        echo "✅ Método authorize() encontrado\n";
    } else {
        echo "❌ Método authorize() NO encontrado\n";
    }
    
    // Verificar logging
    if (preg_match('/Log::info/i', $form_request_content)) {
        echo "✅ Logging en FormRequest encontrado\n";
    } else {
        echo "⚠️ No se detectó logging en FormRequest\n";
    }
    
    // Verificar validaciones importantes
    $validaciones = ['cliente_id', 'vehiculo_id', 'fecha', 'detalles'];
    foreach ($validaciones as $campo) {
        if (preg_match("/{$campo}.*required/i", $form_request_content)) {
            echo "✅ Validación para {$campo} encontrada\n";
        } else {
            echo "⚠️ Validación para {$campo} NO encontrada\n";
        }
    }
} else {
    echo "❌ VentaEditFormRequest no encontrado\n";
}

echo "\n";

// 6. Verificar JavaScript
echo "🔧 VERIFICACIÓN DE JAVASCRIPT:\n";
echo "-" . str_repeat("-", 40) . "\n";

$js_files = [
    'public/js/venta/edit-venta-main-simplified.js',
    'public/js/debugging/form-debug-integrated.js'
];

foreach ($js_files as $js_file) {
    if (file_exists($js_file)) {
        $js_content = file_get_contents($js_file);
        $lines = count(file($js_file));
        echo "✅ " . basename($js_file) . " encontrado ({$lines} líneas)\n";
        
        // Verificar elementos importantes en JS
        if (strpos($js_content, 'forma-editar-venta') !== false) {
            echo "   ✅ Referencia a formulario encontrada\n";
        } else {
            echo "   ❌ Referencia a formulario NO encontrada\n";
        }
        
        if (strpos($js_content, 'submit') !== false) {
            echo "   ✅ Manejo de submit encontrado\n";
        } else {
            echo "   ❌ Manejo de submit NO encontrado\n";
        }
    } else {
        echo "❌ " . basename($js_file) . " NO encontrado\n";
    }
}

echo "\n";

// 7. Recomendaciones basadas en los hallazgos
echo "💡 RECOMENDACIONES:\n";
echo "-" . str_repeat("-", 40) . "\n";

echo "1. Ejecuta el debugging-simplificado.html en tu navegador\n";
echo "2. Usa los tests 1-4 para identificar exactamente dónde falla\n";
echo "3. Revisa el log de Laravel en tiempo real:\n";
echo "   tail -f storage/logs/laravel.log\n";
echo "4. Si el formulario sigue sin funcionar, ejecuta:\n";
echo "   php artisan route:list | grep venta\n";
echo "5. Limpia la cache si es necesario:\n";
echo "   php artisan view:clear\n";
echo "   php artisan route:clear\n";
echo "   php artisan config:clear\n";

echo "\n";

echo "✅ VERIFICACIÓN COMPLETA TERMINADA\n";
echo "📞 Comparte estos resultados para continuar con el debugging\n";
echo "=" . str_repeat("=", 60) . "\n";
?>
