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

        // Verificar si hay tipos de trabajador, ya deberían existir por la migración
        $tipoTrabajadores = TipoTrabajador::all();

        if ($tipoTrabajadores->count() == 0) {
            // Si por alguna razón no existen, mostrar un mensaje de error
            echo "Error: No se encontraron tipos de trabajador. Asegúrese de que la migración 2025_05_15_121720_insert_default_tipo_trabajadors.php se haya ejecutado correctamente.\n";
            return;
        }

        // Crear 5 trabajadores con datos aleatorios
        foreach (range(1, 5) as $index) {
            // Asignar un tipo de trabajador aleatoriamente
            $tipoTrabajador = $tipoTrabajadores->random();

            Trabajador::create([
                'nombre' => $faker->firstName,
                'apellido' => $faker->lastName,
                'telefono' => $faker->phoneNumber,
                'direccion' => $faker->address,
                'email' => $faker->unique()->safeEmail,
                'nit' => $faker->numberBetween(100000, 999999),
                'dpi' => $faker->unique()->numerify('##########'),
                'tipo_trabajador_id' => $tipoTrabajador->id,
                'tipo' => $tipoTrabajador->id, // Sincronizar con tipo_trabajador_id
                'estado' => 1
            ]);
        }
    }
}
