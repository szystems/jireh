<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lote de Pago - {{ $lotePago->numero_lote }}</title>
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
            margin-bottom: 15px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        
        .logo {
            max-width: 80px;
            max-height: 50px;
            margin-bottom: 8px;
        }
        
        .header h1 {
            color: #007bff;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 9px;
            margin-bottom: 5px;
        }
        
        .lote-info {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .lote-info h3 {
            color: #007bff;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #007bff;
            padding-bottom: 5px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
            border-spacing: 10px;
        }
        
        .info-column {
            display: table-cell;
            width: 48%;
            vertical-align: top;
            background-color: #fdfdfd;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            padding: 12px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 4px 0;
            border-bottom: 1px solid #eee;
        }
        
        .info-label {
            font-weight: bold;
            color: #495057;
            width: 50%;
            font-size: 9px;
        }
        
        .info-value {
            color: #212529;
            width: 50%;
            text-align: right;
            font-size: 9px;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 8px;
            font-weight: bold;
            text-align: center;
            white-space: nowrap;
            border-radius: 4px;
        }
        
        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        
        .comisiones-section {
            margin-top: 20px;
        }
        
        .comisiones-section h3 {
            color: #007bff;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #007bff;
            padding-bottom: 5px;
        }
        
        .comisiones-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8px;
        }
        
        .comisiones-table th,
        .comisiones-table td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
        }
        
        .comisiones-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #333;
            font-size: 9px;
        }
        
        .comisiones-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .comisiones-table td.text-left {
            text-align: left;
        }
        
        .comisiones-table td.text-right {
            text-align: right;
        }
        
        .monto-destacado {
            font-weight: bold;
            color: #28a745;
            font-size: 11px;
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
        
        .observaciones {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 10px;
            margin-top: 15px;
        }
        
        .observaciones h4 {
            color: #856404;
            font-size: 10px;
            margin-bottom: 5px;
        }
        
        .observaciones p {
            color: #856404;
            font-size: 9px;
            margin: 0;
        }
        
        .comprobante-info {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 5px;
            padding: 8px;
            margin-top: 10px;
            font-size: 8px;
        }
        
        .totales-destacados {
            background-color: #e7f3ff;
            border: 2px solid #007bff;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            text-align: center;
        }
        
        .totales-destacados h3 {
            color: #007bff;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .total-principal {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
            margin: 5px 0;
        }
        
        .total-secundario {
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($config && $config->logo)
            <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo" class="logo">
        @endif
        <h1>Lote de Pago Detallado</h1>
        <div class="subtitle">
            {{ $lotePago->numero_lote }} | Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Información Principal del Lote -->
    <div class="lote-info">
        <h3>Información del Lote de Pago</h3>
        <div class="info-grid">
            <div class="info-column">
                <div class="info-row">
                    <span class="info-label">Número de Lote:</span>
                    <span class="info-value">{{ $lotePago->numero_lote }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha de Pago:</span>
                    <span class="info-value">{{ $lotePago->fecha_pago ? $lotePago->fecha_pago->format('d/m/Y') : 'No definida' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Método de Pago:</span>
                    <span class="info-value">
                        <span class="badge badge-info">{{ ucfirst($lotePago->metodo_pago) }}</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Referencia:</span>
                    <span class="info-value">{{ $lotePago->referencia ?: 'Sin referencia' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Estado:</span>
                    <span class="info-value">
                        @if($lotePago->estado == 'procesando')
                            <span class="badge badge-warning">Procesando</span>
                        @elseif($lotePago->estado == 'completado')
                            <span class="badge badge-success">Completado</span>
                        @else
                            <span class="badge badge-danger">Anulado</span>
                        @endif
                    </span>
                </div>
            </div>
            <div class="info-column">
                <div class="info-row">
                    <span class="info-label">Cantidad de Comisiones:</span>
                    <span class="info-value">{{ $lotePago->cantidad_comisiones }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Monto Total:</span>
                    <span class="info-value">
                        <span class="monto-destacado">{{ $config->currency_simbol ?? '$' }}{{ number_format($lotePago->monto_total, 2) }}</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Usuario Creador:</span>
                    <span class="info-value">{{ $lotePago->usuario->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha Creación:</span>
                    <span class="info-value">{{ $lotePago->created_at ? $lotePago->created_at->format('d/m/Y H:i') : 'No disponible' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Comprobante (si existe) -->
    @if($lotePago->comprobante_imagen)
    <div class="comprobante-info">
        <strong>Comprobante:</strong> {{ $lotePago->comprobante_imagen }}
    </div>
    @endif

    <!-- Comisiones Incluidas en el Lote -->
    <div class="comisiones-section">
        <h3>Comisiones Incluidas en el Lote</h3>
        
        <table class="comisiones-table">
            <thead>
                <tr>
                    <th style="width: 8%;">ID</th>
                    <th style="width: 20%;">Trabajador</th>
                    <th style="width: 12%;">Tipo Comisión</th>
                    <th style="width: 15%;">Venta Asociada</th>
                    <th style="width: 12%;">Cliente</th>
                    <th style="width: 10%;">Fecha Cálculo</th>
                    <th style="width: 10%;">Monto</th>
                    <th style="width: 8%;">Estado</th>
                    <th style="width: 5%;">%</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lotePago->pagosComisiones as $pago)
                    @php
                        $comision = $pago->comision;
                        $trabajador = $comision->commissionable;
                    @endphp
                    <tr>
                        <td>{{ $comision->id }}</td>
                        <td class="text-left">
                            @if($trabajador instanceof \App\Models\User)
                                {{ $trabajador->name }} <small>(Vendedor)</small>
                            @elseif($trabajador instanceof \App\Models\Trabajador)
                                {{ $trabajador->nombre }}
                                @if(!empty($trabajador->posicion))
                                    <small>({{ ucfirst($trabajador->posicion) }})</small>
                                @else
                                    <small>(Trabajador)</small>
                                @endif
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-info">{{ ucfirst($comision->tipo_comision) }}</span>
                        </td>
                        <td class="text-left">
                            @if($comision->venta)
                                Venta #{{ $comision->venta->id }}
                                @if($comision->venta->fecha_venta)
                                    <br><small>{{ $comision->venta->fecha_venta->format('d/m/Y') }}</small>
                                @endif
                            @else
                                N/A
                            @endif
                        </td>
                        <td class="text-left">
                            @if($comision->venta && $comision->venta->cliente)
                                {{ $comision->venta->cliente->nombre }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $comision->fecha_calculo ? $comision->fecha_calculo->format('d/m/Y') : 'N/A' }}</td>
                        <td class="text-right monto-destacado">
                            {{ $config->currency_simbol ?? '$' }}{{ number_format($comision->monto, 2) }}
                        </td>
                        <td>
                            @if($pago->estado == 'completado')
                                <span class="badge badge-success">Pagado</span>
                            @else
                                <span class="badge badge-warning">Pendiente</span>
                            @endif
                        </td>
                        <td>{{ number_format($comision->porcentaje_comision, 1) }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 20px; color: #666;">
                            No se encontraron comisiones en este lote
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($lotePago->pagosComisiones->count() > 0)
            <tfoot>
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td colspan="6" class="text-right">TOTAL DEL LOTE:</td>
                    <td class="text-right">
                        <span class="monto-destacado">
                            {{ $config->currency_simbol ?? '$' }}{{ number_format($lotePago->monto_total, 2) }}
                        </span>
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <!-- Observaciones (si existen) -->
    @if($lotePago->observaciones)
    <div class="observaciones">
        <h4>Observaciones:</h4>
        <p>{{ $lotePago->observaciones }}</p>
    </div>
    @endif

    <div class="footer">
        Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} - Sistema de Gestión Jireh - Lote: {{ $lotePago->numero_lote }}
    </div>
</body>
</html>
