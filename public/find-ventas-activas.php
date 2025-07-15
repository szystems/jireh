<?php
// Script para encontrar ventas activas en la base de datos
require_once __DIR__.'/../vendor/autoload.php';
use Illuminate\Support\Facades\DB;
$app = require_once __DIR__.'/../bootstrap/app.php';

// Inicializar la aplicación
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Consultar ventas activas
$ventasActivas = DB::select('SELECT id FROM ventas WHERE estado = 1 LIMIT 5');

// Mostrar resultados
echo "<pre>";
echo "Ventas activas:\n";
print_r($ventasActivas);
echo "</pre>";
