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
        $this->call(UsersTableSeeder::class);
        $this->call(ConfigsTableSeeder::class);
        $this->call(ClientesTableSeeder::class);
        $this->call(VehiculosTableSeeder::class);
        $this->call(CategoriasTableSeeder::class);
        $this->call(ProveedoresTableSeeder::class);
        $this->call(UnidadsTableSeeder::class);
        $this->call(ArticuloSeeder::class);
        $this->call(TrabajadorSeeder::class);
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
