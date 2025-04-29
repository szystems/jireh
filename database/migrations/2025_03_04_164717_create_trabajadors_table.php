<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrabajadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Primero creamos la tabla tipo_trabajadors si no existe
        if (!Schema::hasTable('tipo_trabajadors')) {
            Schema::create('tipo_trabajadors', function (Blueprint $table) {
                $table->id();
                $table->string('nombre')->unique();
                $table->text('descripcion')->nullable();
                $table->boolean('aplica_comision')->default(false);
                $table->boolean('requiere_asignacion')->default(false);
                $table->enum('estado', ['activo', 'inactivo'])->default('activo');
                $table->timestamps();
            });
        }

        // Luego creamos la tabla trabajadors con la relación ya incluida
        Schema::create('trabajadors', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('telefono', 20);
            $table->string('direccion');
            $table->string('email')->nullable();
            $table->string('nit')->nullable();
            $table->string('dpi')->nullable();
            $table->string('tipo')->nullable(); // Campo tipo como string para compatibilidad
            $table->foreignId('tipo_trabajador_id')->nullable()->constrained('tipo_trabajadors')->nullOnDelete();
            $table->boolean('estado')->default(1);
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
        Schema::dropIfExists('trabajadors');
        Schema::dropIfExists('tipo_trabajadors');
    }
}
