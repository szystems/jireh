<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MetaVenta;

class MetaVentaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Verificar si ya existen metas para evitar duplicados
        if (MetaVenta::count() > 0) {
            return;
        }

        // Metas Mensuales
        MetaVenta::create([
            'nombre' => 'Bronce Mensual',
            'descripcion' => 'Meta básica mensual para vendedores nuevos',
            'monto_minimo' => 5000.00,
            'monto_maximo' => 9999.99,
            'porcentaje_comision' => 3.0,
            'periodo' => 'mensual',
            'estado' => true
        ]);

        MetaVenta::create([
            'nombre' => 'Plata Mensual',
            'descripcion' => 'Meta intermedia mensual para vendedores regulares',
            'monto_minimo' => 10000.00,
            'monto_maximo' => 19999.99,
            'porcentaje_comision' => 5.0,
            'periodo' => 'mensual',
            'estado' => true
        ]);

        MetaVenta::create([
            'nombre' => 'Oro Mensual',
            'descripcion' => 'Meta premium mensual para vendedores destacados',
            'monto_minimo' => 20000.00,
            'monto_maximo' => 999999.99,
            'porcentaje_comision' => 8.0,
            'periodo' => 'mensual',
            'estado' => true
        ]);

        // Metas Trimestrales
        MetaVenta::create([
            'nombre' => 'Bronce Trimestral',
            'descripcion' => 'Meta básica trimestral',
            'monto_minimo' => 15000.00,
            'monto_maximo' => 29999.99,
            'porcentaje_comision' => 4.0,
            'periodo' => 'trimestral',
            'estado' => true
        ]);

        MetaVenta::create([
            'nombre' => 'Plata Trimestral',
            'descripcion' => 'Meta intermedia trimestral',
            'monto_minimo' => 30000.00,
            'monto_maximo' => 59999.99,
            'porcentaje_comision' => 6.0,
            'periodo' => 'trimestral',
            'estado' => true
        ]);

        MetaVenta::create([
            'nombre' => 'Oro Trimestral',
            'descripcion' => 'Meta premium trimestral',
            'monto_minimo' => 60000.00,
            'monto_maximo' => 999999.99,
            'porcentaje_comision' => 10.0,
            'periodo' => 'trimestral',
            'estado' => true
        ]);

        // Metas Semestrales
        MetaVenta::create([
            'nombre' => 'Bronce Semestral',
            'descripcion' => 'Meta básica semestral',
            'monto_minimo' => 30000.00,
            'monto_maximo' => 59999.99,
            'porcentaje_comision' => 5.0,
            'periodo' => 'semestral',
            'estado' => true
        ]);

        MetaVenta::create([
            'nombre' => 'Plata Semestral',
            'descripcion' => 'Meta intermedia semestral',
            'monto_minimo' => 60000.00,
            'monto_maximo' => 119999.99,
            'porcentaje_comision' => 7.5,
            'periodo' => 'semestral',
            'estado' => true
        ]);

        MetaVenta::create([
            'nombre' => 'Oro Semestral',
            'descripcion' => 'Meta premium semestral',
            'monto_minimo' => 120000.00,
            'monto_maximo' => 999999.99,
            'porcentaje_comision' => 12.0,
            'periodo' => 'semestral',
            'estado' => true
        ]);

        // Metas Anuales
        MetaVenta::create([
            'nombre' => 'Bronce Anual',
            'descripcion' => 'Meta básica anual',
            'monto_minimo' => 60000.00,
            'monto_maximo' => 119999.99,
            'porcentaje_comision' => 6.0,
            'periodo' => 'anual',
            'estado' => true
        ]);

        MetaVenta::create([
            'nombre' => 'Plata Anual',
            'descripcion' => 'Meta intermedia anual',
            'monto_minimo' => 120000.00,
            'monto_maximo' => 239999.99,
            'porcentaje_comision' => 9.0,
            'periodo' => 'anual',
            'estado' => true
        ]);

        MetaVenta::create([
            'nombre' => 'Oro Anual',
            'descripcion' => 'Meta premium anual',
            'monto_minimo' => 240000.00,
            'monto_maximo' => 999999.99,
            'porcentaje_comision' => 15.0,
            'periodo' => 'anual',
            'estado' => true
        ]);
    }
}
