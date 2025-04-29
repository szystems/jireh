<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UnificarRelacionesTipoTrabajador extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Asegurar que la tabla trabajadors tenga la columna tipo
        if (!Schema::hasColumn('trabajadors', 'tipo')) {
            Schema::table('trabajadors', function (Blueprint $table) {
                $table->string('tipo')->nullable()->after('estado')
                    ->comment('Tipo de trabajador: carwash, mecanico, etc.');
            });
            // Establecer valor predeterminado para registros existentes
            DB::table('trabajadors')->update(['tipo' => 'general']);
        }

        // Verificar si ya existe la relación
        if (Schema::hasTable('trabajadors') && !Schema::hasColumn('trabajadors', 'tipo_trabajador_id')) {
            // Añadir la columna de relación con tipo_trabajadors
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
        if (Schema::hasTable('trabajadors')) {
            if (Schema::hasColumn('trabajadors', 'tipo_trabajador_id')) {
                Schema::table('trabajadors', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('tipo_trabajador_id');
                });
            }

            if (Schema::hasColumn('trabajadors', 'tipo')) {
                Schema::table('trabajadors', function (Blueprint $table) {
                    $table->dropColumn('tipo');
                });
            }
        }
    }
}
