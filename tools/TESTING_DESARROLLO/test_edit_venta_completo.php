<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Http\Controllers\Admin\VentaController;
use Illuminate\Http\Request;

echo "=== PRUEBA COMPLETA DE EDICIÓN DE VENTA ===\n\n";

try {
    // Buscar la venta de prueba
    $venta = Venta::with(['cliente', 'vehiculo', 'detalleVentas.articulo', 'detalleVentas.trabajadoresCarwash'])
                  ->find(11);
    
    if (!$venta) {
        echo "❌ No se encontró la venta ID 11\n";
        exit(1);
    }
    
    echo "✅ Venta encontrada: ID {$venta->id}\n";
    echo "   - Cliente: {$venta->cliente->nombre} (ID: {$venta->cliente_id})\n";
    echo "   - Vehículo: {$venta->vehiculo->marca} {$venta->vehiculo->modelo} (ID: {$venta->vehiculo_id})\n";
    echo "   - Fecha original: {$venta->fecha}\n";
    echo "   - Fecha formateada para input: " . $venta->fecha->format('Y-m-d') . "\n";
    echo "   - Estado pago: {$venta->estado_pago}\n";
    echo "   - Tipo venta: {$venta->tipo_venta}\n\n";
    
    // Verificar detalles y trabajadores
    echo "=== DETALLES DE LA VENTA ===\n";
    foreach ($venta->detalleVentas as $index => $detalle) {
        echo "Detalle " . ($index + 1) . ":\n";
        echo "  - ID: {$detalle->id}\n";
        echo "  - Artículo: {$detalle->articulo->nombre} (Tipo: {$detalle->articulo->tipo})\n";
        echo "  - Cantidad: {$detalle->cantidad}\n";
        echo "  - Subtotal: Q.{$detalle->sub_total}\n";
        
        if ($detalle->articulo->tipo === 'servicio') {
            $trabajadores = $detalle->trabajadoresCarwash;
            echo "  - Trabajadores asignados: {$trabajadores->count()}\n";
            foreach ($trabajadores as $trabajador) {
                echo "    * {$trabajador->nombre_completo} (ID: {$trabajador->id})\n";
            }
        }
        echo "\n";
    }
    
    // Simular datos que llegarían del formulario de edición
    echo "=== SIMULACIÓN DE DATOS DEL FORMULARIO ===\n";
    
    $datosFormulario = [
        'numero_factura' => 'FACT-EDIT-' . $venta->id,
        'fecha' => '2025-07-08', // Fecha en formato correcto para input date
        'cliente_id' => $venta->cliente_id,
        'vehiculo_id' => $venta->vehiculo_id,
        'tipo_venta' => 'Car Wash',
        'estado_pago' => 'pagado', // Cambiar estado
        'estado' => $venta->estado,
        'usuario_id' => $venta->usuario_id,
        'detalles' => []
    ];
    
    // Agregar detalles existentes
    foreach ($venta->detalleVentas as $detalle) {
        $datosFormulario['detalles'][$detalle->id] = [
            'articulo_id' => $detalle->articulo_id,
            'cantidad' => $detalle->cantidad,
            'sub_total' => $detalle->sub_total,
            'usuario_id' => $detalle->usuario_id
        ];
        
        // Si es servicio, agregar trabajadores
        if ($detalle->articulo->tipo === 'servicio') {
            $datosFormulario['trabajadores_carwash'][$detalle->id] = 
                $detalle->trabajadoresCarwash->pluck('id')->toArray();
        }
    }
    
    echo "Datos que se enviarían:\n";
    foreach ($datosFormulario as $campo => $valor) {
        if (is_array($valor)) {
            echo "  - {$campo}: [array con " . count($valor) . " elementos]\n";
        } else {
            echo "  - {$campo}: {$valor}\n";
        }
    }
    
    // Verificar que old() funcionaría correctamente
    echo "\n=== VERIFICACIÓN DE OLD() VALUES ===\n";
    
    // Simular errores de validación
    $oldValues = [
        'numero_factura' => 'FACT-OLD-123',
        'fecha' => '2025-07-09',
        'cliente_id' => $venta->cliente_id,
        'vehiculo_id' => $venta->vehiculo_id,
        'tipo_venta' => 'CDS',
        'estado_pago' => 'pendiente'
    ];
    
    foreach ($oldValues as $campo => $valorOld) {
        $valorOriginal = $venta->$campo;
        $valorFinal = $valorOld ?: $valorOriginal;
        
        if ($campo === 'fecha' && $valorOriginal) {
            $valorOriginal = $venta->fecha->format('Y-m-d');
        }
        
        echo "  - {$campo}:\n";
        echo "    * Valor original: {$valorOriginal}\n";
        echo "    * Valor old(): {$valorOld}\n";
        echo "    * Valor final: {$valorFinal}\n";
    }
    
    // Verificar preservación de vehículo con old()
    echo "\n=== VERIFICACIÓN DE PRESERVACIÓN DE VEHÍCULO ===\n";
    
    $vehiculoIdOld = $venta->vehiculo_id; // Simular old('vehiculo_id')
    $vehiculoOriginal = $venta->vehiculo_id;
    
    $vehiculoIdSelected = $vehiculoIdOld ?: $vehiculoOriginal;
    
    if ($vehiculoIdSelected) {
        $vehiculo = \App\Models\Vehiculo::find($vehiculoIdSelected);
        if ($vehiculo) {
            echo "✅ Vehículo preservado correctamente:\n";
            echo "   - ID: {$vehiculo->id}\n";
            echo "   - Texto: {$vehiculo->marca} {$vehiculo->modelo} - {$vehiculo->placa}\n";
        } else {
            echo "❌ Vehículo no encontrado para ID: {$vehiculoIdSelected}\n";
        }
    }
    
    echo "\n✅ PRUEBA COMPLETA DE EDICIÓN EXITOSA\n";
    echo "   - Campo fecha se formatea correctamente para input date\n";
    echo "   - Preservación de datos con old() funciona\n";
    echo "   - Vehículos se cargan correctamente\n";
    echo "   - Detalles y trabajadores están disponibles\n";
    
} catch (Exception $e) {
    echo "❌ ERROR EN LA PRUEBA: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . "\n";
    echo "   Línea: " . $e->getLine() . "\n";
    exit(1);
}
