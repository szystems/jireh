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
                    <h5>Nuevo Artículo o Servicio</h5>
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
                        <div class="card-header">
                            <h5>Formulario de Nuevo Artículo</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('insert-articulo') }}" method="POST" enctype="multipart/form-data" id="form-articulo">
                                @csrf

                                <!-- Navegación de pestañas -->
                                <ul class="nav nav-tabs mb-4" id="articuloTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-tab-pane" type="button" role="tab" aria-controls="info-tab-pane" aria-selected="true">
                                            <i class="bi bi-info-circle"></i> Información Básica
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="precios-tab" data-bs-toggle="tab" data-bs-target="#precios-tab-pane" type="button" role="tab" aria-controls="precios-tab-pane" aria-selected="false">
                                            <i class="bi bi-currency-dollar"></i> Precios y Stock
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link d-none" id="componentes-tab" data-bs-toggle="tab" data-bs-target="#componentes-tab-pane" type="button" role="tab" aria-controls="componentes-tab-pane" aria-selected="false">
                                            <i class="bi bi-boxes"></i> Componentes del Servicio
                                        </button>
                                    </li>
                                </ul>

                                <!-- Contenido de pestañas -->
                                <div class="tab-content" id="articuloTabContent">
                                    <!-- Pestaña de Información Básica -->
                                    <div class="tab-pane fade show active" id="info-tab-pane" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
                                        <div class="row gx-3">
                                            <div class="col-md-4 mb-3">
                                                <label for="codigo" class="form-label">Código</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-upc"></i></span>
                                                    <input type="text" class="form-control" id="codigo" name="codigo" value="{{ old('codigo') }}">
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
                                                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
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
                                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
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
                                                        <option value="">Seleccione una categoría</option>
                                                        @foreach($categorias as $categoria)
                                                            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
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
                                                        <option value="articulo" {{ old('tipo') == 'articulo' ? 'selected' : '' }}>Artículo</option>
                                                        <option value="servicio" {{ old('tipo') == 'servicio' ? 'selected' : '' }}>Servicio</option>
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

                                    <!-- Pestaña de Precios y Stock -->
                                    <div class="tab-pane fade" id="precios-tab-pane" role="tabpanel" aria-labelledby="precios-tab" tabindex="0">
                                        <div class="row gx-3">
                                            <!-- Precios y comisiones -->
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
                                                                <input type="number" step="0.01" class="form-control" id="precio_compra" name="precio_compra" value="{{ old('precio_compra') ?? 0 }}" required min="0" onchange="calcularMargen()">
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
                                                                <input type="number" step="0.01" class="form-control" id="precio_venta" name="precio_venta" value="{{ old('precio_venta') ?? 0 }}" required min="0" onchange="calcularMargen()">
                                                            </div>
                                                            @if ($errors->has('precio_venta'))
                                                                <div class="invalid-feedback d-block">
                                                                    <strong>{{ $errors->first('precio_venta') }}</strong>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Tabla de margen de ganancia detallada -->
                                                        <div id="margen-detalle" class="alert alert-info mb-3">
                                                            <h6 class="mb-3">Margen de Ganancia (incluyendo comisiones e impuestos)</h6>
                                                            <div class="table-responsive mb-3">
                                                                <table class="table table-sm table-bordered">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td width="40%">Precio de Venta</td>
                                                                            <td class="text-end text-success" id="td-precio-venta">{{ $config->currency_simbol ?? '$' }}.0.00</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>Precio de Compra</td>
                                                                            <td class="text-end text-danger" id="td-precio-compra">- {{ $config->currency_simbol ?? '$' }}.0.00</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td id="td-label-comision-vendedor">Comisión Vendedor (0.00%)</td>
                                                                            <td class="text-end text-danger" id="td-comision-vendedor">- $0.00</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td id="td-label-comision-trabajador">Comisión Trabajador (0.00%)</td>
                                                                            <td class="text-end text-danger" id="td-comision-trabajador">- $0.00</td>
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

                                                        <div class="mb-3">
                                                            <label for="tipo_comision_vendedor_id" class="form-label">Comisión para Vendedor</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                                                <select class="form-select" id="tipo_comision_vendedor_id" name="tipo_comision_vendedor_id" required onchange="calcularMargen()">
                                                                    <option value="">Seleccione</option>
                                                                    @foreach($tipoComisiones as $tipoComision)
                                                                        <option value="{{ $tipoComision->id }}" data-porcentaje="{{ $tipoComision->porcentaje }}" {{ old('tipo_comision_vendedor_id') == $tipoComision->id ? 'selected' : '' }}>
                                                                            {{ $tipoComision->nombre }} ({{ number_format($tipoComision->porcentaje, 2) }}%)
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            @if ($errors->has('tipo_comision_vendedor_id'))
                                                                <div class="invalid-feedback d-block">
                                                                    <strong>{{ $errors->first('tipo_comision_vendedor_id') }}</strong>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="mb-0">
                                                            <label for="tipo_comision_trabajador_id" class="form-label">Comisión para Trabajador</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="bi bi-person-gear"></i></span>
                                                                <select class="form-select" id="tipo_comision_trabajador_id" name="tipo_comision_trabajador_id" required onchange="calcularMargen()">
                                                                    <option value="">Seleccione</option>
                                                                    @foreach($tipoComisiones as $tipoComision)
                                                                        <option value="{{ $tipoComision->id }}" data-porcentaje="{{ $tipoComision->porcentaje }}" {{ old('tipo_comision_trabajador_id') == $tipoComision->id ? 'selected' : '' }}>
                                                                            {{ $tipoComision->nombre }} ({{ number_format($tipoComision->porcentaje, 2) }}%)
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            @if ($errors->has('tipo_comision_trabajador_id'))
                                                                <div class="invalid-feedback d-block">
                                                                    <strong>{{ $errors->first('tipo_comision_trabajador_id') }}</strong>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Información de Impuesto -->
                                                        <input type="hidden" id="impuesto_porcentaje" value="{{ $config->impuesto ?? 0 }}">
                                                        <input type="hidden" id="currency_simbol" value="{{ $config->currency_simbol ?? '$' }}">
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
                                                                    <option value="">Seleccione</option>
                                                                    @foreach($unidades as $unidad)
                                                                        <option value="{{ $unidad->id }}"
                                                                                data-tipo="{{ $unidad->tipo }}"
                                                                                {{ old('unidad_id') == $unidad->id ? 'selected' : '' }}>
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
                                                            <label for="stock" class="form-label">Stock Inicial</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text"><i class="bi bi-boxes"></i></span>
                                                                <input type="number" step="0.01" class="form-control" id="stock" name="stock" value="{{ old('stock') ?? 0 }}" required min="0">
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
                                                                <input type="number" step="0.01" class="form-control" id="stock_minimo" name="stock_minimo" value="{{ old('stock_minimo') ?? 0 }}" required min="0">
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
                                        </div>
                                    </div>

                                    <!-- Pestaña de Componentes del Servicio -->
                                    <div class="tab-pane fade" id="componentes-tab-pane" role="tabpanel" aria-labelledby="componentes-tab" tabindex="0">
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle"></i> Agregue los artículos que componen este servicio.
                                        </div>

                                        <!-- Artículos para el servicio -->
                                        <div class="card mb-4">
                                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0"><i class="bi bi-list-check"></i> Artículos del Servicio</h6>
                                                <span class="badge bg-primary" id="contador-articulos">0</span>
                                            </div>
                                            <div class="card-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <label for="articulo_servicio" class="form-label">Buscar Artículo</label>
                                                        <select class="form-select" id="articulo_servicio">
                                                            <option value="">Seleccione un artículo</option>
                                                            @foreach($articulos as $articulo)
                                                                <option value="{{ $articulo->id }}" data-precio="{{ $articulo->precio_compra }}" data-unidad="{{ $articulo->unidad->abreviatura }}">
                                                                    {{ $articulo->codigo ? $articulo->codigo . ' - ' : '' }}{{ $articulo->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('articulos_servicio'))
                                                            <div class="invalid-feedback d-block">
                                                                <strong>{{ $errors->first('articulos_servicio') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="cantidad_servicio" class="form-label">Cantidad</label>
                                                        <input type="number" step="0.01" class="form-control" id="cantidad_servicio" min="0.01" value="1">
                                                    </div>
                                                    <div class="col-md-3 d-flex align-items-end">
                                                        <button type="button" class="btn btn-primary w-100" id="add-articulo-servicio">
                                                            <i class="bi bi-plus-lg"></i> Agregar
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover" id="tabla-servicio">
                                                        <thead>
                                                            <tr>
                                                                <th>Artículo</th>
                                                                <th width="20%">Cantidad</th>
                                                                <th width="25%">Costo</th>
                                                                <th width="10%">Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="servicio-articulos-body">
                                                            @if(old('articulos_servicio'))
                                                                @foreach(old('articulos_servicio') as $articuloId => $cantidad)
                                                                    @php
                                                                        $articuloObj = $articulos->find($articuloId);
                                                                        $costo = $articuloObj->precio_compra * $cantidad;
                                                                    @endphp
                                                                    <tr data-precio="{{ $articuloObj->precio_compra }}">
                                                                        <td>
                                                                            <div class="d-flex align-items-center">
                                                                                <div>
                                                                                    <span class="d-block fw-bold">{{ $articuloObj->nombre }}</span>
                                                                                    <small class="text-muted">{{ $articuloObj->codigo ?? 'Sin código' }}</small>
                                                                                </div>
                                                                            </div>
                                                                            <input type="hidden" name="articulos_servicio[{{ $articuloId }}]" value="{{ $cantidad }}">
                                                                        </td>
                                                                        <td>
                                                                            <div class="input-group input-group-sm">
                                                                                <input type="number" step="0.01" class="form-control cantidad-articulo" value="{{ $cantidad }}" min="0.01" required onchange="actualizarCantidad(this, {{ $articuloId }})">
                                                                                <span class="input-group-text">{{ $articuloObj->unidad->abreviatura }}</span>
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
                                                            @endif
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="table-light">
                                                                <th colspan="2" class="text-end">Costo Total:</th>
                                                                <th>
                                                                    <div class="input-group input-group-sm">
                                                                        <span class="input-group-text">{{ $config->currency_simbol }}.</span>
                                                                        <input type="text" id="costo-total" class="form-control" value="0.00" readonly>
                                                                    </div>
                                                                </th>
                                                                <th></th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
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
                                                <i class="bi bi-check2-circle"></i> Crear Artículo
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Referencias a elementos principales
    const tipoSelect = document.querySelector('#tipo');
    const componentesTab = document.querySelector('#componentes-tab');
    const btnGuardar = document.querySelector('#btn-guardar');
    const precioCompraInput = document.querySelector('#precio_compra');
    const precioVentaInput = document.querySelector('#precio_venta');

    // Referencias para artículos de servicio
    const articuloServicioSelect = document.querySelector('#articulo_servicio');
    const cantidadServicioInput = document.querySelector('#cantidad_servicio');
    const addArticuloBtn = document.querySelector('#add-articulo-servicio');
    const servicioArticulosBody = document.querySelector('#servicio-articulos-body');
    const contadorArticulos = document.querySelector('#contador-articulos');
    const costoTotalElement = document.querySelector('#costo-total');
    const unidadSelect = document.getElementById('unidad_id');
    const stockInput = document.getElementById('stock');
    const stockMinimoInput = document.getElementById('stock_minimo');

    // Mapa para almacenar información de unidades de artículos seleccionados
    const unidadesPorArticulo = new Map();

    // Inicializar Select2 para mejores controles de selección si jQuery está disponible
    if (typeof $ !== 'undefined') {
        $('#articulo_servicio').select2({
            placeholder: 'Buscar artículo por nombre o código',
            allowClear: true
        });

        $('#categoria_id').select2({
            placeholder: 'Seleccione una categoría'
        });

        $('#unidad_id').select2({
            placeholder: 'Seleccione una unidad de medida'
        });
    }

    // Cargar información de unidades para todos los artículos disponibles
    document.querySelectorAll('#articulo_servicio option').forEach(option => {
        const articuloId = option.value;
        if (articuloId) {
            const unidadAbr = option.getAttribute('data-unidad');
            // Consultar el tipo de unidad mediante una petición AJAX
            if (articuloId) {
                fetch(`/api/articulos/${articuloId}/unidad`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            unidadesPorArticulo.set(parseInt(articuloId), {
                                tipo: data.tipo,
                                abreviatura: unidadAbr
                            });
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    });

    // Cambiar visibilidad de pestaña de componentes según el tipo
    tipoSelect.addEventListener('change', function() {
        if (this.value === 'servicio') {
            componentesTab.classList.remove('d-none');
            // Activar la pestaña de componentes
            const componentsTabTrigger = new bootstrap.Tab(componentesTab);
            componentsTabTrigger.show();
        } else {
            componentesTab.classList.add('d-none');
            // Activar primera pestaña
            const infoTabTrigger = document.querySelector('#info-tab');
            const bsInfoTab = new bootstrap.Tab(infoTabTrigger);
            bsInfoTab.show();
        }
    });

    // Calcular margen al cargar la página
    calcularMargen();

    // Modificar validación de campos de stock según tipo de unidad
    unidadSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (!selectedOption) return;

        const tipoUnidad = selectedOption.getAttribute('data-tipo');
        if (tipoUnidad === 'unidad') {
            stockInput.setAttribute('step', '1');
            stockInput.setAttribute('min', '0');
            stockMinimoInput.setAttribute('step', '1');
            stockMinimoInput.setAttribute('min', '0');

            // Validar y corregir valores decimales inmediatamente
            stockInput.value = Math.floor(parseFloat(stockInput.value) || 0);
            stockMinimoInput.value = Math.floor(parseFloat(stockMinimoInput.value) || 0);

            // Añadir validador para solo permitir enteros
            stockInput.addEventListener('input', soloPermitirEnteros);
            stockMinimoInput.addEventListener('input', soloPermitirEnteros);

            // Forzar el cambio para actualizar el valor inmediatamente
            stockInput.dispatchEvent(new Event('input'));
            stockMinimoInput.dispatchEvent(new Event('input'));
        } else {
            stockInput.setAttribute('step', '0.01');
            stockInput.setAttribute('min', '0');
            stockMinimoInput.setAttribute('step', '0.01');
            stockMinimoInput.setAttribute('min', '0');

            // Quitar validador de enteros
            stockInput.removeEventListener('input', soloPermitirEnteros);
            stockMinimoInput.removeEventListener('input', soloPermitirEnteros);
        }
    });

    // Validar tipo de unidad del artículo seleccionado para componentes de servicio
    articuloServicioSelect.addEventListener('change', function() {
        const articuloId = parseInt(this.value);
        if (!articuloId) return;

        // Si no tenemos la información de la unidad, la solicitamos
        if (!unidadesPorArticulo.has(articuloId)) {
            fetch(`/api/articulos/${articuloId}/unidad`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const unidadInfo = {
                            tipo: data.tipo,
                            abreviatura: data.abreviatura
                        };
                        unidadesPorArticulo.set(articuloId, unidadInfo);
                        ajustarCampoCantidad(cantidadServicioInput, unidadInfo.tipo);
                    }
                })
                .catch(error => console.error('Error:', error));
        } else {
            // Si ya tenemos la información, la usamos
            const unidadInfo = unidadesPorArticulo.get(articuloId);
            ajustarCampoCantidad(cantidadServicioInput, unidadInfo.tipo);
        }
    });

    // Agregar nuevo artículo al servicio
    addArticuloBtn.addEventListener('click', function() {
        const articuloId = parseInt(articuloServicioSelect.value);
        if (!articuloId) return alert('Debe seleccionar un artículo');

        const cantidad = parseFloat(cantidadServicioInput.value);
        if (isNaN(cantidad) || cantidad <= 0) return alert('La cantidad debe ser mayor a 0');

        const option = articuloServicioSelect.options[articuloServicioSelect.selectedIndex];
        const articuloNombre = option.textContent;
        const precio = parseFloat(option.getAttribute('data-precio') || 0);
        const unidadInfo = unidadesPorArticulo.get(articuloId) || { abreviatura: '', tipo: 'decimal' };
        const unidad = unidadInfo.abreviatura;
        const tipoUnidad = unidadInfo.tipo;
        const costo = precio * cantidad;

        // Verificar si el tipo es unidad y la cantidad tiene decimales
        if (tipoUnidad === 'unidad' && !Number.isInteger(cantidad)) {
            return alert('Este artículo utiliza una unidad de tipo entero. Por favor, ingrese un número entero.');
        }

        // Verificar si ya existe el artículo en la tabla
        const existeArticulo = document.querySelector(`input[name="articulos_servicio[${articuloId}]"]`);
        if (existeArticulo) {
            return alert('Este artículo ya ha sido agregado al servicio');
        }

        // Crear fila para el nuevo artículo
        const row = document.createElement('tr');
        row.setAttribute('data-precio', precio);
        row.setAttribute('data-tipo-unidad', tipoUnidad);
        row.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <div>
                        <span class="d-block fw-bold">${articuloNombre}</span>
                    </div>
                </div>
                <input type="hidden" name="articulos_servicio[${articuloId}]" value="${cantidad}">
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" step="${tipoUnidad === 'unidad' ? '1' : '0.01'}"
                           class="form-control cantidad-articulo" value="${cantidad}"
                           min="${tipoUnidad === 'unidad' ? '1' : '0.01'}" required
                           onchange="actualizarCantidad(this, ${articuloId})"
                           data-tipo-unidad="${tipoUnidad}">
                    <span class="input-group-text">${unidad}</span>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">{{ $config->currency_simbol }}.</span>
                    <input type="text" class="form-control costo-articulo" value="${costo.toFixed(2)}" readonly>
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-articulo-servicio">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        servicioArticulosBody.appendChild(row);

        // Limpiar campos
        articuloServicioSelect.value = '';
        cantidadServicioInput.value = '1';
        if (typeof $ !== 'undefined') {
            $('#articulo_servicio').trigger('change');
        }

        actualizarContadores();
        actualizarCostoTotal();
    });

    // Eliminar artículo de la tabla (delegación de eventos)
    document.addEventListener('click', function(e) {
        if (e.target.matches('.remove-articulo-servicio') || e.target.closest('.remove-articulo-servicio')) {
            const boton = e.target.matches('.remove-articulo-servicio') ? e.target : e.target.closest('.remove-articulo-servicio');
            boton.closest('tr').remove();
            actualizarContadores();
            actualizarCostoTotal();
        }
    });

    // Actualizar costo al cambiar cantidad (delegación de eventos)
    document.addEventListener('input', function(e) {
        if (e.target.matches('.cantidad-articulo')) {
            const cantidadInput = e.target;
            const row = cantidadInput.closest('tr');
            const tipoUnidad = cantidadInput.getAttribute('data-tipo-unidad');
            let cantidad = parseFloat(cantidadInput.value) || 0;

            // Si es unidad, validamos que sea entero
            if (tipoUnidad === 'unidad') {
                cantidad = Math.floor(cantidad);
                cantidadInput.value = cantidad;
            }

            const costoInput = row.querySelector('.costo-articulo');
            const precio = parseFloat(row.getAttribute('data-precio'));
            const costo = precio * cantidad;
            costoInput.value = costo.toFixed(2);

            // Actualizar el valor en el input hidden
            const articuloId = row.querySelector('input[name^="articulos_servicio["]').name.match(/\[(\d+)\]/)[1];
            row.querySelector(`input[name="articulos_servicio[${articuloId}]"]`).value = cantidad;

            actualizarCostoTotal();
        }
    });

    // Validación del formulario
    document.querySelector('#form-articulo').addEventListener('submit', function(e) {
        if (tipoSelect.value === 'servicio') {
            const tieneArticulos = servicioArticulosBody.querySelectorAll('tr').length > 0;

            if (!tieneArticulos) {
                e.preventDefault();
                alert('Debe agregar al menos un artículo al servicio');
                const componentsTabTrigger = new bootstrap.Tab(componentesTab);
                componentsTabTrigger.show();
            }
        }
    });

    // Función para actualizar contadores
    function actualizarContadores() {
        const cantidadArticulos = servicioArticulosBody.querySelectorAll('tr').length;
        contadorArticulos.textContent = cantidadArticulos.toString();
    }

    // Función para actualizar costo total
    function actualizarCostoTotal() {
        let costoTotal = 0;
        document.querySelectorAll('.costo-articulo').forEach(function(input) {
            costoTotal += parseFloat(input.value.replace(/,/g, '')) || 0;
        });

        costoTotalElement.value = costoTotal.toFixed(2);
        // Si es servicio, sugerir precio de venta basado en los costos
        if (tipoSelect.value === 'servicio') {
            const sugerido = costoTotal * 1.3; // 30% de ganancia
            precioCompraInput.value = costoTotal.toFixed(2);
            // Solo sugerir si el precio actual es 0 o menor que el costo
            if (parseFloat(precioVentaInput.value) == 0 || parseFloat(precioVentaInput.value) < costoTotal) {
                precioVentaInput.value = sugerido.toFixed(2);
            }

            calcularMargen();
        }
    }

    // Verificar si hay datos antiguos de servicio al cargar y actualizar contadores
    if (servicioArticulosBody.querySelectorAll('tr').length > 0) {
        actualizarContadores();
        actualizarCostoTotal();
    }

    // Mostrar/ocultar pestaña de componentes según el tipo al cargar
    if (tipoSelect.value === 'servicio') {
        componentesTab.classList.remove('d-none');
    } else {
        componentesTab.classList.add('d-none');
    }

    // Ejecutar la validación inicial de unidades
    if (unidadSelect.selectedIndex > 0) {
        setTimeout(() => {
            const event = new Event('change');
            unidadSelect.dispatchEvent(event);
        }, 100);
    }

    // Función para validar que solo se ingresen números enteros
    function soloPermitirEnteros(e) {
        const input = e.target;
        const valor = input.value;

        // Si hay un punto decimal, redondeamos al entero más cercano
        if (valor.includes('.')) {
            input.value = Math.floor(parseFloat(valor));
        }
    }

    // Función para ajustar el campo cantidad según el tipo de unidad
    function ajustarCampoCantidad(input, tipoUnidad) {
        if (tipoUnidad === 'unidad') {
            input.setAttribute('step', '1');
            input.setAttribute('min', '1');
            input.value = Math.floor(parseFloat(input.value) || 1);

            // Agregar evento para validar enteros
            input.addEventListener('input', soloPermitirEnteros);
        } else {
            input.setAttribute('step', '0.01');
            input.setAttribute('min', '0.01');

            // Quitar validador de enteros
            input.removeEventListener('input', soloPermitirEnteros);
        }
    }
});

// Función para calcular y mostrar margen de ganancia
function calcularMargen() {
    // Valores principales
    const precioCompra = parseFloat(document.getElementById('precio_compra').value) || 0;
    const precioVenta = parseFloat(document.getElementById('precio_venta').value) || 0;
    const impuestoPorcentaje = parseFloat(document.getElementById('impuesto_porcentaje').value) || 0;
    const simboloMoneda = document.getElementById('currency_simbol').value || '$';

    // Obtener porcentajes de comisión
    const comisionVendedorSelect = document.getElementById('tipo_comision_vendedor_id');
    const comisionTrabajadorSelect = document.getElementById('tipo_comision_trabajador_id');

    let comisionVendedorPorcentaje = 0;
    let comisionTrabajadorPorcentaje = 0;

    // Verificar si hay opciones seleccionadas
    if (comisionVendedorSelect.selectedIndex > 0) {
        comisionVendedorPorcentaje = parseFloat(comisionVendedorSelect.options[comisionVendedorSelect.selectedIndex].getAttribute('data-porcentaje')) || 0;
    }

    if (comisionTrabajadorSelect.selectedIndex > 0) {
        comisionTrabajadorPorcentaje = parseFloat(comisionTrabajadorSelect.options[comisionTrabajadorSelect.selectedIndex].getAttribute('data-porcentaje')) || 0;
    }

    // Calcular costos adicionales
    const comisionVendedorValor = precioVenta * (comisionVendedorPorcentaje / 100);
    const comisionTrabajadorValor = precioVenta * (comisionTrabajadorPorcentaje / 100);
    const impuestoValor = precioVenta * (impuestoPorcentaje / 100);

    // Calcular ganancia y margen
    const ganancia = precioVenta - precioCompra;
    const gananciaReal = ganancia - comisionVendedorValor - comisionTrabajadorValor - impuestoValor;
    const margenReal = precioCompra > 0 ? (gananciaReal / precioCompra) * 100 : 0;

    // Actualizar etiquetas en la tabla
    document.getElementById('td-precio-venta').textContent = `${simboloMoneda}.${formatNumber(precioVenta)}`;
    document.getElementById('td-precio-compra').textContent = `- ${simboloMoneda}.${formatNumber(precioCompra)}`;

    document.getElementById('td-label-comision-vendedor').textContent = `Comisión Vendedor (${formatNumber(comisionVendedorPorcentaje)}%)`;
    document.getElementById('td-comision-vendedor').textContent = `- ${simboloMoneda}.${formatNumber(comisionVendedorValor)}`;

    document.getElementById('td-label-comision-trabajador').textContent = `Comisión Trabajador (${formatNumber(comisionTrabajadorPorcentaje)}%)`;
    document.getElementById('td-comision-trabajador').textContent = `- ${simboloMoneda}.${formatNumber(comisionTrabajadorValor)}`;

    document.getElementById('td-label-impuesto').textContent = `Impuesto (${formatNumber(impuestoPorcentaje)}%)`;
    document.getElementById('td-impuesto').textContent = `- ${simboloMoneda}.${formatNumber(impuestoValor)}`;

    // Colorear ganancia real
    const tdGananciaReal = document.getElementById('td-ganancia-real');
    tdGananciaReal.textContent = `${simboloMoneda}.${formatNumber(gananciaReal)}`;
    tdGananciaReal.className = gananciaReal >= 0 ? 'text-end text-success' : 'text-end text-danger';

    // Actualizar margen visual
    const margenElement = document.getElementById('margen-valor');
    const margenBarra = document.getElementById('margen-barra');
    margenElement.textContent = `${formatNumber(margenReal)}%`;

    // Establecer color según el valor del margen
    let colorClass = 'bg-success';
    if (margenReal < 10) {
        colorClass = 'bg-danger';
    } else if (margenReal < 20) {
        colorClass = 'bg-warning';
    }

    // Remover clases de color anteriores
    margenBarra.className = 'progress-bar';
    margenElement.className = 'badge fs-6';

    // Agregar nueva clase de color
    margenBarra.classList.add(colorClass);
    margenElement.classList.add(colorClass);

    // Actualizar ancho de la barra de progreso (máximo 100%)
    const anchoBarraPorcentaje = Math.min(margenReal, 100);
    margenBarra.style.width = `${anchoBarraPorcentaje}%`;
}

// Función auxiliar para formatear números con 2 decimales
function formatNumber(number) {
    return (Math.round(number * 100) / 100).toFixed(2);
}

// Función para actualizar la cantidad en el input hidden
function actualizarCantidad(input, articuloId) {
    const tipoUnidad = input.getAttribute('data-tipo-unidad');
    let cantidad = parseFloat(input.value);

    if (isNaN(cantidad) || cantidad <= 0) {
        input.value = tipoUnidad === 'unidad' ? '1' : '0.01';
        return alert('La cantidad debe ser mayor a 0');
    }

    // Si es unidad, aseguramos que sea entero
    if (tipoUnidad === 'unidad' && !Number.isInteger(cantidad)) {
        cantidad = Math.floor(cantidad);
        input.value = cantidad;
    }

    const hiddenInput = document.querySelector(`input[name="articulos_servicio[${articuloId}]"]`);
    if (hiddenInput) {
        hiddenInput.value = cantidad;
    }
}
</script>
@endsection
