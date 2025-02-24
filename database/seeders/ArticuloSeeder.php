<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Articulo;
use Illuminate\Support\Str;

class ArticuloSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 50; $i++) {
            Articulo::create([
                'codigo' => 'COD' . Str::padLeft($i, 4, '0'),
                'nombre' => 'Artículo ' . $i,
                'imagen' => null,
                'descripcion' => 'Descripción del artículo ' . $i,
                'precio_compra' => rand(100, 1000) / 10,
                'precio_venta' => rand(1000, 2000) / 10,
                'stock' => rand(10, 100),
                'stock_minimo' => rand(1, 10),
                'categoria_id' => 1, // Asegúrate de tener una categoría con ID 1
                'unidad_id' => 1, // Asegúrate de tener una unidad con ID 1
                'tipo_comision_vendedor_id' => 1, // Asegúrate de tener un tipo de comisión con ID 1
                'tipo_comision_trabajador_id' => 1, // Asegúrate de tener un tipo de comisión con ID 1
                'tipo' => 'articulo',
                'estado' => true,
            ]);
        }
    }
}
