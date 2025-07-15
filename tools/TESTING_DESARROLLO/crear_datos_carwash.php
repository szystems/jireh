<?php

// Cargar el autoloader y la configuración de Laravel
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "CREANDO DATOS DE PRUEBA PARA COMISIONES CAR WASH..." . PHP_EOL;

// 1. Crear una venta
$venta = DB::table('ventas')->insertGetId([
    'cliente_id' => 1,
    'vehiculo_id' => 1,
    'numero_factura' => 'TEST-CARWASH-001',
    'fecha' => Carbon::now()->format('Y-m-d'),
    'tipo_venta' => 'Car Wash',
    'usuario_id' => 1,
    'estado' => 1,
    'estado_pago' => 'pendiente',
    'created_at' => Carbon::now(),
    'updated_at' => Carbon::now()
]);

echo "✓ Venta creada con ID: $venta" . PHP_EOL;

// 2. Obtener un artículo de tipo servicio
$articulo = DB::table('articulos')->where('tipo', 'servicio')->first();
if(!$articulo) {
    // Crear un artículo de servicio de Car Wash
    $articulo_id = DB::table('articulos')->insertGetId([
        'nombre' => 'Lavado Completo',
        'codigo' => 'CARWASH-001',
        'precio_venta' => 50.00,
        'precio_compra' => 0.00,
        'stock_minimo' => 0,
        'stock' => 999,
        'stock_inicial' => 999,
        'categoria_id' => 1,
        'unidad_id' => 1,
        'tipo' => 'servicio',
        'estado' => 1,
        'comision_carwash' => 10.00,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now()
    ]);
    echo "✓ Artículo de servicio creado con ID: $articulo_id" . PHP_EOL;
} else {
    $articulo_id = $articulo->id;
    echo "✓ Usando artículo existente ID: $articulo_id" . PHP_EOL;
}

// 3. Crear detalle de venta
$detalle = DB::table('detalle_ventas')->insertGetId([
    'venta_id' => $venta,
    'articulo_id' => $articulo_id,
    'cantidad' => 1,
    'precio_costo' => 0.00,
    'precio_venta' => 50.00,
    'descuento_id' => null,
    'usuario_id' => 1,
    'sub_total' => 50.00,
    'porcentaje_impuestos' => 0.00,
    'created_at' => Carbon::now(),
    'updated_at' => Carbon::now()
]);

echo "✓ Detalle de venta creado con ID: $detalle" . PHP_EOL;

// 4. Obtener trabajadores Car Wash
$trabajadores = DB::table('trabajadors as t')
    ->join('tipo_trabajadors as tt', 't.tipo_trabajador_id', '=', 'tt.id')
    ->where('tt.nombre', 'like', '%Car Wash%')
    ->select('t.id', 't.nombre', 't.apellido')
    ->get();

if($trabajadores->count() > 0) {
    foreach($trabajadores as $trabajador) {
        // 5. Crear asignación de trabajador
        DB::table('trabajador_detalle_venta')->insert([
            'trabajador_id' => $trabajador->id,
            'detalle_venta_id' => $detalle,
            'monto_comision' => 10.00,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        echo "✓ Asignación creada para trabajador: $trabajador->nombre $trabajador->apellido" . PHP_EOL;
    }
} else {
    echo "❌ No se encontraron trabajadores Car Wash." . PHP_EOL;
}

echo PHP_EOL . "✅ DATOS DE PRUEBA CREADOS EXITOSAMENTE" . PHP_EOL;
