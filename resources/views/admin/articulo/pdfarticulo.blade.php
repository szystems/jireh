<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de {{ $articulo->tipo == 'articulo' ? 'Artículo' : 'Servicio' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.3;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            padding-bottom: 8px;
            border-bottom: 1px solid #ddd;
            margin-bottom: 15px;
            position: relative;
        }
        .logo {
            max-width: 120px;
            max-height: 50px;
            display: block;
            margin: 0 auto 5px;
        }
        h1 {
            font-size: 16px;
            margin: 0 0 5px 0;
        }
        h2 {
            font-size: 14px;
            margin: 10px 0 5px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #ddd;
            color: #2563eb;
        }
        h3 {
            font-size: 13px;
            margin: 10px 0 5px 0;
            color: #444;
            background-color: #f5f5f5;
            padding: 3px 5px;
            border-left: 3px solid #2563eb;
        }
        p {
            margin: 3px 0;
        }
        .section {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        .info-box {
            border: 1px solid #ddd;
            padding: 5px;
            margin-bottom: 10px;
            border-radius: 3px;
            background-color: #f9f9f9;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            border-bottom: 1px dotted #eee;
            padding-bottom: 3px;
        }
        .info-label {
            font-weight: bold;
            width: 40%;
        }
        .info-value {
            text-align: right;
            width: 60%;
        }
        .badge {
            padding: 2px 5px;
            font-size: 10px;
            border-radius: 3px;
            color: white;
            display: inline-block;
        }
        .badge-success { background-color: #10b981; }
        .badge-warning { background-color: #f59e0b; color: #333; }
        .badge-danger { background-color: #ef4444; }
        .badge-info { background-color: #3b82f6; }
        .badge-secondary { background-color: #6b7280; }
        .badge-primary { background-color: #3b82f6; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }
        table th, table td {
            padding: 5px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-danger {
            color: #ef4444;
        }
        .text-success {
            color: #10b981;
        }
        .footer {
            margin-top: 15px;
            padding-top: 5px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            text-align: center;
            color: #666;
        }
        .margin-indicator {
            height: 6px;
            border-radius: 3px;
            background-color: #10b981;
            margin-top: 3px;
            margin-bottom: 5px;
        }
        .col-2 {
            width: 100%;
            display: table;
            table-layout: fixed;
        }
        .col-2 > div {
            display: table-cell;
            width: 50%;
            padding: 5px;
            vertical-align: top;
        }
        .status-bar {
            margin-top: 5px;
            display: block;
            text-align: center;
        }
        .status-bar span {
            margin: 0 2px;
        }
        .header-right {
            position: absolute;
            top: 0;
            right: 0;
            text-align: right;
        }
        .header-detail {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        .header-detail > div {
            display: table-cell;
            vertical-align: top;
        }
        .compact-table td, .compact-table th {
            padding: 3px 5px;
            font-size: 9px;
        }
        .compact-table {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($config && $config->logo)
            <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo" class="logo">
        @endif

        <div class="header-detail">
            <div>
                <h1>{{ $articulo->nombre }}</h1>
                <p>Código: {{ $articulo->codigo ?: 'No definido' }}</p>
            </div>
            <div class="header-right">
                <div class="status-bar">
                    <span class="badge {{ $articulo->estado ? 'badge-success' : 'badge-danger' }}">
                        {{ $articulo->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                    <span class="badge badge-info">
                        {{ ucfirst($articulo->tipo) }}
                    </span>
                    <span class="badge {{ $articulo->stock > $articulo->stock_minimo ? 'badge-success' : ($articulo->stock <= 0 ? 'badge-danger' : 'badge-warning') }}">
                        {{ $estadoStock }}
                    </span>
                </div>
                <p>Fecha: {{ $fechaGeneracion }}</p>
            </div>
        </div>
    </div>

    <div class="col-2">
        <!-- Columna izquierda -->
        <div>
            <h3>Información Básica</h3>
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Categoría:</span>
                    <span class="info-value">{{ $articulo->categoria->nombre }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Unidad de medida:</span>
                    <span class="info-value">{{ $articulo->unidad->nombre }} ({{ $articulo->unidad->abreviatura }})</span>
                </div>
                @if($articulo->descripcion)
                <div class="info-row">
                    <span class="info-label">Descripción:</span>
                    <span class="info-value">{{ Str::limit($articulo->descripcion, 30) }}</span>
                </div>
                @endif
            </div>

            <h3>Precios</h3>
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Precio de compra:</span>
                    <span class="info-value">{{ $config->currency_simbol }} {{ number_format($articulo->precio_compra, 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Precio de venta:</span>
                    <span class="info-value">{{ $config->currency_simbol }} {{ number_format($articulo->precio_venta, 2) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Impuesto ({{ number_format($impuesto, 2) }}%):</span>
                    <span class="info-value">{{ $config->currency_simbol }} {{ number_format($valorImpuesto, 2) }}</span>
                </div>
            </div>

            @if($articulo->descripcion && strlen($articulo->descripcion) > 30)
            <h3>Descripción Completa</h3>
            <div class="info-box" style="margin-bottom: 0;">
                <p>{{ $articulo->descripcion }}</p>
            </div>
            @endif
        </div>

        <!-- Columna derecha -->
        <div>
            <h3>Análisis de Rentabilidad</h3>
            <div class="info-box">
                <table class="compact-table">
                    <tbody>
                        <tr>
                            <td>Precio de venta</td>
                            <td class="text-right">{{ $config->currency_simbol }}.{{ number_format($articulo->precio_venta, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Precio de compra</td>
                            <td class="text-right text-danger">- {{ $config->currency_simbol }}.{{ number_format($articulo->precio_compra, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Impuesto ({{ number_format($impuesto, 2) }}%)</td>
                            <td class="text-right text-danger">- {{ $config->currency_simbol }}.{{ number_format($valorImpuesto, 2) }}</td>
                        </tr>

                        @if($articulo->tipo == 'servicio' && $costosComisiones > 0)
                            <tr>
                                <td>Comisiones</td>
                                <td class="text-right text-danger">- {{ $config->currency_simbol }}.{{ number_format($costosComisiones, 2) }}</td>
                            </tr>
                        @endif

                        <tr>
                            <td><strong>Ganancia real</strong></td>
                            <td class="text-right {{ $gananciaReal >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $config->currency_simbol }}.{{ number_format($gananciaReal, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div>
                    <strong>Margen: {{ number_format($margen, 2) }}%</strong>
                </div>
                <div class="margin-indicator" style="width: {{ min($margen, 100) }}%; background-color: {{ $margen < 10 ? '#ef4444' : ($margen < 20 ? '#f59e0b' : '#10b981') }};"></div>
            </div>

            <h3>Inventario</h3>
            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">Stock actual:</span>
                    <span class="info-value">
                        <span class="badge {{ $articulo->stock <= 0 ? 'badge-danger' : ($articulo->stock <= $articulo->stock_minimo ? 'badge-warning' : 'badge-success') }}">
                            {{ number_format($articulo->stock, $articulo->unidad->tipo == 'unidad' ? 0 : 2) }} {{ $articulo->unidad->abreviatura }}
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Stock mínimo:</span>
                    <span class="info-value">{{ number_format($articulo->stock_minimo, $articulo->unidad->tipo == 'unidad' ? 0 : 2) }} {{ $articulo->unidad->abreviatura }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Estado:</span>
                    <span class="info-value">{{ $estadoStock }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($articulo->tipo == 'servicio')
    <div class="section">
        <h3>Asignación y Comisiones</h3>
        <div class="info-box">
            <table class="compact-table" width="100%">
                <tr>
                    <td width="50%">
                        <strong>Mecánico asignado:</strong>
                        @if($articulo->mecanico)
                            {{ $articulo->mecanico->nombre_completo }}
                        @else
                            No asignado
                        @endif
                    </td>
                    <td width="25%">
                        <strong>Comisión Mecánico:</strong>
                        {{ $config->currency_simbol }} {{ number_format($articulo->costo_mecanico ?? 0, 2) }}
                    </td>
                    <td width="25%">
                        <strong>Comisión Car Wash:</strong>
                        {{ $config->currency_simbol }} {{ number_format($articulo->comision_carwash ?? 0, 2) }}
                    </td>
                </tr>
            </table>
        </div>

        <h3>Componentes del Servicio</h3>
        @if($articulo->articulos->count() > 0)
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th width="40%">Artículo</th>
                        <th width="20%">Cantidad</th>
                        <th width="20%">Precio</th>
                        <th width="20%" class="text-right">Costo Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $costoTotal = 0; @endphp
                    @foreach($articulo->articulos as $componente)
                        @php
                            $cantidad = $componente->pivot->cantidad;
                            $costoComponente = $componente->precio_compra * $cantidad;
                            $costoTotal += $costoComponente;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $componente->nombre }}</strong><br>
                                <small>{{ $componente->categoria->nombre }}</small>
                            </td>
                            <td>{{ number_format($cantidad, $componente->unidad->tipo == 'unidad' ? 0 : 2) }} {{ $componente->unidad->abreviatura }}</td>
                            <td>{{ $config->currency_simbol }} {{ number_format($componente->precio_compra, 2) }}</td>
                            <td class="text-right">{{ $config->currency_simbol }} {{ number_format($costoComponente, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #f2f2f2;">
                        <td colspan="3" class="text-right"><strong>Costo Total de Componentes:</strong></td>
                        <td class="text-right"><strong>{{ $config->currency_simbol }} {{ number_format($costoTotal, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="info-box">
                <p>Este servicio no tiene componentes registrados.</p>
            </div>
        @endif
    </div>
    @endif

    <div class="footer">
        <p>{{ $config->email }} | {{ $config->fb_link ? 'Facebook: '.$config->fb_link.' | ' : '' }} Generado el {{ $fechaGeneracion }}</p>
    </div>
</body>
</html>
