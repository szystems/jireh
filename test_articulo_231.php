<?php
/**
 * Test específico para el artículo LAVADO COMPLETO PICKUPS GRANDES
 * ID 231 que causa problemas en iPage
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST ARTÍCULO LAVADO COMPLETO PICKUPS GRANDES ===\n";
echo "<pre>";

try {
    // 1. Verificar artículo ID 231
    echo "\n1. VERIFICANDO ARTÍCULO 231:\n";
    $articulo = \App\Models\Articulo::with(['mecanico', 'unidad'])->find(231);

    if (!$articulo) {
        echo "❌ Artículo 231 no encontrado\n";
        exit(1);
    }

    echo "✅ Artículo encontrado:\n";
    echo "   - Código: {$articulo->codigo}\n";
    echo "   - Nombre: {$articulo->nombre}\n";
    echo "   - Tipo: {$articulo->tipo}\n";
    echo "   - Stock: {$articulo->stock}\n";
    echo "   - Precio Venta: Q.{$articulo->precio_venta}\n";
    echo "   - Mecánico ID: " . ($articulo->mecanico_id ?? 'NULL') . "\n";
    echo "   - Comisión Carwash: Q." . ($articulo->comision_carwash ?? '0.00') . "\n";

    // 2. Verificar validación de stock
    echo "\n2. VALIDACIÓN DE STOCK:\n";
    $stockValidator = new class {
        use \App\Traits\StockValidation;
    };

    $validacion = $stockValidator->validarStockDisponible(231, 1);
    echo "Resultado: " . ($validacion['valido'] ? '✅ VÁLIDO' : '❌ INVÁLIDO') . "\n";
    echo "Mensaje: {$validacion['mensaje']}\n";

    if (!$validacion['valido']) {
        echo "🚨 PROBLEMA DE STOCK DETECTADO\n";
        return;
    }

    // 3. Simular datos de venta
    echo "\n3. DATOS DE VENTA SIMULADOS:\n";
    $datosVenta = [
        'cliente_id' => 1,
        'vehiculo_id' => 1,
        'numero_factura' => 'TEST-IPAGE-' . time(),
        'fecha' => date('Y-m-d'),
        'tipo_venta' => 'Car Wash',
        'estado_pago' => 'pendiente',
        'detalles' => [
            [
                'articulo_id' => 231,
                'cantidad' => 1,
                'descuento_id' => null,
                'sub_total' => 65.00,
                'usuario_id' => 1
            ]
        ]
    ];

    echo json_encode($datosVenta, JSON_PRETTY_PRINT) . "\n";

    // 4. Validar request
    echo "\n4. VALIDACIÓN DE REQUEST:\n";
    $rules = [
        'cliente_id' => 'required|exists:clientes,id',
        'vehiculo_id' => 'required|exists:vehiculos,id',
        'fecha' => 'required|date',
        'tipo_venta' => 'required|in:Car Wash,CDS',
        'estado_pago' => 'required|in:pagado,pendiente',
        'detalles' => 'required|array',
        'detalles.*.articulo_id' => 'required|exists:articulos,id',
        'detalles.*.cantidad' => 'required|numeric|min:0.01',
    ];

    $validator = \Illuminate\Support\Facades\Validator::make($datosVenta, $rules);
    
    if ($validator->fails()) {
        echo "❌ VALIDACIÓN FALLIDA:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "   - $error\n";
        }
    } else {
        echo "✅ Validación exitosa\n";
    }

    // 5. Verificar configuración
    echo "\n5. CONFIGURACIÓN:\n";
    $config = \App\Models\Config::first();
    echo "Config encontrada: " . ($config ? '✅ SÍ' : '❌ NO') . "\n";
    if ($config) {
        echo "   - Moneda: {$config->currency_simbol}\n";
        echo "   - Impuesto: {$config->impuesto}%\n";
    }

    // 6. Verificar trabajadores carwash (si aplica)
    echo "\n6. TRABAJADORES CARWASH:\n";
    $trabajadores = \App\Models\Trabajador::with('tipoTrabajador')
        ->whereHas('tipoTrabajador', function($query) {
            $query->where('nombre', 'Car Wash')
                  ->where('estado', true);
        })
        ->where('estado', true)
        ->count();
    
    echo "Trabajadores disponibles: {$trabajadores}\n";

    // 7. Verificar límites PHP
    echo "\n7. LÍMITES PHP:\n";
    echo "Memory Limit: " . ini_get('memory_limit') . "\n";
    echo "Max Post Size: " . ini_get('post_max_size') . "\n";
    echo "Max Input Vars: " . ini_get('max_input_vars') . "\n";
    
    // Calcular variables que se enviarían
    $totalVars = count($datosVenta, COUNT_RECURSIVE);
    echo "Variables estimadas: {$totalVars}\n";
    echo "Límite alcanzado: " . ($totalVars > ini_get('max_input_vars') ? '❌ SÍ' : '✅ NO') . "\n";

} catch (\Exception $e) {
    echo "\n🚨 ERROR CAPTURADO:\n";
    echo "Mensaje: {$e->getMessage()}\n";
    echo "Archivo: {$e->getFile()}:{$e->getLine()}\n";
}

echo "</pre>";
echo "\n=== FIN TEST ===";
?>