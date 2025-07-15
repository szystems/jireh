<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuditoriaAutomatica extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auditoria:automatica
                            {--dias=7 : Días hacia atrás para auditar}
                            {--enviar-alertas : Enviar alertas por email si hay inconsistencias críticas}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Ejecuta una auditoría automática del sistema de ventas e inventario';

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
        $this->info('🔍 Iniciando auditoría automática del sistema...');
        
        $dias = $this->option('dias');
        $enviarAlertas = $this->option('enviar-alertas');
        
        try {            // Ejecutar auditoría principal
            $this->info("📊 Ejecutando auditoría de los últimos {$dias} días...");
            $exitCode = Artisan::call('ventas:auditoria', [
                '--dias' => $dias
            ]);
            
            if ($exitCode !== 0) {
                $this->error('❌ Error al ejecutar la auditoría principal');
                return 1;
            }
            
            // Obtener resultado de la auditoría
            $output = Artisan::output();
            $this->line($output);
            
            // Verificar si hay inconsistencias críticas
            $inconsistenciasCriticas = $this->verificarInconsistenciasCriticas();
            
            if ($inconsistenciasCriticas > 0) {
                $this->warn("⚠️  Se encontraron {$inconsistenciasCriticas} inconsistencias críticas");
                
                if ($enviarAlertas) {
                    $this->enviarAlertasCriticas($inconsistenciasCriticas);
                }
            } else {
                $this->info('✅ No se encontraron inconsistencias críticas');
            }
            
            // Generar reporte de stock
            $this->info('📦 Verificando alertas de stock...');
            $alertasStock = $this->verificarAlertasStock();
            
            if ($alertasStock > 0) {
                $this->warn("📦 Se encontraron {$alertasStock} alertas de stock");
            } else {
                $this->info('✅ Stock en niveles normales');
            }
            
            // Registrar ejecución exitosa
            Log::info('Auditoría automática ejecutada', [
                'dias_auditados' => $dias,
                'inconsistencias_criticas' => $inconsistenciasCriticas,
                'alertas_stock' => $alertasStock,
                'fecha_ejecucion' => Carbon::now(),
                'alertas_enviadas' => $enviarAlertas && $inconsistenciasCriticas > 0
            ]);
            
            $this->info('✅ Auditoría automática completada exitosamente');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Error durante la auditoría automática: ' . $e->getMessage());
            
            Log::error('Error en auditoría automática', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'fecha' => Carbon::now()
            ]);
            
            return 1;
        }
    }
    
    private function verificarInconsistenciasCriticas()
    {
        // Contar inconsistencias del último reporte
        $rutaAuditorias = storage_path('app/auditorias');
        
        if (!is_dir($rutaAuditorias)) {
            return 0;
        }
        
        $archivos = glob($rutaAuditorias . '/auditoria_ventas_*.json');
        
        if (empty($archivos)) {
            return 0;
        }
        
        // Obtener el archivo más reciente
        usort($archivos, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        $ultimoReporte = json_decode(file_get_contents($archivos[0]), true);
        
        if (!$ultimoReporte || !isset($ultimoReporte['inconsistencias'])) {
            return 0;
        }
        
        // Contar inconsistencias críticas
        $criticas = 0;
        foreach ($ultimoReporte['inconsistencias'] as $inconsistencia) {
            if (isset($inconsistencia['severidad']) && 
                in_array($inconsistencia['severidad'], ['CRITICA', 'CRÍTICA'])) {
                $criticas++;
            }
            
            // Stock negativo es siempre crítico
            if ($inconsistencia['tipo'] === 'STOCK_NEGATIVO') {
                $criticas++;
            }
        }
        
        return $criticas;
    }
      private function verificarAlertasStock()
    {
        // Usar el controlador de auditoría para verificar alertas
        try {
            $controller = new \App\Http\Controllers\Admin\AuditoriaController();
            $alertas = $controller->obtenerAlertasStockDetalladas();
            
            return count($alertas);
            
        } catch (\Exception $e) {
            $this->warn('Error al verificar alertas de stock: ' . $e->getMessage());
            return 0;
        }
    }
    
    private function enviarAlertasCriticas($cantidad)
    {
        $this->info("📧 Enviando alertas críticas...");
        
        try {
            // Aquí implementar el envío real de alertas
            // Por ahora solo registramos en logs
            
            Log::warning("Alerta crítica: {$cantidad} inconsistencias críticas detectadas", [
                'cantidad_inconsistencias' => $cantidad,
                'fecha' => Carbon::now(),
                'tipo' => 'auditoria_automatica'
            ]);
            
            $this->info("✅ Alertas críticas registradas en logs");
            
            // En el futuro se puede implementar:
            // - Envío por email
            // - Notificaciones push
            // - Integración con Slack/Teams
            // - SMS para casos críticos
            
        } catch (\Exception $e) {
            $this->error('Error al enviar alertas: ' . $e->getMessage());
        }
    }
}
