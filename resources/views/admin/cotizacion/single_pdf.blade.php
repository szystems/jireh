<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cotización {{ $cotizacion->numero_cotizacion }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 12px;
            font-size: 10px;
            color: #333;
            line-height: 1.2;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #007bff;
            padding-bottom: 8px;
        }
        .company-logo {
            max-height: 60px;
            max-width: 90%;
            margin: 5px auto;
            display: block;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 8px;
            padding-top: 2px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 3px;
        }
        .document-title {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .info-box {
            width: 48%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
            background-color: #f9f9f9;
        }
        .info-box h3 {
            margin-top: 0;
            margin-bottom: 6px;
            color: #007bff;
            font-size: 11px;
        }
        .info-row {
            margin-bottom: 4px;
            font-size: 9px;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 70px;
        }
        .estado-badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 2px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .estado-vigente { background-color: #28a745; color: white; }
        .estado-vencida { background-color: #dc3545; color: white; }
        .estado-aprobada { background-color: #007bff; color: white; }
        .estado-generada { background-color: #17a2b8; color: white; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 9px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            font-size: 8px;
            padding: 3px;
        }
        td {
            font-size: 8px;
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
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        .terms {
            margin-top: 12px;
            padding: 8px;
            background-color: #f8f9fa;
            border-radius: 3px;
            font-size: 8px;
        }
        .terms h4 {
            margin-top: 0;
            margin-bottom: 5px;
            color: #007bff;
            font-size: 10px;
        }
        .terms ul {
            margin: 0;
            padding-left: 15px;
        }
        .terms li {
            margin-bottom: 2px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <!-- Logo de la empresa -->
        <div class="logo-container">
            @if($config->logo)
                <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo de la empresa" class="company-logo">
            @endif
        </div>
        
        {{-- <div class="company-name">JIREH</div>
        <div style="font-size: 12px; color: #666;">Sistema de Gestión Empresarial</div> --}}
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
                @if($cotizacion->estado === 'Aprobado')
                    <span class="estado-badge estado-aprobada">Aprobado</span>
                @else
                    <span class="estado-badge estado-generada">Generado</span>
                @endif
            </div>
            @if($cotizacion->estado === 'Generado')
            <div class="info-row">
                <span class="label">Vigencia:</span>
                @if($cotizacion->esta_vigente)
                    @php
                        $diasRestantes = now()->diffInDays($cotizacion->fecha_vencimiento, false);
                    @endphp
                    <span class="estado-badge estado-vigente">Vigente ({{ $diasRestantes }} días)</span>
                @else
                    @php
                        $diasVencida = now()->diffInDays($cotizacion->fecha_vencimiento);
                    @endphp
                    <span class="estado-badge estado-vencida">Vencida ({{ $diasVencida }} días)</span>
                @endif
            </div>
            @endif
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

    <!-- Detalles de productos/servicios -->
    <table>
        <thead>
            <tr>
                <th style="width: 35%;">Artículo/Servicio</th>
                <th style="width: 8%;" class="text-center">Cant.</th>
                <th style="width: 12%;" class="text-right">P.Unit.</th>
                <th style="width: 12%;" class="text-center">Desc.</th>
                <th style="width: 13%;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalGeneral = 0;
                $totalDescuentos = 0;
            @endphp
            @foreach($cotizacion->detalleCotizaciones as $detalle)
                @php
                    // Usar el sub_total que ya está calculado correctamente
                    $subtotalConDescuento = $detalle->sub_total;
                    $totalGeneral += $subtotalConDescuento;
                    
                    // Calcular descuento solo para mostrar
                    $montoDescuento = 0;
                    if ($detalle->descuento_id && $detalle->descuento) {
                        $subtotalSinDescuento = $detalle->cantidad * $detalle->precio_venta;
                        $montoDescuento = $subtotalSinDescuento * ($detalle->descuento->porcentaje_descuento / 100);
                        $totalDescuentos += $montoDescuento;
                    }
                @endphp
                <tr>
                    <td>
                        <strong>{{ $detalle->articulo->codigo }}</strong><br>
                        {{ $detalle->articulo->nombre }}
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
                    <td class="text-right"><strong>{{ $config->currency_simbol }}{{ number_format($totalGeneral + $totalDescuentos, 2) }}</strong></td>
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
        <ul>
            <li>Cotización válida {{ $cotizacion->fecha_vencimiento->diffInDays($cotizacion->fecha_cotizacion) }} días desde emisión.</li>
            <li>Precios incluyen IVA cuando aplique.</li>
            <li>Precios sujetos a cambios tras vencimiento.</li>
            <li>Disponibilidad sujeta a stock al confirmar.</li>
            <li>Requiere aprobación formal para proceder.</li>
        </ul>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Cotización generada el {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} | Sistema JIREH</p>
        <p style="font-size: 9px;">Este documento es una cotización y no constituye una factura. Los precios y condiciones están sujetos a los términos mencionados.</p>
    </div>
</body>
</html>