@extends('layouts.admin')
@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="{{ $articulo->tipo == 'servicio' ? 'bi bi-tools' : 'bi bi-box' }}"></i>
                </div>
                <div class="page-title">
                    <h5>{{ $articulo->tipo == 'servicio' ? 'Detalle de Servicio' : 'Detalle de Artículo' }}</h5>
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

            <!-- Encabezado con acciones -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1">{{ $articulo->nombre }}</h4>
                                <div class="d-flex">
                                    <span class="badge {{ $articulo->tipo == 'servicio' ? 'bg-success' : 'bg-primary' }} me-2">
                                        <i class="{{ $articulo->tipo == 'servicio' ? 'bi bi-tools' : 'bi bi-box' }}"></i>
                                        {{ $articulo->tipo == 'servicio' ? 'Servicio' : 'Artículo' }}
                                    </span>
                                    <span class="badge bg-info me-2">
                                        <i class="bi bi-tag"></i> {{ $articulo->categoria->nombre }}
                                    </span>
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-calendar-check"></i> Creado: {{ $articulo->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                            <div class="btn-group">
                                <a href="{{ url('articulos') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Volver
                                </a>
                                <a href="{{ url('export-articulo-pdf/'.$articulo->id) }}" class="btn btn-outline-danger" target="_blank">
                                    <i class="bi bi-file-pdf"></i> PDF
                                </a>
                                <a href="{{ url('edit-articulo/'.$articulo->id) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $articulo->id }}">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                                @include('admin.articulo.deletemodal')
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navegación de pestañas -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-4" id="articuloTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-tab-pane" type="button" role="tab">
                                        <i class="bi bi-info-circle"></i> Información Básica
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="precios-tab" data-bs-toggle="tab" data-bs-target="#precios-tab-pane" type="button" role="tab">
                                        <i class="bi bi-currency-dollar"></i> Precios y Stock
                                    </button>
                                </li>
                                @if($articulo->tipo == 'servicio')
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="componentes-tab" data-bs-toggle="tab" data-bs-target="#componentes-tab-pane" type="button" role="tab">
                                        <i class="bi bi-boxes"></i> Componentes del Servicio
                                    </button>
                                </li>
                                @endif
                            </ul>

                            <!-- Contenido de pestañas -->
                            <div class="tab-content" id="articuloTabContent">
                                <!-- Pestaña de Información Básica -->
                                <div class="tab-pane fade show active" id="info-tab-pane" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0"><i class="bi bi-card-text"></i> Detalles Generales</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3 row">
                                                        <label class="col-sm-4 col-form-label fw-bold">Código:</label>
                                                        <div class="col-sm-8">
                                                            <p class="form-control-plaintext">
                                                                @if($articulo->codigo)
                                                                    <span class="badge bg-dark">{{ $articulo->codigo }}</span>
                                                                @else
                                                                    <span class="text-muted"><i>No asignado</i></span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <label class="col-sm-4 col-form-label fw-bold">Nombre:</label>
                                                        <div class="col-sm-8">
                                                            <p class="form-control-plaintext">{{ $articulo->nombre }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <label class="col-sm-4 col-form-label fw-bold">Tipo:</label>
                                                        <div class="col-sm-8">
                                                            <p class="form-control-plaintext">
                                                                <span class="badge {{ $articulo->tipo == 'servicio' ? 'bg-success' : 'bg-primary' }}">
                                                                    {{ $articulo->tipo == 'servicio' ? 'Servicio' : 'Artículo' }}
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <label class="col-sm-4 col-form-label fw-bold">Categoría:</label>
                                                        <div class="col-sm-8">
                                                            <p class="form-control-plaintext">
                                                                <a href="{{ url('show-categoria/'.$articulo->categoria_id) }}" class="text-primary">
                                                                    {{ $articulo->categoria->nombre }}
                                                                </a>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <label class="col-sm-4 col-form-label fw-bold">Unidad de Medida:</label>
                                                        <div class="col-sm-8">
                                                            <p class="form-control-plaintext">
                                                                <a href="{{ url('show-unidad/'.$articulo->unidad_id) }}" class="text-primary">
                                                                    {{ $articulo->unidad->nombre }} ({{ $articulo->unidad->abreviatura }})
                                                                </a>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0"><i class="bi bi-file-earmark-text"></i> Descripción</h6>
                                                </div>
                                                <div class="card-body">
                                                    <p class="mb-0">
                                                        @if($articulo->descripcion)
                                                            {{ $articulo->descripcion }}
                                                        @else
                                                            <span class="text-muted"><i>Sin descripción</i></span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="card">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0"><i class="bi bi-clock-history"></i> Metadatos</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-2 row">
                                                        <label class="col-sm-4 col-form-label fw-bold">Creado:</label>
                                                        <div class="col-sm-8">
                                                            <p class="form-control-plaintext">{{ $articulo->created_at->format('d/m/Y H:i:s') }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <label class="col-sm-4 col-form-label fw-bold">Actualizado:</label>
                                                        <div class="col-sm-8">
                                                            <p class="form-control-plaintext">{{ $articulo->updated_at->format('d/m/Y H:i:s') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pestaña de Precios y Stock -->
                                <div class="tab-pane fade" id="precios-tab-pane" role="tabpanel" aria-labelledby="precios-tab" tabindex="0">
                                    <div class="row">
                                        <!-- Precios -->
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0"><i class="bi bi-currency-dollar"></i> Información de Precios</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <div class="card bg-light">
                                                                <div class="card-body text-center">
                                                                    <h6 class="text-muted mb-1">Precio de Compra</h6>
                                                                    <h3 class="text-danger mb-0">{{ $config->currency_simbol }}.{{ number_format($articulo->precio_compra, 2, '.', ',') }}</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="card bg-light">
                                                                <div class="card-body text-center">
                                                                    <h6 class="text-muted mb-1">Precio de Venta</h6>
                                                                    <h3 class="text-success mb-0">{{ $config->currency_simbol }}.{{ number_format($articulo->precio_venta, 2, '.', ',') }}</h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="card mb-3">
                                                        <div class="card-body">
                                                            @php
                                                                // Obtener porcentajes de comisión e impuesto
                                                                $comisionVendedor = $articulo->tipoComisionVendedor->porcentaje ?? 0;
                                                                $comisionTrabajador = $articulo->tipoComisionTrabajador->porcentaje ?? 0;
                                                                $impuesto = $config->impuesto ?? 0;

                                                                // Cálculo de costos adicionales
                                                                $valorComisionVendedor = $articulo->precio_venta * ($comisionVendedor / 100);
                                                                $valorComisionTrabajador = $articulo->precio_venta * ($comisionTrabajador / 100);
                                                                $valorImpuesto = $articulo->precio_venta * ($impuesto / 100);

                                                                // Cálculo del margen real
                                                                $ganancia = $articulo->precio_venta - $articulo->precio_compra;
                                                                $gananciaReal = $ganancia - $valorComisionVendedor - $valorComisionTrabajador - $valorImpuesto;

                                                                $margen = $articulo->precio_compra > 0 ?
                                                                    (($gananciaReal) / $articulo->precio_compra) * 100 : 0;

                                                                $margenClass = 'bg-success';
                                                                if ($margen < 10) {
                                                                    $margenClass = 'bg-danger';
                                                                } elseif ($margen < 20) {
                                                                    $margenClass = 'bg-warning';
                                                                }
                                                            @endphp

                                                            <h6 class="mb-3">Margen de Ganancia (incluyendo comisiones e impuestos)</h6>
                                                            <div class="table-responsive mb-3">
                                                                <table class="table table-sm table-bordered">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td width="40%">Precio de Venta</td>
                                                                            <td class="text-end text-success">{{ $config->currency_simbol }}.{{ number_format($articulo->precio_venta, 2, '.', ',') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Precio de Compra</td>
                                                                            <td class="text-end text-danger">- {{ $config->currency_simbol }}.{{ number_format($articulo->precio_compra, 2, '.', ',') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Comisión Vendedor ({{ number_format($comisionVendedor, 2) }}%)</td>
                                                                            <td class="text-end text-danger">- {{ $config->currency_simbol }}.{{ number_format($valorComisionVendedor, 2, '.', ',') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Comisión Trabajador ({{ number_format($comisionTrabajador, 2) }}%)</td>
                                                                            <td class="text-end text-danger">- {{ $config->currency_simbol }}.{{ number_format($valorComisionTrabajador, 2, '.', ',') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Impuesto ({{ number_format($impuesto, 2) }}%)</td>
                                                                            <td class="text-end text-danger">- {{ $config->currency_simbol }}.{{ number_format($valorImpuesto, 2, '.', ',') }}</td>
                                                                        </tr>
                                                                        <tr class="table-active">
                                                                            <th>Ganancia Real</th>
                                                                            <th class="text-end {{ $gananciaReal > 0 ? 'text-success' : 'text-danger' }}">
                                                                                {{ $config->currency_simbol }}.{{ number_format($gananciaReal, 2, '.', ',') }}
                                                                            </th>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <span>Margen Real:</span>
                                                                <span class="badge fs-6 {{ $margenClass }}">{{ number_format($margen, 2) }}%</span>
                                                            </div>
                                                            <div class="progress" style="height: 10px;">
                                                                <div class="progress-bar {{ $margenClass }}" role="progressbar" style="width: {{ min($margen, 100) }}%"></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="card">
                                                        <div class="card-body">
                                                            <h6 class="mb-3">Información de Comisiones</h6>
                                                            <div class="row">
                                                                <div class="col-md-6 mb-2">
                                                                    <span class="d-block text-muted small">Comisión para Vendedor</span>
                                                                    <span class="badge bg-info">{{ $articulo->tipoComisionVendedor->nombre }} ({{ number_format($comisionVendedor, 2) }}%)</span>
                                                                </div>
                                                                <div class="col-md-6 mb-2">
                                                                    <span class="d-block text-muted small">Comisión para Trabajador</span>
                                                                    <span class="badge bg-info">{{ $articulo->tipoComisionTrabajador->nombre }} ({{ number_format($comisionTrabajador, 2) }}%)</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Stock -->
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0"><i class="bi bi-boxes"></i> Información de Inventario</h6>
                                                </div>
                                                <div class="card-body">
                                                    {{-- @if($articulo->tipo == 'articulo') --}}
                                                        <div class="card mb-3">
                                                            <div class="card-body text-center py-4">
                                                                <h6 class="text-muted mb-2">Stock Actual</h6>
                                                                @if($articulo->stock <= 0)
                                                                    <h2 class="text-danger mb-0">{{ $articulo->stock }} <small>{{ $articulo->unidad->abreviatura }}</small></h2>
                                                                    <span class="badge bg-danger mt-2">Sin Stock</span>
                                                                @elseif($articulo->stock <= $articulo->stock_minimo)
                                                                    <h2 class="text-warning mb-0">{{ $articulo->stock }} <small>{{ $articulo->unidad->abreviatura }}</small></h2>
                                                                    <span class="badge bg-warning text-dark mt-2">Stock Bajo</span>
                                                                @else
                                                                    <h2 class="text-success mb-0">{{ $articulo->stock }} <small>{{ $articulo->unidad->abreviatura }}</small></h2>
                                                                    <span class="badge bg-success mt-2">Stock Disponible</span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <div class="card bg-light">
                                                                    <div class="card-body text-center">
                                                                        <h6 class="text-muted mb-1">Stock Mínimo</h6>
                                                                        <h3 class="mb-0">{{ $articulo->stock_minimo }} <small>{{ $articulo->unidad->abreviatura }}</small></h3>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="card bg-light">
                                                                    <div class="card-body text-center">
                                                                        <h6 class="text-muted mb-1">Diferencia</h6>
                                                                        @php $diferencia = $articulo->stock - $articulo->stock_minimo; @endphp
                                                                        <h3 class="mb-0 {{ $diferencia < 0 ? 'text-danger' : 'text-success' }}">
                                                                            {{ $diferencia }} <small>{{ $articulo->unidad->abreviatura }}</small>
                                                                        </h3>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="card bg-light">
                                                            <div class="card-body">
                                                                <h6 class="mb-2">Estado de Inventario</h6>
                                                                @php
                                                                    $porcentaje = $articulo->stock_minimo > 0 ?
                                                                        min(($articulo->stock / $articulo->stock_minimo) * 100, 200) : 100;

                                                                    $stockClass = 'bg-success';
                                                                    if ($porcentaje <= 0) {
                                                                        $stockClass = 'bg-danger';
                                                                    } elseif ($porcentaje < 100) {
                                                                        $stockClass = 'bg-warning';
                                                                    }
                                                                @endphp
                                                                <div class="progress" style="height: 20px;">
                                                                    <div class="progress-bar {{ $stockClass }}" role="progressbar"
                                                                        style="width: {{ $porcentaje }}%"
                                                                        aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="200">
                                                                        {{ number_format($porcentaje, 0) }}% del mínimo requerido
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    {{-- @else
                                                        <div class="alert alert-info">
                                                            <i class="bi bi-info-circle"></i>
                                                            Este elemento es un servicio y no maneja stock directamente.
                                                        </div>
                                                    @endif --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pestaña de Componentes del Servicio (solo para servicios) -->
                                @if($articulo->tipo == 'servicio')
                                <div class="tab-pane fade" id="componentes-tab-pane" role="tabpanel" aria-labelledby="componentes-tab" tabindex="0">
                                    @php
                                        $totalCosto = 0;

                                        // Reutilizamos las variables de comisiones e impuestos definidas en la otra pestaña
                                        $comisionVendedor = $articulo->tipoComisionVendedor->porcentaje ?? 0;
                                        $comisionTrabajador = $articulo->tipoComisionTrabajador->porcentaje ?? 0;
                                        $impuesto = $config->impuesto ?? 0;

                                        $valorComisionVendedor = $articulo->precio_venta * ($comisionVendedor / 100);
                                        $valorComisionTrabajador = $articulo->precio_venta * ($comisionTrabajador / 100);
                                        $valorImpuesto = $articulo->precio_venta * ($impuesto / 100);
                                    @endphp

                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0"><i class="bi bi-list-check"></i> Artículos que componen el servicio</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Artículo</th>
                                                            <th class="text-center" width="15%">Cantidad</th>
                                                            <th class="text-center" width="15%">Precio Unitario</th>
                                                            <th class="text-end" width="15%">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($articulo->articulos as $articuloServicio)
                                                            @php
                                                                $costo = $articuloServicio->precio_compra * $articuloServicio->pivot->cantidad;
                                                                $totalCosto += $costo;
                                                            @endphp
                                                            <tr>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <div>
                                                                            <a href="{{ url('show-articulo', $articuloServicio->id) }}" class="text-primary fw-bold mb-1 d-block">
                                                                                {{ $articuloServicio->nombre }}
                                                                            </a>
                                                                            <small class="text-muted">
                                                                                @if($articuloServicio->codigo)
                                                                                    Código: <span class="badge bg-dark">{{ $articuloServicio->codigo }}</span>
                                                                                @else
                                                                                    <span class="text-muted"><i>Sin código</i></span>
                                                                                @endif
                                                                                | Categoría: {{ $articuloServicio->categoria->nombre }}
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center align-middle">
                                                                    <span class="badge bg-secondary">
                                                                        {{ number_format($articuloServicio->pivot->cantidad, 2) }} {{ $articuloServicio->unidad->abreviatura }}
                                                                    </span>
                                                                </td>
                                                                <td class="text-center align-middle">
                                                                    {{ $config->currency_simbol }}.{{ number_format($articuloServicio->precio_compra, 2, '.', ',') }}
                                                                </td>
                                                                <td class="text-end align-middle fw-bold">
                                                                    {{ $config->currency_simbol }}.{{ number_format($costo, 2, '.', ',') }}
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center">Este servicio no tiene componentes registrados.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                    <tfoot class="table-group-divider">
                                                        <tr class="table-light">
                                                            <th colspan="3" class="text-end">Total Costo:</th>
                                                            <th class="text-end">{{ $config->currency_simbol }}.{{ number_format($totalCosto, 2, '.', ',') }}</th>
                                                        </tr>
                                                        <tr class="table-light">
                                                            <th colspan="3" class="text-end">Precio de Venta:</th>
                                                            <th class="text-end text-success">{{ $config->currency_simbol }}.{{ number_format($articulo->precio_venta, 2, '.', ',') }}</th>
                                                        </tr>
                                                        <tr class="table-secondary">
                                                            <th colspan="3" class="text-end">Comisión Vendedor ({{ number_format($comisionVendedor, 2) }}%):</th>
                                                            <th class="text-end text-danger">- {{ $config->currency_simbol }}.{{ number_format($valorComisionVendedor, 2, '.', ',') }}</th>
                                                        </tr>
                                                        <tr class="table-secondary">
                                                            <th colspan="3" class="text-end">Comisión Trabajador ({{ number_format($comisionTrabajador, 2) }}%):</th>
                                                            <th class="text-end text-danger">- {{ $config->currency_simbol }}.{{ number_format($valorComisionTrabajador, 2, '.', ',') }}</th>
                                                        </tr>
                                                        <tr class="table-secondary">
                                                            <th colspan="3" class="text-end">Impuesto ({{ number_format($impuesto, 2) }}%):</th>
                                                            <th class="text-end text-danger">- {{ $config->currency_simbol }}.{{ number_format($valorImpuesto, 2, '.', ',') }}</th>
                                                        </tr>
                                                        @php
                                                            $gananciaReal = $articulo->precio_venta - $totalCosto - $valorComisionVendedor - $valorComisionTrabajador - $valorImpuesto;
                                                            $porcentajeGananciaReal = $totalCosto > 0 ? (($gananciaReal / $totalCosto) * 100) : 0;
                                                        @endphp
                                                        <tr class="table-active">
                                                            <th colspan="3" class="text-end">Ganancia Real:</th>
                                                            <th class="text-end {{ $gananciaReal > 0 ? 'text-success' : 'text-danger' }}">
                                                                {{ $config->currency_simbol }}.{{ number_format($gananciaReal, 2, '.', ',') }}
                                                                @if($totalCosto > 0)
                                                                    ({{ number_format($porcentajeGananciaReal, 2) }}%)
                                                                @endif
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->
@endsection
