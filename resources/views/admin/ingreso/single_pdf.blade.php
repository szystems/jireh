<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detalle de Ingreso #{{ $ingreso->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 10px;
        }

        /* Agregar estilos para el logo integrado */
        .company-logo {
            max-height: 70px;
            max-width: 90%;
            margin: 5px auto;
            display: block;
            text-align: center;
        }

        .content {
            margin-top: 10px; /* Reducir el margen superior */
        }

        .logo-container {
            text-align: center;
            margin-bottom: 10px;
            padding-top: 5px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
        }
        .info-section {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-cell {
            display: table-cell;
            padding: 3px 5px;
            width: 50%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            font-size: 10px;
        }
        thead {
            background-color: #007bff;
            color: white;
        }
        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .section-title {
            background-color: #007bff;
            color: white;
            padding: 5px;
            margin: 15px 0 10px;
            font-weight: bold;
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }
        .text-primary { color: #007bff; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-info { color: #17a2b8; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 10px;
        }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .summary-table td, .summary-table th {
            padding: 3px 5px;
        }
        /* Nuevos estilos más modernos */
        .estado-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
        }
        .estado-carwash { background-color: #17a2b8; color: white; }
        .estado-general { background-color: #ffc107; color: #212529; }

        .section-box {
            border: 1px solid #eee;
            border-radius: 5px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            padding: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .section-box-header {
            border-bottom: 1px solid #eee;
            padding: 8px 12px;
            background-color: rgba(0,123,255,0.1);
        }
        .section-box-title {
            margin: 0;
            font-size: 14px;
            color: #007bff;
        }
        .section-box-body {
            padding: 10px 12px;
        }
        .info-columns {
            display: table;
            width: 100%;
        }
        .info-column {
            display: table-cell;
            width: 33.33%;
            vertical-align: top;
            padding: 5px;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
            font-size: 11px;
            display: block;
            margin-bottom: 2px;
        }
        .info-value {
            color: #333;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .total-row.grand-total {
            background-color: #007bff;
            color: white;
        }
        .moneda {
            font-family: monospace;
        }
        .resumen-financiero {
            width: 100%;
            margin-top: 10px;
        }
        .resumen-financiero tr td:first-child {
            width: 70%;
            text-align: right;
            padding-right: 10px;
        }
        .resumen-financiero tr td:last-child {
            width: 30%;
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="content">
        <!-- Añadir logo integrado -->
        <div class="logo-container">
            @if($config->logo)
                <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo de la empresa" class="company-logo">
            @endif
        </div>

        <!-- Encabezado con logo -->
        <div class="header">
            <h1>DETALLE DE INGRESO #{{ $ingreso->id }}</h1>
            @if($ingreso->numero_factura)
                <p>Factura: {{ $ingreso->numero_factura }}</p>
            @endif
            <p>Fecha: {{ \Carbon\Carbon::parse($ingreso->fecha)->format('d/m/Y') }}</p>

            <!-- Tipo de compra -->
            <div>
                <span class="estado-badge {{ $ingreso->tipo_compra == 'Car Wash' ? 'estado-carwash' : 'estado-general' }}">
                    {{ strtoupper($ingreso->tipo_compra) }}
                </span>
            </div>
        </div>

        <!-- Sección de información general con columnas -->
        <div class="section-box">
            <div class="section-box-header">
                <h3 class="section-box-title"><i class="bi bi-info-circle"></i> INFORMACIÓN GENERAL</h3>
            </div>
            <div class="section-box-body">
                <div class="info-columns">
                    <div class="info-column">
                        <div class="info-item">
                            <span class="info-label">Proveedor:</span>
                            <span class="info-value text-primary text-bold">{{ optional($ingreso->proveedor)->nombre ?: 'No especificado' }}</span>
                        </div>
                        @if($ingreso->proveedor && $ingreso->proveedor->telefono)
                            <div class="info-item">
                                <span class="info-label">Teléfono:</span>
                                <span class="info-value">{{ $ingreso->proveedor->telefono }}</span>
                            </div>
                        @endif
                        @if($ingreso->proveedor && $ingreso->proveedor->email)
                            <div class="info-item">
                                <span class="info-label">Email:</span>
                                <span class="info-value text-info">{{ $ingreso->proveedor->email }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="info-column">
                        <div class="info-item">
                            <span class="info-label">Tipo de Compra:</span>
                            <span class="info-value text-bold">{{ $ingreso->tipo_compra }}</span>
                        </div>
                        @if($ingreso->proveedor && $ingreso->proveedor->direccion)
                            <div class="info-item">
                                <span class="info-label">Dirección:</span>
                                <span class="info-value">{{ $ingreso->proveedor->direccion }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="info-column">
                        <div class="info-item">
                            <span class="info-label">Registrado por:</span>
                            <span class="info-value text-primary">{{ optional($ingreso->usuario)->name ?: 'No disponible' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Fecha de registro:</span>
                            <span class="info-value">{{ $ingreso->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de detalles de ingreso -->
        <div class="section-title">DETALLES DEL INGRESO</div>

        <table>
            <thead>
                <tr>
                    <th width="40%">Artículo</th>
                    <th class="text-center" width="12%">Cantidad</th>
                    <th class="text-right" width="16%">Precio Compra</th>
                    <th class="text-right" width="16%">Precio Venta</th>
                    <th class="text-right" width="16%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalCompra = 0;
                    $totalArticulos = 0;
                    $totalValorVenta = 0;
                @endphp
                @foreach($ingreso->detalles as $detalle)
                    @php
                        $subtotal = $detalle->cantidad * $detalle->precio_compra;
                        $totalCompra += $subtotal;
                        $totalArticulos += $detalle->cantidad;
                        $totalValorVenta += $detalle->cantidad * $detalle->precio_venta;
                    @endphp
                    <tr>
                        <td>
                            @if($detalle->articulo)
                                <strong class="text-primary">{{ $detalle->articulo->codigo ?: 'SIN-COD' }}</strong>
                                <br>{{ $detalle->articulo->nombre }}
                            @else
                                Artículo no disponible
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $detalle->cantidad }}
                            @if($detalle->articulo && $detalle->articulo->unidad)
                                {{ $detalle->articulo->unidad->abreviatura }}
                            @endif
                        </td>
                        <td class="text-right moneda">{{ $config->currency_simbol }}.{{ number_format($detalle->precio_compra, 2, '.', ',') }}</td>
                        <td class="text-right moneda">{{ $config->currency_simbol }}.{{ number_format($detalle->precio_venta, 2, '.', ',') }}</td>
                        <td class="text-right text-bold moneda">{{ $config->currency_simbol }}.{{ number_format($subtotal, 2, '.', ',') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row grand-total">
                    <td colspan="4" class="text-right">TOTAL COMPRA:</td>
                    <td class="text-right moneda">{{ $config->currency_simbol }}.{{ number_format($totalCompra, 2, '.', ',') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Sección de información financiera detallada -->
        <div class="section-box">
            <div class="section-box-header">
                <h3 class="section-box-title"><i class="bi bi-cash-coin"></i> RESUMEN FINANCIERO</h3>
            </div>
            <div class="section-box-body">
                <div class="info-columns">
                    <div class="info-column">
                        <table class="resumen-financiero">
                            <tr>
                                <td>Total de artículos ingresados:</td>
                                <td><span class="text-info">{{ number_format($totalArticulos, 0) }}</span> unidades</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-column">
                        <table class="resumen-financiero">
                            <tr>
                                <td>Total compra (costo):</td>
                                <td class="text-danger moneda">{{ $config->currency_simbol }}.{{ number_format($totalCompra, 2, '.', ',') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-column">
                        <table class="resumen-financiero">
                            <tr>
                                <td>Valor en precio de venta:</td>
                                <td class="text-success moneda">{{ $config->currency_simbol }}.{{ number_format($totalValorVenta, 2, '.', ',') }}</td>
                            </tr>
                            @if($totalCompra > 0)
                            <tr style="border-top: 1px solid #ddd; border-bottom: 1px solid #ddd;">
                                <td><strong>GANANCIA POTENCIAL:</strong></td>
                                <td class="text-success moneda"><strong>{{ $config->currency_simbol }}.{{ number_format($totalValorVenta - $totalCompra, 2, '.', ',') }}</strong></td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección de información adicional -->
        @if($ingreso->observaciones)
        <div class="section-box">
            <div class="section-box-header">
                <h3 class="section-box-title"><i class="bi bi-chat-square-text"></i> OBSERVACIONES</h3>
            </div>
            <div class="section-box-body">
                <p>{{ $ingreso->observaciones }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="footer">
        <p>Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p>{{ $config->nombre_negocio }} - {{ $config->telefono }}</p>
    </div>
</body>
</html>
