<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PrevencionInconsistencias;
use App\Services\TransaccionesAtomicas;
use App\Services\MonitoreoAutocorreccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PrevencionInconsistenciasController extends Controller
{    private $prevencion;
    private $transacciones;
    private $monitoreo;

    public function __construct(
        PrevencionInconsistencias $prevencion,
        TransaccionesAtomicas $transacciones,
        MonitoreoAutocorreccion $monitoreo
    ) {
        $this->prevencion = $prevencion;
        $this->transacciones = $transacciones;
        $this->monitoreo = $monitoreo;
    }

    /**
     * Método de prueba simple
     */
    public function test()
    {
        return response()->json([
            'mensaje' => 'Controlador de Prevención funcionando correctamente',
            'timestamp' => now(),
            'servicios_disponibles' => [
                'PrevencionInconsistencias' => class_exists('App\\Services\\PrevencionInconsistencias'),
                'TransaccionesAtomicas' => class_exists('App\\Services\\TransaccionesAtomicas'),
                'MonitoreoAutocorreccion' => class_exists('App\\Services\\MonitoreoAutocorreccion')
            ]
        ]);
    }    /**
     * Dashboard principal de prevención de inconsistencias
     */
    public function dashboard()
    {
        try {
            // Obtener métricas reales del sistema
            $estadoMonitoreo = $this->monitoreo->ejecutarMonitoreoCompleto([
                'correccion_automatica' => false, // Solo obtener métricas, no corregir
                'umbral_critico' => 0.95
            ]);

            $saludSistema = [
                'porcentaje' => $estadoMonitoreo['reporte']['salud_sistema'] ?? 95,
                'timestamp' => $estadoMonitoreo['timestamp'],
                'alertas_activas' => count($estadoMonitoreo['alertas'] ?? []),
                'correcciones_aplicadas' => count($estadoMonitoreo['correcciones'] ?? [])
            ];

            // Alertas recientes del monitoreo
            $alertasRecientes = collect($estadoMonitoreo['alertas'] ?? [])
                ->take(5)
                ->map(function($alerta) {
                    return [
                        'timestamp' => $alerta['timestamp'] ?? now(),
                        'severidad' => $alerta['severidad'] ?? 'MEDIA',
                        'mensaje' => $alerta['mensaje'] ?? 'Alerta de sistema',
                        'tipo' => $alerta['tipo'] ?? 'GENERAL'
                    ];
                })
                ->toArray();

            // Historial de salud (últimas 24 horas simulado con datos reales)
            $historialSalud = $this->obtenerHistorialSalud();

            return view('admin.prevencion.dashboard-working', compact(
                'saludSistema',
                'alertasRecientes',
                'historialSalud'
            ));

        } catch (\Exception $e) {
            Log::error('Error en dashboard de prevención: ' . $e->getMessage());
            
            // Datos de fallback en caso de error
            return view('admin.prevencion.dashboard-working', [
                'saludSistema' => [
                    'porcentaje' => 100, 
                    'timestamp' => now(), 
                    'alertas_activas' => 0, 
                    'correcciones_aplicadas' => 0
                ],
                'alertasRecientes' => [],
                'historialSalud' => []
            ]);
        }
    }    /**
     * OPCIÓN 1: Ejecutar validación preventiva
     */
    public function ejecutarValidacionPreventiva(Request $request)
    {
        try {
            $datosVenta = $request->input('venta', []);
            $detalles = $request->input('detalles', []);

            // Usar el servicio real de validación preventiva
            $resultado = $this->prevencion->validarAntesDeVenta($datosVenta, $detalles);

            return response()->json([
                'exito' => true,
                'resultado' => $resultado,
                'mensaje' => $resultado['valido'] ? 
                    'Validación preventiva exitosa - Operación segura para proceder' : 
                    'Errores detectados en validación preventiva'
            ]);

        } catch (\Exception $e) {
            Log::error('Error en validación preventiva: ' . $e->getMessage());
            return response()->json([
                'exito' => false,
                'error' => 'Error interno en validación preventiva',
                'detalle' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }    /**
     * OPCIÓN 2: Ejecutar venta con transacciones atómicas
     */
    public function ejecutarVentaAtomica(Request $request)
    {
        try {
            // Validar datos de entrada con reglas más robustas
            $request->validate([
                'venta.fecha' => 'required|date',
                'venta.user_id' => 'required|exists:users,id',
                'venta.cliente_id' => 'nullable|exists:clientes,id',
                'venta.observaciones' => 'nullable|string|max:500',
                'detalles' => 'required|array|min:1|max:50',
                'detalles.*.articulo_id' => 'required|exists:articulos,id',
                'detalles.*.cantidad' => 'required|numeric|min:0.01|max:10000',
                'detalles.*.precio_unitario' => 'required|numeric|min:0.01|max:100000'
            ]);

            $datosVenta = $request->input('venta');
            $detalles = $request->input('detalles');

            // Usar el servicio real de transacciones atómicas
            $resultado = $this->transacciones->ejecutarVentaAtomica($datosVenta, $detalles);

            if ($resultado['exito']) {
                return response()->json([
                    'exito' => true,
                    'venta_id' => $resultado['venta_id'],
                    'total' => $resultado['total'],
                    'mensaje' => 'Venta procesada exitosamente con transacciones atómicas',
                    'operaciones_ejecutadas' => $resultado['operaciones_ejecutadas'],
                    'timestamp' => now()
                ]);
            } else {
                return response()->json([
                    'exito' => false,
                    'error' => $resultado['mensaje'] ?? 'Error en procesamiento atómico',
                    'operaciones_revertidas' => $resultado['operaciones_revertidas'] ?? [],
                    'detalle_error' => $resultado['error_detalle'] ?? null
                ], 422);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'exito' => false,
                'error' => 'Datos de entrada inválidos',
                'errores_validacion' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error en venta atómica: ' . $e->getMessage());
            return response()->json([
                'exito' => false,
                'error' => 'Error interno en procesamiento atómico',
                'detalle' => config('app.debug') ? $e->getMessage() : null,
                'operaciones_revertidas' => ['Todas las operaciones fueron revertidas automáticamente']
            ], 500);
        }
    }    /**
     * OPCIÓN 3: Ejecutar monitoreo continuo
     */
    public function ejecutarMonitoreoContinuo(Request $request)
    {
        try {
            $configuracion = $request->validate([
                'correccion_automatica' => 'boolean',
                'umbral_critico' => 'numeric|between:0,1',
                'max_correcciones_automaticas' => 'integer|min:1|max:50'
            ]);

            // Configuración por defecto con valores seguros
            $configuracion = array_merge([
                'correccion_automatica' => true,
                'umbral_critico' => 0.95,
                'max_correcciones_automaticas' => 10,
                'intervalo_monitoreo' => 5
            ], $configuracion);

            // Ejecutar monitoreo usando el servicio real
            $resultado = $this->monitoreo->ejecutarMonitoreoCompleto($configuracion);

            if ($resultado['exito']) {
                // Guardar métricas en cache para uso futuro
                Cache::put('ultima_ejecucion_monitoreo', [
                    'timestamp' => $resultado['timestamp'],
                    'metricas' => $resultado['metricas'],
                    'salud_sistema' => $resultado['reporte']['salud_sistema'] ?? 95
                ], 3600); // 1 hora

                return response()->json([
                    'exito' => true,
                    'timestamp' => $resultado['timestamp'],
                    'metricas' => $resultado['metricas'],
                    'alertas' => count($resultado['alertas']),
                    'correcciones' => count($resultado['correcciones']),
                    'salud_sistema' => $resultado['reporte']['salud_sistema'] ?? 95,
                    'mensaje' => 'Monitoreo continuo ejecutado exitosamente',
                    'detalle_alertas' => array_slice($resultado['alertas'], 0, 5), // Solo primeras 5
                    'detalle_correcciones' => array_slice($resultado['correcciones'], 0, 5)
                ]);
            } else {
                return response()->json([
                    'exito' => false,
                    'error' => $resultado['error'] ?? 'Error en monitoreo',
                    'timestamp' => $resultado['timestamp'],
                    'mensaje' => 'Error durante la ejecución del monitoreo continuo'
                ], 500);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'exito' => false,
                'error' => 'Configuración inválida',
                'errores_validacion' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error en monitoreo continuo: ' . $e->getMessage());
            return response()->json([
                'exito' => false,
                'error' => 'Error interno en monitoreo continuo',
                'detalle' => config('app.debug') ? $e->getMessage() : null,
                'timestamp' => now()
            ], 500);
        }
    }

    /**
     * Obtener estado actual del sistema
     */
    public function estadoSistema()
    {
        try {
            $salud = cache('salud_sistema', ['porcentaje' => 100]);
            $alertas = cache('alertas_monitoreo', []);
            
            // Contar alertas por severidad
            $alertasPorSeveridad = [
                'CRITICA' => 0,
                'ALTA' => 0,
                'MEDIA' => 0,
                'BAJA' => 0
            ];

            foreach ($alertas as $alerta) {
                if (isset($alertasPorSeveridad[$alerta['severidad']])) {
                    $alertasPorSeveridad[$alerta['severidad']]++;
                }
            }

            return response()->json([
                'salud_sistema' => $salud['porcentaje'],
                'total_alertas' => count($alertas),
                'alertas_por_severidad' => $alertasPorSeveridad,
                'ultima_actualizacion' => $salud['timestamp'] ?? now(),
                'estado' => $this->determinarEstadoSistema($salud['porcentaje'])
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo estado del sistema: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error interno'
            ], 500);
        }
    }

    /**
     * Configurar monitoreo automático
     */
    public function configurarMonitoreoAutomatico(Request $request)
    {
        try {
            $configuracion = $request->validate([
                'habilitado' => 'required|boolean',
                'intervalo_minutos' => 'required|integer|min:1|max:60',
                'correccion_automatica' => 'boolean',
                'max_correcciones_por_ciclo' => 'integer|min:1|max:100',
                'alertas_email' => 'boolean',
                'email_destinatarios' => 'nullable|array',
                'email_destinatarios.*' => 'email'
            ]);

            // Guardar configuración en cache
            cache(['configuracion_monitoreo' => $configuracion], 86400); // 24 horas

            // Si está habilitado, programar tarea
            if ($configuracion['habilitado']) {
                $this->programarMonitoreoAutomatico($configuracion);
            }

            return response()->json([
                'exito' => true,
                'mensaje' => 'Configuración de monitoreo guardada',
                'configuracion' => $configuracion
            ]);

        } catch (\Exception $e) {
            Log::error('Error configurando monitoreo: ' . $e->getMessage());
            return response()->json([
                'exito' => false,
                'error' => 'Error guardando configuración'
            ], 500);
        }
    }

    /**
     * Generar reporte de inconsistencias
     */
    public function generarReporteInconsistencias()
    {
        try {
            // Ejecutar monitoreo completo para obtener datos actuales
            $resultadoMonitoreo = $this->monitoreo->ejecutarMonitoreoCompleto([
                'correccion_automatica' => false // Solo diagnóstico
            ]);

            $reporte = [
                'timestamp' => now(),
                'resumen_ejecutivo' => [
                    'salud_sistema' => $resultadoMonitoreo['reporte']['salud_sistema'] ?? 0,
                    'total_alertas' => count($resultadoMonitoreo['alertas'] ?? []),
                    'alertas_criticas' => $this->contarAlertasPorSeveridad($resultadoMonitoreo['alertas'] ?? [], 'CRITICA'),
                    'inconsistencias_detectadas' => $resultadoMonitoreo['metricas']['stocks_negativos'] ?? 0 + 
                                                   $resultadoMonitoreo['metricas']['inconsistencias_stock'] ?? 0 +
                                                   $resultadoMonitoreo['metricas']['errores_integridad'] ?? 0
                ],
                'detalle_alertas' => $resultadoMonitoreo['alertas'] ?? [],
                'metricas_detalladas' => $resultadoMonitoreo['metricas'] ?? [],
                'recomendaciones' => $this->generarRecomendaciones($resultadoMonitoreo)
            ];

            return response()->json([
                'exito' => true,
                'reporte' => $reporte
            ]);

        } catch (\Exception $e) {
            Log::error('Error generando reporte: ' . $e->getMessage());
            return response()->json([
                'exito' => false,
                'error' => 'Error generando reporte'
            ], 500);
        }
    }

    /**
     * Determinar estado del sistema basado en porcentaje de salud
     */
    private function determinarEstadoSistema($porcentajeSalud)
    {
        if ($porcentajeSalud >= 90) {
            return 'EXCELENTE';
        } elseif ($porcentajeSalud >= 70) {
            return 'BUENO';
        } elseif ($porcentajeSalud >= 50) {
            return 'REGULAR';
        } else {
            return 'CRITICO';
        }
    }

    /**
     * Contar alertas por severidad
     */
    private function contarAlertasPorSeveridad($alertas, $severidad)
    {
        return count(array_filter($alertas, function($alerta) use ($severidad) {
            return $alerta['severidad'] === $severidad;
        }));
    }

    /**
     * Generar recomendaciones basadas en el resultado del monitoreo
     */
    private function generarRecomendaciones($resultadoMonitoreo)
    {
        $recomendaciones = [];
        $alertas = $resultadoMonitoreo['alertas'] ?? [];
        $metricas = $resultadoMonitoreo['metricas'] ?? [];

        // Recomendaciones basadas en alertas críticas
        $alertasCriticas = array_filter($alertas, function($alerta) {
            return $alerta['severidad'] === 'CRITICA';
        });

        if (count($alertasCriticas) > 0) {
            $recomendaciones[] = [
                'prioridad' => 'ALTA',
                'titulo' => 'Atender alertas críticas inmediatamente',
                'descripcion' => 'Se detectaron ' . count($alertasCriticas) . ' alertas críticas que requieren atención inmediata.',
                'acciones' => ['Revisar alertas críticas', 'Aplicar correcciones manuales', 'Verificar procesos']
            ];
        }

        // Recomendaciones basadas en stocks negativos
        if (($metricas['stocks_negativos'] ?? 0) > 0) {
            $recomendaciones[] = [
                'prioridad' => 'ALTA',
                'titulo' => 'Corregir stocks negativos',
                'descripcion' => 'Se detectaron artículos con stock negativo.',
                'acciones' => ['Ejecutar corrección automática', 'Revisar proceso de ventas', 'Actualizar stock manualmente']
            ];
        }

        // Recomendaciones basadas en patrones anómalos
        if (($metricas['patrones_anomalos'] ?? 0) > 5) {
            $recomendaciones[] = [
                'prioridad' => 'MEDIA',
                'titulo' => 'Investigar patrones anómalos',
                'descripcion' => 'Se detectaron múltiples patrones de comportamiento inusuales.',
                'acciones' => ['Revisar logs de auditoría', 'Verificar usuarios activos', 'Implementar controles adicionales']
            ];
        }

        return $recomendaciones;
    }

    /**
     * Programar monitoreo automático (placeholder para implementación con scheduler)
     */
    private function programarMonitoreoAutomatico($configuracion)
    {
        // En una implementación real, esto se integraría con el task scheduler de Laravel
        Log::info('Monitoreo automático programado', $configuracion);
    }
}
