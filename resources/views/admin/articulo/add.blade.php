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

                                <!-- Sección de Información Básica -->
                                <div class="section mb-4">
                                    <h5 class="text-primary mb-3"><i class="bi bi-info-circle"></i> Información Básica</h5>
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
                                                        <label for="precio_compra" class="form-label">Precio de compra</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">{{ $config->currency_simbol }}</span>
                                                            <input type="number" step="0.01" min="0" class="form-control" id="precio_compra" name="precio_compra" value="{{ old('precio_compra', 0) }}" required onchange="calcularMargen()">
                                                        </div>
                                                        @if ($errors->has('precio_compra'))
                                                            <div class="invalid-feedback d-block">
                                                                <strong>{{ $errors->first('precio_compra') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="precio_venta" class="form-label">Precio de venta</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">{{ $config->currency_simbol }}</span>
                                                            <input type="number" step="0.01" min="0" class="form-control" id="precio_venta" name="precio_venta" value="{{ old('precio_venta', 0) }}" required onchange="calcularMargen()">
                                                        </div>
                                                        @if ($errors->has('precio_venta'))
                                                            <div class="invalid-feedback d-block">
                                                                <strong>{{ $errors->first('precio_venta') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="mb-0">
                                                        <label for="impuesto_porcentaje" class="form-label">Impuesto (%)</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">%</span>
                                                            <input type="number" step="0.01" min="0" max="100" class="form-control" id="impuesto_porcentaje" value="{{ $config->impuesto ?? 0 }}" readonly onchange="calcularMargen()">
                                                            <input type="hidden" id="currency_simbol" value="{{ $config->currency_simbol }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0"><i class="bi bi-graph-up"></i> Análisis de Rentabilidad</h6>
                                                </div>
                                                <div class="card-body p-0">
                                                    <div id="margen-detalle" class="p-3">
                                                        <h6 class="mb-3">Margen de Ganancia</h6>
                                                        <table class="table table-sm">
                                                            <tbody>
                                                                <tr>
                                                                    <td class="text-start">Precio de venta</td>
                                                                    <td class="text-end" id="td-precio-venta">{{ $config->currency_simbol }}.0.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-start">Precio de compra</td>
                                                                    <td class="text-end text-danger" id="td-precio-compra">- {{ $config->currency_simbol }}.0.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-start" id="td-label-impuesto">Impuesto (0%)</td>
                                                                    <td class="text-end text-danger" id="td-impuesto">- {{ $config->currency_simbol }}.0.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-start fw-bold">Ganancia real</td>
                                                                    <td class="text-end text-success" id="td-ganancia-real">{{ $config->currency_simbol }}.0.00</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                                            <span class="fw-bold">Margen:</span>
                                                            <span class="badge bg-success fs-6" id="margen-valor">0.00%</span>
                                                        </div>
                                                        <div class="progress mt-1">
                                                            <div class="progress-bar bg-success" id="margen-barra" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Stock -->
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0"><i class="bi bi-box-seam"></i> Inventario</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <label for="unidad_id" class="form-label">Unidad de medida</label>
                                                        <select class="form-select" id="unidad_id" name="unidad_id" required>
                                                            <option value="">Seleccione una unidad</option>
                                                            @foreach($unidades as $unidad)
                                                                <option value="{{ $unidad->id }}" {{ old('unidad_id') == $unidad->id ? 'selected' : '' }} data-tipo="{{ $unidad->tipo }}">{{ $unidad->nombre }} ({{ $unidad->abreviatura }})</option>
                                                            @endforeach
                                                        </select>
                                                        @if ($errors->has('unidad_id'))
                                                            <div class="invalid-feedback d-block">
                                                                <strong>{{ $errors->first('unidad_id') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="stock" class="form-label">Stock actual</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="bi bi-boxes"></i></span>
                                                            <input type="number" step="0.01" min="0" class="form-control" id="stock" name="stock" value="{{ old('stock', 0) }}" required>
                                                        </div>
                                                        @if ($errors->has('stock'))
                                                            <div class="invalid-feedback d-block">
                                                                <strong>{{ $errors->first('stock') }}</strong>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="mb-0">
                                                        <label for="stock_minimo" class="form-label">Stock mínimo</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="bi bi-exclamation-triangle"></i></span>
                                                            <input type="number" step="0.01" min="0" class="form-control" id="stock_minimo" name="stock_minimo" value="{{ old('stock_minimo', 0) }}" required>
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

                                        <!-- Sección: Comisión para mecánico (solo visible para servicios) -->
                                        <div class="col-md-12 mt-3" id="seccion-mecanico" style="display: none;">
                                            <div class="card">
                                                <div class="card-header bg-info bg-opacity-25">
                                                    <h6 class="mb-0"><i class="bi bi-wrench-adjustable"></i> Asignación y Comisiones</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4 mb-3">
                                                            <label for="mecanico_id" class="form-label">Asignar Mecánico</label>
                                                            <select class="form-select" id="mecanico_id" name="mecanico_id">
                                                                <option value="">No asignar mecánico fijo</option>
                                                                @foreach($mecanicos as $mecanico)
                                                                    <option value="{{ $mecanico->id }}" {{ old('mecanico_id') == $mecanico->id ? 'selected' : '' }}>
                                                                        {{ $mecanico->nombre_completo }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="costo_mecanico" class="form-label">Comisión Mecánico</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text">{{ $config->currency_simbol }}</span>
                                                                <input type="number" step="0.01" min="0" class="form-control" id="costo_mecanico" name="costo_mecanico" value="{{ old('costo_mecanico', 0) }}" onchange="calcularMargen()">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 mb-3">
                                                            <label for="comision_carwash" class="form-label">Comisión Car Wash</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text">{{ $config->currency_simbol }}</span>
                                                                <input type="number" step="0.01" min="0" class="form-control" id="comision_carwash" name="comision_carwash" value="{{ old('comision_carwash', 0) }}" onchange="calcularMargen()">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sección de Componentes de Servicio (solo para servicios) -->
                                <div class="section mb-4" id="seccion-componentes" style="display: none;">
                                    <h5 class="text-primary mb-3"><i class="bi bi-boxes"></i> Componentes del Servicio</h5>

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
                                                    <label for="articulo_servicio" class="form-label">Seleccionar Artículo</label>
                                                    <select class="form-select" id="articulo_servicio">
                                                        <option value="">Buscar artículo...</option>
                                                        @foreach($articulos->where('tipo', 'articulo') as $articulo)
                                                            <option value="{{ $articulo->id }}" data-precio="{{ $articulo->precio_compra }}" data-unidad="{{ $articulo->unidad->abreviatura }}">
                                                                {{ $articulo->nombre }} ({{ $articulo->unidad->abreviatura }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="cantidad_servicio" class="form-label">Cantidad</label>
                                                    <input type="number" step="0.01" min="0.01" class="form-control" id="cantidad_servicio" value="1">
                                                </div>
                                                <div class="col-md-3 d-flex align-items-end">
                                                    <button type="button" class="btn btn-success w-100" id="add-articulo-servicio">
                                                        <i class="bi bi-plus-circle"></i> Agregar Artículo
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover" id="tabla-servicio">
                                                    <thead>
                                                        <tr>
                                                            <th>Artículo</th>
                                                            <th style="width: 150px;">Cantidad</th>
                                                            <th style="width: 150px;">Costo</th>
                                                            <th style="width: 70px;">Acción</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="servicio-articulos-body">
                                                        <!-- Aquí se añaden dinámicamente los artículos -->
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-light">
                                                            <td class="fw-bold text-end" colspan="2">Costo Total:</td>
                                                            <td class="fw-bold" colspan="2">
                                                                <div class="input-group input-group-sm">
                                                                    <span class="input-group-text">{{ $config->currency_simbol }}</span>
                                                                    <input type="text" class="form-control" id="costo-total" value="0.00" readonly>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
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
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos principales
    const tipoSelect = document.querySelector('#tipo');
    const btnGuardar = document.querySelector('#btn-guardar');
    const precioCompraInput = document.querySelector('#precio_compra');
    const precioVentaInput = document.querySelector('#precio_venta');
    const seccionMecanico = document.querySelector('#seccion-mecanico');
    const seccionComponentes = document.querySelector('#seccion-componentes');

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

        $('#mecanico_id').select2({
            placeholder: 'Seleccione un mecánico'
        });
    }

    // Cambiar visibilidad de secciones según el tipo
    tipoSelect.addEventListener('change', function() {
        console.log("Tipo cambiado a:", this.value);

        if (this.value === 'servicio') {
            // Mostrar elementos para servicios
            if (seccionMecanico) seccionMecanico.style.display = 'block';
            if (seccionComponentes) seccionComponentes.style.display = 'block';

            // Enfocar la sección de componentes para asegurar la visibilidad
            setTimeout(() => {
                const seccionRect = seccionComponentes.getBoundingClientRect();
                const isInViewport = (
                    seccionRect.top >= 0 &&
                    seccionRect.left >= 0 &&
                    seccionRect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                    seccionRect.right <= (window.innerWidth || document.documentElement.clientWidth)
                );

                if (!isInViewport) {
                    seccionComponentes.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 300);
        } else {
            // Ocultar elementos para artículos
            if (seccionMecanico) seccionMecanico.style.display = 'none';
            if (seccionComponentes) seccionComponentes.style.display = 'none';
        }

        // Recalcular margen al cambiar el tipo
        calcularMargen();
    });

    // Calcular margen al cargar la página
    calcularMargen();

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
                    <span class="input-group-text">{{ $config->currency_simbol }}</span>
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

    // Eliminar artículo de la tabla
    document.addEventListener('click', function(e) {
        if (e.target.matches('.remove-articulo-servicio') || e.target.closest('.remove-articulo-servicio')) {
            const boton = e.target.matches('.remove-articulo-servicio') ? e.target : e.target.closest('.remove-articulo-servicio');
            boton.closest('tr').remove();
            actualizarContadores();
            actualizarCostoTotal();
        }
    });

    // Actualizar costo al cambiar cantidad
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
                window.scrollTo({
                    top: document.querySelector('#seccion-componentes').offsetTop - 100,
                    behavior: 'smooth'
                });
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

    // Inicialización según el tipo seleccionado al cargar
    if (tipoSelect.value === 'servicio') {
        seccionMecanico.style.display = 'block';
        seccionComponentes.style.display = 'block';
    } else {
        seccionMecanico.style.display = 'none';
        seccionComponentes.style.display = 'none';
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

    // Ejecutar la validación inicial de unidades
    if (unidadSelect.selectedIndex > 0) {
        setTimeout(() => {
            const event = new Event('change');
            unidadSelect.dispatchEvent(event);
        }, 100);
    }
});

// Función para calcular y mostrar margen de ganancia
function calcularMargen() {
    // Valores principales
    const precioCompra = parseFloat(document.getElementById('precio_compra').value) || 0;
    const precioVenta = parseFloat(document.getElementById('precio_venta').value) || 0;
    const impuestoPorcentaje = parseFloat(document.getElementById('impuesto_porcentaje').value) || 0;
    const simboloMoneda = document.getElementById('currency_simbol').value || '$';

    // Costos adicionales - comisiones
    const costoMecanico = parseFloat(document.getElementById('costo_mecanico')?.value || 0);
    const comisionCarwash = parseFloat(document.getElementById('comision_carwash')?.value || 0);
    const tipoArticulo = document.getElementById('tipo').value;

    // Solo aplicar costos de comisiones si es un servicio
    const costosComisiones = (tipoArticulo === 'servicio') ? (costoMecanico + comisionCarwash) : 0;

    // Calcular costos adicionales
    const impuestoValor = precioVenta * (impuestoPorcentaje / 100);

    // Calcular ganancia y margen incluyendo comisiones
    const ganancia = precioVenta - precioCompra;
    const gananciaReal = ganancia - impuestoValor - costosComisiones;

    // CORRECCIÓN: El margen se calcula sobre el costo total (precio de compra + comisiones)
    const costoTotal = precioCompra + costosComisiones;
    const margenReal = costoTotal > 0 ? (gananciaReal / costoTotal) * 100 : 0;

    // Actualizar etiquetas en la tabla
    document.getElementById('td-precio-venta').textContent = `${simboloMoneda}.${formatNumber(precioVenta)}`;
    document.getElementById('td-precio-compra').textContent = `- ${simboloMoneda}.${formatNumber(precioCompra)}`;

    document.getElementById('td-label-impuesto').textContent = `Impuesto (${formatNumber(impuestoPorcentaje)}%)`;
    document.getElementById('td-impuesto').textContent = `- ${simboloMoneda}.${formatNumber(impuestoValor)}`;

    // Añadir filas para comisiones si es un servicio
    const tablaMargen = document.querySelector('#margen-detalle table tbody');

    // Buscar o crear filas para comisiones
    let filaComisiones = document.getElementById('fila-comisiones');
    if (tipoArticulo === 'servicio' && costosComisiones > 0) {
        if (!filaComisiones) {
            // Crear fila si no existe
            filaComisiones = document.createElement('tr');
            filaComisiones.id = 'fila-comisiones';

            const tdLabel = document.createElement('td');
            tdLabel.className = 'text-start';
            tdLabel.id = 'td-label-comisiones';
            tdLabel.textContent = 'Comisiones (Mecánico/CarWash)';

            const tdValor = document.createElement('td');
            tdValor.className = 'text-end text-danger';
            tdValor.id = 'td-comisiones';

            filaComisiones.appendChild(tdLabel);
            filaComisiones.appendChild(tdValor);

            // Insertar antes de la fila de ganancia
            const filaGanancia = document.querySelector('#margen-detalle table tbody tr:last-child');
            if (filaGanancia) {
                tablaMargen.insertBefore(filaComisiones, filaGanancia);
            } else {
                tablaMargen.appendChild(filaComisiones);
            }
        }

        // Actualizar valor
        document.getElementById('td-comisiones').textContent = `- ${simboloMoneda}.${formatNumber(costosComisiones)}`;
    } else if (filaComisiones) {
        // Remover fila si no es servicio o no hay comisiones
        filaComisiones.remove();
    }

    // Colorear ganancia real
    const tdGananciaReal = document.getElementById('td-ganancia-real');
    tdGananciaReal.textContent = `${simboloMoneda}.${formatNumber(gananciaReal)}`;
    tdGananciaReal.className = gananciaReal >= 0 ? 'text-end text-success' : 'text-end text-danger';

    // Actualizar margen visual con el valor correcto
    const margenElement = document.getElementById('margen-valor');
    const margenBarra = document.getElementById('margen-barra');
    margenElement.textContent = `${formatNumber(margenReal)}%`;

    // Establecer color según el valor del margen real
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
