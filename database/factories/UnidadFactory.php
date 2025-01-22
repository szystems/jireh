<?php

namespace Database\Factories;

use App\Models\Unidad;
use Illuminate\Database\Eloquent\Factories\Factory;

class UnidadFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Unidad::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre' => $this->faker->word,
            'abreviatura' => strtoupper($this->faker->lexify('???')),
            'tipo' => $this->faker->randomElement(['unidad', 'decimal']),
            'estado' => 1, // Por defecto, el estado es activo
        ];
    }
}
