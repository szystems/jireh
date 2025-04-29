<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoTrabajadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_trabajadors', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->boolean('aplica_comision')->default(false);
            $table->boolean('requiere_asignacion')->default(false);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });

        // Añadir columna de relación a la tabla trabajadors si no existe
        if (Schema::hasTable('trabajadors')) {
            // Primero verificamos si ya existe la columna para evitar errores
            if (!Schema::hasColumn('trabajadors', 'tipo_trabajador_id')) {
                Schema::table('trabajadors', function (Blueprint $table) {
                    $table->foreignId('tipo_trabajador_id')->nullable()->constrained('tipo_trabajadors')->nullOnDelete();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('trabajadors') && Schema::hasColumn('trabajadors', 'tipo_trabajador_id')) {
            Schema::table('trabajadors', function (Blueprint $table) {
                $table->dropConstrainedForeignId('tipo_trabajador_id');
            });
        }

        Schema::dropIfExists('tipo_trabajadors');
    }
}
