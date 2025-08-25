<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class ActualizarReportesAuditoria extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auditoria:actualizar-reportes {--dry-run : Ejecutar en modo de prueba sin hacer cambios}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza reportes de auditoría antiguos con la estructura correcta';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('=== ACTUALIZACIÓN DE REPORTES DE AUDITORÍA ===');
        $this->info('Fecha de ejecución: ' . Carbon::now()->format('Y-m-d H:i:s'));
        
        if ($dryRun) {
            $this->warn('MODO DE PRUEBA: No se realizarán cambios en los archivos');
        }
        
        $this->line('');
        
        $rutaAuditorias = storage_path('app/auditorias');
        
        if (!is_dir($rutaAuditorias)) {
            $this->error("❌ No se encontró el directorio de auditorías: $rutaAuditorias");
            return 1;
        }
        
        $archivos = glob($rutaAuditorias . '/*.json');
        $procesados = 0;
        $actualizados = 0;
        $errores = 0;
        
        $this->info("Archivos de reporte encontrados: " . count($archivos));
        $this->line('');
        
        foreach ($archivos as $archivo) {
            $nombreArchivo = basename($archivo);
            $procesados++;
            
            try {
                $contenido = json_decode(file_get_contents($archivo), true);
                
                if (!$contenido) {
                    $this->error("❌ Error al leer JSON: $nombreArchivo");
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
                    if (!$dryRun) {
                        // Crear backup
                        $backup = $archivo . '.backup.' . date('Y-m-d_H-i-s');
                        copy($archivo, $backup);
                        
                        // Guardar versión actualizada
                        file_put_contents($archivo, json_encode($contenido, JSON_PRETTY_PRINT));
                    }
                    
                    $this->line('✅ ' . ($dryRun ? '[SIMULADO] ' : '') . "Actualizado: $nombreArchivo" . (!$dryRun ? ' (backup creado)' : ''));
                    $actualizados++;
                } else {
                    $this->line("ℹ️  Ya actualizado: $nombreArchivo");
                }
                
            } catch (\Exception $e) {
                $this->error("❌ Error procesando $nombreArchivo: " . $e->getMessage());
                $errores++;
            }
        }
        
        $this->line('');
        $this->info('=== RESUMEN DE ACTUALIZACIÓN ===');
        $this->info("Total archivos procesados: $procesados");
        $this->info("Archivos actualizados: $actualizados");
        $this->info("Errores: $errores");
        $this->info('Estado: ' . ($errores == 0 ? 'EXITOSO' : 'COMPLETADO CON ERRORES'));
        
        if ($dryRun) {
            $this->warn('MODO DE PRUEBA: Ejecute sin --dry-run para aplicar los cambios');
        }
        
        $this->line('');
        $this->info('Fecha de finalización: ' . Carbon::now()->format('Y-m-d H:i:s'));
        $this->info('=== FIN DE LA ACTUALIZACIÓN ===');
        
        return 0;
    }
}
