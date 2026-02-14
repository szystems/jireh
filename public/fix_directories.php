<?php
/**
 * Script para crear directorios faltantes en iPage
 * Ejecutar desde public/ en iPage
 */

echo "=== CREANDO DIRECTORIOS FALTANTES ===\n";
echo "<pre>";

// Cambiar al directorio raíz del proyecto (un nivel arriba de public)
$rootPath = dirname(__DIR__);
echo "Directorio raíz: $rootPath\n";

// Directorios necesarios
$directories = [
    'storage/logs',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/sessions', 
    'storage/framework/views',
    'storage/framework/testing',
    'storage/app',
    'storage/app/public',
    'bootstrap/cache'
];

foreach ($directories as $dir) {
    $fullPath = $rootPath . '/' . $dir;
    
    if (!file_exists($fullPath)) {
        if (mkdir($fullPath, 0755, true)) {
            echo "✅ Creado: $dir\n";
        } else {
            echo "❌ Error creando: $dir\n";
        }
    } else {
        echo "✅ Ya existe: $dir\n";
    }
    
    // Verificar permisos
    if (is_writable($fullPath)) {
        echo "   Permisos: ✅ Escribible\n";
    } else {
        echo "   Permisos: ❌ No escribible\n";
        // Intentar cambiar permisos
        if (chmod($fullPath, 0755)) {
            echo "   Permisos corregidos\n";
        }
    }
}

// Crear archivo .gitkeep en directorios vacíos
$gitkeepDirs = [
    'storage/logs',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'bootstrap/cache'
];

foreach ($gitkeepDirs as $dir) {
    $gitkeepPath = $rootPath . '/' . $dir . '/.gitkeep';
    if (!file_exists($gitkeepPath)) {
        if (file_put_contents($gitkeepPath, '')) {
            echo "✅ .gitkeep creado en: $dir\n";
        }
    }
}

echo "\n=== VERIFICACIÓN FINAL ===\n";
foreach ($directories as $dir) {
    $fullPath = $rootPath . '/' . $dir;
    echo "$dir: " . (file_exists($fullPath) && is_writable($fullPath) ? '✅' : '❌') . "\n";
}

echo "</pre>";
echo "\n=== COMPLETADO ===";
?>