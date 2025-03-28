<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Inventario</title>
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

        /* Estilos específicos para inventario */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 8px;
            border-radius: 3px;
        }
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .moneda {
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="logo-container">
            @if(isset($config) && $config->logo)
                <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo de la empresa" class="company-logo">
            @endif
        </div>

        <div class="header">
            <h1>REPORTE DE INVENTARIO</h1>
            <p>{{ isset($config) ? $config->nombre_negocio : 'Jireh Automotriz' }} | {{ isset($config) ? $config->telefono : '' }}</p>
        </div>

        <!-- Filtros aplicados -->
        <div class="filters-info">
            <strong>Filtros aplicados:</strong>
            @if(request('articulo'))
                | <strong>Artículo:</strong> {{ is_numeric(request('articulo')) ? App\Models\Articulo::find(request('articulo'))->nombre : request('articulo') }}
            @endif
            @if(request('categoria'))
                @php
                    $categoria = \App\Models\Categoria::find(request('categoria'));
                @endphp
                | <strong>Categoría:</strong> {{ $categoria ? $categoria->nombre : 'Todas' }}
            @endif
            @if(request('stock'))
                | <strong>Stock:</strong> {{ request('stock') == 'con_stock' ? 'Con Stock' : (request('stock') == 'sin_stock' ? 'Sin Stock' : 'Todos') }}
            @endif
            @if(request('stock_minimo'))
                | <strong>Stock mínimo:</strong> {{ request('stock_minimo') }}
            @endif
            @if(request('tipo'))
                | <strong>Tipo:</strong> {{ request('tipo') }}
            @endif
            @if(request('ordenar'))
                | <strong>Ordenado por:</strong> {{ request('ordenar') }}
            @endif
            @if(!request('articulo') && !request('categoria') && !request('stock') && !request('stock_minimo') && !request('tipo') && !request('ordenar'))
                Sin filtros aplicados - Mostrando todo el inventario
            @endif
            <strong class="ml-3">Fecha:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}
        </div>

        <!-- Resumen estadístico -->
        <div class="summary-section">
            @php
                $totalValorInventario = 0;
                $totalItems = count($articulos);
                $agotados = 0;
                $bajoStock = 0;
                $disponibles = 0;

                foreach($articulos as $articulo) {
                    $valorTotal = $articulo->precio_compra * $articulo->stock;
                    $totalValorInventario += $valorTotal;

                    if ($articulo->stock <= 0) {
                        $agotados++;
                    } elseif ($articulo->stock <= $articulo->stock_minimo) {
                        $bajoStock++;
                    } else {
                        $disponibles++;
                    }
                }
            @endphp

            <table style="border: none; margin: 0;">
                <tr>
                    <td width="25%" class="text-center">
                        <div class="text-primary text-bold">Total Artículos</div>
                        <div>{{ $totalItems }}</div>
                    </td>
                    <td width="25%" class="text-center">
                        <div class="text-success text-bold">Disponibles</div>
                        <div>{{ $disponibles }} ({{ $totalItems > 0 ? round(($disponibles / $totalItems) * 100, 1) : 0 }}%)</div>
                    </td>
                    <td width="25%" class="text-center">
                        <div class="text-warning text-bold">Stock Bajo</div>
                        <div>{{ $bajoStock }} ({{ $totalItems > 0 ? round(($bajoStock / $totalItems) * 100, 1) : 0 }}%)</div>
                    </td>
                    <td width="25%" class="text-center">
                        <div class="text-danger text-bold">Agotados</div>
                        <div>{{ $agotados }} ({{ $totalItems > 0 ? round(($agotados / $totalItems) * 100, 1) : 0 }}%)</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tabla de artículos -->
        <div class="section-title">LISTADO DE ARTÍCULOS EN INVENTARIO</div>
        <table>
            <thead>
                <tr>
                    <th width="8%">Código</th>
                    <th width="22%">Artículo</th>
                    <th width="15%">Categoría</th>
                    <th width="7%" class="text-center">Stock</th>
                    <th width="7%" class="text-center">Stock Min.</th>
                    <th width="8%">Unidad</th>
                    <th width="10%" class="text-right">Precio Compra</th>
                    <th width="10%" class="text-right">Precio Venta</th>
                    <th width="10%" class="text-right">Valor Total</th>
                    <th width="8%">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($articulos as $articulo)
                    @php
                        $valorTotal = $articulo->precio_compra * $articulo->stock;
                    @endphp
                    <tr>
                        <td><span class="text-primary text-bold">{{ $articulo->codigo }}</span></td>
                        <td>
                            <strong>{{ $articulo->nombre }}</strong>
                            @if($articulo->tipo)
                                <br><small class="{{ $articulo->tipo == 'servicio' ? 'text-success' : 'text-primary' }}">
                                    {{ ucfirst($articulo->tipo) }}
                                </small>
                            @endif
                        </td>
                        <td>{{ $articulo->categoria->nombre }}</td>
                        <td class="text-center">
                            @if ($articulo->stock <= 0)
                                <span class="text-danger text-bold">{{ number_format($articulo->stock, 2) }}</span>
                            @elseif (($articulo->stock > 0) && ($articulo->stock <= $articulo->stock_minimo))
                                <span class="text-warning text-bold">{{ number_format($articulo->stock, 2) }}</span>
                            @else
                                <span class="text-success text-bold">{{ number_format($articulo->stock, 2) }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($articulo->stock_minimo, 2) }}</td>
                        <td class="text-center">{{ $articulo->unidad->abreviatura }}</td>
                        <td class="text-right moneda">{{ isset($config) ? $config->currency_simbol : '$' }}.{{ number_format($articulo->precio_compra, 2, '.', ',') }}</td>
                        <td class="text-right moneda">{{ isset($config) ? $config->currency_simbol : '$' }}.{{ number_format($articulo->precio_venta, 2, '.', ',') }}</td>
                        <td class="text-right moneda">{{ isset($config) ? $config->currency_simbol : '$' }}.{{ number_format($valorTotal, 2, '.', ',') }}</td>
                        <td class="text-center">
                            @if ($articulo->stock <= 0)
                                <span class="badge badge-danger">Agotado</span>
                            @elseif (($articulo->stock > 0) && ($articulo->stock <= $articulo->stock_minimo))
                                <span class="badge badge-warning">Stock Bajo</span>
                            @else
                                <span class="badge badge-success">Disponible</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #343a40; color: white;">
                    <td colspan="8" class="text-right text-bold">VALOR TOTAL DEL INVENTARIO:</td>
                    <td class="text-right text-bold moneda">{{ isset($config) ? $config->currency_simbol : '$' }}.{{ number_format($totalValorInventario, 2, '.', ',') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p>Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
            <p>{{ isset($config) ? $config->nombre_negocio : 'Jireh Automotriz' }} | Página 1</p>
        </div>
    </div>
</body>
</html>
