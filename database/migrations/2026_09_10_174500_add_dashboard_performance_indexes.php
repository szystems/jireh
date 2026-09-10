<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDashboardPerformanceIndexes extends Migration
{
    public function up()
    {
        $this->addIndexIfMissing('ventas', ['fecha', 'estado'], 'ventas_fecha_estado_index');
        $this->addIndexIfMissing('ventas', ['usuario_id', 'fecha', 'estado'], 'ventas_usuario_fecha_estado_index');
        $this->addIndexIfMissing('detalle_ventas', ['venta_id'], 'detalle_ventas_venta_id_index');
        $this->addIndexIfMissing('articulos', ['stock', 'stock_minimo'], 'articulos_stock_stock_minimo_index');
        $this->addIndexIfMissing('ingresos', ['fecha'], 'ingresos_fecha_index');
        $this->addIndexIfMissing('comisiones', ['estado', 'fecha_calculo'], 'comisiones_estado_fecha_calculo_index');
    }

    public function down()
    {
        $this->dropIndexIfExists('ventas', 'ventas_fecha_estado_index');
        $this->dropIndexIfExists('ventas', 'ventas_usuario_fecha_estado_index');
        $this->dropIndexIfExists('detalle_ventas', 'detalle_ventas_venta_id_index');
        $this->dropIndexIfExists('articulos', 'articulos_stock_stock_minimo_index');
        $this->dropIndexIfExists('ingresos', 'ingresos_fecha_index');
        $this->dropIndexIfExists('comisiones', 'comisiones_estado_fecha_calculo_index');
    }

    private function addIndexIfMissing(string $table, array $columns, string $name): void
    {
        if (!Schema::hasTable($table) || $this->indexExists($table, $name)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $name) {
            $blueprint->index($columns, $name);
        });
    }

    private function dropIndexIfExists(string $table, string $name): void
    {
        if (!Schema::hasTable($table) || !$this->indexExists($table, $name)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($name) {
            $blueprint->dropIndex($name);
        });
    }

    private function indexExists(string $table, string $name): bool
    {
        $connection = Schema::getConnection();
        $schemaManager = method_exists($connection, 'getDoctrineSchemaManager')
            ? $connection->getDoctrineSchemaManager()
            : null;

        if ($schemaManager) {
            $indexes = $schemaManager->listTableIndexes($table);
            return isset($indexes[$name]);
        }

        return false;
    }
}
