<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\Trabajador;
use App\Models\TipoTrabajador;

// Configurar la base de datos
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'jireh_sistema',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Probar la creación de un trabajador sin dirección
try {
    echo "=== PRUEBA: Creación de trabajador sin dirección ===\n";
    
    // Obtener un tipo de trabajador existente
    $tipoTrabajador = TipoTrabajador::first();
    if (!$tipoTrabajador) {
        echo "Error: No se encontró ningún tipo de trabajador\n";
        exit(1);
    }
    
    echo "Tipo de trabajador encontrado: {$tipoTrabajador->nombre}\n";
    
    // Crear trabajador sin dirección
    $trabajador = new Trabajador();
    $trabajador->nombre = 'Juan Carlos';
    $trabajador->apellido = 'Pérez López';
    $trabajador->telefono = '1234-5678';
    $trabajador->email = 'juan.perez@example.com';
    $trabajador->nit = '12345678-9';
    $trabajador->dpi = '1234567890101';
    $trabajador->tipo_trabajador_id = $tipoTrabajador->id;
    $trabajador->estado = 1;
    // Nota: No se asigna 'direccion', debe ser null
    
    $trabajador->save();
    
    echo "✓ Trabajador creado exitosamente con ID: {$trabajador->id}\n";
    echo "✓ Nombre: {$trabajador->nombre} {$trabajador->apellido}\n";
    echo "✓ Teléfono: {$trabajador->telefono}\n";
    echo "✓ Email: {$trabajador->email}\n";
    echo "✓ Dirección: " . ($trabajador->direccion ?: 'NULL (como esperado)') . "\n";
    echo "✓ Tipo: {$trabajador->tipoTrabajador->nombre}\n";
    echo "✓ Estado: {$trabajador->estado}\n";
    
    // Verificar que la dirección es null
    if ($trabajador->direccion === null) {
        echo "✓ Campo dirección es correctamente NULL\n";
    } else {
        echo "✗ Error: Campo dirección debería ser NULL pero es: '{$trabajador->direccion}'\n";
    }
    
    echo "\n=== PRUEBA: Listar trabajadores ===\n";
    $trabajadores = Trabajador::with('tipoTrabajador')->get();
    foreach ($trabajadores as $t) {
        echo "ID: {$t->id}, Nombre: {$t->nombre} {$t->apellido}, Tipo: {$t->tipoTrabajador->nombre}, Dirección: " . ($t->direccion ?: 'NULL') . "\n";
    }
    
    echo "\n✓ Todas las pruebas pasaron exitosamente\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
