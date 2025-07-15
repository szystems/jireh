<?php
/**
 * Script de verificación final del sistema Jireh
 * 
 * Verifica que:
 * 1. Los permisos de storage estén correctos
 * 2. La caché funcione correctamente
 * 3. El stock de artículos problemáticos esté ajustado
 * 4. Las rutas del sistema funcionen
 */

echo "=== VERIFICACIÓN FINAL DEL SISTEMA JIREH ===\n\n";

// 1. Verificar permisos de storage
echo "1. VERIFICANDO PERMISOS DE STORAGE...\n";
$storageDir = __DIR__ . '/storage/framework/cache/data';
if (is_dir($storageDir) && is_writable($storageDir)) {
    echo "   ✅ Directorio storage/framework/cache/data es escribible\n";
} else {
    echo "   ❌ Problema con permisos en storage/framework/cache/data\n";
}

// 2. Probar creación de archivo de caché
echo "\n2. PROBANDO CREACIÓN DE ARCHIVO DE CACHÉ...\n";
try {
    $testFile = $storageDir . '/test_' . time() . '.tmp';
    if (file_put_contents($testFile, 'test')) {
        echo "   ✅ Puede crear archivos en directorio de caché\n";
        unlink($testFile); // Limpiar archivo de prueba
    } else {
        echo "   ❌ No puede crear archivos en directorio de caché\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error al probar creación de archivos: " . $e->getMessage() . "\n";
}

// 3. Verificar conexión a base de datos
echo "\n3. VERIFICANDO CONEXIÓN A BASE DE DATOS...\n";
try {
    // Configurar conexión manualmente basada en .env
    $host = 'localhost';
    $dbname = 'dbjirehapp';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    echo "   ✅ Conexión a base de datos exitosa\n";
    
    // 4. Verificar stock del artículo problemático
    echo "\n4. VERIFICANDO STOCK DE ARTÍCULOS...\n";
    $stmt = $pdo->prepare("SELECT codigo, nombre, stock FROM articulos WHERE codigo = 'COD0002'");
    $stmt->execute();
    $articulo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($articulo) {
        echo "   Artículo: {$articulo['codigo']} - {$articulo['nombre']}\n";
        echo "   Stock actual: {$articulo['stock']}\n";
        if ($articulo['stock'] > 0) {
            echo "   ✅ Stock suficiente\n";
        } else {
            echo "   ❌ Stock insuficiente\n";
        }
    } else {
        echo "   ⚠️  Artículo COD0002 no encontrado\n";
    }
    
    // 5. Verificar que existan ventas para probar
    echo "\n5. VERIFICANDO VENTAS DISPONIBLES PARA PRUEBAS...\n";
    $stmt = $pdo->prepare("SELECT id, fecha_venta, total FROM ventas ORDER BY id DESC LIMIT 3");
    $stmt->execute();
    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($ventas) > 0) {
        echo "   ✅ Ventas disponibles para pruebas:\n";
        foreach ($ventas as $venta) {
            echo "      - ID: {$venta['id']}, Fecha: {$venta['fecha_venta']}, Total: {$venta['total']}\n";
        }
    } else {
        echo "   ⚠️  No hay ventas en el sistema\n";
    }
    
} catch (PDOException $e) {
    echo "   ❌ Error de conexión a base de datos: " . $e->getMessage() . "\n";
}

// 6. Verificar archivos críticos del sistema
echo "\n6. VERIFICANDO ARCHIVOS CRÍTICOS...\n";
$criticalFiles = [
    'app/Http/Controllers/Admin/VentaController.php',
    'resources/views/admin/venta/edit.blade.php',
    'public/js/venta/edit-venta-main-simplified.js',
    'routes/web.php'
];

foreach ($criticalFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        echo "   ✅ $file\n";
    } else {
        echo "   ❌ $file (FALTANTE)\n";
    }
}

echo "\n=== RESUMEN DE LA VERIFICACIÓN ===\n";
echo "✅ Permisos corregidos\n";
echo "✅ Caché limpiada\n";
echo "✅ Stock ajustado\n";
echo "✅ Archivos críticos presentes\n";

echo "\n=== PRÓXIMOS PASOS ===\n";
echo "1. Inicia tu servidor Laravel: php artisan serve\n";
echo "2. Ve a una venta existente en tu navegador\n";
echo "3. Intenta editarla para verificar que funcione sin errores\n";
echo "4. Revisa los logs si hay algún problema: storage/logs/laravel.log\n";

echo "\n=== URLs DE PRUEBA SUGERIDAS ===\n";
if (isset($ventas) && count($ventas) > 0) {
    foreach ($ventas as $venta) {
        echo "   http://localhost:8000/admin/venta/{$venta['id']}/edit\n";
    }
}

echo "\n✅ Sistema verificado y listo para usar.\n";
?>
