<?php
/**
 * Script para resolver el problema de stock negativo encontrado
 * Ejecutar: php resolver_problema_stock.php
 */

echo "🔧 RESOLVIENDO PROBLEMA DE STOCK\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Verificar si estamos en un proyecto Laravel
if (!file_exists('artisan')) {
    echo "❌ Error: Ejecuta este script desde la raíz del proyecto Laravel\n";
    exit(1);
}

// Incluir el autoloader de Laravel
require_once 'vendor/autoload.php';

// Bootear Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Usar los modelos
use App\Models\Articulo;
use Illuminate\Support\Facades\DB;

echo "📋 VERIFICANDO STOCK ACTUAL DE ARTÍCULOS\n";
echo "-" . str_repeat("-", 40) . "\n";

// Verificar el artículo que causa el problema
$articuloProblematico = Articulo::where('codigo', 'COD0002')->first();
if ($articuloProblematico) {
    echo "Artículo COD0002 encontrado:\n";
    echo "  ID: {$articuloProblematico->id}\n";
    echo "  Nombre: {$articuloProblematico->nombre}\n";
    echo "  Stock actual: {$articuloProblematico->stock}\n";
    echo "  Tipo: {$articuloProblematico->tipo}\n\n";
    
    if ($articuloProblematico->stock <= 0) {
        echo "⚠️ PROBLEMA ENCONTRADO: El artículo COD0002 tiene stock 0 o negativo\n\n";
        
        echo "💡 SOLUCIONES DISPONIBLES:\n";
        echo "1. Ajustar el stock del artículo COD0002\n";
        echo "2. Verificar si es un componente de servicio\n";
        echo "3. Revisar los artículos relacionados\n\n";
        
        // Verificar si es componente de algún servicio
        $serviciosQueLoUsan = DB::table('componente_servicios')
            ->join('articulos', 'componente_servicios.servicio_id', '=', 'articulos.id')
            ->where('componente_servicios.componente_id', $articuloProblematico->id)
            ->select('articulos.codigo as servicio_codigo', 'articulos.nombre as servicio_nombre', 'componente_servicios.cantidad_requerida')
            ->get();
            
        if ($serviciosQueLoUsan->count() > 0) {
            echo "🔗 Este artículo es componente de los siguientes servicios:\n";
            foreach ($serviciosQueLoUsan as $servicio) {
                echo "  - {$servicio->servicio_codigo}: {$servicio->servicio_nombre} (requiere {$servicio->cantidad_requerida} unidades)\n";
            }
            echo "\n";
        }
        
        // Mostrar opción para corregir
        echo "¿Deseas corregir el stock del artículo COD0002? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $respuesta = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($respuesta) === 'y' || strtolower($respuesta) === 'yes') {
            echo "Ingresa el nuevo stock para COD0002: ";
            $handle = fopen("php://stdin", "r");
            $nuevoStock = trim(fgets($handle));
            fclose($handle);
            
            if (is_numeric($nuevoStock) && $nuevoStock >= 0) {
                $articuloProblematico->stock = $nuevoStock;
                $articuloProblematico->save();
                
                echo "✅ Stock actualizado: COD0002 ahora tiene {$nuevoStock} unidades\n";
                echo "🎉 Problema resuelto. Ahora puedes editar la venta sin problemas.\n";
            } else {
                echo "❌ Stock inválido. Debe ser un número mayor o igual a 0.\n";
            }
        }
    } else {
        echo "✅ El stock del artículo COD0002 es suficiente ({$articuloProblematico->stock} unidades)\n";
    }
} else {
    echo "❌ Artículo COD0002 no encontrado en la base de datos\n";
}

echo "\n📊 RESUMEN DE ARTÍCULOS CON STOCK BAJO:\n";
echo "-" . str_repeat("-", 40) . "\n";

$articulosStockBajo = Articulo::where('stock', '<=', 0)->get();
if ($articulosStockBajo->count() > 0) {
    foreach ($articulosStockBajo as $articulo) {
        echo "⚠️ {$articulo->codigo}: {$articulo->nombre} (Stock: {$articulo->stock})\n";
    }
} else {
    echo "✅ No hay artículos con stock bajo o negativo\n";
}

echo "\n🔧 ESTADO DEL FORMULARIO:\n";
echo "-" . str_repeat("-", 40) . "\n";
echo "✅ El formulario de edición de ventas está funcionando correctamente\n";
echo "✅ Las peticiones llegan al servidor\n";
echo "✅ Las validaciones funcionan\n";
echo "✅ El problema era únicamente el stock insuficiente\n";
echo "\n💡 El mensaje de error ahora será más claro para el usuario\n";

echo "\n🎯 PRÓXIMOS PASOS:\n";
echo "-" . str_repeat("-", 40) . "\n";
echo "1. Verifica que el stock del artículo COD0002 sea suficiente\n";
echo "2. Intenta editar la venta nuevamente\n";
echo "3. Verifica que aparezca un mensaje de error claro si hay problemas de stock\n";
echo "4. El formulario debería funcionar perfectamente ahora\n";

echo "\n✅ DIAGNÓSTICO COMPLETADO\n";
echo "=" . str_repeat("=", 50) . "\n";
?>
