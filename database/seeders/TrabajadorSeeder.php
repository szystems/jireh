<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trabajador;
use App\Models\TipoTrabajador;
use Faker\Factory as Faker;

class TrabajadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Obtener los tipos de trabajador disponibles
        $tipoTrabajadores = TipoTrabajador::all();

        // Si no hay tipos de trabajador, crear algunos básicos
        if ($tipoTrabajadores->count() == 0) {
            TipoTrabajador::create([
                'nombre' => 'Mecánico',
                'descripcion' => 'Trabajador encargado de reparaciones mecánicas',
                'aplica_comision' => true,
                'requiere_asignacion' => true,
                'estado' => 'activo'
            ]);

            TipoTrabajador::create([
                'nombre' => 'CarWash',
                'descripcion' => 'Trabajador encargado de lavado de vehículos',
                'aplica_comision' => true,
                'requiere_asignacion' => false,
                'estado' => 'activo'
            ]);

            // Recargar los tipos
            $tipoTrabajadores = TipoTrabajador::all();
        }

        // Crear 5 trabajadores con datos aleatorios
        foreach (range(1, 5) as $index) {
            // Asignar un tipo de trabajador aleatoriamente si hay disponibles
            $tipo_trabajador_id = $tipoTrabajadores->count() > 0
                ? $tipoTrabajadores->random()->id
                : null;

            Trabajador::create([
                'nombre' => $faker->firstName,
                'apellido' => $faker->lastName,
                'telefono' => $faker->phoneNumber,
                'direccion' => $faker->address,
                'email' => $faker->unique()->safeEmail,
                'nit' => $faker->numberBetween(100000, 999999),
                'dpi' => $faker->unique()->numerify('##########'),
                'tipo_trabajador_id' => $tipo_trabajador_id,
                'estado' => 1
            ]);
        }
    }
}
