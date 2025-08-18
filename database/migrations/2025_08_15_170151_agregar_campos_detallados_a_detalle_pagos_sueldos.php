<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AgregarCamposDetalladosADetallePagosSueldos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('detalle_pagos_sueldos', function (Blueprint $table) {
            // Agregar campos detallados para bonificaciones
            $table->decimal('horas_extra', 8, 2)->default(0)->comment('Cantidad de horas extra trabajadas');
            $table->decimal('valor_hora_extra', 8, 2)->default(0)->comment('Valor por hora extra');
            $table->decimal('comisiones', 10, 2)->default(0)->comment('Comisiones ganadas');
            
            // Agregar campo de estado individual por detalle
            $table->enum('estado', ['pendiente', 'pagado', 'cancelado'])->default('pendiente')->comment('Estado individual del pago');
            
            // Agregar campos de auditoría para el estado
            $table->timestamp('fecha_pago')->nullable()->comment('Fecha cuando se marcó como pagado');
            $table->text('observaciones_pago')->nullable()->comment('Observaciones del pago individual');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detalle_pagos_sueldos', function (Blueprint $table) {
            // Eliminar campos en orden inverso
            $table->dropColumn([
                'observaciones_pago',
                'fecha_pago',
                'estado',
                'comisiones',
                'valor_hora_extra',
                'horas_extra'
            ]);
        });
    }
}
