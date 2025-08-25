<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // ============================================================================
        // SEEDERS ESENCIALES PARA PRODUCCIÓN - Solo datos necesarios para funcionar
        // ============================================================================
        
        $this->call(ConfigsTableSeeder::class);    // Configuración básica del sistema
        $this->call(UsersTableSeeder::class);      // Usuario administrador principal
        $this->call(UnidadsTableSeeder::class);    // Unidades de medida básicas
        $this->call(TipoTrabajadorSeeder::class);  // Tipos de trabajador esenciales
        
        // ============================================================================
        // SEEDERS DESHABILITADOS - Base de datos limpia para producción
        // ============================================================================
        // $this->call(ClientesTableSeeder::class);     // Clientes de prueba - NO NECESARIO
        // $this->call(VehiculosTableSeeder::class);    // Vehículos de prueba - NO NECESARIO
        // $this->call(CategoriasTableSeeder::class);   // Categorías de prueba - NO NECESARIO
        // $this->call(ProveedoresTableSeeder::class);  // Proveedores de prueba - NO NECESARIO
        // $this->call(ArticuloSeeder::class);          // Artículos de prueba - NO NECESARIO
        // $this->call(TrabajadorSeeder::class);        // Trabajadores de prueba - NO NECESARIO
        // $this->call(MetaVentaSeeder::class);         // Metas de prueba - NO NECESARIO
        
        echo "\n✅ Base de datos inicializada para PRODUCCIÓN";
        echo "\n📋 Datos creados:";
        echo "\n   - Configuración básica del sistema";
        echo "\n   - Usuario administrador: Emilio Rodriguez";
        echo "\n   - Usuario desarrollador: Otto Szarata";
        echo "\n   - 6 unidades de medida básicas";
        echo "\n   - 2 tipos de trabajador esenciales (Mecánico, Car Wash)";
        echo "\n";
        echo "\n🚀 El sistema está listo para que Jireh Automotriz ingrese sus datos reales.\n";
    }

    /**
     * Este método puede ser llamado manualmente para sincronizar trabajadores existentes
     * con sus respectivos tipos de trabajador.
     */
    public function sincronizarTiposTrabajador()
    {
        $this->call(SincronizarTipoTrabajadorSeeder::class);
    }
}
