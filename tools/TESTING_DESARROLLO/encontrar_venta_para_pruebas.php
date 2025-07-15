<?php
/**
 * Script para encontrar una venta activa con detalles para realizar pruebas
 */

require_once 'vendor/autoload.php';

// Configurar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Venta;
use App\Models\DetalleVenta;

echo "🔍 BUSCANDO VENTA ACTIVA PARA PRUEBAS\n";
echo "====================================\n\n";

// Buscar una venta reciente con detalles
$venta = Venta::with(['detalleVentas.articulo', 'detalleVentas.trabajadoresCarwash', 'cliente'])
    ->whereHas('detalleVentas')
    ->orderBy('created_at', 'desc')
    ->first();

if (!$venta) {
    echo "❌ No se encontraron ventas con detalles\n";
    exit;
}

echo "✅ VENTA ENCONTRADA:\n";
echo "ID: {$venta->id}\n";
echo "Número de Factura: " . ($venta->numero_factura ?: 'Sin factura') . "\n";
echo "Cliente: {$venta->cliente->nombre}\n";
echo "Fecha: {$venta->fecha}\n";
echo "Total: $" . number_format($venta->total, 2) . "\n";
echo "Detalles: {$venta->detalleVentas->count()}\n\n";

echo "📋 DETALLES DE LA VENTA:\n";
echo "========================\n";

foreach ($venta->detalleVentas as $index => $detalle) {
    echo "\n🔸 DETALLE " . ($index + 1) . ":\n";
    echo "   ID: {$detalle->id}\n";
    echo "   Servicio: {$detalle->articulo->nombre}\n";
    echo "   Cantidad: {$detalle->cantidad}\n";
    echo "   Precio: $" . number_format($detalle->precio, 2) . "\n";
    echo "   Subtotal: $" . number_format($detalle->subtotal, 2) . "\n";
    
    $trabajadores = $detalle->trabajadoresCarwash;
    echo "   Trabajadores: {$trabajadores->count()}\n";
    
    if ($trabajadores->count() > 0) {
        foreach ($trabajadores as $trabajador) {
            echo "   - {$trabajador->nombre} (ID: {$trabajador->id})\n";
        }
    } else {
        echo "   - Sin trabajadores asignados\n";
    }
}

echo "\n🌐 URL PARA EDITAR ESTA VENTA:\n";
echo "http://127.0.0.1:8000/edit-venta/{$venta->id}\n\n";

echo "🧪 SUGERENCIAS PARA PRUEBAS:\n";
echo "============================\n";
echo "1. Abrir la URL en el navegador\n";
echo "2. Verificar que se cargan los trabajadores existentes\n";
echo "3. Probar editar trabajadores en un detalle existente\n";
echo "4. Probar agregar un nuevo detalle con trabajadores\n";
echo "5. Verificar que los scripts de depuración muestran información\n";
echo "6. Enviar el formulario y verificar en logs que lleguen los datos\n\n";

// Mostrar también información sobre trabajadores disponibles
echo "👥 TRABAJADORES DISPONIBLES:\n";
echo "============================\n";

$trabajadores = \App\Models\Trabajador::all();
foreach ($trabajadores as $trabajador) {
    echo "- {$trabajador->nombre} (ID: {$trabajador->id})\n";
}

echo "\n✅ Script completado. Use la información anterior para realizar pruebas manuales.\n";
?>
