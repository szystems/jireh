<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMecanicoFieldsToArticulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articulos', function (Blueprint $table) {
            // Relación con trabajador (mecánico)
            $table->foreignId('mecanico_id')->nullable()->constrained('trabajadors')->nullOnDelete();

            // Costo del mecánico para este servicio
            $table->decimal('costo_mecanico', 10, 2)->nullable()->default(0);

            // Comisión fija para carwash (por trabajador)
            $table->decimal('comision_carwash', 10, 2)->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropForeign(['mecanico_id']);
            $table->dropColumn(['mecanico_id', 'costo_mecanico', 'comision_carwash']);
        });
    }
}
