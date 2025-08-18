<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DetallePagoSueldo;
use Illuminate\Support\Facades\DB;

class MigrarDatosDetallesPagos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pagos:migrar-datos 
                            {--dry-run : Solo mostrar qué se haría sin hacer cambios}
                            {--force : Forzar la migración incluso si hay datos en los nuevos campos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migra los datos existentes del campo bonificaciones consolidado a los nuevos campos detallados';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🚀 Iniciando migración de datos de detalles de pagos...');
        
        // Verificar si ya hay datos en los nuevos campos
        $registrosConDatos = DetallePagoSueldo::where(function($query) {
            $query->where('horas_extra', '>', 0)
                  ->orWhere('valor_hora_extra', '>', 0)
                  ->orWhere('comisiones', '>', 0);
        })->count();

        if ($registrosConDatos > 0 && !$this->option('force')) {
            $this->warn("⚠️  Se encontraron {$registrosConDatos} registros que ya tienen datos en los nuevos campos.");
            $this->warn("Usa --force para sobrescribir o revisa los datos manualmente.");
            return 1;
        }

        $registros = DetallePagoSueldo::whereNull('estado')->get();
        $this->info("📊 Se encontraron {$registros->count()} registros para migrar");

        if ($this->option('dry-run')) {
            $this->warn("🔍 MODO DRY-RUN - No se harán cambios reales");
        }

        $barra = $this->output->createProgressBar($registros->count());
        $barra->start();

        DB::beginTransaction();
        
        try {
            foreach ($registros as $detalle) {
                if ($this->option('dry-run')) {
                    $this->line("\n📝 Registro ID {$detalle->id}:");
                    $this->line("  - Bonificaciones actuales: Q{$detalle->bonificaciones}");
                    $this->line("  - Se mantendría en campo 'bonificaciones' (sin separar)");
                    $this->line("  - Estado se establecería como: 'pendiente'");
                } else {
                    // Por ahora, mantener las bonificaciones como están (sin separar automáticamente)
                    // ya que necesitaríamos lógica de negocio específica para saber cómo dividirlas
                    $detalle->update([
                        'horas_extra' => 0, // Se pueden editar manualmente después
                        'valor_hora_extra' => 0,
                        'comisiones' => 0,
                        'estado' => 'pendiente', // Todos los registros existentes quedan pendientes
                        // bonificaciones se mantiene como está
                    ]);
                }
                $barra->advance();
            }

            if (!$this->option('dry-run')) {
                DB::commit();
                $barra->finish();
                $this->newLine();
                $this->info("✅ Migración completada exitosamente!");
                $this->info("📋 Todos los registros existentes ahora tienen estado 'pendiente'");
                $this->info("💡 Las bonificaciones se mantuvieron en el campo consolidado");
                $this->info("🔧 Puedes editar manualmente los registros para separar bonificaciones, horas extra y comisiones");
            } else {
                DB::rollback();
                $barra->finish();
                $this->newLine();
                $this->info("🔍 Simulación completada - no se hicieron cambios reales");
            }

            return 0;
        } catch (\Exception $e) {
            DB::rollback();
            $this->error("❌ Error durante la migración: " . $e->getMessage());
            return 1;
        }
    }
}
