<?php

// Script para actualizar reportes de auditoría con la estructura correcta
require_once __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;

echo "=== ACTUALIZACIÓN DE REPORTES DE AUDITORÍA ===\n";
echo "Fecha de ejecución: " . Carbon::now()->format('Y-m-d H:i:s') . "\n\n";

$rutaAuditorias = storage_path('app/auditorias');

if (!is_dir($rutaAuditorias)) {
    echo "❌ No se encontró el directorio de auditorías: $rutaAuditorias\n";
    return;
}

$archivos = glob($rutaAuditorias . '/*.json');
$procesados = 0;
$actualizados = 0;
$errores = 0;

echo "Archivos de reporte encontrados: " . count($archivos) . "\n\n";

foreach ($archivos as $archivo) {
    $nombreArchivo = basename($archivo);
    $procesados++;
    
    try {
        $contenido = json_decode(file_get_contents($archivo), true);
        
        if (!$contenido) {
            echo "❌ Error al leer JSON: $nombreArchivo\n";
            $errores++;
            continue;
        }
        
        $necesitaActualizacion = false;
        
        // Verificar y corregir campos faltantes en parametros
        if (isset($contenido['parametros'])) {
            // Agregar correcciones_aplicadas si no existe
            if (!isset($contenido['parametros']['correcciones_aplicadas'])) {
                $contenido['parametros']['correcciones_aplicadas'] = $contenido['parametros']['aplicar_correcciones'] ?? false;
                $necesitaActualizacion = true;
            }
            
            // Agregar dias_auditados si no existe pero existe dias
            if (!isset($contenido['parametros']['dias_auditados']) && isset($contenido['parametros']['dias'])) {
                $contenido['parametros']['dias_auditados'] = $contenido['parametros']['dias'];
                $necesitaActualizacion = true;
            }
        }
        
        // Verificar y agregar campos faltantes en estadisticas
        if (isset($contenido['estadisticas'])) {
            if (!isset($contenido['estadisticas']['articulos_con_problemas'])) {
                $contenido['estadisticas']['articulos_con_problemas'] = count($contenido['inconsistencias'] ?? []);
                $necesitaActualizacion = true;
            }
        }
        
        // Actualizar archivo si es necesario
        if ($necesitaActualizacion) {
            // Crear backup
            $backup = $archivo . '.backup.' . date('Y-m-d_H-i-s');
            copy($archivo, $backup);
            
            // Guardar versión actualizada
            file_put_contents($archivo, json_encode($contenido, JSON_PRETTY_PRINT));
            
            echo "✅ Actualizado: $nombreArchivo (backup creado)\n";
            $actualizados++;
        } else {
            echo "ℹ️  Ya actualizado: $nombreArchivo\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error procesando $nombreArchivo: " . $e->getMessage() . "\n";
        $errores++;
    }
}

echo "\n=== RESUMEN DE ACTUALIZACIÓN ===\n";
echo "Total archivos procesados: $procesados\n";
echo "Archivos actualizados: $actualizados\n";
echo "Errores: $errores\n";
echo "Estado: " . ($errores == 0 ? 'EXITOSO' : 'COMPLETADO CON ERRORES') . "\n";

echo "\nFecha de finalización: " . Carbon::now()->format('Y-m-d H:i:s') . "\n";
echo "=== FIN DE LA ACTUALIZACIÓN ===\n";
