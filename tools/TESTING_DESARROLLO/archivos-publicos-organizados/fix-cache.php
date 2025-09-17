<?php
/**
 * Script de reparación: Limpiar Cache y Configuración de Laravel
 * Instrucciones: Sube este archivo a public/ y visita: tudominio.com/fix-cache.php
 */

// Verificar que el usuario tenga permisos (opcional)
$password = 'Reparar2025!'; // Cambia esta contraseña
if (!isset($_GET['password']) || $_GET['password'] !== $password) {
    die('❌ Acceso denegado. Usa: ?password=' . $password);
}

echo '<h1>🧹 Limpiando Cache y Configuración de Laravel</h1>';
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
    
    // Comandos de limpieza a ejecutar
    $commands = [
        'config:clear' => 'Limpiando configuración cacheada',
        'cache:clear' => 'Limpiando cache de aplicación', 
        'view:clear' => 'Limpiando vistas compiladas',
        'route:clear' => 'Limpiando rutas cacheadas'
    ];
    
    foreach ($commands as $command => $description) {
        echo "🔄 $description...\n";
        
        $output = [];
        $returnVar = 0;
        exec("php artisan $command 2>&1", $output, $returnVar);
        
        if ($returnVar === 0) {
            echo "✅ $command - Completado\n";
            if (!empty($output)) {
                foreach ($output as $line) {
                    echo "   $line\n";
                }
            }
        } else {
            echo "❌ Error en $command:\n";
            foreach ($output as $line) {
                echo "   $line\n";
            }
        }
        echo "\n";
    }
    
    // Limpiar directorios manualmente si artisan falla
    echo "🗂️ Limpieza manual de directorios...\n";
    
    $cacheDirs = [
        'bootstrap/cache' => '*.php',
        'storage/framework/cache/data' => '*',
        'storage/framework/views' => '*.php',
        'storage/logs' => '*.log'
    ];
    
    foreach ($cacheDirs as $dir => $pattern) {
        if (is_dir($dir)) {
            $files = glob("$dir/$pattern");
            $count = 0;
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitignore') {
                    unlink($file);
                    $count++;
                }
            }
            echo "✅ $dir - $count archivos eliminados\n";
        } else {
            echo "⚠️ $dir - Directorio no existe\n";
        }
    }
    
    // Verificar permisos de storage
    echo "\n🔐 Verificando permisos de storage...\n";
    $storagePerms = substr(sprintf('%o', fileperms('storage')), -4);
    echo "📁 storage/: $storagePerms\n";
    
    if ($storagePerms >= '0755') {
        echo "✅ Permisos de storage correctos\n";
    } else {
        echo "⚠️ Permisos de storage pueden necesitar ajuste\n";
    }
    
    echo "\n🎉 Limpieza completada. La aplicación debería funcionar mejor ahora.\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo '</pre>';
echo '<p><a href="' . $_SERVER['REQUEST_URI'] . '">🔄 Ejecutar de nuevo</a> | <a href="/">🏠 Ir al inicio</a></p>';
?>
