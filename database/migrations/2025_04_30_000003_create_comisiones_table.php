<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComisionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comisiones', function (Blueprint $table) {
            $table->id();
            // Relación polimórfica para trabajador o usuario
            $table->unsignedBigInteger('commissionable_id');
            $table->string('commissionable_type');
            
            // Tipo de comisión (meta_venta, carwash, mecanico)
            $table->string('tipo_comision', 50);
            
            // Valores monetarios
            $table->decimal('monto', 10, 2);
            $table->decimal('porcentaje', 5, 2)->nullable(); // Si aplica (para comisiones por meta)
            
            // Relaciones opcionales
            $table->foreignId('detalle_venta_id')->nullable()->constrained('detalle_ventas')->onDelete('cascade');
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->onDelete('cascade');
            $table->foreignId('articulo_id')->nullable()->constrained('articulos')->onDelete('set null');
            
            // Estado: pendiente, pagado, cancelado
            $table->enum('estado', ['pendiente', 'pagado', 'cancelado'])->default('pendiente');
            
            // Fechas
            $table->date('fecha_calculo');
            $table->timestamps();
            
            // Índices para mejorar el rendimiento
            $table->index(['commissionable_id', 'commissionable_type']);
            $table->index('estado');
            $table->index('fecha_calculo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comisiones');
    }
}
