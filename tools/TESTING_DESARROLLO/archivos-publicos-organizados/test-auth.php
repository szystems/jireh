<?php
/**
 * Script de diagnóstico específico para credenciales szystems@hotmail.com
 */

// Verificar acceso
$password = 'Reparar2025!';
if (!isset($_GET['password']) || $_GET['password'] !== $password) {
    die('❌ Acceso denegado. Usa: ?password=' . $password);
}

echo '<h1>🔐 Test específico: szystems@hotmail.com</h1>';
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
    
    echo "📁 Directorio actual: " . getcwd() . "\n\n";
    
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
        
        // Credenciales específicas del usuario
        $email = 'szystems@hotmail.com';
        $password_test = 'SPP7007aaa@@@';
        
        echo "=== PRUEBA DE CREDENCIALES ESPECÍFICAS ===\n";
        echo "📧 Email: $email\n";
        echo "🔑 Contraseña: $password_test\n\n";
        
        // Buscar usuario exacto
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo "❌ PROBLEMA: Usuario '$email' NO existe\n\n";
            
            // Mostrar usuarios disponibles
            $stmt = $pdo->query("SELECT id, name, email FROM users ORDER BY id LIMIT 5");
            $allUsers = $stmt->fetchAll();
            echo "👥 Usuarios disponibles:\n";
            foreach ($allUsers as $u) {
                echo "   - {$u['email']} (ID: {$u['id']})\n";
            }
            
        } else {
            echo "✅ Usuario encontrado:\n";
            echo "   ID: {$user['id']}\n";
            echo "   Nombre: {$user['name']}\n";
            echo "   Email: {$user['email']}\n";
            echo "   Estado: " . ($user['estado'] ? 'Activo ✅' : 'Inactivo ❌') . "\n\n";
            
            $storedHash = $user['password'];
            echo "🔐 Hash: " . substr($storedHash, 0, 30) . "...\n\n";
            
            // VERIFICACIÓN CRÍTICA
            echo "🔍 VERIFICANDO CONTRASEÑA:\n";
            $isValid = password_verify($password_test, $storedHash);
            echo "   Resultado: " . ($isValid ? '✅ VÁLIDA' : '❌ INVÁLIDA') . "\n\n";
            
            if (!$isValid) {
                echo "❌ PROBLEMA: Contraseña no coincide\n";
                echo "🔧 SOLUCIONANDO...\n";
                
                if (isset($_GET['fix']) && $_GET['fix'] == 'yes') {
                    // Corregir contraseña
                    $correctHash = password_hash($password_test, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $result = $stmt->execute([$correctHash, $user['id']]);
                    
                    if ($result) {
                        echo "✅ ¡CONTRASEÑA CORREGIDA!\n";
                        echo "� AHORA INTENTA HACER LOGIN\n";
                    } else {
                        echo "❌ Error al corregir\n";
                    }
                } else {
                    echo "Para corregir: agrega &fix=yes\n";
                }
            } else {
                echo "✅ CONTRASEÑA CORRECTA - Problema en otro lugar\n";
            }
        }
        
    } else {
        echo "❌ Archivo .env no encontrado\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo '</pre>';

echo '<h3>🔧 Enlaces:</h3>';
echo '<p><a href="?password=Reparar2025!&fix=yes" style="color: red; font-weight: bold;">CORREGIR CONTRASEÑA</a></p>';
?>

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
    }
    
    echo "📁 Directorio actual: " . getcwd() . "\n\n";
    
    // Conectar a base de datos
    echo "=== VERIFICACIÓN DE TABLA USERS ===\n";
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
                
                // Verificar tabla users
                $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
                if ($stmt->rowCount() > 0) {
                    echo "✅ Tabla 'users' existe\n";
                    
                    // Estructura de la tabla users
                    $stmt = $pdo->query("DESCRIBE users");
                    $columns = $stmt->fetchAll();
                    echo "📋 Estructura de tabla users:\n";
                    foreach ($columns as $column) {
                        echo "   - {$column['Field']}: {$column['Type']}\n";
                    }
                    
                    // Contar usuarios
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
                    $result = $stmt->fetch();
                    echo "\n📊 Total usuarios en BD: {$result['count']}\n";
                    
                    // Mostrar algunos usuarios (sin contraseñas)
                    $stmt = $pdo->query("SELECT id, name, email, created_at FROM users LIMIT 5");
                    $users = $stmt->fetchAll();
                    echo "👥 Primeros 5 usuarios:\n";
                    foreach ($users as $user) {
                        echo "   - ID: {$user['id']}, Nombre: {$user['name']}, Email: {$user['email']}\n";
                    }
                    
                    // Verificar si hay usuarios con contraseñas válidas
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE password IS NOT NULL AND password != ''");
                    $result = $stmt->fetch();
                    echo "\n🔒 Usuarios con contraseña configurada: {$result['count']}\n";
                    
                    // Verificar formato de contraseñas (deben empezar con $2y$ si son bcrypt)
                    $stmt = $pdo->query("SELECT password FROM users WHERE password IS NOT NULL LIMIT 3");
                    $passwords = $stmt->fetchAll();
                    echo "\n🔐 Formato de contraseñas:\n";
                    foreach ($passwords as $i => $pass) {
                        $format = substr($pass['password'], 0, 10) . '...';
                        $type = '';
                        if (strpos($pass['password'], '$2y$') === 0) {
                            $type = ' (bcrypt - correcto)';
                        } elseif (strpos($pass['password'], '$2a$') === 0) {
                            $type = ' (bcrypt - válido)';
                        } elseif (strlen($pass['password']) === 32) {
                            $type = ' (MD5 - obsoleto)';
                        } elseif (strlen($pass['password']) === 40) {
                            $type = ' (SHA1 - obsoleto)';
                        } else {
                            $type = ' (formato desconocido)';
                        }
                        echo "   Usuario " . ($i + 1) . ": $format$type\n";
                    }
                    
                } else {
                    echo "❌ Tabla 'users' NO existe\n";
                }
                
            } catch (Exception $e) {
                echo "❌ Error de conexión: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Verificar configuración de autenticación
    echo "\n=== CONFIGURACIÓN DE AUTENTICACIÓN ===\n";
    
    // Verificar config/auth.php
    if (file_exists('config/auth.php')) {
        echo "✅ Archivo config/auth.php existe\n";
        $authConfig = file_get_contents('config/auth.php');
        
        // Buscar guard por defecto
        if (preg_match("/'default' => '([^']+)'/", $authConfig, $matches)) {
            echo "🛡️ Guard por defecto: {$matches[1]}\n";
        }
        
        // Buscar provider por defecto
        if (preg_match("/'driver' => '([^']+)'/", $authConfig, $matches)) {
            echo "👤 Driver de usuarios: {$matches[1]}\n";
        }
    } else {
        echo "❌ Archivo config/auth.php no encontrado\n";
    }
    
    // Verificar modelo User
    if (file_exists('app/User.php')) {
        echo "✅ Modelo User.php encontrado (ubicación antigua)\n";
    } elseif (file_exists('app/Models/User.php')) {
        echo "✅ Modelo User.php encontrado (ubicación nueva)\n";
    } else {
        echo "❌ Modelo User.php no encontrado\n";
    }
    
    // Verificar LoginController
    echo "\n=== CONTROLADOR DE LOGIN ===\n";
    if (file_exists('app/Http/Controllers/Auth/LoginController.php')) {
        echo "✅ LoginController encontrado\n";
        $loginController = file_get_contents('app/Http/Controllers/Auth/LoginController.php');
        
        // Verificar redirectTo
        if (preg_match("/redirectTo = '([^']+)'/", $loginController, $matches)) {
            echo "🏠 Redirección después del login: {$matches[1]}\n";
        } elseif (preg_match('/redirectTo\(\)/', $loginController)) {
            echo "🏠 Redirección dinámica configurada\n";
        }
        
        // Verificar username field
        if (preg_match("/username\(\).*return '([^']+)'/", $loginController, $matches)) {
            echo "📧 Campo de login: {$matches[1]}\n";
        } else {
            echo "📧 Campo de login: email (por defecto)\n";
        }
    } else {
        echo "❌ LoginController no encontrado\n";
    }
    
    // Test de hash de contraseña
    echo "\n=== PRUEBA DE HASH DE CONTRASEÑA ===\n";
    echo "🧪 Generando hash de prueba para contraseña '12345':\n";
    
    // Simular diferentes métodos de hash
    $testPassword = '12345';
    $bcryptHash = '$2y$10$' . substr(md5(microtime()), 0, 22); // Simulado
    echo "   Bcrypt (Laravel): $bcryptHash...\n";
    echo "   MD5: " . md5($testPassword) . "\n";
    echo "   SHA1: " . sha1($testPassword) . "\n";
    
    echo "\n🎯 RECOMENDACIONES:\n";
    echo "1. Verifica que tengas usuarios en la tabla 'users'\n";
    echo "2. Confirma que las contraseñas estén en formato bcrypt (\$2y\$...)\n";
    echo "3. Prueba con credenciales que sepas que existen\n";
    echo "4. Si las contraseñas están en formato antiguo (MD5/SHA1), hay que migrarlas\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo '</pre>';
echo '<p><strong>🎯 Con esta información podremos identificar por qué el login no autentica correctamente</strong></p>';
?>
