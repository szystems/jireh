<?php
// ==========================================
// DIAGNÓSTICO AVANZADO PARA ERROR ESPECÍFICO
// ==========================================

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h2>DIAGNÓSTICO AVANZADO - ERROR ESPECÍFICO</h2>";
echo "<hr>";

// 1. VERIFICAR CONFIGURACIÓN .ENV
echo "<h3>1. VERIFICACIÓN DE CONFIGURACIÓN .ENV</h3>";
$env_path = '../.env';
if (file_exists($env_path)) {
    $env_content = file_get_contents($env_path);
    echo "✓ Archivo .env encontrado<br>";
    
    // Verificar configuraciones críticas
    $configs = [
        'APP_KEY' => '/APP_KEY=(.*)/',
        'APP_DEBUG' => '/APP_DEBUG=(.*)/',
        'DB_CONNECTION' => '/DB_CONNECTION=(.*)/',
        'DB_HOST' => '/DB_HOST=(.*)/',
        'DB_DATABASE' => '/DB_DATABASE=(.*)/',
        'SESSION_DRIVER' => '/SESSION_DRIVER=(.*)/'
    ];
    
    foreach ($configs as $key => $pattern) {
        preg_match($pattern, $env_content, $matches);
        if ($matches) {
            $value = trim($matches[1]);
            echo "$key: <strong>$value</strong><br>";
        } else {
            echo "$key: <span style='color: red;'>NO CONFIGURADO</span><br>";
        }
    }
} else {
    echo "<span style='color: red;'>✗ Archivo .env NO encontrado</span><br>";
}

echo "<hr>";

// 2. PROBAR CARGA STEP BY STEP
echo "<h3>2. PRUEBA DE CARGA PASO A PASO</h3>";

try {
    echo "Paso 1: Cargando autoloader...<br>";
    if (!file_exists('../vendor/autoload.php')) {
        throw new Exception('vendor/autoload.php no encontrado');
    }
    require '../vendor/autoload.php';
    echo "✓ Autoloader cargado<br>";
    
    echo "Paso 2: Cargando bootstrap de Laravel...<br>";
    if (!file_exists('../bootstrap/app.php')) {
        throw new Exception('bootstrap/app.php no encontrado');
    }
    $app = require_once '../bootstrap/app.php';
    echo "✓ Bootstrap de Laravel cargado<br>";
    
    echo "Paso 3: Configurando variables de entorno...<br>";
    if (method_exists($app, 'loadEnvironmentFrom')) {
        $app->loadEnvironmentFrom('.env');
    }
    echo "✓ Variables de entorno cargadas<br>";
    
    echo "Paso 4: Resolviendo servicios básicos...<br>";
    $config = $app->make('config');
    echo "✓ Servicio de configuración resuelto<br>";
    
    echo "Paso 5: Verificando configuración de aplicación...<br>";
    $appKey = $config->get('app.key');
    if (empty($appKey)) {
        throw new Exception('APP_KEY no está configurada');
    }
    echo "✓ APP_KEY configurada correctamente<br>";
    
    echo "Paso 6: Verificando configuración de base de datos...<br>";
    $dbConfig = $config->get('database.connections.mysql');
    if (empty($dbConfig)) {
        throw new Exception('Configuración de base de datos MySQL no encontrada');
    }
    echo "✓ Configuración de base de datos encontrada<br>";
    
    echo "Paso 7: Probando conexión a base de datos...<br>";
    $db = $app->make('db');
    $connection = $db->connection();
    $pdo = $connection->getPdo();
    echo "✓ Conexión a base de datos exitosa<br>";
    
    echo "Paso 8: Resolviendo HTTP Kernel...<br>";
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
    echo "✓ HTTP Kernel resuelto<br>";
    
    echo "Paso 9: Creando request de prueba...<br>";
    $request = \Illuminate\Http\Request::create('/');
    echo "✓ Request creado<br>";
    
    echo "Paso 10: Procesando request...<br>";
    $response = $kernel->handle($request);
    echo "✓ Request procesado exitosamente<br>";
    echo "Código de respuesta: " . $response->getStatusCode() . "<br>";
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #ffeeee; padding: 10px; border: 1px solid red;'>";
    echo "<strong>ERROR DETECTADO:</strong><br>";
    echo "Mensaje: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
    echo "<br><strong>Stack Trace:</strong><br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
} catch (Error $e) {
    echo "<div style='color: red; background: #ffeeee; padding: 10px; border: 1px solid red;'>";
    echo "<strong>ERROR FATAL DETECTADO:</strong><br>";
    echo "Mensaje: " . $e->getMessage() . "<br>";
    echo "Archivo: " . $e->getFile() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
    echo "<br><strong>Stack Trace:</strong><br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}

echo "<hr>";

// 3. VERIFICAR LOGS DE ERROR
echo "<h3>3. VERIFICACIÓN DE LOGS</h3>";
$log_paths = [
    '../storage/logs/laravel.log',
    '../storage/logs/laravel-' . date('Y-m-d') . '.log'
];

foreach ($log_paths as $log_path) {
    if (file_exists($log_path) && is_readable($log_path)) {
        echo "✓ Log encontrado: $log_path<br>";
        $log_content = file_get_contents($log_path);
        $recent_errors = array_slice(explode("\n", $log_content), -20);
        echo "<strong>Últimos errores:</strong><br>";
        echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 200px; overflow-y: scroll;'>";
        echo htmlspecialchars(implode("\n", $recent_errors));
        echo "</pre>";
        break;
    }
}

echo "<hr>";

// 4. INFORMACIÓN ADICIONAL DEL SERVIDOR
echo "<h3>4. INFORMACIÓN DEL SERVIDOR</h3>";
echo "Tiempo de ejecución PHP: " . ini_get('max_execution_time') . " segundos<br>";
echo "Memoria límite: " . ini_get('memory_limit') . "<br>";
echo "Directorio actual: " . getcwd() . "<br>";
echo "Usuario del servidor: " . get_current_user() . "<br>";

echo "<hr>";
echo "<p><strong>Diagnóstico avanzado completado - " . date('Y-m-d H:i:s') . "</strong></p>";
?>
