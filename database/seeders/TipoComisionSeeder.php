<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Importar la clase DB

class TipoComisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tipo_comisions')->insert([
            [
                'nombre' => 'Comisión Básica',
                'descripcion' => 'Comisión básica para vendedores',
                'porcentaje' => 5.00,
                'estado' => true,
            ],
            [
                'nombre' => 'Comisión Avanzada',
                'descripcion' => 'Comisión avanzada para vendedores experimentados',
                'porcentaje' => 10.00,
                'estado' => true,
            ],
            [
                'nombre' => 'Comisión Especial',
                'descripcion' => 'Comisión especial para vendedores con alto rendimiento',
                'porcentaje' => 15.00,
                'estado' => true,
            ],
        ]);
    }
}
