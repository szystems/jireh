@extends('layouts.admin')
@section('content')
    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-file-earmark-plus"></i>
                </div>
                <div class="page-title">
                    <h5>Nueva Cotización</h5>
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
                            <form action="{{ route('admin.cotizaciones.store') }}" method="POST">
                                @csrf
                                <div class="row gx-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="numero_cotizacion" class="form-label">Número de Cotización</label>
                                        <input type="text" class="form-control" id="numero_cotizacion" name="numero_cotizacion" value="{{ $siguienteNumero }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha_cotizacion" class="form-label">Fecha de Cotización</label>
                                        <input type="date" class="form-control" id="fecha_cotizacion" name="fecha_cotizacion" value="{{ old('fecha_cotizacion', date('Y-m-d')) }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                                        <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', date('Y-m-d', strtotime('+15 days'))) }}" required>
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
                                        <label for="tipo_cotizacion" class="form-label">Tipo de Cotización</label>
                                        <select class="form-control" id="tipo_cotizacion" name="tipo_cotizacion" required>
                                            <option value="Car Wash" {{ old('tipo_cotizacion', 'Car Wash') == 'Car Wash' ? 'selected' : '' }}>Car Wash</option>
                                            <option value="CDS" {{ old('tipo_cotizacion') == 'CDS' ? 'selected' : '' }}>CDS</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <h5>Detalles de la Cotización</h5>
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
                                                                    data-tipo="{{ $articulo->tipo }}">
                                                                {{ $articulo->codigo }} {{ $articulo->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="stock" class="form-label">Stock</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="stock" readonly>
                                                        <span class="input-group-text" id="stock-unidad-abreviatura"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="cantidad" class="form-label">Cantidad</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="cantidad" min="1" step="1">
                                                        <span class="input-group-text" id="cantidad-unidad-abreviatura"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="precio_unitario" class="form-label">Precio Unitario</label>
                                                    <input type="number" class="form-control" id="precio_unitario" step="0.01" min="0">
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="descuento_id" class="form-label">Descuento</label>
                                                    <select class="form-control" id="descuento_id">
                                                        <option value="">Sin descuento</option>
                                                        @foreach ($descuentos as $descuento)
                                                            <option value="{{ $descuento->id }}" data-porcentaje="{{ $descuento->porcentaje_descuento }}">
                                                                {{ $descuento->nombre }} ({{ $descuento->porcentaje_descuento }}%)
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-12">
                                                    <button type="button" class="btn btn-success mt-2" onclick="agregarDetalle()">
                                                        <i class="bi bi-plus"></i> Agregar Artículo
                                                    </button>
                                                </div>
                                            </div>
                                            <table class="table table-bordered" id="tabla-detalles">
                                                <thead>
                                                    <tr>
                                                        <th>Artículo</th>
                                                        <th>Cantidad</th>
                                                        <th>Precio Unitario</th>
                                                        <th>Descuento</th>
                                                        <th>Subtotal</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="detalles-tbody">
                                                    <!-- Los detalles se agregan dinámicamente aquí -->
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="4" class="text-end">Total:</th>
                                                        <th id="total-cotizacion">{{ $config->currency_simbol }}0.00</th>
                                                        <th></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle"></i> Guardar Cotización
                                        </button>
                                        <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left"></i> Cancelar
                                        </a>
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
        let detalles = [];
        let contadorDetalles = 0;
        const currencySymbol = '{{ $config->currency_simbol }}';

        $(document).ready(function() {
            // Inicializar Select2
            $('.select2').select2();

            // Cargar vehículos cuando se selecciona un cliente
            $('#cliente_id').change(function() {
                const clienteId = $(this).val();
                const vehiculoSelect = $('#vehiculo_id');
                
                vehiculoSelect.empty().append('<option value="">Seleccione un vehículo</option>');

                if (clienteId) {
                    $.get(`/api/clientes/${clienteId}/vehiculos`, function(vehiculos) {
                        vehiculos.forEach(function(vehiculo) {
                            vehiculoSelect.append(`<option value="${vehiculo.id}">${vehiculo.placa} - ${vehiculo.marca} ${vehiculo.modelo}</option>`);
                        });
                    });
                }
            });

            // Cargar datos del artículo seleccionado
            $('#articulo').change(function() {
                const option = $(this).find(':selected');
                $('#stock').val(option.data('stock') || '');
                $('#stock-unidad-abreviatura').text(option.data('unidad-abreviatura') || '');
                $('#cantidad-unidad-abreviatura').text(option.data('unidad-abreviatura') || '');
                $('#precio_unitario').val(option.data('precio-venta') || '');
                $('#cantidad').val('1');
            });
        });

        function agregarDetalle() {
            const articuloSelect = $('#articulo');
            const articuloId = articuloSelect.val();
            const articuloTexto = articuloSelect.find(':selected').text();
            const cantidad = parseFloat($('#cantidad').val()) || 0;
            const precioUnitario = parseFloat($('#precio_unitario').val()) || 0;
            const descuentoSelect = $('#descuento_id');
            const descuentoId = descuentoSelect.val();
            const descuentoTexto = descuentoSelect.find(':selected').text();
            const porcentajeDescuento = parseFloat(descuentoSelect.find(':selected').data('porcentaje')) || 0;

            if (!articuloId || cantidad <= 0 || precioUnitario <= 0) {
                alert('Por favor complete todos los campos correctamente');
                return;
            }

            // Verificar si el artículo ya existe en los detalles
            const detalleExistente = detalles.find(d => d.articulo_id == articuloId && d.descuento_id == descuentoId);
            if (detalleExistente) {
                alert('Este artículo con el mismo descuento ya está agregado');
                return;
            }

            const subtotal = cantidad * precioUnitario;
            const montoDescuento = subtotal * (porcentajeDescuento / 100);
            const subtotalConDescuento = subtotal - montoDescuento;

            const detalle = {
                id: contadorDetalles++,
                articulo_id: articuloId,
                articulo_texto: articuloTexto,
                cantidad: cantidad,
                precio_unitario: precioUnitario,
                descuento_id: descuentoId || null,
                descuento_texto: descuentoTexto || 'Sin descuento',
                porcentaje_descuento: porcentajeDescuento,
                subtotal: subtotalConDescuento
            };

            detalles.push(detalle);
            actualizarTablaDetalles();
            limpiarFormularioDetalle();
        }

        function eliminarDetalle(id) {
            detalles = detalles.filter(d => d.id !== id);
            actualizarTablaDetalles();
        }

        function actualizarTablaDetalles() {
            const tbody = $('#detalles-tbody');
            tbody.empty();

            let total = 0;

            detalles.forEach(function(detalle) {
                const fila = `
                    <tr>
                        <td>${detalle.articulo_texto}</td>
                        <td>${detalle.cantidad}</td>
                        <td>${currencySymbol}${detalle.precio_unitario.toFixed(2)}</td>
                        <td>${detalle.descuento_texto}</td>
                        <td>${currencySymbol}${detalle.subtotal.toFixed(2)}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-danger" onclick="eliminarDetalle(${detalle.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(fila);
                
                // Agregar inputs ocultos
                tbody.append(`
                    <input type="hidden" name="detalles[${detalle.id}][articulo_id]" value="${detalle.articulo_id}">
                    <input type="hidden" name="detalles[${detalle.id}][cantidad]" value="${detalle.cantidad}">
                    <input type="hidden" name="detalles[${detalle.id}][precio_unitario]" value="${detalle.precio_unitario}">
                    <input type="hidden" name="detalles[${detalle.id}][descuento_id]" value="${detalle.descuento_id || ''}">
                    <input type="hidden" name="detalles[${detalle.id}][sub_total]" value="${detalle.subtotal}">
                `);

                total += detalle.subtotal;
            });

            $('#total-cotizacion').text(`${currencySymbol}${total.toFixed(2)}`);
        }

        function limpiarFormularioDetalle() {
            $('#articulo').val('').trigger('change');
            $('#cantidad').val('');
            $('#precio_unitario').val('');
            $('#descuento_id').val('');
            $('#stock').val('');
            $('#stock-unidad-abreviatura').text('');
            $('#cantidad-unidad-abreviatura').text('');
        }
    </script>
@endsection