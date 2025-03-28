<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venta_id');
            $table->date('fecha');
            $table->decimal('monto', 10, 2);
            $table->enum('metodo_pago', ['efectivo', 'tarjeta_credito', 'tarjeta_debito', 'transferencia', 'cheque', 'otro']);
            $table->string('referencia')->nullable(); // Referencia o número de comprobante
            $table->string('comprobante_imagen')->nullable(); // Ruta a imagen de comprobante si existe
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('usuario_id'); // Usuario que registra el pago
            $table->timestamps();

            // Relaciones
            $table->foreign('venta_id')->references('id')->on('ventas')->onUpdate('cascade')->onDelete('cascade');
            // No se añade restricción de clave foránea para usuario_id para evitar problemas si un usuario es eliminado
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagos');
    }
}
