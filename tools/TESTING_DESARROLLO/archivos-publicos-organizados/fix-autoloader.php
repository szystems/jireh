<?php
/**
 * Script de reparación: Regenerar Autoloader de Composer
 * Instrucciones: Sube este archivo a public/ y visita: tudominio.com/fix-autoloader.php?password=RepararJireh2025!
 */

// Verificar que el usuario tenga permisos (opcional)
$password = 'Reparar2025!'; // CAMBIA ESTA CONTRASEÑA por una de tu elección
if (!isset($_GET['password']) || $_GET['password'] !== $password) {
    die('❌ Acceso denegado. Usa: ?password=' . $password);
}

echo '<h1>🔧 Reparando Autoloader de Composer</h1>';
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
    
    // 1. Verificar si existe composer.phar o composer
    $composerCmd = 'composer';
    if (file_exists('composer.phar')) {
        $composerCmd = 'php composer.phar';
        echo "✅ Encontrado composer.phar local\n";
    } else {
        echo "ℹ️ Usando composer global\n";
    }
    
    // 2. Verificar vendor/autoload.php
    if (!file_exists('vendor/autoload.php')) {
        echo "❌ vendor/autoload.php NO existe - ejecutando composer install...\n\n";
        
        // Ejecutar composer install
        $output = [];
        $returnVar = 0;
        exec("$composerCmd install --optimize-autoloader --no-dev 2>&1", $output, $returnVar);
        
        foreach ($output as $line) {
            echo $line . "\n";
        }
        
        if ($returnVar === 0) {
            echo "\n✅ Composer install completado\n";
        } else {
            echo "\n❌ Error en composer install (código: $returnVar)\n";
        }
    } else {
        echo "✅ vendor/autoload.php existe\n";
    }
    
    // 3. Regenerar autoloader optimizado
    echo "\n🔄 Regenerando autoloader optimizado...\n";
    $output = [];
    exec("$composerCmd dump-autoload --optimize 2>&1", $output, $returnVar);
    
    foreach ($output as $line) {
        echo $line . "\n";
    }
    
    if ($returnVar === 0) {
        echo "\n✅ Autoloader regenerado exitosamente\n";
    } else {
        echo "\n❌ Error regenerando autoloader\n";
    }
    
    // 4. Verificar archivos críticos
    echo "\n📋 Verificando archivos críticos:\n";
    $criticalFiles = [
        'vendor/autoload.php',
        'vendor/composer/autoload_real.php',
        'vendor/composer/autoload_classmap.php',
        'bootstrap/app.php'
    ];
    
    foreach ($criticalFiles as $file) {
        if (file_exists($file)) {
            echo "✅ $file - OK\n";
        } else {
            echo "❌ $file - FALTA\n";
        }
    }
    
    echo "\n🎉 Proceso completado. Intenta acceder a tu aplicación ahora.\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo '</pre>';
echo '<p><a href="' . $_SERVER['REQUEST_URI'] . '">🔄 Ejecutar de nuevo</a> | <a href="/">🏠 Ir al inicio</a></p>';
?>
