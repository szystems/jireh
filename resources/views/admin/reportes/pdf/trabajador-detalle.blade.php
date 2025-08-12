<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Individual - {{ $trabajador->name }}</title>
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
        
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin: 15px 0 10px 0;
            border-bottom: 1px solid #007bff;
            padding-bottom: 3px;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
            color: #333;
            padding: 15px;
            margin: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #007bff;
            padding-bottom: 8px;
        }
        
        .logo {
            max-width: 80px;
            max-height: 50px;
            margin-bottom: 5px;
        }
        
        .header h1 {
            color: #007bff;
            font-size: 14px;
            margin-bottom: 3px;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 8px;
            margin-bottom: 5px;
        }
        
        .worker-info {
            background-color: #f8f9fa;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        
        .worker-info h2 {
            color: #007bff;
            font-size: 12px;
            margin-bottom: 5px;
        }
        
        .worker-info .stats {
            width: 100%;
            margin-top: 8px;
            overflow: hidden;
        }
        
        .stat-item {
            float: left;
            width: 33.33%;
            text-align: center;
            padding: 0 5px;
            box-sizing: border-box;
        }
        
        .stat-value {
            font-size: 11px;
            font-weight: bold;
            color: #007bff;
        }
        
        .stat-label {
            font-size: 7px;
            color: #666;
        }
        
        .meta-cards {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            margin-bottom: 15px;
        }
        
        .meta-card {
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 8px;
            width: 160px;
            text-align: center;
            display: inline-block;
            vertical-align: top;
        }
        
        .meta-card h5 {
            font-size: 9px;
            margin-bottom: 5px;
            color: #333;
        }
        
        .progress-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            height: 15px;
            overflow: hidden;
            margin-bottom: 5px;
        }
        
        .progress-bar {
            height: 100%;
            text-align: center;
            line-height: 15px;
            color: white;
            font-size: 8px;
            font-weight: bold;
        }
        
        .bg-primary { background-color: #007bff; }
        .bg-success { background-color: #28a745; }
        .bg-info { background-color: #17a2b8; }
        .bg-warning { background-color: #ffc107; color: #212529; }
        .bg-danger { background-color: #dc3545; }
        .bg-secondary { background-color: #6c757d; }
        .bg-dark { background-color: #343a40; }
        
        /* Clases adicionales para compatibilidad */
        .progress-success { background-color: #28a745; }
        .progress-warning { background-color: #ffc107; color: #212529; }
        .progress-info { background-color: #17a2b8; }
        .progress-primary { background-color: #007bff; }
        .progress-danger { background-color: #dc3545; }
        .progress-secondary { background-color: #6c757d; }
        
        .meta-details {
            font-size: 7px;
            line-height: 1.2;
        }
        
        .ventas-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8px;
        }
        
        .ventas-table th,
        .ventas-table td {
            border: 1px solid #ddd;
            padding: 2px;
        }
        
        .ventas-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
            text-align: center;
            font-size: 8px;
        }
        
        .ventas-table td {
            text-align: left;
        }
        
        .ventas-table .fecha-col {
            text-align: center !important;
        }
        
        .ventas-table .monto-col {
            text-align: right !important;
        }
        
        .ventas-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .footer {
            margin-top: 30px;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .two-column {
            display: flex;
            gap: 10px;
        }
        
        .column {
            flex: 1;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($config && $config->logo)
            <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo" class="logo">
        @endif
        <h1>Reporte Individual de Metas</h1>
        <div class="subtitle">
            Período: {{ ucfirst($periodo) }} | 
            Fechas: {{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }} |
            Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Información del Trabajador centrada -->
    <div class="worker-info">
        <h2>{{ $trabajador->name }}</h2>
        <div class="stats">
            <div class="stat-item">
                <div class="stat-value">{{ $config->currency_simbol ?? '$' }}{{ number_format($totalVentas, 2) }}</div>
                <div class="stat-label">Total Ventas</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ count($ventas) }}</div>
                <div class="stat-label">Num. Ventas</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ count($metasConProgreso) }}</div>
                <div class="stat-label">Metas</div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>

    <!-- Progreso de Metas -->
    <div class="section-title">Progreso de Metas</div>
    <div class="meta-cards">
                @foreach($metasConProgreso as $meta)
                    <div class="meta-card">
                        <h5>{{ substr($meta['meta']->nombre, 0, 18) }}</h5>
                        <div class="progress-container">
                            <div class="progress-bar {{ $meta['clase_progreso'] }}" 
                                 style="width: {{ $meta['porcentaje'] }}%">
                                {{ number_format($meta['porcentaje'], 0) }}%
                            </div>
                        </div>
                        <div class="meta-details">
                            <strong>Vendido:</strong> {{ $config->currency_simbol ?? '$' }}{{ number_format($meta['ventas_para_meta'], 2) }}<br>
                            <strong>Meta:</strong> {{ $config->currency_simbol ?? '$' }}{{ number_format($meta['meta']->monto_minimo, 2) }}<br>
                            <strong>Falta:</strong> {{ $config->currency_simbol ?? '$' }}{{ number_format($meta['faltante'], 2) }}
                        </div>
                    </div>
                @endforeach
            </div>

    <!-- Detalle de Ventas -->
    @if(count($ventas) > 0)
        <div class="section-title">Detalle de Ventas ({{ count($ventas) }} ventas)</div>
        <table class="ventas-table">
            <thead>
                <tr>
                    <th class="fecha-col" style="width: 60px;">Fecha</th>
                    <th>Cliente</th>
                    <th class="monto-col" style="width: 50px;">Subtotal</th>
                    <th class="monto-col" style="width: 50px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventas->take(20) as $venta)
                    @php
                        $subtotalVenta = $venta->detalleVentas->sum('sub_total');
                        $totalVenta = $venta->total;
                        $nombreCliente = $venta->cliente->nombre ?? $venta->nombre ?? 'Cliente no especificado';
                        $fechaVenta = \Carbon\Carbon::parse($venta->fecha)->format('d/m/y');
                    @endphp
                    <tr>
                        <td class="fecha-col">{{ $fechaVenta }}</td>
                        <td>{{ substr($nombreCliente, 0, 35) }}</td>
                        <td class="monto-col">{{ $config->currency_simbol ?? '$' }}{{ number_format($subtotalVenta, 2) }}</td>
                        <td class="monto-col">{{ $config->currency_simbol ?? '$' }}{{ number_format($totalVenta, 2) }}</td>
                    </tr>
                @endforeach
                @if(count($ventas) > 20)
                    <tr style="background-color: #e9ecef;">
                        <td colspan="4" class="text-center" style="font-style: italic;">
                            ... y {{ count($ventas) - 20 }} ventas más
                        </td>
                    </tr>
                @endif
                <tr style="border-top: 2px solid #007bff; font-weight: bold; background-color: #f8f9fa;">
                    @php
                        $totalSubtotal = $ventas->sum(function($venta) {
                            return $venta->detalleVentas->sum('sub_total');
                        });
                        $totalGeneral = $ventas->sum('total');
                    @endphp
                    <td colspan="2" class="monto-col"><strong>TOTALES:</strong></td>
                    <td class="monto-col">{{ $config->currency_simbol ?? '$' }}{{ number_format($totalSubtotal, 2) }}</td>
                    <td class="monto-col">{{ $config->currency_simbol ?? '$' }}{{ number_format($totalGeneral, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="section-title">Sin Ventas</div>
        <p style="text-align: center; color: #666; padding: 10px; font-size: 8px;">
            No hay ventas registradas para este trabajador en el período seleccionado.
        </p>
    @endif

    <div class="footer">
        Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} - Sistema de Gestión Jireh
    </div>
</body>
</html>
