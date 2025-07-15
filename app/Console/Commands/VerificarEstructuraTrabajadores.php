<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Trabajador;
use App\Models\TipoTrabajador;

class VerificarEstructuraTrabajadores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trabajadores:verificar-estructura';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica y corrige la estructura de las tablas trabajadors y tipo_trabajadors';

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
        $this->info('Verificando la estructura de las tablas trabajadors y tipo_trabajadors...');
        
        // Verificar si las tablas existen
        if (!Schema::hasTable('tipo_trabajadors')) {
            $this->error('La tabla tipo_trabajadors no existe. Por favor, ejecute las migraciones primero.');
            return 1;
        }
        
        if (!Schema::hasTable('trabajadors')) {
            $this->error('La tabla trabajadors no existe. Por favor, ejecute las migraciones primero.');
            return 1;
        }

        // Verificar si hay tipos de trabajador
        $tiposCount = TipoTrabajador::count();
        if ($tiposCount == 0) {
            $this->warn('No hay tipos de trabajador definidos. Insertando tipos predeterminados...');
            
            // Insertar tipos de trabajador predeterminados
            $this->insertarTiposPredeterminados();
            
            $this->info('Tipos de trabajador predeterminados insertados correctamente.');
        } else {
            $this->info("Se encontraron {$tiposCount} tipos de trabajador existentes.");
        }
        
        // Verificar trabajadores sin tipo_trabajador_id asignado pero con valor en campo 'tipo'
        $trabajadoresSinAsignar = Trabajador::whereNull('tipo_trabajador_id')
                                  ->whereNotNull('tipo')
                                  ->count();
        
        if ($trabajadoresSinAsignar > 0) {
            $this->warn("Existen {$trabajadoresSinAsignar} trabajadores con valor en 'tipo' pero sin 'tipo_trabajador_id'. Corrigiendo...");
            
            // Sincronizar tipo con tipo_trabajador_id
            $this->sincronizarTipoTrabajarador();
            
            $this->info('Los campos tipo_trabajador_id han sido actualizados correctamente.');
        }
        
        // Verificar si la estructura del modelo es correcta
        $this->info('Verificando el modelo Trabajador...');
        $trabajador = new Trabajador();
        $fillable = $trabajador->getFillable();
        
        if (!in_array('tipo', $fillable) || !in_array('tipo_trabajador_id', $fillable)) {
            $this->error('El modelo Trabajador no tiene correctamente definidos los campos tipo y tipo_trabajador_id en $fillable.');
        } else {
            $this->info('La estructura del modelo Trabajador es correcta.');
        }
        
        $this->info('Verificación completada.');
        return 0;
    }
    
    /**
     * Inserta los tipos de trabajador predeterminados
     */
    private function insertarTiposPredeterminados()
    {
        DB::table('tipo_trabajadors')->insert([
            [
                'nombre' => 'Mecánico',
                'descripcion' => 'Trabaja en reparación de vehículos',
                'aplica_comision' => true,
                'requiere_asignacion' => true,
                'tipo_comision' => 'fijo',
                'valor_comision' => 50.00,
                'permite_multiples_trabajadores' => false,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Car Wash',
                'descripcion' => 'Trabaja en limpieza de vehículos',
                'aplica_comision' => true,
                'requiere_asignacion' => false,
                'tipo_comision' => 'por_servicio',
                'valor_comision' => 25.00,
                'permite_multiples_trabajadores' => true,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Administrativo',
                'descripcion' => 'Personal administrativo',
                'aplica_comision' => false,
                'requiere_asignacion' => false,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
    
    /**
     * Sincroniza el campo tipo_trabajador_id con el campo tipo
     */
    private function sincronizarTipoTrabajarador()
    {
        $trabajadores = Trabajador::whereNull('tipo_trabajador_id')
                        ->whereNotNull('tipo')
                        ->get();
                        
        foreach ($trabajadores as $trabajador) {
            $trabajador->tipo_trabajador_id = $trabajador->tipo;
            $trabajador->save();
            $this->line("- Actualizado trabajador ID {$trabajador->id}: {$trabajador->nombre} {$trabajador->apellido}");
        }
    }
}
