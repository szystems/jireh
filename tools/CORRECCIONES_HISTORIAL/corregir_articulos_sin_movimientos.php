<?php

// Script para corregir artículos que tienen stock pero no tienen movimientos registrados
require_once __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== CORRECCIÓN DE ARTÍCULOS SIN MOVIMIENTOS INICIALES ===\n";
echo "Fecha de ejecución: " . Carbon::now()->format('Y-m-d H:i:s') . "\n\n";

try {
    DB::beginTransaction();
    
    // Buscar artículos que tienen stock pero no tienen movimientos
    $articulosSinMovimientos = DB::table('articulos as a')
        ->leftJoin('movimientos_stock as m', 'a.id', '=', 'm.articulo_id')
        ->where('a.estado', 1)
        ->where('a.tipo', 'articulo')
        ->where('a.stock', '>', 0)
        ->whereNull('m.id')
        ->select('a.id', 'a.codigo', 'a.nombre', 'a.stock', 'a.created_at')
        ->get();
    
    echo "Artículos encontrados sin movimientos: " . $articulosSinMovimientos->count() . "\n";
    
    if ($articulosSinMovimientos->count() == 0) {
        echo "No hay artículos que requieran corrección.\n";
        DB::rollback();
        return;
    }
    
    $corregidos = 0;
    $errores = 0;
    
    foreach ($articulosSinMovimientos as $articulo) {
        try {
            // Crear movimiento inicial para este artículo
            DB::table('movimientos_stock')->insert([
                'articulo_id' => $articulo->id,
                'tipo' => 'AJUSTE_INICIAL',
                'stock_anterior' => 0,
                'stock_nuevo' => $articulo->stock,
                'cantidad' => $articulo->stock,
                'referencia_tipo' => 'AJUSTE_INICIAL',
                'referencia_id' => $articulo->id,
                'observaciones' => 'Movimiento inicial creado automáticamente para corregir inconsistencia de auditoría',
                'user_id' => 1, // Usuario administrador
                'created_at' => $articulo->created_at ? Carbon::parse($articulo->created_at) : Carbon::now()->subDays(30),
                'updated_at' => Carbon::now()
            ]);
            
            echo "✅ Corregido: {$articulo->codigo} - {$articulo->nombre} (Stock: {$articulo->stock})\n";
            $corregidos++;
            
        } catch (Exception $e) {
            echo "❌ Error en artículo {$articulo->id}: " . $e->getMessage() . "\n";
            $errores++;
        }
    }
    
    DB::commit();
    
    echo "\n=== RESUMEN DE CORRECCIÓN ===\n";
    echo "Total artículos procesados: " . $articulosSinMovimientos->count() . "\n";
    echo "Artículos corregidos: {$corregidos}\n";
    echo "Errores: {$errores}\n";
    echo "Estado: " . ($errores == 0 ? 'EXITOSO' : 'COMPLETADO CON ERRORES') . "\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "❌ ERROR CRÍTICO: " . $e->getMessage() . "\n";
    echo "La corrección fue revertida.\n";
}

echo "\nFecha de finalización: " . Carbon::now()->format('Y-m-d H:i:s') . "\n";
echo "=== FIN DE LA CORRECCIÓN ===\n";
