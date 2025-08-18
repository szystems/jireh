<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosSueldosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos_sueldos', function (Blueprint $table) {
            $table->id();
            
            // Información del lote
            $table->string('numero_lote', 50)->unique()->comment('Formato: PS-YYYYMM-XXX');
            $table->integer('periodo_mes')->comment('1-12 (Enero a Diciembre)');
            $table->integer('periodo_anio')->comment('Año del período de pago');
            $table->date('fecha_pago')->comment('Fecha en que se realiza el pago');
            
            // Método y estado del pago
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'cheque'])
                  ->default('transferencia')
                  ->comment('Método utilizado para el pago');
            $table->enum('estado', ['pendiente', 'pagado', 'cancelado'])
                  ->default('pendiente')
                  ->comment('Estado del lote de pago');
            
            // Montos y comprobantes
            $table->decimal('total_monto', 10, 2)->default(0)->comment('Suma total de todos los sueldos del lote');
            $table->text('observaciones')->nullable()->comment('Notas adicionales del lote');
            $table->string('comprobante_pago', 255)->nullable()->comment('Archivo de imagen del comprobante');
            
            // Auditoría
            $table->unsignedBigInteger('usuario_creo_id')->comment('Usuario que creó el lote');
            $table->foreign('usuario_creo_id')->references('id')->on('users')->onDelete('cascade');
            
            // Índices para optimización
            $table->index(['periodo_anio', 'periodo_mes'], 'idx_periodo');
            $table->index('estado', 'idx_estado');
            $table->index('fecha_pago', 'idx_fecha_pago');
            
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
        Schema::dropIfExists('pagos_sueldos');
    }
}
