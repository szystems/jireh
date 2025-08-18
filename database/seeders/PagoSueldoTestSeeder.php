<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trabajador;
use App\Models\User;
use App\Models\TipoTrabajador;
use Illuminate\Support\Facades\Hash;

class PagoSueldoTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Crear tipos de trabajador si no existen
        $tipoMecanico = TipoTrabajador::firstOrCreate([
            'nombre' => 'Mecánico',
        ], [
            'descripcion' => 'Técnico mecánico especializado',
            'estado' => 'activo',
            'aplica_comision' => true,
            'tipo_comision' => 'porcentaje_ganancia',
            'porcentaje_comision' => 5.0,
        ]);

        $tipoCarwash = TipoTrabajador::firstOrCreate([
            'nombre' => 'Car Wash',
        ], [
            'descripcion' => 'Especialista en lavado de vehículos',
            'estado' => 'activo',
            'aplica_comision' => true,
            'tipo_comision' => 'monto_fijo',
            'valor_comision' => 25.0,
        ]);

        // Crear trabajadores de prueba
        $trabajadores = [
            [
                'nombre' => 'Juan Carlos',
                'apellido' => 'Pérez González',
                'dpi' => '1234567890101',
                'telefono' => '12345678',
                'direccion' => 'Zona 1, Ciudad',
                'email' => 'juan.perez@jireh.com',
                'tipo_trabajador_id' => $tipoMecanico->id,
                'estado' => 1,
            ],
            [
                'nombre' => 'María Elena',
                'apellido' => 'García López',
                'dpi' => '1234567890102',
                'telefono' => '23456789',
                'direccion' => 'Zona 2, Ciudad',
                'email' => 'maria.garcia@jireh.com',
                'tipo_trabajador_id' => $tipoCarwash->id,
                'estado' => 1,
            ],
            [
                'nombre' => 'Carlos Eduardo',
                'apellido' => 'Hernández Ruiz',
                'dpi' => '1234567890103',
                'telefono' => '34567890',
                'direccion' => 'Zona 3, Ciudad',
                'email' => 'carlos.hernandez@jireh.com',
                'tipo_trabajador_id' => $tipoMecanico->id,
                'estado' => 1,
            ],
            [
                'nombre' => 'Ana Patricia',
                'apellido' => 'Morales Castro',
                'dpi' => '1234567890104',
                'telefono' => '45678901',
                'direccion' => 'Zona 4, Ciudad',
                'email' => 'ana.morales@jireh.com',
                'tipo_trabajador_id' => $tipoCarwash->id,
                'estado' => 1,
            ],
        ];

        foreach ($trabajadores as $trabajadorData) {
            Trabajador::firstOrCreate(
                ['dpi' => $trabajadorData['dpi']], 
                $trabajadorData
            );
        }

        // Crear usuarios/vendedores de prueba
        $usuarios = [
            [
                'name' => 'Roberto Silva',
                'email' => 'roberto.silva@jireh.com',
                'password' => Hash::make('password'),
                'role_as' => 0, // Usuario normal (vendedor)
                'estado' => 1,
                'celular' => '56789012',
                'direccion' => 'Zona 5, Ciudad',
            ],
            [
                'name' => 'Lucía Fernández',
                'email' => 'lucia.fernandez@jireh.com',
                'password' => Hash::make('password'),
                'role_as' => 0,
                'estado' => 1,
                'celular' => '67890123',
                'direccion' => 'Zona 6, Ciudad',
            ],
            [
                'name' => 'Miguel Torres',
                'email' => 'miguel.torres@jireh.com',
                'password' => Hash::make('password'),
                'role_as' => 0,
                'estado' => 1,
                'celular' => '78901234',
                'direccion' => 'Zona 7, Ciudad',
            ],
        ];

        foreach ($usuarios as $usuarioData) {
            User::firstOrCreate(
                ['email' => $usuarioData['email']], 
                $usuarioData
            );
        }

        $this->command->info('✅ Datos de prueba para Pagos de Sueldo creados exitosamente.');
        $this->command->info('📊 Trabajadores creados: ' . Trabajador::count());
        $this->command->info('👥 Usuarios/Vendedores creados: ' . User::where('role_as', 0)->where('estado', 1)->count());
    }
}
