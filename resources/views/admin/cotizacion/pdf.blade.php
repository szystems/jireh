<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cotización {{ $cotizacion->numero_cotizacion }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        .logo {
            max-height: 80px;
            margin-bottom: 15px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        .document-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-top: 10px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-box {
            width: 48%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .info-box h3 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #007bff;
            font-size: 14px;
        }
        .info-row {
            margin-bottom: 8px;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .estado-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .estado-vigente { background-color: #28a745; color: white; }
        .estado-vencida { background-color: #dc3545; color: white; }
        .estado-aprobada { background-color: #ffc107; color: black; }
        .estado-rechazada { background-color: #6c757d; color: white; }
        .estado-convertida { background-color: #17a2b8; color: white; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .terms {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 10px;
        }
        .terms h4 {
            margin-top: 0;
            color: #007bff;
        }
        .validity-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .validity-notice strong {
            color: #856404;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">JIREH</div>
        <div style="font-size: 12px; color: #666;">Sistema de Gestión Empresarial</div>
        <div class="document-title">COTIZACIÓN</div>
        <div style="font-size: 14px; margin-top: 5px;">{{ $cotizacion->numero_cotizacion }}</div>
    </div>

    <!-- Información general -->
    <div class="info-section" style="display: table; width: 100%;">
        <div class="info-box" style="display: table-cell; width: 48%; vertical-align: top;">
            <h3>Información de la Cotización</h3>
            <div class="info-row">
                <span class="label">Número:</span>
                {{ $cotizacion->numero_cotizacion }}
            </div>
            <div class="info-row">
                <span class="label">Fecha:</span>
                {{ $cotizacion->fecha_cotizacion->format('d/m/Y') }}
            </div>
            <div class="info-row">
                <span class="label">Vencimiento:</span>
                {{ $cotizacion->fecha_vencimiento->format('d/m/Y') }}
            </div>
            <div class="info-row">
                <span class="label">Estado:</span>
                @switch($cotizacion->estado)
                    @case('vigente')
                        <span class="estado-badge estado-vigente">Vigente</span>
                        @break
                    @case('vencida')
                        <span class="estado-badge estado-vencida">Vencida</span>
                        @break
                    @case('aprobada')
                        <span class="estado-badge estado-aprobada">Aprobada</span>
                        @break
                    @case('rechazada')
                        <span class="estado-badge estado-rechazada">Rechazada</span>
                        @break
                    @case('convertida')
                        <span class="estado-badge estado-convertida">Convertida</span>
                        @break
                @endswitch
            </div>
        </div>
        
        <div style="display: table-cell; width: 4%;"></div>
        
        <div class="info-box" style="display: table-cell; width: 48%; vertical-align: top;">
            <h3>Información del Cliente</h3>
            <div class="info-row">
                <span class="label">Cliente:</span>
                {{ $cotizacion->cliente->nombre }}
            </div>
            <div class="info-row">
                <span class="label">Email:</span>
                {{ $cotizacion->cliente->email ?? 'No especificado' }}
            </div>
            <div class="info-row">
                <span class="label">Teléfono:</span>
                {{ $cotizacion->cliente->telefono ?? 'No especificado' }}
            </div>
            <div class="info-row">
                <span class="label">Vehículo:</span>
                {{ $cotizacion->vehiculo->placa }} - {{ $cotizacion->vehiculo->marca }} {{ $cotizacion->vehiculo->modelo }}
            </div>
        </div>
    </div>

    <!-- Aviso de validez -->
    @if($cotizacion->estado === 'vigente')
        <div class="validity-notice">
            <strong>Validez de la Cotización:</strong> 
            Esta cotización es válida hasta el {{ $cotizacion->fecha_vencimiento->format('d/m/Y') }}
            @if($cotizacion->fecha_vencimiento->diffInDays(\Carbon\Carbon::now()) <= 3)
                - <strong>¡Próximo a vencer!</strong>
            @endif
        </div>
    @endif

    <!-- Detalles de productos/servicios -->
    <table>
        <thead>
            <tr>
                <th style="width: 40%;">Artículo/Servicio</th>
                <th style="width: 10%;" class="text-center">Cantidad</th>
                <th style="width: 15%;" class="text-right">Precio Unit.</th>
                <th style="width: 15%;" class="text-center">Descuento</th>
                <th style="width: 20%;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalGeneral = 0;
                $totalDescuentos = 0;
                $subtotalSinDescuentos = 0;
            @endphp
            @foreach($cotizacion->detalleCotizaciones as $detalle)
                @php
                    $subtotalDetalle = $detalle->cantidad * $detalle->precio_unitario;
                    $montoDescuento = 0;
                    if ($detalle->descuento_id && $detalle->descuento) {
                        $montoDescuento = $subtotalDetalle * ($detalle->descuento->porcentaje_descuento / 100);
                    }
                    $subtotalConDescuento = $subtotalDetalle - $montoDescuento;
                    $totalGeneral += $subtotalConDescuento;
                    $totalDescuentos += $montoDescuento;
                    $subtotalSinDescuentos += $subtotalDetalle;
                @endphp
                <tr>
                    <td>
                        <strong>{{ $detalle->articulo->codigo }}</strong><br>
                        {{ $detalle->articulo->nombre }}
                        @if($detalle->articulo->descripcion)
                            <br><small style="color: #666;">{{ $detalle->articulo->descripcion }}</small>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $detalle->cantidad }} {{ $detalle->articulo->unidad->abreviatura }}
                    </td>
                    <td class="text-right">
                        {{ $config->currency_simbol }}{{ number_format($detalle->precio_venta, 2) }}
                    </td>
                    <td class="text-center">
                        @if($detalle->descuento)
                            {{ $detalle->descuento->porcentaje_descuento }}%<br>
                            <small>({{ $config->currency_simbol }}{{ number_format($montoDescuento, 2) }})</small>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        {{ $config->currency_simbol }}{{ number_format($subtotalConDescuento, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            @if($totalDescuentos > 0)
                <tr>
                    <td colspan="4" class="text-right"><strong>Subtotal sin descuentos:</strong></td>
                    <td class="text-right"><strong>{{ $config->currency_simbol }}{{ number_format($subtotalSinDescuentos, 2) }}</strong></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right"><strong>Total descuentos:</strong></td>
                    <td class="text-right" style="color: #dc3545;"><strong>-{{ $config->currency_simbol }}{{ number_format($totalDescuentos, 2) }}</strong></td>
                </tr>
            @endif
            <tr class="total-row">
                <td colspan="4" class="text-right"><strong>TOTAL GENERAL:</strong></td>
                <td class="text-right" style="font-size: 14px; color: #007bff;"><strong>{{ $config->currency_simbol }}{{ number_format($totalGeneral, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <!-- Términos y condiciones -->
    <div class="terms">
        <h4>Términos y Condiciones</h4>
        <ul style="margin: 0; padding-left: 20px;">
            <li>Esta cotización tiene una validez de {{ $cotizacion->fecha_vencimiento->diffInDays($cotizacion->fecha_cotizacion) }} días a partir de la fecha de emisión.</li>
            <li>Los precios incluyen IVA cuando aplique.</li>
            <li>Los precios están sujetos a cambios sin previo aviso después del vencimiento.</li>
            <li>La disponibilidad de productos está sujeta a stock al momento de la confirmación.</li>
            <li>Para proceder con la orden, se requiere aprobación formal de esta cotización.</li>
            <li>Los servicios se ejecutarán según cronograma acordado mutuamente.</li>
        </ul>
    </div>

    <!-- Información de contacto -->
    <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
        <h4 style="margin-top: 0; color: #007bff;">Información de Contacto</h4>
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; width: 50%;">
                <strong>Email:</strong> info@jireh.com<br>
                <strong>Teléfono:</strong> (123) 456-7890
            </div>
            <div style="display: table-cell; width: 50%;">
                <strong>Dirección:</strong> Calle Principal #123<br>
                <strong>Ciudad:</strong> Tu Ciudad, País
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Cotización generada el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} | Sistema JIREH</p>
        <p style="font-size: 9px;">Este documento es una cotización y no constituye una factura. Los precios y condiciones están sujetos a los términos mencionados.</p>
    </div>
</body>
</html>