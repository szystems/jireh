@extends('layouts.admin')

@section('content')
    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="page-title">
                    <h5>Editar Cotización</h5>
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
                            <form action="{{ route('admin.cotizaciones.update', $cotizacion->id) }}" method="POST" id="form-editar-cotizacion">
                                @csrf
                                @method('PUT')
                                
                                <!-- Campo hidden para detalles a eliminar -->
                                <input type="hidden" id="detalles-a-eliminar" name="detalles_a_eliminar" value="">
                                <div class="row gx-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="numero_cotizacion" class="form-label">Número de Cotización</label>
                                        <input type="text" class="form-control" id="numero_cotizacion" name="numero_cotizacion" value="{{ $cotizacion->numero_cotizacion }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tipo_cotizacion" class="form-label">Tipo de Cotización</label>
                                        <select class="form-control" id="tipo_cotizacion" name="tipo_cotizacion" required>
                                            <option value="Car Wash" {{ $cotizacion->tipo_cotizacion == 'Car Wash' ? 'selected' : '' }}>Car Wash</option>
                                            <option value="CDS" {{ $cotizacion->tipo_cotizacion == 'CDS' ? 'selected' : '' }}>CDS</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha_cotizacion" class="form-label">Fecha de Cotización</label>
                                        <input type="date" class="form-control" id="fecha_cotizacion" name="fecha_cotizacion" value="{{ $cotizacion->fecha_cotizacion->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                                        <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" value="{{ $cotizacion->fecha_vencimiento->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="estado" class="form-label">Estado</label>
                                        <select class="form-control" id="estado" name="estado" required>
                                            <option value="Generado" {{ $cotizacion->estado == 'Generado' ? 'selected' : '' }}>Generado</option>
                                            <option value="Aprobado" {{ $cotizacion->estado == 'Aprobado' ? 'selected' : '' }}>Aprobado</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cliente_id" class="form-label">Cliente</label>
                                        <select class="form-control select2" id="cliente_id" name="cliente_id" required>
                                            <option value="">Seleccione un cliente</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ $cotizacion->cliente_id == $cliente->id ? 'selected' : '' }}>
                                                    {{ $cliente->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="vehiculo_id" class="form-label">Vehículo</label>
                                        <select class="form-control select2" id="vehiculo_id" name="vehiculo_id" required>
                                            <option value="">Seleccione un vehículo</option>
                                            @if($cotizacion->cliente)
                                                @foreach($cotizacion->cliente->vehiculos as $vehiculo)
                                                    <option value="{{ $vehiculo->id }}" {{ $cotizacion->vehiculo_id == $vehiculo->id ? 'selected' : '' }}>
                                                        {{ $vehiculo->placa }} - {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <h5>Detalles de la Cotización</h5>
                                        @if($cotizacion->estado != 'convertida')
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
                                                            <input type="text" class="form-control" id="cantidad" min="1" step="1" inputmode="decimal" pattern="[0-9]*\.?[0-9]*">
                                                            <span class="input-group-text" id="cantidad-unidad-abreviatura"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="precio_unitario" class="form-label">Precio Unitario</label>
                                                        <input type="text" class="form-control" id="precio_unitario" step="0.01" min="0" inputmode="decimal" pattern="[0-9]*\.?[0-9]*">
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
                                            </div>
                                        @endif
                                        
                                        <table class="table table-bordered" id="tabla-detalles">
                                            <thead>
                                                <tr>
                                                    <th>Artículo</th>
                                                    <th>Cantidad</th>
                                                    <th>Precio Unitario</th>
                                                    <th>Descuento</th>
                                                    <th>Subtotal</th>
                                                    @if($cotizacion->estado != 'convertida')
                                                        <th>Acciones</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody id="detalles-tbody">
                                                @foreach($cotizacion->detalleCotizaciones as $detalle)
                                                    <tr data-detalle-id="{{ $detalle->id }}">
                                                        <td>{{ $detalle->articulo->codigo }} {{ $detalle->articulo->nombre }}</td>
                                                        <td>{{ $detalle->cantidad }}</td>
                                                        <td>{{ $config->currency_simbol }}{{ number_format($detalle->precio_venta, 2) }}</td>
                                                        <td>{{ $detalle->descuento ? $detalle->descuento->nombre . ' (' . $detalle->descuento->porcentaje_descuento . '%)' : 'Sin descuento' }}</td>
                                                        <td>{{ $config->currency_simbol }}{{ number_format($detalle->sub_total, 2) }}</td>
                                                        @if($cotizacion->estado != 'convertida')
                                                            <td>
                                                                <button type="button" class="btn btn-sm btn-danger" onclick="eliminarDetalleExistente({{ $detalle->id }})">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="{{ $cotizacion->estado == 'convertida' ? '4' : '5' }}" class="text-end">Total:</th>
                                                    <th id="total-cotizacion">{{ $config->currency_simbol }}{{ number_format($cotizacion->detalleCotizaciones->sum('sub_total'), 2) }}</th>
                                                    @if($cotizacion->estado != 'convertida')
                                                        <th></th>
                                                    @endif
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        @if($cotizacion->estado != 'convertida')
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-check-circle"></i> Actualizar Cotización
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left"></i> Volver
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
        let contadorDetalles = 1000; // Empezar con un número alto para evitar conflictos
        let detallesAEliminar = [];
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
                const unidadTipo = option.data('unidad-tipo');
                const cantidadInput = $('#cantidad')[0];
                
                $('#stock').val(option.data('stock') || '');
                $('#stock-unidad-abreviatura').text(option.data('unidad-abreviatura') || '');
                $('#cantidad-unidad-abreviatura').text(option.data('unidad-abreviatura') || '');
                $('#precio_unitario').val(option.data('precio-venta') || '');
                
                // Configurar placeholder y valor inicial según tipo de unidad
                if (unidadTipo === 'decimal') {
                    cantidadInput.placeholder = "Ej: 1.50";
                    cantidadInput.value = "1.00";
                } else {
                    cantidadInput.placeholder = "Ej: 3";
                    cantidadInput.value = "1";
                }
                
                // Agregar clase CSS para identificar tipo
                cantidadInput.setAttribute('data-unidad-tipo', unidadTipo);
            });

            // Validación no intrusiva para cantidad - permite escribir libremente
            $('#cantidad').on('input', function(event) {
                const value = event.target.value;
                
                // Solo limpiar caracteres obviamente inválidos, pero permitir estados temporales
                const cleanValue = value.replace(/[^0-9.]/g, '');
                
                // Evitar múltiples puntos decimales
                const parts = cleanValue.split('.');
                if (parts.length > 2) {
                    event.target.value = parts[0] + '.' + parts.slice(1).join('');
                } else {
                    event.target.value = cleanValue;
                }
            });

            // Validación final cuando pierde el foco (blur)
            $('#cantidad').on('blur', function(event) {
                const unidadTipo = event.target.getAttribute('data-unidad-tipo') || 'unidad';
                let value = event.target.value.trim();
                
                // Si está vacío, establecer valor por defecto
                if (value === '' || value === '.') {
                    value = unidadTipo === 'unidad' ? '1' : '1.00';
                    event.target.value = value;
                    return;
                }
                
                const numValue = parseFloat(value);
                
                if (unidadTipo === 'unidad') {
                    // Para unidades, convertir a entero y validar mínimo
                    const intValue = Math.floor(numValue);
                    if (intValue < 1) {
                        event.target.value = '1';
                    } else {
                        event.target.value = intValue.toString();
                    }
                } else {
                    // Para decimales, validar mínimo y formato
                    if (numValue < 0.01) {
                        event.target.value = '0.01';
                    } else {
                        // Limitar a 2 decimales
                        event.target.value = numValue.toFixed(2);
                    }
                }
            });

            // Validación no intrusiva para precio unitario
            $('#precio_unitario').on('input', function(event) {
                const value = event.target.value;
                
                // Solo limpiar caracteres obviamente inválidos
                const cleanValue = value.replace(/[^0-9.]/g, '');
                
                // Evitar múltiples puntos decimales
                const parts = cleanValue.split('.');
                if (parts.length > 2) {
                    event.target.value = parts[0] + '.' + parts.slice(1).join('');
                } else {
                    event.target.value = cleanValue;
                }
            });

            // Validación final para precio cuando pierde el foco
            $('#precio_unitario').on('blur', function(event) {
                let value = event.target.value.trim();
                
                if (value === '' || value === '.') {
                    event.target.value = '0.00';
                    return;
                }
                
                const numValue = parseFloat(value);
                if (isNaN(numValue) || numValue < 0) {
                    event.target.value = '0.00';
                } else {
                    // Formatear a 2 decimales
                    event.target.value = numValue.toFixed(2);
                }
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

            // Validación adicional según tipo de unidad
            const cantidadInput = $('#cantidad')[0];
            const unidadTipo = cantidadInput.getAttribute('data-unidad-tipo') || 'unidad';
            
            if (unidadTipo === 'unidad') {
                // Para unidades, verificar que sea un número entero
                if (cantidad % 1 !== 0) {
                    alert('Para artículos de tipo "unidad", la cantidad debe ser un número entero');
                    $('#cantidad').focus();
                    return;
                }
                if (cantidad < 1) {
                    alert('Para artículos de tipo "unidad", la cantidad mínima es 1');
                    return;
                }
            } else {
                // Para decimales, verificar mínimo
                if (cantidad < 0.01) {
                    alert('Para artículos de tipo "decimal", la cantidad mínima es 0.01 (use punto decimal, ej: 1.50)');
                    return;
                }
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
            agregarFilaDetalle(detalle);
            actualizarTotal();
            limpiarFormularioDetalle();
        }

        function agregarFilaDetalle(detalle) {
            const tbody = $('#detalles-tbody');
            const fila = `
                <tr data-nuevo-detalle-id="${detalle.id}">
                    <td>${detalle.articulo_texto}</td>
                    <td>${detalle.cantidad}</td>
                    <td>${currencySymbol}${detalle.precio_unitario.toFixed(2)}</td>
                    <td>${detalle.descuento_texto}</td>
                    <td>${currencySymbol}${detalle.subtotal.toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" onclick="eliminarDetalleNuevo(${detalle.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                    <input type="hidden" name="nuevos_detalles[${detalle.id}][articulo_id]" value="${detalle.articulo_id}">
                    <input type="hidden" name="nuevos_detalles[${detalle.id}][cantidad]" value="${detalle.cantidad}">
                    <input type="hidden" name="nuevos_detalles[${detalle.id}][precio_venta]" value="${detalle.precio_unitario}">
                    <input type="hidden" name="nuevos_detalles[${detalle.id}][descuento_id]" value="${detalle.descuento_id || ''}">
                    <input type="hidden" name="nuevos_detalles[${detalle.id}][sub_total]" value="${detalle.subtotal}">
                </tr>
            `;
            tbody.append(fila);
        }

        function eliminarDetalleNuevo(id) {
            detalles = detalles.filter(d => d.id !== id);
            $(`tr[data-nuevo-detalle-id="${id}"]`).remove();
            actualizarTotal();
        }

        function eliminarDetalleExistente(detalleId) {
            if (confirm('¿Está seguro de eliminar este detalle?')) {
                detallesAEliminar.push(detalleId);
                $(`tr[data-detalle-id="${detalleId}"]`).hide();
                $('#detalles-a-eliminar').val(detallesAEliminar.join(','));
                actualizarTotal();
            }
        }

        function actualizarTotal() {
            let total = 0;
            
            // Sumar detalles existentes visibles
            $('#detalles-tbody tr[data-detalle-id]:visible').each(function() {
                const subtotalTexto = $(this).find('td:nth-child(5)').text().replace('$', '').replace(',', '');
                total += parseFloat(subtotalTexto) || 0;
            });
            
            // Sumar nuevos detalles
            detalles.forEach(function(detalle) {
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