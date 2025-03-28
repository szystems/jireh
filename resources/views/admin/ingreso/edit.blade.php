@extends('layouts.admin')

@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-cart-plus"></i>
                </div>
                <div class="page-title">
                    <h5>Editar Ingreso #{{ $ingreso->id }}</h5>
                </div>
            </div>
        </div>
        <!-- Main header ends -->

        <!-- Content wrapper start -->
        <div class="content-wrapper">

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-info-circle"></i> Información del Ingreso</h5>
                        </div>
                        <div class="card-body">
                            @if (count($errors)>0)
                                <div class="alert alert-danger" role="alert">
                                    <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-1"></i> Error en el formulario</h5>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{$error}}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ url('update-ingreso/'.$ingreso->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Datos básicos del ingreso -->
                                <div class="row gx-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="numero_factura" class="form-label"><i class="bi bi-receipt me-1"></i> Número de Factura</label>
                                        <input type="text" name="numero_factura" class="form-control" value="{{ old('numero_factura', $ingreso->numero_factura) }}" placeholder="Número de factura">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha" class="form-label"><i class="bi bi-calendar-date me-1"></i> Fecha</label>
                                        <input type="date" name="fecha" class="form-control" value="{{ old('fecha', $ingreso->fecha) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="proveedor_id" class="form-label"><i class="bi bi-shop me-1"></i> Proveedor</label>
                                        <select name="proveedor_id" class="form-control select2" data-placeholder="Seleccione un proveedor">
                                            @foreach($proveedores as $proveedor)
                                                <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $ingreso->proveedor_id) == $proveedor->id ? 'selected' : '' }}>{{ $proveedor->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tipo_compra" class="form-label"><i class="bi bi-tag me-1"></i> Tipo de Compra</label>
                                        <select name="tipo_compra" class="form-control">
                                            <option value="CDS" {{ old('tipo_compra', $ingreso->tipo_compra) == 'CDS' ? 'selected' : '' }}>CDS</option>
                                            <option value="Car Wash" {{ old('tipo_compra', $ingreso->tipo_compra) == 'Car Wash' ? 'selected' : '' }}>Car Wash</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Sección para agregar artículos -->
                                <div class="card mt-4 mb-4 border-info">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0"><i class="bi bi-boxes me-1"></i> Agregar Nuevo Artículo</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-4">
                                                <label for="articulo" class="form-label"><i class="bi bi-box me-1"></i> Artículo</label>
                                                <select class="form-control select2" id="articulo" data-placeholder="Seleccione un artículo">
                                                    <option value="">Seleccione un artículo</option>
                                                    @foreach($todosArticulos as $articulo)
                                                        <option value="{{ $articulo->id }}"
                                                                data-precio-compra="{{ $articulo->precio_compra }}"
                                                                data-precio-venta="{{ $articulo->precio_venta }}"
                                                                data-unidad="{{ $articulo->unidad->nombre }}"
                                                                data-unidad-tipo="{{ $articulo->unidad->tipo }}"
                                                                data-unidad-abreviatura="{{ $articulo->unidad->abreviatura }}">
                                                            {{ $articulo->codigo }} {{ $articulo->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="cantidad" class="form-label"><i class="bi bi-123 me-1"></i> Cantidad</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="cantidad" min="1" step="1">
                                                    <span class="input-group-text" id="unidad-abreviatura"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="precio_compra" class="form-label"><i class="bi bi-currency-dollar me-1"></i> Precio Compra</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ $config->currency_simbol }}</span>
                                                    <input type="number" step="0.01" class="form-control" id="precio_compra">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="precio_venta" class="form-label"><i class="bi bi-tag-fill me-1"></i> Precio Venta</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">{{ $config->currency_simbol }}</span>
                                                    <input type="number" step="0.01" class="form-control" id="precio_venta">
                                                </div>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-primary w-100" id="add-detalle">
                                                    <i class="bi bi-plus-square me-1"></i> Agregar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabla de artículos existentes -->
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="bi bi-list-check me-1"></i> Artículos del Ingreso</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Artículo</th>
                                                        <th>Cantidad</th>
                                                        <th>Precio de Compra</th>
                                                        <th>Precio de Venta</th>
                                                        <th>Subtotal</th>
                                                        <th width="90">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="detalles-body">
                                                    @if(old('detalles'))
                                                        @foreach(old('detalles') as $index => $detalle)
                                                            <tr>
                                                                <td>
                                                                    {{ is_array($detalle) ? $detalle['articulo_id'] : $detalle }}
                                                                    <input type="hidden" name="detalles[{{ $index }}][articulo_id]" value="{{ is_array($detalle) ? $detalle['articulo_id'] : $detalle }}">
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <input type="number" name="detalles[{{ $index }}][cantidad]" class="form-control cantidad-input" value="{{ is_array($detalle) ? $detalle['cantidad'] : '' }}" data-tipo="{{ is_array($detalle) ? $detalle['unidad_tipo'] ?? '' : '' }}">
                                                                        <span class="input-group-text">{{ is_array($detalle) ? $detalle['unidad_abreviatura'] ?? '' : '' }}</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">{{ $config->currency_simbol }}</span>
                                                                        <input type="number" step="0.01" name="detalles[{{ $index }}][precio_compra]" class="form-control precio-compra" value="{{ is_array($detalle) ? $detalle['precio_compra'] : '' }}">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">{{ $config->currency_simbol }}</span>
                                                                        <input type="number" step="0.01" name="detalles[{{ $index }}][precio_venta]" class="form-control" value="{{ is_array($detalle) ? $detalle['precio_venta'] : '' }}">
                                                                    </div>
                                                                </td>
                                                                <td class="subtotal">
                                                                    {{ $config->currency_simbol }}.{{ number_format(is_array($detalle) ? $detalle['precio_compra'] * $detalle['cantidad'] : 0, 2, '.', ',') }}
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-danger btn-sm remove-detalle"><i class="bi bi-trash-fill"></i></button>
                                                                    <input type="hidden" name="detalles[{{ $index }}][eliminar]" class="detalle-eliminar" value="0">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        @foreach($ingreso->detalles as $detalle)
                                                            <tr>
                                                                <td>
                                                                    <input type="hidden" name="detalles[{{ $detalle->id }}][articulo_id]" value="{{ $detalle->articulo_id }}">
                                                                    <strong class="text-primary">{{ $detalle->articulo->codigo }}</strong> {{ $detalle->articulo->nombre }}
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <input type="number" name="detalles[{{ $detalle->id }}][cantidad]" class="form-control cantidad-input" value="{{ $detalle->cantidad }}" data-tipo="{{ $detalle->articulo->unidad->tipo }}">
                                                                        <span class="input-group-text">{{ $detalle->articulo->unidad->abreviatura }}</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">{{ $config->currency_simbol }}</span>
                                                                        <input type="number" step="0.01" name="detalles[{{ $detalle->id }}][precio_compra]" class="form-control precio-compra" value="{{ $detalle->precio_compra }}">
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">{{ $config->currency_simbol }}</span>
                                                                        <input type="number" step="0.01" name="detalles[{{ $detalle->id }}][precio_venta]" class="form-control" value="{{ $detalle->precio_venta }}">
                                                                    </div>
                                                                </td>
                                                                <td class="subtotal">
                                                                    {{ $config->currency_simbol }}.{{ number_format($detalle->precio_compra * $detalle->cantidad, 2, '.', ',') }}
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-danger btn-sm remove-detalle"><i class="bi bi-trash-fill"></i></button>
                                                                    <input type="hidden" name="detalles[{{ $detalle->id }}][eliminar]" class="detalle-eliminar" value="0">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                                <tfoot class="bg-light">
                                                    <tr>
                                                        <td colspan="4" class="text-end fw-bold">Total:</td>
                                                        <td class="text-primary fw-bold" id="total-compra"><strong></strong></td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div id="no-articulos" class="alert alert-info m-3 d-none">
                                            <i class="bi bi-info-circle me-2"></i> No hay artículos en este ingreso. Agregue al menos un artículo.
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="d-flex justify-content-center gap-3 mt-4">
                                    <a href="{{ url('ingresos') }}" class="btn btn-danger">
                                        <i class="bi bi-x-circle me-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success" id="btn-guardar">
                                        <i class="bi bi-check2-square me-1"></i> Actualizar Ingreso
                                    </button>
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
        // Inicializar Select2
        $('.select2').select2();

        // Referencias a elementos del DOM
        const articuloSelect = document.getElementById('articulo');
        const precioCompraInput = document.getElementById('precio_compra');
        const precioVentaInput = document.getElementById('precio_venta');
        const cantidadInput = document.getElementById('cantidad');
        const unidadAbreviaturaSpan = document.getElementById('unidad-abreviatura');
        const addDetalleBtn = document.getElementById('add-detalle');
        const detallesBody = document.getElementById('detalles-body');
        const currencySymbol = @json($config->currency_simbol);

        // Contador para nuevos artículos
        let newItemCounter = 0;

        // Evento para cuando se selecciona un artículo con Select2
        $('#articulo').on('select2:select', function(e) {
            const selectedOption = articuloSelect.options[articuloSelect.selectedIndex];

            if (selectedOption) {
                const precioCompra = selectedOption.getAttribute('data-precio-compra');
                const precioVenta = selectedOption.getAttribute('data-precio-venta');
                const unidadAbreviatura = selectedOption.getAttribute('data-unidad-abreviatura');
                const unidadTipo = selectedOption.getAttribute('data-unidad-tipo');

                precioCompraInput.value = precioCompra;
                precioVentaInput.value = precioVenta;
                unidadAbreviaturaSpan.textContent = unidadAbreviatura;

                if (unidadTipo === 'decimal') {
                    cantidadInput.step = "0.01";
                    cantidadInput.min = "0.01";
                } else {
                    cantidadInput.step = "1";
                    cantidadInput.min = "1";
                }
                cantidadInput.value = "";
            }
        });

        // Validación de entrada para cantidad
        cantidadInput.addEventListener('input', function (event) {
            const selectedOption = articuloSelect.options[articuloSelect.selectedIndex];
            if (!selectedOption) return;

            const unidadTipo = selectedOption.getAttribute('data-unidad-tipo');
            const value = event.target.value;
            const cursorPosition = event.target.selectionStart;

            if (unidadTipo === 'unidad') {
                event.target.value = value.replace(/[^0-9]/g, '');
            } else if (unidadTipo === 'decimal') {
                const decimalValue = value.replace(/[^0-9.]/g, '');
                const parts = decimalValue.split('.');
                event.target.value = parts.length > 2 ? parts[0] + '.' + parts.slice(1).join('') : decimalValue;
            }
            event.target.setSelectionRange(cursorPosition, cursorPosition);
        });

        // Evento para agregar un artículo a la tabla
        addDetalleBtn.addEventListener('click', function () {
            const articuloId = articuloSelect.value;
            if (!articuloId) {
                alert("Seleccione un artículo");
                return;
            }

            // Verificar si el artículo ya está en los detalles
            const existingArticulo = Array.from(detallesBody.querySelectorAll('input[name*="[articulo_id]"]')).find(input => input.value == articuloId);
            if (existingArticulo) {
                alert("El artículo ya está incluido en el ingreso. Edite las cantidades y precios desde la tabla de detalles.");
                return;
            }

            const selectedOption = articuloSelect.options[articuloSelect.selectedIndex];
            const articuloText = selectedOption.text;
            const precioCompra = parseFloat(precioCompraInput.value);
            const precioVenta = parseFloat(precioVentaInput.value);
            const cantidad = parseFloat(cantidadInput.value);
            const unidadAbreviatura = unidadAbreviaturaSpan.textContent;
            const unidadTipo = selectedOption.getAttribute('data-unidad-tipo');

            if (isNaN(precioCompra) || isNaN(precioVenta) || isNaN(cantidad) ||
                cantidad < (unidadTipo === 'unidad' ? 1 : 0.01)) {
                alert('Por favor, complete todos los campos correctamente.');
                return;
            }

            // Usar un identificador único para cada nuevo artículo
            const newIndex = 'new' + newItemCounter++;

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    ${articuloText}
                    <input type="hidden" name="detalles[${newIndex}][articulo_id]" value="${articuloId}">
                </td>
                <td>
                    <div class="input-group">
                        <input type="number" name="detalles[${newIndex}][cantidad]" class="form-control cantidad-input" value="${cantidad}" data-tipo="${unidadTipo}">
                        <span class="input-group-text unidad-abreviatura">${unidadAbreviatura}</span>
                        <input type="hidden" name="detalles[${newIndex}][unidad_tipo]" value="${unidadTipo}">
                        <input type="hidden" name="detalles[${newIndex}][unidad_abreviatura]" value="${unidadAbreviatura}">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">${currencySymbol}</span>
                        <input type="number" step="0.01" name="detalles[${newIndex}][precio_compra]" class="form-control" value="${precioCompra.toFixed(2)}">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">${currencySymbol}</span>
                        <input type="number" step="0.01" name="detalles[${newIndex}][precio_venta]" class="form-control" value="${precioVenta.toFixed(2)}">
                    </div>
                </td>
                <td class="subtotal">
                    ${currencySymbol}.${(cantidad * precioCompra).toFixed(2)}
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-detalle"><i class="bi bi-trash-fill"></i></button>
                </td>`;
            detallesBody.appendChild(row);

            // Reiniciar campos
            articuloSelect.value = '';
            precioCompraInput.value = '';
            precioVentaInput.value = '';
            cantidadInput.value = '';
            unidadAbreviaturaSpan.textContent = '';
            $('#articulo').val('').trigger('change');

            // Aplicar validación de cantidad al nuevo input
            const newCantidadInput = row.querySelector('.cantidad-input');
            newCantidadInput.addEventListener('input', function (event) {
                const value = event.target.value;
                const cursorPosition = event.target.selectionStart;
                const tipo = this.getAttribute('data-tipo');

                if (tipo === 'unidad') {
                    event.target.value = value.replace(/[^0-9]/g, '');
                } else if (tipo === 'decimal') {
                    const decimalValue = value.replace(/[^0-9.]/g, '');
                    const parts = decimalValue.split('.');
                    event.target.value = parts.length > 2 ? parts[0] + '.' + parts.slice(1).join('') : decimalValue;
                }
                event.target.setSelectionRange(cursorPosition, cursorPosition);
            });

            setTimeout(actualizarTotal, 10);
        });

        // Eliminar artículo de la tabla
        detallesBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-detalle') || e.target.closest('.remove-detalle')) {
                const row = e.target.closest('tr');
                const eliminarInput = row.querySelector('.detalle-eliminar');
                if (eliminarInput) {
                    eliminarInput.value = 1;
                    row.style.display = 'none';
                } else {
                    row.remove();
                }
                setTimeout(actualizarTotal, 10); // Pequeño retraso para asegurar que la fila se ha ocultado
            }
        });

        // Configurar campos de cantidad existentes
        document.querySelectorAll('.cantidad-input').forEach(function (input) {
            const tipo = input.getAttribute('data-tipo');
            if (tipo === 'unidad') {
                input.setAttribute('step', '1');
                input.setAttribute('min', '1');
                input.value = Math.floor(input.value);
            } else {
                input.setAttribute('step', '0.01');
                input.setAttribute('min', '0.01');
            }

            input.addEventListener('input', function (event) {
                const value = event.target.value;
                const cursorPosition = event.target.selectionStart;

                if (tipo === 'unidad') {
                    event.target.value = value.replace(/[^0-9]/g, '');
                } else if (tipo === 'decimal') {
                    const decimalValue = value.replace(/[^0-9.]/g, '');
                    const parts = decimalValue.split('.');
                    event.target.value = parts.length > 2 ? parts[0] + '.' + parts.slice(1).join('') : decimalValue;
                }
                event.target.setSelectionRange(cursorPosition, cursorPosition);
            });
        });

        // Validar que al menos exista un artículo en los detalles al enviar el formulario
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            // Contar filas visibles (no ocultas)
            const visibleRows = Array.from(detallesBody.children).filter(row => row.style.display !== 'none');
            if (visibleRows.length === 0) {
                alert("Debe haber al menos un artículo en los detalles.");
                e.preventDefault();
            }
        });

        // Función para actualizar el subtotal de una fila específica
        function actualizarSubtotal(fila) {
            const cantidadInput = fila.querySelector('.cantidad-input');
            const precioInput = fila.querySelector('input[name*="precio_compra"]');
            const subtotalCell = fila.querySelector('.subtotal');

            if (cantidadInput && precioInput && subtotalCell) {
                const cantidad = parseFloat(cantidadInput.value);
                const precio = parseFloat(precioInput.value);

                if (!isNaN(cantidad) && !isNaN(precio)) {
                    const subtotal = cantidad * precio;
                    subtotalCell.textContent = `${currencySymbol}.${subtotal.toFixed(2)}`;
                }
            }
        }

        // Agregar función para actualizar el total
        function actualizarTotal() {
            let total = 0;
            const filas = document.querySelectorAll('#detalles-body tr:not([style*="display: none"])');

            filas.forEach(function(fila) {
                // Actualizar el subtotal de la fila primero
                actualizarSubtotal(fila);

                const subtotalCell = fila.querySelector('.subtotal');
                if (subtotalCell) {
                    // Extraer el valor numérico del subtotal
                    const subtotalText = subtotalCell.textContent.trim();

                    // Procesar el texto para extraer el número correctamente
                    let numeroStr = subtotalText;
                    if (numeroStr.startsWith(currencySymbol)) {
                        numeroStr = numeroStr.substring(currencySymbol.length);
                    }

                    if (numeroStr.startsWith('.')) {
                        numeroStr = numeroStr.substring(1);
                    }

                    numeroStr = numeroStr.replace(/,/g, '');
                    const valorNumerico = parseFloat(numeroStr);

                    if (!isNaN(valorNumerico)) {
                        total += valorNumerico;
                    }
                }
            });

            const totalElement = document.getElementById('total-compra');
            if (totalElement) {
                totalElement.querySelector('strong').textContent = `${currencySymbol}.${total.toFixed(2)}`;
            }
        }

        // Evento input para cantidad y precio - actualización en vivo
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('cantidad-input') || e.target.classList.contains('precio-compra')) {
                // Actualizar el subtotal de la fila inmediatamente
                const fila = e.target.closest('tr');
                if (fila) {
                    actualizarSubtotal(fila);
                    actualizarTotal();
                }
            }
        });

        // Eventos para inputs dinamicamente generados (delegación de eventos)
        detallesBody.addEventListener('change', function(e) {
            if (e.target.classList.contains('cantidad-input') || e.target.name && e.target.name.includes('precio_compra')) {
                actualizarTotal();
            }
        });

        // Calcular total inicial al cargar la página
        setTimeout(actualizarTotal, 100);

        // Mostrar/ocultar mensaje de "no hay artículos"
        function actualizarEstadoTabla() {
            const visibleRows = Array.from(detallesBody.children).filter(row => row.style.display !== 'none');
            document.getElementById('no-articulos').classList.toggle('d-none', visibleRows.length > 0);

            // Habilitar/deshabilitar botón guardar
            document.getElementById('btn-guardar').disabled = visibleRows.length === 0;
        }

        // Aplicar los mismos atajos de teclado de la vista de creación
        document.addEventListener('keydown', function(e) {
            // ALT + A = Agregar artículo (si todos los campos están completos)
            if (e.altKey && e.key.toLowerCase() === 'a') {
                e.preventDefault();
                if (addDetalleBtn && !addDetalleBtn.disabled) {
                    addDetalleBtn.click();
                }
            }

            // ALT + S = Submit/Guardar el formulario
            if (e.altKey && e.key.toLowerCase() === 's') {
                e.preventDefault();
                const btnGuardar = document.getElementById('btn-guardar');
                if (btnGuardar && !btnGuardar.disabled) {
                    btnGuardar.click();
                }
            }
        });

        // Actualizar evento para eliminar detalles
        detallesBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-detalle') || e.target.closest('.remove-detalle')) {
                const row = e.target.closest('tr');
                const eliminarInput = row.querySelector('.detalle-eliminar');
                if (eliminarInput) {
                    eliminarInput.value = 1;
                    row.style.display = 'none';
                } else {
                    row.remove();
                }
                setTimeout(actualizarTotal, 10);
                actualizarEstadoTabla();
            }
        });

        // Calcular total inicial y verificar estado de la tabla
        setTimeout(function() {
            actualizarTotal();
            actualizarEstadoTabla();
        }, 100);
    });
</script>
@endsection
