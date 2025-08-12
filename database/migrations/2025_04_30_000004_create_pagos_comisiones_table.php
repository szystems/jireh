<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosComisionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos_comisiones', function (Blueprint $table) {
            $table->id();

            // Relación con la comisión
            $table->foreignId('comision_id')->constrained('comisiones')->onDelete('cascade');

            // Información del pago
            $table->decimal('monto', 10, 2);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'cheque', 'otro'])->default('efectivo');

            // Usuario que registra
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');

            // Detalles del pago
            $table->date('fecha_pago');
            $table->string('referencia')->nullable();
            $table->string('comprobante_imagen')->nullable();
            $table->text('observaciones')->nullable();
            
            // Estado del pago: pendiente, completado, anulado
            $table->enum('estado', ['pendiente', 'completado', 'anulado'])->default('pendiente');

            $table->timestamps();

            // Índices
            $table->index('fecha_pago');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagos_comisiones');
    }
}
