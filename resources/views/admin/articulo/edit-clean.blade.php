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
                    <h5>Editar {{ $articulo->tipo == 'articulo' ? 'Artículo' : 'Servicio' }}</h5>
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
                            <h5>{{ $articulo->nombre }}</h5>
                            <div>
                                <span class="badge {{ $articulo->tipo == 'servicio' ? 'bg-success' : 'bg-primary' }} me-2">
                                    <i class="{{ $articulo->tipo == 'servicio' ? 'bi bi-tools' : 'bi bi-box' }}"></i>
                                    {{ $articulo->tipo == 'servicio' ? 'Servicio' : 'Artículo' }}
                                </span>
                                <span class="badge bg-info">
                                    <i class="bi bi-tag"></i> {{ $articulo->categoria->nombre }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('update-articulo/'.$articulo->id) }}" method="POST" id="form-articulo">
                                @csrf
                                @method('PUT')

                                <!-- Información básica siempre visible -->
                                <div class="section mb-4">
                                    <h5 class="text-primary mb-3"><i class="bi bi-info-circle"></i> Información Básica</h5>
                                    <div class="row gx-3">
                                        <div class="col-md-4 mb-3">
                                            <label for="codigo" class="form-label">Código</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-upc"></i></span>
                                                <input type="text" class="form-control" id="codigo" name="codigo" value="{{ $articulo->codigo }}">
                                            </div>
                                            @if ($errors->has('codigo'))
                                                <div class="invalid-feedback d-block">
                                                    <strong>{{ $errors->first('codigo') }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-8 mb-3">
                                            <label for="nombre" class="form-label">Nombre</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-type"></i></span>
                                                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $articulo->nombre }}" required>
                                            </div>
                                            @if ($errors->has('nombre'))
                                                <div class="invalid-feedback d-block">
                                                    <strong>{{ $errors->first('nombre') }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ $articulo->descripcion }}</textarea>
                                            </div>
                                            @if ($errors->has('descripcion'))
                                                <div class="invalid-feedback d-block">
                                                    <strong>{{ $errors->first('descripcion') }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="categoria_id" class="form-label">Categoría</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-tags"></i></span>
                                                <select class="form-select" id="categoria_id" name="categoria_id" required>
                                                    @foreach($categorias as $categoria)
                                                        <option value="{{ $categoria->id }}" {{ $articulo->categoria_id == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @if ($errors->has('categoria_id'))
                                                <div class="invalid-feedback d-block">
                                                    <strong>{{ $errors->first('categoria_id') }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="tipo" class="form-label">Tipo</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-bookmark"></i></span>
                                                <select class="form-select" id="tipo" name="tipo" required>
                                                    <option value="articulo" {{ $articulo->tipo == 'articulo' ? 'selected' : '' }}>Artículo</option>
                                                    <option value="servicio" {{ $articulo->tipo == 'servicio' ? 'selected' : '' }}>Servicio</option>
                                                </select>
                                            </div>
                                            @if ($errors->has('tipo'))
                                                <div class="invalid-feedback d-block">
                                                    <strong>{{ $errors->first('tipo') }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Precios y Stock siempre visible -->
                                <div class="section mb-4">
                                    <h5 class="text-primary mb-3"><i class="bi bi-currency-dollar"></i> Precios y Stock</h5>
                                    <div class="row gx-3">
                                        <!-- Precios -->
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0"><i class="bi bi-currency-dollar"></i> Precios</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <label for="precio_compra" class="form-label">Precio de Compra</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="bi bi-cash"></i></span>
                                                            <input type="number" step="0.01" class="form-control" id="precio_compra" name="precio_compra" value="{{ $articulo->precio_compra }}" required min="0" onchange="calcularMargen()">
                                                        </div>
                                                        @if ($errors->has('precio_compra'))
                                                            <div class="invalid-feedback d-block">
                                                                <strong>{{ $errors->first('precio_compra') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="precio_venta" class="form-label">Precio de Venta</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                                            <input type="number" step="0.01" class="form-control" id="precio_venta" name="precio_venta" value="{{ $articulo->precio_venta }}" required min="0" onchange="calcularMargen()">
                                                        </div>
                                                        @if ($errors->has('precio_venta'))
                                                            <div class="invalid-feedback d-block">
                                                                <strong>{{ $errors->first('precio_venta') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Información de Impuesto -->
                                                    <input type="hidden" id="impuesto_porcentaje" value="{{ $config->impuesto ?? 0 }}">
                                                    <input type="hidden" id="currency_simbol" value="{{ $config->currency_simbol ?? '$' }}">

                                                    <!-- Tabla de margen de ganancia detallada -->
                                                    <div id="margen-detalle" class="alert alert-info mb-0">
                                                        <h6 class="mb-3">Margen de Ganancia</h6>
                                                        <div class="table-responsive mb-3">
                                                            <table class="table table-sm table-bordered">
                                                                <tbody>
                                                                    <tr>
                                                                        <td width="40%">Precio de Venta</td>
                                                                        <td class="text-end text-success" id="td-precio-venta">{{ $config->currency_simbol ?? '$' }}.{{ number_format($articulo->precio_venta, 2, '.', ',') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>Precio de Compra</td>
                                                                        <td class="text-end text-danger" id="td-precio-compra">- {{ $config->currency_simbol ?? '$' }}.{{ number_format($articulo->precio_compra, 2, '.', ',') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td id="td-label-impuesto">Impuesto ({{ number_format($config->impuesto ?? 0, 2) }}%)</td>
                                                                        <td class="text-end text-danger" id="td-impuesto">- $0.00</td>
                                                                    </tr>
                                                                    <tr class="table-active">
                                                                        <th>Ganancia Real</th>
                                                                        <th class="text-end" id="td-ganancia-real">$0.00</th>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span>Margen Real:</span>
                                                            <span id="margen-valor" class="badge bg-success fs-6">0.00%</span>
                                                        </div>
                                                        <div class="progress" style="height: 10px;">
                                                            <div class="progress-bar" id="margen-barra" role="progressbar" style="width: 0%"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Stock -->
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0"><i class="bi bi-boxes"></i> Inventario</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <label for="unidad_id" class="form-label">Unidad de Medida</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="bi bi-rulers"></i></span>
                                                            <select class="form-select" id="unidad_id" name="unidad_id" required>
                                                                @foreach($unidades as $unidad)
                                                                    <option value="{{ $unidad->id }}"
                                                                            data-tipo="{{ $unidad->tipo }}"
                                                                            {{ $articulo->unidad_id == $unidad->id ? 'selected' : '' }}>
                                                                        {{ $unidad->nombre }} ({{ $unidad->abreviatura }}) - {{ ucfirst($unidad->tipo) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        @if ($errors->has('unidad_id'))
                                                            <div class="invalid-feedback d-block">
                                                                <strong>{{ $errors->first('unidad_id') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="stock" class="form-label">Stock Actual</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="bi bi-boxes"></i></span>
                                                            <input type="number" step="0.01" class="form-control" id="stock" name="stock" value="{{ $articulo->stock }}" required min="0">
                                                        </div>
                                                        @if ($errors->has('stock'))
                                                            <div class="invalid-feedback d-block">
                                                                <strong>{{ $errors->first('stock') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="mb-0">
                                                        <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="bi bi-exclamation-triangle"></i></span>
                                                            <input type="number" step="0.01" class="form-control" id="stock_minimo" name="stock_minimo" value="{{ $articulo->stock_minimo }}" required min="0">
                                                        </div>
                                                        @if ($errors->has('stock_minimo'))
                                                            <div class="invalid-feedback d-block">
                                                                <strong>{{ $errors->first('stock_minimo') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Nueva sección: Comisión para mecánico (solo visible para servicios) -->
                                        <div class="col-md-12 mt-3" id="seccion-mecanico" style="{{ $articulo->tipo == 'servicio' ? '' : 'display: none;' }}">
                                            <div class="card">
                                                <div class="card-header bg-info bg-opacity-25">
                                                    <h6 class="mb-0"><i class="bi bi-wrench"></i> Asignación de Mecánico</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="mecanico_id" class="form-label">Mecánico asignado</label>
                                                            <select class="form-select" id="mecanico_id" name="mecanico_id">
                                                                <option value="">Sin mecánico asignado</option>
                                                                @foreach($mecanicos as $mecanico)
                                                                    <option value="{{ $mecanico->id }}" {{ old('mecanico_id', $articulo->mecanico_id) == $mecanico->id ? 'selected' : '' }}>
                                                                        {{ $mecanico->nombre }} {{ $mecanico->apellido }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <small class="text-muted">Mecánico que ejecutará este servicio y recibirá comisión</small>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label for="costo_mecanico" class="form-label">Costo de mecánico (Q)</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="bi bi-cash"></i></span>
                                                                <input type="number" step="0.01" min="0" class="form-control" id="costo_mecanico" name="costo_mecanico" value="{{ old('costo_mecanico', $articulo->costo_mecanico) ?? 0 }}">
                                                            </div>
                                                            <small class="text-muted">Monto que recibirá el mecánico por realizar este servicio</small>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label for="comision_carwash" class="form-label">Comisión Car Wash (Q)</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="bi bi-cash"></i></span>
                                                                <input type="number" step="0.01" min="0" class="form-control" id="comision_carwash" name="comision_carwash" value="{{ old('comision_carwash', $articulo->comision_carwash) ?? 0 }}">
                                                            </div>
                                                            <small class="text-muted">Monto para comisión de lavadores por cada servicio</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección de Componentes de Servicio (solo para servicios) -->
                                <div class="section mb-4" id="seccion-componentes" style="{{ $articulo->tipo == 'servicio' ? '' : 'display: none;' }}">
                                    <h5 class="text-primary mb-3"><i class="bi bi-boxes"></i> Componentes del Servicio</h5>

                                    <div class="accordion" id="accordionComponentes">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="headingComponentes">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseComponentes" aria-expanded="true" aria-controls="collapseComponentes">
                                                    <i class="bi bi-boxes me-2"></i> Componentes del Servicio
                                                    <span class="badge bg-primary ms-2" id="contador-existentes">{{ $articulo->articulos->count() }}</span>
                                                </button>
                                            </h2>
                                            <div id="collapseComponentes" class="accordion-collapse collapse show" aria-labelledby="headingComponentes">
                                                <div class="accordion-body">
                                                    <!-- Contenido de componentes del servicio -->
                                                    <div class="alert alert-info">
                                                        <i class="bi bi-info-circle"></i> Agregue los artículos que componen este servicio.
                                                    </div>

                                                    <!-- Artículos Existentes -->
                                                    <div class="card mb-4">
                                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                            <h6 class="mb-0"><i class="bi bi-list-check"></i> Artículos Existentes</h6>
                                                            <span class="badge bg-primary" id="contador-existentes">{{ count($articulo->articulos) }}</span>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-hover" id="tabla-servicio-existentes">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Artículo</th>
                                                                            <th width="20%">Cantidad</th>
                                                                            <th width="25%">Costo</th>
                                                                            <th width="10%">Acciones</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="servicio-articulos-existentes-body">
                                                                        @php $costoTotal = 0; @endphp
                                                                        @foreach($articulo->articulos as $articuloServicio)
                                                                            @php
                                                                                $costo = $articuloServicio->precio_compra * $articuloServicio->pivot->cantidad;
                                                                                $costoTotal += $costo;
                                                                                $tipoUnidadArticulo = $articuloServicio->unidad->tipo ?? 'decimal';
                                                                            @endphp
                                                                            <tr data-precio="{{ $articuloServicio->precio_compra }}" data-tipo-unidad="{{ $tipoUnidadArticulo }}">
                                                                                <td>
                                                                                    <div class="d-flex align-items-center">
                                                                                        <div>
                                                                                            <span class="d-block fw-bold">{{ $articuloServicio->nombre }}</span>
                                                                                            <small class="text-muted">{{ $articuloServicio->codigo ?? 'Sin código' }}</small>
                                                                                        </div>
                                                                                    </div>
                                                                                    <input type="hidden" name="articulos_servicio_existentes[{{ $loop->index }}][id]" value="{{ $articuloServicio->id }}">
                                                                                </td>
                                                                                <td>
                                                                                    <div class="input-group input-group-sm">
                                                                                        <input type="number"
                                                                                               step="{{ $tipoUnidadArticulo === 'unidad' ? '1' : '0.01' }}"
                                                                                               class="form-control cantidad-articulo"
                                                                                               name="articulos_servicio_existentes[{{ $loop->index }}][cantidad]"
                                                                                               value="{{ $tipoUnidadArticulo === 'unidad' ? floor($articuloServicio->pivot->cantidad) : $articuloServicio->pivot->cantidad }}"
                                                                                               min="{{ $tipoUnidadArticulo === 'unidad' ? '1' : '0.01' }}"
                                                                                               data-tipo-unidad="{{ $tipoUnidadArticulo }}"
                                                                                               required>
                                                                                        <span class="input-group-text">{{ $articuloServicio->unidad->abreviatura }}</span>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <div class="input-group input-group-sm">
                                                                                        <span class="input-group-text">{{ $config->currency_simbol }}.</span>
                                                                                        <input type="text" class="form-control costo-articulo" value="{{ number_format($costo, 2) }}" readonly>
                                                                                    </div>
                                                                                </td>
                                                                                <td>
                                                                                    <button type="button" class="btn btn-danger btn-sm remove-articulo-servicio">
                                                                                        <i class="bi bi-trash"></i>
                                                                                    </button>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                    <tfoot>
                                                                        <tr class="table-light">
                                                                            <th colspan="2" class="text-end">Costo Total:</th>
                                                                            <th>
                                                                                <div class="input-group input-group-sm">
                                                                                    <span class="input-group-text">{{ $config->currency_simbol }}.</span>
                                                                                    <input type="text" id="costo-total" class="form-control" value="{{ number_format($costoTotal, 2) }}" readonly>
                                                                                </div>
                                                                            </th>
                                                                            <th></th>
                                                                        </tr>
                                                                    </tfoot>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Agregar Nuevos Artículos -->
                                                    <div class="card">
                                                        <div class="card-header bg-light">
                                                            <h6 class="mb0"><i class="bi bi-plus-circle"></i> Agregar Nuevos Artículos</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row mb-3">
                                                                <div class="col-md-6">
                                                                    <label for="nuevo-articulo" class="form-label">Buscar Artículo</label>
                                                                    <select class="form-select" id="nuevo-articulo">
                                                                        <option value="">Seleccione un artículo</option>
                                                                        @foreach($todosArticulos as $art)
                                                                            <option value="{{ $art->id }}" data-precio="{{ $art->precio_compra }}" data-unidad="{{ $art->unidad->abreviatura }}">
                                                                                {{ $art->codigo ? $art->codigo . ' - ' : '' }}{{ $art->nombre }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <label for="nuevo-cantidad" class="form-label">Cantidad</label>
                                                                    <input type="number" step="0.01" class="form-control" id="nuevo-cantidad" min="0.01" value="1">
                                                                </div>
                                                                <div class="col-md-3 d-flex align-items-end">
                                                                    <button type="button" class="btn btn-primary w-100" id="add-articulo-servicio">
                                                                        <i class="bi bi-plus-lg"></i> Agregar
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-hover" id="tabla-servicio-nuevos">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Artículo</th>
                                                                            <th width="20%">Cantidad</th>
                                                                            <th width="25%">Costo</th>
                                                                            <th width="10%">Acciones</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="servicio-articulos-nuevos-body"></tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 d-flex justify-content-between">
                                        <a href="{{ url('articulos') }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-arrow-left"></i> Volver
                                        </a>
                                        <div>
                                            <a href="{{ url('articulos') }}" class="btn btn-danger me-2">
                                                <i class="bi bi-x-circle"></i> Cancelar
                                            </a>
                                            <button type="submit" class="btn btn-success" id="btn-guardar">
                                                <i class="bi bi-check2-circle"></i> Guardar Cambios
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row end -->
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->

<script src="{{ asset('js/articulo-script.js') }}"></script>
@endsection
