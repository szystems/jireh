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
        // SEEDERS DE DESARROLLO - Datos de prueba para testing
        // ============================================================================
        $this->call(ClientesTableSeeder::class);     // Clientes de prueba
        $this->call(VehiculosTableSeeder::class);    // Vehículos de prueba
        $this->call(CategoriasTableSeeder::class);   // Categorías de prueba
        $this->call(ProveedoresTableSeeder::class);  // Proveedores de prueba
        $this->call(ArticuloSeeder::class);          // Artículos de prueba
        $this->call(TrabajadorSeeder::class);        // Trabajadores de prueba
        $this->call(MetaVentaSeeder::class);         // Metas de prueba
        
        echo "\n✅ Base de datos inicializada para DESARROLLO";
        echo "\n📋 Datos creados:";
        echo "\n   - Configuración básica del sistema";
        echo "\n   - Usuario administrador: Emilio Rodriguez";
        echo "\n   - Usuario desarrollador: Otto Szarata";
        echo "\n   - 6 unidades de medida básicas";
        echo "\n   - 2 tipos de trabajador esenciales (Mecánico, Car Wash)";
        echo "\n   - Clientes de prueba";
        echo "\n   - Vehículos de prueba";
        echo "\n   - Categorías y artículos de prueba";
        echo "\n   - Proveedores de prueba";
        echo "\n   - Trabajadores de prueba";
        echo "\n   - Metas de venta de prueba";
        echo "\n";
        echo "\n🚀 El sistema está listo para desarrollo y pruebas.\n";
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
