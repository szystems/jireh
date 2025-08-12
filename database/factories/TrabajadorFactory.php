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

    /**
     * Estado específico para mecánicos
     */
    public function mecanico()
    {
        return $this->state(function (array $attributes) {
            // Buscar el ID del tipo "Mecánico"
            $mecanicoTipo = TipoTrabajador::where('nombre', 'like', '%mecánico%')
                                          ->orWhere('nombre', 'like', '%Mecánico%')
                                          ->first();
            $mecanicoTipoId = $mecanicoTipo ? $mecanicoTipo->id : 1;
            
            return [
                'tipo_trabajador_id' => $mecanicoTipoId,
                'tipo' => $mecanicoTipoId,
            ];
        });
    }

    /**
     * Estado específico para trabajadores Car Wash
     */
    public function carwash()
    {
        return $this->state(function (array $attributes) {
            // Buscar el ID del tipo "Car Wash"
            $carwashTipo = TipoTrabajador::where('nombre', 'like', '%Car Wash%')
                                         ->orWhere('nombre', 'like', '%carwash%')
                                         ->first();
            $carwashTipoId = $carwashTipo ? $carwashTipo->id : 2;
            
            return [
                'tipo_trabajador_id' => $carwashTipoId,
                'tipo' => $carwashTipoId,
            ];
        });
    }
}
