<?php
/**
 * Script para corregir permisos de caché en Laravel (Windows)
 * 
 * Este script:
 * 1. Limpia toda la caché de Laravel
 * 2. Recrea los directorios necesarios
 * 3. Configura permisos apropiados para Windows
 */

echo "=== CORRECCIÓN DE PERMISOS DE CACHÉ LARAVEL ===\n\n";

// Directorios que necesitan permisos de escritura
$directories = [
    'storage',
    'storage/app',
    'storage/app/public',
    'storage/framework',
    'storage/framework/cache',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/testing',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache'
];

$baseDir = __DIR__;

echo "1. Verificando y creando directorios necesarios...\n";
foreach ($directories as $dir) {
    $fullPath = $baseDir . DIRECTORY_SEPARATOR . $dir;
    
    if (!is_dir($fullPath)) {
        echo "   Creando directorio: $dir\n";
        if (!mkdir($fullPath, 0755, true)) {
            echo "   ❌ Error creando: $dir\n";
        } else {
            echo "   ✅ Creado: $dir\n";
        }
    } else {
        echo "   ✅ Existe: $dir\n";
    }
}

echo "\n2. Limpiando caché existente...\n";

// Limpiar caché de Laravel manualmente
$cacheDir = $baseDir . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'framework' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'data';

if (is_dir($cacheDir)) {
    echo "   Limpiando archivos de caché en: $cacheDir\n";
    
    // Función recursiva para limpiar directorio
    function cleanDirectory($dir) {
        if (!is_dir($dir)) return;
        
        $files = glob($dir . DIRECTORY_SEPARATOR . '*');
        foreach ($files as $file) {
            if (is_dir($file)) {
                cleanDirectory($file);
                rmdir($file);
            } else {
                unlink($file);
            }
        }
    }
    
    cleanDirectory($cacheDir);
    echo "   ✅ Caché limpiada\n";
} else {
    echo "   ⚠️  Directorio de caché no existe, se creará automáticamente\n";
}

echo "\n3. Verificando permisos...\n";

foreach ($directories as $dir) {
    $fullPath = $baseDir . DIRECTORY_SEPARATOR . $dir;
    if (is_dir($fullPath)) {
        if (is_writable($fullPath)) {
            echo "   ✅ Escribible: $dir\n";
        } else {
            echo "   ❌ No escribible: $dir\n";
        }
    }
}

echo "\n4. Intentando corregir permisos (Windows)...\n";

// En Windows, intentamos cambiar permisos usando chmod
foreach ($directories as $dir) {
    $fullPath = $baseDir . DIRECTORY_SEPARATOR . $dir;
    if (is_dir($fullPath)) {
        chmod($fullPath, 0755);
        echo "   📝 Permisos aplicados a: $dir\n";
    }
}

echo "\n5. Limpiando caché de Bootstrap...\n";
$bootstrapCache = $baseDir . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'cache';
if (is_dir($bootstrapCache)) {
    $files = glob($bootstrapCache . DIRECTORY_SEPARATOR . '*.php');
    foreach ($files as $file) {
        if (basename($file) !== '.gitignore') {
            unlink($file);
            echo "   🗑️  Eliminado: " . basename($file) . "\n";
        }
    }
    echo "   ✅ Caché de Bootstrap limpiada\n";
}

echo "\n=== INSTRUCCIONES ADICIONALES PARA WINDOWS ===\n";
echo "Si el problema persiste, ejecuta estos comandos en PowerShell como Administrador:\n\n";

echo "1. Navega al directorio del proyecto:\n";
echo "   cd \"C:\\Users\\szott\\Dropbox\\Desarrollo\\jireh\"\n\n";

echo "2. Da permisos completos al directorio storage:\n";
echo "   icacls storage /grant Everyone:(OI)(CI)F /T\n\n";

echo "3. Da permisos al directorio bootstrap/cache:\n";
echo "   icacls bootstrap\\cache /grant Everyone:(OI)(CI)F /T\n\n";

echo "4. Ejecuta los comandos de Artisan para limpiar caché:\n";
echo "   php artisan cache:clear\n";
echo "   php artisan config:clear\n";
echo "   php artisan route:clear\n";
echo "   php artisan view:clear\n\n";

echo "=== ALTERNATIVA: Ejecutar Laravel con permisos elevados ===\n";
echo "Si nada funciona, ejecuta tu servidor con permisos de administrador:\n";
echo "1. Abre PowerShell o CMD como Administrador\n";
echo "2. Navega al proyecto: cd \"C:\\Users\\szott\\Dropbox\\Desarrollo\\jireh\"\n";
echo "3. Ejecuta: php artisan serve\n\n";

echo "=== VERIFICACIÓN FINAL ===\n";
echo "Después de aplicar los permisos, verifica que funcione probando:\n";
echo "1. Acceder a cualquier página de tu aplicación\n";
echo "2. Editar una venta (que era el problema original)\n";
echo "3. Revisar que no aparezcan más errores de permisos en storage/logs/laravel.log\n\n";

echo "✅ Script completado. Revisa las instrucciones arriba si el problema persiste.\n";
?>
