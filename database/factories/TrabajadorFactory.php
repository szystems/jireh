<?php

namespace Database\Factories;

use App\Models\Trabajador;
use App\Models\TipoTrabajador;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrabajadorFactory extends Factory
{
    protected $model = Trabajador::class;

    public function definition()
    {
        // Obtener un tipo_trabajador_id aleatorio
        $tipoTrabajadorId = TipoTrabajador::pluck('id')->random();
        
        return [
            'nombre' => $this->faker->firstName,
            'apellido' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'telefono' => $this->faker->phoneNumber,
            'direccion' => $this->faker->address,
            'nit' => $this->faker->optional()->numerify('#########'),
            'dpi' => $this->faker->optional()->numerify('###########'),
            'tipo_trabajador_id' => $tipoTrabajadorId,
            'tipo' => $tipoTrabajadorId, // Sincronizado con tipo_trabajador_id
            'estado' => 1,
        ];
    }
}
