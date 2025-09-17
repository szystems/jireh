<?php
/**
 * Script de reparación: Resetear contraseña de usuario
 * Instrucciones: Sube este archivo a public/ y visita: tudominio.com/reset-password.php
 */

// Verificar que el usuario tenga permisos (opcional)
$password = 'Reparar2025!'; // CAMBIA ESTA CONTRASEÑA por una de tu elección
if (!isset($_GET['password']) || $_GET['password'] !== $password) {
    die('❌ Acceso denegado. Usa: ?password=' . $password);
}

echo '<h1>🔑 Reset de Contraseña de Usuario</h1>';
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
    }
    
    echo "📁 Directorio actual: " . getcwd() . "\n\n";
    
    // Conectar a base de datos
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
            $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password_db);
            echo "✅ Conexión a base de datos exitosa\n\n";
            
            // Mostrar usuarios disponibles
            echo "👥 USUARIOS DISPONIBLES:\n";
            $stmt = $pdo->query("SELECT id, name, email FROM users ORDER BY id");
            $users = $stmt->fetchAll();
            foreach ($users as $user) {
                echo "   {$user['id']}. {$user['name']} ({$user['email']})\n";
            }
            
            // Permitir reset de contraseña por GET parameter
            if (isset($_GET['user']) && isset($_GET['newpass'])) {
                $userId = intval($_GET['user']);
                $newPassword = $_GET['newpass'];
                
                echo "\n🔧 CAMBIANDO CONTRASEÑA...\n";
                
                // Buscar usuario
                $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch();
                
                if ($user) {
                    // Generar hash bcrypt de la nueva contraseña
                    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                    
                    // Actualizar contraseña
                    $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
                    $stmt->execute([$hashedPassword, $userId]);
                    
                    echo "✅ Contraseña actualizada para: {$user['name']} ({$user['email']})\n";
                    echo "📧 Email: {$user['email']}\n";
                    echo "🔑 Nueva contraseña: $newPassword\n";
                    echo "🔐 Hash generado: " . substr($hashedPassword, 0, 20) . "...\n";
                    
                    echo "\n🎯 AHORA PUEDES HACER LOGIN CON:\n";
                    echo "   Email: {$user['email']}\n";
                    echo "   Contraseña: $newPassword\n";
                    
                } else {
                    echo "❌ Usuario con ID $userId no encontrado\n";
                }
                
            } else {
                echo "\n🔧 PARA CAMBIAR CONTRASEÑA:\n";
                echo "Agrega estos parámetros a la URL:\n";
                echo "&user=1&newpass=nuevacontraseña123\n\n";
                echo "Ejemplo completo:\n";
                echo "reset-password.php?password=Reparar2025!&user=1&newpass=nuevacontraseña123\n\n";
                
                echo "📋 USUARIOS DISPONIBLES PARA RESET:\n";
                foreach ($users as $user) {
                    echo "   Para {$user['name']}: &user={$user['id']}&newpass=tucontraseña\n";
                }
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo '</pre>';

if (!isset($_GET['user'])) {
    echo '<h3>🔧 Enlaces rápidos para reset:</h3>';
    echo '<p><a href="?password=Reparar2025!&user=1&newpass=admin123">Cambiar contraseña de Otto Szarata a "admin123"</a></p>';
    echo '<p><a href="?password=Reparar2025!&user=2&newpass=admin123">Cambiar contraseña de Otto Empresa a "admin123"</a></p>';
    echo '<p><a href="?password=Reparar2025!&user=3&newpass=admin123">Cambiar contraseña de Byron de León a "admin123"</a></p>';
}

echo '<p><strong>⚠️ Recuerda eliminar este archivo después de usarlo por seguridad</strong></p>';
?>
