<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ObsoletoRemoveTipoFieldFromTrabajadorsV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trabajadors', function (Blueprint $table) {
            // Cambiar el tipo de dato de la columna 'tipo' de string a integer
            $table->unsignedBigInteger('tipo')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trabajadors', function (Blueprint $table) {
            // Revertir el cambio de tipo de dato de la columna 'tipo' de integer a string
            $table->string('tipo')->nullable()->change();
        });
    }
}
