<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientosStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientos_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('articulo_id')->constrained('articulos')->onDelete('cascade');
            $table->string('tipo', 50); // VENTA, INGRESO, AJUSTE_MANUAL, CORRECCION_AUTOMATICA
            $table->decimal('stock_anterior', 10, 2);
            $table->decimal('stock_nuevo', 10, 2);
            $table->decimal('cantidad', 10, 2);
            $table->string('referencia_tipo', 50)->nullable(); // VENTA, INGRESO, etc.
            $table->unsignedBigInteger('referencia_id')->nullable(); // ID de la venta, ingreso, etc.
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('observaciones')->nullable();
            $table->json('metadata')->nullable(); // Para datos adicionales
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['articulo_id', 'created_at']);
            $table->index(['tipo', 'created_at']);
            $table->index(['referencia_tipo', 'referencia_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movimientos_stock');
    }
}
