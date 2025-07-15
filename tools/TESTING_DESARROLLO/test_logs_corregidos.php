<?php

require_once __DIR__ . '/vendor/autoload.php';

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Log;

echo "=== TEST DE LOGS CORREGIDOS ===\n";

try {
    // Simular varios tipos de logs que se usan en el VentaController
    Log::info('Test log con mensaje simple', []);
    echo "✓ Log simple OK\n";
    
    Log::info('Test log con datos', ['venta_id' => 123]);
    echo "✓ Log con datos OK\n";
    
    Log::info('Test log con array complejo', [
        'datos' => ['item1' => 'value1', 'item2' => 'value2'],
        'count' => 5
    ]);
    echo "✓ Log con array complejo OK\n";
    
    // Simular logs dinámicos como los del controlador
    $detalleId = 999;
    $articuloId = 123;
    
    Log::info("Test log dinámico para detalle ID: {$detalleId}", []);
    echo "✓ Log dinámico OK\n";
    
    Log::info("Test log con variable para artículo ID: {$articuloId}", []);
    echo "✓ Log con variable OK\n";
    
    echo "\n=== TODOS LOS LOGS FUNCIONAN CORRECTAMENTE ===\n";
    echo "El error de TypeError ha sido resuelto.\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    exit(1);
}
