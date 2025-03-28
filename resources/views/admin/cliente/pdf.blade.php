<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Listado de Clientes') }}</title>
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

        /* Estilos específicos para clientes */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 8px;
            border-radius: 3px;
        }
        .badge-edad {
            background-color: #3498db;
            color: white;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="logo-container">
            @if(isset($imagen))
                <img src="{{ $imagen }}" alt="Logo de la empresa" class="company-logo">
            @endif
        </div>

        <div class="header">
            <h1>{{ __('LISTADO DE CLIENTES') }}</h1>
            <p>{{ $config->nombre_negocio ?? 'Jireh Automotriz' }} | {{ $config->telefono ?? '' }}</p>
        </div>

        <!-- Filtros y meta información -->
        <div class="filters-info">
            <strong>{{ __('Fecha del Reporte:') }}</strong> {{ date('d/m/Y H:i') }}
            <strong class="ml-3">Total de clientes:</strong> {{ $clientes->count() }}
            @if(isset($filtros) && !empty($filtros))
                @foreach($filtros as $key => $value)
                    @if(!empty($value))
                        | <strong>{{ ucfirst($key) }}:</strong> {{ $value }}
                    @endif
                @endforeach
            @endif
        </div>

        <!-- Estadísticas de clientes -->
        <div class="summary-section">
            <table style="border: none; margin: 0;">
                <tr>
                    <td width="33%" class="text-center">
                        <div class="text-primary text-bold">Total Clientes</div>
                        <div>{{ $clientes->count() }}</div>
                    </td>
                    <td width="33%" class="text-center">
                        <div class="text-success text-bold">Con Vehículos</div>
                        <div>{{ $clientes->filter(function($c) { return isset($c->vehiculos) && $c->vehiculos->count() > 0; })->count() }}</div>
                    </td>
                    <td width="33%" class="text-center">
                        <div class="text-info text-bold">Con Ventas</div>
                        <div>{{ $clientes->filter(function($c) { return isset($c->ventas) && $c->ventas->count() > 0; })->count() }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tabla principal de clientes -->
        <div class="section-title">{{ __('CLIENTES REGISTRADOS') }}</div>
        <table>
            <thead>
                <tr>
                    <th width="30%">{{ __('Información Personal') }}</th>
                    <th width="25%">{{ __('Contacto') }}</th>
                    <th width="15%">{{ __('Fecha Nac.') }}</th>
                    <th width="15%">{{ __('DPI/NIT') }}</th>
                    <th width="15%">{{ __('Vehículos') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $cliente)
                    <tr>
                        <td>
                            <div class="text-bold">{{ $cliente->nombre }}</div>
                            <div>{{ $cliente->direccion }}</div>
                        </td>
                        <td>
                            <div>
                                {{ $cliente->telefono }}
                                @if($cliente->celular)
                                    <br>{{ $cliente->celular }}
                                @endif
                                @if($cliente->email)
                                    <br><span class="text-info">{{ $cliente->email }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @php
                                $fnacimiento = null;
                                $edad = 0;
                                if ($cliente->fecha_nacimiento != null) {
                                    $fnacimiento = date("d/m/Y", strtotime($cliente->fecha_nacimiento));
                                    //calcular edad
                                    $cumpleanos = new DateTime($cliente->fecha_nacimiento);
                                    $hoy = new DateTime();
                                    $annos = $hoy->diff($cumpleanos);
                                    $edad = $annos->y;
                                }
                            @endphp
                            {{ $fnacimiento ?? 'N/A' }}
                            @if ($edad > 0)
                                <br><span class="badge badge-edad">{{ $edad }} años</span>
                            @endif
                        </td>
                        <td>
                            <div>DPI: {{ $cliente->dpi ?: 'N/A' }}</div>
                            <div>NIT: {{ $cliente->nit ?: 'N/A' }}</div>
                        </td>
                        <td class="text-center">
                            @if(isset($cliente->vehiculos))
                                {{ $cliente->vehiculos->count() }} vehículo(s)
                            @else
                                0
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">No hay clientes registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <p>Documento generado el {{ date('d/m/Y H:i:s') }}</p>
            <p>{{ $config->nombre_negocio ?? 'Jireh Automotriz' }} | Página 1</p>
        </div>
    </div>
</body>
</html>
