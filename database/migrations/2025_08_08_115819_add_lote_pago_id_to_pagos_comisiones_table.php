<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLotePagoIdToPagosComisionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pagos_comisiones', function (Blueprint $table) {
            $table->foreignId('lote_pago_id')->nullable()->after('id')->constrained('lotes_pago')->onDelete('set null');
            $table->index('lote_pago_id');
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
            $table->dropForeign(['lote_pago_id']);
            $table->dropIndex(['lote_pago_id']);
            $table->dropColumn('lote_pago_id');
        });
    }
}
