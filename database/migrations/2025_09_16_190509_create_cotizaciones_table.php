<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCotizacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cliente_id')->nullable();
            $table->unsignedBigInteger('vehiculo_id')->nullable();
            $table->string('numero_cotizacion')->unique();
            $table->date('fecha_cotizacion');
            $table->date('fecha_vencimiento');
            $table->enum('tipo_cotizacion', ['Car Wash', 'CDS']);
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->enum('estado', ['vigente', 'vencida', 'aprobada', 'rechazada', 'convertida', 'Generado', 'Aprobado'])->default('Generado');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('cliente_id')->references('id')->on('clientes')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('vehiculo_id')->references('id')->on('vehiculos')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('usuario_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            
            // Índices para optimizar consultas
            $table->index('fecha_cotizacion');
            $table->index('fecha_vencimiento');
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
        Schema::dropIfExists('cotizaciones');
    }
}
