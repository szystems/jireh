<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante de Pago de Sueldos - {{ $pagoSueldo->numero_lote }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .document-title {
            font-size: 18px;
            color: #666;
            margin-top: 5px;
        }
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .info-left, .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .info-right {
            text-align: right;
        }
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            margin-bottom: 10px;
        }
        .info-title {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            background-color: #007bff;
            color: white;
            border-radius: 3px;
            font-size: 10px;
        }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
        .badge-info { background-color: #17a2b8; }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #e9ecef;
            font-weight: bold;
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
        .summary-section {
            background-color: #e8f5e8;
            border: 2px solid #28a745;
            padding: 15px;
            margin-top: 20px;
        }
        .summary-title {
            font-size: 16px;
            font-weight: bold;
            color: #155724;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 30px;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
            font-size: 10px;
            color: #6c757d;
        }
        .signature-section {
            margin-top: 40px;
        }
        .signature-box {
            display: inline-block;
            width: 200px;
            text-align: center;
            margin-right: 50px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 30px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="company-name">JIREH - Car Wash & CDS</div>
        <div class="document-title">COMPROBANTE DE PAGO DE SUELDOS</div>
    </div>

    <!-- Información del Lote -->
    <div class="info-section">
        <div class="info-left">
            <div class="info-box">
                <div class="info-title">Información del Lote</div>
                <table style="width: 100%; border: none;">
                    <tr style="border: none;">
                        <td style="border: none; font-weight: bold;">Número de Lote:</td>
                        <td style="border: none;">{{ $pagoSueldo->numero_lote }}</td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; font-weight: bold;">Período:</td>
                        <td style="border: none;">
                            {{ $pagoSueldo->periodo_completo }}
                        </td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; font-weight: bold;">Fecha de Pago:</td>
                        <td style="border: none;">{{ \Carbon\Carbon::parse($pagoSueldo->fecha_pago)->format('d/m/Y') }}</td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; font-weight: bold;">Método de Pago:</td>
                        <td style="border: none;">
                            @switch($pagoSueldo->metodo_pago)
                                @case('efectivo')
                                    <span class="badge badge-success">Efectivo</span>
                                    @break
                                @case('transferencia')
                                    <span class="badge">Transferencia</span>
                                    @break
                                @case('cheque')
                                    <span class="badge badge-warning">Cheque</span>
                                    @break
                                @default
                                    {{ ucfirst($pagoSueldo->metodo_pago) }}
                            @endswitch
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="info-right">
            <div class="info-box">
                <div class="info-title">Estado y Totales</div>
                <table style="width: 100%; border: none;">
                    <tr style="border: none;">
                        <td style="border: none; font-weight: bold;">Estado:</td>
                        <td style="border: none;">
                            @switch($pagoSueldo->estado)
                                @case('pendiente')
                                    <span class="badge badge-warning">Pendiente</span>
                                    @break
                                @case('pagado')
                                    <span class="badge badge-success">Pagado</span>
                                    @break
                                @case('cancelado')
                                    <span class="badge badge-danger">Cancelado</span>
                                    @break
                                @default
                                    {{ ucfirst($pagoSueldo->estado) }}
                            @endswitch
                        </td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; font-weight: bold;">Total Empleados:</td>
                        <td style="border: none;">{{ $pagoSueldo->detalles->count() }}</td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; font-weight: bold;">Total del Lote:</td>
                        <td style="border: none; font-weight: bold; color: #28a745;">Q{{ number_format($pagoSueldo->total_monto, 2) }}</td>
                    </tr>
                    <tr style="border: none;">
                        <td style="border: none; font-weight: bold;">Creado por:</td>
                        <td style="border: none;">{{ $pagoSueldo->usuario->name ?? 'Usuario eliminado' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Observaciones -->
    @if($pagoSueldo->observaciones)
        <div class="info-box">
            <div class="info-title">Observaciones</div>
            <p>{{ $pagoSueldo->observaciones }}</p>
        </div>
    @endif

    <!-- Detalle de Empleados -->
    <div class="info-title" style="margin-bottom: 10px;">Detalle de Empleados</div>
    <table class="table">
        <thead>
            <tr>
                <th>Empleado</th>
                <th class="text-center">Tipo</th>
                <th class="text-right">Sueldo Base</th>
                <th class="text-right">H. Extra</th>
                <th class="text-right">Valor H.E.</th>
                <th class="text-right">Comisiones</th>
                <th class="text-right">Bonificaciones</th>
                <th class="text-right">Descuentos</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pagoSueldo->detalles as $detalle)
                <tr>
                    <td>
                        <strong>{{ $detalle->employee_name }}</strong>
                        @if($detalle->observaciones)
                            <br><small style="color: #6c757d;">{{ $detalle->observaciones }}</small>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($detalle->employee_type === 'App\Models\Trabajador')
                            <span class="badge">Trabajador</span>
                        @else
                            <span class="badge badge-success">Usuario</span>
                        @endif
                    </td>
                    <td class="text-right">Q{{ number_format($detalle->sueldo_base, 2) }}</td>
                    <td class="text-right">
                        @if($detalle->horas_extra > 0)
                            {{ number_format($detalle->horas_extra, 1) }}h
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($detalle->valor_hora_extra > 0)
                            Q{{ number_format($detalle->valor_hora_extra, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($detalle->comisiones > 0)
                            Q{{ number_format($detalle->comisiones, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($detalle->bonificaciones > 0)
                            Q{{ number_format($detalle->bonificaciones, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($detalle->descuentos > 0)
                            -Q{{ number_format($detalle->descuentos, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right"><strong>Q{{ number_format($detalle->total_pagar, 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="8" class="text-right">TOTAL DEL LOTE:</th>
                <th class="text-right" style="color: #28a745;">Q{{ number_format($pagoSueldo->total_monto, 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <!-- Resumen de Totales -->
    <div class="summary-section">
        <div class="summary-title">Resumen de Totales</div>
        <table style="width: 100%; border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 25%;"><strong>Total Sueldo Base:</strong></td>
                <td style="border: none; width: 25%;">Q{{ number_format($pagoSueldo->detalles->sum('sueldo_base'), 2) }}</td>
                <td style="border: none; width: 25%;"><strong>Total Horas Extra:</strong></td>
                <td style="border: none; width: 25%;">Q{{ number_format($pagoSueldo->detalles->sum(function($detalle) { return $detalle->horas_extra * $detalle->valor_hora_extra; }), 2) }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none;"><strong>Total Comisiones:</strong></td>
                <td style="border: none;">Q{{ number_format($pagoSueldo->detalles->sum('comisiones'), 2) }}</td>
                <td style="border: none;"><strong>Total Bonificaciones:</strong></td>
                <td style="border: none;">Q{{ number_format($pagoSueldo->detalles->sum('bonificaciones'), 2) }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none;"><strong>Total Descuentos:</strong></td>
                <td style="border: none;">-Q{{ number_format($pagoSueldo->detalles->sum('descuentos'), 2) }}</td>
                <td style="border: none;"><strong>TOTAL GENERAL:</strong></td>
                <td style="border: none; font-weight: bold; color: #28a745; font-size: 14px;">Q{{ number_format($pagoSueldo->total_monto, 2) }}</td>
            </tr>
        </table>
    </div>

    <!-- Sección de Firmas -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">Autorizado por</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Recibido por</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Fecha de Recepción</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div style="text-align: center;">
            <strong>JIREH - Car Wash & CDS</strong><br>
            Comprobante generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}<br>
            Este documento es válido como comprobante de pago de sueldos
        </div>
    </div>
</body>
</html>
