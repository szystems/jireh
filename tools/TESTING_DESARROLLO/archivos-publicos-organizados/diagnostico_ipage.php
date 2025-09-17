<?php
// ==========================================
// DIAGNÓSTICO COMPLETO PARA IPAGE
// ==========================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>DIAGNÓSTICO JIREH - iPAGE</h2>";
echo "<hr>";

// 1. INFORMACIÓN DEL SERVIDOR
echo "<h3>1. INFORMACIÓN DEL SERVIDOR</h3>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "<hr>";

// 2. VERIFICAR ARCHIVOS CRÍTICOS
echo "<h3>2. VERIFICACIÓN DE ARCHIVOS</h3>";

$archivos_criticos = [
    '../vendor/autoload.php',
    '../bootstrap/app.php',
    '../.env',
    '../storage/framework/sessions',
    '../storage/logs',
    '../storage/app',
    '../config/app.php',
    '../config/database.php'
];

foreach ($archivos_criticos as $archivo) {
    $existe = file_exists($archivo);
    $legible = $existe ? is_readable($archivo) : false;
    $escribible = $existe ? is_writable($archivo) : false;
    
    echo "Archivo: $archivo<br>";
    echo "- Existe: " . ($existe ? "SÍ" : "NO") . "<br>";
    echo "- Legible: " . ($legible ? "SÍ" : "NO") . "<br>";
    echo "- Escribible: " . ($escribible ? "SÍ" : "NO") . "<br><br>";
}
echo "<hr>";

// 3. VERIFICAR PERMISOS DE STORAGE
echo "<h3>3. PERMISOS DE STORAGE</h3>";
$storage_dirs = [
    '../storage',
    '../storage/app',
    '../storage/framework',
    '../storage/framework/cache',
    '../storage/framework/sessions',
    '../storage/framework/views',
    '../storage/logs'
];

foreach ($storage_dirs as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        echo "Directorio: $dir - Permisos: $perms<br>";
    } else {
        echo "Directorio NO EXISTE: $dir<br>";
    }
}
echo "<hr>";

// 4. EXTENSIONES PHP REQUERIDAS
echo "<h3>4. EXTENSIONES PHP</h3>";
$extensiones = ['mysqli', 'pdo', 'pdo_mysql', 'json', 'mbstring', 'openssl', 'tokenizer', 'xml', 'curl'];

foreach ($extensiones as $ext) {
    echo "Extensión $ext: " . (extension_loaded($ext) ? "INSTALADA" : "NO INSTALADA") . "<br>";
}
echo "<hr>";

// 5. PROBAR CONEXIÓN A BASE DE DATOS
echo "<h3>5. CONEXIÓN A BASE DE DATOS</h3>";

// Leer configuración del .env
$env_path = '../.env';
if (file_exists($env_path)) {
    $env_content = file_get_contents($env_path);
    
    // Extraer configuración de DB
    preg_match('/DB_HOST=(.*)/', $env_content, $host_match);
    preg_match('/DB_DATABASE=(.*)/', $env_content, $db_match);
    preg_match('/DB_USERNAME=(.*)/', $env_content, $user_match);
    preg_match('/DB_PASSWORD=(.*)/', $env_content, $pass_match);
    
    if ($host_match && $db_match && $user_match && $pass_match) {
        $host = trim($host_match[1]);
        $database = trim($db_match[1]);
        $username = trim($user_match[1]);
        $password = trim($pass_match[1]);
        
        echo "Probando conexión a: $host<br>";
        echo "Base de datos: $database<br>";
        echo "Usuario: $username<br>";
        
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
            echo "<strong style='color: green;'>✓ CONEXIÓN EXITOSA</strong><br>";
            
            // Verificar versión de MySQL
            $version = $pdo->query('SELECT VERSION()')->fetchColumn();
            echo "Versión MySQL: $version<br>";
            
            // Verificar tabla de sesiones
            $stmt = $pdo->query("SHOW TABLES LIKE 'sessions'");
            if ($stmt->fetch()) {
                echo "✓ Tabla 'sessions' existe<br>";
            } else {
                echo "✗ Tabla 'sessions' NO existe<br>";
            }
            
        } catch (PDOException $e) {
            echo "<strong style='color: red;'>✗ ERROR DE CONEXIÓN: " . $e->getMessage() . "</strong><br>";
        }
    } else {
        echo "No se pudo leer la configuración de la base de datos del .env<br>";
    }
} else {
    echo "Archivo .env no encontrado<br>";
}
echo "<hr>";

// 6. INTENTAR CARGAR LARAVEL
echo "<h3>6. PRUEBA DE CARGA DE LARAVEL</h3>";

try {
    if (file_exists('../vendor/autoload.php')) {
        require '../vendor/autoload.php';
        echo "✓ Autoloader cargado correctamente<br>";
        
        if (file_exists('../bootstrap/app.php')) {
            $app = require_once '../bootstrap/app.php';
            echo "✓ Aplicación Laravel cargada<br>";
            
            // Intentar resolver el kernel
            $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
            echo "✓ HTTP Kernel resuelto<br>";
            
        } else {
            echo "✗ No se encontró bootstrap/app.php<br>";
        }
    } else {
        echo "✗ No se encontró vendor/autoload.php<br>";
    }
} catch (Exception $e) {
    echo "<strong style='color: red;'>✗ ERROR AL CARGAR LARAVEL: " . $e->getMessage() . "</strong><br>";
    echo "Stack trace:<br><pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<h3>7. VARIABLES DE ENTORNO</h3>";
echo "SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'N/A') . "<br>";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "<br>";
echo "HTTPS: " . ($_SERVER['HTTPS'] ?? 'N/A') . "<br>";
echo "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "<br>";
echo "QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'N/A') . "<br>";

echo "<hr>";
echo "<p><strong>Diagnóstico completado - " . date('Y-m-d H:i:s') . "</strong></p>";
?>
