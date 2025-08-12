<?php

namespace Database\Factories;

use App\Models\DetalleVenta;
use App\Models\Venta;
use App\Models\Articulo;
use Illuminate\Database\Eloquent\Factories\Factory;

class DetalleVentaFactory extends Factory
{
    protected $model = DetalleVenta::class;

    public function definition()
    {
        $cantidad = $this->faker->numberBetween(1, 5);
        $precioVenta = $this->faker->randomFloat(2, 10, 100);
        $precioCosto = $precioVenta * 0.7; // 70% del precio de venta
        $subTotal = $cantidad * $precioVenta;
        
        return [
            'venta_id' => Venta::factory(),
            'articulo_id' => Articulo::factory()->activo(),
            'cantidad' => $cantidad,
            'precio_venta' => $precioVenta, // Corregido: usar precio_venta
            'precio_costo' => $precioCosto,
            'sub_total' => $subTotal,
            'descuento_id' => null, // Sin descuento por defecto
            'usuario_id' => function (array $attributes) {
                return Venta::find($attributes['venta_id'])->usuario_id ?? 1;
            },
        ];
    }

    /**
     * Para servicios Car Wash
     */
    public function carwash()
    {
        return $this->state(function (array $attributes) {
            $articulo = Articulo::where('tipo', 'servicio')
                              ->where('comision_carwash', '>', 0)
                              ->inRandomOrder()
                              ->first();
            
            if (!$articulo) {
                $articulo = Articulo::factory()->carwash()->create();
            }
            
            $cantidad = 1; // Servicios normalmente son cantidad 1
            $precioUnitario = $articulo->precio_venta;
            
            return [
                'articulo_id' => $articulo->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'sub_total' => $cantidad * $precioUnitario,
            ];
        });
    }

    /**
     * Para servicios mecánicos
     */
    public function mecanico()
    {
        return $this->state(function (array $attributes) {
            $articulo = Articulo::where('tipo', 'servicio')
                              ->whereNotNull('mecanico_id')
                              ->inRandomOrder()
                              ->first();
            
            if (!$articulo) {
                $articulo = Articulo::factory()->mecanico()->create();
            }
            
            $cantidad = 1; // Servicios normalmente son cantidad 1
            $precioUnitario = $articulo->precio_venta;
            
            return [
                'articulo_id' => $articulo->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'sub_total' => $cantidad * $precioUnitario,
            ];
        });
    }

    /**
     * Para productos físicos
     */
    public function producto()
    {
        return $this->state(function (array $attributes) {
            $articulo = Articulo::where('tipo', 'producto')
                              ->inRandomOrder()
                              ->first();
            
            if (!$articulo) {
                $articulo = Articulo::factory()->producto()->create();
            }
            
            $cantidad = $this->faker->numberBetween(1, 10);
            $precioUnitario = $articulo->precio_venta;
            
            return [
                'articulo_id' => $articulo->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'sub_total' => $cantidad * $precioUnitario,
            ];
        });
    }
}
