<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de Comisión #{{ $comision->id }}</title>
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
            margin-bottom: 20px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        
        .header h1 {
            color: #007bff;
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .header .subtitle {
            color: #666;
            font-size: 10px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
        
        .info-box {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }
        
        .info-box h3 {
            color: #007bff;
            font-size: 12px;
            margin-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 5px;
        }
        
        .info-row {
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }
        
        .info-label {
            font-weight: bold;
            color: #495057;
            width: 40%;
        }
        
        .info-value {
            color: #212529;
            width: 60%;
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
        
        .venta-details {
            background-color: #e8f5e8;
            border-left: 4px solid #28a745;
        }
        
        .pago-details {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
        }
        
        .pago-details.pagado {
            background-color: #d1ecf1;
            border-left-color: #17a2b8;
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
        
        .working-type {
            font-size: 9px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Detalle de Comisión #{{ $comision->id }}</h1>
        <div class="subtitle">
            Generado el: {{ $fechaGeneracion }}<br>
            {{ $config->company_name ?? 'Sistema de Gestión' }}
        </div>
    </div>

    <div class="info-grid">
        <!-- Columna izquierda -->
        <div class="info-column">
            <!-- Información del Beneficiario -->
            <div class="info-box">
                <h3>Beneficiario</h3>
                <div class="info-row">
                    <span class="info-label">Nombre:</span>
                    <span class="info-value"><strong>{{ $beneficiario }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tipo:</span>
                    <span class="info-value">
                        @if($comision->commissionable_type === 'App\Models\Trabajador')
                            @if($comision->commissionable && $comision->commissionable->posicion)
                                {{ $comision->commissionable->posicion }}
                            @else
                                (Trabajador)
                            @endif
                        @else
                            {{ $tipoReceptor }}
                        @endif
                    </span>
                </div>
                @if($comision->commissionable_type === 'App\Models\Trabajador' && $comision->commissionable && $comision->commissionable->tipoTrabajador)
                <div class="info-row">
                    <span class="info-label">Categoría:</span>
                    <span class="info-value">{{ $comision->commissionable->tipoTrabajador->nombre }}</span>
                </div>
                @endif
            </div>

            <!-- Información de la Comisión -->
            <div class="info-box">
                <h3>Información de la Comisión</h3>
                <div class="info-row">
                    <span class="info-label">Tipo:</span>
                    <span class="info-value">
                        @if($comision->tipo_comision === 'meta_venta' || $comision->tipo_comision === 'venta_meta')
                            <span class="badge badge-primary">Meta de Ventas</span>
                        @elseif($comision->tipo_comision === 'mecanico')
                            <span class="badge badge-info">Mecánico</span>
                        @elseif($comision->tipo_comision === 'carwash')
                            <span class="badge badge-secondary">Car Wash</span>
                        @else
                            <span class="badge badge-warning">{{ ucfirst($comision->tipo_comision) }}</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Monto:</span>
                    <span class="info-value highlight-amount">{{ $config->currency_simbol }} {{ number_format($comision->monto, 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Estado:</span>
                    <span class="info-value">
                        @if($comision->estado === 'pendiente')
                            <span class="badge badge-warning">Pendiente</span>
                        @elseif($comision->estado === 'pagado')
                            <span class="badge badge-success">Pagado</span>
                        @elseif($comision->estado === 'cancelado')
                            <span class="badge badge-danger">Cancelado</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($comision->estado) }}</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha Cálculo:</span>
                    <span class="info-value">{{ $comision->fecha_calculo ? \Carbon\Carbon::parse($comision->fecha_calculo)->format('d/m/Y H:i') : 'No definida' }}</span>
                </div>
            </div>
        </div>

        <!-- Columna derecha -->
        <div class="info-column">
            <!-- Información de Meta (si aplica) -->
            @if($metaInfo)
            <div class="info-box meta-info">
                <h3>Información de Meta</h3>
                <div class="info-row">
                    <span class="info-label">Meta:</span>
                    <span class="info-value"><strong>{{ $metaInfo['nombre'] }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Rango:</span>
                    <span class="info-value">{{ $metaInfo['rango'] }}</span>
                </div>
                @if(isset($metaInfo['porcentaje']))
                <div class="info-row">
                    <span class="info-label">Porcentaje:</span>
                    <span class="info-value">{{ $metaInfo['porcentaje'] }}%</span>
                </div>
                @endif
            </div>
            @endif

            <!-- Información de la Venta -->
            @if($comision->venta)
            <div class="info-box venta-details">
                <h3>Información de la Venta</h3>
                <div class="info-row">
                    <span class="info-label">ID Venta:</span>
                    <span class="info-value"><strong>#{{ $comision->venta->id }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fecha:</span>
                    <span class="info-value">{{ $comision->venta->fecha ? \Carbon\Carbon::parse($comision->venta->fecha)->format('d/m/Y') : 'No definida' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Venta:</span>
                    <span class="info-value">{{ $config->currency_simbol }} {{ number_format($comision->venta->total, 2) }}</span>
                </div>
                @if($comision->venta->cliente)
                <div class="info-row">
                    <span class="info-label">Cliente:</span>
                    <span class="info-value">{{ $comision->venta->cliente->nombre }}</span>
                </div>
                @endif
            </div>
            @endif

            <!-- Información de Pago -->
            <div class="info-box pago-details {{ $comision->estado === 'pagado' ? 'pagado' : '' }}">
                <h3>Estado de Pago</h3>
                @if($loteInfo)
                    <div class="info-row">
                        <span class="info-label">Lote de Pago:</span>
                        <span class="info-value"><strong>#{{ $loteInfo['numero_lote'] }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Fecha Pago:</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($loteInfo['fecha_pago'])->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Método:</span>
                        <span class="info-value">{{ ucfirst($loteInfo['metodo_pago']) }}</span>
                    </div>
                    @if($loteInfo['referencia'])
                    <div class="info-row">
                        <span class="info-label">Referencia:</span>
                        <span class="info-value">{{ $loteInfo['referencia'] }}</span>
                    </div>
                    @endif
                @else
                    <div class="info-row">
                        <span class="info-label">Estado:</span>
                        <span class="info-value">
                            @if($comision->estado === 'pendiente')
                                <span class="text-muted">Pendiente de pago</span>
                            @elseif($comision->estado === 'cancelado')
                                <span class="text-muted">Comisión cancelada</span>
                            @else
                                <span class="text-muted">Sin información de pago</span>
                            @endif
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Información adicional del artículo si existe -->
    @if($comision->articulo)
    <div class="info-box">
        <h3>Artículo Relacionado</h3>
        <div style="display: flex; justify-content: space-between;">
            <div style="width: 30%;">
                <div class="info-row">
                    <span class="info-label">Código:</span>
                    <span class="info-value">{{ $comision->articulo->codigo ?? 'N/A' }}</span>
                </div>
            </div>
            <div style="width: 40%;">
                <div class="info-row">
                    <span class="info-label">Nombre:</span>
                    <span class="info-value"><strong>{{ $comision->articulo->nombre ?? 'N/A' }}</strong></span>
                </div>
            </div>
            <div style="width: 30%;">
                <div class="info-row">
                    <span class="info-label">Precio:</span>
                    <span class="info-value">{{ $config->currency_simbol }} {{ number_format($comision->articulo->precio ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="footer">
        Reporte generado por {{ $config->company_name ?? 'Sistema de Gestión' }} | {{ $fechaGeneracion }}
    </div>
</body>
</html>
