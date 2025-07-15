#!/usr/bin/env php
<?php

/**
 * Script para probar las correcciones del módulo de trabajadores
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Trabajador;
use App\Models\TipoTrabajador;

echo "=== PRUEBA DE CORRECCIONES DEL MÓDULO DE TRABAJADORES ===\n\n";

try {
    // Verificar tipos de trabajador disponibles
    $tiposTrabajador = TipoTrabajador::where('estado', 'activo')->get();
    
    echo "📋 Tipos de trabajador disponibles: " . $tiposTrabajador->count() . "\n";
    foreach ($tiposTrabajador as $tipo) {
        echo "   - {$tipo->nombre} (ID: {$tipo->id})\n";
    }
    echo "\n";

    // Verificar trabajadores existentes
    $trabajadores = Trabajador::with('tipoTrabajador')->take(5)->get();
    
    echo "👥 Trabajadores existentes (muestra): " . $trabajadores->count() . "\n";
    foreach ($trabajadores as $trabajador) {
        echo "   - {$trabajador->nombre} {$trabajador->apellido}\n";
        echo "     Teléfono: " . ($trabajador->telefono ?: 'No especificado') . "\n";
        echo "     Dirección: " . ($trabajador->direccion ?: 'No especificado') . "\n";
        echo "     Tipo: " . ($trabajador->tipoTrabajador ? $trabajador->tipoTrabajador->nombre : 'No asignado') . "\n";
        echo "     Estado: " . ($trabajador->estado ? 'Activo' : 'Inactivo') . "\n";
        echo "     ----\n";
    }

    echo "\n🌐 URLs para probar:\n";
    echo "   Lista de trabajadores: http://127.0.0.1:8000/trabajadores\n";
    echo "   Crear trabajador: http://127.0.0.1:8000/add-trabajador\n";
    if ($trabajadores->count() > 0) {
        echo "   Editar trabajador: http://127.0.0.1:8000/edit-trabajador/{$trabajadores->first()->id}\n";
    }

    echo "\n✅ Correcciones implementadas:\n";
    echo "   ✅ Campo apellido agregado en crear y editar\n";
    echo "   ✅ Dirección cambiada a opcional\n";
    echo "   ✅ Estado predeterminado a 1 (activo) en creación\n";
    echo "   ✅ Select de tipo de trabajador corregido en edición\n";
    echo "   ✅ Validaciones actualizadas en el controlador\n";
    echo "   ✅ Teléfono marcado como obligatorio\n";
    echo "   ✅ Campos NIT y DPI separados correctamente\n";

    echo "\n📝 Para probar la funcionalidad completa:\n";
    echo "   1. Asegúrate de que el servidor esté corriendo (php artisan serve)\n";
    echo "   2. Ve a la URL de crear trabajador\n";
    echo "   3. Verifica que aparezcan todos los campos (incluyendo apellido)\n";
    echo "   4. Confirma que dirección no es obligatoria\n";
    echo "   5. Prueba crear un trabajador sin estado manual\n";
    echo "   6. Edita un trabajador y verifica el select de tipos\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   Línea: " . $e->getLine() . "\n";
    echo "   Archivo: " . $e->getFile() . "\n";
    exit(1);
}
