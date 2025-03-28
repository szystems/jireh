<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $articulo->nombre }} - Ficha de {{ $articulo->tipo == 'servicio' ? 'Servicio' : 'Artículo' }}</title>
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
            padding: 8px 12px;
            background-color: rgba(0,123,255,0.1);
        }
        .section-box-title {
            margin: 0;
            font-size: 14px;
            color: #007bff;
            font-weight: bold;
        }
        .section-box-body {
            padding: 10px 12px;
        }

        /* Estilos específicos para artículos */
        .tipo-badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 9px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .articulo {
            background-color: #007bff;
            color: #fff;
        }
        .servicio {
            background-color: #28a745;
            color: #fff;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            flex: 0 0 30%;
            font-weight: bold;
            color: #6c757d;
        }
        .info-value {
            flex: 0 0 70%;
        }
        .price-card {
            display: inline-block;
            width: 45%;
            padding: 10px;
            margin-right: 10px;
            margin-bottom: 10px;
            text-align: center;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .price-card.compra {
            background-color: #ffeeee;
        }
        .price-card.venta {
            background-color: #eeffee;
        }
        .progress-container {
            margin-top: 10px;
            width: 100%;
            background-color: #f1f1f1;
            border-radius: 5px;
            overflow: hidden;
        }
        .progress-bar {
            height: 15px;
            text-align: center;
            font-size: 8px;
            line-height: 15px;
            color: white;
        }
        .progress-success { background-color: #28a745; }
        .progress-warning { background-color: #ffc107; }
        .progress-danger { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="content">
        <div class="logo-container">
            @if ($config->logo)
                <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo de la empresa" class="company-logo">
            @endif
        </div>

        <div class="header">
            <h1>FICHA DE {{ $articulo->tipo == 'servicio' ? 'SERVICIO' : 'ARTÍCULO' }}</h1>
            <p>{{ $config->nombre_negocio }} | {{ $config->telefono }}</p>
            <div>
                <span class="tipo-badge {{ $articulo->tipo == 'servicio' ? 'servicio' : 'articulo' }}">
                    {{ ucfirst($articulo->tipo) }}
                </span>
                @if($articulo->codigo)
                    <span style="margin-left: 10px;">Código: <strong>{{ $articulo->codigo }}</strong></span>
                @endif
            </div>
        </div>

        <div class="section-box">
            <div class="section-box-header">
                <h3 class="section-box-title">INFORMACIÓN BÁSICA</h3>
            </div>
            <div class="section-box-body">
                <div class="info-row">
                    <div class="info-label">Nombre:</div>
                    <div class="info-value text-bold">{{ $articulo->nombre }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Categoría:</div>
                    <div class="info-value">{{ $articulo->categoria->nombre }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Unidad de Medida:</div>
                    <div class="info-value">{{ $articulo->unidad->nombre }} ({{ $articulo->unidad->abreviatura }})</div>
                </div>
                @if($articulo->descripcion)
                <div class="info-row">
                    <div class="info-label">Descripción:</div>
                    <div class="info-value">{{ $articulo->descripcion }}</div>
                </div>
                @endif
                <div class="info-row">
                    <div class="info-label">Fecha de Registro:</div>
                    <div class="info-value">{{ $articulo->created_at->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>
        @if (Auth::user()->role_as != 1)
            <div class="section-title">INFORMACIÓN DE PRECIOS</div>
            <div class="section-box-body" style="padding: 10px;">
                <div style="display: flex; flex-wrap: wrap;">
                    @if (Auth::user()->role_as != 1)
                    <div class="price-card compra">
                        <div style="font-size: 10px; color: #7f8c8d;">Precio de Compra</div>
                        <div style="font-size: 14px; font-weight: bold; color: #e74c3c;">
                            {{ $config->currency_simbol }}.{{ number_format($articulo->precio_compra, 2, '.', ',') }}
                        </div>
                    </div>
                    @endif
                    <div class="price-card venta">
                        <div style="font-size: 10px; color: #7f8c8d;">Precio de Venta</div>
                        <div style="font-size: 14px; font-weight: bold; color: #27ae60;">
                            {{ $config->currency_simbol }}.{{ number_format($articulo->precio_venta, 2, '.', ',') }}
                        </div>
                    </div>
                </div>

                <table>
                    <tbody>
                        <tr>
                            <td width="40%">Precio de Venta</td>
                            <td class="text-right text-success">{{ $config->currency_simbol }}.{{ number_format($articulo->precio_venta, 2, '.', ',') }}</td>
                        </tr>
                        <tr>
                            <td>Precio de Compra</td>
                            <td class="text-right text-danger">- {{ $config->currency_simbol }}.{{ number_format($articulo->precio_compra, 2, '.', ',') }}</td>
                        </tr>
                        <tr>
                            <td>Comisión Vendedor ({{ number_format($comisionVendedor, 2) }}%)</td>
                            <td class="text-right text-danger">- {{ $config->currency_simbol }}.{{ number_format($valorComisionVendedor, 2, '.', ',') }}</td>
                        </tr>
                        <tr>
                            <td>Comisión Trabajador ({{ number_format($comisionTrabajador, 2) }}%)</td>
                            <td class="text-right text-danger">- {{ $config->currency_simbol }}.{{ number_format($valorComisionTrabajador, 2, '.', ',') }}</td>
                        </tr>
                        <tr>
                            <td>Impuesto ({{ number_format($impuesto, 2) }}%)</td>
                            <td class="text-right text-danger">- {{ $config->currency_simbol }}.{{ number_format($valorImpuesto, 2, '.', ',') }}</td>
                        </tr>
                        <tr style="font-weight: bold; background-color: #f9f9f9;">
                            <td>Ganancia Real</td>
                            <td class="text-right {{ $gananciaReal > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $config->currency_simbol }}.{{ number_format($gananciaReal, 2, '.', ',') }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                        <span>Margen Real:</span>
                        <span style="font-weight: bold;" class="{{ $margen < 10 ? 'text-danger' : ($margen < 20 ? 'text-warning' : 'text-success') }}">
                            {{ number_format($margen, 2) }}%
                        </span>
                    </div>
                    <div class="progress-container">
                        <div class="progress-bar {{ $margen < 10 ? 'progress-danger' : ($margen < 20 ? 'progress-warning' : 'progress-success') }}"
                            style="width: {{ min($margen, 100) }}%">
                            {{ number_format($margen, 0) }}%
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="section-title">INFORMACIÓN DE INVENTARIO</div>
        <div class="section-box-body" style="padding: 10px;">
            <div style="text-align: center;">
                <div style="font-size: 16px; font-weight: bold;" class="{{ $articulo->stock <= 0 ? 'text-danger' : ($articulo->stock <= $articulo->stock_minimo ? 'text-warning' : 'text-success') }}">
                    {{ number_format($articulo->stock, 2) }} <span style="font-size: 10px; color: #7f8c8d;">{{ $articulo->unidad->abreviatura }}</span>
                </div>
                <div style="margin-top: 5px;">
                    @if($articulo->stock <= 0)
                        <span style="display: inline-block; padding: 3px 6px; border-radius: 3px; font-size: 9px; background-color: #f8d7da; color: #721c24;">Sin Stock</span>
                    @elseif($articulo->stock <= $articulo->stock_minimo)
                        <span style="display: inline-block; padding: 3px 6px; border-radius: 3px; font-size: 9px; background-color: #fff3cd; color: #856404;">Stock Bajo</span>
                    @else
                        <span style="display: inline-block; padding: 3px 6px; border-radius: 3px; font-size: 9px; background-color: #d4edda; color: #155724;">Stock Disponible</span>
                    @endif
                </div>
            </div>

            <div style="display: flex; margin-top: 15px;">
                <div style="flex: 1; text-align: center; padding: 10px; background-color: #f5f5f5; margin-right: 10px; border-radius: 5px;">
                    <div style="font-size: 9px; color: #7f8c8d;">Stock Mínimo</div>
                    <div style="font-weight: bold; font-size: 12px;">{{ number_format($articulo->stock_minimo, 2) }} {{ $articulo->unidad->abreviatura }}</div>
                </div>
                <div style="flex: 1; text-align: center; padding: 10px; background-color: #f5f5f5; border-radius: 5px;">
                    <div style="font-size: 9px; color: #7f8c8d;">Diferencia</div>
                    @php $diferencia = $articulo->stock - $articulo->stock_minimo; @endphp
                    <div style="font-weight: bold; font-size: 12px;" class="{{ $diferencia < 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($diferencia, 2) }} {{ $articulo->unidad->abreviatura }}
                    </div>
                </div>
            </div>

            @php
                $porcentaje = $articulo->stock_minimo > 0 ?
                    min(($articulo->stock / $articulo->stock_minimo) * 100, 200) : 100;
            @endphp
            <div class="progress-container">
                <div class="progress-bar {{ $porcentaje <= 0 ? 'progress-danger' : ($porcentaje < 100 ? 'progress-warning' : 'progress-success') }}"
                     style="width: {{ $porcentaje }}%">
                    {{ number_format($porcentaje, 0) }}%
                </div>
            </div>
        </div>

        @if($articulo->tipo == 'servicio' && count($articulo->articulos) > 0)
        <div class="section-title">COMPONENTES DEL SERVICIO</div>
        <table>
            <thead>
                <tr>
                    <th>Artículo</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-right">Precio Unitario</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($articulo->articulos as $articuloServicio)
                    @php
                        $costo = $articuloServicio->precio_compra * $articuloServicio->pivot->cantidad;
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $articuloServicio->nombre }}</strong>
                            @if($articuloServicio->codigo)
                                <br><small>Código: {{ $articuloServicio->codigo }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ number_format($articuloServicio->pivot->cantidad, 2) }} {{ $articuloServicio->unidad->abreviatura }}</td>
                        <td class="text-right">{{ $config->currency_simbol }}.{{ number_format($articuloServicio->precio_compra, 2, '.', ',') }}</td>
                        <td class="text-right">{{ $config->currency_simbol }}.{{ number_format($costo, 2, '.', ',') }}</td>
                    </tr>
                @endforeach
            </tbody>
            @if (Auth::user()->role_as != 1)
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Total Costo:</th>
                        <th class="text-right">{{ $config->currency_simbol }}.{{ number_format($totalCosto, 2, '.', ',') }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">Precio de Venta:</th>
                        <th class="text-right text-success">{{ $config->currency_simbol }}.{{ number_format($articulo->precio_venta, 2, '.', ',') }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">Comisiones e Impuestos:</th>
                        <th class="text-right text-danger">- {{ $config->currency_simbol }}.{{ number_format($valorComisionVendedor + $valorComisionTrabajador + $valorImpuesto, 2, '.', ',') }}</th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">Ganancia Real:</th>
                        <th class="text-right {{ $gananciaReal > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $config->currency_simbol }}.{{ number_format($gananciaReal, 2, '.', ',') }}
                            @if(isset($totalCosto) && $totalCosto > 0)
                                ({{ number_format(($gananciaReal / $totalCosto) * 100, 2) }}%)
                            @endif
                        </th>
                    </tr>
                </tfoot>
            @endif
        </table>
        @endif

        <div class="footer">
            <p>Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
            <p>{{ $config->nombre_negocio }} | {{ $config->telefono }}</p>
        </div>
    </div>
</body>
</html>
