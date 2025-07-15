#!/usr/bin/env php
<?php

// Script para probar el cálculo de totales de ventas
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Venta;
use App\Models\Config;

echo "=== PRUEBA DE CÁLCULO DE TOTALES ===\n";

try {
    // Obtener la primera venta
    $venta = Venta::with(['detalleVentas.articulo.unidad', 'detalleVentas.descuento'])->first();
    
    if (!$venta) {
        echo "❌ No se encontraron ventas en la base de datos\n";
        exit(1);
    }
    
    echo "📋 Venta ID: {$venta->id}\n";
    echo "📋 Número de factura: " . ($venta->numero_factura ?? 'Sin número') . "\n";
    echo "📋 Fecha: {$venta->fecha}\n";
    echo "📋 Total de detalles: " . $venta->detalleVentas->count() . "\n";
    
    // Obtener configuración
    $config = Config::first();
    $currencySymbol = $config ? $config->currency_simbol : '$';
    
    echo "\n=== DETALLES DE LA VENTA ===\n";
    
    $totalCalculado = 0;
    
    foreach ($venta->detalleVentas as $detalle) {
        $articulo = $detalle->articulo;
        $articuloNombre = $articulo ? $articulo->nombre : 'Artículo no disponible';
        
        echo "- {$articuloNombre}\n";
        echo "  Cantidad: {$detalle->cantidad}\n";
        echo "  Subtotal en BD: {$detalle->sub_total}\n";
        
        // Verificar si el cálculo coincide
        if ($articulo) {
            $precioUnitario = $articulo->precio_venta;
            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;
            
            $montoDescuento = 0;
            if ($detalle->descuento_id && $detalle->descuento) {
                $porcentajeDescuento = $detalle->descuento->porcentaje_descuento;
                $montoDescuento = $subtotalSinDescuento * ($porcentajeDescuento / 100);
                echo "  Descuento ({$porcentajeDescuento}%): {$montoDescuento}\n";
            }
            
            $subtotalCalculado = $subtotalSinDescuento - $montoDescuento;
            echo "  Subtotal calculado: {$subtotalCalculado}\n";
            
            if (abs($detalle->sub_total - $subtotalCalculado) > 0.01) {
                echo "  ⚠️ DISCREPANCIA detectada!\n";
            }
        }
        
        $totalCalculado += $detalle->sub_total;
        echo "\n";
    }
    
    $totalEnBD = $venta->detalleVentas->sum('sub_total');
    
    echo "=== TOTALES ===\n";
    echo "Total en BD (sum): {$currencySymbol}.{$totalEnBD}\n";
    echo "Total calculado: {$currencySymbol}.{$totalCalculado}\n";
    echo "Total formateado: {$currencySymbol}." . number_format($totalCalculado, 2) . "\n";
    
    if (abs($totalEnBD - $totalCalculado) > 0.01) {
        echo "❌ DISCREPANCIA EN TOTALES!\n";
        exit(1);
    } else {
        echo "✅ Los totales coinciden correctamente\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== PRUEBA COMPLETADA ===\n";
