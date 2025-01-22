<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{

    protected $model = Cliente::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->name,
            'fecha_nacimiento' => $this->faker->date(),
            'telefono' => $this->faker->optional()->phoneNumber,
            'celular' => $this->faker->phoneNumber,
            'direccion' => $this->faker->optional()->address,
            'email' => $this->faker->unique()->safeEmail,
            'dpi' => $this->faker->optional()->numerify('###########'),
            'nit' => $this->faker->optional()->numerify('###########'),
            'fotografia' => $this->faker->randomElement([
                'team-1.jpg',
                'team-2.jpg',
                'team-3.jpg',
                'team-4.jpg',
                'user.png',
                'user1.png',
                'user2.png',
                'user3.png',
                'user4.png',
                'user5.png',
            ]),
            'estado' => '1',
        ];
    }
}
