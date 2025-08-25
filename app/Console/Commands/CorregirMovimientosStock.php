<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CorregirMovimientosStock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:corregir-movimientos {--dry-run : Ejecutar en modo de prueba sin hacer cambios}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige artículos que tienen stock pero no tienen movimientos registrados';

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
        
        $this->info('=== CORRECCIÓN DE ARTÍCULOS SIN MOVIMIENTOS INICIALES ===');
        $this->info('Fecha de ejecución: ' . Carbon::now()->format('Y-m-d H:i:s'));
        
        if ($dryRun) {
            $this->warn('MODO DE PRUEBA: No se realizarán cambios en la base de datos');
        }
        
        $this->line('');
        
        try {
            if (!$dryRun) {
                DB::beginTransaction();
            }
            
            // Buscar artículos que tienen stock pero no tienen movimientos
            $articulosSinMovimientos = DB::table('articulos as a')
                ->leftJoin('movimientos_stock as m', 'a.id', '=', 'm.articulo_id')
                ->where('a.estado', 1)
                ->where('a.tipo', 'articulo')
                ->where('a.stock', '>', 0)
                ->whereNull('m.id')
                ->select('a.id', 'a.codigo', 'a.nombre', 'a.stock', 'a.created_at')
                ->get();
            
            $this->info('Artículos encontrados sin movimientos: ' . $articulosSinMovimientos->count());
            
            if ($articulosSinMovimientos->count() == 0) {
                $this->info('No hay artículos que requieran corrección.');
                if (!$dryRun) {
                    DB::rollback();
                }
                return 0;
            }
            
            $corregidos = 0;
            $errores = 0;
            
            foreach ($articulosSinMovimientos as $articulo) {
                try {
                    if (!$dryRun) {
                        // Crear movimiento inicial para este artículo
                        DB::table('movimientos_stock')->insert([
                            'articulo_id' => $articulo->id,
                            'tipo' => 'AJUSTE_INICIAL',
                            'stock_anterior' => 0,
                            'stock_nuevo' => $articulo->stock,
                            'cantidad' => $articulo->stock,
                            'referencia_tipo' => 'AJUSTE_INICIAL',
                            'referencia_id' => $articulo->id,
                            'observaciones' => 'Movimiento inicial creado automáticamente para corregir inconsistencia de auditoría',
                            'user_id' => 1, // Usuario administrador
                            'created_at' => $articulo->created_at ? Carbon::parse($articulo->created_at) : Carbon::now()->subDays(30),
                            'updated_at' => Carbon::now()
                        ]);
                    }
                    
                    $this->line('✅ ' . ($dryRun ? '[SIMULADO] ' : '') . "Corregido: {$articulo->codigo} - {$articulo->nombre} (Stock: {$articulo->stock})");
                    $corregidos++;
                    
                } catch (\Exception $e) {
                    $this->error("❌ Error en artículo {$articulo->id}: " . $e->getMessage());
                    $errores++;
                }
            }
            
            if (!$dryRun) {
                DB::commit();
            }
            
            $this->line('');
            $this->info('=== RESUMEN DE CORRECCIÓN ===');
            $this->info('Total artículos procesados: ' . $articulosSinMovimientos->count());
            $this->info('Artículos corregidos: ' . $corregidos);
            $this->info('Errores: ' . $errores);
            $this->info('Estado: ' . ($errores == 0 ? 'EXITOSO' : 'COMPLETADO CON ERRORES'));
            
            if ($dryRun) {
                $this->warn('MODO DE PRUEBA: Ejecute sin --dry-run para aplicar los cambios');
            }
            
        } catch (\Exception $e) {
            if (!$dryRun) {
                DB::rollback();
            }
            $this->error('❌ ERROR CRÍTICO: ' . $e->getMessage());
            if (!$dryRun) {
                $this->error('La corrección fue revertida.');
            }
            return 1;
        }
        
        $this->info('Fecha de finalización: ' . Carbon::now()->format('Y-m-d H:i:s'));
        $this->info('=== FIN DE LA CORRECCIÓN ===');
        
        return 0;
    }
}
