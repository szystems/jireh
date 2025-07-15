<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCommissionAndWorkflowFieldsToTipoTrabajadors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipo_trabajadors', function (Blueprint $table) {
            // Agregar campos relacionados con comisiones si no existen
            if (!Schema::hasColumn('tipo_trabajadors', 'tipo_comision')) {
                $table->string('tipo_comision')->nullable()->after('requiere_asignacion');
            }
            
            if (!Schema::hasColumn('tipo_trabajadors', 'valor_comision')) {
                $table->decimal('valor_comision', 10, 2)->nullable()->after('tipo_comision');
            }
            
            if (!Schema::hasColumn('tipo_trabajadors', 'porcentaje_comision')) {
                $table->decimal('porcentaje_comision', 5, 2)->nullable()->after('valor_comision');
            }
            
            if (!Schema::hasColumn('tipo_trabajadors', 'permite_multiples_trabajadores')) {
                $table->boolean('permite_multiples_trabajadores')->default(false)->after('porcentaje_comision');
            }
            
            if (!Schema::hasColumn('tipo_trabajadors', 'configuracion_adicional')) {
                $table->json('configuracion_adicional')->nullable()->after('permite_multiples_trabajadores');
            }
        });
        
        // Actualizar los tipos de trabajador existentes con valores para los nuevos campos
        DB::table('tipo_trabajadors')
            ->where('nombre', 'Mecánico')
            ->update([
                'tipo_comision' => 'fijo',
                'valor_comision' => 50.00,
                'permite_multiples_trabajadores' => false
            ]);
            
        DB::table('tipo_trabajadors')
            ->where('nombre', 'Car Wash')
            ->update([
                'tipo_comision' => 'por_servicio',
                'valor_comision' => 25.00,
                'permite_multiples_trabajadores' => true
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tipo_trabajadors', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_comision',
                'valor_comision',
                'porcentaje_comision',
                'permite_multiples_trabajadores',
                'configuracion_adicional'
            ]);
        });
    }
}
