<!DOCTYPE html>
<html>
<head>
    <title>Lista de Ingresos</title>
    <style>
        body {
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        th, td {
            border: 1px solid black;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #000;
            color: white;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .filters {
            margin-bottom: 10px;
            font-size: 10px;
            color: #555;
        }
        .filters strong {
            text-decoration: underline;
        }
        .filters span {
            font-weight: bold;
        }
        .text-end {
            text-align: right;
        }
        .details-table {
            font-size: 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($config->logo)
            <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo" height="50">
        @endif
        <h2>Lista de Ingresos</h2>
        <p>Fecha de generación: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
    </div>
    <div class="filters">
        <strong>Filtros utilizados:</strong>
        <span>Fecha Desde:</span> {{ \Carbon\Carbon::parse($filters['fecha_desde'] ?? \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'))->format('d/m/Y') }},
        <span>Fecha Hasta:</span> {{ \Carbon\Carbon::parse($filters['fecha_hasta'] ?? \Carbon\Carbon::now()->format('Y-m-d'))->format('d/m/Y') }}
        @if($filters['numero_factura'])
            , <span>Número de Factura:</span> {{ $filters['numero_factura'] }}
        @endif
        @if($filters['proveedor'] && $proveedores->find($filters['proveedor']))
            , <span>Proveedor:</span> {{ $proveedores->find($filters['proveedor'])->nombre }}
        @endif
        @if($filters['tipo_compra'])
            , <span>Tipo de Compra:</span> {{ $filters['tipo_compra'] }}
        @endif
        @if($filters['usuario'] && $usuarios->find($filters['usuario']))
            , <span>Usuario:</span> {{ $usuarios->find($filters['usuario'])->name }}
        @endif
    </div>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Número de Factura</th>
                <th>Proveedor</th>
                <th>Tipo de Compra</th>
                <th>Usuario</th>
                <th>Detalles</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ingresos as $ingreso)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($ingreso->fecha)->format('d/m/Y') }}</td>
                    <td>{{ $ingreso->numero_factura }}</td>
                    <td>{{ optional($ingreso->proveedor)->nombre ?? 'N/A' }}</td>
                    <td>{{ $ingreso->tipo_compra }}</td>
                    <td>{{ $ingreso->usuario ? $ingreso->usuario->name : 'N/A' }}</td>
                    <td>
                        <table class="details-table">
                            <thead>
                                <tr>
                                    <th>Artículo</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ingreso->detalles as $detalle)
                                    <tr>
                                        <td>{{ optional($detalle->articulo)->nombre ?? 'N/A' }}</td>
                                        <td>{{ $detalle->cantidad }}</td>
                                        <td>{{ $config->currency_simbol }}.{{ number_format($detalle->precio_compra, 2, '.', ',') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                    <td class="text-end">{{ $config->currency_simbol }}.{{ number_format($ingreso->detalles->sum('precio_compra'), 2, '.', ',') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-end"><strong>Total Gastado:</strong></td>
                <td class="text-end text-warning"><strong>{{ $config->currency_simbol }}.{{ number_format($ingresos->sum(function($ingreso) { return $ingreso->detalles->sum('precio_compra'); }), 2, '.', ',') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
