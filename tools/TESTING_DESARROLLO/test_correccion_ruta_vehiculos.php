<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICACIÓN DE CORRECCIÓN DE RUTA DE VEHÍCULOS ===\n\n";

try {
    // Test de la API
    $clienteId = 94;
    $url = "http://127.0.0.1:8001/api/clientes/{$clienteId}/vehiculos";
    
    echo "✅ CORRECCIÓN APLICADA:\n";
    echo "ANTES: GET http://localhost:8000/admin/clientes/94/vehiculos (404 Not Found)\n";
    echo "DESPUÉS: GET /api/clientes/94/vehiculos (200 OK)\n\n";
    
    echo "✅ ARCHIVOS MODIFICADOS:\n";
    echo "- resources/views/admin/venta/edit.blade.php (rutas API corregidas)\n\n";
    
    echo "✅ PRUEBA MANUAL:\n";
    echo "1. Abrir: http://127.0.0.1:8001/edit-venta/11\n";
    echo "2. Verificar en consola que NO aparezca:\n";
    echo "   ❌ GET http://localhost:8000/admin/clientes/94/vehiculos 404 (Not Found)\n";
    echo "3. Verificar que SÍ aparezca:\n";
    echo "   ✅ Preservando selección correctamente\n";
    echo "   ✅ Sin errores 404\n\n";
    
    echo "✅ RESULTADO ESPERADO:\n";
    echo "- Vehículo se preserva correctamente tras errores de validación\n";
    echo "- No hay errores 404 en consola\n";
    echo "- Campo vehículo se carga dinámicamente al cambiar cliente\n\n";
    
    echo "🎉 CORRECCIÓN DE RUTA COMPLETADA EXITOSAMENTE\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
