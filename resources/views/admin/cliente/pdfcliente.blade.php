<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ficha de Cliente: {{ $cliente->nombre }}</title>
    <style>
        @page {
            size: portrait;
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
            margin: 10px 0 5px;
            font-weight: bold;
            text-align: center;
            font-size: 12px;
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

        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 9px;
        }

        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-primary { background-color: #007bff; color: white; }
        .badge-info { background-color: #17a2b8; color: white; }

        .info-table td {
            font-size: 10px;
            padding: 7px;
        }

        .info-table th {
            font-size: 10px;
            background-color: #17a2b8;
            color: white;
            padding: 7px;
        }

        /* Estilo para vehículos */
        .vehiculos-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .vehiculo-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 8px;
            width: 48%;
        }

        /* Estilo para estadísticas */
        .stats-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .stat-item {
            text-align: center;
            padding: 5px;
            background-color: #f8f9fa;
            border-radius: 5px;
            width: 24%;
        }

        .stat-value {
            font-weight: bold;
            font-size: 12px;
        }

        .stat-label {
            font-size: 8px;
            color: #6c757d;
        }

        /* Estilo para la foto del cliente */
        .client-photo-container {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .client-photo {
            max-width: 200px;
            max-height: 200px;
            border: 3px solid #ddd;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="content">
        <!-- Logo y encabezado -->
        <div class="logo-container">
            @if(isset($imagen) && $imagen && file_exists($imagen))
                <img src="{{ $imagen }}" alt="Logo de la empresa" class="company-logo">
            @endif
        </div>

        <!-- Cabecera con título -->
        <div class="header">
            <h1>FICHA DE CLIENTE</h1>
            <p>{{ isset($config) ? $config->nombre_negocio : '' }} |
               {{ isset($config) ? $config->telefono : '' }}</p>
        </div>

        <!-- Información básica del cliente -->
        <div class="section-title">DATOS GENERALES</div>
        <table class="info-table">
            <tr>
                <th width="20%">Nombre Completo</th>
                <td width="30%"><strong>{{ $cliente->nombre }}</strong></td>
                <th width="20%">Fecha de Registro</th>
                <td width="30%">{{ date('d/m/Y', strtotime($cliente->created_at)) }}</td>
            </tr>
            <tr>
                <th>DPI</th>
                <td>{{ $cliente->dpi ?: 'No disponible' }}</td>
                <th>NIT</th>
                <td>{{ $cliente->nit ?: 'No disponible' }}</td>
            </tr>
            <tr>
                <th>Fecha de Nacimiento</th>
                <td>
                    @php
                        $fnacimiento = null;
                        $edad = 0;
                        if ($cliente->fecha_nacimiento != null) {
                            $fnacimiento = date("d/m/Y", strtotime($cliente->fecha_nacimiento));
                            $cumpleanos = new DateTime($cliente->fecha_nacimiento);
                            $hoy = new DateTime();
                            $annos = $hoy->diff($cumpleanos);
                            $edad = $annos->y;
                        }
                    @endphp
                    {{ $fnacimiento ?? 'No disponible' }}
                    @if ($edad > 0)
                        <span class="badge badge-warning">{{ $edad }} años</span>
                    @endif
                </td>
                <th>Estado</th>
                <td>
                    <span class="badge {{ $cliente->estado ? 'badge-success' : 'badge-danger' }}">
                        {{ $cliente->estado ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
            </tr>
        </table>

        <!-- Sección de Contacto -->
        <div class="section-title">INFORMACIÓN DE CONTACTO</div>
        <table class="info-table">
            <tr>
                <th width="20%">Teléfono</th>
                <td width="30%">{{ $cliente->telefono ?: 'No disponible' }}</td>
                <th width="20%">Celular</th>
                <td width="30%">{{ $cliente->celular ?: 'No disponible' }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $cliente->email ?: 'No disponible' }}</td>
                <th>Tipo Cliente</th>
                <td>
                    @if($cliente->tipo_cliente == 1)
                        <span class="badge badge-primary">Personal</span>
                    @elseif($cliente->tipo_cliente == 2)
                        <span class="badge badge-info">Empresarial</span>
                    @else
                        <span class="badge badge-secondary">No especificado</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Dirección</th>
                <td colspan="3">{{ $cliente->direccion ?: 'No disponible' }}</td>
            </tr>
        </table>

        <!-- Sección de vehículos -->
        <div class="section-title">VEHÍCULOS DEL CLIENTE</div>

        @if(isset($vehiculos) && $vehiculos->count() > 0)
            <div class="vehiculos-grid">
                @foreach($vehiculos as $vehiculo)
                    <div class="vehiculo-card">
                        <table class="info-table" style="margin: 0;">
                            <tr>
                                <th colspan="2">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->ano }})</th>
                            </tr>
                            <tr>
                                <td width="30%" class="text-bold">Placa:</td>
                                <td>{{ $vehiculo->placa }}</td>
                            </tr>
                            <tr>
                                <td class="text-bold">Color:</td>
                                <td>{{ $vehiculo->color }}</td>
                            </tr>
                            <tr>
                                <td class="text-bold">VIN:</td>
                                <td>{{ $vehiculo->vin ?: 'No especificado' }}</td>
                            </tr>
                        </table>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 15px; border: 1px dashed #ccc; color: #777; margin-bottom: 15px;">
                No hay vehículos registrados para este cliente.
            </div>
        @endif

        <!-- Sección de ventas -->
        <div class="section-title">HISTORIAL DE VENTAS</div>

        @if(isset($ventas) && $ventas->count() > 0)
            <!-- Resumen estadístico de ventas -->
            @php
                // Calcular ventas pagadas basado en los pagos reales
                $ventasPagadas = 0;
                $ventasPendientes = 0;
                $ventasParciales = 0;
                $totalVentas = 0;
                $totalCobrado = 0;

                foreach($ventas as $venta) {
                    $totalVenta = $venta->detalleVentas->sum('sub_total');
                    $totalPagado = $venta->pagos->sum('monto');

                    // Acumular totales
                    $totalVentas += $totalVenta;
                    $totalCobrado += min($totalVenta, $totalPagado);

                    // Contar estados
                    if ($venta->estado == 1) {
                        if ($totalPagado >= $totalVenta) {
                            $ventasPagadas++;
                        } elseif ($totalPagado > 0) {
                            $ventasParciales++;
                        } else {
                            $ventasPendientes++;
                        }
                    }
                }
            @endphp

            <div class="stats-container">
                <div class="stat-item">
                    <div class="stat-value">{{ $ventas->count() }}</div>
                    <div class="stat-label">TOTAL VENTAS</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value text-success">{{ $ventasPagadas }}</div>
                    <div class="stat-label">VENTAS PAGADAS</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value text-warning">{{ $ventasPendientes + $ventasParciales }}</div>
                    <div class="stat-label">VENTAS PENDIENTES</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ isset($currency) ? $currency : 'Q' }} {{ number_format($totalVentas, 2) }}</div>
                    <div class="stat-label">MONTO TOTAL</div>
                </div>
            </div>

            <!-- Tabla de ventas -->
            <table class="info-table">
                <thead>
                    <tr>
                        <th width="10%">ID/Factura</th>
                        <th width="15%">Fecha</th>
                        <th width="35%">Detalle</th>
                        <th width="15%">Estado</th>
                        <th width="25%" class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ventas as $venta)
                        @php
                            // Determinar el estado del pago
                            $totalVenta = $venta->detalleVentas->sum('sub_total');
                            $totalPagado = $venta->pagos->sum('monto');

                            $estadoPago = 'Pendiente';
                            $badgePago = 'badge-warning';

                            if ($totalPagado >= $totalVenta) {
                                $estadoPago = 'Pagada';
                                $badgePago = 'badge-success';
                            } elseif ($totalPagado > 0) {
                                $estadoPago = 'Parcial';
                                $badgePago = 'badge-info';
                            }
                        @endphp
                        <tr>
                            <td>
                                {{ $venta->id }}
                                @if($venta->numero_factura)
                                    <br><small>{{ $venta->numero_factura }}</small>
                                @endif
                            </td>
                            <td>{{ date('d/m/Y', strtotime($venta->fecha)) }}</td>
                            <td>
                                @if($venta->vehiculo)
                                    <span class="badge badge-primary">
                                        {{ $venta->vehiculo->marca }} {{ $venta->vehiculo->modelo }}
                                    </span><br>
                                @endif

                                @if($venta->detalleVentas && $venta->detalleVentas->count() > 0)
                                    @foreach($venta->detalleVentas->take(2) as $detalle)
                                        - {{ $detalle->articulo->nombre ?? 'Artículo '.$detalle->articulo_id }} ({{ $detalle->cantidad }})<br>
                                    @endforeach
                                    @if($venta->detalleVentas->count() > 2)
                                        <small>... y {{ $venta->detalleVentas->count() - 2 }} más</small>
                                    @endif
                                @else
                                    <small>Sin detalles</small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $badgePago }}">{{ $estadoPago }}</span>
                            </td>
                            <td class="text-right">
                                {{ isset($currency) ? $currency : 'Q' }} {{ number_format($totalVenta, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div style="text-align: center; padding: 15px; border: 1px dashed #ccc; color: #777; margin-bottom: 15px;">
                No hay ventas registradas para este cliente.
            </div>
        @endif

        <!-- Fotografía del cliente al final del documento -->
        <div class="client-photo-container">
            <div class="section-title">FOTOGRAFÍA DEL CLIENTE</div>
            @if ($cliente->fotografia && file_exists($pathcliente.$cliente->fotografia))
                <img src="{{ $pathcliente.$cliente->fotografia }}" class="client-photo" alt="Fotografía del cliente">
            @else
                <img src="{{ public_path('assets/imgs/clientes/usericon4.png') }}" class="client-photo" alt="Imagen por defecto">
            @endif
        </div>
    </div>

    <div class="footer">
        <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }} | {{ isset($config) ? $config->nombre_negocio : 'Sistema de Gestión' }}</p>
    </div>
</body>
</html>
