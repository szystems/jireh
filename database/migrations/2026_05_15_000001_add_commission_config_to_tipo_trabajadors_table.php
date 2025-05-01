<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommissionConfigToTipoTrabajadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipo_trabajadors', function (Blueprint $table) {
            // Añadir campos para configuración de comisiones
            $table->string('tipo_comision')->nullable()->after('requiere_asignacion');
            $table->decimal('valor_comision', 10, 2)->nullable()->after('tipo_comision');
            $table->decimal('porcentaje_comision', 5, 2)->nullable()->after('valor_comision');
            $table->boolean('permite_multiples_trabajadores')->default(false)->after('porcentaje_comision');
            $table->json('configuracion_adicional')->nullable()->after('permite_multiples_trabajadores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tipo_trabajadors', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_comision',
                'valor_comision',
                'porcentaje_comision',
                'permite_multiples_trabajadores',
                'configuracion_adicional'
            ]);
        });
    }
}
