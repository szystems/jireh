<?php

namespace App\Services;

use App\Models\Articulo;
use App\Models\Venta;
use App\Models\DetalleVenta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class MonitoreoAutocorreccion
{
    /**
     * OPCIÓN 3: SISTEMA DE MONITOREO CONTINUO Y AUTO-CORRECCIÓN
     * 
     * Monitorea constantemente el sistema para detectar inconsistencias
     * y aplicar correcciones automáticas en tiempo real.
     */

    private $metricas = [];
    private $alertas = [];
    private $correccionesAplicadas = [];

    /**
     * Ejecutar ciclo completo de monitoreo y auto-corrección
     */
    public function ejecutarMonitoreoCompleto($configuracion = [])
    {
        $configuracion = array_merge([
            'correccion_automatica' => true,
            'umbral_critico' => 0.95,
            'intervalo_monitoreo' => 5, // minutos
            'max_correcciones_automaticas' => 10
        ], $configuracion);

        try {
            $this->inicializarMonitoreo();
            
            // 1. Monitoreo de stock en tiempo real
            $this->monitorearStockTiempoReal($configuracion);
            
            // 2. Detección de patrones anómalos
            $this->detectarPatronesAnomalos($configuracion);
            
            // 3. Validación de integridad continua
            $this->validarIntegridadContinua($configuracion);
            
            // 4. Auto-corrección de inconsistencias menores
            if ($configuracion['correccion_automatica']) {
                $this->aplicarCorreccionesAutomaticas($configuracion);
            }
            
            // 5. Generar reporte de monitoreo
            $reporte = $this->generarReporteMonitoreo();
            
            // 6. Actualizar métricas de salud del sistema
            $this->actualizarMetricasSalud();
            
            return [
                'exito' => true,
                'timestamp' => now(),
                'metricas' => $this->metricas,
                'alertas' => $this->alertas,
                'correcciones' => $this->correccionesAplicadas,
                'reporte' => $reporte
            ];
            
        } catch (\Exception $e) {
            Log::error('Error en monitoreo continuo: ' . $e->getMessage());
            return [
                'exito' => false,
                'error' => $e->getMessage(),
                'timestamp' => now()
            ];
        }
    }
    
    /**
     * Monitorear stock en tiempo real
     */
    private function monitorearStockTiempoReal($configuracion)
    {
        $this->log('Iniciando monitoreo de stock en tiempo real');
        
        // 1. Detectar stocks negativos
        $stocksNegativos = $this->detectarStocksNegativos();
        if (!empty($stocksNegativos)) {
            $this->registrarAlerta('STOCK_NEGATIVO', 'CRITICA', $stocksNegativos);
            
            if ($configuracion['correccion_automatica']) {
                $this->corregirStocksNegativos($stocksNegativos);
            }
        }
        
        // 2. Detectar inconsistencias de stock vs movimientos
        $inconsistenciasStock = $this->detectarInconsistenciasStock();
        if (!empty($inconsistenciasStock)) {
            $this->registrarAlerta('INCONSISTENCIA_STOCK', 'ALTA', $inconsistenciasStock);
            
            if ($configuracion['correccion_automatica']) {
                $this->corregirInconsistenciasStock($inconsistenciasStock);
            }
        }
        
        // 3. Monitorear stocks críticos (por debajo del mínimo)
        $stocksCriticos = $this->detectarStocksCriticos();
        if (!empty($stocksCriticos)) {
            $this->registrarAlerta('STOCK_CRITICO', 'MEDIA', $stocksCriticos);
        }
        
        $this->metricas['stocks_monitoreados'] = Articulo::count();
        $this->metricas['stocks_negativos'] = count($stocksNegativos);
        $this->metricas['inconsistencias_stock'] = count($inconsistenciasStock);
    }
    
    /**
     * Detectar patrones anómalos
     */
    private function detectarPatronesAnomalos($configuracion)
    {
        $this->log('Detectando patrones anómalos');
        
        // 1. Detectar ventas con patrones inusuales
        $ventasAnomalas = $this->detectarVentasAnomalas();
        if (!empty($ventasAnomalas)) {
            $this->registrarAlerta('VENTAS_ANOMALAS', 'MEDIA', $ventasAnomalas);
        }
        
        // 2. Detectar cambios masivos de stock
        $cambiosMasivos = $this->detectarCambiosMasivosStock();
        if (!empty($cambiosMasivos)) {
            $this->registrarAlerta('CAMBIOS_MASIVOS', 'ALTA', $cambiosMasivos);
        }
        
        // 3. Detectar manipulaciones sospechosas
        $manipulacionesSospechosas = $this->detectarManipulacionesSospechosas();
        if (!empty($manipulacionesSospechosas)) {
            $this->registrarAlerta('MANIPULACIONES_SOSPECHOSAS', 'CRITICA', $manipulacionesSospechosas);
        }
        
        $this->metricas['patrones_anomalos'] = count($ventasAnomalas) + count($cambiosMasivos) + count($manipulacionesSospechosas);
    }
    
    /**
     * Validación de integridad continua
     */
    private function validarIntegridadContinua($configuracion)
    {
        $this->log('Validando integridad continua');
        
        // 1. Verificar integridad referencial
        $erroresReferencial = $this->verificarIntegridadReferencial();
        if (!empty($erroresReferencial)) {
            $this->registrarAlerta('INTEGRIDAD_REFERENCIAL', 'CRITICA', $erroresReferencial);
            
            if ($configuracion['correccion_automatica']) {
                $this->corregirIntegridadReferencial($erroresReferencial);
            }
        }
        
        // 2. Verificar coherencia de totales
        $erroresCoherencia = $this->verificarCoherenciaTotales();
        if (!empty($erroresCoherencia)) {
            $this->registrarAlerta('COHERENCIA_TOTALES', 'ALTA', $erroresCoherencia);
            
            if ($configuracion['correccion_automatica']) {
                $this->corregirCoherenciaTotales($erroresCoherencia);
            }
        }
        
        // 3. Verificar duplicaciones
        $duplicaciones = $this->detectarDuplicaciones();
        if (!empty($duplicaciones)) {
            $this->registrarAlerta('DUPLICACIONES', 'MEDIA', $duplicaciones);
        }
        
        $this->metricas['errores_integridad'] = count($erroresReferencial) + count($erroresCoherencia) + count($duplicaciones);
    }
    
    /**
     * Aplicar correcciones automáticas
     */
    private function aplicarCorreccionesAutomaticas($configuracion)
    {
        $maxCorrecciones = $configuracion['max_correcciones_automaticas'];
        $correccionesAplicadas = 0;
        
        foreach ($this->alertas as $alerta) {
            if ($correccionesAplicadas >= $maxCorrecciones) {
                break;
            }
            
            $resultado = $this->aplicarCorreccionPorTipo($alerta);
            if ($resultado['exito']) {
                $correccionesAplicadas++;
                $this->correccionesAplicadas[] = $resultado;
            }
        }
        
        $this->metricas['correcciones_aplicadas'] = $correccionesAplicadas;
    }
    
    /**
     * Detectar stocks negativos
     */
    private function detectarStocksNegativos()
    {
        return Articulo::where('stock', '<', 0)
            ->select('id', 'codigo', 'nombre', 'stock')
            ->get()
            ->toArray();
    }
    
    /**
     * Detectar inconsistencias de stock
     */
    private function detectarInconsistenciasStock()
    {
        $inconsistencias = [];
        
        $articulos = Articulo::all();
        
        foreach ($articulos as $articulo) {
            $stockTeorico = $this->calcularStockTeorico($articulo);
            $diferencia = abs($articulo->stock - $stockTeorico);
            
            if ($diferencia > 0.01) {
                $inconsistencias[] = [
                    'articulo_id' => $articulo->id,
                    'codigo' => $articulo->codigo,
                    'stock_actual' => $articulo->stock,
                    'stock_teorico' => $stockTeorico,
                    'diferencia' => $diferencia
                ];
            }
        }
        
        return $inconsistencias;
    }
    
    /**
     * Calcular stock teórico basado en movimientos
     */
    private function calcularStockTeorico($articulo)
    {
        // Stock inicial + ingresos - ventas
        $ingresos = DB::table('detalle_ingresos')
            ->where('articulo_id', $articulo->id)
            ->sum('cantidad');
            
        $ventas = DB::table('detalle_ventas')
            ->where('articulo_id', $articulo->id)
            ->sum('cantidad');
            
        return ($articulo->stock_inicial ?? 0) + $ingresos - $ventas;
    }
    
    /**
     * Detectar stocks críticos
     */
    private function detectarStocksCriticos()
    {
        return Articulo::where('stock', '<=', DB::raw('stock_minimo'))
            ->where('stock_minimo', '>', 0)
            ->select('id', 'codigo', 'nombre', 'stock', 'stock_minimo')
            ->get()
            ->toArray();
    }
    
    /**
     * Detectar ventas anómalas
     */    private function detectarVentasAnomalas()
    {
        $ventasAnomalas = [];
          // Calcular promedio de ventas basado en la suma de detalles
        $ventasConTotales = DB::table('ventas')
            ->join('detalle_ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->whereDate('ventas.created_at', '>=', now()->subDays(30))
            ->groupBy('ventas.id')
            ->select('ventas.id', DB::raw('SUM(detalle_ventas.cantidad * detalle_ventas.precio_venta) as total_calculado'))
            ->get();
            
        if ($ventasConTotales->count() > 0) {
            $promedioVentas = $ventasConTotales->avg('total_calculado');
            $umbralAlto = $promedioVentas * 5; // 5 veces el promedio
            
            // Buscar ventas con totales muy altos
            $ventasAltas = DB::table('ventas')
                ->join('detalle_ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
                ->whereDate('ventas.created_at', '>=', now()->subDay())
                ->groupBy('ventas.id', 'ventas.fecha', 'ventas.cliente_id')
                ->select('ventas.id', 'ventas.fecha', 'ventas.cliente_id', 
                        DB::raw('SUM(detalle_ventas.cantidad * detalle_ventas.precio_venta) as total_calculado'))
                ->having('total_calculado', '>', $umbralAlto)
                ->get();
                  foreach ($ventasAltas as $venta) {
                $ventasAnomalas[] = [
                    'venta_id' => $venta->id,
                    'total' => $venta->total_calculado,
                    'promedio' => $promedioVentas,
                    'factor' => $venta->total_calculado / $promedioVentas,
                    'tipo' => 'TOTAL_ALTO'
                ];
            }
        }        // Ventas con muchos artículos diferentes - también usando cálculo correcto
        $ventasConMuchosArticulos = DB::table('ventas')
            ->join('detalle_ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->select('ventas.id', 
                    DB::raw('SUM(detalle_ventas.cantidad * detalle_ventas.precio_venta) as total_calculado'), 
                    DB::raw('COUNT(DISTINCT detalle_ventas.articulo_id) as articulos_diferentes'))
            ->whereDate('ventas.created_at', '>=', now()->subDay())
            ->groupBy('ventas.id')
            ->having('articulos_diferentes', '>', 20)
            ->get();
            
        foreach ($ventasConMuchosArticulos as $venta) {
            $ventasAnomalas[] = [
                'venta_id' => $venta->id,
                'total' => $venta->total_calculado,
                'articulos_diferentes' => $venta->articulos_diferentes,
                'tipo' => 'MUCHOS_ARTICULOS'
            ];
        }
        
        return $ventasAnomalas;
    }
    
    /**
     * Detectar cambios masivos de stock
     */
    private function detectarCambiosMasivos()
    {
        // Buscar cambios de stock muy grandes en poco tiempo
        return DB::table('movimientos_stock')
            ->select('articulo_id', DB::raw('SUM(ABS(cantidad)) as cambio_total'))
            ->where('created_at', '>=', now()->subHour())
            ->groupBy('articulo_id')
            ->having('cambio_total', '>', 100)
            ->get()
            ->toArray();
    }
    
    /**
     * Corregir stocks negativos automáticamente
     */
    private function corregirStocksNegativos($stocksNegativos)
    {
        foreach ($stocksNegativos as $item) {
            $articulo = Articulo::find($item['id']);
            if ($articulo && $articulo->stock < 0) {
                // Ajustar a 0 y registrar el ajuste
                $stockAnterior = $articulo->stock;
                $articulo->stock = 0;
                $articulo->save();
                
                $this->registrarCorreccion('STOCK_NEGATIVO_CORREGIDO', [
                    'articulo_id' => $articulo->id,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => 0
                ]);
            }
        }
    }
    
    /**
     * Registrar alerta
     */
    private function registrarAlerta($tipo, $severidad, $datos)
    {
        $alerta = [
            'tipo' => $tipo,
            'severidad' => $severidad,
            'timestamp' => now(),
            'datos' => $datos,
            'id' => uniqid()
        ];
        
        $this->alertas[] = $alerta;
        
        // Log de la alerta
        Log::warning("Alerta detectada: {$tipo}", $alerta);
        
        // Guardar en cache para dashboard
        $alertasCache = Cache::get('alertas_monitoreo', []);
        $alertasCache[] = $alerta;
        Cache::put('alertas_monitoreo', array_slice($alertasCache, -50), 3600); // Últimas 50 alertas por 1 hora
    }
    
    /**
     * Registrar corrección aplicada
     */
    private function registrarCorreccion($tipo, $datos)
    {
        $correccion = [
            'tipo' => $tipo,
            'timestamp' => now(),
            'datos' => $datos,
            'usuario' => 'SISTEMA_AUTOMATICO'
        ];
        
        $this->correccionesAplicadas[] = $correccion;
        
        Log::info("Corrección automática aplicada: {$tipo}", $correccion);
    }
    
    /**
     * Generar reporte de monitoreo
     */
    private function generarReporteMonitoreo()
    {
        return [
            'timestamp_inicio' => $this->metricas['timestamp_inicio'] ?? now(),
            'timestamp_fin' => now(),
            'resumen_metricas' => $this->metricas,
            'total_alertas' => count($this->alertas),
            'alertas_por_severidad' => $this->agruparAlertasPorSeveridad(),
            'total_correcciones' => count($this->correccionesAplicadas),
            'salud_sistema' => $this->calcularSaludSistema()
        ];
    }
    
    /**
     * Calcular salud del sistema
     */
    private function calcularSaludSistema()
    {
        $totalProblemas = count($this->alertas);
        $problemasCorregidos = count($this->correccionesAplicadas);
        
        if ($totalProblemas == 0) {
            return 100; // Salud perfecta
        }
        
        $porcentajeCorreccion = ($problemasCorregidos / $totalProblemas) * 100;
        $penalizacionAlertas = min($totalProblemas * 5, 50); // Máximo 50% de penalización
        
        return max(0, 100 - $penalizacionAlertas + ($porcentajeCorreccion * 0.3));
    }
    
    /**
     * Agrupar alertas por severidad
     */
    private function agruparAlertasPorSeveridad()
    {
        $agrupacion = ['CRITICA' => 0, 'ALTA' => 0, 'MEDIA' => 0, 'BAJA' => 0];
        
        foreach ($this->alertas as $alerta) {
            $agrupacion[$alerta['severidad']]++;
        }
        
        return $agrupacion;
    }
    
    /**
     * Inicializar monitoreo
     */
    private function inicializarMonitoreo()
    {
        $this->metricas = [
            'timestamp_inicio' => now(),
            'version_sistema' => '1.0',
            'modo_monitoreo' => 'CONTINUO'
        ];
        
        $this->alertas = [];
        $this->correccionesAplicadas = [];
    }
    
    /**
     * Log interno
     */
    private function log($mensaje, $datos = [])
    {
        Log::info("Monitoreo: {$mensaje}", $datos);
    }
    
    /**
     * Actualizar métricas de salud del sistema
     */
    private function actualizarMetricasSalud()
    {
        $salud = $this->calcularSaludSistema();
        
        Cache::put('salud_sistema', [
            'porcentaje' => $salud,
            'timestamp' => now(),
            'alertas_activas' => count($this->alertas),
            'correcciones_aplicadas' => count($this->correccionesAplicadas)
        ], 300); // 5 minutos
        
        // Almacenar historial de salud
        $historial = Cache::get('historial_salud', []);
        $historial[] = ['timestamp' => now(), 'salud' => $salud];
        $historial = array_slice($historial, -100); // Últimas 100 mediciones
        Cache::put('historial_salud', $historial, 3600);
    }
    
    /**
     * Corregir inconsistencias de stock
     */
    private function corregirInconsistenciasStock($inconsistencias)
    {
        foreach ($inconsistencias as $inconsistencia) {
            $articulo = Articulo::find($inconsistencia['articulo_id']);
            if ($articulo) {
                $stockAnterior = $articulo->stock;
                $articulo->stock = $inconsistencia['stock_teorico'];
                $articulo->save();
                
                $this->registrarCorreccion('INCONSISTENCIA_STOCK_CORREGIDA', [
                    'articulo_id' => $articulo->id,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $inconsistencia['stock_teorico'],
                    'diferencia' => $inconsistencia['diferencia']
                ]);
            }
        }
    }
    
    /**
     * Detectar cambios masivos de stock
     */
    private function detectarCambiosMasivosStock()
    {
        // Buscar cambios de stock muy grandes en poco tiempo
        return DB::table('movimientos_stock')
            ->select('articulo_id', DB::raw('SUM(ABS(cantidad)) as cambio_total'))
            ->where('created_at', '>=', now()->subHour())
            ->groupBy('articulo_id')
            ->having('cambio_total', '>', 100)
            ->get()
            ->toArray();
    }
    
    /**
     * Detectar manipulaciones sospechosas
     */
    private function detectarManipulacionesSospechosas()
    {
        $manipulaciones = [];
        
        // Buscar ajustes manuales frecuentes del mismo usuario
        $ajustesFreuentes = DB::table('movimientos_stock')
            ->select('user_id', 'articulo_id', DB::raw('COUNT(*) as total_ajustes'))
            ->where('tipo', 'AJUSTE_MANUAL')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('user_id', 'articulo_id')
            ->having('total_ajustes', '>', 5)
            ->get();
            
        foreach ($ajustesFreuentes as $ajuste) {
            $manipulaciones[] = [
                'tipo' => 'AJUSTES_FRECUENTES',
                'user_id' => $ajuste->user_id,
                'articulo_id' => $ajuste->articulo_id,
                'total_ajustes' => $ajuste->total_ajustes
            ];
        }
          // Buscar cambios de stock sin movimientos registrados
        $cambiosSinMovimientos = DB::select("
            SELECT a.id, a.codigo, a.stock, a.stock_inicial,
                   COALESCE(SUM(di.cantidad), 0) as total_ingresos,
                   COALESCE(SUM(dv.cantidad), 0) as total_ventas
            FROM articulos a
            LEFT JOIN detalle_ingresos di ON a.id = di.articulo_id
            LEFT JOIN detalle_ventas dv ON a.id = dv.articulo_id
            GROUP BY a.id, a.codigo, a.stock, a.stock_inicial
            HAVING ABS(a.stock - (COALESCE(a.stock_inicial, 0) + COALESCE(SUM(di.cantidad), 0) - COALESCE(SUM(dv.cantidad), 0))) > 5
        ");
        
        foreach ($cambiosSinMovimientos as $cambio) {
            $manipulaciones[] = [
                'tipo' => 'CAMBIO_SIN_MOVIMIENTO',
                'articulo_id' => $cambio->id,
                'codigo' => $cambio->codigo,
                'stock_actual' => $cambio->stock,
                'stock_esperado' => $cambio->total_ingresos - $cambio->total_ventas
            ];
        }
        
        return $manipulaciones;
    }
    
    /**
     * Verificar integridad referencial
     */
    private function verificarIntegridadReferencial()
    {
        $errores = [];
        
        // Detalles de venta sin venta padre
        $detallesHuerfanos = DB::table('detalle_ventas as dv')
            ->leftJoin('ventas as v', 'dv.venta_id', '=', 'v.id')
            ->whereNull('v.id')
            ->select('dv.id', 'dv.venta_id')
            ->get();
            
        if ($detallesHuerfanos->count() > 0) {
            $errores[] = [
                'tipo' => 'DETALLES_VENTA_HUERFANOS',
                'cantidad' => $detallesHuerfanos->count(),
                'ids' => $detallesHuerfanos->pluck('id')->toArray()
            ];
        }
        
        // Detalles de venta con artículos inexistentes
        $detallesSinArticulo = DB::table('detalle_ventas as dv')
            ->leftJoin('articulos as a', 'dv.articulo_id', '=', 'a.id')
            ->whereNull('a.id')
            ->select('dv.id', 'dv.articulo_id')
            ->get();
            
        if ($detallesSinArticulo->count() > 0) {
            $errores[] = [
                'tipo' => 'DETALLES_SIN_ARTICULO',
                'cantidad' => $detallesSinArticulo->count(),
                'ids' => $detallesSinArticulo->pluck('id')->toArray()
            ];
        }
        
        // Ventas sin detalles
        $ventasSinDetalles = DB::table('ventas as v')
            ->leftJoin('detalle_ventas as dv', 'v.id', '=', 'dv.venta_id')
            ->whereNull('dv.venta_id')
            ->select('v.id')
            ->get();
            
        if ($ventasSinDetalles->count() > 0) {
            $errores[] = [
                'tipo' => 'VENTAS_SIN_DETALLES',
                'cantidad' => $ventasSinDetalles->count(),
                'ids' => $ventasSinDetalles->pluck('id')->toArray()
            ];
        }
        
        return $errores;
    }
    
    /**
     * Corregir integridad referencial
     */
    private function corregirIntegridadReferencial($errores)
    {
        foreach ($errores as $error) {
            switch ($error['tipo']) {
                case 'DETALLES_VENTA_HUERFANOS':
                    // Eliminar detalles huérfanos
                    DB::table('detalle_ventas')->whereIn('id', $error['ids'])->delete();
                    $this->registrarCorreccion('DETALLES_HUERFANOS_ELIMINADOS', $error);
                    break;
                    
                case 'DETALLES_SIN_ARTICULO':
                    // Eliminar detalles con artículos inexistentes
                    DB::table('detalle_ventas')->whereIn('id', $error['ids'])->delete();
                    $this->registrarCorreccion('DETALLES_SIN_ARTICULO_ELIMINADOS', $error);
                    break;
                    
                case 'VENTAS_SIN_DETALLES':
                    // Marcar ventas sin detalles como canceladas
                    DB::table('ventas')
                        ->whereIn('id', $error['ids'])
                        ->update(['estado' => 'cancelada', 'updated_at' => now()]);
                    $this->registrarCorreccion('VENTAS_SIN_DETALLES_CANCELADAS', $error);
                    break;
            }
        }
    }
    
    /**
     * Verificar coherencia de totales
     */
    private function verificarCoherenciaTotales()
    {
        $errores = [];
          // Buscar ventas donde el total calculado tenga inconsistencias en los detalles
        // Como no hay campo total en ventas, verificamos coherencia entre detalles
        $ventasIncoherentes = DB::select("
            SELECT v.id, COALESCE(SUM(dv.sub_total), 0) as suma_detalles,
                   COALESCE(SUM(dv.cantidad * dv.precio_venta), 0) as total_calculado,
                   ABS(COALESCE(SUM(dv.sub_total), 0) - COALESCE(SUM(dv.cantidad * dv.precio_venta), 0)) as diferencia
            FROM ventas v
            LEFT JOIN detalle_ventas dv ON v.id = dv.venta_id
            GROUP BY v.id
            HAVING ABS(COALESCE(SUM(dv.sub_total), 0) - COALESCE(SUM(dv.cantidad * dv.precio_venta), 0)) > 0.01
        ");
        
        foreach ($ventasIncoherentes as $venta) {
            $errores[] = [
                'venta_id' => $venta->id,
                'suma_subtotales' => $venta->suma_detalles,
                'total_calculado' => $venta->total_calculado,
                'diferencia' => $venta->diferencia
            ];
        }
        
        return $errores;
    }
    
    /**
     * Corregir coherencia de totales
     */
    private function corregirCoherenciaTotales($errores)
    {
        foreach ($errores as $error) {
            DB::table('ventas')
                ->where('id', $error['venta_id'])
                ->update(['total' => $error['suma_detalles'], 'updated_at' => now()]);
                
            $this->registrarCorreccion('TOTAL_VENTA_CORREGIDO', $error);
        }
    }
    
    /**
     * Detectar duplicaciones
     */
    private function detectarDuplicaciones()
    {
        $duplicaciones = [];
        
        // Buscar detalles de venta duplicados
        $detallesDuplicados = DB::select("
            SELECT venta_id, articulo_id, COUNT(*) as duplicados
            FROM detalle_ventas
            GROUP BY venta_id, articulo_id
            HAVING COUNT(*) > 1
        ");
        
        foreach ($detallesDuplicados as $duplicado) {
            $duplicaciones[] = [
                'tipo' => 'DETALLES_DUPLICADOS',
                'venta_id' => $duplicado->venta_id,
                'articulo_id' => $duplicado->articulo_id,
                'cantidad_duplicados' => $duplicado->duplicados
            ];
        }
        
        return $duplicaciones;
    }
    
    /**
     * Aplicar corrección según tipo de alerta
     */
    private function aplicarCorreccionPorTipo($alerta)
    {
        try {
            switch ($alerta['tipo']) {
                case 'STOCK_NEGATIVO':
                    $this->corregirStocksNegativos($alerta['datos']);
                    return ['exito' => true, 'tipo' => $alerta['tipo']];
                    
                case 'INCONSISTENCIA_STOCK':
                    $this->corregirInconsistenciasStock($alerta['datos']);
                    return ['exito' => true, 'tipo' => $alerta['tipo']];
                    
                case 'INTEGRIDAD_REFERENCIAL':
                    $this->corregirIntegridadReferencial($alerta['datos']);
                    return ['exito' => true, 'tipo' => $alerta['tipo']];
                    
                case 'COHERENCIA_TOTALES':
                    $this->corregirCoherenciaTotales($alerta['datos']);
                    return ['exito' => true, 'tipo' => $alerta['tipo']];
                    
                default:
                    return ['exito' => false, 'razon' => 'Tipo de corrección no implementado'];
            }
        } catch (\Exception $e) {
            Log::error("Error aplicando corrección: " . $e->getMessage());
            return ['exito' => false, 'error' => $e->getMessage()];
        }
    }
}
