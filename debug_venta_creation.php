<?php
/**
 * Script de debugging para creación de ventas
 * Ejecutar desde la raíz del proyecto: php debug_venta_creation.php
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUGGING CREACIÓN DE VENTA ===\n";

// 1. Verificar artículo ID 206
echo "\n1. VERIFICANDO ARTÍCULO 206:\n";
$articulo = \App\Models\Articulo::with(['mecanico', 'unidad'])->find(206);

if (!$articulo) {
    echo "❌ Artículo 206 no encontrado\n";
    exit(1);
}

echo "✅ Artículo encontrado:\n";
echo "   - Código: {$articulo->codigo}\n";
echo "   - Nombre: {$articulo->nombre}\n";
echo "   - Tipo: {$articulo->tipo}\n";
echo "   - Stock: {$articulo->stock}\n";
echo "   - Precio Venta: Q.{$articulo->precio_venta}\n";
echo "   - Mecánico ID: " . ($articulo->mecanico_id ?? 'NULL') . "\n";
echo "   - Costo Mecánico: Q." . ($articulo->costo_mecanico ?? '0.00') . "\n";
echo "   - Comisión Carwash: Q." . ($articulo->comision_carwash ?? '0.00') . "\n";

if ($articulo->mecanico) {
    echo "   - Mecánico: {$articulo->mecanico->nombre} {$articulo->mecanico->apellido}\n";
}

// 2. Verificar componentes del servicio
echo "\n2. VERIFICANDO COMPONENTES DEL SERVICIO:\n";
$componentes = \Illuminate\Support\Facades\DB::table('servicio_articulo')
    ->where('servicio_id', 206)
    ->get();

if ($componentes->isEmpty()) {
    echo "✅ Servicio sin componentes (servicio puro)\n";
} else {
    echo "📦 Componentes encontrados:\n";
    foreach ($componentes as $componente) {
        $articuloComponente = \App\Models\Articulo::find($componente->articulo_id);
        echo "   - {$articuloComponente->codigo}: {$articuloComponente->nombre} (Cantidad: {$componente->cantidad}, Stock: {$articuloComponente->stock})\n";
    }
}

// 3. Simular validación de stock
echo "\n3. SIMULANDO VALIDACIÓN DE STOCK:\n";
$stockValidator = new class {
    use \App\Traits\StockValidation;
};

$validacion = $stockValidator->validarStockDisponible(206, 1);
echo "Resultado validación: " . ($validacion['valido'] ? '✅ VÁLIDO' : '❌ INVÁLIDO') . "\n";
echo "Mensaje: {$validacion['mensaje']}\n";
echo "Stock actual: {$validacion['stock_actual']}\n";
echo "Stock requerido: {$validacion['stock_requerido']}\n";

// 4. Verificar si requiere mecánico
echo "\n4. VERIFICANDO LÓGICA DE MECÁNICO:\n";
echo "¿Requiere mecánico? " . ($articulo->requiereMecanico() ? '✅ SÍ' : '❌ NO') . "\n";
echo "¿Puede generar comisión mecánico? " . (
    $articulo->tipo === 'servicio' && 
    $articulo->mecanico_id && 
    $articulo->costo_mecanico > 0 
    ? '✅ SÍ' : '❌ NO'
) . "\n";

// 5. Verificar trabajadores de carwash disponibles
echo "\n5. VERIFICANDO TRABAJADORES CARWASH:\n";
$trabajadoresCarwash = \App\Models\Trabajador::with('tipoTrabajador')
    ->whereHas('tipoTrabajador', function($query) {
        $query->where('nombre', 'Car Wash')
              ->where('estado', true);
    })
    ->where('estado', true)
    ->count();

echo "Trabajadores carwash disponibles: {$trabajadoresCarwash}\n";

echo "\n=== FIN DEBUGGING ===\n";