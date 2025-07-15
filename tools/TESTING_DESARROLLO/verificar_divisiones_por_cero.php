<?php

// Cargar el autoloader y la configuración de Laravel
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFICACIÓN DE POSIBLES DIVISIONES POR CERO ===" . PHP_EOL . PHP_EOL;

// 1. Verificar artículos con precio_compra = 0
echo "1. ARTÍCULOS CON PRECIO_COMPRA = 0:" . PHP_EOL;
$articulos_cero = DB::table('articulos')->where('precio_compra', 0)->count();
echo "   Total: $articulos_cero artículos" . PHP_EOL . PHP_EOL;

// 2. Verificar detalles de venta con cantidad = 0
echo "2. DETALLES DE VENTA CON CANTIDAD = 0:" . PHP_EOL;
$detalles_cero = DB::table('detalle_ventas')->where('cantidad', 0)->count();
echo "   Total: $detalles_cero detalles" . PHP_EOL . PHP_EOL;

// 3. Verificar detalles de ingreso con cantidad = 0
echo "3. DETALLES DE INGRESO CON CANTIDAD = 0:" . PHP_EOL;
$ingresos_cero = DB::table('detalle_ingresos')->where('cantidad', 0)->count();
echo "   Total: $ingresos_cero detalles" . PHP_EOL . PHP_EOL;

// 4. Verificar otros casos potenciales
echo "4. RESUMEN DE RIESGOS:" . PHP_EOL;
if ($articulos_cero > 0) {
    echo "   ⚠️  $articulos_cero artículos con precio_compra = 0 (pueden causar división por cero)" . PHP_EOL;
}
if ($detalles_cero > 0) {
    echo "   ⚠️  $detalles_cero detalles de venta con cantidad = 0 (pueden causar división por cero)" . PHP_EOL;
}
if ($ingresos_cero > 0) {
    echo "   ⚠️  $ingresos_cero detalles de ingreso con cantidad = 0 (pueden causar división por cero)" . PHP_EOL;
}

if ($articulos_cero == 0 && $detalles_cero == 0 && $ingresos_cero == 0) {
    echo "   ✅ No se encontraron casos de riesgo adicionales" . PHP_EOL;
}

echo PHP_EOL . "=== VERIFICACIÓN COMPLETADA ===" . PHP_EOL;
