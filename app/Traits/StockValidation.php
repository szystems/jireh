<?php

namespace App\Traits;

use App\Models\Articulo;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

trait StockValidation
{
    /**
     * Valida si hay suficiente stock antes de realizar una venta
     * Incluye protección contra concurrencia
     */
    public function validarStockDisponible($articuloId, $cantidadSolicitada, $ventaId = null)
    {
        $lockKey = "stock_validation_{$articuloId}";
        
        return Cache::lock($lockKey, 10)->block(5, function() use ($articuloId, $cantidadSolicitada, $ventaId) {
            $articulo = Articulo::lockForUpdate()->find($articuloId);
            
            if (!$articulo) {
                return [
                    'valido' => false,
                    'mensaje' => "Artículo ID {$articuloId} no encontrado",
                    'stock_actual' => 0,
                    'stock_requerido' => $cantidadSolicitada
                ];
            }
            
            // Si es un servicio, verificar componentes
            if ($articulo->tipo === 'servicio') {
                return $this->validarStockComponentesServicio($articulo, $cantidadSolicitada, $ventaId);
            }
            
            // Para artículos normales
            $stockDisponible = $articulo->stock;
            
            // Verificar si hay ventas concurrentes que puedan afectar el stock
            $stockReservado = $this->calcularStockReservado($articuloId, $ventaId);
            $stockReal = $stockDisponible - $stockReservado;
            
            if ($stockReal < $cantidadSolicitada) {
                Log::warning("Stock insuficiente para artículo {$articulo->codigo}", [
                    'stock_disponible' => $stockDisponible,
                    'stock_reservado' => $stockReservado,
                    'stock_real' => $stockReal,
                    'cantidad_solicitada' => $cantidadSolicitada,
                    'venta_id' => $ventaId
                ]);
                
                return [
                    'valido' => false,
                    'mensaje' => "Stock insuficiente para {$articulo->codigo} - {$articulo->nombre}. Disponible: {$stockReal}, Solicitado: {$cantidadSolicitada}",
                    'stock_actual' => $stockReal,
                    'stock_requerido' => $cantidadSolicitada,
                    'articulo' => $articulo
                ];
            }
            
            return [
                'valido' => true,
                'mensaje' => "Stock suficiente",
                'stock_actual' => $stockReal,
                'stock_requerido' => $cantidadSolicitada,
                'articulo' => $articulo
            ];
        });
    }
    
    /**
     * Valida stock de componentes para servicios
     */
    private function validarStockComponentesServicio($servicio, $cantidadServicio, $ventaId = null)
    {
        $componentes = DB::table('servicio_articulo')
            ->where('servicio_id', $servicio->id)
            ->get();
        
        if ($componentes->isEmpty()) {
            Log::info("Servicio {$servicio->codigo} no tiene componentes definidos - permitiendo como servicio puro");
            return [
                'valido' => true,
                'mensaje' => "Servicio puro sin componentes - válido",
                'stock_actual' => 999999, // Stock "infinito" para servicios puros
                'stock_requerido' => $cantidadServicio
            ];
        }
        
        $componentesInsuficientes = [];
        
        foreach ($componentes as $componente) {
            $cantidadRequerida = $componente->cantidad * $cantidadServicio;
            $validacion = $this->validarStockDisponible($componente->articulo_id, $cantidadRequerida, $ventaId);
            
            if (!$validacion['valido']) {
                $articuloComponente = Articulo::find($componente->articulo_id);
                $componentesInsuficientes[] = [
                    'articulo' => $articuloComponente,
                    'requerido' => $cantidadRequerida,
                    'disponible' => $validacion['stock_actual']
                ];
            }
        }
        
        if (!empty($componentesInsuficientes)) {
            $mensajes = [];
            foreach ($componentesInsuficientes as $comp) {
                $mensajes[] = "{$comp['articulo']->codigo}: necesario {$comp['requerido']}, disponible {$comp['disponible']}";
            }
            
            return [
                'valido' => false,
                'mensaje' => "Stock insuficiente en componentes del servicio {$servicio->codigo}: " . implode('; ', $mensajes),
                'stock_actual' => 0,
                'stock_requerido' => $cantidadServicio,
                'componentes_insuficientes' => $componentesInsuficientes
            ];
        }
        
        return [
            'valido' => true,
            'mensaje' => "Stock suficiente para servicio y componentes",
            'stock_actual' => $cantidadServicio,
            'stock_requerido' => $cantidadServicio,
            'articulo' => $servicio
        ];
    }
    
    /**
     * Calcula stock que está "reservado" por ventas concurrentes
     * (ventas que están siendo procesadas pero aún no han actualizado el stock)
     */
    private function calcularStockReservado($articuloId, $ventaIdExcluir = null)
    {
        // Por ahora, retornamos 0. En una implementación más avanzada,
        // podríamos mantener una tabla de "reservas temporales" o usar Redis
        return 0;
    }
    
    /**
     * Actualiza el stock de forma segura con verificaciones adicionales
     */
    public function actualizarStockSeguro($articuloId, $cantidad, $operacion = 'restar', $ventaId = null, $descripcion = '')
    {
        $lockKey = "stock_update_{$articuloId}";
        
        return Cache::lock($lockKey, 10)->block(5, function() use ($articuloId, $cantidad, $operacion, $ventaId, $descripcion) {
            $articulo = Articulo::lockForUpdate()->find($articuloId);
            
            if (!$articulo) {
                throw new \Exception("Artículo ID {$articuloId} no encontrado para actualización de stock");
            }
            
            $stockAnterior = $articulo->stock;
            
            if ($operacion === 'restar') {
                $nuevoStock = $stockAnterior - $cantidad;
                
                // Verificación adicional antes de confirmar stock negativo
                if ($nuevoStock < 0 && $articulo->tipo !== 'servicio') {
                    Log::error("Intento de dejar stock negativo", [
                        'articulo_id' => $articuloId,
                        'codigo' => $articulo->codigo,
                        'stock_anterior' => $stockAnterior,
                        'cantidad_restar' => $cantidad,
                        'stock_resultante' => $nuevoStock,
                        'venta_id' => $ventaId,
                        'descripcion' => $descripcion
                    ]);
                    
                    throw new \Exception("La operación dejaría stock negativo para {$articulo->codigo}. Stock actual: {$stockAnterior}, Cantidad a restar: {$cantidad}");
                }
            } else {
                $nuevoStock = $stockAnterior + $cantidad;
            }
            
            $articulo->update(['stock' => $nuevoStock]);
            
            // Log de la operación para auditoría
            Log::info("Stock actualizado", [
                'articulo_id' => $articuloId,
                'codigo' => $articulo->codigo,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $nuevoStock,
                'cantidad' => $cantidad,
                'operacion' => $operacion,
                'venta_id' => $ventaId,
                'descripcion' => $descripcion
            ]);
            
            // Si es un servicio, actualizar componentes
            if ($articulo->tipo === 'servicio' && $operacion === 'restar') {
                $this->actualizarStockComponentesServicio($articulo, $cantidad, $ventaId, $descripcion);
            }
            
            return [
                'exitoso' => true,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $nuevoStock,
                'articulo' => $articulo
            ];
        });
    }
    
    /**
     * Actualiza stock de componentes de un servicio
     */
    private function actualizarStockComponentesServicio($servicio, $cantidadServicio, $ventaId = null, $descripcion = '')
    {
        $componentes = DB::table('servicio_articulo')
            ->where('servicio_id', $servicio->id)
            ->get();
        
        foreach ($componentes as $componente) {
            $cantidadComponente = $componente->cantidad * $cantidadServicio;
            
            $this->actualizarStockSeguro(
                $componente->articulo_id,
                $cantidadComponente,
                'restar',
                $ventaId,
                "Componente de servicio {$servicio->codigo} - {$descripcion}"
            );
        }
    }
    
    /**
     * Verifica la consistencia del stock para un artículo específico
     */
    public function verificarConsistenciaStock($articuloId)
    {
        $articulo = Articulo::find($articuloId);
        
        if (!$articulo) {
            return [
                'consistente' => false,
                'mensaje' => "Artículo no encontrado"
            ];
        }
        
        // Calcular stock teórico basado en movimientos
        $stockTeorico = $this->calcularStockTeoricoPorMovimientos($articuloId);
        $stockActual = $articulo->stock;
        
        $diferencia = abs($stockActual - $stockTeorico);
        $tolerancia = 0.01; // Tolerancia para decimales
        
        return [
            'consistente' => $diferencia <= $tolerancia,
            'stock_actual' => $stockActual,
            'stock_teorico' => $stockTeorico,
            'diferencia' => $stockActual - $stockTeorico,
            'articulo' => $articulo
        ];
    }    /**
     * Calcula el stock teórico basado en todos los movimientos registrados
     */
    private function calcularStockTeoricoPorMovimientos($articuloId)
    {
        // Calcular ENTRADAS: Sumar todos los ingresos de este artículo
        $totalIngresos = \App\Models\DetalleIngreso::where('articulo_id', $articuloId)
            ->sum('cantidad');
        
        // Calcular SALIDAS: Sumar todas las ventas activas de este artículo
        $totalVentas = \App\Models\DetalleVenta::whereHas('venta', function($query) {
                $query->where('estado', true); // Solo ventas activas
            })
            ->where('articulo_id', $articuloId)
            ->sum('cantidad');
        
        // Stock teórico = Ingresos - Ventas
        return $totalIngresos - $totalVentas;
    }
    
    /**
     * Genera un reporte de stock en tiempo real
     */
    public function generarReporteStockTiempoReal()
    {
        $articulos = Articulo::all();
        $reporte = [];
        
        foreach ($articulos as $articulo) {
            $consistencia = $this->verificarConsistenciaStock($articulo->id);
            
            $reporte[] = [
                'articulo_id' => $articulo->id,
                'codigo' => $articulo->codigo,
                'nombre' => $articulo->nombre,
                'tipo' => $articulo->tipo,
                'stock_actual' => $articulo->stock,
                'stock_teorico' => $consistencia['stock_teorico'],
                'diferencia' => $consistencia['diferencia'],
                'consistente' => $consistencia['consistente'],
                'alerta' => $articulo->stock < 0 ? 'STOCK_NEGATIVO' : ($articulo->stock < 10 ? 'STOCK_BAJO' : null)
            ];
        }
        
        return $reporte;
    }
}
