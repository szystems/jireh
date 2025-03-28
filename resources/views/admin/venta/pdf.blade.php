<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Ventas</title>
    <style>
        @page {
            size: landscape;
            margin: 10px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 5px;
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
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 16px;
        }
        .header p {
            margin: 5px 0;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 3px;
            font-size: 9px;
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
            padding: 3px;
            margin: 8px 0 5px;
            font-weight: bold;
            text-align: center;
            font-size: 11px;
        }
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 8px;
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

        .filters-info {
            clear: both;
            font-size: 8px;
            margin-top: 5px;
            margin-bottom: 5px;
            text-align: center;
            border: 1px solid #ddd;
            padding: 3px;
            background-color: #f8f9fa;
        }

        .summary-section {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 5px;
            margin-bottom: 10px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table th, .summary-table td {
            padding: 2px 4px;
            font-size: 8px;
        }

        .badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 8px;
        }
        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-primary { background-color: #007bff; color: white; }
        .badge-secondary { background-color: #6c757d; color: white; }

        .moneda {
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="content">
        <!-- Añadir el logo integrado al inicio del contenido -->
        <div class="logo-container">
            @if(isset($config) && $config->logo)
                <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo de la empresa" class="company-logo">
            @endif
        </div>

        <!-- Cabecera compacta -->
        <div class="header">
            <h1>REPORTE DE VENTAS</h1>
            <p>{{ $config->nombre_negocio ?? '' }} | {{ $config->telefono ?? '' }}</p>
        </div>

        <!-- Filtros aplicados -->
        <div class="filters-info">
            <strong>Filtros:</strong>
            {{ \Carbon\Carbon::parse($filters['fecha_desde'])->format('d/m/Y') }} al
            {{ \Carbon\Carbon::parse($filters['fecha_hasta'])->format('d/m/Y') }}
            @if(isset($filters['numero_factura']) && $filters['numero_factura']) | <strong>Factura:</strong> {{ $filters['numero_factura'] }} @endif
            @if(isset($filters['cliente']) && $filters['cliente']) | <strong>Cliente:</strong> {{ $filters['cliente'] }} @endif
            @if(isset($filters['tipo_venta']) && $filters['tipo_venta']) | <strong>Tipo:</strong> {{ $filters['tipo_venta'] }} @endif
            @if(isset($filters['usuario']) && $filters['usuario']) | <strong>Usuario:</strong> {{ $filters['usuario'] }} @endif
            @if(isset($filters['estado']) && $filters['estado'] !== null) | <strong>Estado:</strong> {{ $filters['estado'] }} @endif
            @if(isset($filters['estado_pago']) && $filters['estado_pago']) | <strong>Pago:</strong> {{ $filters['estado_pago'] }} @endif
        </div>

        <!-- Resumen estadístico -->
        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    @php
                        $totalVentas = 0;
                        $totalFacturado = 0;
                        $totalCobrado = 0;
                        $totalPendiente = 0;
                        $ventasPagadas = 0;
                        $ventasPendientes = 0;
                        $ventasParciales = 0;

                        foreach($ventas as $venta) {
                            if($venta->estado == 1) {
                                $totalVentas++;
                                $subtotal = $venta->detalleVentas->sum('sub_total');
                                $totalFacturado += $subtotal;

                                $pagado = $venta->pagos->sum('monto');
                                $totalCobrado += $pagado;
                                $pendiente = max(0, $subtotal - $pagado);
                                $totalPendiente += $pendiente;

                                if($pagado >= $subtotal) {
                                    $ventasPagadas++;
                                } elseif($pagado > 0) {
                                    $ventasParciales++;
                                } else {
                                    $ventasPendientes++;
                                }
                            }
                        }
                    @endphp
                    <td width="20%" class="text-center">
                        <div class="text-primary text-bold">Total Ventas</div>
                        <div>{{ $totalVentas }}</div>
                    </td>
                    <td width="20%" class="text-center">
                        <div class="text-success text-bold">Monto Facturado</div>
                        <div>{{ $config->currency_simbol }}.{{ number_format($totalFacturado, 2, '.', ',') }}</div>
                    </td>
                    <td width="20%" class="text-center">
                        <div class="text-info text-bold">Monto Cobrado</div>
                        <div>{{ $config->currency_simbol }}.{{ number_format($totalCobrado, 2, '.', ',') }}</div>
                    </td>
                    <td width="20%" class="text-center">
                        <div class="text-danger text-bold">Pendiente por Cobrar</div>
                        <div>{{ $config->currency_simbol }}.{{ number_format($totalPendiente, 2, '.', ',') }}</div>
                    </td>
                    <td width="20%" class="text-center">
                        <div class="text-secondary text-bold">Estado Pagos</div>
                        <br>
                        <div>
                            <span class="badge badge-success">{{ $ventasPagadas }} Pagadas</span>
                            <span class="badge badge-info">{{ $ventasParciales }} Parciales</span>
                            <span class="badge badge-warning">{{ $ventasPendientes }} Pendientes</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tabla de ventas -->
        <div class="section-title">LISTADO DE VENTAS</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="8%">Fecha</th>
                    <th width="8%">Factura</th>
                    <th width="20%">Cliente</th>
                    <th width="10%">Vehículo</th>
                    <th width="10%">Tipo</th>
                    <th width="15%">Productos/Servicios</th>
                    <th width="8%">Estado Venta</th>
                    <th width="8%">Estado Pago</th>
                    <th width="8%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ventas as $venta)
                    @php
                        $total = $venta->detalleVentas->sum('sub_total');
                        $pagado = $venta->pagos->sum('monto');

                        // Determinar estado de pago
                        $estadoPago = 'Pendiente';
                        $badgePago = 'badge-warning';

                        if($pagado >= $total) {
                            $estadoPago = 'Pagada';
                            $badgePago = 'badge-success';
                        } elseif($pagado > 0) {
                            $estadoPago = 'Parcial';
                            $badgePago = 'badge-info';
                        }
                    @endphp
                    <tr>
                        <td>{{ $venta->id }}</td>
                        <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $venta->numero_factura ?? 'No emitida' }}</td>
                        <td>
                            @if($venta->cliente)
                                <span class="text-primary text-bold">{{ $venta->cliente->nombre }}</span>
                                @if($venta->cliente->telefono)
                                    <br><small>Tel: {{ $venta->cliente->telefono }}</small>
                                @endif
                            @else
                                <span class="text-muted">Cliente no disponible</span>
                            @endif
                        </td>
                        <td>
                            @if($venta->vehiculo)
                                {{ $venta->vehiculo->marca }} {{ $venta->vehiculo->modelo }}
                                <br><small>{{ $venta->vehiculo->placa }}</small>
                            @else
                                <span class="text-muted">Sin vehículo</span>
                            @endif
                        </td>
                        <td>{{ $venta->tipo_venta }}</td>
                        <td>
                            @php
                                $detalles = $venta->detalleVentas;
                                $totalItems = $detalles->count();
                            @endphp

                            @if($totalItems > 0)
                                <small>
                                    {{ $totalItems }} item(s):
                                    @foreach($detalles->take(1) as $detalle)
                                        @if($detalle->articulo)
                                            {{ $detalle->articulo->nombre ?? 'Producto' }}
                                        @else
                                            Producto #{{ $detalle->articulo_id }}
                                        @endif
                                    @endforeach
                                    @if($totalItems > 1)
                                        y {{ $totalItems - 1 }} más
                                    @endif
                                </small>
                            @else
                                <span class="text-muted">Sin detalles</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $venta->estado == 1 ? 'badge-primary' : 'badge-danger' }}">
                                {{ $venta->estado == 1 ? 'Activa' : 'Cancelada' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $badgePago }}">{{ $estadoPago }}</span>
                            @if($pagado > 0 && $pagado < $total)
                                <br><small>{{ number_format(($pagado / $total) * 100, 0) }}%</small>
                            @endif
                        </td>
                        <td class="text-right moneda">{{ $config->currency_simbol }}.{{ number_format($total, 2, '.', ',') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p>{{ $config->nombre_negocio ?? 'Sistema de Gestión' }} | Página 1</p>
    </div>
</body>
</html>
