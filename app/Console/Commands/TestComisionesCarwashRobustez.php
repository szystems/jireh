<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Trabajador;
use App\Models\Comision;
use App\Models\Cliente;
use App\Models\Articulo;
use App\Models\TrabajadorDetalleVenta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestComisionesCarwashRobustez extends Command
{
    protected $signature = 'test:comisiones-carwash-robustez {--cleanup : Limpiar datos de prueba al final}';
    protected $description = 'Pruebas de robustez para el sistema de comisiones Car Wash';

    public function handle()
    {
        $this->info('🔬 TESTING DE ROBUSTEZ - COMISIONES CAR WASH');
        $this->line('==============================================');
        
        try {
            // Verificar datos base
            if (!$this->verificarDatosBase()) {
                return 1;
            }
            
            $ventasCreadas = [];
            
            // 1. Prueba con múltiples trabajadores
            $this->info('');
            $ventaId1 = $this->pruebaMultiplesTrabajadores();
            if ($ventaId1) $ventasCreadas[] = $ventaId1;
            
            // 2. Prueba de ediciones consecutivas
            $this->info('');
            $ventaId2 = $this->pruebaEdicionesConsecutivas();
            if ($ventaId2) $ventasCreadas[] = $ventaId2;
            
            // 3. Verificación de integridad de datos
            $this->info('');
            $this->verificarIntegridadDatos();
            
            // 4. Prueba de casos extremos
            $this->info('');
            $ventaId3 = $this->pruebaCasosExtremos();
            if ($ventaId3) $ventasCreadas[] = $ventaId3;
            
            $this->info('');
            $this->info('🎉 TODAS LAS PRUEBAS DE ROBUSTEZ COMPLETADAS!');
            
            // Limpiar si se solicita
            if ($this->option('cleanup')) {
                $this->limpiarDatosPrueba($ventasCreadas);
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error durante las pruebas: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
    
    private function verificarDatosBase()
    {
        $servicioCarwash = Articulo::where('tipo', 'servicio')
            ->where('comision_carwash', '>', 0)
            ->first();
            
        $trabajadoresCarwash = Trabajador::whereHas('tipoTrabajador', function($query) {
            $query->where('nombre', 'Car Wash');
        })->get();
        
        if (!$servicioCarwash) {
            $this->error('❌ No hay servicios Car Wash con comisión configurada');
            return false;
        }
        
        if ($trabajadoresCarwash->count() < 4) {
            $this->error('❌ Se necesitan al menos 4 trabajadores Car Wash para estas pruebas');
            $this->line('   Trabajadores encontrados: ' . $trabajadoresCarwash->count());
            return false;
        }
        
        $this->info('✅ Datos base verificados');
        $this->line("   Servicios Car Wash: 1 (Comisión: Q{$servicioCarwash->comision_carwash})");
        $this->line("   Trabajadores Car Wash: {$trabajadoresCarwash->count()}");
        
        return true;
    }
    
    private function pruebaMultiplesTrabajadores()
    {
        $this->info('🧪 PRUEBA 1: Múltiples trabajadores (4 trabajadores)');
        $this->line('=====================================================');
        
        DB::beginTransaction();
        
        $servicioCarwash = Articulo::where('tipo', 'servicio')
            ->where('comision_carwash', '>', 0)
            ->first();
            
        $trabajadores = Trabajador::whereHas('tipoTrabajador', function($query) {
            $query->where('nombre', 'Car Wash');
        })->take(4)->get();
        
        // Crear venta
        $venta = Venta::create([
            'cliente_id' => 1,
            'usuario_id' => 1,
            'fecha' => now()->toDateString(),
            'tipo_venta' => 'CDS',
            'estado' => true,
            'estado_pago' => 'pendiente'
        ]);
        
        $detalle = $venta->detalleVentas()->create([
            'articulo_id' => $servicioCarwash->id,
            'cantidad' => 1,
            'precio_costo' => $servicioCarwash->precio_compra,
            'precio_venta' => $servicioCarwash->precio_venta,
            'usuario_id' => 1,
            'sub_total' => $servicioCarwash->precio_venta,
            'porcentaje_impuestos' => 0
        ]);
        
        $this->line("Venta creada (ID: {$venta->id})");
        
        // Asignar 4 trabajadores
        $trabajadorIds = $trabajadores->pluck('id')->toArray();
        $detalle->asignarTrabajadores($trabajadorIds, $servicioCarwash->comision_carwash);
        
        $this->line("✅ 4 trabajadores asignados:");
        foreach($trabajadores as $trabajador) {
            $this->line("   - {$trabajador->nombre_completo}");
        }
        
        // Generar comisiones
        $comisiones = $detalle->generarComisionesCarwash();
        $this->line("✅ Comisiones generadas: " . $comisiones->count());
        
        // Cambiar a solo 2 trabajadores
        $nuevosIds = $trabajadores->take(2)->pluck('id')->toArray();
        $detalle->asignarTrabajadores($nuevosIds, $servicioCarwash->comision_carwash);
        
        $detalle = $detalle->fresh();
        $comisionesRegenadas = $detalle->generarComisionesCarwash(true);
        
        $this->line("✅ Trabajadores reducidos a 2");
        $this->line("✅ Comisiones regeneradas: " . $comisionesRegenadas->count());
        
        // Verificar integridad
        $comisionesFinales = Comision::where('detalle_venta_id', $detalle->id)->count();
        if ($comisionesFinales == 2) {
            $this->line("✅ Integridad correcta: {$comisionesFinales} comisiones en BD");
        } else {
            $this->error("❌ Error de integridad: {$comisionesFinales} comisiones (esperadas: 2)");
        }
        
        DB::commit();
        $this->info('🎉 PRUEBA 1 COMPLETADA');
        
        return $venta->id;
    }
    
    private function pruebaEdicionesConsecutivas()
    {
        $this->info('🧪 PRUEBA 2: Ediciones consecutivas (A→B→C→A)');
        $this->line('===============================================');
        
        DB::beginTransaction();
        
        $servicioCarwash = Articulo::where('tipo', 'servicio')
            ->where('comision_carwash', '>', 0)
            ->first();
            
        $trabajadores = Trabajador::whereHas('tipoTrabajador', function($query) {
            $query->where('nombre', 'Car Wash');
        })->take(3)->get();
        
        // Crear venta
        $venta = Venta::create([
            'cliente_id' => 1,
            'usuario_id' => 1,
            'fecha' => now()->toDateString(),
            'tipo_venta' => 'CDS',
            'estado' => true,
            'estado_pago' => 'pendiente'
        ]);
        
        $detalle = $venta->detalleVentas()->create([
            'articulo_id' => $servicioCarwash->id,
            'cantidad' => 1,
            'precio_costo' => $servicioCarwash->precio_compra,
            'precio_venta' => $servicioCarwash->precio_venta,
            'usuario_id' => 1,
            'sub_total' => $servicioCarwash->precio_venta,
            'porcentaje_impuestos' => 0
        ]);
        
        $this->line("Venta creada (ID: {$venta->id})");
        
        // Secuencia de cambios: A → B → C → A
        $secuencia = [
            ['trabajador' => $trabajadores[0], 'paso' => 'A'],
            ['trabajador' => $trabajadores[1], 'paso' => 'B'],
            ['trabajador' => $trabajadores[2], 'paso' => 'C'],
            ['trabajador' => $trabajadores[0], 'paso' => 'A (vuelta)']
        ];
          foreach($secuencia as $i => $cambio) {
            $paso = $i + 1;
            $this->line("🔄 Cambio {$paso}: {$cambio['paso']} - {$cambio['trabajador']->nombre_completo}");
            
            // Asignar trabajador
            $detalle->asignarTrabajadores([$cambio['trabajador']->id], $servicioCarwash->comision_carwash);
            
            // Regenerar comisiones
            $detalle = $detalle->fresh();
            $comisiones = $detalle->generarComisionesCarwash(true);
            
            // Verificar
            $comisionesEnBD = Comision::where('detalle_venta_id', $detalle->id)->count();
            $generadas = $comisiones->count();
            $this->line("   ✅ Comisiones: {$generadas} generadas, {$comisionesEnBD} en BD");
            
            if ($comisiones->count() != 1 || $comisionesEnBD != 1) {
                $this->error("   ❌ Error en cambio {$paso}");
            }
        }
        
        DB::commit();
        $this->info('🎉 PRUEBA 2 COMPLETADA');
        
        return $venta->id;
    }
    
    private function verificarIntegridadDatos()
    {
        $this->info('🧪 VERIFICACIÓN: Integridad de datos');
        $this->line('=====================================');
        
        // Verificar que no hay comisiones huérfanas
        $comisionesHuerfanas = DB::select("
            SELECT c.* FROM comisiones c 
            LEFT JOIN detalle_ventas dv ON c.detalle_venta_id = dv.id 
            WHERE dv.id IS NULL AND c.tipo_comision = 'carwash'
        ");
        
        if (empty($comisionesHuerfanas)) {
            $this->line("✅ No hay comisiones huérfanas");
        } else {
            $this->error("❌ Encontradas " . count($comisionesHuerfanas) . " comisiones huérfanas");
        }
        
        // Verificar consistencia entre asignaciones y comisiones
        $inconsistencias = DB::select("
            SELECT tdv.detalle_venta_id, 
                   COUNT(tdv.id) as asignaciones,
                   COUNT(c.id) as comisiones
            FROM trabajador_detalle_venta tdv
            LEFT JOIN comisiones c ON c.detalle_venta_id = tdv.detalle_venta_id 
                                   AND c.commissionable_id = tdv.trabajador_id
                                   AND c.tipo_comision = 'carwash'
            GROUP BY tdv.detalle_venta_id
            HAVING asignaciones != comisiones
        ");
        
        if (empty($inconsistencias)) {
            $this->line("✅ Consistencia entre asignaciones y comisiones");
        } else {
            $this->error("❌ Encontradas " . count($inconsistencias) . " inconsistencias");
            foreach($inconsistencias as $inc) {
                $this->line("   Detalle {$inc->detalle_venta_id}: {$inc->asignaciones} asignaciones, {$inc->comisiones} comisiones");
            }
        }
        
        $this->info('🎉 VERIFICACIÓN COMPLETADA');
    }
    
    private function pruebaCasosExtremos()
    {
        $this->info('🧪 PRUEBA 3: Casos extremos');
        $this->line('============================');
        
        DB::beginTransaction();
        
        $servicioCarwash = Articulo::where('tipo', 'servicio')
            ->where('comision_carwash', '>', 0)
            ->first();
            
        $trabajador = Trabajador::whereHas('tipoTrabajador', function($query) {
            $query->where('nombre', 'Car Wash');
        })->first();
        
        // Crear venta
        $venta = Venta::create([
            'cliente_id' => 1,
            'usuario_id' => 1,
            'fecha' => now()->toDateString(),
            'tipo_venta' => 'CDS',
            'estado' => true,
            'estado_pago' => 'pendiente'
        ]);
        
        $detalle = $venta->detalleVentas()->create([
            'articulo_id' => $servicioCarwash->id,
            'cantidad' => 1,
            'precio_costo' => $servicioCarwash->precio_compra,
            'precio_venta' => $servicioCarwash->precio_venta,
            'usuario_id' => 1,
            'sub_total' => $servicioCarwash->precio_venta,
            'porcentaje_impuestos' => 0
        ]);
        
        // Caso 1: Eliminar todos los trabajadores
        $this->line("🔍 Caso 1: Eliminar todos los trabajadores");
        $detalle->asignarTrabajadores([$trabajador->id], $servicioCarwash->comision_carwash);
        $detalle->generarComisionesCarwash();
        
        $detalle->asignarTrabajadores([], 0); // Array vacío
        $detalle = $detalle->fresh();
        $comisiones = $detalle->generarComisionesCarwash(true);
        
        $comisionesEnBD = Comision::where('detalle_venta_id', $detalle->id)->count();
        $this->line("   ✅ Trabajadores eliminados: {$comisionesEnBD} comisiones restantes");
        
        // Caso 2: Cambiar monto de comisión a 0
        $this->line("🔍 Caso 2: Comisión con monto 0");
        $detalle->asignarTrabajadores([$trabajador->id], 0); // Comisión 0
        $detalle = $detalle->fresh();
        $comisiones = $detalle->generarComisionesCarwash(true);
        
        $this->line("   ✅ Comisión con monto 0: {$comisiones->count()} comisiones generadas");
        
        DB::commit();
        $this->info('🎉 PRUEBA 3 COMPLETADA');
        
        return $venta->id;
    }
    
    private function limpiarDatosPrueba($ventasCreadas)
    {
        $this->info('');
        $this->info('🧹 Limpiando datos de prueba...');
        
        foreach($ventasCreadas as $ventaId) {
            $venta = Venta::find($ventaId);
            if ($venta) {
                // Eliminar comisiones
                foreach($venta->detalleVentas as $detalle) {
                    Comision::where('detalle_venta_id', $detalle->id)->delete();
                    TrabajadorDetalleVenta::where('detalle_venta_id', $detalle->id)->delete();
                }
                
                $venta->delete();
                $this->line("🗑️  Venta {$ventaId} eliminada");
            }
        }
        
        $this->line("✅ Limpieza completada");
    }
}
