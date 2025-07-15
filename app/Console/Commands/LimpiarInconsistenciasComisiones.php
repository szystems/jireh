<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Comision;
use App\Models\TrabajadorDetalleVenta;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\DB;

class LimpiarInconsistenciasComisiones extends Command
{
    protected $signature = 'fix:inconsistencias-comisiones {--dry-run : Solo mostrar qué se va a limpiar sin ejecutar}';
    protected $description = 'Limpia inconsistencias entre asignaciones de trabajadores y comisiones';

    public function handle()
    {
        $this->info('🧹 LIMPIEZA DE INCONSISTENCIAS - COMISIONES');
        $this->line('===========================================');
        
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('🔍 MODO DRY-RUN: Solo se mostrará qué se va a limpiar');
        }
        
        try {
            // 1. Buscar comisiones huérfanas (sin detalle de venta)
            $this->limpiarComisionesHuerfanas($dryRun);
            
            // 2. Buscar asignaciones sin comisiones correspondientes
            $this->limpiarAsignacionesSinComisiones($dryRun);
            
            // 3. Buscar comisiones sin asignaciones correspondientes
            $this->limpiarComisionesSinAsignaciones($dryRun);
            
            // 4. Verificar estado final
            $this->verificarEstadoFinal();
            
            if (!$dryRun) {
                $this->info('✅ Limpieza completada exitosamente');
            } else {
                $this->info('🔍 Análisis completado. Ejecuta sin --dry-run para aplicar cambios');
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error durante la limpieza: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function limpiarComisionesHuerfanas($dryRun)
    {
        $this->info('');
        $this->info('🔍 1. Buscando comisiones huérfanas...');
        
        $comisionesHuerfanas = DB::select("
            SELECT c.* FROM comisiones c 
            LEFT JOIN detalle_ventas dv ON c.detalle_venta_id = dv.id 
            WHERE dv.id IS NULL AND c.tipo_comision = 'carwash'
        ");
        
        if (empty($comisionesHuerfanas)) {
            $this->line("   ✅ No se encontraron comisiones huérfanas");
            return;
        }
        
        $this->warn("   ⚠️  Encontradas " . count($comisionesHuerfanas) . " comisiones huérfanas:");
        
        foreach($comisionesHuerfanas as $comision) {
            $this->line("      - Comisión ID {$comision->id} → Detalle inexistente ID {$comision->detalle_venta_id}");
        }
        
        if (!$dryRun) {
            $ids = array_column($comisionesHuerfanas, 'id');
            $eliminadas = Comision::whereIn('id', $ids)->delete();
            $this->info("   🗑️  {$eliminadas} comisiones huérfanas eliminadas");
        }
    }
    
    private function limpiarAsignacionesSinComisiones($dryRun)
    {
        $this->info('');
        $this->info('🔍 2. Buscando asignaciones sin comisiones...');
        
        $asignacionesSinComisiones = DB::select("
            SELECT tdv.*, a.nombre as articulo_nombre
            FROM trabajador_detalle_venta tdv
            INNER JOIN detalle_ventas dv ON tdv.detalle_venta_id = dv.id
            INNER JOIN articulos a ON dv.articulo_id = a.id
            LEFT JOIN comisiones c ON c.detalle_venta_id = tdv.detalle_venta_id 
                                   AND c.commissionable_id = tdv.trabajador_id
                                   AND c.tipo_comision = 'carwash'
            WHERE c.id IS NULL 
              AND a.tipo = 'servicio' 
              AND a.comision_carwash > 0
              AND tdv.monto_comision > 0
        ");
        
        if (empty($asignacionesSinComisiones)) {
            $this->line("   ✅ No se encontraron asignaciones sin comisiones");
            return;
        }
        
        $this->warn("   ⚠️  Encontradas " . count($asignacionesSinComisiones) . " asignaciones sin comisiones:");
        
        foreach($asignacionesSinComisiones as $asignacion) {
            $this->line("      - Trabajador {$asignacion->trabajador_id} → Detalle {$asignacion->detalle_venta_id} ({$asignacion->articulo_nombre}) - Q{$asignacion->monto_comision}");
        }
        
        if (!$dryRun) {
            $comisionesCreadas = 0;
            
            foreach($asignacionesSinComisiones as $asignacion) {
                try {
                    // Obtener información adicional
                    $detalleVenta = DetalleVenta::find($asignacion->detalle_venta_id);
                    
                    if ($detalleVenta) {
                        $comision = Comision::create([
                            'commissionable_id' => $asignacion->trabajador_id,
                            'commissionable_type' => 'App\Models\Trabajador',
                            'tipo_comision' => 'carwash',
                            'monto' => $asignacion->monto_comision,
                            'detalle_venta_id' => $asignacion->detalle_venta_id,
                            'venta_id' => $detalleVenta->venta_id,
                            'articulo_id' => $detalleVenta->articulo_id,
                            'fecha_calculo' => now(),
                            'estado' => 'pendiente',
                        ]);
                        
                        if ($comision) {
                            $comisionesCreadas++;
                        }
                    }
                } catch (\Exception $e) {
                    $this->error("      ❌ Error creando comisión para asignación {$asignacion->detalle_venta_id}: " . $e->getMessage());
                }
            }
            
            $this->info("   ✅ {$comisionesCreadas} comisiones creadas para asignaciones faltantes");
        }
    }
    
    private function limpiarComisionesSinAsignaciones($dryRun)
    {
        $this->info('');
        $this->info('🔍 3. Buscando comisiones sin asignaciones...');
        
        $comisionesSinAsignaciones = DB::select("
            SELECT c.*, t.nombre, t.apellido
            FROM comisiones c
            INNER JOIN trabajadors t ON c.commissionable_id = t.id
            LEFT JOIN trabajador_detalle_venta tdv ON c.detalle_venta_id = tdv.detalle_venta_id 
                                                   AND c.commissionable_id = tdv.trabajador_id
            WHERE tdv.id IS NULL 
              AND c.tipo_comision = 'carwash'
              AND c.commissionable_type = 'App\\\Models\\\Trabajador'
        ");
        
        if (empty($comisionesSinAsignaciones)) {
            $this->line("   ✅ No se encontraron comisiones sin asignaciones");
            return;
        }
        
        $this->warn("   ⚠️  Encontradas " . count($comisionesSinAsignaciones) . " comisiones sin asignaciones:");
        
        foreach($comisionesSinAsignaciones as $comision) {
            $this->line("      - Comisión ID {$comision->id} → {$comision->nombre} {$comision->apellido} - Q{$comision->monto} (Detalle {$comision->detalle_venta_id})");
        }
        
        if (!$dryRun) {
            $this->warn("   ⚠️  Estas comisiones probablemente son de pruebas anteriores");
            $this->warn("   🗑️  Se eliminarán por seguridad...");
            
            $ids = array_column($comisionesSinAsignaciones, 'id');
            $eliminadas = Comision::whereIn('id', $ids)->delete();
            $this->info("   🗑️  {$eliminadas} comisiones sin asignaciones eliminadas");
        }
    }
    
    private function verificarEstadoFinal()
    {
        $this->info('');
        $this->info('🔍 4. Verificación final...');
        
        // Verificar comisiones huérfanas
        $huerfanas = DB::select("
            SELECT COUNT(*) as total FROM comisiones c 
            LEFT JOIN detalle_ventas dv ON c.detalle_venta_id = dv.id 
            WHERE dv.id IS NULL AND c.tipo_comision = 'carwash'
        ")[0]->total;
        
        // Verificar inconsistencias
        $inconsistencias = DB::select("
            SELECT COUNT(DISTINCT tdv.detalle_venta_id) as total
            FROM trabajador_detalle_venta tdv
            INNER JOIN detalle_ventas dv ON tdv.detalle_venta_id = dv.id
            INNER JOIN articulos a ON dv.articulo_id = a.id
            LEFT JOIN comisiones c ON c.detalle_venta_id = tdv.detalle_venta_id 
                                   AND c.commissionable_id = tdv.trabajador_id
                                   AND c.tipo_comision = 'carwash'
            WHERE c.id IS NULL 
              AND a.tipo = 'servicio' 
              AND a.comision_carwash > 0
              AND tdv.monto_comision > 0
        ")[0]->total;
        
        if ($huerfanas == 0 && $inconsistencias == 0) {
            $this->info('   ✅ ¡Perfecto! No hay inconsistencias');
        } else {
            $this->warn("   ⚠️  Aún hay: {$huerfanas} huérfanas, {$inconsistencias} inconsistencias");
        }
        
        // Estadísticas generales
        $totalComisiones = Comision::where('tipo_comision', 'carwash')->count();
        $totalAsignaciones = TrabajadorDetalleVenta::count();
        
        $this->info("   📊 Estadísticas: {$totalComisiones} comisiones Car Wash, {$totalAsignaciones} asignaciones");
    }
}
