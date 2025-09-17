<?php
/**
 * Script de reparación: Crear directorios faltantes de Storage
 * Instrucciones: Sube este archivo a public/ y visita: tudominio.com/fix-storage.php
 */

// Verificar que el usuario tenga permisos (opcional)
$password = 'Reparar2025!'; // CAMBIA ESTA CONTRASEÑA por una de tu elección
if (!isset($_GET['password']) || $_GET['password'] !== $password) {
    die('❌ Acceso denegado. Usa: ?password=' . $password);
}

echo '<h1>📁 Creando Directorios de Storage</h1>';
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
    
    // Directorios necesarios de storage
    $storageDirs = [
        'storage/app',
        'storage/app/public',
        'storage/framework',
        'storage/framework/cache',
        'storage/framework/cache/data',
        'storage/framework/sessions',
        'storage/framework/views',
        'storage/logs'
    ];
    
    echo "📂 Creando directorios de storage...\n";
    
    foreach ($storageDirs as $dir) {
        if (is_dir($dir)) {
            echo "✅ $dir - Ya existe\n";
        } else {
            if (mkdir($dir, 0775, true)) {
                echo "✅ $dir - CREADO\n";
            } else {
                echo "❌ $dir - ERROR al crear\n";
            }
        }
    }
    
    // Crear archivos .gitignore necesarios
    echo "\n📝 Creando archivos .gitignore...\n";
    
    $gitignoreFiles = [
        'storage/app/.gitignore' => "*\n!public/\n!.gitignore",
        'storage/app/public/.gitignore' => "*\n!.gitignore",
        'storage/framework/.gitignore' => "compiled.php\nconfig.php\ndown\nevents.scanned.php\nmaintenance.php\nroutes.scanned.php\nroutes.php\nschedule-*\nservices.json",
        'storage/framework/cache/.gitignore' => "*\n!data/\n!.gitignore",
        'storage/framework/cache/data/.gitignore' => "*\n!.gitignore",
        'storage/framework/sessions/.gitignore' => "*\n!.gitignore",
        'storage/framework/views/.gitignore' => "*\n!.gitignore",
        'storage/logs/.gitignore' => "*\n!.gitignore"
    ];
    
    foreach ($gitignoreFiles as $file => $content) {
        if (file_exists($file)) {
            echo "✅ $file - Ya existe\n";
        } else {
            if (file_put_contents($file, $content)) {
                echo "✅ $file - CREADO\n";
            } else {
                echo "❌ $file - ERROR al crear\n";
            }
        }
    }
    
    // Verificar permisos
    echo "\n🔐 Verificando permisos...\n";
    
    foreach ($storageDirs as $dir) {
        if (is_dir($dir)) {
            $perms = substr(sprintf('%o', fileperms($dir)), -4);
            if ($perms >= '0775') {
                echo "✅ $dir - Permisos: $perms (OK)\n";
            } else {
                echo "⚠️ $dir - Permisos: $perms (pueden necesitar ajuste)\n";
                // Intentar cambiar permisos
                if (chmod($dir, 0775)) {
                    echo "✅ $dir - Permisos actualizados a 0775\n";
                }
            }
        }
    }
    
    echo "\n🎉 Directorios de storage creados y configurados.\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo '</pre>';
echo '<p><a href="diagnosis.php?password=Reparar2025!">🔍 Ver Diagnóstico</a> | <a href="migrate-sessions.php?password=Reparar2025!">🗄️ Migrar Sesiones</a> | <a href="/">🏠 Inicio</a></p>';
?>
