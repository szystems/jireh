<?php

namespace Tests\Unit;

use App\Models\Articulo;
use Tests\TestCase;

class ArticuloRentabilidadTest extends TestCase
{
    public function test_el_caso_de_emilio_marca_cuarenta_porciento_y_conserva_la_ganancia()
    {
        $articulo = new Articulo([
            'tipo' => 'articulo',
            'precio_compra' => 130,
            'precio_venta' => 203.84,
        ]);

        $desglose = $articulo->desgloseRentabilidad(12);

        $this->assertEquals(182.00, round($desglose['precio_base'], 2));
        $this->assertEquals(21.84, round($desglose['iva'], 2));
        $this->assertEquals(52.00, round($desglose['ganancia'], 2));
        $this->assertEquals(40.00, round($desglose['margen'], 2));
        $this->assertNotEquals(34.25, round($desglose['margen'], 2));
    }

    public function test_sin_iva_el_margen_es_la_ganancia_sobre_la_compra()
    {
        $articulo = new Articulo([
            'tipo' => 'articulo',
            'precio_compra' => 100,
            'precio_venta' => 140,
        ]);

        $desglose = $articulo->desgloseRentabilidad(0);

        $this->assertEquals(140.00, round($desglose['precio_base'], 2));
        $this->assertEquals(0.00, round($desglose['iva'], 2));
        $this->assertEquals(40.00, round($desglose['ganancia'], 2));
        $this->assertEquals(40.00, round($desglose['margen'], 2));
    }

    public function test_en_un_servicio_las_comisiones_si_restan_y_el_iva_no_entra_en_la_base()
    {
        $articulo = new Articulo([
            'tipo' => 'servicio',
            'precio_compra' => 130,
            'precio_venta' => 203.84,
            'costo_mecanico' => 15,
            'comision_carwash' => 5,
        ]);

        $desglose = $articulo->desgloseRentabilidad(12);

        $this->assertEquals(20.00, round($desglose['comisiones'], 2));
        $this->assertEquals(32.00, round($desglose['ganancia'], 2));
        $this->assertEquals(21.33, round($desglose['margen'], 2));
    }

    public function test_sin_costo_el_margen_queda_en_cero()
    {
        $articulo = new Articulo([
            'tipo' => 'articulo',
            'precio_compra' => 0,
            'precio_venta' => 112,
        ]);

        $desglose = $articulo->desgloseRentabilidad(12);

        $this->assertEquals(0.00, $desglose['margen']);
        $this->assertEquals(100.00, round($desglose['precio_base'], 2));
    }
}
