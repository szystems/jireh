<?php

namespace Database\Factories;

use App\Models\Comision;
use App\Models\User;
use App\Models\Trabajador;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Articulo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ComisionFactory extends Factory
{
    protected $model = Comision::class;

    public function definition()
    {
        $fecha = $this->faker->dateTimeBetween('-3 months', 'now');
        $monto = $this->faker->randomFloat(2, 10, 150);
        
        return [
            'commissionable_id' => 1,
            'commissionable_type' => 'App\Models\User',
            'tipo_comision' => 'venta_meta',
            'monto' => $monto,
            'porcentaje' => $this->faker->randomFloat(2, 2, 8),
            'detalle_venta_id' => null,
            'venta_id' => Venta::factory(),
            'articulo_id' => null,
            'estado' => $this->faker->randomElement(['pendiente', 'pagado']),
            'fecha_calculo' => $fecha,
            'created_at' => $fecha,
            'updated_at' => $fecha,
        ];
    }

    /**
     * Comisión para vendedores por meta
     */
    public function vendedor()
    {
        return $this->state(function (array $attributes) {
            $usuario = User::where('role_as', 1)->inRandomOrder()->first();
            if (!$usuario) {
                $usuario = User::factory()->create(['role_as' => 1]);
            }
            
            return [
                'commissionable_id' => $usuario->id,
                'commissionable_type' => 'App\Models\User',
                'tipo_comision' => 'venta_meta',
                'porcentaje' => $this->faker->randomFloat(2, 3, 10),
                'detalle_venta_id' => null,
                'articulo_id' => null,
            ];
        });
    }

    /**
     * Comisión para mecánicos
     */
    public function mecanico()
    {
        return $this->state(function (array $attributes) {
            $trabajador = Trabajador::whereHas('tipoTrabajador', function($q) {
                $q->where('nombre', 'like', '%mecánico%');
            })->inRandomOrder()->first();
            
            if (!$trabajador) {
                $trabajador = Trabajador::factory()->mecanico()->create();
            }
            
            $detalleVenta = DetalleVenta::factory()->mecanico()->create();
            $articulo = $detalleVenta->articulo;
            
            return [
                'commissionable_id' => $trabajador->id,
                'commissionable_type' => 'App\Models\Trabajador',
                'tipo_comision' => 'mecanico',
                'monto' => $articulo->costo_mecanico ?? $this->faker->randomFloat(2, 15, 80),
                'porcentaje' => null,
                'detalle_venta_id' => $detalleVenta->id,
                'venta_id' => $detalleVenta->venta_id,
                'articulo_id' => $articulo->id,
            ];
        });
    }

    /**
     * Comisión para Car Wash
     */
    public function carwash()
    {
        return $this->state(function (array $attributes) {
            $trabajador = Trabajador::whereHas('tipoTrabajador', function($q) {
                $q->where('nombre', 'like', '%Car Wash%');
            })->inRandomOrder()->first();
            
            if (!$trabajador) {
                $trabajador = Trabajador::factory()->carwash()->create();
            }
            
            $detalleVenta = DetalleVenta::factory()->carwash()->create();
            $articulo = $detalleVenta->articulo;
            
            return [
                'commissionable_id' => $trabajador->id,
                'commissionable_type' => 'App\Models\Trabajador',
                'tipo_comision' => 'carwash',
                'monto' => $articulo->comision_carwash ?? $this->faker->randomFloat(2, 5, 30),
                'porcentaje' => null,
                'detalle_venta_id' => $detalleVenta->id,
                'venta_id' => $detalleVenta->venta_id,
                'articulo_id' => $articulo->id,
            ];
        });
    }

    /**
     * Estado pendiente de pago
     */
    public function pendiente()
    {
        return $this->state(function (array $attributes) {
            return [
                'estado' => 'pendiente',
            ];
        });
    }

    /**
     * Estado pagado
     */
    public function pagado()
    {
        return $this->state(function (array $attributes) {
            return [
                'estado' => 'pagado',
            ];
        });
    }

    /**
     * Comisiones recientes (último mes)
     */
    public function reciente()
    {
        return $this->state(function (array $attributes) {
            $fecha = $this->faker->dateTimeBetween('-1 month', 'now');
            return [
                'fecha_calculo' => $fecha,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ];
        });
    }
}
