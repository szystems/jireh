<?php
/**
 * Script de diagnóstico específico para credenciales
 * Verifica exactamente por qué szystems@hotmail.com / SPP7007aaa@@@ no funciona
 */

// Verificar acceso
$password = 'Reparar2025!';
if (!isset($_GET['password']) || $_GET['password'] !== $password) {
    die('❌ Acceso denegado. Usa: ?password=' . $password);
}

echo '<h1>🔐 Diagnóstico Específico de Login</h1>';
echo '<pre>';

try {
    // Encontrar Laravel
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
        echo "✅ Laravel encontrado en: $rootPath\n";
    } else {
        echo "❌ Laravel no encontrado\n";
        exit;
    }
    
    // Conectar a BD
    if (file_exists('.env')) {
        $envContent = file_get_contents('.env');
        
        preg_match('/DB_HOST=(.+)/', $envContent, $hostMatch);
        preg_match('/DB_DATABASE=(.+)/', $envContent, $dbMatch);
        preg_match('/DB_USERNAME=(.+)/', $envContent, $userMatch);
        preg_match('/DB_PASSWORD=(.*)/', $envContent, $passMatch);
        
        $host = trim($hostMatch[1] ?? '');
        $database = trim($dbMatch[1] ?? '');
        $username = trim($userMatch[1] ?? '');
        $password_db = trim($passMatch[1] ?? '');
        
        $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password_db);
        echo "✅ Conexión BD exitosa\n\n";
        
        // Credenciales a probar
        $email = 'szystems@hotmail.com';
        $password_test = 'SPP7007aaa@@@';
        
        echo "🎯 PROBANDO CREDENCIALES ESPECÍFICAS:\n";
        echo "📧 Email: $email\n";
        echo "🔑 Contraseña: $password_test\n\n";
        
        // Buscar usuario exacto
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo "❌ PROBLEMA: Usuario '$email' NO existe en BD\n";
            
            // Mostrar usuarios similares
            $stmt = $pdo->query("SELECT id, name, email FROM users");
            $users = $stmt->fetchAll();
            echo "\n👥 Usuarios disponibles:\n";
            foreach ($users as $u) {
                echo "   - {$u['email']} (ID: {$u['id']}, Nombre: {$u['name']})\n";
            }
        } else {
            echo "✅ Usuario encontrado en BD:\n";
            echo "   ID: {$user['id']}\n";
            echo "   Nombre: {$user['name']}\n";
            echo "   Email: {$user['email']}\n";
            echo "   Estado: " . ($user['estado'] ? 'Activo' : 'Inactivo') . "\n";
            echo "   Role: {$user['role_as']}\n";
            
            $storedHash = $user['password'];
            echo "   Hash actual: " . substr($storedHash, 0, 30) . "...\n\n";
            
            // Verificar contraseña
            echo "🔍 VERIFICANDO CONTRASEÑA:\n";
            
            // Método 1: password_verify de PHP
            $isValidPHP = password_verify($password_test, $storedHash);
            echo "   PHP password_verify(): " . ($isValidPHP ? '✅ VÁLIDA' : '❌ INVÁLIDA') . "\n";
            
            // Método 2: Hash manual para comparar
            $testHash = password_hash($password_test, PASSWORD_BCRYPT);
            echo "   Nuevo hash generado: " . substr($testHash, 0, 30) . "...\n";
            
            // Método 3: Verificar formato del hash
            $hashInfo = password_get_info($storedHash);
            echo "   Algoritmo del hash: {$hashInfo['algoName']}\n";
            echo "   Opciones del hash: " . json_encode($hashInfo['options']) . "\n\n";
            
            if (!$isValidPHP) {
                echo "❌ PROBLEMA DETECTADO: La contraseña NO coincide con el hash almacenado\n\n";
                echo "🔧 SOLUCIONES POSIBLES:\n";
                echo "1. La contraseña cambió después del 3 de septiembre\n";
                echo "2. Hay un problema con el hash almacenado\n";
                echo "3. La contraseña se corrompió durante migración\n\n";
                
                echo "💡 ¿QUIERES ACTUALIZAR LA CONTRASEÑA?\n";
                if (isset($_GET['fix']) && $_GET['fix'] == 'yes') {
                    $newHash = password_hash($password_test, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$newHash, $user['id']]);
                    
                    echo "✅ Contraseña actualizada con hash correcto\n";
                    echo "🎯 Ahora intenta hacer login nuevamente\n";
                } else {
                    echo "Para actualizar la contraseña, agrega: &fix=yes\n";
                }
            } else {
                echo "✅ CONTRASEÑA VÁLIDA - El problema está en otro lugar\n\n";
                
                echo "🔍 VERIFICANDO OTROS FACTORES:\n";
                
                // Verificar si el usuario está activo
                if ($user['estado'] == 0) {
                    echo "❌ PROBLEMA: Usuario está INACTIVO (estado = 0)\n";
                    if (isset($_GET['activate']) && $_GET['activate'] == 'yes') {
                        $stmt = $pdo->prepare("UPDATE users SET estado = 1 WHERE id = ?");
                        $stmt->execute([$user['id']]);
                        echo "✅ Usuario activado\n";
                    } else {
                        echo "Para activar usuario, agrega: &activate=yes\n";
                    }
                }
                
                // Verificar configuración de sesiones
                echo "\n🔧 VERIFICANDO CONFIGURACIÓN:\n";
                
                if (file_exists('config/session.php')) {
                    $sessionConfig = file_get_contents('config/session.php');
                    if (strpos($sessionConfig, "'driver' => 'database'") !== false) {
                        echo "✅ Sesiones configuradas para base de datos\n";
                    } else {
                        echo "⚠️ Sesiones no están en base de datos\n";
                    }
                }
                
                // Verificar tabla sessions
                try {
                    $stmt = $pdo->query("SELECT COUNT(*) FROM sessions");
                    $count = $stmt->fetchColumn();
                    echo "✅ Tabla sessions existe con $count registros\n";
                } catch (Exception $e) {
                    echo "❌ Tabla sessions no existe o hay problema\n";
                }
            }
        }
        
    } else {
        echo "❌ Archivo .env no encontrado\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "📍 Línea: " . $e->getLine() . "\n";
    echo "📄 Archivo: " . $e->getFile() . "\n";
}

echo '</pre>';

// Enlaces útiles
echo '<h3>🔧 Enlaces de reparación:</h3>';
echo '<p><a href="?password=Reparar2025!&fix=yes">🔑 Actualizar hash de contraseña</a></p>';
echo '<p><a href="?password=Reparar2025!&activate=yes">👤 Activar usuario</a></p>';
echo '<p><a href="?password=Reparar2025!&fix=yes&activate=yes">⚡ Hacer ambos</a></p>';
?>
