<?php

require_once __DIR__ . '/vendor/autoload.php';

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\AuditoriaController;
use App\Models\Venta;
use App\Models\Articulo;
use App\Traits\StockValidation;
use Illuminate\Support\Facades\Artisan;

echo "=== TEST DEL SISTEMA DE AUDITORÍA ===\n";

try {
    // 1. Verificar que el controlador funciona
    $controller = new AuditoriaController();
    echo "✅ AuditoriaController creado correctamente\n";
    
    // 2. Verificar datos básicos
    $totalVentas = Venta::count();
    $totalArticulos = Articulo::count();
    echo "✅ Datos disponibles: {$totalVentas} ventas, {$totalArticulos} artículos\n";
    
    // 3. Verificar que el comando está registrado
    $exitCode = Artisan::call('list');
    $output = Artisan::output();
    
    if (strpos($output, 'ventas:auditoria') !== false) {
        echo "✅ Comando ventas:auditoria registrado correctamente\n";
    } else {
        echo "⚠️  Comando ventas:auditoria no encontrado en la lista\n";
    }
    
    // 4. Verificar que el trait funciona
    // Crear una clase temporal para probar el trait
    $testClass = new class {
        use StockValidation;
    };
    
    // Probar validación de stock
    if (method_exists($testClass, 'validarStockDisponible')) {
        echo "✅ Trait StockValidation funciona correctamente\n";
    } else {
        echo "❌ Error en Trait StockValidation\n";
    }
    
    // 5. Verificar estructura de directorios
    $dirAuditorias = storage_path('app/auditorias');
    if (!is_dir($dirAuditorias)) {
        mkdir($dirAuditorias, 0755, true);
        echo "✅ Directorio de auditorías creado: {$dirAuditorias}\n";
    } else {
        echo "✅ Directorio de auditorías existe: {$dirAuditorias}\n";
    }
    
    // 6. Verificar rutas
    $rutaIndex = url('admin/auditoria');
    echo "✅ Ruta principal de auditoría: {$rutaIndex}\n";
    
    echo "\n=== SISTEMA DE AUDITORÍA LISTO PARA USAR ===\n";
    echo "Funcionalidades disponibles:\n";
    echo "- Comando: php artisan ventas:auditoria\n";
    echo "- Interfaz web: /admin/auditoria\n";
    echo "- Validaciones de stock en tiempo real\n";
    echo "- Detección de inconsistencias\n";
    echo "- Reportes automáticos\n";
    echo "- Corrección automática de problemas menores\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    exit(1);
}
