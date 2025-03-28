{{-- filepath: c:\Users\szott\Dropbox\Desarrollo\jireh\resources\views\admin\venta\index.blade.php --}}
@extends('layouts.admin')

@section('content')
    <!-- Incluir Chart.js para los gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="page-title">
                    <h5>Ventas</h5>
                </div>
            </div>
        </div>
        <div class="content-wrapper">
            <!-- Estadísticas en cards - Siempre visibles independientemente de la pestaña -->
            <div class="row mb-4">
                @php
                    // Calcular estadísticas
                    $totalVentas = $ventas->where('estado', true)->count();
                    $totalMonto = $ventas->where('estado', true)->sum(function($venta) {
                        return $venta->detalleVentas->sum('sub_total');
                    });

                    $totalDescuentos = $ventas->where('estado', true)->sum(function($venta) {
                        $descuento = 0;
                        foreach ($venta->detalleVentas as $detalle) {
                            if ($detalle->descuento_id && $detalle->descuento) {
                                $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                                $subtotal = $precioUnitario * $detalle->cantidad;
                                $descuento += $subtotal * ($detalle->descuento->porcentaje_descuento / 100);
                            }
                        }
                        return $descuento;
                    });

                    $totalArticulos = $ventas->where('estado', true)->sum(function($venta) {
                        return $venta->detalleVentas->sum('cantidad');
                    });

                    $totalPagado = $ventas->where('estado', true)->sum(function($venta) {
                        return $venta->pagos ? $venta->pagos->sum('monto') : 0;
                    });

                    $pendientePago = $totalMonto - $totalPagado;

                    $ventasPorTipo = $ventas->where('estado', true)->groupBy('tipo_venta')
                        ->map(function($ventas, $tipo) {
                            return [
                                'tipo' => $tipo ?: 'Sin especificar',
                                'cantidad' => $ventas->count(),
                                'monto' => $ventas->sum(function($v) {
                                    return $v->detalleVentas->sum('sub_total');
                                })
                            ];
                        })->values();
                @endphp

                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-primary">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <div class="fs-6 fw-bold">Ventas</div>
                            <div class="fs-4 text-primary">{{ number_format($totalVentas, 0, '.', ',') }}</div>
                            <div class="small text-muted">totales</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-success">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <div class="fs-6 fw-bold">Total Ventas</div>
                            <div class="fs-4 text-success">{{ $config->currency_simbol }}.{{ number_format($totalMonto, 2, '.', ',') }}</div>
                            <div class="small text-muted">monto total</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-info">
                                <i class="bi bi-box-fill"></i>
                            </div>
                            <div class="fs-6 fw-bold">Artículos</div>
                            <div class="fs-4 text-info">{{ number_format($totalArticulos, 0, '.', ',') }}</div>
                            <div class="small text-muted">unidades vendidas</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-warning">
                                <i class="bi bi-tag-fill"></i>
                            </div>
                            <div class="fs-6 fw-bold">Descuentos</div>
                            <div class="fs-4 text-warning">{{ $config->currency_simbol }}.{{ number_format($totalDescuentos, 2, '.', ',') }}</div>
                            <div class="small text-muted">aplicados</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="fs-6 fw-bold">Pagado</div>
                            <div class="fs-4 text-success">{{ $config->currency_simbol }}.{{ number_format($totalPagado, 2, '.', ',') }}</div>
                            <div class="small text-muted">total</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-danger">
                                <i class="bi bi-exclamation-circle"></i>
                            </div>
                            <div class="fs-6 fw-bold">Pendiente</div>
                            <div class="fs-4 text-danger">{{ $config->currency_simbol }}.{{ number_format($pendientePago, 2, '.', ',') }}</div>
                            <div class="small text-muted">por cobrar</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navegación de pestañas -->
            <ul class="nav nav-tabs mb-3" id="ventasTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="listado-tab" data-bs-toggle="tab" data-bs-target="#listado-tab-pane" type="button" role="tab" aria-controls="listado-tab-pane" aria-selected="true">
                        <i class="bi bi-list-ul"></i> Listado de Ventas
                    </button>
                </li>
                @if (Auth::user()->role_as != 1)
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="estadisticas-tab" data-bs-toggle="tab" data-bs-target="#estadisticas-tab-pane" type="button" role="tab" aria-controls="estadisticas-tab-pane" aria-selected="false">
                        <i class="bi bi-bar-chart-line"></i> Estadísticas y Gráficos
                    </button>
                </li>
                @endif
            </ul>

            <!-- Contenido de las pestañas -->
            <div class="tab-content" id="ventasTabsContent">
                <!-- Pestaña de Listado -->
                <div class="tab-pane fade show active" id="listado-tab-pane" role="tabpanel" aria-labelledby="listado-tab" tabindex="0">
                    @include('admin.venta.search')

                    <!-- Botón para añadir nueva venta -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Lista de Ventas</h6>
                        <a href="{{ url('add-venta') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Nueva Venta
                        </a>
                    </div>

                    <!-- Tabla de ventas -->
                    <div class="card">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 text-white mb-3">Lista de Ventas</h5>
                            <div>
                                {{-- <a href="{{ route('ventas.export.excel') }}?{{ http_build_query(request()->all()) }}" class="btn btn-success btn-sm">
                                    <i class="bi bi-file-earmark-excel"></i> Excel
                                </a> --}}
                                <a target="blank_" href="{{ route('ventas.export.pdf') }}?{{ http_build_query(request()->all()) }}" class="btn btn-danger btn-sm">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="filters mb-3">
                                <strong>Filtros utilizados:</strong>
                                <span>Fecha Desde: {{ \Carbon\Carbon::parse(request('fecha_desde', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d')))->format('d/m/Y') }}</span>,
                                <span>Fecha Hasta: {{ \Carbon\Carbon::parse(request('fecha_hasta', \Carbon\Carbon::now()->format('Y-m-d')))->format('d/m/Y') }}</span>
                                @if(request('numero_factura'))
                                    , <span>Número de Factura: {{ request('numero_factura') }}</span>
                                @endif
                                @if(request('cliente') && $clientes->find(request('cliente')))
                                    , <span>Cliente: {{ $clientes->find(request('cliente'))->nombre }}</span>
                                @endif
                                @if(request('tipo_venta'))
                                    , <span>Tipo de Venta: {{ request('tipo_venta') }}</span>
                                @endif
                                @if(request('usuario') && $usuarios->find(request('usuario')))
                                    , <span>Usuario: {{ $usuarios->find(request('usuario'))->name }}</span>
                                @endif
                                @if(request('estado') !== null)
                                    , <span>Estado: {{ request('estado') == '1' ? 'Activa' : 'Cancelada' }}</span>
                                @endif
                                @if(request('estado_pago'))
                                    , <span>Estado de Pago: {{ ucfirst(request('estado_pago')) }}</span>
                                @endif
                            </div>
                            <div class="row gx-3">
                                <div class="col-sm-12 col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-3">
                                                <a href="{{ url('add-venta')  }}" class="btn btn-primary"><i class="bi bi-plus-square"></i> Agregar</a>
                                                {{-- <div>
                                                    <a href="{{ route('ventas.export.pdf', request()->query()) }}" class="btn btn-danger"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                                                    <a href="{{ route('ventas.export.excel', request()->query()) }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Excel</a>
                                                </div> --}}
                                            </div>
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Acciones</th>
                                                        <th>Fecha</th>
                                                        <th>Número de Factura</th>
                                                        <th>Cliente / Vehículo</th>
                                                        <th class="text-center">Tipo de Venta</th>
                                                        <th>Usuario</th>
                                                        <th>Detalles</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($ventas as $venta)
                                                        <tr class="{{ $venta->estado ? '' : 'table-secondary text-muted' }}">
                                                            <td>
                                                                <a href="{{ url('show-venta/'.$venta->id)  }}" class="btn btn-info btn-sm"><i class="bi bi-eye-fill"></i></a>
                                                                <a href="{{ url('edit-venta/'.$venta->id)  }}" class="btn btn-warning btn-sm {{ $venta->estado ? '' : 'disabled' }}"><i class="bi bi-pencil-fill "></i></a>
                                                                @if($venta->estado)
                                                                    <a type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $venta->id }}">
                                                                        <i class="bi bi-trash-fill"></i>
                                                                    </a>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <a href="{{ url('show-venta/'.$venta->id)  }}" class="text-primary">
                                                                    {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}
                                                                </a>
                                                                @unless($venta->estado)
                                                                    <span class="badge bg-danger">Cancelada</span>
                                                                @endunless
                                                                <!-- Agregar badge para estado de pago -->
                                                                @if($venta->estado)
                                                                    <span class="badge {{ $venta->estado_pago == 'pagado' ? 'bg-success' : 'bg-warning' }}">
                                                                        {{ ucfirst($venta->estado_pago) }}
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td><a href="{{ url('show-venta/'.$venta->id)  }}">{{ $venta->numero_factura }}</a></td>
                                                            <td>
                                                                <div>
                                                                    <a href="{{ url('show-cliente/'.$venta->cliente_id) }}" class="text-primary">{{ optional($venta->cliente)->nombre }}</a>
                                                                </div>
                                                                @if($venta->vehiculo)
                                                                <div class="text-muted small">
                                                                    <a href="{{ url('show-vehiculo/'.$venta->vehiculo->id) }}"><i class="bi bi-car-front"></i> {{ $venta->vehiculo->marca }} {{ $venta->vehiculo->modelo }} - {{ $venta->vehiculo->placa }}</a>
                                                                </div>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">{{ $venta->tipo_venta }}</td>
                                                            <td >{{ optional($venta->usuario)->name }}</td>
                                                            <td>
                                                                <table class="table table-sm table-bordered table-striped" style="font-size: 12px;">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Artículo</th>
                                                                            <th class="text-center">Cantidad</th>
                                                                            <th>Descuento</th>
                                                                            <th class="text-end">Impuestos</th>
                                                                            @if (Auth::user()->role_as != 1)
                                                                                <th class="text-end">Com. Trab.</th>
                                                                                <th class="text-end">Com. Vend.</th>
                                                                            @endif
                                                                            <th class="text-end">Subtotal</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php
                                                                            $totalImpuestosVenta = 0;
                                                                            $totalComisionTrabajadorVenta = 0;
                                                                            $totalComisionUsuarioVenta = 0;
                                                                        @endphp
                                                                        @foreach($venta->detalleVentas as $detalle)
                                                                            <tr>
                                                                                <td class="text-primary">{{ optional($detalle->articulo)->nombre }}</td>
                                                                                <td class="text-center">
                                                                                    {{ $detalle->cantidad }}
                                                                                    @if($detalle->articulo && $detalle->articulo->unidad)
                                                                                        {{ $detalle->articulo->unidad->abreviatura }}
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    @php
                                                                                        // Calcular precio unitario
                                                                                        $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);

                                                                                        // Calcular subtotal sin descuento (precio unitario × cantidad)
                                                                                        $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

                                                                                        // Calcular monto de descuento si aplica
                                                                                        $montoDescuento = 0;
                                                                                        $porcentajeDescuento = 0;
                                                                                        if($detalle->descuento_id) {
                                                                                            $descuento = \App\Models\Descuento::find($detalle->descuento_id);
                                                                                            if($descuento) {
                                                                                                $porcentajeDescuento = $descuento->porcentaje_descuento;
                                                                                                $montoDescuento = $subtotalSinDescuento * ($porcentajeDescuento / 100);

                                                                                                echo $descuento->nombre . ' (' . $porcentajeDescuento . '%) - ' .
                                                                                                    $config->currency_simbol . '.' . number_format($montoDescuento, 2);
                                                                                            } else {
                                                                                                echo 'Sin descuento';
                                                                                            }
                                                                                        } else {
                                                                                            echo 'Sin descuento';
                                                                                        }

                                                                                        // Calcular subtotal con descuento
                                                                                        $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;

                                                                                        // Calcular impuesto
                                                                                        $impuestoDetalle = $subtotalConDescuento * ($detalle->porcentaje_impuestos ?? 0) / 100;
                                                                                        $totalImpuestosVenta += $impuestoDetalle;

                                                                                        // Calcular comisión del trabajador
                                                                                        $comisionTrabajador = 0;
                                                                                        $porcentajeComisionTrabajador = 0;
                                                                                        if ($detalle->tipo_comision_trabajador_id) {
                                                                                            $tipoComisionTrabajador = \App\Models\TipoComision::find($detalle->tipo_comision_trabajador_id);
                                                                                            if ($tipoComisionTrabajador) {
                                                                                                $porcentajeComisionTrabajador = $tipoComisionTrabajador->porcentaje;
                                                                                                $comisionTrabajador = $subtotalConDescuento * ($porcentajeComisionTrabajador / 100);
                                                                                                $totalComisionTrabajadorVenta += $comisionTrabajador;
                                                                                            }
                                                                                        }

                                                                                        // Calcular comisión del usuario (vendedor)
                                                                                        $comisionUsuario = 0;
                                                                                        $porcentajeComisionUsuario = 0;
                                                                                        if ($detalle->tipo_comision_usuario_id) {
                                                                                            $tipoComisionUsuario = \App\Models\TipoComision::find($detalle->tipo_comision_usuario_id);
                                                                                            if ($tipoComisionUsuario) {
                                                                                                $porcentajeComisionUsuario = $tipoComisionUsuario->porcentaje;
                                                                                                $comisionUsuario = $subtotalConDescuento * ($porcentajeComisionUsuario / 100);
                                                                                                $totalComisionUsuarioVenta += $comisionUsuario;
                                                                                            }
                                                                                        }
                                                                                    @endphp
                                                                                </td>
                                                                                <td class="text-end">
                                                                                    {{ $config->currency_simbol }}.{{ number_format($impuestoDetalle, 2, '.', ',') }}
                                                                                    ({{ number_format($detalle->porcentaje_impuestos ?? 0, 2) }}%)
                                                                                </td>
                                                                                @if (Auth::user()->role_as != 1)
                                                                                    <td class="text-end">
                                                                                        @if ($detalle->tipo_comision_trabajador_id)
                                                                                            {{ $config->currency_simbol }}.{{ number_format($comisionTrabajador, 2, '.', ',') }}
                                                                                            ({{ number_format($porcentajeComisionTrabajador, 2) }}%)
                                                                                        @else
                                                                                            -
                                                                                        @endif
                                                                                    </td>
                                                                                    <td class="text-end">
                                                                                        @if ($detalle->tipo_comision_usuario_id)
                                                                                            {{ $config->currency_simbol }}.{{ number_format($comisionUsuario, 2, '.', ',') }}
                                                                                            ({{ number_format($porcentajeComisionUsuario, 2) }}%)
                                                                                        @else
                                                                                            -
                                                                                        @endif
                                                                                    </td>
                                                                                @endif
                                                                                <td class="text-end">
                                                                                    {{ $config->currency_simbol }}.{{ number_format($subtotalConDescuento, 2, '.', ',') }}
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                        @php
                                                                            // Recalcular totales correctamente
                                                                            $totalSinDescuento = 0;
                                                                            $totalDescuentos = 0;
                                                                            $totalVenta = 0;

                                                                            foreach($venta->detalleVentas as $detalle) {
                                                                                // Calcular precio unitario
                                                                                $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);

                                                                                // Calcular subtotal sin descuento
                                                                                $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;
                                                                                $totalSinDescuento += $subtotalSinDescuento;

                                                                                // Calcular monto de descuento
                                                                                $montoDescuento = 0;
                                                                                if($detalle->descuento_id) {
                                                                                    $descuento = \App\Models\Descuento::find($detalle->descuento_id);
                                                                                    if($descuento) {
                                                                                        $porcentajeDescuento = $descuento->porcentaje_descuento;
                                                                                        $montoDescuento = $subtotalSinDescuento * ($porcentajeDescuento / 100);
                                                                                    }
                                                                                }

                                                                                $totalDescuentos += $montoDescuento;
                                                                                $totalVenta += ($subtotalSinDescuento - $montoDescuento);
                                                                            }
                                                                        @endphp
                                                                        <tr class="table-secondary">
                                                                            <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                                                            <td class="text-danger">
                                                                                <strong>{{ $config->currency_simbol }}.{{ number_format($totalDescuentos, 2, '.', ',') }}</strong>
                                                                            </td>
                                                                            <td class="text-end text-info">
                                                                                <strong>{{ $config->currency_simbol }}.{{ number_format($totalImpuestosVenta, 2, '.', ',') }}</strong>
                                                                            </td>
                                                                            @if (Auth::user()->role_as != 1)
                                                                                <td class="text-end text-success">
                                                                                    <strong>{{ $config->currency_simbol }}.{{ number_format($totalComisionTrabajadorVenta, 2, '.', ',') }}</strong>
                                                                                </td>
                                                                                <td class="text-end text-primary">
                                                                                    <strong>{{ $config->currency_simbol }}.{{ number_format($totalComisionUsuarioVenta, 2, '.', ',') }}</strong>
                                                                                </td>
                                                                            @endif
                                                                            <td class="text-end">
                                                                                <strong>Total: {{ $config->currency_simbol }}.{{ number_format($totalVenta, 2, '.', ',') }}</strong>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        @include('admin.venta.deletemodal')
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <!-- SECCIÓN DE VENTAS -->
                                                    <tr class="table-primary">
                                                        <td colspan="7" class="text-center"><strong>RESUMEN DE VENTAS</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="6" class="text-end"><strong>Subtotal sin descuento:</strong></td>
                                                        <td class="text-end">
                                                            <strong>
                                                                {{ $config->currency_simbol }}.{{
                                                                    number_format(
                                                                        $ventas->where('estado', true)->sum(function($venta) {
                                                                            $subtotal = 0;
                                                                            foreach($venta->detalleVentas as $detalle) {
                                                                                $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                                                                                $subtotal += $precioUnitario * $detalle->cantidad;
                                                                            }
                                                                            return $subtotal;
                                                                        }), 2, '.', ','
                                                                    )
                                                                }}
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="6" class="text-end"><strong>Total descuentos:</strong></td>
                                                        <td class="text-end text-danger">
                                                            <strong>
                                                                {{ $config->currency_simbol }}.{{
                                                                    number_format(
                                                                        $ventas->where('estado', true)->sum(function($venta) {
                                                                            $descuentoTotal = 0;
                                                                            foreach($venta->detalleVentas as $detalle) {
                                                                                $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                                                                                $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

                                                                                $montoDescuento = 0;
                                                                                if($detalle->descuento_id) {
                                                                                    $descuento = \App\Models\Descuento::find($detalle->descuento_id);
                                                                                    if($descuento) {
                                                                                        $porcentajeDescuento = $descuento->porcentaje_descuento;
                                                                                        $montoDescuento = $subtotalSinDescuento * ($porcentajeDescuento / 100);
                                                                                    }
                                                                                }

                                                                                $descuentoTotal += $montoDescuento;
                                                                            }
                                                                            return $descuentoTotal;
                                                                        }), 2, '.', ','
                                                                    )
                                                                }}
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="6" class="text-end"><strong>Total de ventas (después de descuentos):</strong></td>
                                                        <td class="text-end text-primary">
                                                            <strong>
                                                                {{ $config->currency_simbol }}.{{
                                                                    number_format(
                                                                        $ventas->where('estado', true)->sum(function($venta) {
                                                                            $totalVenta = 0;
                                                                            foreach($venta->detalleVentas as $detalle) {
                                                                                $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                                                                                $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

                                                                                $montoDescuento = 0;
                                                                                if($detalle->descuento_id) {
                                                                                    $descuento = \App\Models\Descuento::find($detalle->descuento_id);
                                                                                    if($descuento) {
                                                                                        $porcentajeDescuento = $descuento->porcentaje_descuento;
                                                                                        $montoDescuento = $subtotalSinDescuento * ($porcentajeDescuento / 100);
                                                                                    }
                                                                                }

                                                                                $totalVenta += ($subtotalSinDescuento - $montoDescuento);
                                                                            }
                                                                            return $totalVenta;
                                                                        }), 2, '.', ','
                                                                    )
                                                                }}
                                                            </strong>
                                                        </td>
                                                    </tr>

                                                    <!-- SECCIÓN DE COSTOS Y GASTOS -->
                                                    @if (Auth::user()->role_as != 1)
                                                    <tr class="table-danger">
                                                        <td colspan="7" class="text-center"><strong>COSTOS Y GASTOS</strong></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="6" class="text-end"><strong>Total costo de compra:</strong></td>
                                                        <td class="text-end text-danger">
                                                            <strong>
                                                                {{ $config->currency_simbol }}.{{
                                                                    number_format(
                                                                        $ventas->where('estado', true)->sum(function($venta) {
                                                                            $costosTotal = 0;
                                                                            foreach($venta->detalleVentas as $detalle) {
                                                                                $costosTotal += $detalle->precio_costo * $detalle->cantidad;
                                                                            }
                                                                            return $costosTotal;
                                                                        }), 2, '.', ','
                                                                    )
                                                                }}
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="6" class="text-end"><strong>Total de impuestos:</strong></td>
                                                        <td class="text-end text-info">
                                                            <strong>
                                                                {{ $config->currency_simbol }}.{{
                                                                    number_format(
                                                                        $ventas->where('estado', true)->sum(function($venta) {
                                                                            $impuestoTotal = 0;
                                                                            foreach($venta->detalleVentas as $detalle) {
                                                                                // Calcular precio unitario y subtotal
                                                                                $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                                                                                $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

                                                                                // Calcular descuento
                                                                                $montoDescuento = 0;
                                                                                if($detalle->descuento_id) {
                                                                                    $descuento = \App\Models\Descuento::find($detalle->descuento_id);
                                                                                    if($descuento) {
                                                                                        $porcentajeDescuento = $descuento->porcentaje_descuento;
                                                                                        $montoDescuento = $subtotalSinDescuento * ($porcentajeDescuento / 100);
                                                                                    }
                                                                                }

                                                                                // Calcular subtotal con descuento
                                                                                $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;

                                                                                // Calcular impuesto
                                                                                $impuestoDetalle = $subtotalConDescuento * ($detalle->porcentaje_impuestos ?? 0) / 100;
                                                                                $impuestoTotal += $impuestoDetalle;
                                                                            }
                                                                            return $impuestoTotal;
                                                                        }), 2, '.', ','
                                                                    )
                                                                }}
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="6" class="text-end"><strong>Total comisiones trabajador:</strong></td>
                                                        <td class="text-end text-success">
                                                            <strong>
                                                                {{ $config->currency_simbol }}.{{
                                                                    number_format(
                                                                        $ventas->where('estado', true)->sum(function($venta) {
                                                                            $comisionTrabajadorTotal = 0;
                                                                            foreach($venta->detalleVentas as $detalle) {
                                                                                // Calcular subtotal después de descuento
                                                                                $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                                                                                $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

                                                                                // Aplicar descuento
                                                                                $montoDescuento = 0;
                                                                                if($detalle->descuento_id) {
                                                                                    $descuento = \App\Models\Descuento::find($detalle->descuento_id);
                                                                                    if($descuento) {
                                                                                        $montoDescuento = $subtotalSinDescuento * ($descuento->porcentaje_descuento / 100);
                                                                                    }
                                                                                }
                                                                                $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;

                                                                                // Calcular comisión del trabajador
                                                                                if($detalle->tipo_comision_trabajador_id) {
                                                                                    $tipoComision = \App\Models\TipoComision::find($detalle->tipo_comision_trabajador_id);
                                                                                    if($tipoComision) {
                                                                                        $comisionTrabajadorTotal += $subtotalConDescuento * ($tipoComision->porcentaje / 100);
                                                                                    }
                                                                                }
                                                                            }
                                                                            return $comisionTrabajadorTotal;
                                                                        }), 2, '.', ','
                                                                    )
                                                                }}
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="6" class="text-end"><strong>Total comisiones vendedor:</strong></td>
                                                        <td class="text-end text-primary">
                                                            <strong>
                                                                {{ $config->currency_simbol }}.{{
                                                                    number_format(
                                                                        $ventas->where('estado', true)->sum(function($venta) {
                                                                            $comisionUsuarioTotal = 0;
                                                                            foreach($venta->detalleVentas as $detalle) {
                                                                                // Calcular subtotal después de descuento
                                                                                $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                                                                                $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

                                                                                // Aplicar descuento
                                                                                $montoDescuento = 0;
                                                                                if($detalle->descuento_id) {
                                                                                    $descuento = \App\Models\Descuento::find($detalle->descuento_id);
                                                                                    if($descuento) {
                                                                                        $montoDescuento = $subtotalSinDescuento * ($descuento->porcentaje_descuento / 100);
                                                                                    }
                                                                                }
                                                                                $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;

                                                                                // Calcular comisión del usuario (vendedor)
                                                                                if($detalle->tipo_comision_usuario_id) {
                                                                                    $tipoComision = \App\Models\TipoComision::find($detalle->tipo_comision_usuario_id);
                                                                                    if($tipoComision) {
                                                                                        $comisionUsuarioTotal += $subtotalConDescuento * ($tipoComision->porcentaje / 100);
                                                                                    }
                                                                                }
                                                                            }
                                                                            return $comisionUsuarioTotal;
                                                                        }), 2, '.', ','
                                                                    )
                                                                }}
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                    @endif

                                                    <!-- SECCIÓN DE RESULTADOS -->
                                                    @if (Auth::user()->role_as != 1)
                                                    <tr class="table-success">
                                                        <td colspan="7" class="text-center"><strong>RESULTADOS</strong></td>
                                                    </tr>
                                                    <tr class="table-success">
                                                        <td colspan="6" class="text-end"><strong>GANANCIA NETA:</strong></td>
                                                        <td class="text-end">
                                                    @endif
                                                            @php
                                                                // Calcular total de ventas
                                                                $totalVentas = $ventas->where('estado', true)->sum(function($venta) {
                                                                    $totalVenta = 0;
                                                                    foreach($venta->detalleVentas as $detalle) {
                                                                        $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                                                                        $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;
                                                                        $montoDescuento = 0;
                                                                        if($detalle->descuento_id) {
                                                                            $descuento = \App\Models\Descuento::find($detalle->descuento_id);
                                                                            if($descuento) {
                                                                                $porcentajeDescuento = $descuento->porcentaje_descuento;
                                                                                $montoDescuento = $subtotalSinDescuento * ($porcentajeDescuento / 100);
                                                                            }
                                                                        }
                                                                        $totalVenta += ($subtotalSinDescuento - $montoDescuento);
                                                                    }
                                                                    return $totalVenta;
                                                                });

                                                                // Calcular total comisiones trabajador
                                                                $totalComisionesTrabajador = $ventas->where('estado', true)->sum(function($venta) {
                                                                    $comisionTrabajadorTotal = 0;
                                                                    foreach($venta->detalleVentas as $detalle) {
                                                                        $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                                                                        $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;
                                                                        $montoDescuento = 0;
                                                                        if($detalle->descuento_id) {
                                                                            $descuento = \App\Models\Descuento::find($detalle->descuento_id);
                                                                            if($descuento) {
                                                                                $montoDescuento = $subtotalSinDescuento * ($descuento->porcentaje_descuento / 100);
                                                                            }
                                                                        }
                                                                        $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;
                                                                        if($detalle->tipo_comision_trabajador_id) {
                                                                            $tipoComision = \App\Models\TipoComision::find($detalle->tipo_comision_trabajador_id);
                                                                            if($tipoComision) {
                                                                                $comisionTrabajadorTotal += $subtotalConDescuento * ($tipoComision->porcentaje / 100);
                                                                            }
                                                                        }
                                                                    }
                                                                    return $comisionTrabajadorTotal;
                                                                });

                                                                // Calcular total comisiones vendedor
                                                                $totalComisionesVendedor = $ventas->where('estado', true)->sum(function($venta) {
                                                                    $comisionUsuarioTotal = 0;
                                                                    foreach($venta->detalleVentas as $detalle) {
                                                                        $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                                                                        $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;
                                                                        $montoDescuento = 0;
                                                                        if($detalle->descuento_id) {
                                                                            $descuento = \App\Models\Descuento::find($detalle->descuento_id);
                                                                            if($descuento) {
                                                                                $montoDescuento = $subtotalSinDescuento * ($descuento->porcentaje_descuento / 100);
                                                                            }
                                                                        }
                                                                        $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;
                                                                        if($detalle->tipo_comision_usuario_id) {
                                                                            $tipoComision = \App\Models\TipoComision::find($detalle->tipo_comision_usuario_id);
                                                                            if($tipoComision) {
                                                                                $comisionUsuarioTotal += $subtotalConDescuento * ($tipoComision->porcentaje / 100);
                                                                            }
                                                                        }
                                                                    }
                                                                    return $comisionUsuarioTotal;
                                                                });

                                                                // Calcular total impuestos
                                                                $totalImpuestos = $ventas->where('estado', true)->sum(function($venta) {
                                                                    $impuestoTotal = 0;
                                                                    foreach($venta->detalleVentas as $detalle) {
                                                                        $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                                                                        $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;
                                                                        $montoDescuento = 0;
                                                                        if($detalle->descuento_id) {
                                                                            $descuento = \App\Models\Descuento::find($detalle->descuento_id);
                                                                            if($descuento) {
                                                                                $porcentajeDescuento = $descuento->porcentaje_descuento;
                                                                                $montoDescuento = $subtotalSinDescuento * ($porcentajeDescuento / 100);
                                                                            }
                                                                        }
                                                                        $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;
                                                                        $impuestoDetalle = $subtotalConDescuento * ($detalle->porcentaje_impuestos ?? 0) / 100;
                                                                        $impuestoTotal += $impuestoDetalle;
                                                                    }
                                                                    return $impuestoTotal;
                                                                });

                                                                // Calcular total costos
                                                                $totalCostos = $ventas->where('estado', true)->sum(function($venta) {
                                                                    $costosTotal = 0;
                                                                    foreach($venta->detalleVentas as $detalle) {
                                                                        $costosTotal += $detalle->precio_costo * $detalle->cantidad;
                                                                    }
                                                                    return $costosTotal;
                                                                });

                                                                // Calcular ganancia neta
                                                                $gananciaNeta = $totalVentas - $totalComisionesVendedor - $totalComisionesTrabajador - $totalImpuestos - $totalCostos;
                                                            @endphp
                                                    @if (Auth::user()->role_as != 1)
                                                            <strong class="text-success">
                                                                {{ $config->currency_simbol }}.{{ number_format($gananciaNeta, 2, '.', ',') }}
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                    @endif

                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pestaña de Estadísticas -->
                <div class="tab-pane fade" id="estadisticas-tab-pane" role="tabpanel" aria-labelledby="estadisticas-tab" tabindex="0">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 text-white">Análisis de Ventas</h5>
                        </div>
                        <div class="card-body">
                            <!-- Resumen de filtros aplicados -->
                            <div class="alert alert-info mb-4">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Periodo analizado:</strong>
                                {{ \Carbon\Carbon::parse(request('fecha_desde', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d')))->format('d/m/Y') }} al
                                {{ \Carbon\Carbon::parse(request('fecha_hasta', \Carbon\Carbon::now()->format('Y-m-d')))->format('d/m/Y') }}

                                @if(request('numero_factura') || request('cliente') || request('tipo_venta') || request('usuario') || request('estado') !== null || request('estado_pago'))
                                <hr>
                                <strong>Filtros adicionales:</strong>
                                <ul class="mb-0">
                                    @if(request('numero_factura'))
                                        <li>Número de Factura: {{ request('numero_factura') }}</li>
                                    @endif
                                    @if(request('cliente') && $clientes->find(request('cliente')))
                                        <li>Cliente: {{ $clientes->find(request('cliente'))->nombre }}</li>
                                    @endif
                                    @if(request('tipo_venta'))
                                        <li>Tipo de Venta: {{ request('tipo_venta') }}</li>
                                    @endif
                                    @if(request('usuario') && $usuarios->find(request('usuario')))
                                        <li>Usuario: {{ $usuarios->find(request('usuario'))->name }}</li>
                                    @endif
                                    @if(request('estado') !== null)
                                        <li>Estado: {{ request('estado') == '1' ? 'Activa' : 'Cancelada' }}</li>
                                    @endif
                                    @if(request('estado_pago'))
                                        <li>Estado de Pago: {{ ucfirst(request('estado_pago')) }}</li>
                                    @endif
                                </ul>
                                @endif
                            </div>

                            <!-- Gráficos -->
                            <div class="row">
                                <!-- Ventas por Tipo -->
                                <div class="col-xl-6 col-lg-6 col-md-12">
                                    <div class="card mb-3">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="card-title mb-0 text-white">Ventas por Tipo</h5>
                                        </div>
                                        <div class="card-body">
                                            <div style="height: 300px">
                                                <canvas id="chartTipoVentas"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Estado de Pagos -->
                                <div class="col-xl-6 col-lg-6 col-md-12">
                                    <div class="card mb-3">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="card-title mb-0 text-white">Estado de Pagos</h5>
                                        </div>
                                        <div class="card-body">
                                            <div style="height: 300px">
                                                <canvas id="chartPagos"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Resumen financiero -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header bg-success text-white">
                                            <h5 class="card-title mb-0 text-white">Resumen Financiero</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="card bg-light p-3 text-center">
                                                        <h6 class="text-primary mb-2">Ingresos</h6>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>Ventas totales:</div>
                                                            <div><strong>{{ $config->currency_simbol }}.{{ number_format($totalVentas, 2) }}</strong></div>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center text-danger">
                                                            <div>Descuentos:</div>
                                                            <div><strong>{{ $config->currency_simbol }}.{{ number_format($totalDescuentos, 2) }}</strong></div>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center text-info">
                                                            <div>Impuestos:</div>
                                                            <div><strong>{{ $config->currency_simbol }}.{{ number_format($totalImpuestos, 2) }}</strong></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card bg-light p-3 text-center">
                                                        <h6 class="text-danger mb-2">Costos y Gastos</h6>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>Costo de productos:</div>
                                                            <div><strong>{{ $config->currency_simbol }}.{{ number_format($totalCostos, 2) }}</strong></div>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>Comisiones trabajadores:</div>
                                                            <div><strong>{{ $config->currency_simbol }}.{{ number_format($totalComisionesTrabajador, 2) }}</strong></div>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>Comisiones vendedores:</div>
                                                            <div><strong>{{ $config->currency_simbol }}.{{ number_format($totalComisionesVendedor, 2) }}</strong></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card bg-success text-white p-3 text-center h-100 d-flex flex-column justify-content-center">
                                                        <h6 class="mb-3">Ganancia Neta</h6>
                                                        <h3 class="mb-0">{{ $config->currency_simbol }}.{{ number_format($gananciaNeta, 2) }}</h3>
                                                        <div class="mt-2">
                                                            @php
                                                                $margenBruto = $totalVentas > 0 ? ($gananciaNeta / $totalVentas) * 100 : 0;
                                                            @endphp
                                                            Margen: {{ number_format($margenBruto, 2) }}%
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para los gráficos -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Objeto para almacenar las instancias de los gráficos
            let charts = {};

            // Inicializar los gráficos cuando se activa la pestaña de estadísticas
            document.getElementById('estadisticas-tab').addEventListener('shown.bs.tab', function(e) {
                initializeCharts();
            });

            // Si la pestaña de estadísticas está activa al cargar la página, inicializar los gráficos
            if (document.querySelector('#estadisticas-tab.active')) {
                initializeCharts();
            }

            function initializeCharts() {
                try {
                    console.log('Inicializando gráficos de ventas...');

                    // Verificar que los elementos canvas existan
                    const chartTipoVentasEl = document.getElementById('chartTipoVentas');
                    const chartPagosEl = document.getElementById('chartPagos');

                    // Datos para el gráfico de tipos de venta
                    const tiposVenta = [
                        @foreach($ventasPorTipo as $item)
                            '{{ $item['tipo'] }}',
                        @endforeach
                    ];

                    const cantidadesPorTipo = [
                        @foreach($ventasPorTipo as $item)
                            {{ $item['cantidad'] }},
                        @endforeach
                    ];

                    const montosPorTipo = [
                        @foreach($ventasPorTipo as $item)
                            {{ $item['monto'] }},
                        @endforeach
                    ];

                    // Crear gráfico de tipos de venta si no existe ya
                    if (chartTipoVentasEl && !charts.chartTipoVentas) {
                        charts.chartTipoVentas = new Chart(chartTipoVentasEl, {
                            type: 'bar',
                            data: {
                                labels: tiposVenta,
                                datasets: [
                                    {
                                        label: 'Cantidad de ventas',
                                        data: cantidadesPorTipo,
                                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                        borderColor: 'rgba(54, 162, 235, 1)',
                                        borderWidth: 1,
                                        yAxisID: 'y'
                                    },
                                    {
                                        label: 'Monto total',
                                        data: montosPorTipo,
                                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 1,
                                        yAxisID: 'y1'
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        type: 'linear',
                                        position: 'left',
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Cantidad'
                                        }
                                    },
                                    y1: {
                                        type: 'linear',
                                        position: 'right',
                                        beginAtZero: true,
                                        grid: {
                                            drawOnChartArea: false
                                        },
                                        title: {
                                            display: true,
                                            text: 'Monto Total'
                                        }
                                    }
                                }
                            }
                        });
                        console.log('Gráfico de tipos de venta creado');
                    }

                    // Gráfico de estado de pagos si no existe ya
                    if (chartPagosEl && !charts.chartPagos) {
                        charts.chartPagos = new Chart(chartPagosEl, {
                            type: 'pie',
                            data: {
                                labels: ['Pagado', 'Pendiente'],
                                datasets: [{
                                    data: [{{ $totalPagado }}, {{ $pendientePago }}],
                                    backgroundColor: [
                                        'rgba(75, 192, 192, 0.6)',
                                        'rgba(255, 99, 132, 0.6)'
                                    ],
                                    borderColor: [
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(255, 99, 132, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }
                        });
                        console.log('Gráfico de estado de pagos creado');
                    }
                } catch (error) {
                    console.error('Error al crear los gráficos:', error);
                }
            }
        });
    </script>
@endsection
