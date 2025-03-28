<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyEstadoPagoEnumInVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modificar el ENUM para incluir 'parcial'
        DB::statement("ALTER TABLE ventas MODIFY COLUMN estado_pago ENUM('pendiente', 'pagado', 'parcial') NOT NULL DEFAULT 'pendiente'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revertir al ENUM original
        DB::statement("ALTER TABLE ventas MODIFY COLUMN estado_pago ENUM('pendiente', 'pagado') NOT NULL DEFAULT 'pendiente'");
    }
}
