<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;

class ConfigsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // ============================================================================
        // CONFIGURACIÓN BÁSICA DEL SISTEMA - DATOS ESENCIALES PARA FUNCIONAR
        // ============================================================================
        Config::create([
            'email' => 'info@jirehautomotriz.com',
            'time_zone' => 'America/Guatemala',
            'currency' => 'GTQ Q',
            'currency_simbol' => 'Q',
            'currency_iso' => 'GTQ',
            'descuento_maximo' => 0.00,  // El cliente podrá configurar esto
            'impuesto' => 0.00,          // El cliente podrá configurar esto
        ]);
        
        echo "\n✅ Configuración básica del sistema creada";
        echo "\n   - Email: info@jirehautomotriz.com";
        echo "\n   - Zona horaria: America/Guatemala";
        echo "\n   - Moneda: Quetzal (GTQ Q)\n";
    }
}
