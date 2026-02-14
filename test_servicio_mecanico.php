<?php
/**
 * Test específico para servicios con mecánico asignado
 * Simula una venta con el servicio ID 206
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEST SERVICIO CON MECÁNICO ASIGNADO ===\n";

try {
    // Datos de prueba para crear una venta
    $datosVenta = [
        'cliente_id' => 1, // Asumiendo que existe cliente ID 1
        'vehiculo_id' => 1, // Asumiendo que existe vehículo ID 1
        'numero_factura' => 'TEST-' . time(),
        'fecha' => date('Y-m-d'),
        'tipo_venta' => 'CDS',
        'estado_pago' => 'pendiente',
        'detalles' => [
            [
                'articulo_id' => 206,
                'cantidad' => 1,
                'descuento_id' => null,
                'sub_total' => 945.00
            ]
        ],
        'aplicar_impuestos' => false
    ];

    echo "1. DATOS DE LA VENTA:\n";
    echo json_encode($datosVenta, JSON_PRETTY_PRINT) . "\n";

    // Simular la validación del VentaFormRequest
    echo "\n2. SIMULANDO VALIDACIÓN DE REQUEST:\n";
    
    $rules = [
        'cliente_id' => 'required|exists:clientes,id',
        'vehiculo_id' => 'required|exists:vehiculos,id',
        'numero_factura' => 'nullable|string|max:255',
        'fecha' => 'required|date',
        'tipo_venta' => 'required|in:Car Wash,CDS',
        'estado_pago' => 'required|in:pagado,pendiente',
        'detalles' => 'required|array',
        'detalles.*.articulo_id' => 'required|exists:articulos,id',
        'detalles.*.cantidad' => 'required|numeric|min:0.01',
        'detalles.*.sub_total' => 'required|numeric|min:0',
    ];

    $validator = \Illuminate\Support\Facades\Validator::make($datosVenta, $rules);
    
    if ($validator->fails()) {
        echo "❌ VALIDACIÓN FALLIDA:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "   - $error\n";
        }
        return;
    }
    echo "✅ Validación de request exitosa\n";

    // 3. Verificar que el artículo existe y es un servicio con mecánico
    echo "\n3. VERIFICANDO ARTÍCULO:\n";
    $articulo = \App\Models\Articulo::with('mecanico')->find(206);
    
    if (!$articulo) {
        echo "❌ Artículo no encontrado\n";
        return;
    }

    echo "✅ Artículo: {$articulo->nombre}\n";
    echo "   - Tipo: {$articulo->tipo}\n";
    echo "   - Stock: {$articulo->stock}\n";
    echo "   - Mecánico: " . ($articulo->mecanico ? $articulo->mecanico->nombre : 'Sin mecánico') . "\n";
    echo "   - Costo mecánico: Q.{$articulo->costo_mecanico}\n";

    // 4. Simular validación de stock usando el trait
    echo "\n4. SIMULANDO VALIDACIÓN DE STOCK:\n";
    $stockValidator = new class {
        use \App\Traits\StockValidation;
    };

    $validacionStock = $stockValidator->validarStockDisponible(206, 1);
    echo "Resultado: " . ($validacionStock['valido'] ? '✅ VÁLIDO' : '❌ INVÁLIDO') . "\n";
    echo "Mensaje: {$validacionStock['mensaje']}\n";
    
    if (!$validacionStock['valido']) {
        echo "🚨 AQUÍ ESTÁ EL PROBLEMA: La validación de stock está fallando\n";
        
        // Verificar si es por componentes
        if (isset($validacionStock['componentes_insuficientes'])) {
            echo "📦 Componentes insuficientes:\n";
            foreach ($validacionStock['componentes_insuficientes'] as $comp) {
                echo "   - {$comp['articulo']->codigo}: necesario {$comp['requerido']}, disponible {$comp['disponible']}\n";
            }
        }
        return;
    }

    // 5. Si llegamos aquí, simular la creación real
    echo "\n5. SIMULANDO CREACIÓN DE VENTA:\n";
    
    \Illuminate\Support\Facades\DB::beginTransaction();
    
    // Crear la venta
    $venta = \App\Models\Venta::create([
        'cliente_id' => $datosVenta['cliente_id'],
        'vehiculo_id' => $datosVenta['vehiculo_id'],
        'numero_factura' => $datosVenta['numero_factura'],
        'fecha' => $datosVenta['fecha'],
        'tipo_venta' => $datosVenta['tipo_venta'],
        'estado_pago' => $datosVenta['estado_pago'],
        'usuario_id' => 1, // Usuario de prueba
        'estado' => true
    ]);
    
    echo "✅ Venta creada con ID: {$venta->id}\n";

    // Crear el detalle
    $detalleData = $datosVenta['detalles'][0];
    $detalleData['usuario_id'] = 1;
    $detalleData['precio_costo'] = $articulo->precio_compra;
    $detalleData['precio_venta'] = $articulo->precio_venta;
    $detalleData['porcentaje_impuestos'] = 0;

    $detalleVenta = $venta->detalleVentas()->create($detalleData);
    echo "✅ Detalle creado con ID: {$detalleVenta->id}\n";

    // Simular actualización de stock (sin hacerlo realmente)
    echo "✅ Stock se actualizaría correctamente\n";

    // Generar comisión del mecánico
    echo "\n6. GENERANDO COMISIÓN DEL MECÁNICO:\n";
    $comisionMecanico = $detalleVenta->generarComisionMecanico();
    
    if ($comisionMecanico) {
        echo "✅ Comisión generada:\n";
        echo "   - Mecánico: {$articulo->mecanico->nombre}\n";
        echo "   - Monto: Q.{$comisionMecanico->monto}\n";
        echo "   - Estado: {$comisionMecanico->estado}\n";
    } else {
        echo "❌ No se pudo generar comisión del mecánico\n";
    }

    // Rollback para no afectar la BD
    \Illuminate\Support\Facades\DB::rollBack();
    echo "\n✅ PRUEBA COMPLETADA (Rollback aplicado)\n";

} catch (\Exception $e) {
    \Illuminate\Support\Facades\DB::rollBack();
    echo "\n🚨 ERROR CAPTURADO:\n";
    echo "Mensaje: {$e->getMessage()}\n";
    echo "Archivo: {$e->getFile()}:{$e->getLine()}\n";
    echo "\nStack trace:\n{$e->getTraceAsString()}\n";
}

echo "\n=== FIN TEST ===\n";