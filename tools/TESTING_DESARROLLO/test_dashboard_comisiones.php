<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Trabajador;
use App\Models\User;
use App\Models\Articulo;

// Configurar la base de datos
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'dbjirehapp',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    echo "=== VERIFICACIÓN DE DATOS PARA DASHBOARD DE COMISIONES ===\n\n";
    
    // 1. Verificar ventas existentes
    $totalVentas = Venta::where('estado', 1)->count();
    echo "✓ Total de ventas activas: {$totalVentas}\n";
    
    // 2. Verificar ventas con vendedor asignado
    $ventasConVendedor = Venta::where('estado', 1)->whereNotNull('usuario_id')->count();
    echo "✓ Ventas con vendedor asignado: {$ventasConVendedor}\n";
    
    // 3. Verificar servicios con mecánico
    $serviciosConMecanico = Articulo::where('tipo', 'servicio')
                                   ->whereNotNull('mecanico_id')
                                   ->count();
    echo "✓ Servicios con mecánico asignado: {$serviciosConMecanico}\n";
    
    // 4. Verificar trabajadores carwash asignados
    $carwashAsignados = $capsule::table('trabajador_detalle_venta')
                           ->count();
    echo "✓ Asignaciones de carwash: {$carwashAsignados}\n";
    
    // 5. Verificar tipos de trabajadores
    $mecanicos = Trabajador::whereHas('tipoTrabajador', function($q) {
        $q->where('nombre', 'like', '%mecánico%');
    })->count();
    echo "✓ Mecánicos registrados: {$mecanicos}\n";
    
    $carwasheros = Trabajador::whereHas('tipoTrabajador', function($q) {
        $q->where('nombre', 'like', '%carwash%');
    })->count();
    echo "✓ Carwasheros registrados: {$carwasheros}\n";
    
    // 6. Verificar vendedores (usuarios administradores)
    $vendedores = User::where('role_as', 1)->count();
    echo "✓ Vendedores (usuarios admin): {$vendedores}\n";
    
    echo "\n=== CÁLCULO DE COMISIONES DE PRUEBA ===\n";
    
    // Comisiones vendedores (5% sobre ventas del mes actual)
    $ventasDelMes = $capsule::table('detalle_ventas as dv')
                         ->join('ventas as v', 'dv.venta_id', '=', 'v.id')
                         ->whereMonth('v.fecha', date('m'))
                         ->whereYear('v.fecha', date('Y'))
                         ->where('v.estado', 1)
                         ->sum('dv.sub_total');
    $comisionVendedores = $ventasDelMes * 0.05;
    echo "✓ Total vendido este mes: Q" . number_format($ventasDelMes, 2) . "\n";
    echo "✓ Comisión vendedores (5%): Q" . number_format($comisionVendedores, 2) . "\n";
    
    // Comisiones mecánicos
    $comisionMecanicos = $capsule::table('detalle_ventas as dv')
        ->join('ventas as v', 'dv.venta_id', '=', 'v.id')
        ->join('articulos as a', 'dv.articulo_id', '=', 'a.id')
        ->whereMonth('v.fecha', date('m'))
        ->whereYear('v.fecha', date('Y'))
        ->where('v.estado', 1)
        ->where('a.tipo', 'servicio')
        ->whereNotNull('a.mecanico_id')
        ->sum($capsule::raw('a.costo_mecanico * dv.cantidad'));
    echo "✓ Comisión mecánicos: Q" . number_format($comisionMecanicos, 2) . "\n";
    
    // Comisiones carwash
    $comisionCarwash = $capsule::table('trabajador_detalle_venta as tdv')
        ->join('detalle_ventas as dv', 'tdv.detalle_venta_id', '=', 'dv.id')
        ->join('ventas as v', 'dv.venta_id', '=', 'v.id')
        ->join('trabajadors as t', 'tdv.trabajador_id', '=', 't.id')
        ->join('tipo_trabajadors as tt', 't.tipo_trabajador_id', '=', 'tt.id')
        ->whereMonth('v.fecha', date('m'))
        ->whereYear('v.fecha', date('Y'))
        ->where('v.estado', 1)
        ->where('tt.nombre', 'like', '%carwash%')
        ->sum('tdv.monto_comision');
    echo "✓ Comisión carwash: Q" . number_format($comisionCarwash, 2) . "\n";
    
    $totalComisiones = $comisionVendedores + $comisionMecanicos + $comisionCarwash;
    echo "\n✓ TOTAL COMISIONES DEL MES: Q" . number_format($totalComisiones, 2) . "\n";
    
    echo "\n=== DASHBOARD LISTO PARA FUNCIONAR ===\n";
    echo "✅ Acceso: http://localhost/comisiones/dashboard\n";
    echo "✅ Sidebar: Sección 'Sistema de Comisiones' -> 'Dashboard Comisiones'\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
