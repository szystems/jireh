<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('vehiculo_id')->nullable();
            $table->string('numero_factura')->nullable();
            $table->date('fecha');
            $table->enum('tipo_venta', ['Car Wash', 'CDS']);
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->boolean('estado')->nullable()->default(true);
            $table->enum('estado_pago', ['pendiente', 'pagado', 'parcial'])->default('pendiente');
            $table->timestamps();

            $table->foreign('cliente_id')->references('id')->on('clientes')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('vehiculo_id')->references('id')->on('vehiculos')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('usuario_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ventas');
    }
}
