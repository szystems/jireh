<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoToPagosComisionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pagos_comisiones', function (Blueprint $table) {
            if (!Schema::hasColumn('pagos_comisiones', 'estado')) {
                $table->enum('estado', ['pendiente', 'completado', 'anulado'])
                      ->default('pendiente')
                      ->after('total_monto')
                      ->comment('Estado del pago de comisión');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pagos_comisiones', function (Blueprint $table) {
            if (Schema::hasColumn('pagos_comisiones', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }
}
