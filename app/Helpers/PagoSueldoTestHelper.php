<?php

namespace App\Helpers;

use App\Models\PagoSueldo;
use App\Models\DetallePagoSueldo;
use App\Models\Trabajador;
use App\Models\User;
use Carbon\Carbon;

class PagoSueldoTestHelper
{
    /**
     * Generar datos de ejemplo para crear lotes de prueba
     */
    public static function getEmpleadosEjemplo()
    {
        $trabajadores = Trabajador::activos()->take(3)->get();
        $usuarios = User::where('estado', 1)->where('role_as', 0)->take(2)->get();
        
        $empleadosEjemplo = [];
        
        // Agregar trabajadores de ejemplo
        foreach ($trabajadores as $index => $trabajador) {
            $empleadosEjemplo[] = [
                'tipo' => 'trabajador',
                'id' => $trabajador->id,
                'nombre' => $trabajador->nombre,
                'sueldo_base' => rand(3000, 8000),
                'horas_extra' => rand(0, 20),
                'valor_hora_extra' => rand(25, 50),
                'comisiones' => rand(0, 1000),
                'bonificaciones' => rand(0, 500),
                'descuentos' => rand(0, 300),
                'observaciones' => 'Empleado de prueba #' . ($index + 1)
            ];
        }
        
        // Agregar usuarios de ejemplo
        foreach ($usuarios as $index => $usuario) {
            $empleadosEjemplo[] = [
                'tipo' => 'usuario',
                'id' => $usuario->id,
                'nombre' => $usuario->name,
                'sueldo_base' => rand(4000, 10000),
                'horas_extra' => rand(0, 15),
                'valor_hora_extra' => rand(30, 60),
                'comisiones' => rand(500, 2000),
                'bonificaciones' => rand(0, 800),
                'descuentos' => rand(0, 400),
                'observaciones' => 'Vendedor de prueba #' . ($index + 1)
            ];
        }
        
        return $empleadosEjemplo;
    }
    
    /**
     * Generar lote de ejemplo con errores intencionados para probar validación
     */
    public static function getLoteConErrores()
    {
        return [
            'periodo_mes' => '', // Error: campo requerido
            'periodo_anio' => date('Y'),
            'metodo_pago' => 'transferencia',
            'fecha_pago' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'observaciones' => 'Lote de prueba con errores de validación',
            'empleados' => [] // Error: array vacío
        ];
    }
    
    /**
     * Generar lote válido de ejemplo
     */
    public static function getLoteValido()
    {
        return [
            'periodo_mes' => date('n'),
            'periodo_anio' => date('Y'),
            'metodo_pago' => 'transferencia',
            'fecha_pago' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'observaciones' => 'Lote de prueba válido - ' . Carbon::now()->format('d/m/Y H:i'),
            'empleados' => self::getEmpleadosEjemplo()
        ];
    }
    
    /**
     * Calcular totales de un empleado
     */
    public static function calcularTotalesEmpleado($empleado)
    {
        $sueldoBase = $empleado['sueldo_base'] ?? 0;
        $horasExtra = ($empleado['horas_extra'] ?? 0) * ($empleado['valor_hora_extra'] ?? 0);
        $comisiones = $empleado['comisiones'] ?? 0;
        $bonificaciones = $empleado['bonificaciones'] ?? 0;
        $descuentos = $empleado['descuentos'] ?? 0;
        
        $totalBonificaciones = $horasExtra + $comisiones + $bonificaciones;
        $totalPagar = $sueldoBase + $totalBonificaciones - $descuentos;
        
        return [
            'sueldo_base' => $sueldoBase,
            'total_bonificaciones' => $totalBonificaciones,
            'total_descuentos' => $descuentos,
            'total_pagar' => $totalPagar
        ];
    }
}
