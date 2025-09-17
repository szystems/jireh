<?php
/**
 * Script de migración: Activar sesiones de base de datos
 * Instrucciones: Sube este archivo a public/ y visita: tudominio.com/migrate-sessions.php
 */

// Verificar que el usuario tenga permisos (opcional)
$password = 'Reparar2025!'; // Cambia esta contraseña
if (!isset($_GET['password']) || $_GET['password'] !== $password) {
    die('❌ Acceso denegado. Usa: ?password=' . $password);
}

echo '<h1>🗄️ Migración a Sesiones de Base de Datos</h1>';
echo '<pre>';

try {
    // Cambiar al directorio raíz de Laravel (buscar hacia arriba hasta encontrar composer.json)
    $currentDir = getcwd();
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
        chdir($rootPath);
        echo "✅ Encontrado Laravel en: $rootPath\n";
    } else {
        echo "❌ No se encontró Laravel (composer.json) en los niveles superiores\n";
        $rootPath = dirname(__DIR__);
        chdir($rootPath);
        echo "ℹ️ Usando directorio padre por defecto: $rootPath\n";
    }
    
    echo "📁 Directorio actual: " . getcwd() . "\n\n";
    
    // 0. Crear respaldo de .env antes de modificar
    echo "💾 Creando respaldo de .env...\n";
    if (file_exists('.env')) {
        $backupName = '.env.backup.' . date('Y-m-d_H-i-s');
        if (copy('.env', $backupName)) {
            echo "✅ Respaldo creado: $backupName\n";
        } else {
            echo "⚠️ No se pudo crear respaldo, continuando...\n";
        }
    } else {
        echo "⚠️ Archivo .env no encontrado\n";
    }
    echo "\n";
    
    // 1. Verificar si existe el archivo de migración
    echo "🔍 Verificando migración de sesiones...\n";
    $migrationsDir = 'database/migrations';
    $sessionMigrationExists = false;
    
    if (is_dir($migrationsDir)) {
        $files = scandir($migrationsDir);
        foreach ($files as $file) {
            if (strpos($file, 'create_sessions_table') !== false) {
                echo "✅ Encontrada migración de sesiones: $file\n";
                $sessionMigrationExists = true;
                break;
            }
        }
    }
    
    // 2. Crear migración si no existe
    if (!$sessionMigrationExists) {
        echo "📝 Creando migración de sesiones...\n";
        $output = [];
        exec('php artisan session:table 2>&1', $output, $returnVar);
        
        if ($returnVar === 0) {
            echo "✅ Migración de sesiones creada\n";
            foreach ($output as $line) {
                echo "   $line\n";
            }
        } else {
            echo "❌ Error creando migración:\n";
            foreach ($output as $line) {
                echo "   $line\n";
            }
        }
        echo "\n";
    }
    
    // 3. Ejecutar migraciones
    echo "🚀 Ejecutando migraciones...\n";
    $output = [];
    exec('php artisan migrate --force 2>&1', $output, $returnVar);
    
    if ($returnVar === 0) {
        echo "✅ Migraciones ejecutadas exitosamente\n";
        foreach ($output as $line) {
            echo "   $line\n";
        }
    } else {
        echo "❌ Error en migraciones:\n";
        foreach ($output as $line) {
            echo "   $line\n";
        }
    }
    echo "\n";
    
    // 4. Verificar si existe la tabla sessions
    echo "🔍 Verificando tabla sessions en la base de datos...\n";
    if (file_exists('.env')) {
        $envContent = file_get_contents('.env');
        
        preg_match('/DB_HOST=(.+)/', $envContent, $hostMatch);
        preg_match('/DB_DATABASE=(.+)/', $envContent, $dbMatch);
        preg_match('/DB_USERNAME=(.+)/', $envContent, $userMatch);
        preg_match('/DB_PASSWORD=(.*)/', $envContent, $passMatch);
        
        $host = isset($hostMatch[1]) ? trim($hostMatch[1]) : '';
        $database = isset($dbMatch[1]) ? trim($dbMatch[1]) : '';
        $username = isset($userMatch[1]) ? trim($userMatch[1]) : '';
        $password = isset($passMatch[1]) ? trim($passMatch[1]) : '';
        
        if ($host && $database && $username) {
            try {
                $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
                $stmt = $pdo->query("SHOW TABLES LIKE 'sessions'");
                
                if ($stmt->rowCount() > 0) {
                    echo "✅ Tabla 'sessions' existe en la base de datos\n";
                    
                    // Verificar estructura
                    $stmt = $pdo->query("DESCRIBE sessions");
                    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    echo "📋 Columnas de la tabla sessions: " . implode(', ', $columns) . "\n";
                    
                } else {
                    echo "❌ Tabla 'sessions' NO existe en la base de datos\n";
                }
            } catch (Exception $e) {
                echo "❌ Error verificando base de datos: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // 5. Actualizar configuración .env
    echo "\n⚙️ Actualizando configuración .env...\n";
    if (file_exists('.env')) {
        $envContent = file_get_contents('.env');
        
        // Cambiar SESSION_DRIVER a database
        if (strpos($envContent, 'SESSION_DRIVER=') !== false) {
            $envContent = preg_replace('/SESSION_DRIVER=.*/', 'SESSION_DRIVER=database', $envContent);
            echo "✅ SESSION_DRIVER cambiado a database\n";
        } else {
            $envContent .= "\nSESSION_DRIVER=database\n";
            echo "✅ SESSION_DRIVER agregado como database\n";
        }
        
        // Asegurar otras configuraciones de sesión
        $sessionConfigs = [
            'SESSION_LIFETIME' => '480',
            'SESSION_EXPIRE_ON_CLOSE' => 'false',
            'SESSION_ENCRYPT' => 'false',
            'SESSION_COOKIE_HTTPONLY' => 'true'
        ];
        
        foreach ($sessionConfigs as $key => $value) {
            if (strpos($envContent, $key . '=') !== false) {
                $envContent = preg_replace("/$key=.*/", "$key=$value", $envContent);
                echo "✅ $key actualizado a $value\n";
            } else {
                $envContent .= "$key=$value\n";
                echo "✅ $key agregado como $value\n";
            }
        }
        
        // Guardar archivo .env
        if (file_put_contents('.env', $envContent)) {
            echo "✅ Archivo .env actualizado correctamente\n";
        } else {
            echo "❌ Error guardando archivo .env\n";
        }
    } else {
        echo "❌ Archivo .env no encontrado\n";
    }
    
    // 6. Limpiar configuración cacheada
    echo "\n🧹 Limpiando configuración cacheada...\n";
    $output = [];
    exec('php artisan config:clear 2>&1', $output);
    echo "✅ Configuración limpiada\n";
    
    echo "\n🎉 Migración a sesiones de base de datos completada!\n";
    echo "📝 IMPORTANTE: Prueba tu aplicación ahora. El error 419 debería estar resuelto.\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo '</pre>';
echo '<p><a href="diagnosis.php?password=jireh2025">🔍 Ver Diagnóstico</a> | <a href="fix-cache.php?password=jireh2025">🧹 Limpiar Cache</a> | <a href="/">🏠 Inicio</a></p>';
?>
