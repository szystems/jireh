<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listado de Vehículos</title>
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

        /* Estilos específicos para vehículos */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 8px;
            border-radius: 3px;
        }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; color: white; }
        .badge-primary { background-color: #007bff; color: white; }
        .badge-success { background-color: #28a745; color: white; }

        .compact-cell {
            line-height: 1.2;
            padding: 2px !important;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="logo-container">
            @if (isset($imagen))
                <img src="{{ $imagen }}" alt="Logo de la empresa" class="company-logo">
            @endif
        </div>

        <div class="header">
            <h1>LISTADO DE VEHÍCULOS</h1>
            <p>{{ isset($config) ? $config->nombre_negocio : 'Jireh Automotriz' }} | {{ isset($config) ? $config->telefono : '' }}</p>
        </div>

        <!-- Filtros aplicados -->
        <div class="filters-info">
            <strong>Fecha Reporte:</strong> {{ date('d/m/Y') }}
            @if(request('fvehiculo'))
                | <strong>Búsqueda:</strong> {{ request('fvehiculo') }}
            @endif
            @if(request('fano'))
                | <strong>Año:</strong> {{ request('fano') }}
            @endif
            @if(request('fcliente'))
                @php
                    $clienteNombre = DB::table('clientes')->where('id', request('fcliente'))->value('nombre');
                @endphp
                | <strong>Cliente:</strong> {{ $clienteNombre ?? request('fcliente') }}
            @endif
        </div>

        <!-- Resumen estadístico -->
        <div class="summary-section">
            <table style="border: none; margin: 0;">
                <tr>
                    <td width="25%" class="text-center">
                        <div class="text-primary text-bold">Total Vehículos</div>
                        <div>{{ $vehiculos->count() }}</div>
                    </td>
                    <td width="25%" class="text-center">
                        <div class="text-success text-bold">Clientes con Vehículos</div>
                        <div>{{ $vehiculos->groupBy('cliente_id')->count() }}</div>
                    </td>
                    <td width="25%" class="text-center">
                        <div class="text-info text-bold">Marcas Registradas</div>
                        <div>{{ $vehiculos->groupBy('marca')->count() }}</div>
                    </td>
                    <td width="25%" class="text-center">
                        <div class="text-warning text-bold">Con Ventas</div>
                        @php
                            $conVentas = 0;
                            foreach($vehiculos as $vehiculo) {
                                if(isset($vehiculo->ventas) && $vehiculo->ventas->where('estado', 1)->count() > 0) {
                                    $conVentas++;
                                }
                            }
                        @endphp
                        <div>{{ $conVentas }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tabla de vehículos -->
        <div class="section-title">VEHÍCULOS REGISTRADOS</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="20%">Vehículo</th>
                    <th width="15%">Detalles</th>
                    <th width="25%">Cliente</th>
                    <th width="18%">Contacto</th>
                    <th width="17%">Historial</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehiculos as $vehiculo)
                    <tr>
                        <td class="text-center">{{ $vehiculo->id }}</td>
                        <td class="compact-cell">
                            <span class="text-bold text-primary">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</span>
                            <br>
                            <small>Año: {{ $vehiculo->ano }}</small>
                        </td>
                        <td class="compact-cell">
                            <small><span class="text-muted">Color:</span> {{ $vehiculo->color }}</small><br>
                            <small><span class="text-muted">Placa:</span> <span class="text-bold">{{ $vehiculo->placa }}</span></small><br>
                            <small><span class="text-muted">VIN:</span> {{ Str::limit($vehiculo->vin, 14) }}</small>
                        </td>
                        <td class="compact-cell">
                            @if($vehiculo->cliente)
                                <span class="text-bold">{{ $vehiculo->cliente->nombre }}</span>
                                @if($vehiculo->cliente->fecha_nacimiento)
                                    @php
                                        $edad = date_diff(date_create($vehiculo->cliente->fecha_nacimiento), date_create('today'))->y;
                                    @endphp
                                    <small>({{ $edad }} años)</small>
                                @endif
                                <br>
                                <small>DPI: {{ $vehiculo->cliente->dpi ?: 'No registrado' }}</small><br>
                                <small>NIT: {{ $vehiculo->cliente->nit ?: 'No registrado' }}</small>
                            @else
                                <span class="text-danger">Cliente no disponible</span>
                            @endif
                        </td>
                        <td class="compact-cell">
                            @if($vehiculo->cliente)
                                @if($vehiculo->cliente->telefono)
                                    <small><span class="text-muted">Tel:</span> {{ $vehiculo->cliente->telefono }}</small><br>
                                @endif
                                @if($vehiculo->cliente->celular)
                                    <small><span class="text-muted">Cel:</span> {{ $vehiculo->cliente->celular }}</small><br>
                                @endif
                                @if($vehiculo->cliente->email)
                                    <small><span class="text-muted">Email:</span> <span class="text-info">{{ $vehiculo->cliente->email }}</span></small>
                                @endif
                            @else
                                <span class="text-muted">Sin información</span>
                            @endif
                        </td>
                        <td class="compact-cell">
                            @if(isset($vehiculo->ventas))
                                @php
                                    $ventasCount = $vehiculo->ventas->where('estado', 1)->count();
                                    $totalMontoVentas = 0;

                                    foreach($vehiculo->ventas->where('estado', 1) as $venta) {
                                        if($venta->detalleVentas) {
                                            $totalMontoVentas += $venta->detalleVentas->sum('sub_total');
                                        }
                                    }

                                    $ultimaVenta = $vehiculo->ventas->where('estado', 1)->sortByDesc('fecha')->first();
                                @endphp

                                @if($ventasCount > 0)
                                    <small><span class="text-muted">Ventas:</span> <span class="badge badge-primary">{{ $ventasCount }}</span></small><br>
                                    <small><span class="text-muted">Total:</span> <span class="text-bold">{{ isset($config) ? $config->currency_simbol : 'Q' }}{{ number_format($totalMontoVentas, 2) }}</span></small><br>
                                    <small><span class="text-muted">Última:</span> {{ $ultimaVenta ? date('d/m/Y', strtotime($ultimaVenta->fecha)) : 'N/A' }}</small>
                                @else
                                    <span class="text-muted">Sin historial de ventas</span>
                                @endif
                            @else
                                <span class="text-muted">Datos no disponibles</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>Documento generado el {{ date('d/m/Y H:i:s') }}</p>
            <p>{{ isset($config) ? $config->nombre_negocio : 'Jireh Automotriz' }} | Página 1</p>
        </div>
    </div>
</body>
</html>
