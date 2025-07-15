<?php

/**
 * TEST DIRECTO DE EDICIÓN DE TRABAJADORES
 * Simula exactamente lo que debe hacer el sistema cuando se editan trabajadores
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TEST DIRECTO EDICIÓN TRABAJADORES ===\n";

try {
    DB::beginTransaction();    // 1. BUSCAR UNA VENTA CON TRABAJADORES PARA PROBAR
    $venta = \App\Models\Venta::find(41); // Usar la venta que sabemos que existe
    if (!$venta) {
        echo "❌ No se encontró venta 41\n";
        exit;
    }
    
    $detalle = $venta->detalleVentas->first();
    echo "✅ Venta encontrada: ID {$venta->id}, Detalle: ID {$detalle->id}\n";
      // 2. VER ESTADO ACTUAL
    $trabajadoresActuales = $detalle->trabajadoresCarwash ? $detalle->trabajadoresCarwash->pluck('id')->toArray() : [];
    $comisionesActuales = $detalle->comisiones ? $detalle->comisiones->count() : 0;
    echo "📊 Trabajadores actuales: " . implode(', ', $trabajadoresActuales) . "\n";
    echo "📊 Comisiones actuales: {$comisionesActuales}\n";
    
    // 3. SIMULAR CAMBIO DE TRABAJADORES (lo que debería hacer el formulario)
    $nuevosTrabajadores = [9]; // Cambiar a trabajador 9
    echo "🔄 Cambiando a trabajadores: " . implode(', ', $nuevosTrabajadores) . "\n";
    
    // 4. APLICAR EL CAMBIO (exactamente como lo haría el controlador)
    $articulo = $detalle->articulo;
    if ($articulo && $articulo->tipo === 'servicio') {
        // Esto es exactamente lo que debe hacer el controlador
        $detalle->asignarTrabajadores($nuevosTrabajadores, $articulo->comision_carwash);
        echo "✅ Trabajadores asignados\n";
        
        // Verificar el resultado
        $trabajadoresNuevos = $detalle->fresh()->trabajadoresCarwash ? $detalle->fresh()->trabajadoresCarwash->pluck('id')->toArray() : [];
        $comisionesNuevas = $detalle->fresh()->comisiones ? $detalle->fresh()->comisiones->count() : 0;
        echo "📊 Trabajadores después: " . implode(', ', $trabajadoresNuevos) . "\n";
        echo "📊 Comisiones después: {$comisionesNuevas}\n";
        
        if ($trabajadoresNuevos == $nuevosTrabajadores && $comisionesNuevas == count($nuevosTrabajadores)) {
            echo "✅ ÉXITO: Los trabajadores se cambiaron correctamente\n";
        } else {
            echo "❌ ERROR: Los trabajadores no se cambiaron como se esperaba\n";
        }
    } else {
        echo "❌ ERROR: El artículo no es un servicio\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
} finally {
    DB::rollBack();
    echo "\n=== TEST COMPLETADO (ROLLBACK) ===\n";
}

echo "\n=== CONCLUSIÓN ===\n";
echo "Si este test muestra ÉXITO, el problema está en:\n";
echo "1. El frontend no envía los datos correctamente\n";
echo "2. El controlador no recibe los datos en el formato correcto\n";
echo "3. El controlador no llama al método asignarTrabajadores\n";
echo "\nSi muestra ERROR, el problema está en el modelo DetalleVenta.\n";
