<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetallePagosSueldosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_pagos_sueldos', function (Blueprint $table) {
            $table->id();
            
            // Relación con el lote de pago principal
            $table->unsignedBigInteger('pago_sueldo_id')->comment('ID del lote de pago al que pertenece');
            $table->foreign('pago_sueldo_id')->references('id')->on('pagos_sueldos')->onDelete('cascade');
            
            // Empleado (puede ser trabajador o usuario/vendedor)
            $table->unsignedBigInteger('trabajador_id')->nullable()->comment('ID del trabajador si aplica');
            $table->unsignedBigInteger('usuario_id')->nullable()->comment('ID del usuario/vendedor si aplica');
            $table->enum('tipo_empleado', ['trabajador', 'vendedor'])
                  ->comment('Tipo de empleado: trabajador o vendedor');
            
            // Foreign keys
            $table->foreign('trabajador_id')->references('id')->on('trabajadors')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade');
            
            // Cálculos del sueldo
            $table->decimal('sueldo_base', 10, 2)->comment('Sueldo base del empleado');
            $table->decimal('bonificaciones', 10, 2)->default(0)->comment('Bonos, horas extra, comisiones adicionales');
            $table->decimal('deducciones', 10, 2)->default(0)->comment('Descuentos, préstamos, deducciones legales');
            $table->decimal('total_pagar', 10, 2)->comment('Monto final a pagar (base + bonos - deducciones)');
            
            // Información adicional
            $table->text('observaciones')->nullable()->comment('Notas específicas para este empleado');
            
            // Nota: Validación de que debe tener trabajador_id O usuario_id (no ambos) se maneja en el modelo
            
            // Índices para optimización
            $table->index('pago_sueldo_id', 'idx_lote_pago');
            $table->index(['tipo_empleado', 'trabajador_id'], 'idx_empleado_trabajador');
            $table->index(['tipo_empleado', 'usuario_id'], 'idx_empleado_usuario');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_pagos_sueldos');
    }
}
