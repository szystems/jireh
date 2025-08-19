@extends('layouts.admin')

@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-boxes"></i>
                </div>
                <div class="page-title">
                    <h5>Detalle de {{ $articulo->tipo == 'articulo' ? 'Artículo' : 'Servicio' }}</h5>
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
            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title">{{ $articulo->nombre }}</h5>
                            <div>
                                <span class="badge bg-{{ $articulo->estado ? 'success' : 'danger' }}">
                                    {{ $articulo->estado ? 'Activo' : 'Inactivo' }}
                                </span>
                                <span class="badge bg-info ms-1">
                                    {{ ucfirst($articulo->tipo) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Sección de Información Básica -->
                            <div class="section mb-4">
                                <h5 class="text-primary mb-3"><i class="bi bi-info-circle"></i> Información Básica</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <strong><i class="bi bi-upc me-2"></i>Código:</strong>
                                                    <span class="float-end">{{ $articulo->codigo ?: 'No definido' }}</span>
                                                </div>
                                                <div class="mb-3">
                                                    <strong><i class="bi bi-tag me-2"></i>Categoría:</strong>
                                                    <span class="badge bg-primary float-end">{{ $articulo->categoria->nombre }}</span>
                                                </div>
                                                <div class="mb-3">
                                                    <strong><i class="bi bi-rulers me-2"></i>Unidad de medida:</strong>
                                                    <span class="badge bg-secondary float-end">{{ $articulo->unidad->nombre }} ({{ $articulo->unidad->abreviatura }})</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <strong><i class="bi bi-card-text me-2"></i>Descripción:</strong>
                                                    <p class="mt-2">{{ $articulo->descripcion ?: 'Sin descripción' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección de Precios y Stock -->
                            <div class="section mb-4">
                                <h5 class="text-primary mb-3"><i class="bi bi-currency-dollar"></i> Precios y Stock</h5>
                                <div class="row">
                                    <!-- Precios -->
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="bi bi-tag-fill"></i> Precios</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <strong>Precio de compra:</strong>
                                                    <span class="float-end">{{ $config->currency_simbol }} {{ number_format($articulo->precio_compra, 2) }}</span>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Precio de venta:</strong>
                                                    <span class="float-end">{{ $config->currency_simbol }} {{ number_format($articulo->precio_venta, 2) }}</span>
                                                </div>
                                                <div class="mb-0">
                                                    <strong>Impuesto ({{ number_format($config->impuesto ?? 0, 2) }}%):</strong>
                                                    <span class="float-end">{{ $config->currency_simbol }} {{ number_format(($articulo->precio_venta * ($config->impuesto ?? 0) / 100), 2) }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        @if (Auth::user()->role_as != 1)
                                        <!-- Análisis de Rentabilidad - Solo Administradores -->
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="bi bi-graph-up"></i> Análisis de Rentabilidad</h6>
                                            </div>
                                            <div class="card-body p-0">
                                                <div class="p-3">
                                                    <table class="table table-sm">
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-start">Precio de venta</td>
                                                                <td class="text-end">{{ $config->currency_simbol }}.{{ number_format($articulo->precio_venta, 2) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start">Precio de compra</td>
                                                                <td class="text-end text-danger">- {{ $config->currency_simbol }}.{{ number_format($articulo->precio_compra, 2) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-start">Impuesto ({{ number_format($config->impuesto ?? 0, 2) }}%)</td>
                                                                <td class="text-end text-danger">- {{ $config->currency_simbol }}.{{ number_format(($articulo->precio_venta * ($config->impuesto ?? 0) / 100), 2) }}</td>
                                                            </tr>

                                                            @php
                                                                $costosComisiones = 0;
                                                                $impuestoValor = $articulo->precio_venta * (($config->impuesto ?? 0) / 100);
                                                            @endphp

                                                            @if($articulo->tipo == 'servicio')
                                                                @php
                                                                    $costosComisiones = ($articulo->costo_mecanico ?? 0) + ($articulo->comision_carwash ?? 0);
                                                                    $gananciaBasica = $articulo->precio_venta - $articulo->precio_compra;
                                                                    $gananciaReal = $gananciaBasica - $impuestoValor - $costosComisiones;

                                                                    // Calcular el margen sobre el costo total
                                                                    $costoTotal = $articulo->precio_compra + $costosComisiones;
                                                                    $margenReal = $costoTotal > 0 ? ($gananciaReal / $costoTotal) * 100 : 0;
                                                                @endphp

                                                                @if($costosComisiones > 0)
                                                                    <tr>
                                                                        <td class="text-start">Comisiones (Mecánico/CarWash)</td>
                                                                        <td class="text-end text-danger">- {{ $config->currency_simbol }}.{{ number_format($costosComisiones, 2) }}</td>
                                                                    </tr>
                                                                @endif
                                                            @else
                                                                @php
                                                                    $gananciaBasica = $articulo->precio_venta - $articulo->precio_compra;
                                                                    $gananciaReal = $gananciaBasica - $impuestoValor;
                                                                    $margenReal = $articulo->precio_compra > 0 ? ($gananciaReal / $articulo->precio_compra) * 100 : 0;
                                                                @endphp
                                                            @endif

                                                            <tr>
                                                                <td class="text-start fw-bold">Ganancia real</td>
                                                                <td class="text-end {{ $gananciaReal >= 0 ? 'text-success' : 'text-danger' }}">
                                                                    {{ $config->currency_simbol }}.{{ number_format($gananciaReal, 2) }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                                        <span class="fw-bold">Margen:</span>
                                                        @php
                                                            $margenClass = 'bg-success';
                                                            if ($margenReal < 10) $margenClass = 'bg-danger';
                                                            elseif ($margenReal < 20) $margenClass = 'bg-warning';
                                                        @endphp
                                                        <span class="badge {{ $margenClass }} fs-6">{{ number_format($margenReal, 2) }}%</span>
                                                    </div>

                                                    <div class="progress mt-1">
                                                        <div class="progress-bar {{ $margenClass }}" role="progressbar"
                                                             style="width: {{ min($margenReal, 100) }}%"
                                                             aria-valuenow="{{ min($margenReal, 100) }}"
                                                             aria-valuemin="0"
                                                             aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <!-- Vista Simplificada para Vendedores -->
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="bi bi-tag"></i> Información de Precio</h6>
                                            </div>
                                            <div class="card-body p-3">
                                                <table class="table table-sm">
                                                    <tbody>
                                                        <tr>
                                                            <td class="text-start">Precio de venta</td>
                                                            <td class="text-end text-success fw-bold">{{ $config->currency_simbol }}.{{ number_format($articulo->precio_venta, 2) }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <small class="text-muted">Información disponible para vendedores</small>
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Stock -->
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0"><i class="bi bi-box-seam"></i> Inventario</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <strong>Stock actual:</strong>
                                                    @php
                                                        $stockClass = 'bg-success';
                                                        if ($articulo->stock <= 0) {
                                                            $stockClass = 'bg-danger';
                                                        } elseif ($articulo->stock <= $articulo->stock_minimo) {
                                                            $stockClass = 'bg-warning text-dark';
                                                        }
                                                    @endphp
                                                    <span class="float-end badge {{ $stockClass }} fs-6">
                                                        {{ number_format($articulo->stock, $articulo->unidad->tipo == 'unidad' ? 0 : 2) }} {{ $articulo->unidad->abreviatura }}
                                                    </span>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Stock mínimo:</strong>
                                                    <span class="float-end">
                                                        {{ number_format($articulo->stock_minimo, $articulo->unidad->tipo == 'unidad' ? 0 : 2) }} {{ $articulo->unidad->abreviatura }}
                                                    </span>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Estado de stock:</strong>
                                                    <span class="float-end">
                                                        @if ($articulo->stock <= 0)
                                                            <span class="badge bg-danger">Agotado</span>
                                                        @elseif ($articulo->stock <= $articulo->stock_minimo)
                                                            <span class="badge bg-warning text-dark">Stock bajo</span>
                                                        @else
                                                            <span class="badge bg-success">Disponible</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección: Comisión para mecánico (solo visible para servicios) -->
                            @if($articulo->tipo == 'servicio')
                            <div class="section mb-4" id="seccion-mecanico">
                                <h5 class="text-primary mb-3"><i class="bi bi-wrench"></i> Asignación y Comisiones</h5>
                                <div class="card">
                                    <div class="card-header bg-info bg-opacity-25">
                                        <h6 class="mb-0"><i class="bi bi-wrench-adjustable"></i> Mecánico y Comisiones</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <strong>Mecánico asignado:</strong>
                                                <p class="mt-2">
                                                    @if($articulo->mecanico)
                                                        <span class="badge bg-info">{{ $articulo->mecanico->nombre_completo }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">No asignado</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <strong>Comisión Mecánico:</strong>
                                                <p class="mt-2">
                                                    <span class="badge bg-success">{{ $config->currency_simbol }} {{ number_format($articulo->costo_mecanico ?? 0, 2) }}</span>
                                                </p>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <strong>Comisión Car Wash:</strong>
                                                <p class="mt-2">
                                                    <span class="badge bg-success">{{ $config->currency_simbol }} {{ number_format($articulo->comision_carwash ?? 0, 2) }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección: Componentes del Servicio (solo visible para servicios) -->
                            <div class="section mb-4" id="seccion-componentes">
                                <h5 class="text-primary mb-3"><i class="bi bi-boxes"></i> Componentes del Servicio</h5>

                                <div class="card mb-4">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0"><i class="bi bi-list-check"></i> Artículos del Servicio</h6>
                                        <span class="badge bg-primary">{{ count($articulo->articulos) }}</span>
                                    </div>
                                    <div class="card-body">
                                        @if($articulo->articulos->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Artículo</th>
                                                            <th>Cantidad</th>
                                                            <th>Precio</th>
                                                            <th>Costo Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php $costoTotal = 0; @endphp
                                                        @foreach($articulo->articulos as $componente)
                                                            @php
                                                                $cantidad = $componente->pivot->cantidad;
                                                                $costoComponente = $componente->precio_compra * $cantidad;
                                                                $costoTotal += $costoComponente;
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div>
                                                                            <span class="d-block fw-bold">{{ $componente->nombre }}</span>
                                                                            <small class="text-muted">{{ $componente->categoria->nombre }}</small>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span>{{ number_format($cantidad, $componente->unidad->tipo == 'unidad' ? 0 : 2) }} {{ $componente->unidad->abreviatura }}</span>
                                                                </td>
                                                                <td>
                                                                    {{ $config->currency_simbol }} {{ number_format($componente->precio_compra, 2) }}
                                                                </td>
                                                                <td>
                                                                    {{ $config->currency_simbol }} {{ number_format($costoComponente, 2) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-light">
                                                            <td class="fw-bold text-end" colspan="3">Costo Total de Componentes:</td>
                                                            <td class="fw-bold">{{ $config->currency_simbol }} {{ number_format($costoTotal, 2) }}</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        @else
                                            <div class="alert alert-info mb-0">
                                                <i class="bi bi-info-circle me-2"></i> Este servicio no tiene componentes registrados.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="d-flex gap-2 justify-content-center mt-4">
                                <a href="{{ url('articulos') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Volver
                                </a>
                                <a href="{{ url('edit-articulo/'.$articulo->id) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil-fill"></i> Editar
                                </a>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $articulo->id }}">
                                    <i class="bi bi-trash-fill"></i> Eliminar
                                </button>
                                <a href="{{ url('export-articulo-pdf/'.$articulo->id) }}" class="btn btn-info" target="_blank">
                                    <i class="bi bi-file-pdf-fill"></i> Exportar PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row end -->
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->

    @include('admin.articulo.deletemodal')
@endsection
