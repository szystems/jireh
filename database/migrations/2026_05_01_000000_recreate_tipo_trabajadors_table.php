<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RecreateTipoTrabajadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // En lugar de intentar eliminar la tabla, vamos a trabajar con la existente
        // Si la tabla no existe, la creamos
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

            // Creamos algunos tipos de trabajador por defecto
            DB::table('tipo_trabajadors')->insert([
                ['nombre' => 'Mecánico', 'descripcion' => 'Trabaja en reparación de vehículos', 'aplica_comision' => true, 'requiere_asignacion' => true, 'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'Car Wash', 'descripcion' => 'Trabaja en limpieza de vehículos', 'aplica_comision' => true, 'requiere_asignacion' => false, 'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'Administrativo', 'descripcion' => 'Personal administrativo', 'aplica_comision' => false, 'requiere_asignacion' => false, 'estado' => 'activo', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Añadimos la columna de relación a trabajadors si no existe
        if (Schema::hasTable('trabajadors') && !Schema::hasColumn('trabajadors', 'tipo_trabajador_id')) {
            Schema::table('trabajadors', function (Blueprint $table) {
                $table->foreignId('tipo_trabajador_id')->nullable()->constrained('tipo_trabajadors')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Sólo eliminamos la relación, no la tabla completa para evitar problemas con FK
        if (Schema::hasTable('trabajadors') && Schema::hasColumn('trabajadors', 'tipo_trabajador_id')) {
            Schema::table('trabajadors', function (Blueprint $table) {
                $table->dropConstrainedForeignId('tipo_trabajador_id');
            });
        }

        // No eliminamos la tabla tipo_trabajadors para evitar problemas con otras FK
    }
}
