<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('venta_id');
            $table->unsignedBigInteger('articulo_id')->nullable();
            $table->decimal('cantidad', 10, 2); // Cambiado de integer a decimal para permitir valores decimales
            $table->decimal('precio_costo', 10, 2)->default(0.00);
            $table->decimal('precio_venta', 10, 2)->default(0.00);
            $table->unsignedBigInteger('descuento_id')->nullable();
            $table->unsignedBigInteger('usuario_id'); // Sin restricción de clave foránea
            $table->decimal('sub_total', 10, 2);
            $table->decimal('porcentaje_impuestos', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('venta_id')->references('id')->on('ventas')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('articulo_id')->references('id')->on('articulos')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('descuento_id')->references('id')->on('descuentos')->onUpdate('cascade')->onDelete('set null');
            // Removemos ambas claves foráneas problemáticas (trabajador_id y usuario_id)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_ventas');
    }
}
