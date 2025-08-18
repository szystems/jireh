<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PagoSueldo;
use App\Models\DetallePagoSueldo;
use App\Models\Trabajador;
use App\Models\User;
use Carbon\Carbon;

class LotesPagoSueldoTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener algunos trabajadores y usuarios
        $trabajadores = Trabajador::where('estado', 1)->take(3)->get();
        $usuarios = User::where('estado', 1)->where('role_as', 0)->take(2)->get();

        if ($trabajadores->count() === 0 || $usuarios->count() === 0) {
            $this->command->error('❌ No hay suficientes trabajadores o usuarios para crear lotes de prueba.');
            return;
        }

        // Lote 1: Julio 2025 - PAGADO
        $lote1 = PagoSueldo::create([
            'numero_lote' => 'PS-202507-001',
            'periodo_mes' => 7,
            'periodo_anio' => 2025,
            'fecha_pago' => '2025-07-31',
            'metodo_pago' => 'transferencia',
            'estado' => 'pagado',
            'total_monto' => 0, // Se calculará automáticamente
            'observaciones' => 'Pago de sueldos correspondiente al mes de julio 2025',
            'usuario_creo_id' => 1, // Asumiendo que el admin tiene ID 1
        ]);

        $totalLote1 = 0;

        // Agregar trabajadores al lote 1
        foreach ($trabajadores as $index => $trabajador) {
            $sueldoBase = 2500 + ($index * 300); // Diferentes sueldos
            $bonificaciones = 200 + ($index * 50);
            $deducciones = 100;
            $totalPagar = $sueldoBase + $bonificaciones - $deducciones;
            $totalLote1 += $totalPagar;

            DetallePagoSueldo::create([
                'pago_sueldo_id' => $lote1->id,
                'trabajador_id' => $trabajador->id,
                'usuario_id' => null,
                'tipo_empleado' => 'trabajador',
                'sueldo_base' => $sueldoBase,
                'bonificaciones' => $bonificaciones,
                'deducciones' => $deducciones,
                'total_pagar' => $totalPagar,
                'observaciones' => "Pago completo julio - {$trabajador->nombre}",
            ]);
        }

        // Agregar usuarios al lote 1
        foreach ($usuarios as $index => $usuario) {
            $sueldoBase = 3000 + ($index * 200);
            $comisiones = 300 + ($index * 100);
            $deducciones = 150;
            $totalPagar = $sueldoBase + $comisiones - $deducciones;
            $totalLote1 += $totalPagar;

            DetallePagoSueldo::create([
                'pago_sueldo_id' => $lote1->id,
                'trabajador_id' => null,
                'usuario_id' => $usuario->id,
                'tipo_empleado' => 'vendedor',
                'sueldo_base' => $sueldoBase,
                'bonificaciones' => $comisiones,
                'deducciones' => $deducciones,
                'total_pagar' => $totalPagar,
                'observaciones' => "Incluye comisiones julio - {$usuario->name}",
            ]);
        }

        $lote1->update(['total_monto' => $totalLote1]);

        // Lote 2: Agosto 2025 - PENDIENTE
        $lote2 = PagoSueldo::create([
            'numero_lote' => 'PS-202508-001',
            'periodo_mes' => 8,
            'periodo_anio' => 2025,
            'fecha_pago' => '2025-08-31',
            'metodo_pago' => 'transferencia',
            'estado' => 'pendiente',
            'total_monto' => 0,
            'observaciones' => 'Pago de sueldos agosto 2025 - En proceso',
            'usuario_creo_id' => 1,
        ]);

        $totalLote2 = 0;

        // Solo algunos empleados en el lote 2 (parcial)
        $trabajadoresParcial = $trabajadores->take(2);
        $usuariosParcial = $usuarios->take(1);

        foreach ($trabajadoresParcial as $index => $trabajador) {
            $sueldoBase = 2600 + ($index * 300);
            $horasExtra = 150; // Horas extra este mes
            $deducciones = 80;
            $totalPagar = $sueldoBase + $horasExtra - $deducciones;
            $totalLote2 += $totalPagar;

            DetallePagoSueldo::create([
                'pago_sueldo_id' => $lote2->id,
                'trabajador_id' => $trabajador->id,
                'usuario_id' => null,
                'tipo_empleado' => 'trabajador',
                'sueldo_base' => $sueldoBase,
                'bonificaciones' => $horasExtra,
                'deducciones' => $deducciones,
                'total_pagar' => $totalPagar,
                'observaciones' => "Incluye 12 horas extra - {$trabajador->nombre}",
            ]);
        }

        foreach ($usuariosParcial as $usuario) {
            $sueldoBase = 3200;
            $comisiones = 450;
            $bono = 200;
            $deducciones = 100;
            $totalBonificaciones = $comisiones + $bono;
            $totalPagar = $sueldoBase + $totalBonificaciones - $deducciones;
            $totalLote2 += $totalPagar;

            DetallePagoSueldo::create([
                'pago_sueldo_id' => $lote2->id,
                'trabajador_id' => null,
                'usuario_id' => $usuario->id,
                'tipo_empleado' => 'vendedor',
                'sueldo_base' => $sueldoBase,
                'bonificaciones' => $totalBonificaciones,
                'deducciones' => $deducciones,
                'total_pagar' => $totalPagar,
                'observaciones' => "Comisiones Q{$comisiones} + Bono Q{$bono} - {$usuario->name}",
            ]);
        }

        $lote2->update(['total_monto' => $totalLote2]);

        // Lote 3: Agosto 2025 (segundo lote) - PENDIENTE
        $lote3 = PagoSueldo::create([
            'numero_lote' => 'PS-202508-002',
            'periodo_mes' => 8,
            'periodo_anio' => 2025,
            'fecha_pago' => '2025-08-15',
            'metodo_pago' => 'efectivo',
            'estado' => 'pendiente',
            'total_monto' => 0,
            'observaciones' => 'Adelanto quincenal - Empleados específicos',
            'usuario_creo_id' => 1,
        ]);

        $totalLote3 = 0;

        // Solo un trabajador con adelanto
        if ($trabajadores->count() > 2) {
            $trabajadorAdelanto = $trabajadores->skip(2)->first();
            $adelanto = 1200;
            $totalLote3 += $adelanto;

            DetallePagoSueldo::create([
                'pago_sueldo_id' => $lote3->id,
                'trabajador_id' => $trabajadorAdelanto->id,
                'usuario_id' => null,
                'tipo_empleado' => 'trabajador',
                'sueldo_base' => $adelanto,
                'bonificaciones' => 0,
                'deducciones' => 0,
                'total_pagar' => $adelanto,
                'observaciones' => "Adelanto quincenal solicitado - {$trabajadorAdelanto->nombre}",
            ]);
        }

        $lote3->update(['total_monto' => $totalLote3]);

        $this->command->info('✅ Lotes de pago de prueba creados exitosamente:');
        $this->command->info("📄 {$lote1->numero_lote} - {$lote1->estado} - Q" . number_format($lote1->total_monto, 2));
        $this->command->info("📄 {$lote2->numero_lote} - {$lote2->estado} - Q" . number_format($lote2->total_monto, 2));
        $this->command->info("📄 {$lote3->numero_lote} - {$lote3->estado} - Q" . number_format($lote3->total_monto, 2));
        $this->command->info('🎯 Total creado: ' . PagoSueldo::count() . ' lotes con ' . DetallePagoSueldo::count() . ' detalles');
    }
}
