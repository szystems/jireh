<?php
/**
 * Script de reparación: Configurar SESSION_SAME_SITE faltante
 * Instrucciones: Sube este archivo a public/ y visita: tudominio.com/fix-session-config.php
 */

// Verificar que el usuario tenga permisos (opcional)
$password = 'Reparar2025!'; // CAMBIA ESTA CONTRASEÑA por una de tu elección
if (!isset($_GET['password']) || $_GET['password'] !== $password) {
    die('❌ Acceso denegado. Usa: ?password=' . $password);
}

echo '<h1>⚙️ Configurar SESSION_SAME_SITE</h1>';
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
    
    // Crear respaldo de .env
    echo "💾 Creando respaldo de .env...\n";
    if (file_exists('.env')) {
        $backupName = '.env.backup.session-fix.' . date('Y-m-d_H-i-s');
        if (copy('.env', $backupName)) {
            echo "✅ Respaldo creado: $backupName\n\n";
        }
        
        $envContent = file_get_contents('.env');
        
        // Configuraciones adicionales necesarias
        $sessionConfigs = [
            'SESSION_SAME_SITE' => 'lax',
            'SESSION_SECURE_COOKIE' => 'false',
            'SESSION_DOMAIN' => 'null',
            'SESSION_COOKIE' => 'burotributario_session'
        ];
        
        echo "⚙️ Agregando configuraciones de sesión faltantes...\n";
        
        $modified = false;
        foreach ($sessionConfigs as $key => $value) {
            if (strpos($envContent, $key . '=') !== false) {
                echo "ℹ️ $key ya existe\n";
            } else {
                $envContent .= "\n$key=$value";
                echo "✅ $key agregado como $value\n";
                $modified = true;
            }
        }
        
        // Verificar y corregir otras configuraciones importantes
        echo "\n🔍 Verificando otras configuraciones...\n";
        
        // APP_URL debe estar configurado
        if (!preg_match('/APP_URL=/', $envContent)) {
            $envContent .= "\nAPP_URL=https://software.burotributario.com";
            echo "✅ APP_URL agregado\n";
            $modified = true;
        }
        
        // Asegurar que APP_ENV sea production
        if (preg_match('/APP_ENV=local/', $envContent)) {
            $envContent = preg_replace('/APP_ENV=local/', 'APP_ENV=production', $envContent);
            echo "✅ APP_ENV cambiado a production\n";
            $modified = true;
        }
        
        // Asegurar que APP_DEBUG sea false
        if (preg_match('/APP_DEBUG=true/', $envContent)) {
            $envContent = preg_replace('/APP_DEBUG=true/', 'APP_DEBUG=false', $envContent);
            echo "✅ APP_DEBUG cambiado a false\n";
            $modified = true;
        }
        
        if ($modified) {
            // Guardar archivo .env actualizado
            if (file_put_contents('.env', $envContent)) {
                echo "\n✅ Archivo .env actualizado correctamente\n";
            } else {
                echo "\n❌ Error guardando archivo .env\n";
            }
        } else {
            echo "\n✅ No se necesitaron cambios\n";
        }
        
        // Mostrar configuración final de sesiones
        echo "\n📋 CONFIGURACIÓN FINAL DE SESIONES:\n";
        $finalEnv = file_get_contents('.env');
        
        $sessionVars = [
            'SESSION_DRIVER', 'SESSION_LIFETIME', 'SESSION_ENCRYPT',
            'SESSION_COOKIE_HTTPONLY', 'SESSION_SAME_SITE', 'SESSION_SECURE_COOKIE',
            'SESSION_DOMAIN', 'SESSION_COOKIE'
        ];
        
        foreach ($sessionVars as $var) {
            if (preg_match("/$var=(.+)/", $finalEnv, $matches)) {
                $value = trim($matches[1]);
                echo "   $var = $value\n";
            } else {
                echo "   $var = NO DEFINIDO\n";
            }
        }
        
    } else {
        echo "❌ Archivo .env no encontrado\n";
    }
    
    echo "\n🧹 Limpiando configuración cacheada...\n";
    
    // Limpiar cache manualmente
    $cacheFiles = [
        'bootstrap/cache/config.php',
        'bootstrap/cache/routes.php', 
        'bootstrap/cache/services.php'
    ];
    
    foreach ($cacheFiles as $file) {
        if (file_exists($file)) {
            unlink($file);
            echo "✅ Eliminado: $file\n";
        }
    }
    
    echo "\n🎉 Configuración completada!\n";
    echo "📝 AHORA PRUEBA: Ve a https://software.burotributario.com/login e intenta hacer login\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo '</pre>';
echo '<p><strong>🎯 Próximo paso:</strong> <a href="https://software.burotributario.com/login">Ir a login</a> y probar</p>';
?>
