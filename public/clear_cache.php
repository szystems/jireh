<?php
/**
 * Script para limpiar caché en hosting compartido (iPage)
 * Acceder via: https://tudominio.com/clear_cache.php?key=jireh2026
 *
 * IMPORTANTE: Eliminar o proteger este archivo en producción después de usar.
 */

// Protección básica con clave
if (!isset($_GET['key']) || $_GET['key'] !== 'jireh2026') {
    http_response_code(403);
    die('Acceso denegado.');
}

// Ajustar la ruta base del proyecto
$basePath = realpath(__DIR__ . '/..');

$results = [];

// 1. Limpiar caché de vistas compiladas
$viewsPath = $basePath . '/storage/framework/views';
if (is_dir($viewsPath)) {
    $files = glob($viewsPath . '/*');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $count++;
        }
    }
    $results[] = "✅ Vistas compiladas: {$count} archivos eliminados";
} else {
    $results[] = "⚠️ Carpeta de vistas no encontrada";
}

// 2. Limpiar caché de configuración
$configCache = $basePath . '/bootstrap/cache/config.php';
if (file_exists($configCache)) {
    unlink($configCache);
    $results[] = "✅ Caché de configuración eliminado";
} else {
    $results[] = "ℹ️ No había caché de configuración";
}

// 3. Limpiar caché de rutas
$routesCache = $basePath . '/bootstrap/cache/routes-v7.php';
if (file_exists($routesCache)) {
    unlink($routesCache);
    $results[] = "✅ Caché de rutas eliminado";
} else {
    $results[] = "ℹ️ No había caché de rutas";
}

// 4. Limpiar caché de servicios
$servicesCache = $basePath . '/bootstrap/cache/services.php';
if (file_exists($servicesCache)) {
    unlink($servicesCache);
    $results[] = "✅ Caché de servicios eliminado";
} else {
    $results[] = "ℹ️ No había caché de servicios";
}

// 5. Limpiar caché de paquetes
$packagesCache = $basePath . '/bootstrap/cache/packages.php';
if (file_exists($packagesCache)) {
    unlink($packagesCache);
    $results[] = "✅ Caché de paquetes eliminado";
} else {
    $results[] = "ℹ️ No había caché de paquetes";
}

// 6. Limpiar caché de aplicación (framework/cache/data)
$cachePath = $basePath . '/storage/framework/cache/data';
if (is_dir($cachePath)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cachePath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    $count = 0;
    foreach ($iterator as $item) {
        if ($item->isFile() && $item->getFilename() !== '.gitignore') {
            unlink($item->getRealPath());
            $count++;
        }
    }
    $results[] = "✅ Caché de aplicación: {$count} archivos eliminados";
} else {
    $results[] = "ℹ️ No había caché de aplicación";
}

// Mostrar resultados
header('Content-Type: text/html; charset=utf-8');
echo "<h2>🧹 Limpieza de Caché - Sistema Jireh</h2>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<ul>";
foreach ($results as $result) {
    echo "<li>{$result}</li>";
}
echo "</ul>";
echo "<p><strong>✅ Limpieza completada.</strong></p>";
echo "<p><a href='/'>← Volver al sistema</a></p>";
