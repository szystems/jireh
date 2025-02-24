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
                        <div class="card-body">
                            @if (count($errors)>0)
                                <div class="alert alert-danger text-white" role="alert">
                                    <ul>
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
                                        <label for="numero_factura" class="form-label">Número de Factura</label>
                                        <input type="text" class="form-control" id="numero_factura" name="numero_factura" value="{{ old('numero_factura') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha" class="form-label">Fecha</label>
                                        <input type="date" class="form-control" id="fecha" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="proveedor" class="form-label">Proveedor</label>
                                        <select class="form-control select2" id="proveedor_id" name="proveedor_id" required>
                                            @foreach ($proveedores as $proveedor)
                                                <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                                    {{ $proveedor->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tipo_compra" class="form-label">Tipo de Compra</label>
                                        <select class="form-control" id="tipo_compra" name="tipo_compra" required>
                                            <option value="Car Wash" {{ old('tipo_compra') == 'Car Wash' ? 'selected' : '' }}>Car Wash</option>
                                            <option value="CDS" {{ old('tipo_compra') == 'CDS' ? 'selected' : '' }}>CDS</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <h5>Detalles del Ingreso</h5>
                                        <div class="table-responsive">

                                            <div class="row mb-3">
                                                <div class="col-md-4">
                                                    <label for="articulo" class="form-label">Artículo</label>
                                                    <select class="form-control select2" id="articulo">
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
                                                    <label for="precio_compra" class="form-label">Precio Compra</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">{{ $config->currency_simbol }}</span>
                                                        <input type="number" step="0.01" class="form-control" id="precio_compra">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="precio_venta" class="form-label">Precio Venta</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">{{ $config->currency_simbol }}</span>
                                                        <input type="number" step="0.01" class="form-control" id="precio_venta">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="cantidad" class="form-label">Cantidad</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="cantidad" min="1" step="1">
                                                        <span class="input-group-text" id="unidad-abreviatura"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" class="btn btn-primary" id="add-detalle"><i class="bi bi-plus-square"></i> Agregar Artículo</button>
                                                </div>
                                            </div>
                                            <table class="table align-middle table-striped flex-column" id="tabla-detalles">
                                                <thead>
                                                    <tr>
                                                        <th>Artículo</th>
                                                        <th>Precio Compra</th>
                                                        <th>Precio Venta</th>
                                                        <th>Cantidad</th>
                                                        <th>Subtotal</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <!-- filepath: /c:/Users/szott/Dropbox/Desarrollo/jireh/resources/views/admin/ingreso/create.blade.php -->
                                                <tbody id="detalles-body">
                                                    @if(old('detalles'))
                                                        @foreach(old('detalles') as $index => $detalle)
                                                            <tr>
                                                                <td>
                                                                    <!-- Si dispones del nombre o código del artículo, puedes mostrarlo; de lo contrario, se muestra el ID -->
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
                                                                <td class="subtotal">
                                                                    {{ $config->currency_simbol }}{{ number_format($detalle['precio_compra'] * $detalle['cantidad'], 2) }}
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-danger btn-sm remove-detalle"><i class="bi bi-trash-fill"></i></button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <a href="{{ url('ingresos')  }}" class="btn btn-danger"><i class="bi bi-x-circle"></i> Cancelar</a>
                                        <button type="submit" class="btn btn-success"><i class="bi bi-check2-square"></i> Guardar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const articuloSelect = document.getElementById('articulo');
            const precioCompraInput = document.getElementById('precio_compra');
            const precioVentaInput = document.getElementById('precio_venta');
            const cantidadInput = document.getElementById('cantidad');
            const unidadAbreviaturaSpan = document.getElementById('unidad-abreviatura');
            const addDetalleBtn = document.getElementById('add-detalle');
            const detallesBody = document.getElementById('detalles-body');

            // Inicializar select2
            $('.select2').select2();

            // Inicializar contador de índices usando old (si existen)
            let detalleIndex = {{ count(old('detalles', [])) }};

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

            // Agregar detalle con botón
            addDetalleBtn.addEventListener('click', function () {
                const articuloId = articuloSelect.value;
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
                const currencySymbol = @json($config->currency_simbol);
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
                $('.select2').select2();
                actualizarTotal();
            });

            // Eliminar detalle
            detallesBody.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-detalle')) {
                    e.target.closest('tr').remove();
                    actualizarTotal();
                }
            });

            // Actualizar total
            function actualizarTotal() {
                let total = 0;
                const subtotales = detallesBody.querySelectorAll('.subtotal');
                subtotales.forEach(function (subtotal) {
                    total += parseFloat(subtotal.textContent);
                });
                let totalElement = document.getElementById('total');
                if (totalElement) {
                    totalElement.textContent = total.toFixed(2);
                } else {
                    const newTotalElement = document.createElement('div');
                    newTotalElement.innerHTML = `<strong>Total:</strong> <span id="total">${total.toFixed(2)}</span>`;
                    detallesBody.parentElement.appendChild(newTotalElement);
                }
            }
        });
    </script>
@endsection
