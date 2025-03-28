<?php

namespace Database\Factories;

use App\Models\Trabajador;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrabajadorFactory extends Factory
{
    protected $model = Trabajador::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'telefono' => $this->faker->optional()->phoneNumber,
            'direccion' => $this->faker->optional()->address,
            'no_documento' => $this->faker->optional()->numerify('###########'),
            'fecha_nacimiento' => $this->faker->optional()->date(),
            'estado' => $this->faker->randomElement(['activo', 'inactivo']),
        ];
    }
}
