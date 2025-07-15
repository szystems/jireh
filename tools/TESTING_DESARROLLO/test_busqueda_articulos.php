<?php

// Cargar el autoloader y la configuración de Laravel
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== PRUEBA DE BÚSQUEDA DE ARTÍCULOS ===" . PHP_EOL . PHP_EOL;

// Simular la búsqueda que estaba causando el error
$farticulo = 'completo';
$fcategoria = '';
$forden = 'nombre_asc';
$fstock = '';
$ftipo = '';

echo "Parámetros de búsqueda:" . PHP_EOL;
echo "- Artículo: '$farticulo'" . PHP_EOL;
echo "- Categoría: '$fcategoria'" . PHP_EOL;
echo "- Orden: '$forden'" . PHP_EOL;
echo "- Stock: '$fstock'" . PHP_EOL;
echo "- Tipo: '$ftipo'" . PHP_EOL . PHP_EOL;

// Construir la consulta como lo hace el controlador
$query = DB::table('articulos as a')
    ->leftJoin('categorias as c', 'a.categoria_id', '=', 'c.id')
    ->leftJoin('unidads as u', 'a.unidad_id', '=', 'u.id')
    ->select('a.*', 'c.nombre as categoria_nombre', 'u.nombre as unidad_nombre');

// Aplicar filtros
if (!empty($farticulo)) {
    $query->where(function($q) use ($farticulo) {
        $q->where('a.nombre', 'like', '%' . $farticulo . '%')
          ->orWhere('a.codigo', 'like', '%' . $farticulo . '%')
          ->orWhere('a.descripcion', 'like', '%' . $farticulo . '%');
    });
}

if (!empty($fcategoria)) {
    $query->where('a.categoria_id', $fcategoria);
}

if (!empty($ftipo)) {
    $query->where('a.tipo', $ftipo);
}

// Aplicar ordenamiento
switch ($forden) {
    case 'nombre_asc':
        $query->orderBy('a.nombre', 'asc');
        break;
    case 'nombre_desc':
        $query->orderBy('a.nombre', 'desc');
        break;
    case 'codigo_asc':
        $query->orderBy('a.codigo', 'asc');
        break;
    case 'codigo_desc':
        $query->orderBy('a.codigo', 'desc');
        break;
    case 'precio_asc':
        $query->orderBy('a.precio_venta', 'asc');
        break;
    case 'precio_desc':
        $query->orderBy('a.precio_venta', 'desc');
        break;
    default:
        $query->orderBy('a.nombre', 'asc');
}

$articulos = $query->get();

echo "RESULTADOS DE LA BÚSQUEDA:" . PHP_EOL;
echo "Total de artículos encontrados: " . $articulos->count() . PHP_EOL . PHP_EOL;

foreach ($articulos as $articulo) {
    echo "✓ {$articulo->codigo}: {$articulo->nombre}" . PHP_EOL;
    echo "  - Tipo: {$articulo->tipo}" . PHP_EOL;
    echo "  - Precio compra: Q{$articulo->precio_compra}" . PHP_EOL;
    echo "  - Precio venta: Q{$articulo->precio_venta}" . PHP_EOL;
    
    // Simular el cálculo de ganancia que estaba causando el error
    if ($articulo->precio_compra > 0) {
        $ganancia = (($articulo->precio_venta - $articulo->precio_compra) / $articulo->precio_compra) * 100;
        echo "  - Ganancia: " . number_format($ganancia, 1) . "%" . PHP_EOL;
    } else {
        echo "  - Ganancia: N/A" . PHP_EOL;
    }
    echo "" . PHP_EOL;
}

echo "✅ BÚSQUEDA COMPLETADA SIN ERRORES" . PHP_EOL;
