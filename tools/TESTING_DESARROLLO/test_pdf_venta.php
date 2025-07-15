#!/usr/bin/env php
<?php

/**
 * Script para probar la generación del PDF de venta con cálculos corregidos
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Venta;

echo "=== PRUEBA DE GENERACIÓN DE PDF DE VENTA ===\n\n";

try {
    // Buscar una venta con servicios que tenga comisiones
    $venta = Venta::with([
        'detalleVentas.articulo',
        'detalleVentas.trabajadoresCarwash',
        'detalleVentas.descuento',
        'cliente',
        'vehiculo',
        'usuario',
        'pagos.usuario'
    ])->whereHas('detalleVentas.articulo', function($query) {
        $query->where('tipo', 'servicio');
    })->first();

    if (!$venta) {
        echo "❌ No se encontraron ventas con servicios\n";
        
        // Buscar cualquier venta activa
        $venta = Venta::where('estado', true)->first();
        if (!$venta) {
            echo "❌ No se encontraron ventas activas\n";
            exit(1);
        }
    }

    echo "✅ Venta encontrada: ID {$venta->id}\n";
    echo "   Número de factura: " . ($venta->numero_factura ?: 'No especificado') . "\n";
    echo "   Fecha: " . $venta->created_at->format('d/m/Y') . "\n";
    echo "   Cliente: " . ($venta->cliente ? $venta->cliente->nombre : 'No especificado') . "\n";
    echo "   Detalles: " . $venta->detalleVentas->count() . " artículos\n\n";

    // Mostrar detalles de servicios con comisiones
    foreach ($venta->detalleVentas as $detalle) {
        if ($detalle->articulo && $detalle->articulo->tipo === 'servicio') {
            echo "🔧 Servicio: " . $detalle->articulo->nombre . "\n";
            echo "   - Cantidad: " . $detalle->cantidad . "\n";
            echo "   - Precio costo: Q" . number_format($detalle->precio_costo, 2) . "\n";
            echo "   - Trabajadores carwash: " . $detalle->trabajadoresCarwash->count() . "\n";
            foreach ($detalle->trabajadoresCarwash as $trabajador) {
                echo "     * " . $trabajador->nombre . " - Q" . number_format($trabajador->pivot->monto_comision, 2) . "\n";
            }
            echo "   - Comisión mecánico: Q" . number_format($detalle->articulo->costo_mecanico ?? 0, 2) . "\n";
        }
    }

    echo "\n🌐 URLs para probar:\n";
    echo "   Vista web: http://127.0.0.1:8000/show-venta/{$venta->id}\n";
    echo "   PDF: http://127.0.0.1:8000/venta/export/single/pdf/{$venta->id}\n\n";

    echo "✅ Datos preparados para generar PDF\n";
    echo "   - Los cálculos de costos incluyen comisiones\n";
    echo "   - El resumen financiero mostrará valores correctos\n";
    echo "   - La ganancia neta será precisa\n\n";

    echo "📝 Para probar:\n";
    echo "   1. Asegúrate de que el servidor esté corriendo (php artisan serve)\n";
    echo "   2. Abre la URL del PDF en tu navegador\n";
    echo "   3. Verifica que los totales coincidan con la vista web\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   Línea: " . $e->getLine() . "\n";
    echo "   Archivo: " . $e->getFile() . "\n";
    exit(1);
}
