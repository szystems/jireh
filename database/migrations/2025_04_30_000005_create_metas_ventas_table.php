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

            // Relación con usuario/vendedor
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');

            // Rango de meta
            $table->decimal('monto_minimo', 10, 2);
            $table->decimal('monto_maximo', 10, 2);
            $table->decimal('porcentaje_comision', 5, 2);

            // Periodo de aplicación
            $table->enum('periodo', ['diario', 'semanal', 'quincenal', 'mensual', 'trimestral', 'semestral', 'anual'])->default('mensual');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();

            // Estado: activo/inactivo
            $table->boolean('estado')->default(true);

            $table->timestamps();

            // Índices
            $table->index(['usuario_id', 'periodo']);
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
        Schema::dropIfExists('metas_ventas');
    }
}
