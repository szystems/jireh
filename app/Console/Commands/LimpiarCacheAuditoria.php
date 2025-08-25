<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class LimpiarCacheAuditoria extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auditoria:limpiar-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia el cache de estadísticas del módulo de auditoría';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Limpiando cache de auditoría...');
        
        Cache::forget('auditoria_estadisticas_generales');
        
        $this->info('✓ Cache de estadísticas generales limpiado');
        $this->info('✓ Proceso completado exitosamente');
        
        return Command::SUCCESS;
    }
}
