<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoTrabajador;

class TipoTrabajadorSeeder extends Seeder
{
    /**
     * Ejecuta los seeders de la base de datos.
     * Crea los tipos de trabajador esenciales para el funcionamiento del sistema.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('📋 Creando tipos de trabajador esenciales...');

        $tiposTrabajador = [
            [
                'nombre' => 'Mecánico',
                'descripcion' => 'Trabaja en reparación de vehículos',
                'aplica_comision' => true,
                'requiere_asignacion' => true,
                'tipo_comision' => 'fijo',
                'valor_comision' => 50.00,
                'porcentaje_comision' => null,
                'permite_multiples_trabajadores' => false,
                'configuracion_adicional' => null,
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Car Wash',
                'descripcion' => 'Trabaja en limpieza de vehículos',
                'aplica_comision' => true,
                'requiere_asignacion' => false,
                'tipo_comision' => 'variable',
                'valor_comision' => null,
                'porcentaje_comision' => null,
                'permite_multiples_trabajadores' => true,
                'configuracion_adicional' => null,
                'estado' => 'activo',
            ],
        ];

        foreach ($tiposTrabajador as $tipo) {
            TipoTrabajador::firstOrCreate(
                ['nombre' => $tipo['nombre']], // Buscar por nombre
                $tipo // Crear con todos estos datos si no existe
            );
        }

        $this->command->info('✅ Tipos de trabajador creados: ' . TipoTrabajador::count());
    }
}
