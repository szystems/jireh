<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => 'Str::random(10)',
            'role_as' => $this->faker->randomElement([
                '0',
                '1',
            ]),
            'principal' => '0',
            'estado' => '1',
            'telefono'=> $this->faker->numberBetween($min = 10000000, $max = 99999999),
            'celular'=> $this->faker->numberBetween($min = 10000000, $max = 99999999),
            'direccion'=> $this->faker->streetAddress,
            'fecha_nacimiento' => $this->faker->dateTimeBetween('-60 years', '-5 years'),
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
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
