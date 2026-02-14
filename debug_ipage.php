<?php
/**
 * Script de debugging para problemas de iPage
 * Subir este archivo a iPage y ejecutar para diagnóstico
 */

echo "=== DEBUGGING iPage vs Local ===\n";
echo "<pre>";

// 1. Configuración PHP
echo "\n1. CONFIGURACIÓN PHP:\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Max Post Size: " . ini_get('post_max_size') . "\n";
echo "Upload Max Filesize: " . ini_get('upload_max_filesize') . "\n";
echo "Max Input Vars: " . ini_get('max_input_vars') . "\n";
echo "Max Execution Time: " . ini_get('max_execution_time') . "\n";

// 2. Extensiones PHP necesarias
echo "\n2. EXTENSIONES PHP:\n";
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath'];
foreach ($required_extensions as $ext) {
    echo "Extension $ext: " . (extension_loaded($ext) ? '✅ Loaded' : '❌ Missing') . "\n";
}

// 3. Permisos de archivos
echo "\n3. PERMISOS DE ARCHIVOS:\n";
$paths_to_check = [
    'storage/logs' => 'storage/logs',
    'storage/framework/cache' => 'storage/framework/cache',
    'storage/framework/sessions' => 'storage/framework/sessions',
    'storage/framework/views' => 'storage/framework/views',
    'bootstrap/cache' => 'bootstrap/cache'
];

foreach ($paths_to_check as $path) {
    if (file_exists($path)) {
        echo "$path: " . (is_writable($path) ? '✅ Writable' : '❌ Not Writable') . "\n";
    } else {
        echo "$path: ❌ Not Found\n";
    }
}

// 4. Variables de entorno
echo "\n4. VARIABLES DE ENTORNO:\n";
echo "APP_ENV: " . (getenv('APP_ENV') ?: 'Not set') . "\n";
echo "APP_DEBUG: " . (getenv('APP_DEBUG') ?: 'Not set') . "\n";
echo "DB_CONNECTION: " . (getenv('DB_CONNECTION') ?: 'Not set') . "\n";
echo "APP_URL: " . (getenv('APP_URL') ?: 'Not set') . "\n";

// 5. Prueba de conexión a BD
echo "\n5. CONEXIÓN BASE DE DATOS:\n";
try {
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
        
        if (file_exists('bootstrap/app.php')) {
            $app = require_once 'bootstrap/app.php';
            $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
            $kernel->bootstrap();
            
            // Probar conexión
            \Illuminate\Support\Facades\DB::connection();
            echo "✅ Conexión a BD exitosa\n";
            
            // Probar query simple
            $result = \Illuminate\Support\Facades\DB::select('SELECT COUNT(*) as total FROM articulos');
            echo "✅ Query test exitosa - Artículos: " . $result[0]->total . "\n";
            
        } else {
            echo "❌ bootstrap/app.php no encontrado\n";
        }
    } else {
        echo "❌ vendor/autoload.php no encontrado\n";
    }
} catch (Exception $e) {
    echo "❌ Error BD: " . $e->getMessage() . "\n";
}

// 6. Log de errores recientes
echo "\n6. LOGS RECIENTES:\n";
$logPath = 'storage/logs/laravel.log';
if (file_exists($logPath)) {
    $logs = file_get_contents($logPath);
    $recentLogs = substr($logs, -2000); // Últimos 2000 caracteres
    echo "Últimos logs:\n" . $recentLogs . "\n";
} else {
    echo "❌ No se encontró archivo de logs\n";
}

echo "</pre>";
echo "\n=== FIN DEBUGGING ===";
?>