<?php

// Script para corregir inconsistencias de stock inicial
// Este script genera movimientos_stock retroactivos para artículos que tienen stock
// pero no tienen movimientos registrados (inconsistencias de auditoría)

require_once __DIR__ . '/../../vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Articulo;
use App\Models\MovimientoStock;
use Illuminate\Support\Facades\DB;

echo "=== CORRECCIÓN DE INCONSISTENCIAS DE STOCK INICIAL ===\n\n";

try {
    // Lista de artículos con inconsistencias (IDs del 2 al 49 según tu reporte)
    $articulosInconsistentes = range(2, 49);
    
    $corregidos = 0;
    $errores = 0;
    
    DB::beginTransaction();
    
    foreach ($articulosInconsistentes as $articuloId) {
        // Verificar que el artículo existe y tiene stock
        $articulo = Articulo::find($articuloId);
        
        if (!$articulo) {
            echo "❌ Artículo ID {$articuloId} no encontrado\n";
            $errores++;
            continue;
        }
        
        if ($articulo->stock <= 0) {
            echo "⚠️  Artículo ID {$articuloId} ({$articulo->codigo}) no tiene stock actual\n";
            continue;
        }
        
        // Verificar si ya existe algún movimiento para este artículo
        $movimientosExistentes = MovimientoStock::where('articulo_id', $articuloId)->count();
        
        if ($movimientosExistentes > 0) {
            echo "ℹ️  Artículo ID {$articuloId} ({$articulo->codigo}) ya tiene {$movimientosExistentes} movimientos registrados\n";
            continue;
        }
        
        // Crear movimiento de stock inicial
        $movimiento = MovimientoStock::create([
            'articulo_id' => $articulo->id,
            'tipo' => 'AJUSTE_INICIAL',
            'stock_anterior' => 0,
            'stock_nuevo' => $articulo->stock,
            'cantidad' => $articulo->stock,
            'referencia_tipo' => 'AJUSTE_INICIAL',
            'referencia_id' => $articulo->id,
            'observaciones' => 'Ajuste inicial para corregir inconsistencia de auditoría - Stock existente sin movimiento registrado',
            'user_id' => 1, // Usuario administrador por defecto
            'created_at' => $articulo->created_at, // Usar la fecha de creación del artículo
            'updated_at' => $articulo->created_at,
        ]);
        
        if ($movimiento) {
            echo "✅ Movimiento creado para Artículo ID {$articuloId} ({$articulo->codigo}) - Stock: {$articulo->stock}\n";
            $corregidos++;
        } else {
            echo "❌ Error al crear movimiento para Artículo ID {$articuloId}\n";
            $errores++;
        }
    }
    
    DB::commit();
    
    echo "\n=== RESUMEN ===\n";
    echo "✅ Artículos corregidos: {$corregidos}\n";
    echo "❌ Errores: {$errores}\n";
    echo "📊 Total procesados: " . count($articulosInconsistentes) . "\n\n";
    
    if ($corregidos > 0) {
        echo "🎉 ¡Corrección completada! Ahora puedes ejecutar la auditoría de stock nuevamente.\n";
        echo "   Las inconsistencias deberían haberse reducido significativamente.\n\n";
    }
    
    echo "ℹ️  Nota: Los movimientos creados tienen tipo 'AJUSTE_INICIAL' y fecha de creación\n";
    echo "   igual a la fecha de creación del artículo para mantener coherencia histórica.\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "   La transacción ha sido revertida.\n";
}

echo "\n=== FIN DEL SCRIPT ===\n";
