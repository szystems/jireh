<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Listado de Ingresos</title>
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
        .info-section {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-cell {
            display: table-cell;
            padding: 3px 5px;
            width: 50%;
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
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 10px;
        }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .summary-table td, .summary-table th {
            padding: 3px 5px;
        }
        .page-number {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }
        .filters-box {
            border: 1px solid #ddd;
            padding: 5px;
            margin-bottom: 8px;
            background-color: #f8f9fa;
            font-size: 9px;
        }
        .filters-box strong {
            color: #007bff;
        }
        .details-table {
            margin: 2px 0;
            width: 100%;
        }
        .details-table th {
            background-color: #17a2b8;
            color: white;
            font-size: 7px;
            padding: 2px;
        }
        .details-table td {
            font-size: 7px;
            padding: 1px 2px;
        }
        .ingreso-card {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            margin-bottom: 20px;
            overflow: hidden;
            break-inside: avoid;
        }
        .ingreso-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 5px;
        }
        .ingreso-header table {
            margin-bottom: 0;
        }
        .ingreso-header th {
            background-color: #007bff;
            color: white;
        }
        .ingreso-body {
            padding: 0;
        }
        .ingreso-footer {
            background-color: #fff3cd;
            border-top: 1px solid #ffeeba;
            padding: 5px;
            text-align: right;
            font-weight: bold;
        }
        .ingreso-id {
            background-color: #6c757d;
            color: white;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 8px;
        }
        .page-break {
            page-break-after: always;
        }
        .compact-row td {
            padding: 2px 3px;
        }
        .divider-row {
            border-top: 1px dashed #ccc !important;
            border-bottom: none !important;
            height: 2px !important;
            padding: 0 !important;
        }
        .summary-table td {
            padding: 2px;
        }
        .summary-box {
            float: right;
            width: 30%;
            margin-left: 70%;
            margin-bottom: 10px;
        }
        .header div.logo-container {
            width: 25%;
            float: left;
            text-align: left;
        }
        .header div.title-container {
            width: 50%;
            float: left;
            text-align: center;
        }
        .header div.info-container {
            width: 25%;
            float: right;
            text-align: right;
        }
        .filters-info {
            clear: both;
            font-size: 8px;
            margin-top: 5px;
            margin-bottom: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="logo-container">
            @if($config->logo)
                <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo de la empresa" class="company-logo">
            @endif
        </div>

        <!-- Cabecera compacta -->
        <div class="header">
            <div class="title-container">
                <h1>LISTADO DE INGRESOS</h1>
            </div>
            <div class="info-container">
                <p>{{ $config->nombre_negocio }}<br>{{ $config->telefono }}</p>
            </div>
        </div>

        <div class="filters-info">
            Filtros: {{ \Carbon\Carbon::parse($filters['fecha_desde'] ?? \Carbon\Carbon::now()->subDays(30)->format('Y-m-d'))->format('d/m/Y') }}
            al {{ \Carbon\Carbon::parse($filters['fecha_hasta'] ?? \Carbon\Carbon::now()->format('Y-m-d'))->format('d/m/Y') }}
            @if($filters['numero_factura']) | Factura: {{ $filters['numero_factura'] }} @endif
            @if($filters['proveedor'] && $proveedores->find($filters['proveedor'])) | Proveedor: {{ $proveedores->find($filters['proveedor'])->nombre }} @endif
            @if($filters['tipo_compra']) | Tipo: {{ $filters['tipo_compra'] }} @endif
        </div>

        @php
            $totalGastado = 0;
            $totalArticulos = 0;
        @endphp

        <!-- Tabla principal optimizada para espacio -->
        <table class="table">
            <thead>
                <tr>
                    <th width="12%">Fecha</th>
                    <th width="18%">Factura</th>
                    <th width="20%">Proveedor</th>
                    <th width="10%">Tipo</th>
                    <th width="40%">Detalles</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ingresos as $ingreso)
                    @php
                        $totalIngreso = 0;
                        $cantidadArticulos = 0;
                    @endphp

                    <tr class="compact-row">
                        <td class="text-center">{{ \Carbon\Carbon::parse($ingreso->fecha)->format('d/m/Y') }}</td>
                        <td>{{ $ingreso->numero_factura ?: 'Sin factura' }}</td>
                        <td>{{ optional($ingreso->proveedor)->nombre ?? 'No especificado' }}</td>
                        <td class="text-center">
                            <span class="{{ $ingreso->tipo_compra == 'Car Wash' ? 'text-info' : 'text-warning' }} text-bold">
                                {{ $ingreso->tipo_compra }}
                            </span>
                        </td>
                        <td>
                            <!-- Tabla de detalles compacta -->
                            <table class="details-table">
                                <thead>
                                    <tr>
                                        <th width="45%">Artículo</th>
                                        <th width="15%" class="text-center">Cant.</th>
                                        <th width="20%" class="text-right">Precio</th>
                                        <th width="20%" class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ingreso->detalles as $detalle)
                                        @php
                                            $subtotal = $detalle->cantidad * $detalle->precio_compra;
                                            $totalIngreso += $subtotal;
                                            $cantidadArticulos += $detalle->cantidad;
                                            $totalGastado += $subtotal;
                                            $totalArticulos += $detalle->cantidad;
                                        @endphp
                                        <tr>
                                            <td>
                                                @if($detalle->articulo)
                                                    <span class="text-primary text-bold">{{ $detalle->articulo->codigo ?: 'SIN' }}</span>-{{ $detalle->articulo->nombre }}
                                                @else
                                                    Artículo no disponible
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $detalle->cantidad }}{{ $detalle->articulo && $detalle->articulo->unidad ? $detalle->articulo->unidad->abreviatura : '' }}
                                            </td>
                                            <td class="text-right">{{ $config->currency_simbol }}.{{ number_format($detalle->precio_compra, 2, '.', ',') }}</td>
                                            <td class="text-right text-bold">{{ $config->currency_simbol }}.{{ number_format($subtotal, 2, '.', ',') }}</td>
                                        </tr>
                                    @endforeach
                                    <tr style="background-color: #fff3cd;">
                                        <td colspan="3" class="text-right text-bold">Total:</td>
                                        <td class="text-right text-bold">{{ $config->currency_simbol }}.{{ number_format($totalIngreso, 2, '.', ',') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="divider-row"></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #343a40; color: white;">
                    <td colspan="4" class="text-right text-bold">TOTAL GASTADO:</td>
                    <td class="text-right text-bold">{{ $config->currency_simbol }}.{{ number_format($totalGastado, 2, '.', ',') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Resumen compacto -->
        <div class="summary-box">
            <table class="summary-table">
                <tr style="background-color: #007bff; color: white;">
                    <th colspan="2" class="text-center">RESUMEN</th>
                </tr>
                <tr>
                    <td width="60%" class="text-right text-bold">Total ingresos:</td>
                    <td width="40%" class="text-center text-primary text-bold">{{ count($ingresos) }}</td>
                </tr>
                <tr>
                    <td class="text-right text-bold">Total artículos:</td>
                    <td class="text-center text-info text-bold">{{ number_format($totalArticulos, 0) }} unid.</td>
                </tr>
                <tr>
                    <td class="text-right text-bold">Total gastado:</td>
                    <td class="text-center text-danger text-bold">{{ $config->currency_simbol }}.{{ number_format($totalGastado, 2, '.', ',') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="footer">
        <p>Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }} | Página 1</p>
    </div>
</body>
</html>
