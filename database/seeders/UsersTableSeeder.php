<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ============================================================================
        // USUARIO ADMINISTRADOR PRINCIPAL - Emilio Rodriguez (Cliente)
        // ============================================================================
        User::create([
            'name' => 'Emilio Rodriguez',
            'email' => 'jirehautomotrizventas@gmail.com',
            'password' => Hash::make('jireh2025'),  // Contraseña temporal, se debe cambiar
            'role_as' => '0',  // Administrador
            'principal' => '1',  // Usuario principal
        ]);

        // ============================================================================
        // USUARIO DESARROLLADOR - Otto Szarata (Soporte técnico)
        // ============================================================================
        User::create([
            'name' => 'Otto Szarata',
            'email' => 'szystems@hotmail.com',
            'password' => Hash::make('SPP7007aaa@@@'),
            'role_as' => '0',  // Administrador
            'principal' => '0',  // Usuario de soporte
        ]);

        // ============================================================================
        // DATOS DE PRUEBA DESHABILITADOS PARA PRODUCCIÓN
        // ============================================================================
        // User::factory()->count(20)->create();  // NO crear usuarios de prueba
        
        echo "\n✅ Usuarios creados para producción:";
        echo "\n   - Emilio Rodriguez (jirehautomotrizventas@gmail.com) - ADMIN PRINCIPAL";
        echo "\n   - Otto Szarata (szystems@hotmail.com) - SOPORTE TÉCNICO";
        echo "\n   📝 IMPORTANTE: Cambiar contraseña de Emilio tras primer login\n";
    }
}
