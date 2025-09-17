<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleCotizacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cotizacion_id');
            $table->unsignedBigInteger('articulo_id')->nullable();
            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio_costo', 10, 2)->default(0.00);
            $table->decimal('precio_venta', 10, 2)->default(0.00);
            $table->unsignedBigInteger('descuento_id')->nullable();
            $table->unsignedBigInteger('usuario_id'); // Usuario que agrega el item
            $table->decimal('sub_total', 10, 2);
            $table->decimal('porcentaje_impuestos', 10, 2)->default(0);
            $table->timestamps();

            // Foreign keys
            $table->foreign('cotizacion_id')->references('id')->on('cotizaciones')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('articulo_id')->references('id')->on('articulos')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('descuento_id')->references('id')->on('descuentos')->onUpdate('cascade')->onDelete('set null');
            
            // Índices para optimizar consultas
            $table->index('cotizacion_id');
            $table->index('articulo_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_cotizaciones');
    }
}
