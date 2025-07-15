<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trabajador;
use App\Models\TipoTrabajador;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SincronizarTipoTrabajadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Este seeder está diseñado para ayudar a migrar datos donde el campo 'tipo'
     * podría contener valores antiguos que necesitan ser asociados a los nuevos
     * registros en tipo_trabajadors.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Iniciando sincronización de tipo_trabajador_id para trabajadores existentes...');
        
        // Crear un mapeo de valores antiguos a nuevos IDs de tipo_trabajador
        $mapeoTipos = [
            'mecanico' => 1,  // ID del tipo Mecánico
            'mecánico' => 1,
            'carwash' => 2,   // ID del tipo Car Wash
            'car wash' => 2,
            'car_wash' => 2,
            'administrativo' => 3, // ID del tipo Administrativo
            'admin' => 3
        ];
        
        // También mapeamos valores numéricos si existieran
        for ($i = 1; $i <= 3; $i++) {
            $mapeoTipos[(string)$i] = $i;
        }
        
        // Obtener todos los trabajadores
        $trabajadores = Trabajador::all();
        $contador = 0;
        
        foreach ($trabajadores as $trabajador) {
            $tipoOriginal = $trabajador->tipo;
            
            // Si ya tiene un tipo_trabajador_id asignado, lo respetamos
            if (!empty($trabajador->tipo_trabajador_id)) {
                // Solo nos aseguramos que el campo 'tipo' esté sincronizado
                if ($trabajador->tipo != $trabajador->tipo_trabajador_id) {
                    $trabajador->tipo = $trabajador->tipo_trabajador_id;
                    $trabajador->save();
                    $contador++;
                    $this->command->info("Trabajador ID {$trabajador->id}: Sincronizado campo 'tipo' con tipo_trabajador_id existente ({$trabajador->tipo_trabajador_id}).");
                }
                continue;
            }
            
            // Intentamos determinar el tipo_trabajador_id basado en el valor de 'tipo'
            $nuevoTipoId = null;
            
            // Si es un string, buscamos en nuestro mapeo
            if (is_string($tipoOriginal)) {
                $tipoOriginalLower = strtolower(trim($tipoOriginal));
                if (isset($mapeoTipos[$tipoOriginalLower])) {
                    $nuevoTipoId = $mapeoTipos[$tipoOriginalLower];
                } else {
                    // Intentamos buscar por nombre en la tabla tipo_trabajadors
                    $tipoTrabajador = TipoTrabajador::where('nombre', 'like', "%{$tipoOriginalLower}%")->first();
                    if ($tipoTrabajador) {
                        $nuevoTipoId = $tipoTrabajador->id;
                    }
                }
            } 
            // Si es numérico, verificamos si corresponde a un ID válido
            elseif (is_numeric($tipoOriginal)) {
                $tipoTrabajador = TipoTrabajador::find($tipoOriginal);
                if ($tipoTrabajador) {
                    $nuevoTipoId = $tipoTrabajador->id;
                }
            }
            
            // Si encontramos un tipo_trabajador_id, actualizamos
            if ($nuevoTipoId) {
                $trabajador->tipo_trabajador_id = $nuevoTipoId;
                $trabajador->tipo = $nuevoTipoId; // Mantenemos sincronizado
                $trabajador->save();
                $contador++;
                $this->command->info("Trabajador ID {$trabajador->id}: Asignado tipo_trabajador_id {$nuevoTipoId} basado en el valor original '{$tipoOriginal}'.");
            } else {
                // Si no pudimos mapear, lo dejamos como predeterminado (el primer tipo)
                if (TipoTrabajador::count() > 0) {
                    $primerTipo = TipoTrabajador::first();
                    $trabajador->tipo_trabajador_id = $primerTipo->id;
                    $trabajador->tipo = $primerTipo->id;
                    $trabajador->save();
                    $contador++;
                    $this->command->warn("Trabajador ID {$trabajador->id}: No se pudo determinar el tipo_trabajador_id basado en '{$tipoOriginal}'. Asignado tipo predeterminado ID {$primerTipo->id}.");
                } else {
                    $this->command->error("Error: No hay tipos de trabajador definidos. No se pudo asignar un tipo predeterminado al trabajador ID {$trabajador->id}.");
                }
            }
        }
        
        $this->command->info("Sincronización completada. Se actualizaron {$contador} trabajadores.");
    }
}
