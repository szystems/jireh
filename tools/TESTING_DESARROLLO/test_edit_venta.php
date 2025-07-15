<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\DetalleVenta;

echo "=== PRUEBA DEL FORMULARIO DE EDICIÓN DE VENTA ===\n\n";

try {
    // 1. Buscar una venta existente con detalles
    $venta = Venta::with(['cliente', 'vehiculo', 'detalleVentas.articulo', 'detalleVentas.trabajadoresCarwash'])
                  ->whereHas('detalleVentas')
                  ->first();
    
    if (!$venta) {
        echo "❌ No se encontraron ventas con detalles para probar\n";
        exit(1);
    }
    
    echo "✅ Venta encontrada: ID {$venta->id}\n";
    echo "   - Cliente: {$venta->cliente->nombre}\n";
    echo "   - Vehículo: " . ($venta->vehiculo ? "{$venta->vehiculo->marca} {$venta->vehiculo->modelo}" : "Sin vehículo") . "\n";
    echo "   - Detalles: {$venta->detalleVentas->count()}\n\n";
    
    // 2. Verificar que los campos necesarios existen
    echo "=== VERIFICACIÓN DE CAMPOS ===\n";
    
    $camposRequeridos = [
        'numero_factura' => $venta->numero_factura,
        'fecha' => $venta->fecha,
        'cliente_id' => $venta->cliente_id,
        'vehiculo_id' => $venta->vehiculo_id,
        'tipo_venta' => $venta->tipo_venta,
        'estado_pago' => $venta->estado_pago,
        'estado' => $venta->estado,
        'usuario_id' => $venta->usuario_id
    ];
    
    foreach ($camposRequeridos as $campo => $valor) {
        $estado = $valor !== null ? "✅" : "⚠️ ";
        echo "   {$estado} {$campo}: " . ($valor ?? 'NULL') . "\n";
    }
    
    // 3. Verificar detalles de la venta
    echo "\n=== VERIFICACIÓN DE DETALLES ===\n";
    
    foreach ($venta->detalleVentas as $index => $detalle) {
        echo "   Detalle " . ($index + 1) . ":\n";
        echo "     - ID: {$detalle->id}\n";
        echo "     - Artículo: " . ($detalle->articulo ? $detalle->articulo->nombre : 'Sin artículo') . "\n";
        echo "     - Cantidad: {$detalle->cantidad}\n";
        echo "     - Subtotal: {$detalle->sub_total}\n";
        echo "     - Tipo artículo: " . ($detalle->articulo ? $detalle->articulo->tipo : 'N/A') . "\n";
        
        if ($detalle->articulo && $detalle->articulo->tipo === 'servicio') {
            $trabajadores = $detalle->trabajadoresCarwash;
            echo "     - Trabajadores asignados: {$trabajadores->count()}\n";
            if ($trabajadores->count() > 0) {
                foreach ($trabajadores as $trabajador) {
                    echo "       * {$trabajador->nombre_completo}\n";
                }
            }
        }
        echo "\n";
    }
    
    // 4. Simular datos de formulario edit con old()
    echo "=== SIMULACIÓN DE PRESERVACIÓN DE DATOS ===\n";
    
    // Simular errores de validación con old() data
    $oldData = [
        'numero_factura' => 'FACTURA-EDITADA-001',
        'fecha' => '2025-07-08',
        'cliente_id' => $venta->cliente_id,
        'vehiculo_id' => $venta->vehiculo_id,
        'tipo_venta' => 'Car Wash',
        'estado_pago' => 'pagado'
    ];
    
    echo "   Datos simulados con old():\n";
    foreach ($oldData as $campo => $valor) {
        $valorFinal = $valor ?? $venta->$campo;
        echo "     - {$campo}: {$valorFinal}\n";
    }
    
    // 5. Verificar datos de configuración JavaScript
    echo "\n=== VERIFICACIÓN DE CONFIGURACIÓN JAVASCRIPT ===\n";
    
    $config = app('App\Models\Config')::first();
    $detallesOriginales = $venta->detalleVentas->mapWithKeys(function ($detalle) {
        return [$detalle->id => [
            'precio_unitario' => (float) ($detalle->precio_venta ?? 0),
            'cantidad' => (float) ($detalle->cantidad ?? 0),
            'descuento_id' => $detalle->descuento_id ?? null,
            'descuento_porcentaje' => (float) ($detalle->descuento_porcentaje ?? 0),
            'trabajadores_asignados' => ($detalle->trabajadoresCarwash ?? collect())->pluck('id')->toArray(),
            'articulo_tipo' => $detalle->articulo->tipo ?? 'producto',
            'unidad_tipo' => $detalle->articulo->unidad->tipo ?? 'decimal'
        ]];
    });
    
    echo "   ✅ Símbolo de moneda: {$config->currency_simbol}\n";
    echo "   ✅ Vehículo ID original: {$venta->vehiculo_id}\n";
    echo "   ✅ Detalles originales: " . count($detallesOriginales) . " detalles configurados\n";
    
    // 6. Verificar rutas necesarias
    echo "\n=== VERIFICACIÓN DE RUTAS ===\n";
    
    $rutasNecesarias = [
        'admin.ventas.index',
        'admin.ventas.update',
        'admin.ventas.detalle.update',
        'admin.ventas.detalle.destroy',
        'admin.ventas.detalle.restore',
        'api.articulos.para_venta'
    ];
    
    foreach ($rutasNecesarias as $ruta) {
        try {
            $url = route($ruta, ['venta' => $venta->id, 'detalle' => 1]);
            echo "   ✅ {$ruta}: {$url}\n";
        } catch (Exception $e) {
            echo "   ❌ {$ruta}: Error - {$e->getMessage()}\n";
        }
    }
    
    echo "\n✅ PRUEBA DEL FORMULARIO DE EDICIÓN COMPLETADA SIN ERRORES CRÍTICOS\n";
    
} catch (Exception $e) {
    echo "❌ ERROR EN LA PRUEBA: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . "\n";
    echo "   Línea: " . $e->getLine() . "\n";
    exit(1);
}
