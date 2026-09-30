<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExtendDepartamentosVentaCotizacion extends Migration
{
    /**
     * Agrega departamentos de factura sin renombrar Car Wash ni CDS.
     * Las comisiones no usan esta columna para calcularse.
     */
    private $valores = [
        'Car Wash',
        'CDS',
        'Autos',
        'Accesorios',
        'Pintura Automotriz',
    ];

    public function up()
    {
        $this->extenderEnum('ventas', 'tipo_venta');
        $this->extenderEnum('cotizaciones', 'tipo_cotizacion');
    }

    public function down()
    {
        $this->reducirEnum('ventas', 'tipo_venta');
        $this->reducirEnum('cotizaciones', 'tipo_cotizacion');
    }

    private function extenderEnum(string $tabla, string $columna): void
    {
        if (!Schema::hasTable($tabla) || !Schema::hasColumn($tabla, $columna)) {
            return;
        }

        $lista = implode(',', array_map(function ($valor) {
            return "'" . str_replace("'", "''", $valor) . "'";
        }, $this->valores));

        $null = $this->permiteNulo($tabla, $columna) ? 'NULL' : 'NOT NULL';

        DB::statement("ALTER TABLE `{$tabla}` MODIFY `{$columna}` ENUM({$lista}) {$null}");
    }

    private function reducirEnum(string $tabla, string $columna): void
    {
        if (!Schema::hasTable($tabla) || !Schema::hasColumn($tabla, $columna)) {
            return;
        }

        $nuevos = array_slice($this->valores, 2);
        $enUso = DB::table($tabla)->whereIn($columna, $nuevos)->count();

        if ($enUso > 0) {
            throw new RuntimeException(
                "No se puede revertir {$tabla}.{$columna}: hay {$enUso} registros en Autos, Accesorios o Pintura."
            );
        }

        $null = $this->permiteNulo($tabla, $columna) ? 'NULL' : 'NOT NULL';
        DB::statement("ALTER TABLE `{$tabla}` MODIFY `{$columna}` ENUM('Car Wash','CDS') {$null}");
    }

    private function permiteNulo(string $tabla, string $columna): bool
    {
        $meta = DB::selectOne(
            'SELECT IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$tabla, $columna]
        );

        return $meta && $meta->IS_NULLABLE === 'YES';
    }
}
