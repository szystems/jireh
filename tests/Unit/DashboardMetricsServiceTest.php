<?php

namespace Tests\Unit;

use App\Services\DashboardMetricsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DashboardMetricsServiceTest extends TestCase
{
    /** @var DashboardMetricsService */
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => false,
            'cache.default' => 'array',
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');
        DB::setDefaultConnection('sqlite');

        $this->createTables();
        $this->service = new DashboardMetricsService();
        Carbon::setTestNow(Carbon::create(2026, 9, 10, 12, 0, 0));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_suma_ventas_del_periodo_en_sql_sin_cargar_colecciones()
    {
        $this->insertVenta(1, '2026-09-10', 1, 1, 150.50);
        $this->insertDetalle(1, 1, 150.50);
        $this->insertDetalle(2, 1, 49.50);
        $this->insertVenta(2, '2026-09-10', 1, 0, 999);
        $this->insertDetalle(3, 2, 999);
        $this->insertVenta(3, '2026-08-01', 1, 1, 80);
        $this->insertDetalle(4, 3, 80);

        $total = $this->service->sumVentasPorPeriodo('2026-09-01', '2026-09-30');

        $this->assertEquals(200.0, $total);
    }

    public function test_filtra_ventas_por_vendedor()
    {
        $this->insertVenta(1, '2026-09-10', 7, 1, 100);
        $this->insertDetalle(1, 1, 100);
        $this->insertVenta(2, '2026-09-10', 8, 1, 40);
        $this->insertDetalle(2, 2, 40);

        $this->assertEquals(100.0, $this->service->sumVentasPorPeriodo('2026-09-01', '2026-09-30', 7));
        $this->assertEquals(40.0, $this->service->sumVentasPorPeriodo('2026-09-01', '2026-09-30', 8));
    }

    public function test_suma_ingresos_como_precio_por_cantidad()
    {
        DB::table('ingresos')->insert([
            'id' => 1,
            'fecha' => '2026-09-05',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('detalle_ingresos')->insert([
            'id' => 1,
            'ingreso_id' => 1,
            'precio_compra' => 10,
            'cantidad' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('detalle_ingresos')->insert([
            'id' => 2,
            'ingreso_id' => 1,
            'precio_compra' => 5,
            'cantidad' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertEquals(40.0, $this->service->sumIngresosPorPeriodo('2026-09-01', '2026-09-30'));
    }

    public function test_valor_inventario_usa_stock_por_precio()
    {
        DB::table('articulos')->insert([
            ['id' => 1, 'stock' => 2, 'precio_venta' => 10, 'stock_minimo' => 0],
            ['id' => 2, 'stock' => 0, 'precio_venta' => 99, 'stock_minimo' => 0],
            ['id' => 3, 'stock' => 4, 'precio_venta' => 5, 'stock_minimo' => 1],
        ]);

        $this->assertEquals(40.0, $this->service->valorInventario());
    }

    public function test_agrupa_ventas_por_mes_en_una_consulta()
    {
        $this->insertVenta(1, '2026-09-02', 1, 1, 30);
        $this->insertDetalle(1, 1, 30);
        $this->insertVenta(2, '2026-08-15', 1, 1, 20);
        $this->insertDetalle(2, 2, 20);

        $serie = $this->service->ventasAgrupadasPorMes(3);

        $this->assertCount(3, $serie['totales']);
        $this->assertCount(3, $serie['labels']);
        $this->assertEquals(20.0, $serie['totales'][1]);
        $this->assertEquals(30.0, $serie['totales'][2]);
    }

    public function test_agrupa_ventas_por_vendedor_del_mes()
    {
        $this->insertVenta(1, '2026-09-02', 4, 1, 15);
        $this->insertDetalle(1, 1, 15);
        $this->insertVenta(2, '2026-09-03', 4, 1, 5);
        $this->insertDetalle(2, 2, 5);
        $this->insertVenta(3, '2026-09-03', 9, 1, 8);
        $this->insertDetalle(3, 3, 8);

        $porVendedor = $this->service->ventasPorVendedorEnMes(2026, 9);

        $this->assertEquals(20.0, $porVendedor[4]);
        $this->assertEquals(8.0, $porVendedor[9]);
    }

    private function createTables(): void
    {
        Schema::create('ventas', function ($table) {
            $table->id();
            $table->date('fecha');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('detalle_ventas', function ($table) {
            $table->id();
            $table->unsignedBigInteger('venta_id');
            $table->decimal('sub_total', 10, 2);
            $table->timestamps();
        });

        Schema::create('ingresos', function ($table) {
            $table->id();
            $table->date('fecha');
            $table->timestamps();
        });

        Schema::create('detalle_ingresos', function ($table) {
            $table->id();
            $table->unsignedBigInteger('ingreso_id');
            $table->decimal('precio_compra', 10, 2);
            $table->decimal('cantidad', 10, 2);
            $table->timestamps();
        });

        Schema::create('articulos', function ($table) {
            $table->id();
            $table->decimal('stock', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);
            $table->decimal('stock_minimo', 10, 2)->default(0);
        });
    }

    private function insertVenta(int $id, string $fecha, int $usuarioId, int $estado, float $ignoredTotal): void
    {
        DB::table('ventas')->insert([
            'id' => $id,
            'fecha' => $fecha,
            'usuario_id' => $usuarioId,
            'estado' => $estado,
            'created_at' => $fecha . ' 10:00:00',
            'updated_at' => $fecha . ' 10:00:00',
        ]);
    }

    private function insertDetalle(int $id, int $ventaId, float $subTotal): void
    {
        DB::table('detalle_ventas')->insert([
            'id' => $id,
            'venta_id' => $ventaId,
            'sub_total' => $subTotal,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
