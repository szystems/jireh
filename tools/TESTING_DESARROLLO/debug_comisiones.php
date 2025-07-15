<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\{Venta, Cliente, Articulo, Trabajador, DetalleVenta, TrabajadorDetalleVenta, Comision};

echo "DEBUG: Simulando exactamente la Prueba 2\n";
echo "=========================================\n";

// Crear una venta simple
$venta = Venta::create([
    'cliente_id' => 1,
    'fecha' => now()->format('Y-m-d'),
    'tipo_venta' => 'Car Wash',
    'usuario_id' => 1,
    'estado' => true,
    'estado_pago' => 'pendiente'
]);

$servicio = Articulo::where('tipo', 'servicio')->where('comision_carwash', '>', 0)->first();
$detalle = $venta->detalleVentas()->create([
    'articulo_id' => $servicio->id,
    'cantidad' => 1,
    'precio_costo' => $servicio->precio_compra,
    'precio_venta' => $servicio->precio_venta,
    'usuario_id' => 1,
    'sub_total' => $servicio->precio_venta,
    'porcentaje_impuestos' => 0
]);

echo "Venta creada: {$venta->id}\n";
echo "Detalle creado: {$detalle->id}\n";

// PASO 1: Asignar trabajadores iniciales (como en Prueba 1)
$trabajadoresIniciales = Trabajador::whereHas('tipoTrabajador', function($q) { 
    $q->where('nombre', 'Car Wash'); 
})->take(2)->get();

$trabajadorIds = $trabajadoresIniciales->pluck('id')->toArray();
$detalle->asignarTrabajadores($trabajadorIds, $servicio->comision_carwash);

echo "PASO 1: Trabajadores iniciales asignados: " . implode(', ', $trabajadorIds) . "\n";

$comisionesIniciales = $detalle->generarComisionesCarwash();
echo "Comisiones iniciales generadas: {$comisionesIniciales->count()}\n";

$comisionesEnBD = Comision::where('detalle_venta_id', $detalle->id)->count();
echo "Comisiones en BD después del paso 1: {$comisionesEnBD}\n";

// PASO 2: Cambiar trabajadores (como en Prueba 2)
echo "\nPASO 2: Cambiando trabajadores...\n";

$nuevoTrabajador = Trabajador::whereHas('tipoTrabajador', function($q) { 
    $q->where('nombre', 'Car Wash'); 
})->whereNotIn('id', $trabajadorIds)->first();

if ($nuevoTrabajador) {
    echo "Nuevo trabajador seleccionado: {$nuevoTrabajador->nombre_completo} (ID: {$nuevoTrabajador->id})\n";
    
    // Simular exactamente lo que hace la Prueba 2
    $detalle->asignarTrabajadores([$nuevoTrabajador->id], $servicio->comision_carwash);
    
    echo "Trabajador reasignado\n";
    
    // Verificar asignaciones actuales
    $nuevasAsignaciones = $detalle->fresh()->trabajadoresCarwash;
    echo "Nuevas asignaciones: {$nuevasAsignaciones->count()}\n";
    foreach($nuevasAsignaciones as $asignacion) {
        echo "  - {$asignacion->nombre_completo} (Comisión: {$asignacion->pivot->monto_comision})\n";
    }
    
    // Aquí es donde falla en la prueba: forzar regeneración
    echo "\nProbando forzar regeneración...\n";
    $nuevasComisiones = $detalle->generarComisionesCarwash(true);
    echo "Comisiones regeneradas (método devuelve): {$nuevasComisiones->count()}\n";
    
    $comisionesEnBD = Comision::where('detalle_venta_id', $detalle->id)->count();
    echo "Comisiones en BD después de regenerar: {$comisionesEnBD}\n";
    
    if ($comisionesEnBD == 0) {
        echo "\n¡PROBLEMA ENCONTRADO! No se están creando las comisiones después de forzar regeneración\n";
        
        // Debug paso a paso
        echo "Debug paso a paso:\n";
        echo "1. Eliminando comisiones manualmente...\n";
        Comision::where('detalle_venta_id', $detalle->id)->where('tipo_comision', 'carwash')->delete();
        
        echo "2. Verificando asignaciones en tabla pivot:\n";
        $asignacionesEnPivot = TrabajadorDetalleVenta::where('detalle_venta_id', $detalle->id)->get();
        foreach($asignacionesEnPivot as $asig) {
            echo "   - Trabajador {$asig->trabajador_id}, Monto: {$asig->monto_comision}\n";
            $comisionGenerada = $asig->generarComision();
            echo "     Comisión generada: " . ($comisionGenerada ? "Sí (ID: {$comisionGenerada->id})" : 'No') . "\n";
        }
        
        $comisionesFinales = Comision::where('detalle_venta_id', $detalle->id)->count();
        echo "3. Comisiones finales en BD: {$comisionesFinales}\n";
    }
} else {
    echo "No hay más trabajadores Car Wash disponibles para la prueba\n";
}

// Limpiar
Comision::where('venta_id', $venta->id)->delete();
$venta->detalleVentas()->delete();
$venta->delete();

echo "\nLimpieza completada\n";
