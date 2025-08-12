<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Metas de Ventas</title>
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
            font-size: 10px;
            line-height: 1.3;
            color: #333;
            padding: 15px;
            margin: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 12px;
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
        
        .meta-summary {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-bottom: 15px;
            gap: 8px;
        }
        
        .meta-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin: 3px;
            width: 160px;
            text-align: center;
            display: inline-block;
            vertical-align: top;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .meta-card h5 {
            font-size: 10px;
            margin-bottom: 8px;
            color: #007bff;
            font-weight: bold;
        }
        
        .progress-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            height: 12px;
            overflow: hidden;
            margin-bottom: 4px;
        }
        
        .progress-bar {
            height: 100%;
            text-align: center;
            line-height: 12px;
            color: white;
            font-size: 7px;
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
        
        .workers-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 7px;
        }
        
        .workers-table th,
        .workers-table td {
            border: 1px solid #ddd;
            padding: 3px;
            text-align: center;
        }
        
        .workers-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
            font-size: 8px;
        }
        
        .workers-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .progress-mini {
            width: 40px;
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            display: inline-block;
            margin: 1px 0;
        }
        
        .progress-mini .bar {
            height: 100%;
        }
        
        /* Aplicar colores a las mini barras también */
        .progress-mini .progress-success { background-color: #28a745; }
        .progress-mini .progress-warning { background-color: #ffc107; }
        .progress-mini .progress-info { background-color: #17a2b8; }
        .progress-mini .progress-primary { background-color: #007bff; }
        .progress-mini .progress-danger { background-color: #dc3545; }
        .progress-mini .progress-secondary { background-color: #6c757d; }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-left {
            text-align: left;
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
        
        .worker-name {
            max-width: 80px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .meta-details {
            font-size: 8px;
            line-height: 1.2;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($config && $config->logo)
            <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo" class="logo">
        @endif
        <h1>Reporte de Metas de Ventas</h1>
        <div class="subtitle">
            Período: {{ ucfirst($periodo) }} | 
            Fechas: {{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }} |
            Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Resumen de Metas -->
    <div class="meta-summary">
        @foreach($metasOriginales as $meta)
            <div class="meta-card">
                <h5>{{ substr($meta->nombre, 0, 18) }}</h5>
                <div class="progress-container">
                    <div class="progress-bar {{ $meta->clase_progreso }}" 
                         style="width: {{ min($meta->progreso_promedio ?? 0, 100) }}%">
                        {{ number_format($meta->progreso_promedio ?? 0, 0) }}%
                    </div>
                </div>
                <div class="meta-details">
                    <strong>{{ $config->currency_simbol ?? '$' }}{{ number_format($meta->monto_minimo, 2) }}</strong><br>
                    <small>{{ ucfirst($meta->periodo) }}</small>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Tabla de Trabajadores -->
    <table class="workers-table">
        <thead>
            <tr>
                <th style="width: 120px;">Trabajador</th>
                @foreach($metasOriginales as $meta)
                    <th style="width: 100px;">{{ substr($meta->nombre, 0, 15) }}</th>
                @endforeach
                <th style="width: 110px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($trabajadores as $trabajador)
                <tr>
                    <td class="text-left worker-name">{{ $trabajador->name }}</td>
                    @foreach($metasOriginales as $meta)
                        @php
                            $metaData = $trabajador->metasConProgreso->first(function($item) use ($meta) {
                                return $item['meta']->id == $meta->id;
                            });
                            $porcentaje = $metaData ? $metaData['porcentaje'] : 0;
                            $vendido = $metaData ? $metaData['ventas_para_meta'] : 0;
                            $claseProgreso = $metaData ? $metaData['clase_progreso'] : $meta->clase_progreso;
                        @endphp
                        <td>
                            <div class="progress-mini">
                                <div class="bar {{ $claseProgreso }}" 
                                     style="width: {{ $porcentaje }}%"></div>
                            </div>
                            <div style="font-size: 7px;">
                                {{ number_format($porcentaje, 0) }}%<br>
                                {{ $config->currency_simbol ?? '$' }}{{ number_format($vendido, 2) }}
                            </div>
                        </td>
                    @endforeach
                    <td class="text-right" style="font-size: 8px;">
                        <strong>{{ $config->currency_simbol ?? '$' }}{{ number_format($trabajador->metasConProgreso->sum('ventas_para_meta'), 2) }}</strong>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} - Sistema de Gestión Jireh
    </div>
</body>
</html>
