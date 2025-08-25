<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
                $table->string('tipo_comision')->nullable();
                $table->decimal('valor_comision', 10, 2)->nullable();
                $table->decimal('porcentaje_comision', 5, 2)->nullable();
                $table->boolean('permite_multiples_trabajadores')->default(false);
                $table->json('configuracion_adicional')->nullable();
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
            $table->string('direccion')->nullable(); // Cambiado a nullable
            $table->string('email')->nullable();
            $table->string('nit')->nullable();
            $table->string('dpi')->nullable();
            $table->unsignedBigInteger('tipo')->nullable(); // Campo tipo como integer para sincronizarse con tipo_trabajador_id
            $table->foreignId('tipo_trabajador_id')->nullable()->constrained('tipo_trabajadors')->nullOnDelete();
            $table->boolean('estado')->default(1);
            $table->timestamps();
        });

        // Insertar tipos de trabajador por defecto
        DB::table('tipo_trabajadors')->insert([
            [
                'nombre' => 'Mecánico',
                'descripcion' => 'Trabaja en reparación de vehículos',
                'aplica_comision' => true,
                'requiere_asignacion' => true,
                'tipo_comision' => 'fijo',
                'valor_comision' => 50.00,
                'porcentaje_comision' => null,
                'permite_multiples_trabajadores' => false,
                'configuracion_adicional' => null,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Car Wash',
                'descripcion' => 'Trabaja en limpieza de vehículos',
                'aplica_comision' => true,
                'requiere_asignacion' => false,
                'tipo_comision' => 'variable',
                'valor_comision' => null,
                'porcentaje_comision' => null,
                'permite_multiples_trabajadores' => true,
                'configuracion_adicional' => null,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        // Agregar foreign key para mecanico_id en articulos después de crear trabajadors
        Schema::table('articulos', function (Blueprint $table) {
            $table->foreign('mecanico_id')->references('id')->on('trabajadors')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar foreign key antes de eliminar tablas
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropForeign(['mecanico_id']);
        });
        
        Schema::dropIfExists('trabajadors');
        Schema::dropIfExists('tipo_trabajadors');
    }
}
