<?php

namespace Database\Factories;

use App\Models\Articulo;
use App\Models\Categoria;
use App\Models\Unidad;
use App\Models\Trabajador;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticuloFactory extends Factory
{
    protected $model = Articulo::class;

    public function definition()
    {
        $tipo = $this->faker->randomElement(['producto', 'servicio']);
        $esCarWash = $this->faker->boolean(30); // 30% probabilidad de ser car wash
        $esMecanico = $tipo === 'servicio' && !$esCarWash && $this->faker->boolean(40); // 40% de servicios son mecánicos
        
        return [
            'codigo' => 'ART-' . $this->faker->unique()->numberBetween(1000, 9999),
            'nombre' => $this->generarNombreArticulo($tipo, $esCarWash),
            'imagen' => null,
            'descripcion' => $this->faker->optional(0.8)->sentence(10),
            'precio_compra' => $tipo === 'producto' ? $this->faker->randomFloat(2, 5, 100) : 0,
            'precio_venta' => $this->faker->randomFloat(2, 10, 200),
            'stock' => $tipo === 'producto' ? $this->faker->numberBetween(0, 100) : 0,
            'stock_minimo' => $tipo === 'producto' ? $this->faker->numberBetween(5, 20) : 0,
            'categoria_id' => Categoria::factory(),
            'unidad_id' => Unidad::factory(),
            'mecanico_id' => $esMecanico ? Trabajador::factory()->mecanico() : null,
            'costo_mecanico' => $esMecanico ? $this->faker->randomFloat(2, 15, 80) : 0,
            'comision_carwash' => $esCarWash ? $this->faker->randomFloat(2, 5, 25) : 0,
            'tipo' => $tipo,
            'estado' => $this->faker->randomElement([0, 1]), // 0 = inactivo, 1 = activo
        ];
    }

    /**
     * Generar nombre según tipo de artículo
     */
    private function generarNombreArticulo($tipo, $esCarWash)
    {
        if ($tipo === 'servicio') {
            if ($esCarWash) {
                return $this->faker->randomElement([
                    'Lavado Básico',
                    'Lavado Completo',
                    'Encerado Premium',
                    'Lavado y Aspirado',
                    'Detallado Interior',
                    'Limpieza de Motor',
                    'Pulido de Faros',
                    'Tratamiento de Cuero'
                ]);
            } else {
                return $this->faker->randomElement([
                    'Cambio de Aceite',
                    'Alineación y Balanceo',
                    'Revisión de Frenos',
                    'Cambio de Filtros',
                    'Diagnóstico Computarizado',
                    'Reparación de Motor',
                    'Cambio de Llantas',
                    'Mantenimiento Preventivo'
                ]);
            }
        } else {
            return $this->faker->randomElement([
                'Aceite de Motor',
                'Filtro de Aire',
                'Pastillas de Freno',
                'Bujías',
                'Anticongelante',
                'Llanta 195/65R15',
                'Batería 12V',
                'Limpiador de Motor',
                'Cera para Auto',
                'Shampoo para Carros'
            ]);
        }
    }

    /**
     * Estado específico para artículos activos
     */
    public function activo()
    {
        return $this->state(function (array $attributes) {
            return [
                'estado' => 1,
            ];
        });
    }

    /**
     * Estado específico para servicios de car wash
     */
    public function carwash()
    {
        return $this->state(function (array $attributes) {
            return [
                'tipo' => 'servicio',
                'comision_carwash' => $this->faker->randomFloat(2, 8, 30),
                'precio_compra' => 0,
                'stock' => 0,
                'stock_minimo' => 0,
                'mecanico_id' => null,
                'costo_mecanico' => 0,
            ];
        });
    }

    /**
     * Estado específico para servicios mecánicos
     */
    public function mecanico()
    {
        return $this->state(function (array $attributes) {
            return [
                'tipo' => 'servicio',
                'mecanico_id' => Trabajador::factory()->mecanico(),
                'costo_mecanico' => $this->faker->randomFloat(2, 20, 100),
                'precio_compra' => 0,
                'stock' => 0,
                'stock_minimo' => 0,
                'comision_carwash' => 0,
            ];
        });
    }

    /**
     * Estado específico para productos físicos
     */
    public function producto()
    {
        return $this->state(function (array $attributes) {
            return [
                'tipo' => 'articulo', // Corregido: debe ser 'articulo' según la migración
                'stock' => $this->faker->numberBetween(10, 100),
                'stock_minimo' => $this->faker->numberBetween(5, 15),
                'precio_compra' => $this->faker->randomFloat(2, 10, 80),
                'mecanico_id' => null,
                'costo_mecanico' => 0,
                'comision_carwash' => 0,
            ];
        });
    }
}
