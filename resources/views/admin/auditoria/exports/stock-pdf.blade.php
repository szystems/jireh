<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Stock Tiempo Real</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2E7D32;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #2E7D32;
            margin: 0;
            font-size: 24px;
        }
        
        .header p {
            margin: 5px 0;
            color: #666;
        }
        
        .stats {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .stat-item {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 10px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }
        
        .stat-item h3 {
            margin: 0;
            font-size: 18px;
            color: #2E7D32;
        }
        
        .stat-item p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #666;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .table th {
            background-color: #2E7D32;
            color: white;
            padding: 8px 4px;
            text-align: center;
            font-size: 10px;
            border: 1px solid #ddd;
        }
        
        .table td {
            padding: 6px 4px;
            border: 1px solid #ddd;
            font-size: 9px;
            text-align: center;
        }
        
        .table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .stock-negativo {
            background-color: #ffebee !important;
            color: #c62828;
        }
        
        .stock-bajo {
            background-color: #fff3e0 !important;
            color: #ef6c00;
        }
        
        .stock-normal {
            color: #2e7d32;
        }
        
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        
        .badge-success {
            background-color: #4caf50;
            color: white;
        }
        
        .badge-danger {
            background-color: #f44336;
            color: white;
        }
        
        .badge-warning {
            background-color: #ff9800;
            color: white;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE STOCK EN TIEMPO REAL</h1>
        <p><strong>Fecha de generación:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
        <p><strong>Total de artículos:</strong> {{ count($reporteStock['articulos']) }}</p>
    </div>
    
    <div class="stats">
        <div class="stat-item">
            <h3>{{ $reporteStock['estadisticas']['stock_negativo'] }}</h3>
            <p>Stock Negativo</p>
        </div>
        <div class="stat-item">
            <h3>{{ $reporteStock['estadisticas']['stock_bajo'] }}</h3>
            <p>Stock Bajo (≤10)</p>
        </div>
        <div class="stat-item">
            <h3>{{ $reporteStock['estadisticas']['stock_normal'] }}</h3>
            <p>Stock Normal (>10)</p>
        </div>
        <div class="stat-item">
            <h3>{{ $reporteStock['estadisticas']['total_articulos'] }}</h3>
            <p>Total Artículos</p>
        </div>
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th style="width: 12%;">Código</th>
                <th style="width: 25%;">Artículo</th>
                <th style="width: 8%;">Tipo</th>
                <th style="width: 10%;">Stock Actual</th>
                <th style="width: 10%;">Stock Teórico</th>
                <th style="width: 8%;">Diferencia</th>
                <th style="width: 10%;">Estado</th>
                <th style="width: 8%;">Nivel</th>
                <th style="width: 9%;">Última Venta</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reporteStock['articulos'] as $index => $item)
                @if($index > 0 && $index % 30 == 0)
                    </tbody>
                    </table>
                    <div class="page-break"></div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 12%;">Código</th>
                                <th style="width: 25%;">Artículo</th>
                                <th style="width: 8%;">Tipo</th>
                                <th style="width: 10%;">Stock Actual</th>
                                <th style="width: 10%;">Stock Teórico</th>
                                <th style="width: 8%;">Diferencia</th>
                                <th style="width: 10%;">Estado</th>
                                <th style="width: 8%;">Nivel</th>
                                <th style="width: 9%;">Última Venta</th>
                            </tr>
                        </thead>
                        <tbody>
                @endif
                
                <tr class="{{ $item['stock_actual'] < 0 ? 'stock-negativo' : ($item['stock_actual'] <= 10 ? 'stock-bajo' : 'stock-normal') }}">
                    <td><strong>{{ $item['articulo']->codigo }}</strong></td>
                    <td style="text-align: left;">{{ $item['articulo']->nombre }}</td>
                    <td>{{ ucfirst($item['articulo']->tipo) }}</td>
                    <td>
                        <span class="badge {{ $item['stock_actual'] < 0 ? 'badge-danger' : ($item['stock_actual'] <= 10 ? 'badge-warning' : 'badge-success') }}">
                            {{ $item['stock_actual'] }}
                        </span>
                    </td>
                    <td>{{ $item['stock_teorico'] }}</td>
                    <td>
                        @if($item['diferencia'] != 0)
                            <span class="badge badge-danger">{{ $item['diferencia'] }}</span>
                        @else
                            <span class="badge badge-success">0</span>
                        @endif
                    </td>
                    <td>
                        @if($item['consistente'])
                            <span class="badge badge-success">OK</span>
                        @else
                            <span class="badge badge-danger">ERROR</span>
                        @endif
                    </td>
                    <td>
                        @if($item['stock_actual'] < 0)
                            <span class="badge badge-danger">CRÍTICO</span>
                        @elseif($item['stock_actual'] <= 10)
                            <span class="badge badge-warning">BAJO</span>
                        @else
                            <span class="badge badge-success">NORMAL</span>
                        @endif
                    </td>
                    <td>
                        @if($item['ultima_venta'])
                            {{ $item['ultima_venta']->format('d/m/Y') }}
                        @else
                            <span style="color: #999;">Sin ventas</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p><strong>Sistema de Gestión JIREH</strong> - Reporte generado automáticamente</p>
        <p>Fecha: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
