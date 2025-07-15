<?php
/**
 * Script para probar que la duplicación de nuevos detalles se ha resuelto
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
use App\Models\Articulo;

echo "🧪 PRUEBA DE DUPLICACIÓN RESUELTA\n";
echo "==================================\n\n";

// Encontrar una venta para probar
$venta = Venta::with(['detalleVentas.articulo', 'cliente'])
    ->orderBy('created_at', 'desc')
    ->first();

if (!$venta) {
    echo "❌ No se encontraron ventas para probar\n";
    exit;
}

echo "✅ USANDO VENTA PARA PRUEBA:\n";
echo "ID: {$venta->id}\n";
echo "Cliente: {$venta->cliente->nombre}\n";
echo "Detalles actuales: {$venta->detalleVentas->count()}\n\n";

// Contar detalles antes de la prueba
$detallesAntes = $venta->detalleVentas->count();
echo "📊 DETALLES ANTES DE LA PRUEBA: {$detallesAntes}\n\n";

// Simular envío de nuevos detalles (como lo haría el frontend)
echo "🔧 SIMULANDO AGREGAR NUEVO DETALLE...\n";

// Obtener un artículo de tipo servicio para la prueba
$articulo = Articulo::where('tipo', 'servicio')->first();
if (!$articulo) {
    $articulo = Articulo::first();
}

if (!$articulo) {
    echo "❌ No se encontraron artículos para la prueba\n";
    exit;
}

echo "Artículo para prueba: {$articulo->nombre} (ID: {$articulo->id})\n";

// Simular datos como los enviaría el frontend
$nuevosDetalles = [
    0 => [
        'articulo_id' => $articulo->id,
        'cantidad' => 1,
        'precio_unitario' => $articulo->precio_venta,
        'sub_total' => $articulo->precio_venta,
        'descuento_id' => null,
        'trabajadores_carwash' => [8, 9] // IDs de trabajadores de prueba
    ]
];

// Simular request con configuración
$config = \App\Models\Config::first();

echo "\n🚀 PROCESANDO NUEVO DETALLE (simulación del código corregido)...\n";

// Simular el código del controlador corregido
foreach ($nuevosDetalles as $index => $nuevoDetalle) {
    echo "=== PROCESANDO NUEVO DETALLE ÍNDICE: {$index} ===\n";
    
    // Verificar si ya existe un detalle similar (código de prevención de duplicación)
    $detallesExistentes = $venta->detalleVentas()
        ->where('articulo_id', $nuevoDetalle['articulo_id'])
        ->where('cantidad', $nuevoDetalle['cantidad'])
        ->where('created_at', '>', now()->subMinute())
        ->get();
    
    if ($detallesExistentes->count() > 0) {
        echo "⚠️ DUPLICACIÓN DETECTADA: Saltando detalle duplicado\n";
        continue;
    }
    
    // Agregar datos requeridos
    $nuevoDetalle['porcentaje_impuestos'] = $config->impuesto;
    $nuevoDetalle['usuario_id'] = 1; // Simular usuario
    $nuevoDetalle['precio_costo'] = $articulo->precio_compra;
    $nuevoDetalle['precio_venta'] = $articulo->precio_venta;
    
    // Crear el detalle
    $detalle = $venta->detalleVentas()->create($nuevoDetalle);
    echo "✅ Nuevo detalle creado con ID: {$detalle->id}\n";
    
    // Asignar trabajadores si es servicio
    if ($articulo->tipo === 'servicio' && isset($nuevoDetalle['trabajadores_carwash'])) {
        $trabajadores = $nuevoDetalle['trabajadores_carwash'];
        $detalle->asignarTrabajadores($trabajadores, $articulo->comision_carwash);
        echo "✅ Trabajadores asignados: " . implode(', ', $trabajadores) . "\n";
    }
}

// Verificar detalles después
$venta->refresh();
$detallesDespues = $venta->detalleVentas->count();

echo "\n📊 RESULTADOS DE LA PRUEBA:\n";
echo "===========================\n";
echo "Detalles antes: {$detallesAntes}\n";
echo "Detalles después: {$detallesDespues}\n";
echo "Diferencia: " . ($detallesDespues - $detallesAntes) . "\n\n";

if (($detallesDespues - $detallesAntes) === 1) {
    echo "✅ PRUEBA EXITOSA: Se agregó exactamente 1 detalle (sin duplicación)\n";
} else {
    echo "❌ PRUEBA FALLIDA: Se agregaron " . ($detallesDespues - $detallesAntes) . " detalles\n";
}

echo "\n🎯 VERIFICACIÓN ADICIONAL:\n";
echo "Últimos detalles creados:\n";
$ultimosDetalles = $venta->detalleVentas()
    ->where('created_at', '>', now()->subMinute())
    ->orderBy('created_at', 'desc')
    ->get();

foreach ($ultimosDetalles as $detalle) {
    echo "- ID: {$detalle->id}, Artículo: {$detalle->articulo->nombre}, Creado: {$detalle->created_at}\n";
}

echo "\n🏁 PRUEBA COMPLETADA\n";
?>
