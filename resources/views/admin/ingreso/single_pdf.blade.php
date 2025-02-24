<!DOCTYPE html>
<html>
<head>
    <title>Ingreso #{{ $ingreso->id }}</title>
    <style>
        body {
            font-size: 10px;
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 4px;
            text-align: left;
        }
        th {
            background-color: #333;
            color: white;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 0;
            font-size: 12px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: left;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }
        .details-table th, .details-table td {
            padding: 8px;
        }
        .details-table th {
            background-color: #000;
            color: white;
            font-size: 12px;
        }
        .details-table td {
            font-size: 12px;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .info-table th, .info-table td {
            padding: 4px;
            border: none;
        }
        .info-table th {
            width: 30%;
            text-align: left;
        }
        .info-table td {
            width: 70%;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($config->logo)
            <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo" height="50">
        @endif
        <h2>Ingreso</h2>
        <p>Fecha de generación: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
    </div>
    <div class="section-title">Información del Ingreso</div>
    <table class="info-table">
        <tr>
            <th>Fecha</th>
            <td>{{ \Carbon\Carbon::parse($ingreso->fecha)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Número de Factura</th>
            <td>{{ $ingreso->numero_factura }}</td>
        </tr>
        <tr>
            <th>Proveedor</th>
            <td>{{ optional($ingreso->proveedor)->nombre ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th>Tipo de Compra</th>
            <td>{{ $ingreso->tipo_compra }}</td>
        </tr>
        <tr>
            <th>Usuario</th>
            <td>{{ $ingreso->usuario ? $ingreso->usuario->name : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Total</th>
            <td class="text-warning">{{ $config->currency_simbol }}.{{ number_format($ingreso->detalles->sum('precio_compra'), 2, '.', ',') }}</td>
        </tr>
    </table>
    <div class="section-title">Detalles del Ingreso</div>
    <table class="details-table">
        <thead>
            <tr>
                <th>Artículo</th>
                <th class="text-center">Cantidad</th>
                <th class="text-end">Precio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ingreso->detalles as $detalle)
                <tr>
                    <td>{{ optional($detalle->articulo)->nombre }}</td>
                    <td class="text-center">{{ $detalle->cantidad }}</td>
                    <td class="text-end">{{ $config->currency_simbol }}.{{ number_format($detalle->precio_compra, 2, '.', ',') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
