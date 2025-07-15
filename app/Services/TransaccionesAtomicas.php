<?php

namespace App\Services;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Articulo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class TransaccionesAtomicas
{
    /**
     * OPCIÓN 2: SISTEMA DE TRANSACCIONES ATÓMICAS Y ROLLBACK AUTOMÁTICO
     * 
     * Garantiza que todas las operaciones complejas sean completamente exitosas
     * o completamente revertidas, evitando estados inconsistentes.
     */

    private $puntosSavepoint = [];
    private $operacionesLog = [];

    /**
     * Ejecutar venta con transacción atómica completa
     */
    public function ejecutarVentaAtomica($datosVenta, $detalles)
    {
        $ventaId = null;
        $operacionesEjecutadas = [];
        
        try {
            DB::beginTransaction();
            $this->crearSavepoint('inicio_venta');
            
            // Paso 1: Crear la venta
            $this->logOperacion('Creando venta', $datosVenta);
            $venta = $this->crearVentaConValidacion($datosVenta);
            $ventaId = $venta->id;
            $operacionesEjecutadas[] = "Venta creada ID: {$ventaId}";
            
            $this->crearSavepoint('venta_creada');
            
            // Paso 2: Procesar cada detalle de venta
            $totalCalculado = 0;
            foreach ($detalles as $index => $detalle) {
                $this->logOperacion("Procesando detalle {$index}", $detalle);
                
                $detalleVenta = $this->procesarDetalleAtomica($venta, $detalle);
                $totalCalculado += $detalleVenta->subtotal;
                $operacionesEjecutadas[] = "Detalle procesado: {$detalle['articulo_id']} x {$detalle['cantidad']}";
                
                // Crear savepoint después de cada detalle
                $this->crearSavepoint("detalle_{$index}");
            }
            
            // Paso 3: Actualizar total de venta y validar coherencia
            $this->validarYActualizarTotalVenta($venta, $totalCalculado);
            $operacionesEjecutadas[] = "Total validado: {$totalCalculado}";
            
            // Paso 4: Validación final de integridad
            $this->validacionFinalIntegridad($venta);
            $operacionesEjecutadas[] = "Validación final completada";
            
            // Paso 5: Commit de la transacción
            DB::commit();
            $this->limpiarSavepoints();
            
            $this->logOperacion('Venta completada exitosamente', [
                'venta_id' => $ventaId,
                'total' => $totalCalculado,
                'operaciones' => $operacionesEjecutadas
            ]);
            
            return [
                'exito' => true,
                'venta_id' => $ventaId,
                'total' => $totalCalculado,
                'mensaje' => 'Venta procesada exitosamente',
                'operaciones_ejecutadas' => $operacionesEjecutadas
            ];
            
        } catch (Exception $e) {
            // Rollback automático con información detallada
            $this->ejecutarRollbackInteligente($e, $ventaId, $operacionesEjecutadas);
            
            return [
                'exito' => false,
                'error' => $e->getMessage(),
                'venta_id' => $ventaId,
                'operaciones_revertidas' => $operacionesEjecutadas,
                'punto_fallo' => $this->identificarPuntoFallo($e)
            ];
        }
    }
    
    /**
     * Crear venta con validación previa
     */
    private function crearVentaConValidacion($datosVenta)
    {
        // Validaciones previas
        $this->validarDatosVenta($datosVenta);
        
        $venta = new Venta();
        $venta->fill($datosVenta);
        $venta->estado = 'procesando'; // Estado temporal
        $venta->save();
        
        return $venta;
    }
    
    /**
     * Procesar detalle de venta de forma atómica
     */
    private function procesarDetalleAtomica($venta, $detalle)
    {
        try {
            // 1. Validar y bloquear artículo
            $articulo = Articulo::lockForUpdate()->findOrFail($detalle['articulo_id']);
            
            // 2. Verificar stock disponible
            if ($articulo->stock < $detalle['cantidad']) {
                throw new Exception("Stock insuficiente para {$articulo->codigo}. Disponible: {$articulo->stock}, Requerido: {$detalle['cantidad']}");
            }
            
            // 3. Crear detalle de venta
            $detalleVenta = new DetalleVenta();
            $detalleVenta->venta_id = $venta->id;
            $detalleVenta->articulo_id = $detalle['articulo_id'];
            $detalleVenta->cantidad = $detalle['cantidad'];
            $detalleVenta->precio_unitario = $detalle['precio_unitario'];
            $detalleVenta->subtotal = $detalle['cantidad'] * $detalle['precio_unitario'];
            $detalleVenta->save();
            
            // 4. Actualizar stock de forma atómica
            $stockAnterior = $articulo->stock;
            $articulo->stock -= $detalle['cantidad'];
            $articulo->save();
            
            // 5. Registrar el movimiento para auditoría
            $this->registrarMovimientoStock($articulo, $stockAnterior, $articulo->stock, 'VENTA', $venta->id);
            
            return $detalleVenta;
            
        } catch (Exception $e) {
            throw new Exception("Error procesando detalle para artículo {$detalle['articulo_id']}: " . $e->getMessage());
        }
    }
    
    /**
     * Validar y actualizar total de venta
     */
    private function validarYActualizarTotalVenta($venta, $totalCalculado)
    {
        // Recalcular total desde la base de datos para verificar consistencia
        $totalBD = DB::table('detalle_ventas')
            ->where('venta_id', $venta->id)
            ->sum('subtotal');
        
        if (abs($totalBD - $totalCalculado) > 0.01) {
            throw new Exception("Inconsistencia en total de venta. Calculado: {$totalCalculado}, BD: {$totalBD}");
        }
        
        $venta->total = $totalCalculado;
        $venta->estado = 'completada';
        $venta->save();
    }
    
    /**
     * Validación final de integridad
     */
    private function validacionFinalIntegridad($venta)
    {
        // Verificar que todos los detalles existen
        $detallesCount = DetalleVenta::where('venta_id', $venta->id)->count();
        if ($detallesCount == 0) {
            throw new Exception("Venta sin detalles: ID {$venta->id}");
        }
        
        // Verificar que no hay stock negativo
        $stocksNegativos = DB::table('articulos')
            ->whereIn('id', function($query) use ($venta) {
                $query->select('articulo_id')
                      ->from('detalle_ventas')
                      ->where('venta_id', $venta->id);
            })
            ->where('stock', '<', 0)
            ->count();
        
        if ($stocksNegativos > 0) {
            throw new Exception("Se detectaron stocks negativos después de la venta");
        }
        
        // Verificar coherencia de totales
        $totalDetalles = DetalleVenta::where('venta_id', $venta->id)->sum('subtotal');
        if (abs($totalDetalles - $venta->total) > 0.01) {
            throw new Exception("Total de venta no coincide con suma de detalles");
        }
    }
    
    /**
     * Ejecutar rollback inteligente
     */
    private function ejecutarRollbackInteligente($excepcion, $ventaId, $operacionesEjecutadas)
    {
        try {
            DB::rollBack();
            
            // Log detallado del rollback
            Log::error('Rollback automático ejecutado', [
                'excepcion' => $excepcion->getMessage(),
                'venta_id' => $ventaId,
                'operaciones_revertidas' => $operacionesEjecutadas,
                'savepoints' => $this->puntosSavepoint,
                'archivo' => $excepcion->getFile(),
                'linea' => $excepcion->getLine()
            ]);
            
            // Limpiar caches relacionados
            $this->limpiarCachesRollback($ventaId);
            
            // Notificar a sistemas externos si es necesario
            $this->notificarRollback($ventaId, $excepcion);
            
        } catch (Exception $rollbackException) {
            Log::critical('Error durante rollback', [
                'excepcion_original' => $excepcion->getMessage(),
                'excepcion_rollback' => $rollbackException->getMessage(),
                'venta_id' => $ventaId
            ]);
        }
    }
    
    /**
     * Crear savepoint para rollback parcial
     */
    private function crearSavepoint($nombre)
    {
        $nombreSavepoint = "sp_" . $nombre . "_" . time();
        DB::statement("SAVEPOINT {$nombreSavepoint}");
        $this->puntosSavepoint[] = $nombreSavepoint;
        
        $this->logOperacion("Savepoint creado", ['nombre' => $nombreSavepoint]);
    }
    
    /**
     * Validar datos básicos de venta
     */
    private function validarDatosVenta($datosVenta)
    {
        if (empty($datosVenta['fecha'])) {
            throw new Exception("Fecha de venta requerida");
        }
        
        if (empty($datosVenta['user_id'])) {
            throw new Exception("Usuario vendedor requerido");
        }
        
        // Validar que el usuario existe
        if (!DB::table('users')->where('id', $datosVenta['user_id'])->exists()) {
            throw new Exception("Usuario vendedor no existe: {$datosVenta['user_id']}");
        }
    }
    
    /**
     * Registrar movimiento de stock para auditoría
     */
    private function registrarMovimientoStock($articulo, $stockAnterior, $stockNuevo, $tipo, $referenciaId)
    {
        DB::table('movimientos_stock')->insert([
            'articulo_id' => $articulo->id,
            'tipo' => $tipo,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $stockNuevo,
            'cantidad' => $stockAnterior - $stockNuevo,
            'referencia_id' => $referenciaId,
            'user_id' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    
    /**
     * Identificar punto específico de fallo
     */
    private function identificarPuntoFallo($excepcion)
    {
        $mensaje = $excepcion->getMessage();
        
        if (strpos($mensaje, 'Stock insuficiente') !== false) {
            return 'VALIDACION_STOCK';
        } elseif (strpos($mensaje, 'Usuario') !== false) {
            return 'VALIDACION_USUARIO';
        } elseif (strpos($mensaje, 'total') !== false) {
            return 'CALCULO_TOTALES';
        } elseif (strpos($mensaje, 'detalle') !== false) {
            return 'PROCESAMIENTO_DETALLES';
        } else {
            return 'DESCONOCIDO';
        }
    }
    
    /**
     * Limpiar caches relacionados después de rollback
     */
    private function limpiarCachesRollback($ventaId)
    {
        // Implementar limpieza de caches específicos
        cache()->forget("venta_{$ventaId}");
        cache()->forget("detalles_venta_{$ventaId}");
    }
    
    /**
     * Notificar rollback a sistemas externos
     */
    private function notificarRollback($ventaId, $excepcion)
    {
        // Implementar notificaciones si es necesario
        // Por ejemplo, webhooks, emails, etc.
    }
    
    /**
     * Registrar operación en log
     */
    private function logOperacion($descripcion, $datos = [])
    {
        $this->operacionesLog[] = [
            'timestamp' => now(),
            'descripcion' => $descripcion,
            'datos' => $datos
        ];
    }
    
    /**
     * Limpiar savepoints después de commit exitoso
     */
    private function limpiarSavepoints()
    {
        $this->puntosSavepoint = [];
        $this->operacionesLog = [];
    }
}
