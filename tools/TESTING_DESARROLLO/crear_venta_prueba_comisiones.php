#!/usr/bin/env php
<?php

/**
 * Script para crear venta de prueba con comisiones reales
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Articulo;
use App\Models\Cliente;
use App\Models\Trabajador;

echo "=== CREANDO VENTA DE PRUEBA CON COMISIONES REALES ===\n\n";

try {
    // Buscar datos necesarios
    $cliente = Cliente::first();
    $servicio = Articulo::where('tipo', 'servicio')->first();
    $trabajador = Trabajador::first();

    if (!$cliente || !$servicio || !$trabajador) {
        echo "❌ No se encontraron datos necesarios:\n";
        echo "   Cliente: " . ($cliente ? "✅" : "❌") . "\n";
        echo "   Servicio: " . ($servicio ? "✅" : "❌") . "\n";
        echo "   Trabajador: " . ($trabajador ? "✅" : "❌") . "\n";
        exit(1);
    }

    echo "📋 Datos encontrados:\n";
    echo "   Cliente: {$cliente->nombre}\n";
    echo "   Servicio: {$servicio->nombre}\n";
    echo "   Precio venta: Q" . number_format($servicio->precio_venta, 2) . "\n";
    echo "   Precio costo: Q" . number_format($servicio->precio_compra, 2) . "\n";
    echo "   Costo mecánico: Q" . number_format($servicio->costo_mecanico, 2) . "\n";
    echo "   Trabajador: {$trabajador->nombre}\n\n";

    // Crear venta
    $venta = Venta::create([
        'cliente_id' => $cliente->id,
        'numero_factura' => 'TEST-COSTO-' . time(),
        'fecha' => now(),
        'tipo_venta' => 'Car Wash',
        'estado_pago' => 'pendiente',
        'estado' => true,
        'usuario_id' => 1,
        'total' => 0
    ]);

    // Crear detalle
    $detalle = DetalleVenta::create([
        'venta_id' => $venta->id,
        'articulo_id' => $servicio->id,
        'cantidad' => 2,
        'precio_costo' => $servicio->precio_compra,
        'precio_venta' => $servicio->precio_venta,
        'sub_total' => $servicio->precio_venta * 2,
        'porcentaje_impuestos' => 12,
        'usuario_id' => 1
    ]);

    // Asignar trabajador con comisión
    $detalle->trabajadoresCarwash()->attach($trabajador->id, [
        'monto_comision' => 75.00
    ]);

    echo "✅ Venta creada exitosamente!\n";
    echo "   ID: {$venta->id}\n";
    echo "   Número factura: {$venta->numero_factura}\n";
    echo "   Cantidad: 2\n";
    echo "   Comisión carwash: Q75.00\n";
    echo "   Impuesto: 12%\n\n";

    // Calcular costos como en la vista
    $cantidad = 2;
    $precioUnitario = $servicio->precio_venta;
    $precioCosto = $servicio->precio_compra;

    $subtotalSinDescuento = $precioUnitario * $cantidad;
    $subtotalConDescuento = $subtotalSinDescuento; // sin descuento
    $impuestoDetalle = $subtotalConDescuento * 0.12;

    // Costo total
    $costoCompraArticulo = $precioCosto * $cantidad;
    $costoCompraArticulo += 75.00; // comisión carwash
    $costoCompraArticulo += $servicio->costo_mecanico * $cantidad; // comisión mecánico

    echo "📊 CÁLCULOS ESPERADOS:\n";
    echo "   Subtotal sin descuento: Q" . number_format($subtotalSinDescuento, 2) . "\n";
    echo "   Impuesto (12%): Q" . number_format($impuestoDetalle, 2) . "\n";
    echo "   Total venta: Q" . number_format($subtotalConDescuento, 2) . "\n";
    echo "   Costo base: Q" . number_format($precioCosto * $cantidad, 2) . "\n";
    echo "   + Comisión carwash: Q75.00\n";
    echo "   + Comisión mecánico: Q" . number_format($servicio->costo_mecanico * $cantidad, 2) . "\n";
    echo "   = Costo total: Q" . number_format($costoCompraArticulo, 2) . "\n";
    echo "   💰 Ganancia neta: Q" . number_format($subtotalConDescuento - $impuestoDetalle - $costoCompraArticulo, 2) . "\n\n";

    echo "🌐 URL para ver en el navegador:\n";
    echo "   http://localhost:8000/show-venta/{$venta->id}\n";
    echo "   (Asegúrate de que el servidor esté corriendo)\n\n";

    echo "✅ Prueba completada exitosamente!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   Línea: " . $e->getLine() . "\n";
    echo "   Archivo: " . $e->getFile() . "\n";
    exit(1);
}
