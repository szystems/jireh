<?php

namespace Database\Factories;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class VentaFactory extends Factory
{
    protected $model = Venta::class;

    public function definition()
    {
        $fecha = $this->faker->dateTimeBetween('-6 months', 'now');
        $tipoVenta = $this->faker->randomElement(['CDS', 'Car Wash']);
        
        // Buscar un usuario vendedor existente
        $usuario = User::where('role_as', 1)->inRandomOrder()->first();
        $usuarioId = $usuario ? $usuario->id : User::factory();
        
        return [
            'fecha' => $fecha,
            'cliente_id' => Cliente::factory(),
            'usuario_id' => $usuarioId,
            'tipo_venta' => $tipoVenta,
            'estado' => 1, // Activa
            'estado_pago' => 'pendiente',
            'numero_factura' => 'FAC-' . $this->faker->unique()->numberBetween(1000, 9999),
            'created_at' => $fecha,
            'updated_at' => $fecha,
        ];
    }

    /**
     * Estado específico para ventas Car Wash
     */
    public function carwash()
    {
        return $this->state(function (array $attributes) {
            return [
                'tipo_venta' => 'Car Wash',
            ];
        });
    }

    /**
     * Estado específico para ventas CDS
     */
    public function cds()
    {
        return $this->state(function (array $attributes) {
            return [
                'tipo_venta' => 'CDS',
            ];
        });
    }

    /**
     * Ventas recientes (último mes)
     */
    public function reciente()
    {
        return $this->state(function (array $attributes) {
            $fecha = $this->faker->dateTimeBetween('-1 month', 'now');
            return [
                'fecha' => $fecha,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ];
        });
    }

    /**
     * Ventas del mes actual
     */
    public function mesActual()
    {
        return $this->state(function (array $attributes) {
            $fecha = $this->faker->dateTimeBetween(Carbon::now()->startOfMonth(), 'now');
            return [
                'fecha' => $fecha,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ];
        });
    }
}
