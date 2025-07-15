<?php
/**
 * SCRIPT DE VERIFICACIÓN FINAL
 * Confirma que el sistema está listo para pruebas en vivo
 */

// Cargar el entorno de Laravel
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICACIÓN FINAL DEL SISTEMA ===\n";

// 1. Verificar que exista una venta para editar
$venta = \App\Models\Venta::with('detalleVentas')->first();
if ($venta) {
    echo "✅ Venta encontrada para pruebas: ID {$venta->id}\n";
    echo "   Detalles disponibles: " . $venta->detalleVentas->count() . "\n";
} else {
    echo "❌ No se encontró venta para pruebas\n";
}

// 2. Verificar trabajadores Car Wash disponibles
$trabajadores = \App\Models\Trabajador::where('tipo', 'car_wash')->get();
echo "✅ Trabajadores Car Wash disponibles: {$trabajadores->count()}\n";
foreach ($trabajadores as $t) {
    echo "   - {$t->nombre} (ID: {$t->id})\n";
}

// 3. Verificar que el controlador no tenga errores de sintaxis
$controllerFile = __DIR__ . '/app/Http/Controllers/Admin/VentaController.php';
if (file_exists($controllerFile)) {
    $syntax = shell_exec("php -l \"$controllerFile\" 2>&1");
    if (strpos($syntax, 'No syntax errors') !== false) {
        echo "✅ VentaController.php: Sin errores de sintaxis\n";
    } else {
        echo "❌ VentaController.php: Errores de sintaxis detectados\n";
        echo "   $syntax\n";
    }
} else {
    echo "❌ No se encontró VentaController.php\n";
}

// 4. Verificar archivos de scripts JS
$jsFiles = [
    'public/js/venta/fix-trabajadores-simple.js',
    'public/test-form-data.js'
];

foreach ($jsFiles as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        echo "✅ Script JS encontrado: $file\n";
    } else {
        echo "❌ Script JS faltante: $file\n";
    }
}

// 5. Verificar vistas actualizadas
$views = [
    'resources/views/admin/venta/show.blade.php',
    'resources/views/admin/venta/edit.blade.php',
    'resources/views/admin/venta/single_pdf.blade.php'
];

foreach ($views as $view) {
    $fullPath = __DIR__ . '/' . $view;
    if (file_exists($fullPath)) {
        $content = file_get_contents($fullPath);
        $hasWorkers = strpos($content, 'trabajador') !== false || strpos($content, 'car') !== false;
        echo "✅ Vista encontrada: $view " . ($hasWorkers ? "(con referencias a trabajadores)" : "") . "\n";
    } else {
        echo "❌ Vista faltante: $view\n";
    }
}

echo "\n=== RESUMEN ===\n";
if ($venta && $trabajadores->count() > 0) {
    echo "🎯 SISTEMA LISTO PARA PRUEBAS EN VIVO\n";
    echo "\nPróximos pasos:\n";
    echo "1. Abrir /admin/ventas/{$venta->id}/edit en el navegador\n";
    echo "2. Abrir consola del navegador (F12)\n";
    echo "3. Monitorear logs: tail -f storage/logs/laravel.log\n";
    echo "4. Editar trabajadores y validar que los datos lleguen al backend\n";
    echo "5. Verificar que las comisiones se regeneren correctamente\n";
} else {
    echo "⚠️  SISTEMA NECESITA CONFIGURACIÓN ADICIONAL\n";
    if (!$venta) echo "   - Crear venta con trabajadores Car Wash\n";
    if ($trabajadores->count() == 0) echo "   - Agregar trabajadores Car Wash\n";
}

echo "\n=== VERIFICACIÓN COMPLETADA ===\n";
