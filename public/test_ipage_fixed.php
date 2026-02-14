<?php
/**
 * Test para iPage desde carpeta public/
 * Corregido para ejecutar desde public/
 */

// Cambiar al directorio raíz del proyecto
$rootPath = dirname(__DIR__);
chdir($rootPath);

echo "=== TEST IPAGE DESDE PUBLIC ===\n";
echo "<pre>";

try {
    echo "Directorio actual: " . getcwd() . "\n";
    echo "Buscando vendor/autoload.php...\n";
    
    if (file_exists('vendor/autoload.php')) {
        require_once 'vendor/autoload.php';
        echo "✅ vendor/autoload.php cargado\n";
    } else {
        echo "❌ vendor/autoload.php no encontrado\n";
        echo "Listando archivos en directorio raíz:\n";
        $files = scandir('.');
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "  - $file\n";
            }
        }
        exit(1);
    }
    
    if (file_exists('bootstrap/app.php')) {
        $app = require_once 'bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        echo "✅ Laravel bootstrapped\n";
    } else {
        echo "❌ bootstrap/app.php no encontrado\n";
        exit(1);
    }

    // Test de conexión BD
    echo "\n=== CONEXIÓN BASE DE DATOS ===\n";
    try {
        \Illuminate\Support\Facades\DB::connection();
        echo "✅ Conexión a BD exitosa\n";
        
        $result = \Illuminate\Support\Facades\DB::select('SELECT COUNT(*) as total FROM articulos');
        echo "✅ Query test exitosa - Artículos: " . $result[0]->total . "\n";
        
        // Test artículo específico
        $articulo = \Illuminate\Support\Facades\DB::select('SELECT * FROM articulos WHERE id = 231 LIMIT 1');
        if (!empty($articulo)) {
            echo "✅ Artículo 231 encontrado: " . $articulo[0]->nombre . "\n";
            echo "   Stock: " . $articulo[0]->stock . "\n";
            echo "   Tipo: " . $articulo[0]->tipo . "\n";
        } else {
            echo "❌ Artículo 231 no encontrado\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error BD: " . $e->getMessage() . "\n";
    }

    // Test de configuración
    echo "\n=== CONFIGURACIÓN ===\n";
    try {
        $config = \App\Models\Config::first();
        if ($config) {
            echo "✅ Config encontrada\n";
            echo "   Moneda: {$config->currency_simbol}\n";
            echo "   Impuesto: {$config->impuesto}%\n";
        } else {
            echo "❌ Config no encontrada\n";
        }
    } catch (Exception $e) {
        echo "❌ Error Config: " . $e->getMessage() . "\n";
    }

    // Test de trabajadores
    echo "\n=== TRABAJADORES ===\n";
    try {
        $totalTrabajadores = \Illuminate\Support\Facades\DB::select('SELECT COUNT(*) as total FROM trabajadors WHERE estado = 1');
        echo "✅ Trabajadores activos: " . $totalTrabajadores[0]->total . "\n";
    } catch (Exception $e) {
        echo "❌ Error Trabajadores: " . $e->getMessage() . "\n";
    }

    // Test de validación básica
    echo "\n=== TEST VALIDACIÓN ===\n";
    $datosTest = [
        'cliente_id' => 1,
        'vehiculo_id' => 1,
        'fecha' => date('Y-m-d'),
        'tipo_venta' => 'Car Wash',
        'estado_pago' => 'pendiente',
        'detalles' => [
            [
                'articulo_id' => 231,
                'cantidad' => 1,
                'sub_total' => 65.00
            ]
        ]
    ];

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

    $validator = \Illuminate\Support\Facades\Validator::make($datosTest, $rules);
    
    if ($validator->fails()) {
        echo "❌ VALIDACIÓN FALLIDA:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "   - $error\n";
        }
    } else {
        echo "✅ Validación básica exitosa\n";
    }

} catch (\Exception $e) {
    echo "\n🚨 ERROR GENERAL:\n";
    echo "Mensaje: {$e->getMessage()}\n";
    echo "Archivo: {$e->getFile()}:{$e->getLine()}\n";
}

echo "</pre>";
echo "\n=== FIN TEST ===";
?>