<?php

namespace Database\Factories;

use App\Models\Vehiculo;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehiculoFactory extends Factory
{

    protected $model = Vehiculo::class;

    public function definition()
    {

        $marcas = ['Toyota', 'Ford', 'Chevrolet', 'Honda', 'Nissan'];
        $modelos = ['Corolla', 'F-150', 'Malibu', 'Civic', 'Altima'];
        $anoMin = 2000;
        $anoMax = date('Y'); // Año actual


        return [
            'cliente_id' => Cliente::factory(), // Crea un cliente automáticamente
            'marca' => $marcas[array_rand($marcas)], // Selecciona una marca aleatoria
            'modelo' => $modelos[array_rand($modelos)], // Selecciona un modelo aleatorio
            'ano' => rand($anoMin, $anoMax), // Selecciona un año aleatorio dentro del rango
            'color' => $this->faker->safeColorName, // Mantén el color aleatorio
            'placa' => $this->faker->unique()->regexify('[A-Z]{3}-[0-9]{3}'), // Ejemplo: ABC-123
            'vin' => $this->faker->unique()->regexify('[A-HJ-NPR-Z0-9]{17}'), // Ejemplo de VIN
        ];
    }
}
