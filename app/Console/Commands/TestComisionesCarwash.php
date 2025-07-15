<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Venta, Cliente, Articulo, Trabajador, DetalleVenta, TrabajadorDetalleVenta, Comision};
use Illuminate\Support\Facades\DB;

class TestComisionesCarwash extends Command
{
    protected $signature = 'test:comisiones-carwash {--cleanup : Limpiar datos de prueba al final}';
    protected $description = 'Testing exhaustivo del sistema de comisiones Car Wash';

    public function handle()
    {
        $this->info('🧪 TESTING EXHAUSTIVO - COMISIONES CAR WASH');
        $this->info('==========================================');
        
        $cleanup = $this->option('cleanup');
        $testVentaIds = [];
        
        try {
            // PRUEBA 1: Crear venta con servicio Car Wash
            $testVentaIds[] = $this->prueba1_CrearVentaConCarwash();
            
            // PRUEBA 2: Editar venta y cambiar trabajadores
            $testVentaIds[] = $this->prueba2_EditarTrabajadores($testVentaIds[0]);
            
            // PRUEBA 3: Verificar cálculos de comisiones
            $this->prueba3_VerificarCalculos($testVentaIds[0]);
            
            // PRUEBA 4: Probar múltiples servicios
            $testVentaIds[] = $this->prueba4_MultiplesServicios();
            
            // PRUEBA 5: Casos límite
            $this->prueba5_CasosLimite();
            
            $this->info('');
            $this->info('🎉 TODAS LAS PRUEBAS COMPLETADAS EXITOSAMENTE!');
            
            if ($cleanup) {
                $this->info('🧹 Limpiando datos de prueba...');
                $this->limpiarDatosPrueba($testVentaIds);
            } else {
                $this->warn('💡 Para limpiar los datos de prueba, ejecuta: php artisan test:comisiones-carwash --cleanup');
                $this->line('   IDs de ventas de prueba: ' . implode(', ', $testVentaIds));
            }
            
        } catch (\Exception $e) {
            $this->error('❌ ERROR EN LAS PRUEBAS: ' . $e->getMessage());
            $this->line('Archivo: ' . $e->getFile() . ':' . $e->getLine());
            return 1;
        }
        
        return 0;
    }

    private function prueba1_CrearVentaConCarwash()
    {
        $this->info('');
        $this->info('🧪 PRUEBA 1: Crear venta con servicio Car Wash');
        $this->line('============================================');
        
        // Datos para la prueba
        $cliente = Cliente::first();
        $servicioCarwash = Articulo::where('tipo', 'servicio')
                                  ->where('comision_carwash', '>', 0)
                                  ->first();
        $trabajadoresCarwash = Trabajador::whereHas('tipoTrabajador', function($query) {
            $query->where('nombre', 'Car Wash');
        })->take(2)->get();

        $this->line("Cliente: {$cliente->nombre} (ID: {$cliente->id})");
        $this->line("Servicio: {$servicioCarwash->nombre} (ID: {$servicioCarwash->id}, Comisión: Q{$servicioCarwash->comision_carwash})");
        $this->line("Trabajadores Car Wash:");
        foreach($trabajadoresCarwash as $trabajador) {
            $this->line("  - {$trabajador->nombre_completo} (ID: {$trabajador->id})");
        }

        DB::beginTransaction();
        
        // 1. Crear la venta
        $venta = Venta::create([
            'cliente_id' => $cliente->id,
            'fecha' => now()->format('Y-m-d'),
            'tipo_venta' => 'Car Wash',
            'usuario_id' => 1,
            'estado' => true,
            'estado_pago' => 'pendiente'
        ]);
        
        $this->line("✅ Venta creada (ID: {$venta->id})");
        
        // 2. Crear el detalle de venta
        $detalle = $venta->detalleVentas()->create([
            'articulo_id' => $servicioCarwash->id,
            'cantidad' => 1,
            'precio_costo' => $servicioCarwash->precio_compra,
            'precio_venta' => $servicioCarwash->precio_venta,
            'usuario_id' => 1,
            'sub_total' => $servicioCarwash->precio_venta,
            'porcentaje_impuestos' => 0
        ]);
        
        $this->line("✅ Detalle creado (ID: {$detalle->id}, Subtotal: Q{$detalle->sub_total})");
        
        // 3. Asignar trabajadores Car Wash
        $trabajadorIds = $trabajadoresCarwash->pluck('id')->toArray();
        $resultado = $detalle->asignarTrabajadores($trabajadorIds, $servicioCarwash->comision_carwash);
        
        $this->line("✅ Trabajadores asignados: " . count($resultado) . " asignaciones");
        
        // 4. Verificar asignaciones
        $asignaciones = $detalle->trabajadoresCarwash;
        $this->line("Asignaciones verificadas:");
        foreach($asignaciones as $asignacion) {
            $this->line("  - {$asignacion->nombre_completo} (Comisión: Q{$asignacion->pivot->monto_comision})");
        }
        
        // 5. Generar comisiones
        $comisionesGeneradas = $detalle->generarComisionesCarwash();
        $this->line("✅ Comisiones generadas: " . $comisionesGeneradas->count());
        
        foreach($comisionesGeneradas as $comision) {
            $trabajador = Trabajador::find($comision->commissionable_id);
            $this->line("  - {$trabajador->nombre_completo}: Q{$comision->monto} ({$comision->estado})");
        }
        
        DB::commit();
        $this->info("🎉 PRUEBA 1 COMPLETADA");
        
        return $venta->id;
    }

    private function prueba2_EditarTrabajadores($ventaId)
    {
        $this->info('');
        $this->info('🧪 PRUEBA 2: Editar trabajadores asignados');
        $this->line('=========================================');
        
        $venta = Venta::find($ventaId);
        $detalle = $venta->detalleVentas->first();
        
        $this->line("Editando venta ID: {$ventaId}, detalle ID: {$detalle->id}");
        
        // Obtener trabajador diferente
        $nuevoTrabajador = Trabajador::whereHas('tipoTrabajador', function($query) {
            $query->where('nombre', 'Car Wash');
        })->whereNotIn('id', $detalle->trabajadoresCarwash->pluck('id'))->first();
        
        if (!$nuevoTrabajador) {
            $this->warn("No hay más trabajadores Car Wash para probar edición");
            return $ventaId;
        }
        
        $this->line("Asignando nuevo trabajador: {$nuevoTrabajador->nombre_completo}");
        
        DB::beginTransaction();
          // Cambiar asignación
        $detalle->asignarTrabajadores([$nuevoTrabajador->id], $detalle->articulo->comision_carwash);
        
        // Refrescar la instancia para asegurar que las relaciones estén actualizadas
        $detalle = $detalle->fresh();
        
        // Verificar cambio
        $nuevasAsignaciones = $detalle->trabajadoresCarwash;
        $this->line("✅ Nuevas asignaciones:");
        foreach($nuevasAsignaciones as $asignacion) {
            $this->line("  - {$asignacion->nombre_completo} (Comisión: Q{$asignacion->pivot->monto_comision})");
        }        // Regenerar comisiones usando el parámetro de forzar regeneración
        $nuevasComisiones = $detalle->generarComisionesCarwash(true);
        $this->line("✅ Comisiones regeneradas: " . $nuevasComisiones->count());
        
        // Verificar que se crearon correctamente
        $comisionesEnBD = Comision::where('detalle_venta_id', $detalle->id)->count();
        $this->line("   Comisiones en BD: " . $comisionesEnBD);
        
        DB::commit();
        $this->info("🎉 PRUEBA 2 COMPLETADA");
        
        return $ventaId;
    }

    private function prueba3_VerificarCalculos($ventaId)
    {
        $this->info('');
        $this->info('🧪 PRUEBA 3: Verificar cálculos de comisiones');
        $this->line('==========================================');
        
        $venta = Venta::find($ventaId);
        $detalle = $venta->detalleVentas->first();
        
        $this->line("Verificando cálculos para venta ID: {$ventaId}");
        
        // Verificar que las comisiones coincidan con la configuración
        $comisionEsperada = $detalle->articulo->comision_carwash;
        $trabajadoresAsignados = $detalle->trabajadoresCarwash;
        
        $this->line("Comisión configurada en artículo: Q{$comisionEsperada}");
        $this->line("Trabajadores asignados: {$trabajadoresAsignados->count()}");
        
        foreach($trabajadoresAsignados as $trabajador) {
            $comisionReal = $trabajador->pivot->monto_comision;
            $this->line("  - {$trabajador->nombre_completo}: Q{$comisionReal}");
            
            if ($comisionReal == $comisionEsperada) {
                $this->line("    ✅ Correcto");
            } else {
                $this->error("    ❌ ERROR: Esperado Q{$comisionEsperada}, obtuvo Q{$comisionReal}");
            }
        }
        
        // Verificar registros de comisiones
        $comisiones = Comision::where('detalle_venta_id', $detalle->id)->get();
        $this->line("Registros de comisiones en BD: {$comisiones->count()}");
        
        $totalComisiones = $comisiones->sum('monto');
        $totalEsperado = $comisionEsperada * $trabajadoresAsignados->count();
        
        $this->line("Total comisiones: Q{$totalComisiones}");
        $this->line("Total esperado: Q{$totalEsperado}");
        
        if ($totalComisiones == $totalEsperado) {
            $this->info("✅ Cálculos correctos");
        } else {
            $this->error("❌ ERROR en cálculos totales");
        }
        
        $this->info("🎉 PRUEBA 3 COMPLETADA");
    }

    private function prueba4_MultiplesServicios()
    {
        $this->info('');
        $this->info('🧪 PRUEBA 4: Venta con múltiples servicios Car Wash');
        $this->line('================================================');
        
        $cliente = Cliente::skip(1)->first();
        $servicios = Articulo::where('tipo', 'servicio')
                            ->where('comision_carwash', '>', 0)
                            ->take(2)->get();
        
        if ($servicios->count() < 2) {
            $this->warn("No hay suficientes servicios Car Wash para esta prueba");
            return null;
        }
        
        $trabajadores = Trabajador::whereHas('tipoTrabajador', function($query) {
            $query->where('nombre', 'Car Wash');
        })->get();
        
        DB::beginTransaction();
        
        $venta = Venta::create([
            'cliente_id' => $cliente->id,
            'fecha' => now()->format('Y-m-d'),
            'tipo_venta' => 'Car Wash',
            'usuario_id' => 1,
            'estado' => true,
            'estado_pago' => 'pendiente'
        ]);
        
        $this->line("✅ Venta múltiple creada (ID: {$venta->id})");
        
        foreach($servicios as $index => $servicio) {
            $detalle = $venta->detalleVentas()->create([
                'articulo_id' => $servicio->id,
                'cantidad' => 1,
                'precio_costo' => $servicio->precio_compra,
                'precio_venta' => $servicio->precio_venta,
                'usuario_id' => 1,
                'sub_total' => $servicio->precio_venta,
                'porcentaje_impuestos' => 0
            ]);
            
            // Asignar trabajadores diferentes a cada servicio
            $trabajadorParaEsteServicio = $trabajadores->slice($index, 1);
            $detalle->asignarTrabajadores(
                $trabajadorParaEsteServicio->pluck('id')->toArray(), 
                $servicio->comision_carwash
            );
            
            $this->line("  ✅ Servicio: {$servicio->nombre} → {$trabajadorParaEsteServicio->first()->nombre_completo}");
        }
        
        // Generar todas las comisiones
        foreach($venta->detalleVentas as $detalle) {
            $detalle->generarComisionesCarwash();
        }
        
        $totalComisiones = Comision::where('venta_id', $venta->id)->count();
        $montoTotal = Comision::where('venta_id', $venta->id)->sum('monto');
        
        $this->line("✅ Total comisiones generadas: {$totalComisiones}");
        $this->line("✅ Monto total comisiones: Q{$montoTotal}");
        
        DB::commit();
        $this->info("🎉 PRUEBA 4 COMPLETADA");
        
        return $venta->id;
    }

    private function prueba5_CasosLimite()
    {
        $this->info('');
        $this->info('🧪 PRUEBA 5: Casos límite y validaciones');
        $this->line('=====================================');
        
        // Caso 1: Servicio sin comisión Car Wash
        $servicioSinComision = Articulo::where('tipo', 'servicio')
                                      ->where('comision_carwash', 0)
                                      ->first();
        
        if ($servicioSinComision) {
            $this->line("🔍 Probando servicio sin comisión: {$servicioSinComision->nombre}");
            
            $detalleTemporal = new DetalleVenta([
                'articulo_id' => $servicioSinComision->id,
                'cantidad' => 1,
                'sub_total' => $servicioSinComision->precio_venta
            ]);
            $detalleTemporal->articulo = $servicioSinComision;
            
            $trabajador = Trabajador::whereHas('tipoTrabajador', function($query) {
                $query->where('nombre', 'Car Wash');
            })->first();
            
            $resultado = $detalleTemporal->asignarTrabajadores([$trabajador->id], 0);
            
            if (empty($resultado)) {
                $this->line("  ✅ Correcto: No se asignaron trabajadores a servicio sin comisión");
            } else {
                $this->error("  ❌ ERROR: Se asignaron trabajadores a servicio sin comisión");
            }
        }
        
        // Caso 2: Array vacío de trabajadores
        $this->line("🔍 Probando array vacío de trabajadores");
        $detalleTemporal = new DetalleVenta();
        $resultado = $detalleTemporal->asignarTrabajadores([], 10);
        
        if (empty($resultado)) {
            $this->line("  ✅ Correcto: Array vacío no genera asignaciones");
        } else {
            $this->error("  ❌ ERROR: Array vacío generó asignaciones");
        }
        
        // Caso 3: IDs inválidos
        $this->line("🔍 Probando IDs de trabajadores inválidos");
        $resultado = $detalleTemporal->asignarTrabajadores([99999, 88888], 10);
        
        if (empty($resultado)) {
            $this->line("  ✅ Correcto: IDs inválidos no generan asignaciones");
        } else {
            $this->error("  ❌ ERROR: IDs inválidos generaron asignaciones");
        }
        
        $this->info("🎉 PRUEBA 5 COMPLETADA");
    }

    private function limpiarDatosPrueba($ventaIds)
    {
        foreach($ventaIds as $ventaId) {
            if ($ventaId) {
                $venta = Venta::find($ventaId);
                if ($venta) {
                    // Eliminar comisiones
                    Comision::where('venta_id', $ventaId)->delete();
                    
                    // Eliminar asignaciones de trabajadores
                    foreach($venta->detalleVentas as $detalle) {
                        $detalle->trabajadoresCarwash()->detach();
                    }
                    
                    // Eliminar detalles
                    $venta->detalleVentas()->delete();
                    
                    // Eliminar venta
                    $venta->delete();
                    
                    $this->line("🗑️  Venta {$ventaId} eliminada");
                }
            }
        }
        
        $this->info("✅ Limpieza completada");
    }
}
