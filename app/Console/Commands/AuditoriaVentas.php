<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Articulo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuditoriaVentas extends Command
{
    protected $signature = 'ventas:auditoria {--fix : Intentar corregir automáticamente las inconsistencias encontradas} {--dias=30 : Número de días hacia atrás para auditar} {--articulo= : ID específico de artículo para auditar}';
    
    protected $description = 'Audita las ventas para detectar inconsistencias en stock e inventario';

    private $inconsistencias = [];    private $estadisticas = [
        'ventas_auditadas' => 0,
        'detalles_auditados' => 0,
        'articulos_con_problemas' => 0,
        'stock_inconsistente' => 0,
        'ventas_duplicadas' => 0,
        'stock_negativo' => 0,
        'manipulaciones_detectadas' => 0,
        'errores_integridad' => 0,
        'errores_sistema' => 0,
        'correcciones_aplicadas' => 0
    ];

    public function handle()
    {
        $this->info('🔍 Iniciando auditoría de ventas...');
        $this->info('Fecha: ' . now()->format('Y-m-d H:i:s'));
        
        $dias = $this->option('dias');
        $articuloEspecifico = $this->option('articulo');
        $aplicarCorrecciones = $this->option('fix');
        
        if ($aplicarCorrecciones) {
            $this->warn('⚠️  MODO CORRECCIÓN ACTIVADO - Se intentarán corregir automáticamente las inconsistencias');
        }
        
        $fechaInicio = Carbon::now()->subDays($dias);
        $this->info("Auditando desde: {$fechaInicio->format('Y-m-d')} hasta ahora");
        
        try {
            DB::beginTransaction();
              // 1. Auditar stock vs movimientos de ventas
            $this->auditarStockVsVentas($fechaInicio, $articuloEspecifico, $aplicarCorrecciones);
            
            // 2. Detectar ventas duplicadas
            $this->detectarVentasDuplicadas($fechaInicio, $aplicarCorrecciones);
            
            // 3. Verificar stock negativo
            $this->verificarStockNegativo($articuloEspecifico, $aplicarCorrecciones);
            
            // 4. Auditar integridad de detalles de venta
            $this->auditarDetallesVenta($fechaInicio, $aplicarCorrecciones);
            
            // 5. Verificar servicios y componentes
            $this->auditarServiciosYComponentes($fechaInicio, $aplicarCorrecciones);
            
            // 6. 🆕 AUDITORÍA INTEGRAL: Detectar manipulaciones manuales
            $this->auditarIntegridadStock($fechaInicio, $aplicarCorrecciones);
            
            // 7. 🆕 AUDITORÍA INTEGRAL: Verificar integridad entre tablas
            $this->auditarIntegridadTablas($fechaInicio, $aplicarCorrecciones);
            
            // 8. 🆕 AUDITORÍA INTEGRAL: Detectar errores del sistema
            $this->auditarErroresSistema($fechaInicio, $aplicarCorrecciones);
            
            // Auditoría integral
            $this->auditarIntegridadStock($fechaInicio, $aplicarCorrecciones);
            $this->auditarIntegridadTablas($fechaInicio, $aplicarCorrecciones);
            $this->auditarErroresSistema($fechaInicio, $aplicarCorrecciones);
            
            if ($aplicarCorrecciones) {
                DB::commit();
                $this->info('✅ Correcciones aplicadas y confirmadas');
            } else {
                DB::rollBack();
                $this->info('🔍 Auditoría completada (solo consulta)');
            }
            
            // Mostrar resultados
            $this->mostrarResultados();
            
            // Generar reporte
            $this->generarReporte();
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error durante la auditoría: ' . $e->getMessage());
            Log::error('Error en auditoría de ventas: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function auditarStockVsVentas($fechaInicio, $articuloEspecifico = null, $aplicarCorrecciones = false)
    {
        $this->info('🔍 Auditando stock vs movimientos de ventas...');
        
        $articulosQuery = Articulo::query();
        
        if ($articuloEspecifico) {
            $articulosQuery->where('id', $articuloEspecifico);
        }
        
        $articulos = $articulosQuery->get();
        
        foreach ($articulos as $articulo) {
            $this->estadisticas['detalles_auditados']++;
            
            // Calcular stock teórico basado en movimientos de ventas
            $stockTeoricoCalculado = $this->calcularStockTeorico($articulo, $fechaInicio);
            $stockActual = $articulo->stock;
            
            $diferencia = $stockActual - $stockTeoricoCalculado;
            
            if (abs($diferencia) > 0.01) { // Tolerancia para decimales
                $this->estadisticas['stock_inconsistente']++;
                $this->estadisticas['articulos_con_problemas']++;
                
                $inconsistencia = [
                    'tipo' => 'STOCK_INCONSISTENTE',
                    'articulo_id' => $articulo->id,
                    'articulo_codigo' => $articulo->codigo,
                    'articulo_nombre' => $articulo->nombre,
                    'stock_actual' => $stockActual,
                    'stock_teorico' => $stockTeoricoCalculado,
                    'diferencia' => $diferencia,
                    'severidad' => abs($diferencia) > 10 ? 'ALTA' : (abs($diferencia) > 5 ? 'MEDIA' : 'BAJA')
                ];
                
                $this->inconsistencias[] = $inconsistencia;
                
                $this->warn("⚠️  {$articulo->codigo} - {$articulo->nombre}");
                $this->warn("   Stock actual: {$stockActual}, Stock teórico: {$stockTeoricoCalculado}, Diferencia: {$diferencia}");
                  if ($aplicarCorrecciones && abs($diferencia) <= 50) { // Solo corregir diferencias menores
                    $articulo->update(['stock' => $stockTeoricoCalculado]);
                    $this->estadisticas['correcciones_aplicadas']++;
                    $this->info("✅ Stock corregido para {$articulo->codigo}");
                }
            }
        }
    }    private function calcularStockTeorico($articulo, $fechaInicio)
    {
        // NUEVA LÓGICA: Auditoría integral de stock
        
        // 1. Calcular stock teórico basado en movimientos registrados
        $totalIngresos = \App\Models\DetalleIngreso::where('articulo_id', $articulo->id)
            ->sum('cantidad');
        
        $totalVentas = DetalleVenta::whereHas('venta', function($query) {
                $query->where('estado', true); // Solo ventas activas
            })
            ->where('articulo_id', $articulo->id)
            ->sum('cantidad');
        
        // 2. Usar stock_inicial si existe, sino calcularlo implícitamente
        $stockInicial = $articulo->stock_inicial ?? 0;
        
        // Si no hay stock_inicial definido, lo calculamos implícitamente
        if ($stockInicial == 0 && ($totalIngresos > 0 || $totalVentas > 0)) {
            // Stock inicial implícito = Stock actual + Ventas - Ingresos
            // (Lo que debería haber tenido inicialmente para llegar al stock actual)
            $stockInicial = $articulo->stock + $totalVentas - $totalIngresos;
            
            // Si el stock inicial calculado es negativo, es sospechoso
            if ($stockInicial < 0) {
                // Registrar como posible manipulación
                $this->inconsistencias[] = [
                    'tipo' => 'POSIBLE_MANIPULACION',
                    'articulo_id' => $articulo->id,
                    'articulo_codigo' => $articulo->codigo,
                    'articulo_nombre' => $articulo->nombre,
                    'stock_actual' => $articulo->stock,
                    'stock_inicial_calculado' => $stockInicial,
                    'total_ingresos' => $totalIngresos,
                    'total_ventas' => $totalVentas,
                    'mensaje' => 'Stock inicial calculado es negativo, posible manipulación manual',
                    'severidad' => 'ALTA'
                ];
            }
        }
        
        // Si es un servicio, el stock no se consume físicamente
        if ($articulo->tipo === 'servicio') {
            return $articulo->stock;
        }
        
        // 3. Stock teórico = Stock inicial + Ingresos - Ventas
        return $stockInicial + $totalIngresos - $totalVentas;
    }

    private function calcularComponentesConsumidos($servicioId, $fechaInicio)
    {
        // Obtener componentes del servicio
        $componentes = DB::table('servicio_articulo')
            ->where('servicio_id', $servicioId)
            ->get();
        
        $consumoTotal = 0;
        
        foreach ($componentes as $componente) {
            $ventasServicio = DetalleVenta::whereHas('venta', function($query) use ($fechaInicio) {
                    $query->where('fecha', '>=', $fechaInicio)
                          ->where('estado', true);
                })
                ->where('articulo_id', $servicioId)
                ->sum('cantidad');
            
            $consumoComponente = $ventasServicio * $componente->cantidad;
            $consumoTotal += $consumoComponente;
        }
        
        return $consumoTotal;
    }

    private function detectarVentasDuplicadas($fechaInicio, $aplicarCorrecciones = false)
    {
        $this->info('🔍 Detectando ventas duplicadas...');
        
        // Buscar ventas sospechosas (mismo cliente, mismo día, montos similares)
        $ventasSospechosas = DB::select("
            SELECT v1.id as venta1_id, v2.id as venta2_id, 
                   v1.cliente_id, v1.fecha, v1.numero_factura as factura1, v2.numero_factura as factura2,
                   COUNT(*) as coincidencias
            FROM ventas v1
            JOIN ventas v2 ON v1.cliente_id = v2.cliente_id 
                           AND v1.fecha = v2.fecha 
                           AND v1.id < v2.id
                           AND v1.estado = 1 AND v2.estado = 1
            WHERE v1.fecha >= ?
            GROUP BY v1.id, v2.id, v1.cliente_id, v1.fecha, v1.numero_factura, v2.numero_factura
            HAVING coincidencias > 0
        ", [$fechaInicio]);
        
        foreach ($ventasSospechosas as $sospechosa) {
            $this->estadisticas['ventas_duplicadas']++;
            
            // Verificar si tienen detalles similares
            $detallesSimilares = $this->compararDetallesVenta($sospechosa->venta1_id, $sospechosa->venta2_id);
            
            if ($detallesSimilares > 0) {
                $inconsistencia = [
                    'tipo' => 'VENTA_DUPLICADA',
                    'venta1_id' => $sospechosa->venta1_id,
                    'venta2_id' => $sospechosa->venta2_id,
                    'cliente_id' => $sospechosa->cliente_id,
                    'fecha' => $sospechosa->fecha,
                    'detalles_similares' => $detallesSimilares,
                    'severidad' => $detallesSimilares > 2 ? 'ALTA' : 'MEDIA'
                ];
                
                $this->inconsistencias[] = $inconsistencia;
                
                $this->warn("⚠️  Posible venta duplicada:");
                $this->warn("   Venta {$sospechosa->venta1_id} vs Venta {$sospechosa->venta2_id}");
                $this->warn("   Cliente: {$sospechosa->cliente_id}, Fecha: {$sospechosa->fecha}");
                $this->warn("   Detalles similares: {$detallesSimilares}");
            }
        }
    }

    private function compararDetallesVenta($venta1Id, $venta2Id)
    {
        return DB::select("
            SELECT COUNT(*) as similares
            FROM detalle_ventas d1
            JOIN detalle_ventas d2 ON d1.articulo_id = d2.articulo_id 
                                   AND ABS(d1.cantidad - d2.cantidad) < 0.1
            WHERE d1.venta_id = ? AND d2.venta_id = ?
        ", [$venta1Id, $venta2Id])[0]->similares ?? 0;
    }

    private function verificarStockNegativo($articuloEspecifico = null, $aplicarCorrecciones = false)
    {
        $this->info('🔍 Verificando stock negativo...');
        
        $query = Articulo::where('stock', '<', 0);
        
        if ($articuloEspecifico) {
            $query->where('id', $articuloEspecifico);
        }
        
        $articulosNegativos = $query->get();
        
        foreach ($articulosNegativos as $articulo) {
            $this->estadisticas['stock_negativo']++;
            $this->estadisticas['articulos_con_problemas']++;
            
            $inconsistencia = [
                'tipo' => 'STOCK_NEGATIVO',
                'articulo_id' => $articulo->id,
                'articulo_codigo' => $articulo->codigo,
                'articulo_nombre' => $articulo->nombre,
                'stock_actual' => $articulo->stock,
                'severidad' => $articulo->stock < -10 ? 'ALTA' : 'MEDIA'
            ];
            
            $this->inconsistencias[] = $inconsistencia;
            
            $this->error("❌ Stock negativo: {$articulo->codigo} - {$articulo->nombre} (Stock: {$articulo->stock})");
              if ($aplicarCorrecciones) {
                $articulo->update(['stock' => 0]); // Resetear a 0 como medida de seguridad
                $this->estadisticas['correcciones_aplicadas']++;
                $this->info("✅ Stock reseteado a 0 para {$articulo->codigo}");
            }
        }
    }

    private function auditarDetallesVenta($fechaInicio, $aplicarCorrecciones = false)
    {
        $this->info('🔍 Auditando integridad de detalles de venta...');
        
        // Buscar detalles de venta sin artículo válido
        $detallesSinArticulo = DetalleVenta::whereHas('venta', function($query) use ($fechaInicio) {
                $query->where('fecha', '>=', $fechaInicio);
            })
            ->whereDoesntHave('articulo')
            ->get();
        
        foreach ($detallesSinArticulo as $detalle) {
            $inconsistencia = [
                'tipo' => 'DETALLE_SIN_ARTICULO',
                'detalle_id' => $detalle->id,
                'venta_id' => $detalle->venta_id,
                'articulo_id' => $detalle->articulo_id,
                'severidad' => 'ALTA'
            ];
            
            $this->inconsistencias[] = $inconsistencia;
            $this->error("❌ Detalle {$detalle->id} sin artículo válido (Artículo ID: {$detalle->articulo_id})");
        }
        
        // Buscar detalles con cantidades sospechosas
        $detallesSospechosos = DetalleVenta::whereHas('venta', function($query) use ($fechaInicio) {
                $query->where('fecha', '>=', $fechaInicio);
            })
            ->where(function($query) {
                $query->where('cantidad', '<=', 0)
                      ->orWhere('cantidad', '>', 1000); // Cantidad muy alta
            })
            ->get();
        
        foreach ($detallesSospechosos as $detalle) {
            $inconsistencia = [
                'tipo' => 'CANTIDAD_SOSPECHOSA',
                'detalle_id' => $detalle->id,
                'venta_id' => $detalle->venta_id,
                'articulo_id' => $detalle->articulo_id,
                'cantidad' => $detalle->cantidad,
                'severidad' => $detalle->cantidad <= 0 ? 'ALTA' : 'MEDIA'
            ];
            
            $this->inconsistencias[] = $inconsistencia;
            $this->warn("⚠️  Cantidad sospechosa: Detalle {$detalle->id}, Cantidad: {$detalle->cantidad}");
        }
    }

    private function auditarServiciosYComponentes($fechaInicio, $aplicarCorrecciones = false)
    {
        $this->info('🔍 Auditando servicios y sus componentes...');
        
        $servicios = Articulo::where('tipo', 'servicio')->get();
        
        foreach ($servicios as $servicio) {
            // Verificar que el servicio tenga componentes definidos
            $componentes = DB::table('servicio_articulo')
                ->where('servicio_id', $servicio->id)
                ->get();
            
            if ($componentes->count() === 0) {
                $inconsistencia = [
                    'tipo' => 'SERVICIO_SIN_COMPONENTES',
                    'articulo_id' => $servicio->id,
                    'articulo_codigo' => $servicio->codigo,
                    'articulo_nombre' => $servicio->nombre,
                    'severidad' => 'MEDIA'
                ];
                
                $this->inconsistencias[] = $inconsistencia;
                $this->warn("⚠️  Servicio sin componentes: {$servicio->codigo} - {$servicio->nombre}");
            }
            
            // Verificar que los componentes existan
            foreach ($componentes as $componente) {
                $articuloComponente = Articulo::find($componente->articulo_id);
                if (!$articuloComponente) {
                    $inconsistencia = [
                        'tipo' => 'COMPONENTE_INEXISTENTE',
                        'servicio_id' => $servicio->id,
                        'componente_id' => $componente->articulo_id,
                        'severidad' => 'ALTA'
                    ];
                    
                    $this->inconsistencias[] = $inconsistencia;
                    $this->error("❌ Componente inexistente {$componente->articulo_id} en servicio {$servicio->codigo}");
                }
            }
        }
    }

    /**
     * AUDITORÍA INTEGRAL: Detecta manipulaciones manuales y errores del sistema
     */
    private function auditarIntegridadStock($fechaInicio, $aplicarCorrecciones = false)
    {
        $this->info('🔍 Auditando integridad de stock (manipulaciones manuales)...');
        
        $articulos = Articulo::where('tipo', 'articulo')->get();
        
        foreach ($articulos as $articulo) {
            // Calcular lo que DEBERÍA ser el stock basado en movimientos
            $totalIngresos = \App\Models\DetalleIngreso::where('articulo_id', $articulo->id)->sum('cantidad');
            $totalVentas = DetalleVenta::whereHas('venta', function($query) {
                    $query->where('estado', true);
                })
                ->where('articulo_id', $articulo->id)
                ->sum('cantidad');
            
            $stockInicial = $articulo->stock_inicial ?? 0;
            $stockEsperado = $stockInicial + $totalIngresos - $totalVentas;
            $stockActual = $articulo->stock;
            
            $diferencia = $stockActual - $stockEsperado;
            
            // Si hay diferencia significativa, puede ser manipulación manual
            if (abs($diferencia) > 0.01) {
                // Verificar si hay logs de ajustes manuales que justifiquen la diferencia
                $ajustesRegistrados = $this->buscarAjustesManualesEnLogs($articulo->id);
                  if (empty($ajustesRegistrados) && abs($diferencia) > 1) {
                    $this->inconsistencias[] = [
                        'tipo' => 'MANIPULACION_NO_REGISTRADA',
                        'articulo_id' => $articulo->id,
                        'articulo_codigo' => $articulo->codigo,
                        'articulo_nombre' => $articulo->nombre,
                        'stock_actual' => $stockActual,
                        'stock_esperado' => $stockEsperado,
                        'diferencia' => $diferencia,
                        'total_ingresos' => $totalIngresos,
                        'total_ventas' => $totalVentas,
                        'mensaje' => 'Diferencia de stock sin registro de ajuste manual en logs',
                        'severidad' => abs($diferencia) > 10 ? 'ALTA' : 'MEDIA'
                    ];
                    
                    $this->estadisticas['articulos_con_problemas']++;
                    $this->estadisticas['manipulaciones_detectadas']++;
                    $this->warn("⚠️  {$articulo->codigo}: Posible manipulación no registrada (Dif: {$diferencia})");
                }
            }
        }
    }
    
    /**
     * AUDITORÍA: Verificar integridad entre tablas
     */
    private function auditarIntegridadTablas($fechaInicio, $aplicarCorrecciones = false)
    {
        $this->info('🔍 Auditando integridad entre tablas...');
          // 1. Verificar ventas huérfanas (sin cliente)
        $ventasHuerfanas = Venta::whereNull('cliente_id')->orWhereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('clientes')
                  ->whereColumn('clientes.id', 'ventas.cliente_id');
        })->count();
          if ($ventasHuerfanas > 0) {
            $this->inconsistencias[] = [
                'tipo' => 'INTEGRIDAD_REFERENCIAL',
                'tabla_origen' => 'ventas',
                'tabla_destino' => 'clientes',
                'cantidad_registros' => $ventasHuerfanas,
                'mensaje' => "Ventas sin cliente válido encontradas",
                'severidad' => 'MEDIA'
            ];
            $this->estadisticas['errores_integridad']++;
            $this->warn("⚠️  {$ventasHuerfanas} ventas sin cliente válido");
        }
        
        // 2. Verificar detalles de venta huérfanos (sin artículo)
        $detallesHuerfanos = DetalleVenta::whereNull('articulo_id')->orWhereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('articulos')
                  ->whereColumn('articulos.id', 'detalle_ventas.articulo_id');
        })->count();
        
        if ($detallesHuerfanos > 0) {
            $this->inconsistencias[] = [
                'tipo' => 'INTEGRIDAD_REFERENCIAL',
                'tabla_origen' => 'detalle_ventas',
                'tabla_destino' => 'articulos',
                'cantidad_registros' => $detallesHuerfanos,
                'mensaje' => "Detalles de venta sin artículo válido encontrados",
                'severidad' => 'ALTA'
            ];
            $this->warn("⚠️  {$detallesHuerfanos} detalles de venta sin artículo válido");
        }
        
        // 3. Verificar inconsistencias en precios (precio_venta = 0 o negativo)
        $preciosInconsistentes = DetalleVenta::where('precio_venta', '<=', 0)->count();
        
        if ($preciosInconsistentes > 0) {
            $this->inconsistencias[] = [
                'tipo' => 'DATOS_INCONSISTENTES',
                'tabla' => 'detalle_ventas',
                'campo' => 'precio_venta',
                'cantidad_registros' => $preciosInconsistentes,
                'mensaje' => "Detalles de venta con precio_venta <= 0",
                'severidad' => 'MEDIA'
            ];
            $this->warn("⚠️  {$preciosInconsistentes} detalles con precio_venta inconsistente");
        }        // 4. Verificar totales de venta vs suma de detalles
        $ventasConTotalesIncorrectos = DB::select("
            SELECT v.id, 
                   COALESCE(SUM(dv.sub_total), 0) as suma_detalles,
                   COUNT(dv.id) as cantidad_detalles
            FROM ventas v
            LEFT JOIN detalle_ventas dv ON v.id = dv.venta_id
            WHERE v.estado = 1
            GROUP BY v.id
            HAVING COUNT(dv.id) = 0
        ");
        
        foreach ($ventasConTotalesIncorrectos as $venta) {
            $this->inconsistencias[] = [
                'tipo' => 'VENTA_SIN_DETALLES',
                'venta_id' => $venta->id,
                'suma_detalles' => $venta->suma_detalles,
                'cantidad_detalles' => $venta->cantidad_detalles,
                'mensaje' => "Venta activa sin detalles",
                'severidad' => 'ALTA'
            ];
            $this->estadisticas['errores_integridad']++;
        }
        
        if (count($ventasConTotalesIncorrectos) > 0) {
            $this->warn("⚠️  " . count($ventasConTotalesIncorrectos) . " ventas sin detalles");
        }
    }
    
    /**
     * AUDITORÍA: Detectar errores del sistema en actualizaciones de stock
     */
    private function auditarErroresSistema($fechaInicio, $aplicarCorrecciones = false)
    {
        $this->info('🔍 Auditando errores del sistema en actualizaciones de stock...');
        
        // Buscar ventas que NO actualizaron el stock correctamente
        $ventasRecientes = Venta::where('created_at', '>=', $fechaInicio)
                                ->where('estado', true)
                                ->with('detalles.articulo')
                                ->get();
        
        foreach ($ventasRecientes as $venta) {
            foreach ($venta->detalles as $detalle) {
                if ($detalle->articulo && $detalle->articulo->tipo === 'articulo') {
                    // Simular qué debería ser el stock sin esta venta
                    $stockSimulado = $detalle->articulo->stock + $detalle->cantidad;
                    
                    // Obtener el stock que tenía antes de esta venta (aproximación)
                    $ventasPosteriores = DetalleVenta::whereHas('venta', function($query) use ($venta) {
                            $query->where('created_at', '>', $venta->created_at)
                                  ->where('estado', true);
                        })
                        ->where('articulo_id', $detalle->articulo_id)
                        ->sum('cantidad');
                    
                    $stockAntesVenta = $detalle->articulo->stock + $ventasPosteriores;
                    $stockDespuesVenta = $stockAntesVenta - $detalle->cantidad;
                    
                    // Si hay una gran discrepancia, puede ser error del sistema
                    if (abs($stockDespuesVenta - $detalle->articulo->stock) > 0.01) {
                        $this->inconsistencias[] = [
                            'tipo' => 'ERROR_ACTUALIZACION_STOCK',
                            'venta_id' => $venta->id,
                            'detalle_id' => $detalle->id,
                            'articulo_id' => $detalle->articulo_id,
                            'articulo_codigo' => $detalle->articulo->codigo,
                            'cantidad_vendida' => $detalle->cantidad,
                            'stock_actual' => $detalle->articulo->stock,
                            'stock_esperado' => $stockDespuesVenta,
                            'mensaje' => 'Posible error en actualización automática de stock',
                            'severidad' => 'ALTA'
                        ];
                    }
                }
            }
        }
    }
    
    /**
     * Buscar ajustes manuales en los logs del sistema
     */
    private function buscarAjustesManualesEnLogs($articuloId)
    {
        // Esta función buscaría en los logs del sistema ajustes manuales
        // Por simplicidad, retornamos array vacío
        // En implementación real, buscaría en storage/logs/ o tabla de logs
        return [];
    }

    private function mostrarResultados()
    {
        $this->info('');
        $this->info('📊 RESULTADOS DE LA AUDITORÍA');
        $this->info('================================');
        
        foreach ($this->estadisticas as $key => $value) {
            $label = str_replace('_', ' ', strtoupper($key));
            $this->info("{$label}: {$value}");
        }
        
        $this->info('');
        $this->info('🔍 RESUMEN DE INCONSISTENCIAS');
        $this->info('=============================');
        
        $tiposInconsistencias = [];
        foreach ($this->inconsistencias as $inconsistencia) {
            $tipo = $inconsistencia['tipo'];
            if (!isset($tiposInconsistencias[$tipo])) {
                $tiposInconsistencias[$tipo] = ['total' => 0, 'alta' => 0, 'media' => 0, 'baja' => 0];
            }
            $tiposInconsistencias[$tipo]['total']++;
            $tiposInconsistencias[$tipo][strtolower($inconsistencia['severidad'])]++;
        }
        
        foreach ($tiposInconsistencias as $tipo => $datos) {
            $this->info("{$tipo}: {$datos['total']} (Alta: {$datos['alta']}, Media: {$datos['media']}, Baja: {$datos['baja']})");
        }
    }

    private function generarReporte()
    {
        $fecha = now()->format('Y-m-d_H-i-s');
        $nombreArchivo = "auditoria_ventas_{$fecha}.json";
        $rutaCompleta = storage_path("app/auditorias/{$nombreArchivo}");
        
        // Crear directorio si no existe
        if (!file_exists(dirname($rutaCompleta))) {
            mkdir(dirname($rutaCompleta), 0755, true);
        }
        
        $reporte = [
            'fecha_auditoria' => now()->toDateTimeString(),
            'parametros' => [
                'dias_auditados' => $this->option('dias'),
                'articulo_especifico' => $this->option('articulo'),
                'correcciones_aplicadas' => $this->option('fix')
            ],
            'estadisticas' => $this->estadisticas,
            'inconsistencias' => $this->inconsistencias
        ];
        
        file_put_contents($rutaCompleta, json_encode($reporte, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        
        $this->info('');
        $this->info("📄 Reporte guardado en: {$rutaCompleta}");
        
        // Registrar en el log del sistema
        Log::info('Auditoría de ventas completada', [
            'estadisticas' => $this->estadisticas,
            'total_inconsistencias' => count($this->inconsistencias),
            'archivo_reporte' => $nombreArchivo
        ]);
    }
}
