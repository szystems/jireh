<?php
/**
 * Script de diagnóstico: Verificar estado del servidor y Laravel
 * Instrucciones: Sube este archivo a public/ y visita: tudominio.com/diagnosis.php
 */

// Verificar que el usuario tenga permisos (opcional)
$password = 'Reparar2025!'; // Cambia esta contraseña
if (!isset($_GET['password']) || $_GET['password'] !== $password) {
    die('❌ Acceso denegado. Usa: ?password=' . $password);
}

echo '<h1>🔍 Diagnóstico del Servidor y Laravel</h1>';
echo '<pre>';

try {
    // Información básica del servidor
    echo "=== INFORMACIÓN DEL SERVIDOR ===\n";
    echo "📅 Fecha del servidor: " . date('Y-m-d H:i:s') . "\n";
    echo "🖥️ Sistema operativo: " . php_uname() . "\n";
    echo "🐘 Versión PHP: " . PHP_VERSION . "\n";
    echo "📁 Directorio actual: " . getcwd() . "\n";
    echo "👤 Usuario PHP: " . get_current_user() . "\n\n";
    
    // Cambiar al directorio raíz de Laravel (buscar hacia arriba hasta encontrar composer.json)
    $rootPath = dirname(__DIR__);
    $currentDir = getcwd();
    
    // Si estamos en un subdirectorio, buscar hacia arriba
    $searchDir = $currentDir;
    $maxLevels = 5; // Máximo 5 niveles hacia arriba
    $level = 0;
    
    while ($level < $maxLevels && !file_exists($searchDir . '/composer.json')) {
        $parentDir = dirname($searchDir);
        if ($parentDir === $searchDir) break; // Llegamos a la raíz del sistema
        $searchDir = $parentDir;
        $level++;
    }
    
    if (file_exists($searchDir . '/composer.json')) {
        $rootPath = $searchDir;
        echo "✅ Encontrado Laravel en: $rootPath\n";
    } else {
        echo "❌ No se encontró Laravel (composer.json) en los niveles superiores\n";
        echo "ℹ️ Usando directorio padre por defecto: $rootPath\n";
    }
    
    chdir($rootPath);
    
    // Verificar archivos críticos de Laravel
    echo "=== ARCHIVOS CRÍTICOS DE LARAVEL ===\n";
    $criticalFiles = [
        'composer.json' => 'Archivo de dependencias',
        'composer.lock' => 'Versiones bloqueadas',
        'vendor/autoload.php' => 'Autoloader de Composer',
        'bootstrap/app.php' => 'Bootstrap de Laravel',
        '.env' => 'Configuración de entorno',
        'artisan' => 'CLI de Laravel',
        'storage/logs/laravel.log' => 'Log de Laravel'
    ];
    
    foreach ($criticalFiles as $file => $description) {
        if (file_exists($file)) {
            $size = filesize($file);
            $modified = date('Y-m-d H:i:s', filemtime($file));
            echo "✅ $file - $description (${size} bytes, modificado: $modified)\n";
        } else {
            echo "❌ $file - FALTA - $description\n";
        }
    }
    
    // Verificar directorios de storage
    echo "\n=== DIRECTORIOS DE STORAGE ===\n";
    $storageDirs = [
        'storage/app' => 'Archivos de aplicación',
        'storage/framework/cache' => 'Cache del framework', 
        'storage/framework/sessions' => 'Sesiones de archivo',
        'storage/framework/views' => 'Vistas compiladas',
        'storage/logs' => 'Archivos de log',
        'bootstrap/cache' => 'Cache de bootstrap'
    ];
    
    foreach ($storageDirs as $dir => $description) {
        if (is_dir($dir)) {
            $fileCount = count(scandir($dir)) - 2; // -2 por . y ..
            $perms = substr(sprintf('%o', fileperms($dir)), -4);
            echo "✅ $dir - $description ($fileCount archivos, permisos: $perms)\n";
        } else {
            echo "❌ $dir - NO EXISTE - $description\n";
        }
    }
    
    // Verificar configuración de PHP
    echo "\n=== CONFIGURACIÓN DE PHP ===\n";
    $phpSettings = [
        'memory_limit' => 'Límite de memoria',
        'max_execution_time' => 'Tiempo máximo de ejecución',
        'upload_max_filesize' => 'Tamaño máximo de archivo',
        'post_max_size' => 'Tamaño máximo de POST',
        'session.save_handler' => 'Manejador de sesiones',
        'session.save_path' => 'Ruta de sesiones'
    ];
    
    foreach ($phpSettings as $setting => $description) {
        $value = ini_get($setting);
        echo "📋 $setting = '$value' - $description\n";
    }
    
    // Verificar extensiones necesarias
    echo "\n=== EXTENSIONES DE PHP ===\n";
    $requiredExtensions = [
        'pdo' => 'Base de datos PDO',
        'pdo_mysql' => 'MySQL PDO', 
        'mbstring' => 'Strings multibyte',
        'openssl' => 'Encriptación SSL',
        'json' => 'Manejo de JSON',
        'curl' => 'Cliente HTTP',
        'fileinfo' => 'Información de archivos',
        'tokenizer' => 'Tokenizer para Laravel'
    ];
    
    foreach ($requiredExtensions as $ext => $description) {
        if (extension_loaded($ext)) {
            echo "✅ $ext - $description\n";
        } else {
            echo "❌ $ext - FALTA - $description\n";
        }
    }
    
    // Verificar conexión a base de datos (si .env existe)
    echo "\n=== CONFIGURACIÓN DE BASE DE DATOS ===\n";
    if (file_exists('.env')) {
        $envContent = file_get_contents('.env');
        
        // Extraer configuración de BD
        preg_match('/DB_HOST=(.+)/', $envContent, $hostMatch);
        preg_match('/DB_DATABASE=(.+)/', $envContent, $dbMatch);
        preg_match('/DB_USERNAME=(.+)/', $envContent, $userMatch);
        
        $host = isset($hostMatch[1]) ? trim($hostMatch[1]) : 'No definido';
        $database = isset($dbMatch[1]) ? trim($dbMatch[1]) : 'No definido';
        $username = isset($userMatch[1]) ? trim($userMatch[1]) : 'No definido';
        
        echo "🏠 Host: $host\n";
        echo "📊 Base de datos: $database\n";
        echo "👤 Usuario: $username\n";
        
        // Intentar conexión (solo si tenemos los datos)
        if ($host !== 'No definido' && $database !== 'No definido') {
            try {
                preg_match('/DB_PASSWORD=(.*)/', $envContent, $passMatch);
                $password = isset($passMatch[1]) ? trim($passMatch[1]) : '';
                
                $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
                echo "✅ Conexión a base de datos exitosa\n";
            } catch (Exception $e) {
                echo "❌ Error de conexión: " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "❌ Archivo .env no encontrado\n";
    }
    
    echo "\n🎉 Diagnóstico completado.\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo '</pre>';
echo '<p><a href="fix-autoloader.php?password=jireh2025">🔧 Reparar Autoloader</a> | <a href="fix-cache.php?password=jireh2025">🧹 Limpiar Cache</a> | <a href="/">🏠 Inicio</a></p>';
?>
