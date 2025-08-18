<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lote de Sueldos #{{ $pagoSueldo->numero_lote }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1cm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 8px;
        }
        
        .header .logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 8px;
        }
        
        .header h1 {
            color: #007bff;
            font-size: 18px;
            margin-bottom: 3px;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 10px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 8px;
        }
        
        .info-box {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
            background-color: #f9f9f9;
        }
        
        .info-box h3 {
            color: #007bff;
            font-size: 12px;
            margin-bottom: 8px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 4px;
        }
        
        .info-row {
            margin-bottom: 6px;
            display: flex;
            justify-content: space-between;
        }
        
        .info-label {
            font-weight: bold;
            color: #495057;
            width: 45%;
        }
        
        .info-value {
            color: #212529;
            width: 55%;
            text-align: right;
        }
        
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            font-size: 9px;
            display: inline-block;
        }
        
        .badge-primary { background-color: #007bff; }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
        .badge-info { background-color: #17a2b8; }
        .badge-secondary { background-color: #6c757d; }
        
        .meta-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .meta-info h3 {
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.3);
        }
        
        .empleados-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 15px 0;
            font-size: 9px;
        }
        
        .empleados-table th,
        .empleados-table td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: left;
        }
        
        .empleados-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        .empleados-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .empleados-table td {
            vertical-align: top;
            min-height: 30px;
        }
        
        .empleados-table .small-text {
            font-size: 8px;
            color: #666;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-muted { color: #6c757d; }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 9px;
            color: #666;
        }
        
        .highlight-amount {
            font-size: 16px;
            font-weight: bold;
            color: #28a745;
        }
        
        .progress-section {
            background-color: #e8f5e8;
            border-left: 4px solid #28a745;
        }
        
        .totals-section {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        
        .section-full {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .estado-pagado { 
            color: #28a745; 
            font-weight: bold; 
            font-size: 8px; 
            background-color: #d4edda;
            padding: 2px 4px;
            border-radius: 3px;
            border: 1px solid #c3e6cb;
        }
        .estado-pendiente { 
            color: #856404; 
            font-weight: bold; 
            font-size: 8px; 
            background-color: #fff3cd;
            padding: 2px 4px;
            border-radius: 3px;
            border: 1px solid #ffeaa7;
        }
        .estado-cancelado { 
            color: #721c24; 
            font-weight: bold; 
            font-size: 8px; 
            background-color: #f8d7da;
            padding: 2px 4px;
            border-radius: 3px;
            border: 1px solid #f5c6cb;
        }
        
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
            font-weight: bold;
            font-size: 9px;
            display: inline-block;
        }
        
        .badge-primary { background-color: #007bff; }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
        .badge-info { background-color: #17a2b8; }
        .badge-secondary { background-color: #6c757d; }
        
        .page-break {
            page-break-before: always;
        }
        
        .page-break-avoid {
            page-break-inside: avoid;
        }
        
        .keep-together {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="Logo" class="logo">
        @endif
        <h1>Lote de Sueldos #{{ $pagoSueldo->numero_lote }}</h1>
        <div class="subtitle">
            Generado el: {{ $fechaGeneracion }}<br>
            {{ $config->company_name ?? 'Sistema de Gestión' }}
        </div>
    </div>

    <div class="info-grid">
        <!-- Columna izquierda -->
        <div class="info-column">
            <!-- Información del Lote -->
            <div class="info-box">
                <h3>Información del Lote</h3>
                <div class="info-row">
                    <span class="info-label">Número:</span>
                    <span class="info-value"><strong>{{ $pagoSueldo->numero_lote }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Período:</span>
                    <span class="info-value">
                        <span class="badge badge-info">{{ $pagoSueldo->periodo_completo }}</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Estado:</span>
                    <span class="info-value">
                        @if($pagoSueldo->estado === 'pendiente')
                            <span class="badge badge-warning">Pendiente</span>
                        @elseif($pagoSueldo->estado === 'pagado')
                            <span class="badge badge-success">Pagado</span>
                        @elseif($pagoSueldo->estado === 'cancelado')
                            <span class="badge badge-danger">Cancelado</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($pagoSueldo->estado) }}</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha de Pago:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($pagoSueldo->fecha_pago)->format('d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Método de Pago:</span>
                    <span class="info-value">{{ ucfirst($pagoSueldo->metodo_pago) }}</span>
                </div>
            </div>

            <!-- Información de Control -->
            <div class="info-box">
                <h3>Control y Auditoría</h3>
                <div class="info-row">
                    <span class="info-label">Creado por:</span>
                    <span class="info-value">{{ $pagoSueldo->usuario->name ?? 'Usuario eliminado' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha Creación:</span>
                    <span class="info-value">{{ $pagoSueldo->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($pagoSueldo->updated_at->ne($pagoSueldo->created_at))
                <div class="info-row">
                    <span class="info-label">Última Modificación:</span>
                    <span class="info-value">{{ $pagoSueldo->updated_at->format('d/m/Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Columna derecha -->
        <div class="info-column">
            <!-- Resumen de Progreso -->
            @php
                $resumen = $pagoSueldo->getResumenPagos();
            @endphp
            <div class="info-box progress-section">
                <h3>Progreso de Pagos</h3>
                <div class="info-row">
                    <span class="info-label">Total Empleados:</span>
                    <span class="info-value"><strong>{{ $resumen['total_empleados'] }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pagados:</span>
                    <span class="info-value estado-pagado">{{ $resumen['empleados_pagados'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pendientes:</span>
                    <span class="info-value estado-pendiente">{{ $resumen['empleados_pendientes'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Cancelados:</span>
                    <span class="info-value estado-cancelado">{{ $resumen['empleados_cancelados'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Progreso:</span>
                    <span class="info-value">{{ number_format($resumen['progreso_porcentaje'], 1) }}%</span>
                </div>
            </div>

            <!-- Resumen de Montos -->
            <div class="info-box totals-section">
                <h3>Resumen Financiero</h3>
                <div class="info-row">
                    <span class="info-label">Total Sueldos Base:</span>
                    <span class="info-value">Q{{ number_format($pagoSueldo->detalles->sum('sueldo_base'), 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total H. Extra:</span>
                    <span class="info-value">Q{{ number_format($pagoSueldo->detalles->sum(function($d) { return ($d->horas_extra ?? 0) * ($d->valor_hora_extra ?? 0); }), 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Comisiones:</span>
                    <span class="info-value">Q{{ number_format($pagoSueldo->detalles->sum('comisiones'), 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Bonificaciones:</span>
                    <span class="info-value">Q{{ number_format($pagoSueldo->detalles->sum('bonificaciones'), 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Descuentos:</span>
                    <span class="info-value">-Q{{ number_format($pagoSueldo->detalles->sum('deducciones'), 2) }}</span>
                </div>
                <div class="info-row" style="border-top: 2px solid #007bff; padding-top: 8px; margin-top: 8px;">
                    <span class="info-label"><strong>TOTAL LOTE:</strong></span>
                    <span class="info-value highlight-amount">Q{{ number_format($pagoSueldo->total_monto, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($pagoSueldo->observaciones)
    <!-- Observaciones -->
    <div class="info-box section-full">
        <h3>Observaciones del Lote</h3>
        <p style="margin: 0; padding: 10px; background-color: #e8f4f8; border-radius: 4px;">
            {{ $pagoSueldo->observaciones }}
        </p>
    </div>
    @endif

    <!-- Detalle de Empleados -->
    <div class="info-box section-full keep-together" style="margin-top: 20px;">
        <h3>Detalle de Empleados</h3>
        
        <table class="empleados-table">
            <thead>
                <tr>
                    <th style="width: 16%;">Empleado</th>
                    <th style="width: 9%;">Sueldo Base</th>
                    <th style="width: 5%;">H.E.</th>
                    <th style="width: 8%;">$/H</th>
                    <th style="width: 8%;">Com.</th>
                    <th style="width: 8%;">Bon.</th>
                    <th style="width: 8%;">Desc.</th>
                    <th style="width: 10%;">Total</th>
                    <th style="width: 12%;">Estado</th>
                    <th style="width: 19%;">Observaciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pagoSueldo->detalles as $detalle)
                <tr>
                    <td>
                        <strong>
                            @if($detalle->trabajador)
                                {{ $detalle->trabajador->nombre }} {{ $detalle->trabajador->apellido }}
                                <br><span class="small-text">Trabajador</span>
                            @elseif($detalle->usuario)
                                {{ $detalle->usuario->name }}
                                <br><span class="small-text">Vendedor</span>
                            @else
                                <span class="small-text">Empleado eliminado</span>
                            @endif
                        </strong>
                    </td>
                    <td class="text-right">{{ number_format($detalle->sueldo_base, 2) }}</td>
                    <td class="text-center">{{ $detalle->horas_extra ?? 0 }}</td>
                    <td class="text-right">{{ number_format($detalle->valor_hora_extra ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($detalle->comisiones ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($detalle->bonificaciones ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($detalle->deducciones ?? 0, 2) }}</td>
                    <td class="text-right"><strong>{{ number_format($detalle->total_pagar, 2) }}</strong></td>
                    <td class="text-center">
                        @if($detalle->estado === 'pagado')
                            <span class="estado-pagado">PAGADO</span>
                            @if($detalle->fecha_pago)
                                <br><span class="small-text">{{ \Carbon\Carbon::parse($detalle->fecha_pago)->format('d/m') }}</span>
                            @endif
                        @elseif($detalle->estado === 'pendiente')
                            <span class="estado-pendiente">PENDIENTE</span>
                        @elseif($detalle->estado === 'cancelado')
                            <span class="estado-cancelado">CANCELADO</span>
                        @endif
                    </td>
                    <td style="font-size: 8px;">
                        @if($detalle->observaciones)
                            <strong>Emp:</strong> {{ Str::limit($detalle->observaciones, 40) }}
                        @endif
                        @if($detalle->observaciones_pago && $detalle->estado === 'pagado')
                            @if($detalle->observaciones)<br>@endif
                            <strong>Pago:</strong> {{ Str::limit($detalle->observaciones_pago, 40) }}
                        @endif
                        @if(!$detalle->observaciones && !$detalle->observaciones_pago)
                            <span class="small-text">-</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Resumen Final -->
    <div class="info-grid" style="margin-top: 15px;">
        <div class="info-column">
            <div class="info-box" style="margin-bottom: 10px;">
                <h3>Montos Pagados</h3>
                @php
                    $pagados = $pagoSueldo->detalles->where('estado', 'pagado');
                    $pendientes = $pagoSueldo->detalles->where('estado', 'pendiente');
                @endphp
                <div class="info-row">
                    <span class="info-label">Pagado:</span>
                    <span class="info-value estado-pagado">Q{{ number_format($pagados->sum('total_pagar'), 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pendiente:</span>
                    <span class="info-value estado-pendiente">Q{{ number_format($pendientes->sum('total_pagar'), 2) }}</span>
                </div>
            </div>
        </div>
        
        <div class="info-column">
            <div class="info-box" style="margin-bottom: 10px;">
                <h3>Estadísticas</h3>
                <div class="info-row">
                    <span class="info-label">Procesados:</span>
                    <span class="info-value">{{ $resumen['empleados_pagados'] }} de {{ $resumen['total_empleados'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Promedio:</span>
                    <span class="info-value">Q{{ $resumen['total_empleados'] > 0 ? number_format($pagoSueldo->total_monto / $resumen['total_empleados'], 2) : '0.00' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        Reporte generado por {{ config('app.name', 'Sistema de Gestión') }} | {{ $fechaGeneracion }}
    </div>
</body>
</html>
