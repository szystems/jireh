<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Admin\AuditoriaController;
use Carbon\Carbon;

class EjecutarAuditoria extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auditoria:ejecutar {--dias=30 : Número de días a auditar} {--articulo= : ID específico de artículo a auditar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta una auditoría completa de stock e inconsistencias';

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
        $dias = $this->option('dias');
        $articuloId = $this->option('articulo');
        
        $this->info('=== AUDITORÍA DE STOCK E INVENTARIO ===');
        $this->info('Fecha: ' . Carbon::now()->format('Y-m-d H:i:s'));
        $this->info('Período: Últimos ' . $dias . ' días');
        
        if ($articuloId) {
            $this->info('Artículo específico: ' . $articuloId);
        }
        
        $this->line('');
        $this->info('Ejecutando auditoría...');
        
        try {
            // Crear una instancia del controlador
            $controller = new AuditoriaController();
            
            // Ejecutar auditoría usando reflexión para acceder al método privado
            $reflection = new \ReflectionClass($controller);
            $method = $reflection->getMethod('ejecutarAuditoriaManual');
            $method->setAccessible(true);
            
            $inconsistencias = $method->invoke($controller, $dias, false, $articuloId);
            
            $this->line('');
            $this->info('=== RESULTADOS DE LA AUDITORÍA ===');
            $this->info('Inconsistencias encontradas: ' . count($inconsistencias));
            
            if (count($inconsistencias) > 0) {
                $this->warn('Se detectaron las siguientes inconsistencias:');
                $this->line('');
                
                foreach ($inconsistencias as $inc) {
                    $this->line("- Artículo: {$inc['articulo']['nombre']} (ID: {$inc['articulo']['id']})");
                    $this->line("  Código: {$inc['articulo']['codigo']}");
                    $this->line("  Stock actual: {$inc['stock_actual']}, Teórico: {$inc['stock_teorico']}");
                    $this->line("  Diferencia: {$inc['diferencia']} - Severidad: {$inc['severidad']}");
                    $this->line('');
                }
                
                if ($this->confirm('¿Desea aplicar correcciones automáticas?')) {
                    $this->info('Aplicando correcciones...');
                    
                    $inconsistenciasCorregidas = $method->invoke($controller, $dias, true, $articuloId);
                    
                    $this->info('Correcciones aplicadas: ' . count($inconsistenciasCorregidas));
                    $this->info('✅ Auditoría completada con correcciones');
                } else {
                    $this->warn('Las inconsistencias no fueron corregidas automáticamente');
                }
            } else {
                $this->info('✅ Sistema consistente - No se encontraron inconsistencias');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error durante la auditoría: ' . $e->getMessage());
            return 1;
        }
        
        $this->line('');
        $this->info('Fecha de finalización: ' . Carbon::now()->format('Y-m-d H:i:s'));
        $this->info('=== FIN DE LA AUDITORÍA ===');
        
        return 0;
    }
}
