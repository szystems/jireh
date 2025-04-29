<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrabajadorDetalleVentaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trabajador_detalle_venta', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('trabajador_id')->constrained('trabajadors')->onDelete('cascade');
            $table->foreignId('detalle_venta_id')->constrained('detalle_ventas')->onDelete('cascade');

            // Para el caso de CarWash, pueden participar varios trabajadores
            $table->decimal('monto_comision', 10, 2)->default(0);

            $table->timestamps();

            // Índices y clave única
            $table->index(['trabajador_id', 'detalle_venta_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trabajador_detalle_venta');
    }
}
