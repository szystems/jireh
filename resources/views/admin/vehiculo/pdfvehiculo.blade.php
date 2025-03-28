<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalles del Vehículo</title>
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

        .vehicle-info {
            display: flex;
            margin-bottom: 15px;
        }
        .vehicle-details {
            width: 70%;
        }
        .vehicle-image {
            width: 30%;
            text-align: center;
            padding-right: 15px;
        }
        .vehicle-photo {
            max-width: 100%;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
        }
        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 9px;
            color: white;
            font-weight: bold;
        }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
        .badge-info { background-color: #17a2b8; }
        .badge-primary { background-color: #007bff; }
    </style>
</head>
<body>
    <div class="content">
        <!-- Logo integrado en el flujo normal del documento -->
        <div class="logo-container">
            @if (isset($imagen))
                <img src="{{ $imagen }}" alt="Logo de la empresa" class="company-logo">
            @endif
        </div>

        <div class="header">
            <h1>FICHA TÉCNICA DEL VEHÍCULO</h1>
            <p>{{ isset($config) ? $config->nombre_negocio : 'Jireh Automotriz' }} | {{ isset($config) ? $config->telefono : '' }}</p>
        </div>

        <div class="vehicle-info">
            <div class="vehicle-details">
                <div class="section-title">INFORMACIÓN DEL VEHÍCULO</div>
                <table class="table table-striped">
                    <tr>
                        <th width="30%">Marca / Modelo:</th>
                        <td><strong>{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</strong></td>
                    </tr>
                    <tr>
                        <th>Año:</th>
                        <td>{{ $vehiculo->ano }}</td>
                    </tr>
                    <tr>
                        <th>Color:</th>
                        <td>{{ $vehiculo->color }}</td>
                    </tr>
                    <tr>
                        <th>Placa:</th>
                        <td>{{ $vehiculo->placa }}</td>
                    </tr>
                    <tr>
                        <th>VIN / Número de Chasis:</th>
                        <td>{{ $vehiculo->vin }}</td>
                    </tr>
                    @if(property_exists($vehiculo, 'tipo_vehiculo') && !empty($vehiculo->tipo_vehiculo))
                    <tr>
                        <th>Tipo de Vehículo:</th>
                        <td>{{ $vehiculo->tipo_vehiculo }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Fecha de Registro:</th>
                        <td>{{ $vehiculo->created_at instanceof \DateTime ? $vehiculo->created_at->format('d/m/Y') : date('d/m/Y', strtotime($vehiculo->created_at)) }}</td>
                    </tr>
                </table>
            </div>
            <div class="vehicle-image">
                @if ($vehiculo->fotografia)
                    <img src="{{ $pathvehiculo . $vehiculo->fotografia }}" alt="Fotografía del vehículo" class="vehicle-photo">
                @else
                    <div style="border: 1px dashed #ccc; padding: 20px; text-align: center; color: #999;">
                        No hay fotografía disponible
                    </div>
                @endif
            </div>
        </div>

        <div class="section-title">INFORMACIÓN DEL PROPIETARIO</div>
        <table class="table table-striped">
            <tr>
                <th width="20%">Nombre:</th>
                <td><strong>{{ $cliente ? $cliente->nombre : 'No disponible' }}</strong></td>
                <th width="20%">DPI:</th>
                <td>{{ $cliente ? $cliente->dpi : 'No disponible' }}</td>
            </tr>
            <tr>
                <th>Teléfono:</th>
                <td>{{ $cliente ? $cliente->telefono : 'No disponible' }}</td>
                <th>NIT:</th>
                <td>{{ $cliente ? $cliente->nit : 'No disponible' }}</td>
            </tr>
            <tr>
                <th>Celular:</th>
                <td>{{ $cliente && $cliente->celular ? $cliente->celular : 'No disponible' }}</td>
                <th>Email:</th>
                <td>{{ $cliente && $cliente->email ? $cliente->email : 'No disponible' }}</td>
            </tr>
            <tr>
                <th>Dirección:</th>
                <td colspan="3">{{ $cliente ? $cliente->direccion : 'No disponible' }}</td>
            </tr>
        </table>

        <div class="section-title">HISTORIAL DE VENTAS</div>

        @if(isset($vehiculo->ventas) && $vehiculo->ventas->where('estado', 1)->count() > 0)
            <div style="margin-bottom: 15px; background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; border-radius: 5px;">
                <div style="display: flex; justify-content: space-between;">
                    <div style="flex: 1; text-align: center;">
                        <div style="font-weight: bold;">Total de Ventas:</div>
                        <div>{{ $vehiculo->ventas->where('estado', 1)->count() }}</div>
                    </div>
                    <div style="flex: 1; text-align: center;">
                        <div style="font-weight: bold;">Primera Venta:</div>
                        @php
                            $primeraVenta = $vehiculo->ventas->where('estado', 1)->sortBy('fecha')->first();
                            echo $primeraVenta ? date('d/m/Y', strtotime($primeraVenta->fecha)) : 'N/A';
                        @endphp
                    </div>
                    <div style="flex: 1; text-align: center;">
                        <div style="font-weight: bold;">Última Venta:</div>
                        @php
                            $ultimaVenta = $vehiculo->ventas->where('estado', 1)->sortByDesc('fecha')->first();
                            echo $ultimaVenta ? date('d/m/Y', strtotime($ultimaVenta->fecha)) : 'N/A';
                        @endphp
                    </div>
                    <div style="flex: 1; text-align: center;">
                        @php
                            $totalVentas = 0;
                            foreach($vehiculo->ventas->where('estado', 1) as $venta) {
                                if($venta->detalleVentas) {
                                    $totalVentas += $venta->detalleVentas->sum('sub_total');
                                }
                            }
                        @endphp
                        <div style="font-weight: bold;">Monto Total:</div>
                        <div>{{ isset($config) ? $config->currency_simbol : 'Q' }}{{ number_format($totalVentas, 2) }}</div>
                    </div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Número Factura</th>
                        <th>Productos/Servicios</th>
                        <th>Estado</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehiculo->ventas->where('estado', 1)->sortByDesc('fecha') as $index => $venta)
                        @php
                            $totalVenta = 0;
                            if ($venta->detalleVentas && $venta->detalleVentas->count() > 0) {
                                $totalVenta = $venta->detalleVentas->sum('sub_total');
                            }

                            // Calcular el total pagado
                            $totalPagado = 0;
                            if ($venta->pagos && $venta->pagos->count() > 0) {
                                $totalPagado = $venta->pagos->sum('monto');
                            }

                            // Determinar el estado del pago
                            $estadoPago = 'pendiente';
                            $badgeClass = 'badge-warning';
                            if ($totalPagado >= $totalVenta) {
                                $estadoPago = 'pagada';
                                $badgeClass = 'badge-success';
                            } elseif ($totalPagado > 0) {
                                $estadoPago = 'parcial';
                                $badgeClass = 'badge-primary';
                            }
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ date('d/m/Y', strtotime($venta->fecha)) }}</td>
                            <td>{{ $venta->tipo_venta }}</td>
                            <td>{{ $venta->numero_factura ?? 'Sin factura' }}</td>
                            <td>
                                @if($venta->detalleVentas && $venta->detalleVentas->count() > 0)
                                    @foreach($venta->detalleVentas->take(3) as $detalle)
                                        - {{ $detalle->articulo->nombre ?? 'Artículo #'.$detalle->articulo_id }} ({{ $detalle->cantidad }})<br>
                                    @endforeach
                                    @if($venta->detalleVentas->count() > 3)
                                        <span style="color: #7f8c8d;">... y {{ $venta->detalleVentas->count() - 3 }} más</span>
                                    @endif
                                @else
                                    <span style="color: #7f8c8d;">Sin detalles</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($estadoPago) }}</span>
                            </td>
                            <td class="text-right">{{ isset($config) ? $config->currency_simbol : 'Q' }} {{ number_format($totalVenta, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6" class="text-right">TOTAL:</th>
                        <th class="text-right">{{ isset($config) ? $config->currency_simbol : 'Q' }} {{ number_format($totalVentas, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        @else
            <div style="text-align: center; padding: 20px; border: 1px dashed #ccc; color: #999;">
                <p>No hay ventas registradas para este vehículo.</p>
            </div>
        @endif

        <div class="footer">
            <p>Documento generado el {{ date('d/m/Y H:i:s') }}</p>
            <p>{{ isset($config) ? $config->nombre_negocio : 'Jireh Automotriz' }} | {{ isset($config) ? $config->telefono : '' }}</p>
        </div>
    </div>
</body>
</html>
