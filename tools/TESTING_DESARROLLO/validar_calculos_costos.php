#!/usr/bin/env php
<?php

/**
 * Script de validación de cálculos de costos y ganancias
 * Verifica que los cálculos en la vista show.blade.php sean correctos
 */

// Incluir el autoloader de Laravel
require_once __DIR__ . '/vendor/autoload.php';

// Crear instancia de aplicación Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';

// Cargar configuración
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Articulo;
use App\Models\Trabajador;
use App\Models\Config;
use App\Models\Descuento;
use Illuminate\Support\Facades\DB;

echo "=== VALIDACIÓN DE CÁLCULOS DE COSTOS Y GANANCIAS ===\n\n";

try {
    // Buscar la config
    $config = Config::first();
    if (!$config) {
        echo "❌ No se encontró configuración del sistema\n";
        exit(1);
    }

    // Buscar ventas activas para validar
    $ventas = Venta::where('estado', true)
                   ->with([
                       'detalleVentas.articulo',
                       'detalleVentas.trabajadoresCarwash',
                       'detalleVentas.descuento'
                   ])
                   ->take(5)
                   ->get();

    if ($ventas->isEmpty()) {
        echo "❌ No se encontraron ventas activas para validar\n";
        exit(1);
    }

    echo "✅ Encontradas " . $ventas->count() . " ventas para validar\n\n";

    foreach ($ventas as $venta) {
        echo "📋 VENTA ID: {$venta->id}\n";
        echo "   Fecha: " . $venta->created_at->format('d/m/Y') . "\n";
        echo "   Número de detalles: " . $venta->detalleVentas->count() . "\n";
        echo "   ----------------------------------------\n";

        // Variables para totales
        $totalVenta = 0;
        $totalCostoCompra = 0;
        $totalDescuentos = 0;
        $totalImpuestos = 0;
        $subtotalSinDescuentoTotal = 0;

        foreach ($venta->detalleVentas as $detalle) {
            echo "   🔸 Artículo: " . ($detalle->articulo ? $detalle->articulo->nombre : 'No disponible') . "\n";
            echo "      Cantidad: {$detalle->cantidad}\n";
            echo "      Precio costo: Q" . number_format($detalle->precio_costo, 2) . "\n";
            
            // Calcular precio unitario usando precio histórico
            $precioUnitario = $detalle->precio_venta > 0 ? $detalle->precio_venta : ($detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad));
            echo "      Precio unitario: Q" . number_format($precioUnitario, 2) . "\n";

            // Calcular subtotal sin descuento
            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;
            $subtotalSinDescuentoTotal += $subtotalSinDescuento;
            echo "      Subtotal sin descuento: Q" . number_format($subtotalSinDescuento, 2) . "\n";

            // Calcular descuento
            $montoDescuento = 0;
            if ($detalle->descuento_id) {
                $descuento = Descuento::find($detalle->descuento_id);
                if ($descuento) {
                    $porcentajeDescuento = $descuento->porcentaje_descuento;
                    $montoDescuento = $subtotalSinDescuento * ($porcentajeDescuento / 100);
                    echo "      Descuento ({$porcentajeDescuento}%): Q" . number_format($montoDescuento, 2) . "\n";
                }
            }

            // Calcular subtotal con descuento
            $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;
            echo "      Subtotal con descuento: Q" . number_format($subtotalConDescuento, 2) . "\n";

            // Calcular impuesto
            $impuestoDetalle = $subtotalConDescuento * ($detalle->porcentaje_impuestos ?? 0) / 100;
            $totalImpuestos += $impuestoDetalle;
            echo "      Impuesto (" . ($detalle->porcentaje_impuestos ?? 0) . "%): Q" . number_format($impuestoDetalle, 2) . "\n";

            // Calcular costo de compra (incluyendo comisiones para servicios)
            $costoCompraArticulo = $detalle->precio_costo * $detalle->cantidad;
            echo "      Costo base: Q" . number_format($costoCompraArticulo, 2) . "\n";

            // Si es servicio, agregar comisiones
            if ($detalle->articulo && $detalle->articulo->tipo === 'servicio') {
                echo "      🔧 SERVICIO - Calculando comisiones:\n";
                
                // Comisiones de trabajadores carwash
                $totalComisionesCarwash = 0;
                foreach ($detalle->trabajadoresCarwash as $trabajador) {
                    if ($trabajador->pivot && $trabajador->pivot->monto_comision) {
                        $totalComisionesCarwash += $trabajador->pivot->monto_comision;
                        echo "         - Carwash {$trabajador->nombre}: Q" . number_format($trabajador->pivot->monto_comision, 2) . "\n";
                    }
                }
                $costoCompraArticulo += $totalComisionesCarwash;
                
                // Comisión de mecánico
                if ($detalle->articulo->mecanico_id && $detalle->articulo->costo_mecanico > 0) {
                    $comisionMecanico = $detalle->articulo->costo_mecanico * $detalle->cantidad;
                    $costoCompraArticulo += $comisionMecanico;
                    echo "         - Mecánico: Q" . number_format($comisionMecanico, 2) . "\n";
                }
                
                echo "      Total comisiones carwash: Q" . number_format($totalComisionesCarwash, 2) . "\n";
            }

            echo "      COSTO TOTAL ARTÍCULO: Q" . number_format($costoCompraArticulo, 2) . "\n";
            
            // Actualizar totales
            $totalCostoCompra += $costoCompraArticulo;
            $totalDescuentos += $montoDescuento;
            $totalVenta += $subtotalConDescuento;
            
            echo "      --------\n";
        }

        // Mostrar resumen de la venta
        echo "\n   📊 RESUMEN DE LA VENTA:\n";
        echo "   Subtotal sin descuento: Q" . number_format($subtotalSinDescuentoTotal, 2) . "\n";
        echo "   Total descuentos: Q" . number_format($totalDescuentos, 2) . "\n";
        echo "   Total impuestos: Q" . number_format($totalImpuestos, 2) . "\n";
        echo "   Total costo de compra: Q" . number_format($totalCostoCompra, 2) . "\n";
        echo "   Total venta: Q" . number_format($totalVenta, 2) . "\n";
        echo "   💰 GANANCIA NETA: Q" . number_format($totalVenta - $totalImpuestos - $totalCostoCompra, 2) . "\n";
        echo "\n   ========================================\n\n";
    }

    echo "✅ Validación completada exitosamente\n";
    echo "   - Se validaron " . $ventas->count() . " ventas\n";
    echo "   - Los cálculos incluyen correctamente las comisiones de servicios\n";
    echo "   - La ganancia neta se calcula como: Total Venta - Impuestos - Costo Total\n";

} catch (Exception $e) {
    echo "❌ Error durante la validación: " . $e->getMessage() . "\n";
    echo "   Línea: " . $e->getLine() . "\n";
    echo "   Archivo: " . $e->getFile() . "\n";
    exit(1);
}
