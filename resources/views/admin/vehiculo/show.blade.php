@extends('layouts.admin')
@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-car-front"></i>
                </div>
                <div class="page-title">
                    <h5>Detalle de Vehículo</h5>
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
            <!-- Breadcrumb start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('vehiculos') }}">Vehículos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</li>
                </ol>
            </nav>
            <!-- Breadcrumb end -->

            <!-- Row start -->
            <div class="row gx-3">
                <!-- Acciones rápidas -->
                <div class="col-12 mb-3">
                    <div class="d-flex justify-content-end">
                        <div class="btn-group shadow-sm">
                            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#printVehiculoModal{{ $vehiculo->id }}">
                                <i class="bi bi-printer"></i> Imprimir
                            </button>
                            <a href="{{ url('edit-vehiculo/'.$vehiculo->id) }}" class="btn btn-warning">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $vehiculo->id }}">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Información principal con pestañas -->
                <div class="col-lg-8 col-md-7">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-car-front me-2"></i>{{ $vehiculo->marca }} {{ $vehiculo->modelo }} {{ $vehiculo->ano }}
                            </h5>
                            <span class="badge bg-primary">{{ $vehiculo->ano }}</span>
                        </div>
                        <div class="card-body">
                            <!-- Sistema de pestañas -->
                            <ul class="nav nav-tabs nav-tabs-custom mb-3" id="vehiculoTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-tab-pane"
                                        type="button" role="tab" aria-selected="true">
                                        <i class="bi bi-info-circle me-1"></i> Información
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="ventas-tab" data-bs-toggle="tab" data-bs-target="#ventas-tab-pane"
                                        type="button" role="tab" aria-selected="false">
                                        <i class="bi bi-cart-check me-1"></i> Ventas
                                        @php
                                            $ventasCount = $vehiculo->ventas ? $vehiculo->ventas->where('estado', 1)->count() : 0;
                                        @endphp
                                        <span class="badge rounded-pill bg-success ms-1">{{ $ventasCount }}</span>
                                    </button>
                                </li>
                                {{-- <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="servicios-tab" data-bs-toggle="tab" data-bs-target="#servicios-tab-pane"
                                        type="button" role="tab" aria-selected="false">
                                        <i class="bi bi-tools me-1"></i> Servicios
                                        <span class="badge rounded-pill bg-info ms-1">0</span>
                                    </button>
                                </li> --}}
                            </ul>

                            <!-- Contenido de las pestañas -->
                            <div class="tab-content" id="vehiculoTabContent">
                                <!-- Pestaña de información -->
                                <div class="tab-pane fade show active" id="info-tab-pane" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="vehicle-detail-card">
                                                <h6 class="text-muted mb-1">Marca</h6>
                                                <p class="mb-0 fs-5 fw-bold">{{ $vehiculo->marca }}</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="vehicle-detail-card">
                                                <h6 class="text-muted mb-1">Modelo</h6>
                                                <p class="mb-0 fs-5 fw-bold">{{ $vehiculo->modelo }}</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="vehicle-detail-card">
                                                <h6 class="text-muted mb-1">Color</h6>
                                                <p class="mb-0">
                                                    <span class="badge" style="background-color: #f0f0f0; color: #333;">
                                                        <i class="bi bi-palette-fill me-1"></i>{{ $vehiculo->color }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="vehicle-detail-card">
                                                <h6 class="text-muted mb-1">Placa</h6>
                                                <p class="mb-0">
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-card-text me-1"></i>{{ $vehiculo->placa }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="vehicle-detail-card">
                                                <h6 class="text-muted mb-1">VIN/Número de Chasis</h6>
                                                <p class="mb-0">
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-upc-scan me-1"></i>{{ $vehiculo->vin }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>

                                        @if(isset($vehiculo->tipo_vehiculo) && !empty($vehiculo->tipo_vehiculo))
                                        <div class="col-md-6">
                                            <div class="vehicle-detail-card">
                                                <h6 class="text-muted mb-1">Tipo de Vehículo</h6>
                                                <p class="mb-0">{{ $vehiculo->tipo_vehiculo }}</p>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="col-md-6">
                                            <div class="vehicle-detail-card">
                                                <h6 class="text-muted mb-1">Fecha de Registro</h6>
                                                <p class="mb-0">{{ $vehiculo->created_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="vehicle-detail-card">
                                                <h6 class="text-muted mb-1">Última Actualización</h6>
                                                <p class="mb-0">{{ $vehiculo->updated_at->format('d/m/Y H:i') }}</p>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <hr>
                                            <h5 class="mb-3">Información del Cliente</h5>
                                        </div>

                                        <div class="col-12">
                                            <div class="d-flex align-items-start">
                                                <div class="flex-shrink-0">
                                                    @if ($vehiculo->cliente->fotografia != null)
                                                        <img src="{{ asset('assets/imgs/clientes/'.$vehiculo->cliente->fotografia) }}" class="rounded-circle me-3" style="width: 64px; height: 64px; object-fit: cover;" alt="{{ $vehiculo->cliente->nombre }}" />
                                                    @else
                                                        <img src="{{ asset('assets/imgs/clientes/usericon4.png') }}" class="rounded-circle me-3" style="width: 64px; height: 64px; object-fit: cover;" alt="{{ $vehiculo->cliente->nombre }}" />
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <h5 class="mb-0">
                                                            <a href="{{ url('show-cliente/'.$vehiculo->cliente->id) }}" class="text-primary text-decoration-none">
                                                                {{ $vehiculo->cliente->nombre }}
                                                            </a>
                                                        </h5>
                                                    </div>
                                                    @php
                                                        $fnacimiento = null;
                                                        $edad = 0;
                                                        if ($vehiculo->cliente->fecha_nacimiento != null) {
                                                            $fnacimiento = date("d-m-Y", strtotime($vehiculo->cliente->fecha_nacimiento));
                                                            $cumpleanos = new DateTime($vehiculo->cliente->fecha_nacimiento);
                                                            $hoy = new DateTime();
                                                            $edad = $hoy->diff($cumpleanos)->y;
                                                        }
                                                    @endphp

                                                    <div class="mb-2">
                                                        @if ($edad > 0)
                                                            <span class="badge bg-secondary me-2">{{ $edad }} años</span>
                                                        @endif
                                                        @if ($vehiculo->cliente->dpi)
                                                            <span class="badge bg-light text-dark me-2">DPI: {{ $vehiculo->cliente->dpi }}</span>
                                                        @endif
                                                        @if ($vehiculo->cliente->nit)
                                                            <span class="badge bg-light text-dark">NIT: {{ $vehiculo->cliente->nit }}</span>
                                                        @endif
                                                    </div>

                                                    <div class="mt-3">
                                                        <div class="row g-2">
                                                            <div class="col-auto">
                                                                <a href="mailto:{{ $vehiculo->cliente->email }}" class="btn btn-sm btn-outline-info" title="Correo electrónico">
                                                                    <i class="bi bi-envelope me-1"></i>{{ $vehiculo->cliente->email }}
                                                                </a>
                                                            </div>
                                                            <div class="col-auto">
                                                                <a href="tel:+502{{ $vehiculo->cliente->telefono }}" class="btn btn-sm btn-outline-secondary" title="Llamar al teléfono">
                                                                    <i class="bi bi-telephone me-1"></i>{{ $vehiculo->cliente->telefono }}
                                                                </a>
                                                            </div>
                                                            @if ($vehiculo->cliente->celular != null)
                                                                <div class="col-auto">
                                                                    <a href="https://wa.me/502{{ $vehiculo->cliente->celular }}" target="_blank" class="btn btn-sm btn-outline-success" title="Contactar por WhatsApp">
                                                                        <i class="bi bi-whatsapp me-1"></i>{{ $vehiculo->cliente->celular }}
                                                                    </a>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="mt-3">
                                                        <h6 class="text-muted mb-1">Dirección</h6>
                                                        <p class="mb-0">{{ $vehiculo->cliente->direccion }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pestaña de ventas -->
                                <div class="tab-pane fade" id="ventas-tab-pane" role="tabpanel" aria-labelledby="ventas-tab" tabindex="0">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="mb-0"><i class="bi bi-cart-check me-2"></i>Historial de Ventas</h5>
                                        <a href="{{ url('add-venta?vehiculo_id=' . $vehiculo->id . '&cliente_id=' . $vehiculo->cliente->id) }}" class="btn btn-success btn-sm">
                                            <i class="bi bi-plus-lg"></i> Nueva Venta
                                        </a>
                                    </div>

                                    @if(isset($vehiculo->ventas) && $vehiculo->ventas->where('estado', 1)->count() > 0)
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <h6 class="mb-0">Total de ventas: <span class="badge bg-primary">{{ $vehiculo->ventas->where('estado', 1)->count() }}</span></h6>
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
                                                                <th class="border-0">Cliente</th>
                                                                <th class="border-0 text-center">Estado/Pago</th>
                                                                <th class="border-0 text-end">Total</th>
                                                                <th class="border-0 text-center">Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($vehiculo->ventas->where('estado', 1)->sortByDesc('fecha') as $venta)
                                                                <tr>
                                                                    <td>
                                                                        <div class="fw-medium">{{ $venta->numero_factura ?? 'Sin factura' }}</div>
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
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="flex-shrink-0">
                                                                                @if ($venta->cliente && $venta->cliente->fotografia)
                                                                                    <img src="{{ asset('assets/imgs/clientes/'.$venta->cliente->fotografia) }}" class="rounded-circle" width="32" height="32" alt="Cliente">
                                                                                @else
                                                                                    <img src="{{ asset('assets/imgs/clientes/usericon4.png') }}" class="rounded-circle" width="32" height="32" alt="Cliente">
                                                                                @endif
                                                                            </div>
                                                                            <div class="ms-2">
                                                                                <a href="{{ url('show-cliente/'.$venta->cliente->id) }}" class="text-decoration-none">
                                                                                    {{ $venta->cliente->nombre }}
                                                                                </a>
                                                                            </div>
                                                                        </div>
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
                                                                                        $porcentajePagado = min(100, ($totalPagado / max(1, $totalVenta)) * 100);
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
                                                                        <div class="fw-bold">Q {{ number_format($totalVenta, 2) }}</div>

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
                                                                            <a href="{{ route('ventas.export.single.pdf', $venta->id) }}" target="_blank" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Imprimir">
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
                                        </div>
                                    @else
                                        <div class="text-center py-5">
                                            <div class="mb-3">
                                                <img src="{{ asset('assets/imgs/cart-empty.png') }}" class="opacity-25" width="80" alt="No hay ventas" />
                                            </div>
                                            <h6>No hay ventas registradas para este vehículo</h6>
                                            <p class="text-muted">Registre una nueva venta haciendo clic en el botón "Nueva Venta"</p>
                                            <a href="{{ url('add-venta?vehiculo_id=' . $vehiculo->id . '&cliente_id=' . $vehiculo->cliente->id) }}" class="btn btn-success">
                                                <i class="bi bi-plus-circle"></i> Crear Nueva Venta
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                {{-- <!-- Pestaña de servicios -->
                                <div class="tab-pane fade" id="servicios-tab-pane" role="tabpanel" aria-labelledby="servicios-tab" tabindex="0">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5 class="mb-0"><i class="bi bi-tools me-2"></i>Historial de Servicios</h5>
                                        <a href="{{ url('nuevo-servicio/'.$vehiculo->id) }}" class="btn btn-success btn-sm">
                                            <i class="bi bi-plus-lg"></i> Nuevo Servicio
                                        </a>
                                    </div>
                                    <div class="text-center py-5">
                                        <div class="mb-3">
                                            <i class="bi bi-tools text-secondary" style="font-size: 3rem;"></i>
                                        </div>
                                        <h6>No hay servicios registrados para este vehículo</h6>
                                        <p class="text-muted">Registre un nuevo servicio haciendo clic en el botón "Nuevo Servicio"</p>
                                        <a href="{{ url('nuevo-servicio/'.$vehiculo->id) }}" class="btn btn-success">
                                            <i class="bi bi-plus-circle"></i> Crear Nuevo Servicio
                                        </a>
                                    </div>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fotografía del vehículo y accesos rápidos -->
                <div class="col-lg-4 col-md-5">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-image me-2"></i>Fotografía del Vehículo
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="position-relative">
                                @if ($vehiculo->fotografia != null)
                                    <img src="{{ asset('assets/imgs/vehiculos/'.$vehiculo->fotografia) }}"
                                        class="img-fluid w-100" alt="{{ $vehiculo->marca }} {{ $vehiculo->modelo }}"
                                        style="max-height: 300px; object-fit: cover;">
                                    <a href="{{ asset('assets/imgs/vehiculos/'.$vehiculo->fotografia) }}"
                                       target="_blank"
                                       class="position-absolute bottom-0 end-0 m-2 btn btn-sm btn-light"
                                       title="Ver imagen completa">
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </a>
                                @else
                                    <div class="text-center py-5 bg-light">
                                        <i class="bi bi-image text-secondary" style="font-size: 4rem;"></i>
                                        <p class="mt-2 text-muted">No hay fotografía disponible</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- <!-- Acciones rápidas -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-lightning-charge me-2"></i>Acciones Rápidas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ url('add-venta?vehiculo_id='.$vehiculo->id) }}" class="btn btn-primary">
                                    <i class="bi bi-cart-plus me-2"></i>Nueva Venta
                                </a>
                                <a href="{{ url('nuevo-servicio/'.$vehiculo->id) }}" class="btn btn-success">
                                    <i class="bi bi-tools me-2"></i>Nuevo Servicio
                                </a>
                                <a href="{{ url('edit-vehiculo/'.$vehiculo->id) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil-square me-2"></i>Editar Información
                                </a>
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $vehiculo->id }}">
                                    <i class="bi bi-trash me-2"></i>Eliminar Vehículo
                                </button>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Detalles del registro -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>Detalles del Registro
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                    <span>Fecha de registro:</span>
                                    <span class="badge bg-light text-dark">{{ $vehiculo->created_at->format('d/m/Y H:i') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0">
                                    <span>Última actualización:</span>
                                    <span class="badge bg-light text-dark">{{ $vehiculo->updated_at->format('d/m/Y H:i') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row end -->
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->

    @include('admin.vehiculo.printvehiculomodal')
    @include('admin.vehiculo.deletemodal')

    <style>
        .vehicle-detail-card {
            margin-bottom: 16px;
        }

        .vehicle-detail-card h6 {
            font-size: 0.85rem;
        }

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
