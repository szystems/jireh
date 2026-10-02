<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateDepartamentosAndRelaxTipoVenta extends Migration
{
    /**
     * Los nombres coinciden con lo ya guardado en facturas.
     * No se actualiza ninguna venta, cotización ni comisión.
     */
    private $iniciales = [
        ['nombre' => 'Car Wash', 'etiqueta' => 'Car Wash', 'orden' => 1],
        ['nombre' => 'CDS', 'etiqueta' => 'Centro de Servicios (CDS)', 'orden' => 2],
        ['nombre' => 'Autos', 'etiqueta' => 'Autos', 'orden' => 3],
        ['nombre' => 'Accesorios', 'etiqueta' => 'Accesorios', 'orden' => 4],
        ['nombre' => 'Pintura Automotriz', 'etiqueta' => 'Pintura automotriz', 'orden' => 5],
    ];

    public function up()
    {
        if (!Schema::hasTable('departamentos')) {
            Schema::create('departamentos', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 100)->unique();
                $table->string('etiqueta', 100);
                $table->unsignedInteger('orden')->default(0);
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        $ahora = now();
        foreach ($this->iniciales as $item) {
            $existe = DB::table('departamentos')->where('nombre', $item['nombre'])->exists();
            if ($existe) {
                continue;
            }
            DB::table('departamentos')->insert([
                'nombre' => $item['nombre'],
                'etiqueta' => $item['etiqueta'],
                'orden' => $item['orden'],
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);
        }

        $this->aTexto('ventas', 'tipo_venta');
        $this->aTexto('cotizaciones', 'tipo_cotizacion');
    }

    public function down()
    {
        Schema::dropIfExists('departamentos');
    }

    private function aTexto(string $tabla, string $columna): void
    {
        if (!Schema::hasTable($tabla) || !Schema::hasColumn($tabla, $columna)) {
            return;
        }

        $meta = DB::selectOne(
            'SELECT DATA_TYPE, IS_NULLABLE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$tabla, $columna]
        );

        if (!$meta || strtolower($meta->DATA_TYPE) === 'varchar') {
            return;
        }

        $null = $meta->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL';
        DB::statement("ALTER TABLE `{$tabla}` MODIFY `{$columna}` VARCHAR(100) {$null}");
    }
}
