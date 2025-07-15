<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Sistema de Auditoría de Ventas e Inventario
    |--------------------------------------------------------------------------
    |
    | Configuraciones para el sistema de auditoría de Jireh Automotriz
    |
    */

    // Configuración de alertas de stock
    'stock' => [
        'limite_stock_bajo' => 10,
        'limite_stock_critico' => 0,
        'dias_historial_movimientos' => 30,
    ],

    // Configuración de auditoría
    'auditoria' => [
        'dias_por_defecto' => 30,
        'max_reportes_mostrar' => 10,
        'auto_correccion_habilitada' => false,
        'ruta_reportes' => 'auditorias',
    ],

    // Configuración de notificaciones
    'notificaciones' => [
        'habilitadas' => true,
        'canales' => ['log'], // 'mail', 'sms', 'slack'
        'destinatarios_admin' => [
            // 'admin@jireh.com'
        ],
    ],

    // Mensajes del sistema
    'mensajes' => [
        'stock_negativo' => 'Stock negativo detectado',
        'stock_bajo' => 'Stock bajo - considere reabastecer',
        'inconsistencia_stock' => 'Inconsistencia entre stock actual y movimientos',
        'venta_duplicada' => 'Posible venta duplicada detectada',
        'detalle_sospechoso' => 'Detalle de venta con datos anómalos',
    ],

    // Configuración de exportación
    'exportacion' => [
        'formatos_permitidos' => ['excel', 'pdf', 'json'],
        'max_registros_excel' => 10000,
        'incluir_graficos_pdf' => false,
    ],

    // Configuración de correcciones automáticas
    'correcciones' => [
        'stock_negativo' => true,
        'inconsistencias_menores' => true,
        'backup_antes_correccion' => true,
        'log_detallado' => true,
    ],
];
