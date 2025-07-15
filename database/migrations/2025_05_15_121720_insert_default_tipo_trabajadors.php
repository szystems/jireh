<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class InsertDefaultTipoTrabajadors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insertar tipos de trabajador por defecto
        DB::table('tipo_trabajadors')->insert([
            [
                'nombre' => 'Mecánico',
                'descripcion' => 'Trabaja en reparación de vehículos',
                'aplica_comision' => true,
                'requiere_asignacion' => true,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Car Wash',
                'descripcion' => 'Trabaja en limpieza de vehículos',
                'aplica_comision' => true,
                'requiere_asignacion' => false,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Administrativo',
                'descripcion' => 'Personal administrativo',
                'aplica_comision' => false,
                'requiere_asignacion' => false,
                'estado' => 'activo',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar los tipos de trabajador predeterminados
        DB::table('tipo_trabajadors')->whereIn('nombre', ['Mecánico', 'Car Wash', 'Administrativo'])->delete();
    }
}
