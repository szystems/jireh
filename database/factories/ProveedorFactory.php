<?php

namespace Database\Factories;

use App\Models\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProveedorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    protected $model = Proveedor::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->company(),
            'nit' => $this->faker->optional()->numerify('########-#'),
            'contacto' => $this->faker->name(),
            'telefono' => $this->faker->optional()->phoneNumber(),
            'celular' => $this->faker->optional()->phoneNumber(),
            'direccion' => $this->faker->address(),
            'email' => $this->faker->unique()->safeEmail(),
            'banco' => $this->faker->company(),
            'nombre_cuenta' => $this->faker->company(),
            'tipo_cuenta' => $this->faker->randomElement(['Monetaria','Ahorro']),
            'numero_cuenta' => $this->faker->unique()->randomNumber(8, true),
        ];
    }
}
