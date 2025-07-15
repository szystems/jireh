<?php

// Cargar el autoloader y la configuración de Laravel
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ANÁLISIS COMISIONES CAR WASH ===\n\n";

// 1. Verificar artículos de tipo servicio con comisión
echo "1. ARTÍCULOS DE TIPO SERVICIO:\n";
$articulosServicio = DB::table('articulos')
    ->where('tipo', 'servicio')
    ->get(['id', 'nombre', 'codigo', 'precio_venta', 'comision_carwash']);

foreach($articulosServicio as $articulo) {
    echo "- {$articulo->codigo}: {$articulo->nombre} - Precio: Q{$articulo->precio_venta} - Comisión: Q{$articulo->comision_carwash}\n";
}

// 2. Verificar trabajadores de tipo carwash
echo "\n2. TRABAJADORES CAR WASH:\n";
$trabajadoresCarwash = DB::table('trabajadors as t')
    ->join('tipo_trabajadors as tt', 't.tipo_trabajador_id', '=', 'tt.id')
    ->where('tt.nombre', 'like', '%carwash%')
    ->get(['t.id', 't.nombre', 't.apellido', 'tt.nombre as tipo']);

foreach($trabajadoresCarwash as $trabajador) {
    echo "- ID: {$trabajador->id}, Nombre: {$trabajador->nombre} {$trabajador->apellido}, Tipo: {$trabajador->tipo}\n";
}

// 3. Verificar asignaciones en trabajador_detalle_venta
echo "\n3. ASIGNACIONES DE TRABAJADORES CAR WASH:\n";
$asignaciones = DB::table('trabajador_detalle_venta as tdv')
    ->join('trabajadors as t', 'tdv.trabajador_id', '=', 't.id')
    ->join('tipo_trabajadors as tt', 't.tipo_trabajador_id', '=', 'tt.id')
    ->join('detalle_ventas as dv', 'tdv.detalle_venta_id', '=', 'dv.id')
    ->join('articulos as a', 'dv.articulo_id', '=', 'a.id')
    ->join('ventas as v', 'dv.venta_id', '=', 'v.id')
    ->where('tt.nombre', 'like', '%carwash%')
    ->where('a.tipo', 'servicio')
    ->get(['t.nombre', 't.apellido', 'a.nombre as servicio', 'v.fecha', 'dv.cantidad', 'a.comision_carwash', 'tdv.monto_comision']);

foreach($asignaciones as $asignacion) {
    echo "- {$asignacion->nombre} {$asignacion->apellido}: {$asignacion->servicio} - Fecha: {$asignacion->fecha} - Cant: {$asignacion->cantidad} - Comisión Artículo: Q{$asignacion->comision_carwash} - Monto Guardado: Q{$asignacion->monto_comision}\n";
}

// 4. Calcular comisiones correctamente
echo "\n4. CÁLCULO CORRECTO DE COMISIONES:\n";
$fechaInicio = '2025-01-01';
$fechaFin = '2025-12-31';

$comisionesCarwash = DB::table('trabajador_detalle_venta as tdv')
    ->join('trabajadors as t', 'tdv.trabajador_id', '=', 't.id')
    ->join('tipo_trabajadors as tt', 't.tipo_trabajador_id', '=', 'tt.id')
    ->join('detalle_ventas as dv', 'tdv.detalle_venta_id', '=', 'dv.id')
    ->join('articulos as a', 'dv.articulo_id', '=', 'a.id')
    ->join('ventas as v', 'dv.venta_id', '=', 'v.id')
    ->where('tt.nombre', 'like', '%carwash%')
    ->where('a.tipo', 'servicio')
    ->where('v.estado', 1)
    ->whereBetween('v.fecha', [$fechaInicio, $fechaFin])
    ->select(
        't.id',
        't.nombre',
        't.apellido',
        DB::raw('COUNT(DISTINCT v.id) as total_ventas'),
        DB::raw('COUNT(tdv.id) as total_servicios'),
        DB::raw('SUM(tdv.monto_comision) as comision_calculada')
    )
    ->groupBy('t.id', 't.nombre', 't.apellido')
    ->get();

foreach($comisionesCarwash as $comision) {
    echo "- {$comision->nombre} {$comision->apellido}: {$comision->total_servicios} servicios - Total comisión: Q{$comision->comision_calculada}\n";
}

echo "\n=== FIN DEL ANÁLISIS ===\n";
