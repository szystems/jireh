@extends('layouts.admin')
@section('content')
    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-cart-plus"></i>
                </div>
                <div class="page-title">
                    <h5>Registrar Ingreso</h5>
                </div>
            </div>
        </div>
        <div class="content-wrapper">
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-3"><i class="bi bi-info-circle"></i> Información del Ingreso</h5>
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
                            <form action="{{ url('insert-ingreso') }}" method="POST">
                                @csrf
                                <div class="row gx-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="numero_factura" class="form-label"><i class="bi bi-receipt me-1"></i> Número de Factura</label>
                                        <input type="text" class="form-control" id="numero_factura" name="numero_factura" value="{{ old('numero_factura') }}" placeholder="Ingrese el número de factura">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha" class="form-label"><i class="bi bi-calendar-date me-1"></i> Fecha</label>
                                        <input type="date" class="form-control" id="fecha" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="proveedor_id" class="form-label"><i class="bi bi-shop me-1"></i> Proveedor</label>
                                        <select class="form-control select2" id="proveedor_id" name="proveedor_id" required data-placeholder="Seleccione un proveedor">
                                            @foreach ($proveedores as $proveedor)
                                                <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                                    {{ $proveedor->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tipo_compra" class="form-label"><i class="bi bi-tag me-1"></i> Tipo de Compra</label>
                                        <select class="form-control" id="tipo_compra" name="tipo_compra" required>
                                            <option value="Car Wash" {{ old('tipo_compra') == 'Car Wash' ? 'selected' : '' }}>Car Wash</option>
                                            <option value="CDS" {{ old('tipo_compra') == 'CDS' ? 'selected' : '' }}>CDS</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Sección para agregar artículos -->
                                <div class="card mt-4 mb-4 border-info">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-3"><i class="bi bi-boxes me-1"></i> Agregar Artículos</h5>
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
                                            <div class="col-md-2">
                                                <label for="cantidad" class="form-label"><i class="bi bi-123 me-1"></i> Cantidad</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="cantidad" min="1" step="1">
                                                    <span class="input-group-text" id="unidad-abreviatura"></span>
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

                                <!-- Tabla de artículos agregados -->
                                <div class="card mb-4">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-3"><i class="bi bi-list-check me-1"></i> Artículos del Ingreso</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped mb-0" id="tabla-detalles">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Artículo</th>
                                                        <th>Precio Compra</th>
                                                        <th>Precio Venta</th>
                                                        <th>Cantidad</th>
                                                        <th>Subtotal</th>
                                                        <th width="90">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="detalles-body">
                                                    @if(old('detalles'))
                                                        @foreach(old('detalles') as $index => $detalle)
                                                            <tr>
                                                                <td>
                                                                    Artículo ID: {{ $detalle['articulo_id'] }}
                                                                    <input type="hidden" name="detalles[{{ $index }}][articulo_id]" value="{{ $detalle['articulo_id'] }}">
                                                                </td>
                                                                <td>
                                                                    {{ $config->currency_simbol }}{{ number_format($detalle['precio_compra'], 2) }}
                                                                    <input type="hidden" name="detalles[{{ $index }}][precio_compra]" value="{{ $detalle['precio_compra'] }}">
                                                                </td>
                                                                <td>
                                                                    {{ $config->currency_simbol }}{{ number_format($detalle['precio_venta'], 2) }}
                                                                    <input type="hidden" name="detalles[{{ $index }}][precio_venta]" value="{{ $detalle['precio_venta'] }}">
                                                                </td>
                                                                <td>
                                                                    {{ $detalle['cantidad'] }}
                                                                    <input type="hidden" name="detalles[{{ $index }}][cantidad]" value="{{ $detalle['cantidad'] }}">
                                                                </td>
                                                                <td class="subtotal fw-bold text-primary">
                                                                    {{ $config->currency_simbol }}{{ number_format($detalle['precio_compra'] * $detalle['cantidad'], 2) }}
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-danger btn-sm remove-detalle">
                                                                        <i class="bi bi-trash-fill"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                                <tfoot class="bg-light">
                                                    <tr>
                                                        <td colspan="4" class="text-end fw-bold">Total:</td>
                                                        <td class="text-primary fw-bold" id="total-compra">{{ $config->currency_simbol }}.0.00</td>
                                                        <td></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <div id="no-articulos" class="alert alert-info m-3 {{ old('detalles') ? 'd-none' : '' }}">
                                            <i class="bi bi-info-circle me-2"></i> No hay artículos agregados. Seleccione artículos para incluir en el ingreso.
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones de acción -->
                                <div class="d-flex justify-content-center gap-3 mt-4">
                                    <a href="{{ url('ingresos') }}" class="btn btn-danger">
                                        <i class="bi bi-x-circle me-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success" id="btn-guardar">
                                        <i class="bi bi-check2-square me-1"></i> Guardar Ingreso
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script con pequeñas mejoras de usabilidad -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const articuloSelect = document.getElementById('articulo');
            const precioCompraInput = document.getElementById('precio_compra');
            const precioVentaInput = document.getElementById('precio_venta');
            const cantidadInput = document.getElementById('cantidad');
            const unidadAbreviaturaSpan = document.getElementById('unidad-abreviatura');
            const addDetalleBtn = document.getElementById('add-detalle');
            const detallesBody = document.getElementById('detalles-body');
            const currencySymbol = @json($config->currency_simbol); // Definir aquí para todo el ámbito

            // Inicializar select2
            $('.select2').select2();

            // Preseleccionar artículo si viene de la página de inventario
            @if(isset($articuloSeleccionado) && $articuloSeleccionado)
                // Establecer el valor en el select2
                $(articuloSelect).val('{{ $articuloSeleccionado->id }}').trigger('change');

                // Llenar campos relacionados
                precioCompraInput.value = '{{ $articuloSeleccionado->precio_compra }}';
                precioVentaInput.value = '{{ $articuloSeleccionado->precio_venta }}';
                unidadAbreviaturaSpan.textContent = '{{ $articuloSeleccionado->unidad->abreviatura }}';

                // Ajustar el tipo de entrada según la unidad
                @if($articuloSeleccionado->unidad->tipo == 'decimal')
                    cantidadInput.step = "0.01";
                    cantidadInput.min = "0.01";
                @else
                    cantidadInput.step = "1";
                    cantidadInput.min = "1";
                @endif

                // Enfocar el campo de cantidad para permitir un flujo rápido
                setTimeout(() => {
                    cantidadInput.focus();
                }, 500);
            @endif

            // Inicializar contador de índices usando old (si existen)
            let detalleIndex = {{ count(old('detalles', [])) }};

            // Actualizar total - definir la función primero
            function actualizarTotal() {
                let total = 0;
                const subtotales = detallesBody.querySelectorAll('.subtotal');
                subtotales.forEach(function (subtotal) {
                    // Extraer el valor numérico quitando el símbolo de moneda y formato
                    const subtotalText = subtotal.textContent.trim();

                    // Mejoramos la extracción del valor numérico
                    // Primero eliminamos el símbolo de moneda y cualquier punto después de él
                    let numeroStr = subtotalText;
                    if (numeroStr.startsWith(currencySymbol)) {
                        numeroStr = numeroStr.substring(currencySymbol.length);
                    }

                    // Si empieza con un punto (después del símbolo de moneda), lo eliminamos
                    if (numeroStr.startsWith('.')) {
                        numeroStr = numeroStr.substring(1);
                    }

                    // Reemplazamos las comas por vacío (para manejar miles)
                    numeroStr = numeroStr.replace(/,/g, '');

                    // Ahora convertimos a número
                    const valorNumerico = parseFloat(numeroStr);

                    if (!isNaN(valorNumerico)) {
                        total += valorNumerico;
                    }
                });

                const totalElement = document.getElementById('total-compra');
                if (totalElement) {
                    totalElement.innerHTML = `<strong>${currencySymbol}.${total.toFixed(2)}</strong>`;
                }
            }

            $(articuloSelect).on('select2:select', function (e) {
                const selectedOption = e.params.data;
                const precioCompra = selectedOption.element.getAttribute('data-precio-compra');
                const precioVenta = selectedOption.element.getAttribute('data-precio-venta');
                const unidadAbreviatura = selectedOption.element.getAttribute('data-unidad-abreviatura');
                const unidadTipo = selectedOption.element.getAttribute('data-unidad-tipo');

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
            });

            // Validar input cantidad
            cantidadInput.addEventListener('input', function (event) {
                const unidadTipo = articuloSelect.options[articuloSelect.selectedIndex].getAttribute('data-unidad-tipo');
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

            // Mejora: Mostrar/ocultar mensaje de "no hay artículos"
            function actualizarEstadoTabla() {
                const tieneArticulos = detallesBody.querySelectorAll('tr').length > 0;
                document.getElementById('no-articulos').classList.toggle('d-none', tieneArticulos);

                // Habilitar/deshabilitar botón guardar
                document.getElementById('btn-guardar').disabled = !tieneArticulos;
            }

            // Agregar detalle con botón (modifica el existente)
            addDetalleBtn.addEventListener('click', function () {
                const articuloId = articuloSelect.value;
                if (!articuloId) {
                    alert('Por favor, seleccione un artículo.');
                    return;
                }

                // Verificar si el artículo ya está en la tabla de detalles
                const exists = Array.from(detallesBody.querySelectorAll('input[name*="[articulo_id]"]'))
                                  .some(input => input.value === articuloId);
                if (exists) {
                    alert("El artículo ya está en la lista de detalles.");
                    return;
                }

                const articuloText = articuloSelect.options[articuloSelect.selectedIndex].text;
                const precioCompra = parseFloat(precioCompraInput.value);
                const precioVenta = parseFloat(precioVentaInput.value);
                const cantidad = parseFloat(cantidadInput.value);
                const unidadAbreviatura = unidadAbreviaturaSpan.textContent;

                if (isNaN(precioCompra) || isNaN(precioVenta) || isNaN(cantidad) ||
                    cantidad < (cantidadInput.step === "1" ? 1 : 0.01)) {
                    alert('Por favor, complete todos los campos correctamente.');
                    return;
                }

                const subtotal = precioCompra * cantidad;
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>
                        ${articuloText}
                        <input type="hidden" name="detalles[${detalleIndex}][articulo_id]" value="${articuloId}">
                    </td>
                    <td>
                        ${currencySymbol}.${precioCompra.toFixed(2)}
                        <input type="hidden" name="detalles[${detalleIndex}][precio_compra]" value="${precioCompra}">
                    </td>
                    <td>
                        ${currencySymbol}.${precioVenta.toFixed(2)}
                        <input type="hidden" name="detalles[${detalleIndex}][precio_venta]" value="${precioVenta}">
                    </td>
                    <td>
                        ${cantidad} ${unidadAbreviatura}
                        <input type="hidden" name="detalles[${detalleIndex}][cantidad]" value="${cantidad}">
                    </td>
                    <td class="subtotal">${currencySymbol}.${subtotal.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-detalle"><i class="bi bi-trash-fill"></i></button>
                    </td>`;
                detallesBody.appendChild(row);
                detalleIndex++;

                // Reiniciar campos
                articuloSelect.value = '';
                precioCompraInput.value = '';
                precioVentaInput.value = '';
                cantidadInput.value = '';
                unidadAbreviaturaSpan.textContent = '';
                $(articuloSelect).val('').trigger('change'); // Forma correcta de resetear Select2

                // Actualizar el total después de agregar el artículo
                actualizarTotal();
                actualizarEstadoTabla();
            });

            // Evento para eliminar detalles (modifica el existente)
            detallesBody.addEventListener('click', function (e) {
                const target = e.target;
                if (target.classList.contains('remove-detalle') || target.closest('.remove-detalle')) {
                    target.closest('tr').remove();
                    actualizarTotal();
                    actualizarEstadoTabla(); // Actualizar estado después de eliminar
                }
            });

            // Atajos de teclado para mejorar la experiencia
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

            // Actualizar estado inicial de la tabla
            actualizarEstadoTabla();

            // Calcular total inicial al cargar la página
            actualizarTotal();
        });
    </script>
@endsection
