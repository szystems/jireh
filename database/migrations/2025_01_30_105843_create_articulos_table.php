<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->nullable();
            $table->string('nombre');
            $table->string('imagen')->nullable();
            $table->text('descripcion')->nullable();
            $table->decimal('precio_compra', 10, 2);
            $table->decimal('precio_venta', 10, 2);
            $table->decimal('stock', 10, 2);
            $table->decimal('stock_minimo', 10, 2);
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->foreignId('unidad_id')->constrained('unidads');
            $table->enum('tipo', ['articulo', 'servicio']);
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('servicio_articulo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servicio_id')->constrained('articulos');
            $table->foreignId('articulo_id')->constrained('articulos');
            $table->decimal('cantidad', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servicio_articulo');
        Schema::dropIfExists('articulos');
    }
}
