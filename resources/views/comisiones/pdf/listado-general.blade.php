<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Comisiones</title>
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
        
        .comisiones-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 8px;
        }
        
        .comisiones-table th,
        .comisiones-table td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: left;
            vertical-align: top;
        }
        
        .comisiones-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 8px;
            color: #495057;
        }
        
        .comisiones-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .comisiones-table tbody tr:hover {
            background-color: #e9ecef;
        }
        
        .badge {
            padding: 2px 4px;
            border-radius: 3px;
            color: white;
            font-weight: bold;
            font-size: 7px;
        }
        
        .badge-primary { background-color: #007bff; }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
        .badge-info { background-color: #17a2b8; }
        .badge-secondary { background-color: #6c757d; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        .footer {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 8px;
            color: #666;
        }
        
        .meta-info {
            font-size: 7px;
            color: #666;
        }
        
        .meta-badge {
            display: inline-block;
            background: #e9ecef;
            padding: 1px 3px;
            border-radius: 2px;
            margin-right: 2px;
            font-size: 6px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Comisiones</h1>
        <div class="subtitle">
            Generado el: {{ $fechaGeneracion }}<br>
            {{ $config->company_name ?? 'Sistema de Gestión' }}
        </div>
    </div>

    <!-- Estadísticas resumen -->
    <div class="summary-cards">
        <div class="summary-card">
            <h5>Total</h5>
            <div class="value text-primary">{{ $config->currency_simbol }} {{ number_format($totalComisiones, 2) }}</div>
            <div class="label">Monto Total</div>
        </div>
        <div class="summary-card">
            <h5>Cantidad</h5>
            <div class="value text-info">{{ $cantidadComisiones }}</div>
            <div class="label">Comisiones</div>
        </div>
        <div class="summary-card">
            <h5>Pendientes</h5>
            <div class="value text-warning">{{ $pendientes }}</div>
            <div class="label">Por Pagar</div>
        </div>
        <div class="summary-card">
            <h5>Pagadas</h5>
            <div class="value text-success">{{ $pagadas }}</div>
            <div class="label">Completadas</div>
        </div>
        <div class="summary-card">
            <h5>Canceladas</h5>
            <div class="value text-danger">{{ $canceladas }}</div>
            <div class="label">Anuladas</div>
        </div>
    </div>

    <!-- Filtros aplicados -->
    @if($filtrosAplicados !== 'Sin filtros aplicados')
    <div class="filters-applied">
        <h6>Filtros Aplicados</h6>
        <div>{{ $filtrosAplicados }}</div>
    </div>
    @endif

    <!-- Tabla de comisiones -->
    <table class="comisiones-table">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="18%">Beneficiario</th>
                <th width="10%">Tipo</th>
                <th width="15%">Comisión</th>
                <th width="18%">Meta/Detalles</th>
                <th width="8%">Monto</th>
                <th width="8%">Estado</th>
                <th width="10%">Fecha</th>
                <th width="8%">Venta</th>
            </tr>
        </thead>
        <tbody>
            @forelse($comisiones as $comision)
            <tr>
                <td class="text-center">{{ $comision['id'] }}</td>
                <td>
                    <strong>{{ $comision['beneficiario_nombre'] }}</strong>
                    <div class="meta-info">{{ $comision['tipo_receptor'] }}</div>
                </td>
                <td>
                    @if($comision['tipo_comision'] === 'meta_venta' || $comision['tipo_comision'] === 'venta_meta')
                        <span class="badge badge-primary">Meta</span>
                    @elseif($comision['tipo_comision'] === 'mecanico')
                        <span class="badge badge-info">Mecánico</span>
                    @elseif($comision['tipo_comision'] === 'carwash')
                        <span class="badge badge-secondary">Car Wash</span>
                    @else
                        <span class="badge badge-warning">{{ ucfirst($comision['tipo_comision']) }}</span>
                    @endif
                </td>
                <td>{{ $comision['tipo_comision_texto'] }}</td>
                <td>
                    @if($comision['meta_info'])
                        <div class="meta-badge">{{ $comision['meta_info']['nombre'] }}</div>
                        <div class="meta-info">{{ $comision['meta_info']['rango'] }}</div>
                    @else
                        <span class="meta-info">-</span>
                    @endif
                </td>
                <td class="text-right">
                    <strong>{{ $config->currency_simbol }} {{ number_format($comision['monto'], 2) }}</strong>
                </td>
                <td class="text-center">
                    @if($comision['estado'] === 'pendiente')
                        <span class="badge badge-warning">Pendiente</span>
                    @elseif($comision['estado'] === 'pagado')
                        <span class="badge badge-success">Pagado</span>
                    @elseif($comision['estado'] === 'cancelado')
                        <span class="badge badge-danger">Cancelado</span>
                    @else
                        <span class="badge badge-secondary">{{ ucfirst($comision['estado']) }}</span>
                    @endif
                </td>
                <td class="text-center">
                    {{ $comision['fecha_calculo'] ? \Carbon\Carbon::parse($comision['fecha_calculo'])->format('d/m/Y') : '-' }}
                </td>
                <td class="text-center">
                    @if($comision['venta_id'])
                        #{{ $comision['venta_id'] }}
                        @if($comision['fecha_venta'])
                            <div class="meta-info">{{ \Carbon\Carbon::parse($comision['fecha_venta'])->format('d/m/Y') }}</div>
                        @endif
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">
                    <em>No se encontraron comisiones con los filtros aplicados</em>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Reporte generado por {{ $config->company_name ?? 'Sistema de Gestión' }} | {{ $fechaGeneracion }}
    </div>
</body>
</html>
