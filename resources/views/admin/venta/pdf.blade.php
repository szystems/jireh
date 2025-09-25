<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas</title>
    <style>
        /* Estilos CSS para el PDF */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .logo {
            max-height: 60px;
            margin-bottom: 10px;
        }
        h1, h2, h3 {
            margin: 5px 0;
            color: #333;
        }
        .filters {
            margin: 15px 0;
            padding: 8px;
            background-color: #f5f5f5;
            border-radius: 5px;
            font-size: 11px;
        }
        .summary {
            margin: 15px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 11px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 9px;
            border-radius: 3px;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($config->logo)
            <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo" class="logo">
        @endif
        <h1>REPORTE DE VENTAS</h1>
        <h3>{{ $config->nombre_negocio }}</h3>
    </div>

    <div class="filters">
        <strong>Filtros aplicados:</strong>
        <ul>
            @if(isset($filters['fecha_desde']) && $filters['fecha_desde'])
                <li>Desde: {{ $filters['fecha_desde'] }}</li>
            @endif
            @if(isset($filters['fecha_hasta']) && $filters['fecha_hasta'])
                <li>Hasta: {{ $filters['fecha_hasta'] }}</li>
            @endif
            @if(isset($filters['numero_factura']) && $filters['numero_factura'])
                <li>Número de factura: {{ $filters['numero_factura'] }}</li>
            @endif
            @if(isset($filters['cliente']) && $filters['cliente'])
                <li>Cliente: {{ $filters['cliente'] }}</li>
            @endif
            @if(isset($filters['tipo_venta']) && $filters['tipo_venta'])
                <li>Tipo de venta: {{ $filters['tipo_venta'] }}</li>
            @endif
            @if(isset($filters['usuario']) && $filters['usuario'])
                <li>Usuario: {{ $filters['usuario'] }}</li>
            @endif
            @if(isset($filters['estado']) && $filters['estado'])
                <li>Estado: {{ $filters['estado'] }}</li>
            @endif
            @if(isset($filters['estado_pago']) && $filters['estado_pago'])
                <li>Estado de pago: {{ $filters['estado_pago'] }}</li>
            @endif
            @if(isset($filters['iva']) && $filters['iva'] && $filters['iva'] != '')
                <li>IVA: {{ $filters['iva'] == 'con_iva' ? 'Con IVA' : ($filters['iva'] == 'sin_iva' ? 'Sin IVA' : 'Todas') }}</li>
            @endif
        </ul>
    </div>

    <div class="summary">
        <h3>Resumen</h3>
        <table>
            <tr>
                <td width="25%"><strong>Total Ventas:</strong> {{ $ventas->count() }}</td>
                <td width="25%"><strong>Ventas Canceladas:</strong> {{ $ventas->where('estado', 0)->count() }}</td>
                <td width="25%"><strong>Ventas Pagadas:</strong> {{ $ventas->where('estado_pago', 'pagado')->count() }}</td>
                <td width="25%"><strong>Ventas Pendientes:</strong> {{ $ventas->where('estado_pago', 'pendiente')->count() }}</td>
            </tr>
        </table>
    </div>

    <!-- Resumen de Tipos de Pago -->
    <div class="summary" style="margin-top: 15px;">
        <h3>Resumen Financiero</h3>
        
        @if(!empty($resumen['total_tipos_pago']))
        <div style="margin-bottom: 10px;">
            <h4 style="margin-bottom: 8px; font-size: 14px;">Totales por Tipo de Pago:</h4>
            <table style="width: 100%; font-size: 12px; border: none;">
                @php $contador = 0; @endphp
                @foreach($resumen['total_tipos_pago'] as $metodo => $total)
                    @if($contador % 2 == 0)
                        <tr>
                    @endif
                    <td width="20%" style="padding: 3px; border: none;"><strong>{{ $metodo }}:</strong></td>
                    <td width="30%" style="padding: 3px; border: none;">{{ $config->currency_simbol }}{{ number_format($total, 2) }}</td>
                    @php $contador++; @endphp
                    @if($contador % 2 == 0 || $loop->last)
                        @if($loop->last && $contador % 2 != 0)
                            <td width="20%" style="border: none;"></td>
                            <td width="30%" style="border: none;"></td>
                        @endif
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>
        @else
        <div style="margin-bottom: 10px;">
            <p style="font-size: 12px; color: #666;">No hay tipos de pago registrados para mostrar.</p>
        </div>
        @endif
        
        <table style="font-size: 12px;">
            <tr>
                <td width="25%"><strong>Total Ventas:</strong> {{ $config->currency_simbol }}{{ number_format($resumen['total_ventas'], 2) }}</td>
                <td width="25%"><strong>Total Pagos:</strong> {{ $config->currency_simbol }}{{ number_format($resumen['total_pagos'], 2) }}</td>
                <td width="25%"><strong>Diferencia:</strong> 
                    <span style="color: {{ $resumen['diferencia'] >= 0 ? 'green' : 'red' }};">
                        {{ $config->currency_simbol }}{{ number_format($resumen['diferencia'], 2) }}
                    </span>
                </td>
                <td width="25%">
                    @if(Auth::user()->role_as != 1)
                        <strong>Ganancia Neta:</strong> {{ $config->currency_simbol }}{{ number_format($resumen['total_ganancia_neta'], 2) }}
                    @endif
                </td>
            </tr>
            @if(isset($filters['iva']) && $filters['iva'] != '' && Auth::user()->role_as != 1)
            <tr>
                <td width="25%"><strong>Ganancia (IVA considerado):</strong></td>
                <td colspan="3">{{ $config->currency_simbol }}{{ number_format($resumen['total_ganancia_neta_con_iva'], 2) }}</td>
            </tr>
            @endif
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="8%">Fecha</th>
                <th width="12%">Factura</th>
                <th width="13%">Cliente</th>
                <th width="10%">Tipo</th>
                <th width="10%">Vendedor</th>
                <th width="7%">Estado</th>
                <th width="10%">Pago</th>
                <th width="10%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
                @php
                    $ventaTotal = $venta->total; // Usar el total directo de la venta
                @endphp
                <tr>
                    <td>{{ $venta->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                    <td>{{ $venta->numero_factura ?? 'N/A' }}</td>
                    <td>
                        {{ $venta->cliente->nombre ?? 'Cliente no disponible' }}
                        @if(isset($venta->vehiculo))
                            <br><small>{{ $venta->vehiculo->marca }} {{ $venta->vehiculo->modelo }} - {{ $venta->vehiculo->placa }}</small>
                        @endif
                    </td>
                    <td>{{ $venta->tipo_venta }}</td>
                    <td>{{ $venta->usuario->name }}</td>
                    <td>
                        @if($venta->estado)
                            <span class="badge badge-success">Activa</span>
                        @else
                            <span class="badge badge-danger">Cancelada</span>
                        @endif
                    </td>
                    <td>
                        @if($venta->estado_pago == 'pagado')
                            <span class="badge badge-success">Pagado</span>
                        @else
                            <span class="badge badge-warning">Pendiente</span>
                        @endif
                    </td>
                    <td class="text-right">
                        {{ $config->currency_simbol }}{{ number_format($ventaTotal, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="8" class="text-right"><strong>TOTAL VENTAS:</strong></td>
                <td class="text-right">{{ $config->currency_simbol }}{{ number_format($resumen['total_ventas'], 2) }}</td>
            </tr>
            @if(Auth::user()->role_as != 1)
                <tr class="total-row">
                    <td colspan="8" class="text-right"><strong>TOTAL GANANCIAS:</strong></td>
                    <td class="text-right">{{ $config->currency_simbol }}{{ number_format($resumen['total_ganancia_neta'], 2) }}</td>
                </tr>
                @if(isset($filters['iva']) && $filters['iva'] != '')
                <tr class="total-row">
                    <td colspan="8" class="text-right"><strong>GANANCIAS (IVA Considerado):</strong></td>
                    <td class="text-right">{{ $config->currency_simbol }}{{ number_format($resumen['total_ganancia_neta_con_iva'], 2) }}</td>
                </tr>
                @endif
            @endif
        </tfoot>
    </table>

    <div class="footer">
        <p>Documento generado el {{ date('Y-m-d H:i:s') }}</p>
        <p>{{ $config->nombre_negocio }} | {{ $config->telefono }}</p>
    </div>
</body>
</html>
