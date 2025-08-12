<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLotesPagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lotes_pago', function (Blueprint $table) {
            $table->id();
            
            // Información del lote
            $table->string('numero_lote')->unique(); // LOTE-2025-001
            $table->date('fecha_pago');
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'cheque', 'otro'])->default('efectivo');
            $table->string('referencia')->nullable();
            $table->string('comprobante_imagen')->nullable();
            $table->text('observaciones')->nullable();
            
            // Totales calculados
            $table->decimal('monto_total', 10, 2);
            $table->integer('cantidad_comisiones');
            
            // Control
            $table->enum('estado', ['procesando', 'completado', 'anulado'])->default('procesando');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            
            $table->timestamps();
            
            // Índices
            $table->index('fecha_pago');
            $table->index('estado');
            $table->index('numero_lote');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lotes_pago');
    }
}
