<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Artículos</title>
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

        /* Estilos específicos para artículos */
        .compact-row td {
            padding: 2px;
        }
        .stock-badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 8px;
        }
        .disponible { background-color: #d4edda; color: #155724; }
        .bajo { background-color: #fff3cd; color: #856404; }
        .agotado { background-color: #f8d7da; color: #721c24; }
        .na { background-color: #e2e3e5; color: #383d41; }
        .tipo-articulo { color: #007bff; }
        .tipo-servicio { color: #28a745; }
        .ganancia-positiva { color: #28a745; }
        .ganancia-negativa { color: #dc3545; }
    </style>
</head>
<body>
    <div class="content">
        <!-- Logo integrado en el flujo normal del documento -->
        <div class="logo-container">
            @if(isset($imagen) && $imagen)
                <img src="{{ $imagen }}" alt="Logo de la empresa" class="company-logo">
            @elseif(isset($config) && $config->logo)
                <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo de la empresa" class="company-logo">
            @endif
        </div>

        <div class="header">
            <h1>LISTADO DE ARTÍCULOS</h1>
            <p>{{ isset($config) ? $config->nombre_negocio : 'Jireh Automotriz' }} | {{ isset($config) ? $config->telefono : '' }}</p>
        </div>

        <!-- Filtros aplicados -->
        <div class="filters-info">
            <strong>Filtros:</strong>
            @if(isset($filtros['articulo']) && $filtros['articulo']) | <strong>Artículo:</strong> {{ $filtros['articulo'] }} @endif
            @if(isset($filtros['categoria']) && $filtros['categoria']) | <strong>Categoría:</strong> {{ $filtros['categoria'] }} @endif
            @if(isset($filtros['tipo']) && $filtros['tipo']) | <strong>Tipo:</strong> {{ $filtros['tipo'] }} @endif
            @if(isset($filtros['stock']) && $filtros['stock']) | <strong>Stock:</strong> {{ $filtros['stock'] }} @endif
            @if(!isset($filtros) || (count(array_filter($filtros)) == 0)) Sin filtros aplicados @endif
            <strong class="ml-3">Fecha:</strong> {{ date('d/m/Y') }}
        </div>

        <!-- Resumen estadístico -->
        <div class="summary-section">
            <table style="border: none; margin: 0;">
                <tr>
                    <td width="25%" class="text-center">
                        <div class="text-primary text-bold">Total Artículos</div>
                        <div>{{ count($articulos) }}</div>
                    </td>
                    <td width="25%" class="text-center">
                        <div class="text-success text-bold">Disponibles</div>
                        <div>{{ $articulos->where('stock', '>', 0)->count() }}</div>
                    </td>
                    <td width="25%" class="text-center">
                        <div class="text-warning text-bold">Stock Bajo</div>
                        <div>{{ $articulos->where('stock', '>', 0)->where('stock', '<=', 'stock_minimo')->count() }}</div>
                    </td>
                    <td width="25%" class="text-center">
                        <div class="text-danger text-bold">Agotados</div>
                        <div>{{ $articulos->where('stock', '<=', 0)->count() }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tabla de artículos -->
        <div class="section-title">LISTADO DE ARTÍCULOS</div>
        <table>
            <thead>
                <tr>
                    <th width="8%">Código</th>
                    <th width="22%">Artículo</th>
                    <th width="12%">Categoría</th>
                    <th width="8%" class="text-center">Stock</th>
                    <th width="10%">Unidad</th>
                    <th width="12%" class="text-right">Precio Compra</th>
                    <th width="12%" class="text-right">Precio Venta</th>
                    <th width="8%" class="text-right">Margen</th>
                    <th width="8%">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($articulos as $articulo)
                    @php
                        $margen = $articulo->precio_compra > 0 ?
                            (($articulo->precio_venta - $articulo->precio_compra) / $articulo->precio_compra) * 100 : 0;
                    @endphp
                    <tr class="compact-row">
                        <td><span class="text-primary text-bold">{{ $articulo->codigo }}</span></td>
                        <td>
                            {{ $articulo->nombre }}
                            <br>
                            <small class="{{ $articulo->tipo == 'servicio' ? 'tipo-servicio' : 'tipo-articulo' }}">
                                {{ ucfirst($articulo->tipo) }}
                            </small>
                        </td>
                        <td>{{ $articulo->categoria->nombre }}</td>
                        <td class="text-center">
                            {{ number_format($articulo->stock, 2) }}
                        </td>
                        <td>{{ $articulo->unidad->nombre }}</td>
                        <td class="text-right">{{ isset($config) ? $config->currency_simbol : 'Q' }}.{{ number_format($articulo->precio_compra, 2, '.', ',') }}</td>
                        <td class="text-right">{{ isset($config) ? $config->currency_simbol : 'Q' }}.{{ number_format($articulo->precio_venta, 2, '.', ',') }}</td>
                        <td class="text-right">
                            <span class="{{ $margen > 0 ? 'ganancia-positiva' : 'ganancia-negativa' }}">
                                {{ number_format($margen, 0) }}%
                            </span>
                        </td>
                        <td class="text-center">
                            @if($articulo->tipo == 'servicio')
                                <span class="stock-badge na">N/A</span>
                            @elseif($articulo->stock <= 0)
                                <span class="stock-badge agotado">Agotado</span>
                            @elseif($articulo->stock <= $articulo->stock_minimo)
                                <span class="stock-badge bajo">Bajo</span>
                            @else
                                <span class="stock-badge disponible">Disponible</span>
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
