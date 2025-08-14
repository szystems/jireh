<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Lotes de Pago</title>
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
        
        .summary-cards {
            display: flex;
            flex-wrap: nowrap;
            justify-content: center;
            margin-bottom: 15px;
            gap: 5px;
        }
        
        .summary-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 8px;
            margin: 2px;
            width: 90px;
            text-align: center;
            display: inline-block;
            vertical-align: top;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .summary-card h5 {
            font-size: 9px;
            margin-bottom: 4px;
            color: #007bff;
            font-weight: bold;
        }
        
        .summary-card .value {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .summary-card .label {
            font-size: 7px;
            color: #666;
        }
        
        .text-primary { color: #007bff; }
        .text-success { color: #28a745; }
        .text-warning { color: #ffc107; }
        .text-danger { color: #dc3545; }
        .text-info { color: #17a2b8; }
        
        .filters-applied {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 8px;
            margin-bottom: 15px;
            font-size: 8px;
        }
        
        .filters-applied h6 {
            margin-bottom: 5px;
            color: #495057;
            font-size: 9px;
        }
        
        .lotes-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 8px;
        }
        
        .lotes-table th,
        .lotes-table td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: center;
        }
        
        .lotes-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
            font-size: 8px;
        }
        
        .lotes-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .lotes-table td.text-left {
            text-align: left;
        }
        
        .lotes-table td.text-right {
            text-align: right;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7px;
            font-weight: bold;
            text-align: center;
            white-space: nowrap;
            border-radius: 3px;
        }
        
        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        
        .footer {
            margin-top: 20px;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .numero-lote {
            font-weight: bold;
            color: #007bff;
        }
        
        .monto-total {
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($config && $config->logo)
            <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo" class="logo">
        @endif
        <h1>Reporte de Lotes de Pago</h1>
        <div class="subtitle">
            Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Filtros Aplicados -->
    @if(count($filtrosAplicados) > 0)
    <div class="filters-applied">
        <h6><i class="bi bi-funnel"></i> Filtros Aplicados:</h6>
        {{ implode(' | ', $filtrosAplicados) }}
    </div>
    @endif

    <!-- Resumen Estadístico -->
    <div class="summary-cards">
        <div class="summary-card">
            <h5>Total Lotes</h5>
            <div class="value text-primary">{{ number_format($estadisticas['total_lotes']) }}</div>
            <div class="label">lotes</div>
        </div>
        
        <div class="summary-card">
            <h5>Monto Total</h5>
            <div class="value text-success">{{ $config->currency_simbol ?? '$' }}{{ number_format($estadisticas['monto_total'], 2) }}</div>
            <div class="label">pagado</div>
        </div>
        
        <div class="summary-card">
            <h5>Comisiones</h5>
            <div class="value text-info">{{ number_format($estadisticas['total_comisiones']) }}</div>
            <div class="label">comisiones</div>
        </div>
        
        <div class="summary-card">
            <h5>Completados</h5>
            <div class="value text-success">{{ $estadisticas['por_estado']['completado'] }}</div>
            <div class="label">lotes</div>
        </div>
        
        <div class="summary-card">
            <h5>Procesando</h5>
            <div class="value text-warning">{{ $estadisticas['por_estado']['procesando'] }}</div>
            <div class="label">lotes</div>
        </div>
        
        <div class="summary-card">
            <h5>Anulados</h5>
            <div class="value text-danger">{{ $estadisticas['por_estado']['anulado'] }}</div>
            <div class="label">lotes</div>
        </div>
    </div>

    <!-- Tabla de Lotes -->
    <table class="lotes-table">
        <thead>
            <tr>
                <th style="width: 15%;">Número Lote</th>
                <th style="width: 12%;">Fecha Pago</th>
                <th style="width: 12%;">Método</th>
                <th style="width: 10%;">Estado</th>
                <th style="width: 8%;">Comisiones</th>
                <th style="width: 15%;">Monto Total</th>
                <th style="width: 15%;">Usuario</th>
                <th style="width: 13%;">Referencia</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lotesPago as $lote)
                <tr>
                    <td class="text-left numero-lote">{{ $lote->numero_lote }}</td>
                    <td>{{ $lote->fecha_pago ? $lote->fecha_pago->format('d/m/Y') : 'N/A' }}</td>
                    <td>
                        <span class="badge badge-info">{{ ucfirst($lote->metodo_pago) }}</span>
                    </td>
                    <td>
                        @if($lote->estado == 'procesando')
                            <span class="badge badge-warning">Procesando</span>
                        @elseif($lote->estado == 'completado')
                            <span class="badge badge-success">Completado</span>
                        @else
                            <span class="badge badge-danger">Anulado</span>
                        @endif
                    </td>
                    <td>{{ $lote->cantidad_comisiones }}</td>
                    <td class="text-right monto-total">
                        {{ $config->currency_simbol ?? '$' }}{{ number_format($lote->monto_total, 2) }}
                    </td>
                    <td class="text-left">{{ $lote->usuario->name ?? 'N/A' }}</td>
                    <td class="text-left">{{ $lote->referencia ? substr($lote->referencia, 0, 20) : 'Sin referencia' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #666;">
                        No se encontraron lotes de pago con los filtros aplicados
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($lotesPago->count() > 0)
        <tfoot>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="4" class="text-right">TOTALES:</td>
                <td>{{ $estadisticas['total_comisiones'] }}</td>
                <td class="text-right">{{ $config->currency_simbol ?? '$' }}{{ number_format($estadisticas['monto_total'], 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} - Sistema de Gestión Jireh - Lotes de Pago
    </div>
</body>
</html>
