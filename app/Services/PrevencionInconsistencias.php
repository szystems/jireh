<?php

namespace App\Services;

use App\Models\Articulo;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PrevencionInconsistencias
{
    /**
     * OPCIÓN 1: SISTEMA DE VALIDACIÓN PREVENTIVA EN TIEMPO REAL
     * 
     * Valida todas las operaciones críticas ANTES de ejecutarlas
     * para prevenir inconsistencias desde el origen.
     */

    /**
     * Validación integral antes de crear/actualizar una venta
     */
    public function validarAntesDeVenta($datosVenta, $detalles)
    {
        $errores = [];
        
        try {
            // 1. Validar stock disponible con bloqueo optimista
            foreach ($detalles as $detalle) {
                $validacionStock = $this->validarStockConBloqueo($detalle['articulo_id'], $detalle['cantidad']);
                if (!$validacionStock['valido']) {
                    $errores[] = $validacionStock['mensaje'];
                }
            }
            
            // 2. Validar integridad de precios
            $validacionPrecios = $this->validarConsistenciaPrecios($detalles);
            if (!$validacionPrecios['valido']) {
                $errores[] = $validacionPrecios['mensaje'];
            }
            
            // 3. Validar límites de negocio
            $validacionLimites = $this->validarLimitesNegocio($datosVenta, $detalles);
            if (!$validacionLimites['valido']) {
                $errores[] = $validacionLimites['mensaje'];
            }
            
            // 4. Verificar integridad referencial
            $validacionReferencias = $this->validarIntegridadReferencial($datosVenta);
            if (!$validacionReferencias['valido']) {
                $errores[] = $validacionReferencias['mensaje'];
            }
            
        } catch (\Exception $e) {
            Log::error('Error en validación preventiva: ' . $e->getMessage());
            $errores[] = 'Error interno de validación';
        }
        
        return [
            'valido' => empty($errores),
            'errores' => $errores,
            'timestamp' => now(),
            'tipo_validacion' => 'PREVENTIVA_TIEMPO_REAL'
        ];
    }
    
    /**
     * Validación de stock con bloqueo para evitar condiciones de carrera
     */
    private function validarStockConBloqueo($articuloId, $cantidad)
    {
        $lockKey = "validacion_stock_{$articuloId}";
        
        return Cache::lock($lockKey, 10)->block(5, function() use ($articuloId, $cantidad) {
            $articulo = Articulo::lockForUpdate()->find($articuloId);
            
            if (!$articulo) {
                return [
                    'valido' => false,
                    'mensaje' => "Artículo ID {$articuloId} no encontrado"
                ];
            }
            
            // Calcular stock real considerando ventas pendientes
            $stockReservado = $this->calcularStockReservadoTiempoReal($articuloId);
            $stockDisponible = $articulo->stock - $stockReservado;
            
            if ($stockDisponible < $cantidad) {
                return [
                    'valido' => false,
                    'mensaje' => "Stock insuficiente para {$articulo->codigo}. Disponible: {$stockDisponible}, Requerido: {$cantidad}"
                ];
            }
            
            // Marcar stock como reservado temporalmente
            $this->reservarStockTemporal($articuloId, $cantidad);
            
            return [
                'valido' => true,
                'mensaje' => 'Stock validado y reservado',
                'stock_disponible' => $stockDisponible
            ];
        });
    }
    
    /**
     * Validar consistencia de precios
     */
    private function validarConsistenciaPrecios($detalles)
    {
        foreach ($detalles as $detalle) {
            $articulo = Articulo::find($detalle['articulo_id']);
            
            if (!$articulo) {
                return [
                    'valido' => false,
                    'mensaje' => "Artículo no encontrado para validación de precio"
                ];
            }
            
            // Validar que el precio no varíe más del 50% del precio base
            $precioBase = $articulo->precio_venta;
            $precioVenta = $detalle['precio_unitario'];
            $variacion = abs(($precioVenta - $precioBase) / $precioBase * 100);
            
            if ($variacion > 50) {
                return [
                    'valido' => false,
                    'mensaje' => "Variación de precio excesiva para {$articulo->codigo}: {$variacion}%"
                ];
            }
        }
        
        return ['valido' => true, 'mensaje' => 'Precios validados'];
    }
    
    /**
     * Validar límites de negocio
     */
    private function validarLimitesNegocio($datosVenta, $detalles)
    {
        $totalVenta = collect($detalles)->sum(function($detalle) {
            return $detalle['cantidad'] * $detalle['precio_unitario'];
        });
        
        // Validar límite máximo por venta
        if ($totalVenta > 50000) {
            return [
                'valido' => false,
                'mensaje' => "El total de la venta excede el límite máximo permitido"
            ];
        }
        
        // Validar cantidad máxima por artículo
        foreach ($detalles as $detalle) {
            if ($detalle['cantidad'] > 100) {
                return [
                    'valido' => false,
                    'mensaje' => "Cantidad excesiva para un artículo: {$detalle['cantidad']}"
                ];
            }
        }
        
        return ['valido' => true, 'mensaje' => 'Límites de negocio validados'];
    }
    
    /**
     * Validar integridad referencial
     */
    private function validarIntegridadReferencial($datosVenta)
    {
        // Validar que el cliente existe
        if (isset($datosVenta['cliente_id'])) {
            $clienteExiste = DB::table('clientes')->where('id', $datosVenta['cliente_id'])->exists();
            if (!$clienteExiste) {
                return [
                    'valido' => false,
                    'mensaje' => "Cliente especificado no existe"
                ];
            }
        }
        
        // Validar que el usuario vendedor existe
        if (isset($datosVenta['user_id'])) {
            $userExiste = DB::table('users')->where('id', $datosVenta['user_id'])->exists();
            if (!$userExiste) {
                return [
                    'valido' => false,
                    'mensaje' => "Usuario vendedor no existe"
                ];
            }
        }
        
        return ['valido' => true, 'mensaje' => 'Integridad referencial validada'];
    }
    
    /**
     * Calcular stock reservado en tiempo real
     */
    private function calcularStockReservadoTiempoReal($articuloId)
    {
        // Buscar reservas temporales activas (últimos 10 minutos)
        $reservas = Cache::get("reservas_temporales_{$articuloId}", []);
        $ahora = now();
        
        $stockReservado = 0;
        foreach ($reservas as $reserva) {
            // Solo considerar reservas de los últimos 10 minutos
            if ($ahora->diffInMinutes($reserva['timestamp']) <= 10) {
                $stockReservado += $reserva['cantidad'];
            }
        }
        
        return $stockReservado;
    }
    
    /**
     * Reservar stock temporalmente
     */
    private function reservarStockTemporal($articuloId, $cantidad)
    {
        $reservas = Cache::get("reservas_temporales_{$articuloId}", []);
        
        $reservas[] = [
            'cantidad' => $cantidad,
            'timestamp' => now(),
            'session_id' => session()->getId()
        ];
        
        // Guardar por 15 minutos
        Cache::put("reservas_temporales_{$articuloId}", $reservas, 900);
    }
    
    /**
     * Liberar reserva temporal de stock
     */
    public function liberarReservaTemporal($articuloId, $cantidad)
    {
        $reservas = Cache::get("reservas_temporales_{$articuloId}", []);
        $sessionId = session()->getId();
        
        // Remover la primera reserva que coincida con la sesión
        foreach ($reservas as $key => $reserva) {
            if ($reserva['session_id'] === $sessionId && $reserva['cantidad'] == $cantidad) {
                unset($reservas[$key]);
                break;
            }
        }
        
        Cache::put("reservas_temporales_{$articuloId}", array_values($reservas), 900);
    }
}
