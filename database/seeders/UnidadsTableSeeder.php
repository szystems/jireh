<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unidad;

class UnidadsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Unidad::create([
            'nombre' => 'Unidad',
            'abreviatura' => 'UND',
            'tipo' => 'unidad',
            'estado' => 1,
        ]);

        Unidad::create([
            'nombre' => 'Kilogramo',
            'abreviatura' => 'KG',
            'tipo' => 'decimal',
            'estado' => 1,
        ]);

        Unidad::create([
            'nombre' => 'Litro',
            'abreviatura' => 'L',
            'tipo' => 'decimal',
            'estado' => 1,
        ]);

        Unidad::create([
            'nombre' => 'Metro',
            'abreviatura' => 'M',
            'tipo' => 'decimal',
            'estado' => 1,
        ]);

        Unidad::create([
            'nombre' => 'Centímetro',
            'abreviatura' => 'CM',
            'tipo' => 'decimal',
            'estado' => 1,
        ]);

        Unidad::create([
            'nombre' => 'Mililitro',
            'abreviatura' => 'ML',
            'tipo' => 'decimal',
            'estado' => 1,
        ]);
    }
}
