<?php

// Cargar el autoloader y la configuración de Laravel
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\Admin\ComisionController;
use Carbon\Carbon;

echo "=== PRUEBA ESPECÍFICA: COMISIONES CAR WASH ===" . PHP_EOL . PHP_EOL;

// Crear instancia del controlador
$controller = new ComisionController();

// Usar reflexión para acceder a métodos privados
$reflection = new ReflectionClass($controller);

// Obtener método calcularPeriodo
$calcularPeriodo = $reflection->getMethod('calcularPeriodo');
$calcularPeriodo->setAccessible(true);
$fechas = $calcularPeriodo->invoke($controller, 'mes_actual');

echo "Período: " . $fechas['inicio']->format('Y-m-d') . " a " . $fechas['fin']->format('Y-m-d') . PHP_EOL . PHP_EOL;

// Obtener método calcularComisionesCarwash
$calcularComisionesCarwash = $reflection->getMethod('calcularComisionesCarwash');
$calcularComisionesCarwash->setAccessible(true);
$comisionesCarwash = $calcularComisionesCarwash->invoke($controller, $fechas);

echo "RESULTADO DEL MÉTODO calcularComisionesCarwash():" . PHP_EOL;
echo "Registros encontrados: " . $comisionesCarwash->count() . PHP_EOL . PHP_EOL;

if ($comisionesCarwash->count() > 0) {
    $totalComisionesCarwash = 0;
    foreach ($comisionesCarwash as $comision) {
        echo "✓ TRABAJADOR: {$comision->nombre} {$comision->apellido} (ID: {$comision->id})" . PHP_EOL;
        echo "  └─ Total servicios: {$comision->total_servicios}" . PHP_EOL;
        echo "  └─ Comisión calculada: Q{$comision->comision_calculada}" . PHP_EOL;
        echo "  └─ Estado: {$comision->estado}" . PHP_EOL;
        echo "" . PHP_EOL;
        $totalComisionesCarwash += $comision->comision_calculada;
    }
    
    echo "🎯 TOTAL COMISIONES CAR WASH: Q{$totalComisionesCarwash}" . PHP_EOL;
    echo "✅ El método funciona correctamente!" . PHP_EOL;
} else {
    echo "❌ No se encontraron comisiones de Car Wash." . PHP_EOL;
    echo "Verificando si hay problemas en los datos..." . PHP_EOL;
}

echo PHP_EOL . "=== FIN DE LA PRUEBA ===" . PHP_EOL;
