@extends('layouts.admin')
@section('content')
    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="page-title">
                    <h5>Editar Venta @if ($venta->numero_factura) - Factura: {{ $venta->numero_factura }} @endif</h5>
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
                            <form action="{{ url('update-venta/'.$venta->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row gx-3">
                                    <!-- Información básica de la venta -->
                                    <div class="col-md-6 mb-3">
                                        <label for="numero_factura" class="form-label">Número de Factura</label>
                                        <input type="text" class="form-control" id="numero_factura" name="numero_factura" value="{{ old('numero_factura', $venta->numero_factura) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha" class="form-label">Fecha</label>
                                        <input type="date" class="form-control" id="fecha" name="fecha" value="{{ old('fecha', $venta->fecha) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cliente_id" class="form-label">Cliente</label>
                                        <select class="form-control select2" id="cliente_id" name="cliente_id" required>
                                            <option value="">Seleccione un cliente</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ (old('cliente_id', $venta->cliente_id) == $cliente->id) ? 'selected' : '' }}>
                                                    {{ $cliente->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="vehiculo_id" class="form-label">Vehículo</label>
                                        <select class="form-control select2" id="vehiculo_id" name="vehiculo_id" required>
                                            <option value="">Seleccione un vehículo</option>
                                            @if($venta->vehiculo)
                                                <option value="{{ $venta->vehiculo_id }}" selected>
                                                    {{ $venta->vehiculo->marca }} {{ $venta->vehiculo->modelo }} - {{ $venta->vehiculo->placa }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tipo_venta" class="form-label">Tipo de Venta</label>
                                        <select class="form-control" id="tipo_venta" name="tipo_venta" required>
                                            <option value="Car Wash" {{ (old('tipo_venta', $venta->tipo_venta) == 'Car Wash') ? 'selected' : '' }}>Car Wash</option>
                                            <option value="CDS" {{ (old('tipo_venta', $venta->tipo_venta) == 'CDS') ? 'selected' : '' }}>CDS</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="estado_pago" class="form-label">Estado de Pago</label>
                                        <select class="form-control" id="estado_pago" name="estado_pago" required>
                                            <option value="pendiente" {{ (old('estado_pago', $venta->estado_pago) == 'pendiente') ? 'selected' : '' }}>Pendiente</option>
                                            <option value="pagado" {{ (old('estado_pago', $venta->estado_pago) == 'pagado') ? 'selected' : '' }}>Pagado</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="estado" value="{{ $venta->estado }}">
                                    <input type="hidden" name="usuario_id" value="{{ $venta->usuario_id }}">

                                    <!-- Detalles existentes de la venta -->
                                    <div class="col-md-12 mb-3">
                                        <h5>Detalles de la Venta</h5>

                                        @if($venta->detalleVentas->count() > 0)
                                            <h6 class="mt-4 mb-3">Detalles Existentes</h6>
                                            <div class="table-responsive mb-4">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Artículo</th>
                                                            <th>Cantidad</th>
                                                            <th>Precio</th>
                                                            <th>Descuento</th>
                                                            <th>Subtotal</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($venta->detalleVentas as $detalle)
                                                            @php
                                                                // Calcular precio unitario correctamente
                                                                $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);

                                                                // Calcular subtotal sin descuento
                                                                $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

                                                                // Calcular descuento si existe
                                                                $montoDescuento = 0;
                                                                if($detalle->descuento_id) {
                                                                    $descuento = \App\Models\Descuento::find($detalle->descuento_id);
                                                                    if($descuento) {
                                                                        $porcentajeDescuento = $descuento->porcentaje_descuento;
                                                                        $montoDescuento = $subtotalSinDescuento * ($porcentajeDescuento / 100);
                                                                    }
                                                                }

                                                                // Calcular subtotal final
                                                                $subtotalFinal = $subtotalSinDescuento - $montoDescuento;

                                                                // Obtener el tipo de unidad
                                                                $unidadTipo = $detalle->articulo && $detalle->articulo->unidad ? $detalle->articulo->unidad->tipo : 'decimal';
                                                                $step = $unidadTipo === 'unidad' ? '1' : '0.01';
                                                                $min = $unidadTipo === 'unidad' ? '1' : '0.01';
                                                            @endphp
                                                            <tr id="detalle-row-{{ $detalle->id }}" class="detalle-existente">
                                                                <td>
                                                                    @if($detalle->articulo)
                                                                        {{ $detalle->articulo->nombre }}
                                                                    @else
                                                                        Artículo no disponible
                                                                    @endif
                                                                    <input type="hidden" name="detalles[{{ $detalle->id }}][articulo_id]" value="{{ $detalle->articulo_id }}">
                                                                </td>
                                                                <td>
                                                                    <input type="number" class="form-control cantidad-input"
                                                                        name="detalles[{{ $detalle->id }}][cantidad]"
                                                                        value="{{ $detalle->cantidad }}"
                                                                        min="{{ $min }}" step="{{ $step }}"
                                                                        data-detalle-id="{{ $detalle->id }}"
                                                                        data-precio="{{ $precioUnitario }}"
                                                                        data-descuento-id="{{ $detalle->descuento_id }}"
                                                                        data-unidad-tipo="{{ $unidadTipo }}">
                                                                </td>
                                                                <td>{{ $config->currency_simbol }}.{{ number_format($precioUnitario, 2) }}</td>
                                                                <td>
                                                                    <select class="form-control select2 descuento-select"
                                                                        name="detalles[{{ $detalle->id }}][descuento_id]"
                                                                        data-detalle-id="{{ $detalle->id }}">
                                                                        <option value="">Sin descuento</option>
                                                                        @foreach($descuentos as $descuento)
                                                                            <option value="{{ $descuento->id }}"
                                                                                data-porcentaje="{{ $descuento->porcentaje_descuento }}"
                                                                                {{ $detalle->descuento_id == $descuento->id ? 'selected' : '' }}>
                                                                                {{ $descuento->nombre }} ({{ $descuento->porcentaje_descuento }}%)
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td class="subtotal-cell" id="subtotal-{{ $detalle->id }}">
                                                                    {{ $config->currency_simbol }}.{{ number_format($subtotalFinal, 2) }}
                                                                    <input type="hidden" name="detalles[{{ $detalle->id }}][sub_total]" value="{{ $subtotalFinal }}" class="subtotal-input">
                                                                    <input type="hidden" name="detalles[{{ $detalle->id }}][usuario_id]" value="{{ $detalle->usuario_id }}">
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-danger btn-sm eliminar-detalle" data-detalle-id="{{ $detalle->id }}">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                    <input type="hidden" name="detalles[{{ $detalle->id }}][eliminar]" value="0" id="eliminar-{{ $detalle->id }}">
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif

                                        <!-- Formulario para agregar nuevos detalles -->
                                        <div class="card bg-light p-3 mb-3">
                                            <h6>Agregar Nuevo Detalle</h6>
                                            <div class="row">
                                                <div class="col-md-5 mb-2">
                                                    <label for="articulo">Artículo</label>
                                                    <select id="articulo" class="form-control select2">
                                                        <option value="">Seleccione un artículo</option>
                                                        @foreach($todosArticulos as $articulo)
                                                            <option value="{{ $articulo->id }}"
                                                                data-precio="{{ $articulo->precio_venta }}"
                                                                data-stock="{{ $articulo->stock }}"
                                                                data-unidad="{{ $articulo->unidad->abreviatura ?? '' }}"
                                                                data-unidad-tipo="{{ $articulo->unidad->tipo ?? 'decimal' }}">
                                                                {{ $articulo->codigo }} - {{ $articulo->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <label for="stock">Stock</label>
                                                    <div class="input-group">
                                                        <input type="text" id="stock" class="form-control" readonly>
                                                        <span class="input-group-text" id="unidad-abreviatura"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <label for="cantidad-nuevo">Cantidad</label>
                                                    <input type="number" id="cantidad-nuevo" class="form-control" min="0.01" step="0.01">
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label for="descuento-nuevo">Descuento</label>
                                                    <select id="descuento-nuevo" class="form-control select2">
                                                        <option value="">Sin descuento</option>
                                                        @foreach($descuentos as $descuento)
                                                            <option value="{{ $descuento->id }}" data-porcentaje="{{ $descuento->porcentaje_descuento }}">
                                                                {{ $descuento->nombre }} ({{ $descuento->porcentaje_descuento }}%)
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-12 text-end mt-2">
                                                    <button type="button" id="agregar-detalle" class="btn btn-primary">
                                                        <i class="bi bi-plus-circle"></i> Agregar Detalle
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tabla para nuevos detalles -->
                                        <div id="nuevos-detalles-container" style="display: none;">
                                            <h6 class="mt-4 mb-3">Nuevos Detalles</h6>
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="tabla-nuevos-detalles">
                                                    <thead>
                                                        <tr>
                                                            <th>Artículo</th>
                                                            <th>Cantidad</th>
                                                            <th>Precio</th>
                                                            <th>Descuento</th>
                                                            <th>Subtotal</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="nuevos-detalles">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <a href="{{ url('ventas')  }}" class="btn btn-danger"><i class="bi bi-x-circle"></i> Cancelar</a>
                                                <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Guardar Cambios</button>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <h4 id="total-venta" class="text-primary">
                                                    Total: {{ $config->currency_simbol }}.{{ number_format($venta->detalleVentas->sum('sub_total'), 2) }}
                                                </h4>
                                            </div>
                                        </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Variables globales
            const currencySymbol = '{{ $config->currency_simbol }}';
            let nuevoDetalleCount = 0;

            // Inicializar select2
            $('.select2').select2();

            // Cargar vehículos cuando se selecciona un cliente
            $('#cliente_id').on('change', function() {
                const clienteId = $(this).val();
                if (!clienteId) return;

                // Realizar petición AJAX para obtener los vehículos
                $.ajax({
                    url: `/api/clientes/${clienteId}/vehiculos`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Limpiar opciones actuales
                        $('#vehiculo_id').empty().append('<option value="">Seleccione un vehículo</option>');

                        // Agregar nuevas opciones
                        if (data && data.length > 0) {
                            data.forEach(function(vehiculo) {
                                $('#vehiculo_id').append(`<option value="${vehiculo.id}">${vehiculo.marca} ${vehiculo.modelo} - ${vehiculo.placa}</option>`);
                            });
                        }

                        // Si hay un vehículo seleccionado previamente, intentar seleccionarlo
                        const vehiculoSeleccionado = '{{ $venta->vehiculo_id }}';
                        if (vehiculoSeleccionado) {
                            $('#vehiculo_id').val(vehiculoSeleccionado).trigger('change');
                        }
                    },
                    error: function(error) {
                        console.error('Error al cargar vehículos:', error);
                    }
                });
            });

            // Disparar evento change si ya hay un cliente seleccionado
            if ($('#cliente_id').val()) {
                $('#cliente_id').trigger('change');
            }

            // Actualizar información al seleccionar un artículo
            $('#articulo').on('change', function() {
                const articuloId = $(this).val();
                if (!articuloId) {
                    $('#stock').val('');
                    $('#unidad-abreviatura').text('');
                    return;
                }

                const option = $(this).find('option:selected');
                const stock = option.data('stock');
                const unidad = option.data('unidad');
                const unidadTipo = option.data('unidad-tipo') || 'decimal'; // Si no está definido, asumimos decimal

                // Configurar el input de cantidad según el tipo de unidad
                const cantidadInput = $('#cantidad-nuevo');
                if (unidadTipo === 'unidad') {
                    cantidadInput.attr('step', '1');
                    cantidadInput.attr('min', '1');
                    cantidadInput.val(Math.floor(cantidadInput.val() || 1)); // Convierte a entero si hay un valor previo
                } else {
                    cantidadInput.attr('step', '0.01');
                    cantidadInput.attr('min', '0.01');
                }

                $('#stock').val(stock);
                $('#unidad-abreviatura').text(unidad);
            });

            // Validación del input de cantidad para respetar el tipo de unidad
            $('#cantidad-nuevo').on('input', function(e) {
                const articuloId = $('#articulo').val();
                if (!articuloId) return;

                const option = $('#articulo').find('option:selected');
                const unidadTipo = option.data('unidad-tipo') || 'decimal';

                if (unidadTipo === 'unidad') {
                    // Para unidades, solo permitir números enteros
                    this.value = this.value.replace(/[^0-9]/g, '');
                    if (this.value === '') this.value = '1';
                    if (parseInt(this.value) < 1) this.value = '1';
                } else {
                    // Para decimales, permitir punto decimal y validar formato
                    const parts = this.value.split('.');
                    if (parts.length > 2) {
                        this.value = parts[0] + '.' + parts.slice(1).join('');
                    }
                    // Asegurarse que sea al menos 0.01
                    if (parseFloat(this.value) < 0.01) this.value = '0.01';
                }

                // También podríamos validar contra el stock disponible aquí
                const stock = parseFloat(option.data('stock') || 0);
                if (parseFloat(this.value) > stock) {
                    alert(`La cantidad no puede exceder el stock disponible: ${stock}`);
                    this.value = stock.toString();
                }
            });

            // Evento para eliminar detalles existentes
            $('.eliminar-detalle').on('click', function() {
                const detalleId = $(this).data('detalle-id');
                const row = $(`#detalle-row-${detalleId}`);

                // Añadir un campo oculto para el detalle a eliminar con un nombre que el controlador procesará correctamente
                if ($(`input[name="detalles_a_eliminar[]"][value="${detalleId}"]`).length === 0) {
                    $('form').append(`<input type="hidden" name="detalles_a_eliminar[]" value="${detalleId}">`);
                }

                // Deshabilitar y renombrar los campos para que no se envíen
                row.find('input, select').prop('disabled', true);

                // Ocultar la fila completamente
                row.hide();

                // Insertar fila de confirmación de eliminación
                const confirmRow = $(`<tr id="confirm-row-${detalleId}" class="table-danger">
                    <td colspan="7" class="text-center">
                        <span class="text-danger">Artículo eliminado</span>
                        <button type="button" class="btn btn-sm btn-secondary ms-3 restaurar-detalle" data-detalle-id="${detalleId}">
                            <i class="bi bi-arrow-counterclockwise"></i> Restaurar
                        </button>
                    </td>
                </tr>`);

                row.after(confirmRow);
                actualizarTotal();
            });

            // Corregimos la delegación de eventos para los botones de restaurar
            // Este es el problema principal: usamos `$(document).on()` en lugar de sólo `.on()`
            $(document).on('click', '.restaurar-detalle', function() {
                const detalleId = $(this).data('detalle-id');
                console.log('Restaurando detalle ID:', detalleId); // Debugging
                restaurarDetalle(detalleId);
            });

            // Función para restaurar detalle
            function restaurarDetalle(detalleId) {
                const row = $(`#detalle-row-${detalleId}`);
                const confirmRow = $(`#confirm-row-${detalleId}`);

                console.log('Encontrado row:', row.length); // Debugging
                console.log('Encontrado confirmRow:', confirmRow.length); // Debugging

                // Eliminar el campo oculto que marca para eliminación
                $(`input[name="detalles_a_eliminar[]"][value="${detalleId}"]`).remove();

                // Rehabilitar los campos
                row.find('input, select').prop('disabled', false);

                // Mostrar la fila original
                row.show();

                // Eliminar la fila de confirmación
                confirmRow.remove();

                // Actualizar el total
                actualizarTotal();
            }

            // Recalcular subtotales cuando se cambia la cantidad o descuento en detalles existentes
            $('.cantidad-input, .descuento-select').on('change', function() {
                const detalleId = $(this).data('detalle-id');
                recalcularSubtotal(detalleId);
            });

            // Función para recalcular subtotal de un detalle
            function recalcularSubtotal(detalleId) {
                const cantidadInput = $(`.cantidad-input[data-detalle-id="${detalleId}"]`);
                const descuentoSelect = $(`.descuento-select[data-detalle-id="${detalleId}"]`);

                const cantidad = parseFloat(cantidadInput.val());
                const precioUnitario = parseFloat(cantidadInput.data('precio'));
                const descuentoId = descuentoSelect.val();

                // Calcular subtotal sin descuento (precio unitario × cantidad)
                let subtotalSinDescuento = precioUnitario * cantidad;
                let montoDescuento = 0;
                let subtotal = subtotalSinDescuento;

                // Aplicar descuento si existe
                if (descuentoId) {
                    const porcentajeDescuento = parseFloat(descuentoSelect.find('option:selected').data('porcentaje'));
                    montoDescuento = subtotalSinDescuento * (porcentajeDescuento / 100);
                    subtotal = subtotalSinDescuento - montoDescuento;
                }

                // Actualizar el valor mostrado y el input hidden
                $(`#subtotal-${detalleId}`).html(
                    `${currencySymbol}.${subtotal.toFixed(2)}
                    <input type="hidden" name="detalles[${detalleId}][sub_total]" value="${subtotal}" class="subtotal-input">`
                );

                actualizarTotal();
            }

            // Agregar nuevo detalle
            $('#agregar-detalle').on('click', function() {
                const articuloId = $('#articulo').val();
                if (!articuloId) {
                    alert('Debe seleccionar un artículo');
                    return;
                }

                // Verificar si el artículo ya existe en los detalles existentes
                let articuloExistente = false;

                // Comprobar en detalles existentes visibles (no eliminados)
                $('.detalle-existente:visible').each(function() {
                    const articuloIdExistente = $(this).find('input[name$="[articulo_id]"]').val();
                    if (articuloIdExistente === articuloId) {
                        articuloExistente = true;
                        return false; // Salir del bucle
                    }
                });

                // Comprobar en nuevos detalles
                if (!articuloExistente) {
                    $('#nuevos-detalles input[name$="[articulo_id]"]').each(function() {
                        if ($(this).val() === articuloId) {
                            articuloExistente = true;
                            return false; // Salir del bucle
                        }
                    });
                }

                if (articuloExistente) {
                    alert('Este artículo ya está agregado a la venta. Si desea modificar la cantidad, edite el detalle existente.');
                    return;
                }

                const cantidad = parseFloat($('#cantidad-nuevo').val());
                if (isNaN(cantidad) || cantidad <= 0) {
                    alert('Ingrese una cantidad válida');
                    return;
                }

                // Obtener datos del artículo
                const articuloOption = $('#articulo option:selected');
                const articuloNombre = articuloOption.text();
                const precioUnitario = parseFloat(articuloOption.data('precio'));

                // Calcular subtotal sin descuento (precio unitario × cantidad)
                let subtotalSinDescuento = precioUnitario * cantidad;
                let montoDescuento = 0;
                let subtotal = subtotalSinDescuento;
                let descuentoTexto = 'Sin descuento';

                // Aplicar descuento si existe
                let descuentoId = $('#descuento-nuevo').val();
                if (descuentoId) {
                    const descuentoOption = $('#descuento-nuevo option:selected');
                    const porcentajeDescuento = parseFloat(descuentoOption.data('porcentaje'));
                    montoDescuento = subtotalSinDescuento * (porcentajeDescuento / 100);
                    subtotal = subtotalSinDescuento - montoDescuento;
                    descuentoTexto = `${descuentoOption.text()} - ${currencySymbol}.${montoDescuento.toFixed(2)}`;
                }

                // Crear fila en la tabla con nombres de campo actualizados
                const newRow = `
                <tr>
                    <td>
                        ${articuloNombre}
                        <input type="hidden" name="nuevos_detalles[${nuevoDetalleCount}][articulo_id]" value="${articuloId}">
                    </td>
                    <td>
                        ${cantidad}
                        <input type="hidden" name="nuevos_detalles[${nuevoDetalleCount}][cantidad]" value="${cantidad}">
                    </td>
                    <td>${currencySymbol}.${precioUnitario.toFixed(2)}</td>
                    <td>
                        ${descuentoTexto}
                        <input type="hidden" name="nuevos_detalles[${nuevoDetalleCount}][descuento_id]" value="${descuentoId || ''}">
                    </td>
                    <td>
                        ${currencySymbol}.${subtotal.toFixed(2)}
                        <input type="hidden" name="nuevos_detalles[${nuevoDetalleCount}][sub_total]" value="${subtotal}" class="subtotal-input">
                        <input type="hidden" name="nuevos_detalles[${nuevoDetalleCount}][usuario_id]" value="{{ Auth::id() }}">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm eliminar-nuevo-detalle">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;

                // Agregar a la tabla y mostrar contenedor si está oculto
                $('#nuevos-detalles').append(newRow);
                $('#nuevos-detalles-container').show();

                // Incrementar contador y limpiar campos
                nuevoDetalleCount++;
                $('#articulo, #descuento-nuevo').val('').trigger('change');
                $('#cantidad-nuevo, #stock').val('');
                $('#unidad-abreviatura').text('');

                actualizarTotal();
            });

            // Eliminar nuevo detalle
            $(document).on('click', '.eliminar-nuevo-detalle', function() {
                $(this).closest('tr').remove();
                if ($('#nuevos-detalles tr').length === 0) {
                    $('#nuevos-detalles-container').hide();
                }
                actualizarTotal();
            });

            // Función para actualizar el total de la venta
            function actualizarTotal() {
                let total = 0;

                // Sumar subtotales de detalles existentes no eliminados
                $('.detalle-existente:visible').each(function() {
                    const subtotalInput = $(this).find('.subtotal-input');
                    if (subtotalInput.length) {
                        total += parseFloat(subtotalInput.val() || 0);
                    }
                });

                // Sumar subtotales de nuevos detalles
                $('#nuevos-detalles .subtotal-input').each(function() {
                    total += parseFloat($(this).val() || 0);
                });

                // Actualizar texto del total
                $('#total-venta').text(`Total: ${currencySymbol}.${total.toFixed(2)}`);
                console.log("Total actualizado:", total);
            }

            // Inicializar subtotales y total al cargar la página
            $('.detalle-existente').each(function() {
                const detalleId = $(this).attr('id').replace('detalle-row-', '');
                recalcularSubtotal(detalleId);
            });

            actualizarTotal();

            // Al cargar la página, actualizar los nombres de los campos existentes
            $('.detalle-existente').each(function() {
                const detalleId = $(this).attr('id').replace('detalle-row-', '');

                // Actualizar nombres de campos
                $(this).find('input[name^="detalles[' + detalleId + ']"]').each(function() {
                    const fieldName = $(this).attr('name').split(']')[1].replace('[', '').replace(']', '');
                    if (fieldName) {
                        $(this).attr('name', `detalles_a_mantener[${detalleId}][${fieldName}]`);
                    }
                });

                // Asegurarnos de que articulo_id y usuario_id siempre existan
                if ($(this).find('input[name="detalles_a_mantener[' + detalleId + '][articulo_id]"]').length === 0) {
                    const articuloId = $(this).find('input[value="' + detalleId + '"]').data('articulo-id');
                    if (articuloId) {
                        $(this).append(`<input type="hidden" name="detalles_a_mantener[${detalleId}][articulo_id]" value="${articuloId}">`);
                    }
                }

                if ($(this).find('input[name="detalles_a_mantener[' + detalleId + '][usuario_id]"]').length === 0) {
                    const usuarioId = '{{ Auth::id() }}';
                    $(this).append(`<input type="hidden" name="detalles_a_mantener[${detalleId}][usuario_id]" value="${usuarioId}">`);
                }

                // Actualizar selects también
                $(this).find('select').each(function() {
                    const originalName = $(this).attr('name');
                    if (originalName && originalName.startsWith('detalles[' + detalleId + ']')) {
                        const fieldName = originalName.split(']')[1].replace('[', '').replace(']', '');
                        if (fieldName) {
                            $(this).attr('name', `detalles_a_mantener[${detalleId}][${fieldName}]`);
                            $(this).data('field-name', fieldName);
                        }
                    }
                });
            });

            // Agregar contenedor oculto para los detalles a eliminar
            $('form').append('<div id="eliminar-detalles-container" style="display:none;"></div>');

            // Agregar evento submit al formulario
            document.querySelector('form').addEventListener('submit', function(e) {
                // Asegurarnos de que haya al menos un detalle
                const tieneDetallesExistentes = $('.detalle-existente').filter(function() {
                    return !$(this).find('input[id^="eliminar-"]').val() || $(this).find('input[id^="eliminar-"]').val() === '0';
                }).length > 0;

                const tieneNuevosDetalles = $('#nuevos-detalles tr').length > 0;

                if (!tieneDetallesExistentes && !tieneNuevosDetalles) {
                    e.preventDefault();
                    alert('Debe haber al menos un artículo en los detalles.');
                    return false;
                }

                // Si no hay nuevos detalles, remover las entradas relacionadas
                if (!tieneNuevosDetalles) {
                    $('input[name^="nuevos_detalles"], select[name^="nuevos_detalles"]').remove();
                }

                // Asegurar que las entradas tienen usuario_id
                $('.detalle-existente').each(function() {
                    const detalleId = $(this).attr('id').replace('detalle-row-', '');
                    if ($(this).find('input[name="detalles_a_mantener[' + detalleId + '][usuario_id]"]').length === 0) {
                        $(this).append(`<input type="hidden" name="detalles_a_mantener[${detalleId}][usuario_id]" value="{{ Auth::id() }}">`);
                    }
                });

                // Verificar que los detalles marcados para eliminar están correctamente incluidos
                const detallesAEliminar = document.querySelectorAll('input[name="detalles_a_eliminar[]"]');
                console.log('Detalles a eliminar:', detallesAEliminar.length);
                detallesAEliminar.forEach(function(input) {
                    console.log('Detalle ID para eliminar:', input.value);
                });
            });

            // Validación del input de cantidad en detalles existentes
            $('.cantidad-input').on('input', function(e) {
                const unidadTipo = $(this).data('unidad-tipo') || 'decimal';

                if (unidadTipo === 'unidad') {
                    // Solo permitir números enteros
                    this.value = this.value.replace(/[^0-9]/g, '');
                    if (this.value === '') this.value = '1';
                    if (parseInt(this.value) < 1) this.value = '1';
                } else {
                    // Permitir números decimales
                    const parts = this.value.split('.');
                    if (parts.length > 2) {
                        this.value = parts[0] + '.' + parts.slice(1).join('');
                    }
                    // Asegurarse que sea al menos 0.01
                    if (parseFloat(this.value || '0') < 0.01) this.value = '0.01';
                }

                // Recalcular subtotal tras la validación
                const detalleId = $(this).data('detalle-id');
                recalcularSubtotal(detalleId);
            });
        });
    </script>
@endsection
