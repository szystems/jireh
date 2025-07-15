<?php

require_once __DIR__ . '/vendor/autoload.php';

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Venta;
use App\Models\Config;

echo "=== TEST DE VISTA SHOW.BLADE.PHP ===\n";

try {
    // Buscar una venta con detalles para probar
    $venta = Venta::with(['detalleVentas.articulo', 'detalleVentas.trabajadoresCarwash', 'cliente', 'vehiculo'])
                  ->whereHas('detalleVentas')
                  ->first();
    
    if (!$venta) {
        echo "❌ No se encontró ninguna venta con detalles para probar\n";
        exit(1);
    }
    
    echo "✅ Venta encontrada: ID {$venta->id}\n";
    echo "   - Número de factura: " . ($venta->numero_factura ?: 'No especificado') . "\n";
    echo "   - Cliente: " . ($venta->cliente ? $venta->cliente->nombre : 'No especificado') . "\n";
    echo "   - Detalles: " . $venta->detalleVentas->count() . "\n";
    
    // Verificar detalles con trabajadores
    $detallesConTrabajadores = 0;
    foreach ($venta->detalleVentas as $detalle) {
        if ($detalle->articulo && $detalle->articulo->tipo === 'servicio') {
            $trabajadores = $detalle->trabajadoresCarwash;
            if ($trabajadores->count() > 0) {
                $detallesConTrabajadores++;
                echo "   - Detalle {$detalle->id}: {$trabajadores->count()} trabajadores asignados\n";
            }
        }
    }
    
    if ($detallesConTrabajadores > 0) {
        echo "✅ Se encontraron {$detallesConTrabajadores} detalles con trabajadores asignados\n";
    } else {
        echo "⚠️  No se encontraron detalles con trabajadores asignados\n";
    }
    
    // Verificar configuración
    $config = Config::first();
    if ($config) {
        echo "✅ Configuración encontrada: símbolo de moneda '{$config->currency_simbol}'\n";
    } else {
        echo "❌ No se encontró configuración\n";
    }
    
    echo "\n=== TEST COMPLETADO ===\n";
    echo "La vista debería mostrar:\n";
    echo "- Título: 'Detalle de Venta - Factura: {$venta->numero_factura}'\n";
    echo "- Columnas en orden: Artículo, Cantidad, Precio Venta, Precio Costo, Trabajadores Asignados, Descuento, Impuestos, Subtotal\n";
    echo "- Los trabajadores aparecen en su propia columna, no en la de descuentos\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    exit(1);
}
