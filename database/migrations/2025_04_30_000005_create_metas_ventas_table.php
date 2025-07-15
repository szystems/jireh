<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetasVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metas_ventas', function (Blueprint $table) {
            $table->id();

            // Información de la meta
            $table->string('nombre'); // Ej: "Meta Bronce", "Meta Plata", etc.
            $table->text('descripcion')->nullable();

            // Rango de meta (sin relación a usuario específico)
            $table->decimal('monto_minimo', 10, 2);
            $table->decimal('monto_maximo', 10, 2)->nullable(); // NULL significa "sin límite superior"
            $table->decimal('porcentaje_comision', 5, 2);

            // Periodo de aplicación
            $table->enum('periodo', ['mensual', 'trimestral', 'semestral', 'anual'])->default('mensual');

            // Estado: activo/inactivo
            $table->boolean('estado')->default(true);

            $table->timestamps();

            // Índices
            $table->index(['periodo', 'estado']);
            $table->index('monto_minimo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('metas_ventas');
    }
}
