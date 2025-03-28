@extends('layouts.admin')
@section('content')
    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-cart-plus"></i>
                </div>
                <div class="page-title">
                    <h5>Registrar Venta</h5>
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
                            <form action="{{ url('insert-venta') }}" method="POST">
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
                                        <label for="cliente_id" class="form-label">Cliente</label>
                                        <select class="form-control select2" id="cliente_id" name="cliente_id" required>
                                            <option value="">Seleccione un cliente</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}"
                                                    {{ (old('cliente_id') == $cliente->id || (isset($cliente_id) && $cliente_id == $cliente->id)) ? 'selected' : '' }}>
                                                    {{ $cliente->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="vehiculo_id" class="form-label">Vehículo</label>
                                        <select class="form-control select2" id="vehiculo_id" name="vehiculo_id" required>
                                            <option value="">Seleccione un vehículo</option>
                                            <!-- Los vehículos se cargarán dinámicamente al seleccionar un cliente -->
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tipo_venta" class="form-label">Tipo de Venta</label>
                                        <select class="form-control" id="tipo_venta" name="tipo_venta" required>
                                            <option value="Car Wash" {{ old('tipo_venta') == 'Car Wash' ? 'selected' : '' }}>Car Wash</option>
                                            <option value="CDS" {{ old('tipo_venta') == 'CDS' ? 'selected' : '' }}>CDS</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <input type="hidden" id="estado_pago" name="estado_pago" value="pendiente">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <h5>Detalles de la Venta</h5>
                                        <div class="table-responsive">
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <label for="articulo" class="form-label">Artículo</label>
                                                    <br>
                                                    <select class="form-control select2" id="articulo">
                                                        <option value="">Seleccione un artículo</option>
                                                        @foreach($todosArticulos as $articulo)
                                                            <option value="{{ $articulo->id }}"
                                                                    data-precio-venta="{{ $articulo->precio_venta }}"
                                                                    data-stock="{{ $articulo->stock }}"
                                                                    data-unidad="{{ $articulo->unidad->nombre }}"
                                                                    data-unidad-tipo="{{ $articulo->unidad->tipo }}"
                                                                    data-unidad-abreviatura="{{ $articulo->unidad->abreviatura }}"
                                                                    data-tipo-comision-trabajador="{{ $articulo->tipo_comision_trabajador_id }}"
                                                                    data-tipo-comision-vendedor="{{ $articulo->tipo_comision_vendedor_id }}">
                                                                {{ $articulo->codigo }} {{ $articulo->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="stock" class="form-label">Stock</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="stock" readonly>
                                                        <span class="input-group-text" id="stock-unidad-abreviatura"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="cantidad" class="form-label">Cantidad</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="cantidad" min="1" step="1">
                                                        <span class="input-group-text" id="unidad-abreviatura"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label for="precio_venta" class="form-label">Precio Venta</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">{{ $config->currency_simbol }}</span>
                                                        <input type="number" step="0.01" class="form-control" id="precio_venta" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="descuento_id" class="form-label">Descuento</label>
                                                    <br>
                                                    <select class="form-control select2" id="descuento_id">
                                                        <option value="">Sin descuento</option>
                                                        @foreach($descuentos as $descuento)
                                                            <option value="{{ $descuento->id }}"
                                                                    data-porcentaje="{{ $descuento->porcentaje_descuento }}">
                                                                {{ $descuento->nombre }} ({{ $descuento->porcentaje_descuento }}%)
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="trabajador_id" class="form-label">Trabajador</label>
                                                    <br>
                                                    <select class="form-control select2" id="trabajador_id">
                                                        <option value="">Seleccione un trabajador</option>
                                                        @foreach($trabajadores as $trabajador)
                                                            <option value="{{ $trabajador->id }}">
                                                                {{ $trabajador->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <input type="hidden" name="usuario_id" value="{{ Auth::id() }}">
                                                <input type="hidden" id="tipo_comision_trabajador_id">
                                                <input type="hidden" id="tipo_comision_usuario_id">
                                                <div class="col-md-12 mt-3 d-flex justify-content-end">
                                                    <button type="button" class="btn btn-primary" id="add-detalle"><i class="bi bi-plus-square"></i> Agregar Artículo</button>
                                                </div>
                                            </div>
                                            <table class="table align-middle table-striped flex-column" id="tabla-detalles">
                                                <thead>
                                                    <tr>
                                                        <th>Artículo</th>
                                                        <th>Cantidad</th>
                                                        <th>Precio</th>
                                                        <th>Descuento</th>
                                                        <th>Trabajador</th>
                                                        <th>Subtotal</th>
                                                        <th>Acciones</th>
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
                                                                    {{ $detalle['cantidad'] }}
                                                                    <input type="hidden" name="detalles[{{ $index }}][cantidad]" value="{{ $detalle['cantidad'] }}">
                                                                </td>
                                                                <td>
                                                                    {{ $config->currency_simbol }}{{ number_format($detalle['sub_total'] / $detalle['cantidad'], 2) }}
                                                                </td>
                                                                <td>
                                                                    @if(isset($detalle['descuento_id']) && $detalle['descuento_id'])
                                                                        @php
                                                                            $descuento = \App\Models\Descuento::find($detalle['descuento_id']);
                                                                            if ($descuento) {
                                                                                $precioUnitario = $detalle['sub_total'] / $detalle['cantidad'];
                                                                                $subtotalSinDescuento = $precioUnitario * $detalle['cantidad'];
                                                                                $porcentajeDescuento = $descuento->porcentaje_descuento;
                                                                                // Calculamos el monto de descuento basándonos en el subtotal original
                                                                                $factorDescuento = $porcentajeDescuento / 100;
                                                                                $montoDescuento = $subtotalSinDescuento * $factorDescuento / (1 - $factorDescuento);
                                                                                echo $descuento->nombre . ' (' . $porcentajeDescuento . '%) - ' . $config->currency_simbol . '.' . number_format($montoDescuento, 2);
                                                                            } else {
                                                                                echo 'Sin descuento';
                                                                            }
                                                                        @endphp
                                                                    @else
                                                                        Sin descuento
                                                                    @endif
                                                                    <input type="hidden" name="detalles[{{ $index }}][descuento_id]" value="{{ $detalle['descuento_id'] ?? '' }}">
                                                                </td>
                                                                <td>
                                                                    {{ $detalle['trabajador_id'] }}
                                                                    <input type="hidden" name="detalles[{{ $index }}][trabajador_id]" value="{{ $detalle['trabajador_id'] }}">
                                                                    <input type="hidden" name="detalles[{{ $index }}][usuario_id]" value="{{ $detalle['usuario_id'] }}">
                                                                    <input type="hidden" name="detalles[{{ $index }}][tipo_comision_trabajador_id]" value="{{ $detalle['tipo_comision_trabajador_id'] }}">
                                                                    <input type="hidden" name="detalles[{{ $index }}][tipo_comision_usuario_id]" value="{{ $detalle['tipo_comision_usuario_id'] }}">
                                                                </td>
                                                                <td class="subtotal">
                                                                    {{ $config->currency_simbol }}{{ number_format($detalle['sub_total'], 2) }}
                                                                    <input type="hidden" name="detalles[{{ $index }}][sub_total]" value="{{ $detalle['sub_total'] }}">
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
                                        <a href="{{ url('ventas')  }}" class="btn btn-danger"><i class="bi bi-x-circle"></i> Cancelar</a>
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
            const clienteSelect = document.getElementById('cliente_id');
            const vehiculoSelect = document.getElementById('vehiculo_id');
            const articuloSelect = document.getElementById('articulo');
            const stockInput = document.getElementById('stock');
            const precioVentaInput = document.getElementById('precio_venta');
            const cantidadInput = document.getElementById('cantidad');
            const trabajadorSelect = document.getElementById('trabajador_id');
            const tipoComisionTrabajadorInput = document.getElementById('tipo_comision_trabajador_id');
            const tipoComisionUsuarioInput = document.getElementById('tipo_comision_usuario_id');
            const unidadAbreviaturaSpan = document.getElementById('unidad-abreviatura');
            const stockUnidadAbreviaturaSpan = document.getElementById('stock-unidad-abreviatura');
            const addDetalleBtn = document.getElementById('add-detalle');
            const detallesBody = document.getElementById('detalles-body');
            const descuentoSelect = document.getElementById('descuento_id');

            // Inicializar select2
            $('.select2').select2();

            // Inicializar contador de índices usando old (si existen)
            let detalleIndex = {{ count(old('detalles', [])) }};

            // Cargar vehículos cuando se selecciona un cliente
            $(clienteSelect).on('select2:select', function (e) {
                const clienteId = e.target.value;
                console.log('Cliente seleccionado:', clienteId);

                // Agregar el token CSRF a todas las solicitudes AJAX
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: `/api/clientes/${clienteId}/vehiculos`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log('Vehículos recibidos:', data);
                        vehiculoSelect.innerHTML = '<option value="">Seleccione un vehículo</option>';

                        if (data && data.length > 0) {
                            data.forEach(function(vehiculo) {
                                const option = document.createElement('option');
                                option.value = vehiculo.id;

                                // Asegurarse de que los campos existan
                                const marca = vehiculo.marca || '';
                                const modelo = vehiculo.modelo || '';
                                const placa = vehiculo.placa || '';

                                option.textContent = `${marca} ${modelo} - ${placa}`;
                                vehiculoSelect.appendChild(option);
                            });
                        } else {
                            // Agregar un mensaje si no hay vehículos
                            const option = document.createElement('option');
                            option.disabled = true;
                            option.textContent = 'No hay vehículos registrados para este cliente';
                            vehiculoSelect.appendChild(option);
                        }

                        // Actualizar el select2
                        $(vehiculoSelect).trigger('change');
                    },
                    error: function(error) {
                        console.error('Error al cargar los vehículos:', error);
                        alert('Error al cargar los vehículos. Por favor, intente nuevamente.');
                    }
                });
            });

            // Cuando se selecciona un artículo
            $(articuloSelect).on('select2:select', function (e) {
                const selectedOption = e.params.data;
                const stock = selectedOption.element.getAttribute('data-stock');
                const precioVenta = selectedOption.element.getAttribute('data-precio-venta');
                const unidadAbreviatura = selectedOption.element.getAttribute('data-unidad-abreviatura');
                const unidadTipo = selectedOption.element.getAttribute('data-unidad-tipo');
                const tipoComisionTrabajador = selectedOption.element.getAttribute('data-tipo-comision-trabajador');
                const tipoComisionVendedor = selectedOption.element.getAttribute('data-tipo-comision-vendedor');

                stockInput.value = stock;
                precioVentaInput.value = precioVenta;
                unidadAbreviaturaSpan.textContent = unidadAbreviatura;
                stockUnidadAbreviaturaSpan.textContent = unidadAbreviatura;
                tipoComisionTrabajadorInput.value = tipoComisionTrabajador;
                tipoComisionUsuarioInput.value = tipoComisionVendedor;

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
                const stock = parseFloat(stockInput.value);
                const value = event.target.value;
                const cantidad = parseFloat(value);
                const cursorPosition = event.target.selectionStart;

                if (unidadTipo === 'unidad') {
                    // Para unidades, solo permitir números enteros
                    event.target.value = value.replace(/[^0-9]/g, '');
                    if (event.target.value === '') event.target.value = '1';
                    if (parseInt(event.target.value) < 1) event.target.value = '1';
                } else {
                    // Para decimales, permitir punto decimal
                    const decimalValue = value.replace(/[^0-9.]/g, '');
                    const parts = decimalValue.split('.');
                    event.target.value = parts.length > 2 ? parts[0] + '.' + parts.slice(1).join('') : decimalValue;
                }

                // Validar que la cantidad no exceda el stock
                if (cantidad > stock) {
                    alert('La cantidad no puede exceder el stock disponible: ' + stock);
                    event.target.value = stock;
                }

                event.target.setSelectionRange(cursorPosition, cursorPosition);
            });

            // Agregar detalle con botón
            addDetalleBtn.addEventListener('click', function () {
                const articuloId = articuloSelect.value;
                const trabajadorId = trabajadorSelect.value;
                const descuentoId = descuentoSelect.value;

                // Verificar si el artículo ya está en la tabla de detalles
                const exists = Array.from(detallesBody.querySelectorAll('input[name*="[articulo_id]"]'))
                                  .some(input => input.value === articuloId);
                if (exists) {
                    alert("El artículo ya está en la lista de detalles.");
                    return;
                }

                if (!articuloId || !trabajadorId) {
                    alert('Por favor, seleccione un artículo y un trabajador.');
                    return;
                }

                const articuloText = articuloSelect.options[articuloSelect.selectedIndex].text;
                const trabajadorText = trabajadorSelect.options[trabajadorSelect.selectedIndex].text;
                const precioVenta = parseFloat(precioVentaInput.value);
                const cantidad = parseFloat(cantidadInput.value);
                const unidadAbreviatura = unidadAbreviaturaSpan.textContent;
                const tipoComisionTrabajadorId = tipoComisionTrabajadorInput.value;
                const tipoComisionUsuarioId = tipoComisionUsuarioInput.value;

                if (isNaN(precioVenta) || isNaN(cantidad) ||
                    cantidad < (cantidadInput.step === "1" ? 1 : 0.01)) {
                    alert('Por favor, complete todos los campos correctamente.');
                    return;
                }

                // Calcular el subtotal original sin descuento
                const subtotalOriginal = precioVenta * cantidad;
                let subtotal = subtotalOriginal;
                let descuentoText = 'Sin descuento';
                let porcentajeDescuento = 0;
                let montoDescuento = 0;
                const currencySymbol = @json($config->currency_simbol);

                // Aplicar descuento si existe
                if (descuentoId) {
                    const descuentoOption = descuentoSelect.options[descuentoSelect.selectedIndex];
                    porcentajeDescuento = parseFloat(descuentoOption.getAttribute('data-porcentaje'));
                    const descuentoNombre = descuentoOption.textContent;

                    // Calcular monto de descuento
                    montoDescuento = subtotalOriginal * (porcentajeDescuento / 100);

                    // Aplicar el descuento al subtotal
                    subtotal = subtotalOriginal - montoDescuento;

                    // Mostrar el nombre del descuento, el porcentaje y el monto en quetzales
                    descuentoText = `${descuentoNombre} - ${currencySymbol}.${montoDescuento.toFixed(2)}`;

                    // Asegurarse de que el subtotal no sea negativo
                    if (subtotal < 0) subtotal = 0;
                }

                const row = document.createElement('tr');
                const usuarioId = {{ Auth::id() }}; // Usuario autenticado

                row.innerHTML = `
                    <td>
                        ${articuloText}
                        <input type="hidden" name="detalles[${detalleIndex}][articulo_id]" value="${articuloId}">
                    </td>
                    <td>
                        ${cantidad} ${unidadAbreviatura}
                        <input type="hidden" name="detalles[${detalleIndex}][cantidad]" value="${cantidad}">
                    </td>
                    <td>
                        ${currencySymbol}.${precioVenta.toFixed(2)}
                    </td>
                    <td>
                        ${descuentoText}
                        <input type="hidden" name="detalles[${detalleIndex}][descuento_id]" value="${descuentoId || ''}">
                    </td>
                    <td>
                        ${trabajadorText}
                        <input type="hidden" name="detalles[${detalleIndex}][trabajador_id]" value="${trabajadorId}">
                        <input type="hidden" name="detalles[${detalleIndex}][usuario_id]" value="${usuarioId}">
                        <input type="hidden" name="detalles[${detalleIndex}][tipo_comision_trabajador_id]" value="${tipoComisionTrabajadorId}">
                        <input type="hidden" name="detalles[${detalleIndex}][tipo_comision_usuario_id]" value="${tipoComisionUsuarioId}">
                    </td>
                    <td class="subtotal">
                        ${currencySymbol}.${subtotal.toFixed(2)}
                        <input type="hidden" name="detalles[${detalleIndex}][sub_total]" value="${subtotal}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-detalle"><i class="bi bi-trash-fill"></i></button>
                    </td>`;
                detallesBody.appendChild(row);
                detalleIndex++;

                // Reiniciar campos
                articuloSelect.value = '';
                trabajadorSelect.value = '';
                descuentoSelect.value = '';
                stockInput.value = '';
                precioVentaInput.value = '';
                cantidadInput.value = '';
                unidadAbreviaturaSpan.textContent = '';
                stockUnidadAbreviaturaSpan.textContent = '';
                tipoComisionTrabajadorInput.value = '';
                tipoComisionUsuarioInput.value = '';
                $('.select2').trigger('change');
                actualizarTotal();
            });

            // Eliminar detalle
            detallesBody.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-detalle') || e.target.closest('.remove-detalle')) {
                    e.target.closest('tr').remove();
                    actualizarTotal();
                }
            });

            // Actualizar total
            function actualizarTotal() {
                let total = 0;
                const subtotales = detallesBody.querySelectorAll('.subtotal input[name*="[sub_total]"]');
                subtotales.forEach(function (subtotal) {
                    total += parseFloat(subtotal.value);
                });

                let totalElement = document.getElementById('total-amount');
                if (totalElement) {
                    totalElement.textContent = `${@json($config->currency_simbol)}.${total.toFixed(2)}`;
                } else {
                    const totalContainer = document.createElement('div');
                    totalContainer.classList.add('mt-3', 'd-flex', 'justify-content-end');
                    totalContainer.innerHTML = `
                        <div class="h5">
                            <strong>Total:</strong> <span id="total-amount">${@json($config->currency_simbol)}.${total.toFixed(2)}</span>
                        </div>`;
                    document.querySelector('#tabla-detalles').parentNode.appendChild(totalContainer);
                }
            }

            // Cargar datos iniciales
            if ({{ count(old('detalles', [])) }} > 0) {
                actualizarTotal();
            }

            // Si hay un cliente_id en la URL, disparar el evento change para cargar sus vehículos
            @if(isset($cliente_id) && $cliente_id)
                // Esperar a que select2 esté inicializado
                setTimeout(function() {
                    // Seleccionar cliente
                    $('#cliente_id').val('{{ $cliente_id }}').trigger('change');

                    // Si el cliente tiene vehículos, disparar el evento para cargarlos
                    $(clienteSelect).trigger('select2:select');
                }, 500);
            @endif
        });
    </script>
@endsection
