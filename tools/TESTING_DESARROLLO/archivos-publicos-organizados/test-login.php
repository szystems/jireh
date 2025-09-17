<?php
/**
 * Script de diagnóstico: Probar login y sesiones
 * Instrucciones: Sube este archivo a public/ y visita: tudominio.com/test-login.php
 */

// Verificar que el usuario tenga permisos (opcional)
$password = 'Reparar2025!'; // CAMBIA ESTA CONTRASEÑA por una de tu elección
if (!isset($_GET['password']) || $_GET['password'] !== $password) {
    die('❌ Acceso denegado. Usa: ?password=' . $password);
}

echo '<h1>🔐 Diagnóstico de Login y Sesiones</h1>';
echo '<pre>';

try {
    // Cambiar al directorio raíz de Laravel
    $currentDir = getcwd();
    $searchDir = $currentDir;
    $maxLevels = 5;
    $level = 0;
    
    while ($level < $maxLevels && !file_exists($searchDir . '/composer.json')) {
        $parentDir = dirname($searchDir);
        if ($parentDir === $searchDir) break;
        $searchDir = $parentDir;
        $level++;
    }
    
    if (file_exists($searchDir . '/composer.json')) {
        $rootPath = $searchDir;
        chdir($rootPath);
        echo "✅ Encontrado Laravel en: $rootPath\n";
    } else {
        echo "❌ No se encontró Laravel\n";
        $rootPath = dirname(__DIR__);
        chdir($rootPath);
        echo "ℹ️ Usando directorio padre: $rootPath\n";
    }
    
    echo "📁 Directorio actual: " . getcwd() . "\n\n";
    
    // Verificar configuración de sesiones
    echo "=== CONFIGURACIÓN DE SESIONES ===\n";
    if (file_exists('.env')) {
        $envContent = file_get_contents('.env');
        
        // Verificar configuraciones de sesión
        $sessionConfigs = [
            'SESSION_DRIVER' => 'Controlador de sesiones',
            'SESSION_LIFETIME' => 'Duración de sesión',
            'SESSION_ENCRYPT' => 'Encriptación',
            'SESSION_COOKIE_HTTPONLY' => 'Cookie HttpOnly',
            'SESSION_SAME_SITE' => 'SameSite policy'
        ];
        
        foreach ($sessionConfigs as $config => $description) {
            if (preg_match("/$config=(.+)/", $envContent, $matches)) {
                $value = trim($matches[1]);
                echo "✅ $config = '$value' - $description\n";
            } else {
                echo "❌ $config - NO DEFINIDO - $description\n";
            }
        }
    } else {
        echo "❌ Archivo .env no encontrado\n";
    }
    
    // Probar conexión a base de datos y verificar tabla sessions
    echo "\n=== VERIFICACIÓN DE TABLA SESSIONS ===\n";
    if (file_exists('.env')) {
        $envContent = file_get_contents('.env');
        
        preg_match('/DB_HOST=(.+)/', $envContent, $hostMatch);
        preg_match('/DB_DATABASE=(.+)/', $envContent, $dbMatch);
        preg_match('/DB_USERNAME=(.+)/', $envContent, $userMatch);
        preg_match('/DB_PASSWORD=(.*)/', $envContent, $passMatch);
        
        $host = isset($hostMatch[1]) ? trim($hostMatch[1]) : '';
        $database = isset($dbMatch[1]) ? trim($dbMatch[1]) : '';
        $username = isset($userMatch[1]) ? trim($userMatch[1]) : '';
        $password_db = isset($passMatch[1]) ? trim($passMatch[1]) : '';
        
        if ($host && $database && $username) {
            try {
                $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password_db);
                echo "✅ Conexión a base de datos exitosa\n";
                
                // Verificar tabla sessions
                $stmt = $pdo->query("SHOW TABLES LIKE 'sessions'");
                if ($stmt->rowCount() > 0) {
                    echo "✅ Tabla 'sessions' existe\n";
                    
                    // Contar sesiones
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM sessions");
                    $result = $stmt->fetch();
                    echo "📊 Total sesiones en BD: {$result['count']}\n";
                    
                    // Mostrar últimas sesiones
                    $stmt = $pdo->query("SELECT id, user_id, ip_address, last_activity FROM sessions ORDER BY last_activity DESC LIMIT 5");
                    $sessions = $stmt->fetchAll();
                    echo "📋 Últimas 5 sesiones:\n";
                    foreach ($sessions as $session) {
                        $lastActivity = date('Y-m-d H:i:s', $session['last_activity']);
                        echo "   - ID: {$session['id']}, User: {$session['user_id']}, IP: {$session['ip_address']}, Última actividad: $lastActivity\n";
                    }
                    
                } else {
                    echo "❌ Tabla 'sessions' NO existe\n";
                }
                
            } catch (Exception $e) {
                echo "❌ Error de conexión: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Probar sesión actual de PHP
    echo "\n=== SESIÓN PHP ACTUAL ===\n";
    session_start();
    echo "📋 Session ID: " . session_id() . "\n";
    echo "📋 Session Status: " . session_status() . " (1=disabled, 2=active)\n";
    echo "📋 Session Save Path: " . session_save_path() . "\n";
    echo "📋 Session Save Handler: " . ini_get('session.save_handler') . "\n";
    
    // Probar escribir en sesión
    $_SESSION['test'] = 'funcionando_' . time();
    echo "✅ Escritura en sesión: {$_SESSION['test']}\n";
    
    // Verificar CSRF token
    echo "\n=== TOKEN CSRF ===\n";
    if (function_exists('csrf_token') || function_exists('app')) {
        echo "⚠️ No se puede generar token CSRF fuera del contexto de Laravel\n";
    } else {
        echo "ℹ️ Token CSRF se genera dentro de Laravel\n";
    }
    
    // Verificar middlewares
    echo "\n=== ARCHIVOS CRÍTICOS PARA LOGIN ===\n";
    $loginFiles = [
        'app/Http/Controllers/Auth/LoginController.php' => 'Controlador de login',
        'app/Http/Middleware/VerifyCsrfToken.php' => 'Middleware CSRF',
        'resources/views/auth/login.blade.php' => 'Vista de login',
        'routes/web.php' => 'Rutas web'
    ];
    
    foreach ($loginFiles as $file => $description) {
        if (file_exists($file)) {
            $size = filesize($file);
            echo "✅ $file - $description (${size} bytes)\n";
        } else {
            echo "❌ $file - FALTA - $description\n";
        }
    }
    
    echo "\n🎯 RECOMENDACIONES:\n";
    echo "1. Intenta hacer login y copia el error exacto que aparece\n";
    echo "2. Revisa el archivo storage/logs/laravel.log para ver errores recientes\n";
    echo "3. Verifica que la ruta de login esté correcta\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo '</pre>';
echo '<p><strong>🔍 Próximo paso:</strong> Intenta hacer login y comparte el error exacto que aparece</p>';
?>
