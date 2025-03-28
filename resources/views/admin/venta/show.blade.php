@extends('layouts.admin')
@section('content')
    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="page-title">
                    <h5>Detalle de Venta @if ($venta->numero_factura) - Factura: @endif
                        @if ($venta->numero_factura)
                            {{ $venta->numero_factura }}
                        @else
                            No especificado
                        @endif
                    </h5>
                </div>
            </div>
        </div>
        <div class="content-wrapper">
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <a href="{{ url('ventas') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Ventas
                    </a>
                    <br>
                    <div class="card mb-3">

                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center pb-3">
                            <h5 class="card-title mb-0 text-white">
                                Información de Venta
                                @unless($venta->estado)
                                    <span class="badge bg-danger ms-2">CANCELADA</span>
                                @endunless
                            </h5>
                            <div>
                                @if($venta->estado)
                                    <a href="{{ url('edit-venta/'.$venta->id) }}" class="btn btn-sm btn-warning me-1">
                                        <i class="bi bi-pencil-fill"></i> Editar
                                    </a>
                                    <a type="button" class="btn btn-sm btn-danger me-1" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $venta->id }}">
                                        <i class="bi bi-trash-fill"></i> Eliminar
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-warning me-1" disabled>
                                        <i class="bi bi-pencil-fill"></i> Editar
                                    </button>
                                @endif
                                <a href="{{ route('ventas.export.single.pdf', $venta->id) }}" class="btn btn-sm btn-light">
                                    <i class="bi bi-file-pdf"></i> PDF
                                </a>
                            </div>
                        </div>
                        <div class="card-body {{ $venta->estado ? '' : 'bg-light' }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h6 class="fw-bold">Factura:</h6>
                                        <p>{{ $venta->numero_factura ?: 'No especificado' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <h6 class="fw-bold">Fecha:</h6>
                                        <p>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <h6 class="fw-bold">Tipo de Venta:</h6>
                                        <p>{{ $venta->tipo_venta }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <h6 class="fw-bold">Estado de Pago:</h6>
                                        <span class="badge {{ $venta->estado_pago == 'pagado' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($venta->estado_pago) }}
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <h6 class="fw-bold">Estado:</h6>
                                        @if($venta->estado)
                                            <span class="badge bg-success">Activa</span>
                                        @else
                                            <span class="badge bg-danger">Cancelada</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h6 class="fw-bold">Cliente:</h6>
                                        <p>
                                            @if($venta->cliente)
                                                <a href="{{ url('show-cliente/'.$venta->cliente->id) }}">{{ $venta->cliente->nombre }}</a>
                                                @if($venta->cliente->telefono)
                                                    <br><small>Teléfono: {{ $venta->cliente->telefono }}</small>
                                                @endif
                                                @if($venta->cliente->email)
                                                    <br><small>Email: {{ $venta->cliente->email }}</small>
                                                @endif
                                            @else
                                                No especificado
                                            @endif
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <h6 class="fw-bold">Vehículo:</h6>
                                        <p>
                                            @if($venta->vehiculo)
                                                <i class="bi bi-car-front"></i>
                                                <a href="{{ url('show-vehiculo/'.$venta->vehiculo->id) }}">
                                                    {{ $venta->vehiculo->marca }} {{ $venta->vehiculo->modelo }} - {{ $venta->vehiculo->placa }}
                                                </a>
                                            @else
                                                No especificado
                                            @endif
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <h6 class="fw-bold">Registrado por:</h6>
                                        <p>{{ optional($venta->usuario)->name ?: 'Usuario no disponible' }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <h6 class="fw-bold">Fecha de registro:</h6>
                                        <p>{{ $venta->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0 text-white">Detalles de la Venta</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Artículo</th>
                                            <th class="text-center">Cantidad</th>
                                            <th>Precio Venta</th>
                                            <th>Precio Costo</th>
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
                                            $totalDescuentos = 0;
                                            $totalVenta = 0;
                                            $subtotalSinDescuentoTotal = 0;
                                            $totalImpuestos = 0;
                                            $totalComisionTrabajador = 0;
                                            $totalComisionVendedor = 0;
                                            $totalCostoCompra = 0;
                                        @endphp
                                        @foreach($venta->detalleVentas as $detalle)
                                            @php
                                                // Calcular precio unitario (usar el precio del artículo si existe, o calcularlo desde el subtotal)
                                                $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                                                $precioCosto = $detalle->precio_costo;

                                                // Calcular subtotal sin descuento (precio unitario × cantidad)
                                                $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;
                                                $subtotalSinDescuentoTotal += $subtotalSinDescuento;

                                                // Calcular monto de descuento si aplica
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
                                                $totalImpuestos += $impuestoDetalle;

                                                // Calcular comisión del trabajador
                                                $comisionTrabajador = 0;
                                                $porcentajeComisionTrabajador = 0;
                                                if ($detalle->tipo_comision_trabajador_id) {
                                                    $tipoComisionTrabajador = \App\Models\TipoComision::find($detalle->tipo_comision_trabajador_id);
                                                    if ($tipoComisionTrabajador) {
                                                        $porcentajeComisionTrabajador = $tipoComisionTrabajador->porcentaje;
                                                        $comisionTrabajador = $subtotalConDescuento * ($porcentajeComisionTrabajador / 100);
                                                        $totalComisionTrabajador += $comisionTrabajador;
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
                                                        $totalComisionVendedor += $comisionUsuario;
                                                    }
                                                }

                                                // Calcular costo de compra total
                                                $costoCompraArticulo = $detalle->precio_costo * $detalle->cantidad;
                                                $totalCostoCompra += $costoCompraArticulo;

                                                // Actualizar totales
                                                $totalDescuentos += $montoDescuento;
                                                $totalVenta += $subtotalConDescuento;
                                            @endphp
                                            <tr>
                                                <td>
                                                    @if($detalle->articulo)
                                                        <strong>{{ $detalle->articulo->codigo }}</strong> - {{ $detalle->articulo->nombre }}
                                                    @else
                                                        Artículo no disponible
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    {{ $detalle->cantidad }}
                                                    @if($detalle->articulo && $detalle->articulo->unidad)
                                                        {{ $detalle->articulo->unidad->abreviatura }}
                                                    @endif
                                                </td>
                                                <td>{{ $config->currency_simbol }}.{{ number_format($precioUnitario, 2, '.', ',') }}</td>
                                                <td>{{ $config->currency_simbol }}.{{ number_format($precioCosto, 2, '.', ',') }}</td>
                                                <td>
                                                    @if($detalle->descuento_id)
                                                        @php
                                                            $descuento = \App\Models\Descuento::find($detalle->descuento_id);
                                                        @endphp
                                                        @if($descuento)
                                                            {{ $descuento->nombre }} ({{ $descuento->porcentaje_descuento }}%) -
                                                            {{ $config->currency_simbol }}.{{ number_format($montoDescuento, 2, '.', ',') }}
                                                        @else
                                                            Sin descuento
                                                        @endif
                                                    @else
                                                        Sin descuento
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    {{ $config->currency_simbol }}.{{ number_format($impuestoDetalle, 2, '.', ',') }}
                                                    ({{ number_format($detalle->porcentaje_impuestos ?? 0, 2) }}%)
                                                </td>
                                                @if (Auth::user()->role_as != 1)
                                                    <td class="text-end">
                                                        @if ($detalle->tipo_comision_trabajador_id)
                                                            {{ $config->currency_simbol }}.{{ number_format($comisionTrabajador, 2, '.', ',') }}
                                                            ({{ number_format($porcentajeComisionTrabajador, 2) }}%)<br>
                                                            <small class="text-muted">
                                                                @if($detalle->trabajador)
                                                                    {{ $detalle->trabajador->nombre }}
                                                                @else
                                                                    No asignado
                                                                @endif
                                                            </small>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        @if ($detalle->tipo_comision_usuario_id)
                                                            {{ $config->currency_simbol }}.{{ number_format($comisionUsuario, 2, '.', ',') }}
                                                            ({{ number_format($porcentajeComisionUsuario, 2) }}%)<br>
                                                            <small class="text-muted">
                                                                {{ optional($detalle->usuario)->name ?: 'Usuario no disponible' }}
                                                            </small>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                @endif
                                                <td class="text-end"><strong>{{ $config->currency_simbol }}.{{ number_format($subtotalConDescuento, 2, '.', ',') }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <!-- SECCIÓN DE VENTAS -->
                                        <tr class="table-primary">
                                            <td colspan="9" class="text-center"><strong>RESUMEN DE VENTA</strong></td>
                                        </tr>
                                        <tr class="table-secondary">
                                            <td colspan="7" class="text-end"><strong>Subtotal sin descuento:</strong></td>
                                            <td colspan="2" class="text-end">{{ $config->currency_simbol }}.{{ number_format($subtotalSinDescuentoTotal, 2, '.', ',') }}</td>
                                        </tr>
                                        <tr class="table-secondary">
                                            <td colspan="7" class="text-end"><strong>Total Descuentos:</strong></td>
                                            <td colspan="2" class="text-end text-danger">{{ $config->currency_simbol }}.{{ number_format($totalDescuentos, 2, '.', ',') }}</td>
                                        </tr>
                                        <tr class="table-secondary">
                                            <td colspan="7" class="text-end"><strong>Total Impuestos:</strong></td>
                                            <td colspan="2" class="text-end text-info">{{ $config->currency_simbol }}.{{ number_format($totalImpuestos, 2, '.', ',') }}</td>
                                        </tr>
                                        @if (Auth::user()->role_as != 1)
                                            <tr class="table-secondary">
                                                <td colspan="7" class="text-end"><strong>Total Comisiones Trabajador:</strong></td>
                                                <td colspan="2" class="text-end text-success">{{ $config->currency_simbol }}.{{ number_format($totalComisionTrabajador, 2, '.', ',') }}</td>
                                            </tr>
                                            <tr class="table-secondary">
                                                <td colspan="7" class="text-end"><strong>Total Comisiones Vendedor:</strong></td>
                                                <td colspan="2" class="text-end text-primary">{{ $config->currency_simbol }}.{{ number_format($totalComisionVendedor, 2, '.', ',') }}</td>
                                            </tr>
                                            <tr class="table-secondary">
                                                <td colspan="7" class="text-end"><strong>Total Costo de Compra:</strong></td>
                                                <td colspan="2" class="text-end text-danger">{{ $config->currency_simbol }}.{{ number_format($totalCostoCompra, 2, '.', ',') }}</td>
                                            </tr>
                                        @endif
                                        <tr class="table-secondary">
                                            <td colspan="7" class="text-end"><strong>Total Venta:</strong></td>
                                            <td colspan="2" class="text-end text-primary"><h5>{{ $config->currency_simbol }}.{{ number_format($totalVenta, 2, '.', ',') }}</h5></td>
                                        </tr>
                                        @if (Auth::user()->role_as != 1)
                                            <tr class="table-success">
                                                <td colspan="7" class="text-end"><strong>GANANCIA NETA:</strong></td>
                                                <td colspan="2" class="text-end">
                                                    <h5 class="text-success">{{ $config->currency_simbol }}.{{ number_format($totalVenta - $totalComisionTrabajador - $totalComisionVendedor - $totalImpuestos - $totalCostoCompra, 2, '.', ',') }}</h5>
                                                </td>
                                            </tr>
                                        @endif
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de Pagos -->
                    <div class="card mt-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 text-white">Pagos de la Venta</h5>
                            @if($venta->estado)
                                <button type="button" class="btn btn-light btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#createPagoModal">
                                    <i class="bi bi-plus-circle"></i> Registrar Pago
                                </button>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($venta->pagos && $venta->pagos->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Monto</th>
                                                <th>Método de Pago</th>
                                                <th>Referencia</th>
                                                <th>Comprobante</th>
                                                <th>Registrado por</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $totalPagado = 0;
                                            @endphp

                                            @foreach($venta->pagos as $pago)
                                                @php
                                                    $totalPagado += $pago->monto;
                                                @endphp
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') }}</td>
                                                    <td class="text-end">{{ $config->currency_simbol }}.{{ number_format($pago->monto, 2, '.', ',') }}</td>
                                                    <td>{{ $pago->nombre_metodo_pago }}</td>
                                                    <td>{{ $pago->referencia ?: 'N/A' }}</td>
                                                    <td>
                                                        @if($pago->comprobante_imagen)
                                                            <a href="{{ asset($pago->comprobante_imagen) }}" target="_blank">
                                                                <i class="bi bi-file-earmark-image"></i> Ver comprobante
                                                            </a>
                                                        @else
                                                            <span class="text-muted">Sin comprobante</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ optional($pago->usuario)->name }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editPagoModal{{ $pago->id }}">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deletePagoModal{{ $pago->id }}">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @include('admin.venta.editpagomodal', ['pago' => $pago])
                                                @include('admin.venta.deletepagomodal', ['pago' => $pago])
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-info">
                                                <td colspan="1"><strong>TOTAL PAGADO:</strong></td>
                                                <td class="text-end"><strong>{{ $config->currency_simbol }}.{{ number_format($totalPagado, 2, '.', ',') }}</strong></td>
                                                <td colspan="5">
                                                    @php
                                                        $saldoPendiente = $totalVenta - $totalPagado;
                                                    @endphp
                                                    <span class="float-end">
                                                        <strong>Saldo pendiente: {{ $config->currency_simbol }}.{{ number_format($saldoPendiente > 0 ? $saldoPendiente : 0, 2, '.', ',') }}</strong>
                                                        @if($saldoPendiente <= 0)
                                                            <span class="badge bg-success ms-2">PAGADO COMPLETAMENTE</span>
                                                        @endif
                                                    </span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    No hay pagos registrados para esta venta.
                                    @if($venta->estado)
                                        <button type="button" class="btn btn-primary btn-sm ms-3 mb-3" data-bs-toggle="modal" data-bs-target="#createPagoModal">
                                            <i class="bi bi-plus-circle"></i> Registrar Primer Pago
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <a href="{{ url('ventas') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Volver a Ventas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.venta.deletemodal')
    @include('admin.venta.createpagomodal')
@endsection
