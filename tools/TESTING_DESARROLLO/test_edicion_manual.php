<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Trabajador;
use App\Models\Comision;
use App\Models\TrabajadorDetalleVenta;
use Illuminate\Support\Facades\DB;

echo "\n=== TEST MANUAL EDICIÓN DE VENTA ===\n";

// Usar la venta de prueba que creamos antes
$venta = Venta::find(41);

if (!$venta) {
    echo "No se encontró la venta de prueba ID 41\n";
    exit;
}

echo "Venta encontrada: ID {$venta->id}\n";

$detalle = $venta->detalleVentas->first();
echo "Detalle encontrado: ID {$detalle->id}\n";

// Ver trabajadores actuales
$trabajadoresActuales = $detalle->trabajadoresCarwash;
echo "Trabajadores actuales: " . $trabajadoresActuales->count() . "\n";
foreach($trabajadoresActuales as $trabajador) {
    echo "  - {$trabajador->nombre_completo} (ID: {$trabajador->id})\n";
}

// Ver comisiones actuales
$comisionesActuales = Comision::where('detalle_venta_id', $detalle->id)->get();
echo "Comisiones actuales: " . $comisionesActuales->count() . "\n";
foreach($comisionesActuales as $comision) {
    $trabajador = \App\Models\Trabajador::find($comision->commissionable_id);
    echo "  - {$trabajador->nombre_completo}: Q{$comision->monto}\n";
}

// Intentar cambiar a un trabajador diferente
$todosLosTrabajadores = \App\Models\Trabajador::whereHas('tipoTrabajador', function($query) {
    $query->where('nombre', 'Car Wash');
})->get();

$trabajadorNuevo = $todosLosTrabajadores->whereNotIn('id', $trabajadoresActuales->pluck('id'))->first();

if (!$trabajadorNuevo) {
    echo "No hay más trabajadores Car Wash disponibles para cambiar\n";
    exit;
}

echo "\nCambiando a trabajador: {$trabajadorNuevo->nombre_completo} (ID: {$trabajadorNuevo->id})\n";

DB::beginTransaction();

// Simular lo que haría el controlador
$detalle->asignarTrabajadores([$trabajadorNuevo->id], $detalle->articulo->comision_carwash);

// Verificar el cambio
$detalle = $detalle->fresh();
$nuevosTrabajaadores = $detalle->trabajadoresCarwash;
echo "Nuevos trabajadores: " . $nuevosTrabajaadores->count() . "\n";
foreach($nuevosTrabajaadores as $trabajador) {
    echo "  - {$trabajador->nombre_completo} (ID: {$trabajador->id})\n";
}

// Regenerar comisiones
$comisiones = $detalle->generarComisionesCarwash(true);
echo "Comisiones regeneradas: " . $comisiones->count() . "\n";

// Verificar en BD
$comisionesFinales = Comision::where('detalle_venta_id', $detalle->id)->get();
echo "Comisiones finales en BD: " . $comisionesFinales->count() . "\n";
foreach($comisionesFinales as $comision) {
    $trabajador = \App\Models\Trabajador::find($comision->commissionable_id);
    echo "  - {$trabajador->nombre_completo}: Q{$comision->monto}\n";
}

DB::rollback();

echo "\n=== TEST COMPLETADO (ROLLBACK) ===\n";

?>
