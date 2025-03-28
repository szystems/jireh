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
        $this->call(TipoComisionSeeder::class); // Registrar el seeder de tipo_comisions
        $this->call(ArticuloSeeder::class);
        $this->call(TrabajadorSeeder::class);
    }
}
