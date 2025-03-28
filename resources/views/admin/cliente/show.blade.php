@extends('layouts.admin')
@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="page-title">
                    <h5>Ficha de Cliente</h5>
                </div>
            </div>
            <!-- Date range start -->
            <div class="d-flex align-items-end d-none d-sm-block">
                <h6 class="float-end text-light" id="reloj"></h6>
            </div>
        </div>
        <!-- Main header ends -->

        <!-- Content wrapper start -->
        <div class="content-wrapper">
            <!-- Barra de navegación de cliente -->
            <div class="card bg-primary bg-opacity-10 border-0 mb-3">
                <div class="card-body py-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ url('clientes') }}">Clientes</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $cliente->nombre }}</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Perfil del cliente -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="row g-0">
                                <div class="col-lg-3 col-md-4">
                                    <div class="bg-light h-100 d-flex flex-column align-items-center justify-content-center p-4">
                                        @if ($cliente->fotografia != null)
                                            <img src="{{ asset('assets/imgs/clientes/'.$cliente->fotografia) }}" class="img-9x rounded-circle border border-4 border-white shadow mb-3" alt="{{ $cliente->nombre }}" />
                                        @else
                                            <img src="{{ asset('assets/imgs/clientes/usericon4.png') }}" class="img-9x rounded-circle border border-4 border-white shadow mb-3" alt="{{ $cliente->nombre }}" />
                                        @endif
                                        <h4 class="text-center mb-0">{{ $cliente->nombre }}</h4>

                                        @php
                                            $fnacimiento = null;
                                            $edad = 0;
                                            if ($cliente->fecha_nacimiento != null) {
                                                $fnacimiento = date("d-m-Y", strtotime($cliente->fecha_nacimiento));
                                                //calcular edad
                                                $cumpleanos = new DateTime($cliente->fecha_nacimiento);
                                                $hoy = new DateTime();
                                                $annos = $hoy->diff($cumpleanos);
                                                $edad = $annos->y;
                                            }
                                        @endphp

                                        @if ($edad > 0)
                                            <div class="badge bg-secondary mt-2">{{ $edad }} años</div>
                                        @endif

                                        <div class="mt-4 d-flex flex-column w-100">
                                            <a href="{{ url('edit-cliente/'.$cliente->id) }}" class="btn btn-primary btn-sm mb-2">
                                                <i class="bi bi-pencil"></i> Editar Cliente
                                            </a>
                                            <a target="_blank" href="{{ url('pdf-cliente/'.$cliente->id) }}" class="btn btn-outline-info btn-sm mb-2">
                                                <i class="bi bi-printer"></i> Imprimir Ficha
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $cliente->id }}">
                                                <i class="bi bi-trash"></i> Eliminar Cliente
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-9 col-md-8">
                                    <div class="p-4">
                                        <!-- Pestañas de información -->
                                        <ul class="nav nav-tabs nav-tabs-custom" id="clientTab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-tab-pane"
                                                    type="button" role="tab" aria-selected="true">
                                                    <i class="bi bi-info-circle me-1"></i> Información
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="vehiculos-tab" data-bs-toggle="tab" data-bs-target="#vehiculos-tab-pane"
                                                    type="button" role="tab" aria-selected="false">
                                                    <i class="bi bi-car-front me-1"></i> Vehículos
                                                    <span class="badge rounded-pill bg-primary ms-1">{{ $vehiculos->count() }}</span>
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="ventas-tab" data-bs-toggle="tab" data-bs-target="#ventas-tab-pane"
                                                    type="button" role="tab" aria-selected="false">
                                                    <i class="bi bi-cart-check me-1"></i> Ventas
                                                    <span class="badge rounded-pill bg-success ms-1">{{ $ventas->count() ?? 0 }}</span>
                                                </button>
                                            </li>
                                        </ul>

                                        <div class="tab-content p-3 border border-top-0 rounded-bottom" id="clientTabContent">
                                            <!-- Pestaña de información -->
                                            <div class="tab-pane fade show active" id="info-tab-pane" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
                                                <div class="row g-4">
                                                    <div class="col-md-6">
                                                        <div class="info-group">
                                                            <label class="text-muted small mb-1">
                                                                <i class="bi bi-calendar3 me-1"></i>Fecha de Nacimiento
                                                            </label>
                                                            <p class="mb-3 fw-medium">
                                                                @if($fnacimiento)
                                                                    {{ $fnacimiento }}
                                                                @else
                                                                    <span class="text-secondary">No especificada</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="info-group">
                                                            <label class="text-muted small mb-1">
                                                                <i class="bi bi-envelope me-1"></i>Email
                                                            </label>
                                                            <p class="mb-3 fw-medium">
                                                                @if($cliente->email)
                                                                    <a class="link-primary" href="mailto:{{ $cliente->email }}">{{ $cliente->email }}</a>
                                                                @else
                                                                    <span class="text-secondary">No especificado</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="info-group">
                                                            <label class="text-muted small mb-1">
                                                                <i class="bi bi-telephone me-1"></i>Teléfono
                                                            </label>
                                                            <p class="mb-3 fw-medium">
                                                                <div class="d-flex flex-wrap gap-2">
                                                                    <a class="badge bg-secondary bg-opacity-10 text-secondary" href="tel:+502{{ $cliente->telefono }}">
                                                                        <i class="bi bi-telephone"></i> {{ $cliente->telefono }}
                                                                    </a>

                                                                    @if($cliente->celular)
                                                                        <a class="badge bg-secondary bg-opacity-10 text-secondary" href="tel:+502{{ $cliente->celular }}">
                                                                            <i class="bi bi-phone"></i> {{ $cliente->celular }}
                                                                        </a>
                                                                        <a class="badge bg-success" href="https://wa.me/502{{ $cliente->celular }}" target="_blank">
                                                                            <i class="bi bi-whatsapp"></i> WhatsApp
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="info-group">
                                                            <label class="text-muted small mb-1">
                                                                <i class="bi bi-card-text me-1"></i>DPI
                                                            </label>
                                                            <p class="mb-3 fw-medium">
                                                                @if($cliente->dpi)
                                                                    <span class="badge bg-light text-dark border">{{ $cliente->dpi }}</span>
                                                                @else
                                                                    <span class="text-secondary">No especificado</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="info-group">
                                                            <label class="text-muted small mb-1">
                                                                <i class="bi bi-receipt me-1"></i>NIT
                                                            </label>
                                                            <p class="mb-3 fw-medium">
                                                                @if($cliente->nit)
                                                                    <span class="badge bg-light text-dark border">{{ $cliente->nit }}</span>
                                                                @else
                                                                    <span class="text-secondary">No especificado</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="info-group">
                                                            <label class="text-muted small mb-1">
                                                                <i class="bi bi-geo-alt me-1"></i>Dirección
                                                            </label>
                                                            <p class="mb-0 fw-medium">
                                                                @if($cliente->direccion)
                                                                    {{ $cliente->direccion }}
                                                                @else
                                                                    <span class="text-secondary">No especificada</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Pestaña de vehículos -->
                                            <div class="tab-pane fade" id="vehiculos-tab-pane" role="tabpanel" aria-labelledby="vehiculos-tab" tabindex="0">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <h5 class="mb-0"><i class="bi bi-car-front me-2"></i>Vehículos del Cliente</h5>
                                                    <a href="{{ url('add-vehiculo?cliente_id=' . $cliente->id) }}" class="btn btn-success btn-sm">
                                                        <i class="bi bi-plus-lg"></i> Agregar Vehículo
                                                    </a>
                                                </div>

                                                @if($vehiculos->count() > 0)
                                                    <div class="row g-3">
                                                        @foreach($vehiculos as $vehiculo)
                                                            <div class="col-xl-6">
                                                                <div class="card h-100">
                                                                    <div class="card-body p-0">
                                                                        <div class="row g-0">
                                                                            <div class="col-md-4">
                                                                                <div class="h-100 d-flex align-items-center justify-content-center p-3 bg-light">
                                                                                    @if ($vehiculo->fotografia != null)
                                                                                        <img src="{{ asset('assets/imgs/vehiculos/'.$vehiculo->fotografia) }}"
                                                                                            class="img-fluid rounded" alt="Vehículo" />
                                                                                    @else
                                                                                        <img src="{{ asset('assets/imgs/vehiculos/vehiculoicon.png') }}"
                                                                                            class="img-fluid rounded" alt="Vehículo" />
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-8">
                                                                                <div class="p-3">
                                                                                    <h5 class="card-title mb-1">
                                                                                        {{ $vehiculo->marca }} {{ $vehiculo->modelo }} {{ $vehiculo->ano }}
                                                                                    </h5>
                                                                                    <div class="mb-2">
                                                                                        <span class="badge bg-primary me-1">{{ $vehiculo->color }}</span>
                                                                                        <span class="badge bg-secondary">{{ $vehiculo->placa }}</span>
                                                                                    </div>
                                                                                    <p class="card-text small mb-3">
                                                                                        <span class="d-block text-muted">
                                                                                            <strong>VIN:</strong> {{ $vehiculo->vin ?: 'No especificado' }}
                                                                                        </span>
                                                                                    </p>

                                                                                    <div class="d-flex gap-2">
                                                                                        <a href="{{ url('show-vehiculo/'.$vehiculo->id) }}" class="btn btn-sm btn-primary">
                                                                                            <i class="bi bi-eye"></i>
                                                                                        </a>
                                                                                        <a href="{{ url('edit-vehiculo/'.$vehiculo->id) }}" class="btn btn-sm btn-warning">
                                                                                            <i class="bi bi-pencil"></i>
                                                                                        </a>
                                                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                                                            data-bs-target="#deleteModal-{{ $vehiculo->id }}">
                                                                                            <i class="bi bi-trash"></i>
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                @include('admin.vehiculo.deletemodal')
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="text-center py-5">
                                                        <img src="{{ asset('assets/imgs/vehiculos/vehiculoicon.png') }}" class="mb-3 opacity-25" width="80" alt="No hay vehículos" />
                                                        <h6>No hay vehículos registrados para este cliente</h6>
                                                        <p class="text-muted">Registre un vehículo haciendo clic en el botón "Agregar Vehículo"</p>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Pestaña de ventas -->
                                            <div class="tab-pane fade" id="ventas-tab-pane" role="tabpanel" aria-labelledby="ventas-tab" tabindex="0">
                                                <div class="d-flex justify-content-between align-items-center mb-4">
                                                    <h5 class="mb-0"><i class="bi bi-cart-check me-2"></i>Historial de Ventas</h5>
                                                    <a href="{{ url('add-venta?cliente_id=' . $cliente->id) }}" class="btn btn-success btn-sm">
                                                        <i class="bi bi-plus-lg"></i> Nueva Venta
                                                    </a>
                                                </div>

                                                @if(isset($ventas) && $ventas->count() > 0)
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-light">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-6">
                                                                    <h6 class="mb-0">Total de ventas: <span class="badge bg-primary">{{ $ventas->total() }}</span></h6>
                                                                </div>
                                                                <div class="col-md-6 text-end">
                                                                    <span class="text-secondary small">Ordenado por fecha (más reciente primero)</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-body p-0">
                                                            <div class="table-responsive">
                                                                <table class="table table-hover border-0 mb-0">
                                                                    <thead class="table-light">
                                                                        <tr>
                                                                            <th class="border-0">#</th>
                                                                            <th class="border-0">Fecha</th>
                                                                            <th class="border-0">Detalle</th>
                                                                            <th class="border-0 text-center">Estado/Pago</th>
                                                                            <th class="border-0 text-end">Total</th>
                                                                            <th class="border-0 text-center">Acciones</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($ventas as $venta)
                                                                            <tr>
                                                                                <td>
                                                                                    <div class="d-flex align-items-center">
                                                                                        <div class="avatar avatar-sm bg-primary text-white rounded-circle me-2">
                                                                                            <i class="bi bi-receipt"></i>
                                                                                        </div>
                                                                                        <div>
                                                                                            <div class="fw-medium">{{ $venta->numero_factura }}</div>
                                                                                            @if($venta->numero_factura)
                                                                                                <div class="text-muted small">Fact: {{ $venta->numero_factura }}</div>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="fw-medium">{{ date('d/m/Y', strtotime($venta->fecha)) }}</div>
                                                                                    <div class="text-muted small">
                                                                                        @if($venta->usuario)
                                                                                            <i class="bi bi-person"></i> {{ $venta->usuario->name }}
                                                                                        @endif
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    @if($venta->vehiculo)
                                                                                        <div class="d-flex align-items-center mb-1">
                                                                                            <div class="avatar avatar-xs bg-info text-white rounded-circle me-1">
                                                                                                <i class="bi bi-car-front-fill"></i>
                                                                                            </div>
                                                                                            <span class="text-truncate">
                                                                                                {{ $venta->vehiculo->marca }} {{ $venta->vehiculo->modelo }}
                                                                                            </span>
                                                                                        </div>
                                                                                    @endif

                                                                                    @php
                                                                                        $detalles = $venta->detalleVentas;
                                                                                        $totalItems = $detalles->count();
                                                                                    @endphp

                                                                                    @if($totalItems > 0)
                                                                                        <div class="small">
                                                                                            <div class="fw-medium">{{ $totalItems }} producto(s):</div>
                                                                                            <div class="text-truncate" style="max-width: 200px;">
                                                                                                @foreach($detalles->take(2) as $index => $detalle)
                                                                                                    @if($index > 0), @endif
                                                                                                    @if($detalle->articulo)
                                                                                                        {{ $detalle->articulo->nombre ?? 'Producto' }} ({{ $detalle->cantidad }})
                                                                                                    @else
                                                                                                        Producto #{{ $detalle->articulo_id }}
                                                                                                    @endif
                                                                                                @endforeach
                                                                                                @if($totalItems > 2)
                                                                                                    <span class="text-muted">y {{ $totalItems - 2 }} más</span>
                                                                                                @endif
                                                                                            </div>
                                                                                        </div>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    @php
                                                                                        // Calcular el total de la venta
                                                                                        $totalVenta = 0;
                                                                                        if ($venta->detalleVentas && $venta->detalleVentas->count() > 0) {
                                                                                            $totalVenta = $venta->detalleVentas->sum('sub_total');
                                                                                        }

                                                                                        // Calcular el total pagado
                                                                                        $totalPagado = 0;
                                                                                        if ($venta->pagos && $venta->pagos->count() > 0) {
                                                                                            $totalPagado = $venta->pagos->sum('monto');
                                                                                        }

                                                                                        // Determinar el estado del pago
                                                                                        $estadoPago = 'pendiente';
                                                                                        if ($totalPagado >= $totalVenta) {
                                                                                            $estadoPago = 'pagada';
                                                                                        } elseif ($totalPagado > 0) {
                                                                                            $estadoPago = 'parcial';
                                                                                        }
                                                                                    @endphp

                                                                                    <!-- Estado de la venta -->
                                                                                    <div class="d-block mb-1">
                                                                                        @if($venta->estado == 1)
                                                                                            <span class="badge bg-primary">Activa</span>
                                                                                        @else
                                                                                            <span class="badge bg-danger">Cancelada</span>
                                                                                        @endif
                                                                                    </div>

                                                                                    <!-- Estado del pago -->
                                                                                    <div class="d-block mb-1">
                                                                                        @if($estadoPago == 'pagada')
                                                                                            <span class="badge bg-success">Pagada</span>
                                                                                        @elseif($estadoPago == 'parcial')
                                                                                            <span class="badge bg-info">Pago Parcial</span>
                                                                                        @else
                                                                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                                                                        @endif
                                                                                    </div>

                                                                                    <!-- Mostrar información de pagos -->
                                                                                    @if($venta->pagos && $venta->pagos->count() > 0)
                                                                                        <div class="small text-muted">
                                                                                            {{ $venta->pagos->count() }} pago(s)
                                                                                            <div class="progress mt-1" style="height: 5px;">
                                                                                                @php
                                                                                                    $porcentajePagado = min(100, ($totalPagado / $totalVenta) * 100);
                                                                                                @endphp
                                                                                                <div class="progress-bar bg-success" role="progressbar"
                                                                                                     style="width: {{ $porcentajePagado }}%;"
                                                                                                     aria-valuenow="{{ $porcentajePagado }}"
                                                                                                     aria-valuemin="0"
                                                                                                     aria-valuemax="100"></div>
                                                                                            </div>
                                                                                            <span class="small">{{ number_format($porcentajePagado, 0) }}%</span>
                                                                                        </div>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-end">
                                                                                    @php
                                                                                        $total = 0;
                                                                                        if ($venta->detalleVentas && $venta->detalleVentas->count() > 0) {
                                                                                            $total = $venta->detalleVentas->sum('sub_total');
                                                                                        }
                                                                                    @endphp
                                                                                    <div class="fw-bold">Q {{ number_format($total, 2) }}</div>

                                                                                    @if($venta->pagos && $venta->pagos->count() > 0)
                                                                                        <div class="text-success small">
                                                                                            <i class="bi bi-check-circle-fill"></i>
                                                                                            {{ $venta->pagos->count() }} pago(s)
                                                                                        </div>
                                                                                    @endif
                                                                                </td>
                                                                                <td class="text-center">
                                                                                    <div class="btn-group">
                                                                                        <a href="{{ url('show-venta/'.$venta->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Ver Detalles">
                                                                                            <i class="bi bi-eye-fill"></i>
                                                                                        </a>
                                                                                        <a href="{{ url('pdf-venta/'.$venta->id) }}" target="_blank" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Imprimir">
                                                                                            <i class="bi bi-printer-fill"></i>
                                                                                        </a>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer bg-white">
                                                            <div class="d-flex justify-content-center">
                                                                {{ $ventas->links() }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Resumen de ventas -->
                                                    <div class="card">
                                                        <div class="card-body">
                                                            <div class="row g-4">
                                                                <div class="col-md-3 col-6">
                                                                    <div class="text-center">
                                                                        <h5 class="mb-1 text-primary">{{ $ventas->count() }}</h5>
                                                                        <p class="text-muted small mb-0">Total de Ventas</p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 col-6">
                                                                    <div class="text-center">
                                                                        @php
                                                                            // Calcular ventas pagadas basado en los pagos reales
                                                                            $ventasPagadas = 0;
                                                                            foreach($ventas as $venta) {
                                                                                $totalVenta = $venta->detalleVentas->sum('sub_total');
                                                                                $totalPagado = $venta->pagos->sum('monto');

                                                                                if ($totalPagado >= $totalVenta && $venta->estado == 1) {
                                                                                    $ventasPagadas++;
                                                                                }
                                                                            }
                                                                        @endphp
                                                                        <h5 class="mb-1 text-success">{{ $ventasPagadas }}</h5>
                                                                        <p class="text-muted small mb-0">Ventas Pagadas</p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 col-6">
                                                                    <div class="text-center">
                                                                        @php
                                                                            // Calcular ventas pendientes basado en los pagos reales
                                                                            $ventasPendientes = 0;
                                                                            $ventasParciales = 0;

                                                                            foreach($ventas as $venta) {
                                                                                if ($venta->estado != 1) continue; // Omitir ventas canceladas

                                                                                $totalVenta = $venta->detalleVentas->sum('sub_total');
                                                                                $totalPagado = $venta->pagos->sum('monto');

                                                                                if ($totalPagado <= 0) {
                                                                                    $ventasPendientes++;
                                                                                } elseif ($totalPagado < $totalVenta) {
                                                                                    $ventasParciales++;
                                                                                }
                                                                            }
                                                                        @endphp
                                                                        <h5 class="mb-1 text-warning">
                                                                            {{ $ventasPendientes }}
                                                                            @if($ventasParciales > 0)
                                                                                <small class="text-info">+{{ $ventasParciales }} parciales</small>
                                                                            @endif
                                                                        </h5>
                                                                        <p class="text-muted small mb-0">Ventas Pendientes</p>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 col-6">
                                                                    <div class="text-center">
                                                                        @php
                                                                            $totalVentas = 0;
                                                                            $totalCobrado = 0;
                                                                            foreach($ventas as $venta) {
                                                                                if ($venta->detalleVentas && $venta->detalleVentas->count() > 0) {
                                                                                    $totalVenta = $venta->detalleVentas->sum('sub_total');
                                                                                    $totalVentas += $totalVenta;
                                                                                    $totalCobrado += min($totalVenta, $venta->pagos->sum('monto'));
                                                                                }
                                                                            }
                                                                        @endphp
                                                                        <h5 class="mb-1">Q {{ number_format($totalVentas, 2) }}</h5>
                                                                        <p class="text-muted small mb-0">
                                                                            Monto Total
                                                                            @if($totalCobrado < $totalVentas)
                                                                                <span class="badge bg-success">{{ number_format(($totalCobrado/$totalVentas)*100, 0) }}% cobrado</span>
                                                                            @endif
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-center py-5">
                                                        <div class="mb-3">
                                                            <img src="{{ asset('assets/imgs/cart-empty.png') }}" class="opacity-25" width="80" alt="No hay ventas" />
                                                        </div>
                                                        <h6>No hay ventas registradas para este cliente</h6>
                                                        <p class="text-muted">Registre una nueva venta haciendo clic en el botón "Nueva Venta"</p>
                                                        <a href="{{ url('add-venta?cliente_id=' . $cliente->id) }}" class="btn btn-success">
                                                            <i class="bi bi-plus-circle"></i> Crear Nueva Venta
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('admin.cliente.deletemodal')
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->

    <style>
        /* Estilo personalizado para las pestañas */
        .nav-tabs-custom .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
            padding: 0.5rem 1rem;
            color: #6c757d;
            font-weight: 500;
        }

        .nav-tabs-custom .nav-link:hover {
            background-color: rgba(0,0,0,0.05);
        }

        .nav-tabs-custom .nav-link.active {
            color: #495057;
            background-color: #fff;
            border-color: #dee2e6 #dee2e6 #fff;
        }
    </style>

    <script>
        // Activar los tooltips de Bootstrap
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
@endsection
