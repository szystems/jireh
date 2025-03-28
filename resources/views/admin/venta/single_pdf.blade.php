<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Detalle de Venta #{{ $venta->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 10px;
        }

        .company-logo {
            max-height: 70px;
            max-width: 90%;
            margin: 5px auto;
            display: block;
            text-align: center;
        }

        .content {
            margin-top: 10px;
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
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            font-size: 12px;
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
            font-size: 12px;
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
        .text-warning { color: #ffc107; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }

        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-primary { background-color: #007bff; color: white; }

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
            font-weight: bold;
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

        .moneda {
            font-family: monospace;
        }

        .totales-table {
            width: 40%;
            margin-left: 60%;
        }
        .totales-table td {
            padding: 3px 5px;
        }
        .totales-table td.label {
            text-align: right;
            font-weight: bold;
        }
        .totales-table td.value {
            text-align: right;
        }
        .totales-table tr.total {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .venta-info {
            border: 1px solid #ddd;
            border-radius: 3px;
            padding: 5px;
            background-color: #f8f9fa;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="content">
        <!-- Añadir el logo integrado al inicio del contenido -->
        <div class="logo-container">
            @if($config->logo)
                <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo de la empresa" class="company-logo">
            @endif
        </div>

        <!-- Encabezado con información general -->
        <div class="header">
            <h1>DETALLE DE VENTA #{{ $venta->id }}</h1>

            @if($venta->numero_factura)
                <p>Factura: {{ $venta->numero_factura }}</p>
            @endif

            <p>Fecha: {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</p>

            <span class="badge {{ $venta->estado == 1 ? 'badge-primary' : 'badge-danger' }}">
                {{ $venta->estado == 1 ? 'VENTA ACTIVA' : 'VENTA CANCELADA' }}
            </span>
            <span class="badge badge-info">{{ strtoupper($venta->tipo_venta) }}</span>
        </div>

        <!-- Sección de información del cliente y vehículo -->
        <div class="section-box">
            <div class="section-box-header">
                <h3 class="section-box-title">INFORMACIÓN GENERAL</h3>
            </div>
            <div class="section-box-body">
                <div class="info-columns">
                    <div class="info-column">
                        <div class="info-item">
                            <span class="info-label">Cliente:</span>
                            <span class="info-value text-primary text-bold">
                                {{ $venta->cliente ? $venta->cliente->nombre : 'No especificado' }}
                            </span>
                        </div>
                        @if($venta->cliente)
                            @if($venta->cliente->telefono)
                                <div class="info-item">
                                    <span class="info-label">Teléfono:</span>
                                    <span class="info-value">{{ $venta->cliente->telefono }}</span>
                                </div>
                            @endif
                            @if($venta->cliente->celular)
                                <div class="info-item">
                                    <span class="info-label">Celular:</span>
                                    <span class="info-value">{{ $venta->cliente->celular }}</span>
                                </div>
                            @endif
                            @if($venta->cliente->direccion)
                                <div class="info-item">
                                    <span class="info-label">Dirección:</span>
                                    <span class="info-value">{{ $venta->cliente->direccion }}</span>
                                </div>
                            @endif
                        @endif
                    </div>

                    <div class="info-column">
                        @if($venta->vehiculo)
                            <div class="info-item">
                                <span class="info-label">Vehículo:</span>
                                <span class="info-value text-bold">
                                    {{ $venta->vehiculo->marca }} {{ $venta->vehiculo->modelo }}
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Placa:</span>
                                <span class="info-value">{{ $venta->vehiculo->placa }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Color:</span>
                                <span class="info-value">{{ $venta->vehiculo->color }}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Año:</span>
                                <span class="info-value">{{ $venta->vehiculo->ano }}</span>
                            </div>
                        @else
                            <div class="info-item">
                                <span class="info-label">Vehículo:</span>
                                <span class="info-value">No se especificó vehículo</span>
                            </div>
                        @endif
                    </div>

                    <div class="info-column">
                        <div class="info-item">
                            <span class="info-label">Vendedor:</span>
                            <span class="info-value text-primary">
                                {{ $venta->usuario ? $venta->usuario->name : 'No disponible' }}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Fecha de registro:</span>
                            <span class="info-value">{{ $venta->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalles de la venta -->
        <div class="section-title">DETALLES DE VENTA</div>

        <table>
            <thead>
                <tr>
                    <th width="35%">Artículo</th>
                    <th class="text-center" width="10%">Cantidad</th>
                    <th class="text-right" width="10%">Precio</th>
                    @if (Auth::user()->role_as != 1)
                        <th class="text-right" width="10%">Costo</th>
                    @endif
                    <th class="text-right" width="10%">Descuento</th>
                    <th class="text-center" width="15%">Trabajador</th>
                    <th class="text-right" width="10%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalVenta = 0;
                    $totalDescuentos = 0;
                    $totalCostos = 0;
                @endphp

                @foreach($venta->detalleVentas as $detalle)
                    @php
                        $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                        $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

                        // Calcular monto de descuento
                        $montoDescuento = 0;
                        if($detalle->descuento_id && $detalle->descuento) {
                            $montoDescuento = $subtotalSinDescuento * ($detalle->descuento->porcentaje_descuento / 100);
                        }

                        $totalDescuentos += $montoDescuento;
                        $totalVenta += ($subtotalSinDescuento - $montoDescuento);
                        $totalCostos += ($detalle->precio_costo * $detalle->cantidad);
                    @endphp

                    <tr>
                        <td>
                            @if($detalle->articulo)
                                <span class="text-primary text-bold">{{ $detalle->articulo->codigo ?: 'SIN-COD' }}</span>
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
                        <td class="text-right moneda">{{ $config->currency_simbol }}.{{ number_format($precioUnitario, 2, '.', ',') }}</td>
                        @if (Auth::user()->role_as != 1)
                            <td class="text-right moneda">{{ $config->currency_simbol }}.{{ number_format($detalle->precio_costo, 2, '.', ',') }}</td>
                        @endif
                        <td class="text-right">
                            @if($montoDescuento > 0)
                                <span class="text-danger moneda">
                                    {{ $config->currency_simbol }}.{{ number_format($montoDescuento, 2, '.', ',') }}
                                </span>
                                @if($detalle->descuento)
                                    <br><small>({{ $detalle->descuento->porcentaje_descuento }}%)</small>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($detalle->trabajador)
                                {{ $detalle->trabajador->nombre }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right text-bold moneda">
                            {{ $config->currency_simbol }}.{{ number_format($subtotalSinDescuento - $montoDescuento, 2, '.', ',') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Tabla de totales y resumen -->
        <table class="totales-table">
            <tr>
                <td class="label">Subtotal:</td>
                <td class="value moneda">{{ $config->currency_simbol }}.{{ number_format($totalVenta + $totalDescuentos, 2, '.', ',') }}</td>
            </tr>
            <tr>
                <td class="label">Descuentos:</td>
                <td class="value text-danger moneda">-{{ $config->currency_simbol }}.{{ number_format($totalDescuentos, 2, '.', ',') }}</td>
            </tr>
            <tr class="total">
                <td class="label">TOTAL VENTA:</td>
                <td class="value text-success moneda">{{ $config->currency_simbol }}.{{ number_format($totalVenta, 2, '.', ',') }}</td>
            </tr>
        </table>

        <!-- Sección de pagos -->
        <div class="section-title">PAGOS REALIZADOS</div>

        @php
            // Inicializar la variable totalPagado fuera del condicional para evitar errores
            $totalPagado = 0;
        @endphp

        @if($venta->pagos && $venta->pagos->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th width="15%">Fecha</th>
                        <th width="15%">Método</th>
                        <th width="40%">Observaciones</th>
                        <th width="15%">Registrado por</th>
                        <th width="15%" class="text-right">Monto</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($venta->pagos as $pago)
                        @php $totalPagado += $pago->monto; @endphp

                        <tr>
                            <td>{{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $pago->metodo_pago }}</td>
                            <td>{{ $pago->observaciones }}</td>
                            <td>{{ $pago->usuario ? $pago->usuario->name : 'No disponible' }}</td>
                            <td class="text-right moneda">{{ $config->currency_simbol }}.{{ number_format($pago->monto, 2, '.', ',') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #f8f9fa; font-weight: bold;">
                        <td colspan="4" class="text-right">TOTAL PAGADO:</td>
                        <td class="text-right moneda">{{ $config->currency_simbol }}.{{ number_format($totalPagado, 2, '.', ',') }}</td>
                    </tr>

                    @php $saldoPendiente = $totalVenta - $totalPagado; @endphp

                    @if($saldoPendiente > 0)
                        <tr style="background-color: #fff3cd;">
                            <td colspan="4" class="text-right text-bold">SALDO PENDIENTE:</td>
                            <td class="text-right text-danger text-bold moneda">{{ $config->currency_simbol }}.{{ number_format($saldoPendiente, 2, '.', ',') }}</td>
                        </tr>
                    @elseif($saldoPendiente < 0)
                        <tr style="background-color: #d4edda;">
                            <td colspan="4" class="text-right text-bold">SALDO A FAVOR:</td>
                            <td class="text-right text-success text-bold moneda">{{ $config->currency_simbol }}.{{ number_format(abs($saldoPendiente), 2, '.', ',') }}</td>
                        </tr>
                    @else
                        <tr style="background-color: #d4edda;">
                            <td colspan="5" class="text-center text-success text-bold">VENTA COMPLETAMENTE PAGADA</td>
                        </tr>
                    @endif
                </tfoot>
            </table>
        @else
            <div class="text-center" style="padding: 10px; border: 1px dashed #ddd; color: #666;">
                No se han registrado pagos para esta venta.
            </div>
        @endif

        <!-- Sección de resumen financiero -->
        @if (Auth::user()->role_as != 1)
            <div class="section-box">
                <div class="section-box-header">
                    <h3 class="section-box-title">RESUMEN FINANCIERO</h3>
                </div>
                <div class="section-box-body">
                    <div class="info-columns">
                        <div class="info-column">
                            <table style="border: none; width: 100%;">
                                <tr>
                                    <td style="border: none; text-align: right; font-weight: bold; width: 60%;">Valor de Venta:</td>
                                    <td style="border: none; text-align: right; width: 40%;">
                                        <span class="text-success moneda">{{ $config->currency_simbol }}.{{ number_format($totalVenta, 2, '.', ',') }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: none; text-align: right; font-weight: bold;">Costo de Artículos:</td>
                                    <td style="border: none; text-align: right;">
                                        <span class="text-danger moneda">{{ $config->currency_simbol }}.{{ number_format($totalCostos, 2, '.', ',') }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="info-column">
                            <table style="border: none; width: 100%;">
                                <tr>
                                    <td style="border: none; text-align: right; font-weight: bold; width: 60%;">Margen Bruto:</td>
                                    <td style="border: none; text-align: right; width: 40%;">
                                        @php
                                            $margenBruto = $totalVenta > 0 ? (($totalVenta - $totalCostos) / $totalVenta) * 100 : 0;
                                        @endphp
                                        <span class="{{ $margenBruto >= 20 ? 'text-success' : ($margenBruto >= 10 ? 'text-warning' : 'text-danger') }}">
                                            {{ number_format($margenBruto, 2) }}%
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: none; text-align: right; font-weight: bold;">Ganancia Bruta:</td>
                                    <td style="border: none; text-align: right;">
                                        <span class="text-success moneda">{{ $config->currency_simbol }}.{{ number_format($totalVenta - $totalCostos, 2, '.', ',') }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="info-column">
                            <table style="border: none; width: 100%;">
                                <tr>
                                    <td style="border: none; text-align: right; font-weight: bold; width: 60%;">Estado de Pago:</td>
                                    <td style="border: none; text-align: right; width: 40%;">
                                        @php
                                            $porcentajePagado = $totalVenta > 0 ? ($totalPagado / $totalVenta) * 100 : 0;
                                        @endphp
                                        <span class="{{ $porcentajePagado >= 100 ? 'text-success' : ($porcentajePagado > 0 ? 'text-warning' : 'text-danger') }}">
                                            {{ number_format($porcentajePagado, 0) }}%
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="border: none; text-align: right; font-weight: bold;">Estado Venta:</td>
                                    <td style="border: none; text-align: right;">
                                        <span class="badge {{ $venta->estado == 1 ? 'badge-success' : 'badge-danger' }}">
                                            {{ $venta->estado == 1 ? 'Activa' : 'Cancelada' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="footer">
        <p>Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p>{{ $config->nombre_negocio ?? 'Sistema de Gestión' }} | {{ $config->telefono ?? '' }}</p>
    </div>
</body>
</html>
