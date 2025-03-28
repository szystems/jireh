<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Artículos Vendidos</title>
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

        .compact-cell {
            line-height: 1.1;
            padding: 2px !important;
        }

        .moneda {
            font-family: monospace;
        }

        .section-box {
            border: 1px solid #eee;
            border-radius: 5px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            padding: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .section-box-header {
            border-bottom: 1px solid #eee;
            padding: 5px 8px;
            background-color: rgba(0,123,255,0.1);
        }
        .section-box-title {
            margin: 0;
            font-size: 12px;
            color: #007bff;
            font-weight: bold;
        }
        .section-box-body {
            padding: 5px 8px;
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
                <h1>REPORTE DE ARTÍCULOS VENDIDOS</h1>
            </div>
            <div class="info-container">
                <p>{{ $config->nombre_negocio }}<br>{{ $config->telefono }}</p>
            </div>
        </div>

        <!-- Filtros aplicados -->
        <div class="filters-info">
            <strong>Filtros:</strong>
            {{ \Carbon\Carbon::parse($filtros['fecha_desde'])->format('d/m/Y') }} al
            {{ \Carbon\Carbon::parse($filtros['fecha_hasta'])->format('d/m/Y') }}
            @if($filtros['codigo']) | <strong>Código:</strong> {{ $filtros['codigo'] }} @endif
            @if($filtros['articulo']) | <strong>Artículo:</strong> {{ $filtros['articulo'] }} @endif
            @if($filtros['categoria']) | <strong>Categoría:</strong> {{ $filtros['categoria'] }} @endif
            @if($filtros['trabajador']) | <strong>Trabajador:</strong> {{ $filtros['trabajador'] }} @endif
            @if($filtros['usuario']) | <strong>Usuario:</strong> {{ $filtros['usuario'] }} @endif
        </div>

        <!-- Resumen estadístico -->
        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <td width="25%" class="text-center">
                        <div class="text-primary text-bold">Total Artículos</div>
                        <div>{{ number_format($totales['totalArticulosVendidos'], 0, '.', ',') }} unid.</div>
                    </td>
                    <td width="25%" class="text-center">
                        <div class="text-success text-bold">Total Ventas</div>
                        <div>{{ $config->currency_simbol }}.{{ number_format($totales['totalVentas'], 2, '.', ',') }}</div>
                    </td>
                    <td width="25%" class="text-center">
                        <div class="text-danger text-bold">Total Costos</div>
                        <div>{{ $config->currency_simbol }}.{{ number_format($totales['totalCostos'], 2, '.', ',') }}</div>
                    </td>
                    <td width="25%" class="text-center">
                        <div class="text-info text-bold">Ganancia Neta</div>
                        <div>{{ $config->currency_simbol }}.{{ number_format($totales['totalVentas'] - $totales['totalCostos'] - $totales['totalComisionesVendedor'] - $totales['totalComisionesTrabajador'] - ($totales['totalImpuestos'] ?? 0), 2, '.', ',') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        @if(count($detallesVenta) > 0)
            @php
                // Inicializar totalImpuestos si no existe y calcular el total acumulado de impuestos
                if (!isset($totales['totalImpuestos'])) {
                    $totales['totalImpuestos'] = 0;
                    // Calcular los impuestos totales correctamente sumando los impuestos individuales
                    foreach($detallesVenta as $detalle) {
                        $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                        $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

                        // Aplicar descuento si existe
                        $montoDescuento = 0;
                        if($detalle->descuento_id && $detalle->descuento) {
                            $montoDescuento = $subtotalSinDescuento * ($detalle->descuento->porcentaje_descuento / 100);
                        }

                        $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;

                        // Calcular impuesto usando el porcentaje específico de este detalle
                        $impuestoDetalle = $subtotalConDescuento * ($detalle->porcentaje_impuestos ?? 0) / 100;
                        $totales['totalImpuestos'] += $impuestoDetalle;
                    }
                }
            @endphp

            <div class="section-title">DETALLE DE ARTÍCULOS VENDIDOS</div>
            <table>
                <thead>
                    <tr>
                        <th width="8%">Fecha</th>
                        <th width="8%">Factura</th>
                        <th width="20%">Artículo</th>
                        <th width="8%">Categoría</th>
                        <th width="5%" class="text-center">Cant</th>
                        <th width="7%" class="text-right">P.Venta</th>
                        <th width="7%" class="text-right">P.Costo</th>
                        <th width="7%" class="text-right">Desc</th>
                        <th width="7%" class="text-right">Imp.</th>
                        <th width="7%" class="text-right">Com.Trab</th>
                        <th width="7%" class="text-right">Com.Vend</th>
                        <th width="9%" class="text-right">Ganancia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($detallesVenta as $detalle)
                        @php
                            // Calcular precio unitario y subtotales
                            $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

                            // Calcular monto de descuento
                            $montoDescuento = 0;
                            if($detalle->descuento_id && $detalle->descuento) {
                                $montoDescuento = $subtotalSinDescuento * ($detalle->descuento->porcentaje_descuento / 100);
                            }

                            // Calcular subtotal con descuento
                            $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;

                            // Calcular impuestos
                            $impuestoDetalle = $subtotalConDescuento * ($detalle->porcentaje_impuestos ?? 0) / 100;

                            // Calcular costo total
                            $costoTotal = $detalle->precio_costo * $detalle->cantidad;

                            // Calcular comisiones
                            $comisionTrabajador = 0;
                            if ($detalle->tipo_comision_trabajador_id) {
                                $tipoComision = \App\Models\TipoComision::find($detalle->tipo_comision_trabajador_id);
                                if ($tipoComision) {
                                    $comisionTrabajador = $subtotalConDescuento * ($tipoComision->porcentaje / 100);
                                }
                            }

                            $comisionVendedor = 0;
                            if ($detalle->tipo_comision_usuario_id) {
                                $tipoComision = \App\Models\TipoComision::find($detalle->tipo_comision_usuario_id);
                                if ($tipoComision) {
                                    $comisionVendedor = $subtotalConDescuento * ($tipoComision->porcentaje / 100);
                                }
                            }

                            // Calcular ganancia neta
                            $ganancia = $subtotalConDescuento - $costoTotal - $comisionTrabajador - $comisionVendedor - $impuestoDetalle;
                        @endphp
                        <tr class="compact-cell">
                            <td>{{ \Carbon\Carbon::parse($detalle->venta->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $detalle->venta->numero_factura ?? 'N/A' }}</td>
                            <td>
                                @if($detalle->articulo)
                                    <span class="text-primary text-bold">{{ $detalle->articulo->codigo }}</span> -
                                    {{ $detalle->articulo->nombre }}
                                @else
                                    Artículo no disponible
                                @endif
                            </td>
                            <td>
                                @if($detalle->articulo && $detalle->articulo->categoria)
                                    {{ $detalle->articulo->categoria->nombre }}
                                @else
                                    --
                                @endif
                            </td>
                            <td class="text-center">
                                {{ $detalle->cantidad }}
                                @if($detalle->articulo && $detalle->articulo->unidad)
                                    <span class="text-muted">{{ $detalle->articulo->unidad->abreviatura }}</span>
                                @endif
                            </td>
                            <td class="text-right moneda">{{ $config->currency_simbol }}.{{ number_format($precioUnitario, 2, '.', ',') }}</td>
                            <td class="text-right moneda">{{ $config->currency_simbol }}.{{ number_format($detalle->precio_costo, 2, '.', ',') }}</td>
                            <td class="text-right">
                                @if($montoDescuento > 0)
                                    <span class="text-danger moneda">{{ $config->currency_simbol }}.{{ number_format($montoDescuento, 2, '.', ',') }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right">
                                @if($impuestoDetalle > 0)
                                    <span class="text-warning moneda">{{ $config->currency_simbol }}.{{ number_format($impuestoDetalle, 2, '.', ',') }}</span>
                                    <small>({{ $detalle->porcentaje_impuestos ?? 0 }}%)</small>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right">
                                @if($comisionTrabajador > 0)
                                    <span class="text-info moneda">{{ $config->currency_simbol }}.{{ number_format($comisionTrabajador, 2, '.', ',') }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right">
                                @if($comisionVendedor > 0)
                                    <span class="text-primary moneda">{{ $config->currency_simbol }}.{{ number_format($comisionVendedor, 2, '.', ',') }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-right text-bold">
                                <span class="{{ $ganancia > 0 ? 'text-success' : 'text-danger' }} moneda">
                                    {{ $config->currency_simbol }}.{{ number_format($ganancia, 2, '.', ',') }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background-color: #343a40; color: white;">
                        <td colspan="4" class="text-right text-bold">TOTALES:</td>
                        <td class="text-center text-bold">{{ number_format($totales['totalArticulosVendidos'], 0, '.', ',') }}</td>
                        <td class="text-right" colspan="2">
                            <span class="text-bold">{{ $config->currency_simbol }}.{{ number_format($totales['totalVentas'], 2, '.', ',') }}</span>
                        </td>
                        <td class="text-right text-bold">{{ $config->currency_simbol }}.{{ number_format($totales['totalDescuentos'], 2, '.', ',') }}</td>
                        <td class="text-right text-bold">{{ $config->currency_simbol }}.{{ number_format($totales['totalImpuestos'], 2, '.', ',') }}</td>
                        <td class="text-right text-bold">{{ $config->currency_simbol }}.{{ number_format($totales['totalComisionesTrabajador'], 2, '.', ',') }}</td>
                        <td class="text-right text-bold">{{ $config->currency_simbol }}.{{ number_format($totales['totalComisionesVendedor'], 2, '.', ',') }}</td>
                        <td class="text-right text-bold">{{ $config->currency_simbol }}.{{ number_format($totales['totalVentas'] - $totales['totalCostos'] - $totales['totalComisionesVendedor'] - $totales['totalComisionesTrabajador'] - $totales['totalImpuestos'], 2, '.', ',') }}</td>
                    </tr>
                </tfoot>
            </table>
        @else
            <div class="section-box">
                <div class="section-box-body" style="text-align: center;">
                    <p>No se encontraron artículos vendidos con los filtros especificados.</p>
                </div>
            </div>
        @endif

        <!-- Sección de Resumen de Indicadores -->
        @if(count($detallesVenta) > 0)
            <div class="section-title">ANÁLISIS DE RENDIMIENTO</div>
            <div class="section-box">
                <div class="section-box-header">
                    <h3 class="section-box-title">INDICADORES FINANCIEROS</h3>
                </div>
                <div class="section-box-body">
                    <table class="summary-table">
                        <tr>
                            <td width="30%" class="text-right text-bold">Margen bruto promedio:</td>
                            <td width="20%" class="text-right text-success">
                                @php
                                    $margenBruto = 0;
                                    if ($totales['totalVentas'] > 0) {
                                        $margenBruto = (($totales['totalVentas'] - $totales['totalCostos']) / $totales['totalVentas']) * 100;
                                    }
                                @endphp
                                {{ number_format($margenBruto, 2) }}%
                            </td>
                            <td width="30%" class="text-right text-bold">Rentabilidad neta:</td>
                            <td width="20%" class="text-right text-info">
                                @php
                                    $rentabilidadNeta = 0;
                                    if ($totales['totalVentas'] > 0) {
                                        $gananciaNeta = $totales['totalVentas'] - $totales['totalCostos'] - $totales['totalComisionesVendedor'] - $totales['totalComisionesTrabajador'] - $totales['totalImpuestos'];
                                        $rentabilidadNeta = ($gananciaNeta / $totales['totalVentas']) * 100;
                                    }
                                @endphp
                                {{ number_format($rentabilidadNeta, 2) }}%
                            </td>
                        </tr>
                        <tr>
                            <td class="text-right text-bold">Relación descuentos/ventas:</td>
                            <td class="text-right">
                                @php
                                    $relacionDescuentos = 0;
                                    if ($totales['totalVentas'] > 0) {
                                        $relacionDescuentos = ($totales['totalDescuentos'] / ($totales['totalVentas'] + $totales['totalDescuentos'])) * 100;
                                    }
                                @endphp
                                {{ number_format($relacionDescuentos, 2) }}%
                            </td>
                            <td class="text-right text-bold">Relación impuestos/ventas:</td>
                            <td class="text-right">
                                @php
                                    $relacionImpuestos = 0;
                                    if ($totales['totalVentas'] > 0 && $totales['totalImpuestos'] > 0) {
                                        $relacionImpuestos = ($totales['totalImpuestos'] / $totales['totalVentas']) * 100;
                                    }
                                @endphp
                                {{ number_format($relacionImpuestos, 2) }}%
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div class="footer">
        <p>Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p>{{ $config->nombre_negocio }}</p>
    </div>
</body>
</html>
