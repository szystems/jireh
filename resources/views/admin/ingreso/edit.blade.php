@extends('layouts.admin')

@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-box"></i>
                </div>
                <div class="page-title">
                    <h5>Editar Ingreso</h5>
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
                            <h5>Formulario de Edición de Ingreso</h5>
                        </div>
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
                            <form action="{{ url('update-ingreso/'.$ingreso->id) }} }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="numero_factura" class="form-label">Número de Factura</label>
                                        <input type="text" name="numero_factura" class="form-control" value="{{ old('numero_factura', $ingreso->numero_factura) }}">
                                        @if ($errors->has('numero_factura'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('numero_factura') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fecha" class="form-label">Fecha</label>
                                        <input type="date" name="fecha" class="form-control" value="{{ old('fecha', $ingreso->fecha) }}">
                                        @if ($errors->has('fecha'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('fecha') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="proveedor_id" class="form-label">Proveedor</label>
                                        <select name="proveedor_id" class="form-control select2">
                                            @foreach($proveedores as $proveedor)
                                                <option value="{{ $proveedor->id }}" {{ old('proveedor_id', $ingreso->proveedor_id) == $proveedor->id ? 'selected' : '' }}>{{ $proveedor->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('proveedor_id'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('proveedor_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tipo_compra" class="form-label">Tipo de Compra</label>
                                        <select name="tipo_compra" class="form-control">
                                            <option value="CDS" {{ old('tipo_compra', $ingreso->tipo_compra) == 'CDS' ? 'selected' : '' }}>CDS</option>
                                            <option value="Car Wash" {{ old('tipo_compra', $ingreso->tipo_compra) == 'Car Wash' ? 'selected' : '' }}>Car Wash</option>
                                        </select>
                                        @if ($errors->has('tipo_compra'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('tipo_compra') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <h5>Agregar Nuevo Artículo</h5>
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
                                                <label for="cantidad" class="form-label">Cantidad</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="cantidad" min="1" step="1">
                                                    <span class="input-group-text" id="unidad-abreviatura"></span>
                                                </div>
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

                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-primary" id="add-detalle"><i class="bi bi-plus-square"></i> Agregar Artículo</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="detalles" class="form-label">Detalles del Ingreso</label>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Artículo</th>
                                                    <th>Cantidad</th>
                                                    <th>Precio de Compra</th>
                                                    <th>Precio de Venta</th>
                                                    <th>Acciones</th>
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
                                                                    <input type="number" step="0.01" name="detalles[{{ $index }}][precio_compra]" class="form-control" value="{{ is_array($detalle) ? $detalle['precio_compra'] : '' }}">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">{{ $config->currency_simbol }}</span>
                                                                    <input type="number" step="0.01" name="detalles[{{ $index }}][precio_venta]" class="form-control" value="{{ is_array($detalle) ? $detalle['precio_venta'] : '' }}">
                                                                </div>
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
                                                                {{ $detalle->articulo->codigo }} {{ $detalle->articulo->nombre }}
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
                                                                    <input type="number" step="0.01" name="detalles[{{ $detalle->id }}][precio_compra]" class="form-control" value="{{ $detalle->precio_compra }}">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">{{ $config->currency_simbol }}</span>
                                                                    <input type="number" step="0.01" name="detalles[{{ $detalle->id }}][precio_venta]" class="form-control" value="{{ $detalle->precio_venta }}">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-danger btn-sm remove-detalle"><i class="bi bi-trash-fill"></i></button>
                                                                <input type="hidden" name="detalles[{{ $detalle->id }}][eliminar]" class="detalle-eliminar" value="0">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ url('ingresos')  }}" type="button" class="btn btn-danger">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check2-square"></i> Grabar
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
        $('.select2').select2();

        const articuloSelect = document.getElementById('articulo');
        const precioCompraInput = document.getElementById('precio_compra');
        const precioVentaInput = document.getElementById('precio_venta');
        const cantidadInput = document.getElementById('cantidad');
        const unidadAbreviaturaSpan = document.getElementById('unidad-abreviatura');
        const addDetalleBtn = document.getElementById('add-detalle');
        const detallesBody = document.getElementById('detalles-body');
        const currencySymbol = @json($config->currency_simbol);

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

            const articuloText = articuloSelect.options[articuloSelect.selectedIndex].text;
            const precioCompra = parseFloat(precioCompraInput.value);
            const precioVenta = parseFloat(precioVentaInput.value);
            const cantidad = parseFloat(cantidadInput.value);
            const unidadAbreviatura = unidadAbreviaturaSpan.textContent;
            const unidadTipo = articuloSelect.options[articuloSelect.selectedIndex].getAttribute('data-unidad-tipo');

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
                    <input type="hidden" name="detalles[new][articulo_id]" value="${articuloId}">
                </td>
                <td>
                    <div class="input-group">
                        <input type="number" name="detalles[new][cantidad]" class="form-control cantidad-input" value="${cantidad}" data-tipo="${unidadTipo}">
                        <span class="input-group-text unidad-abreviatura">${unidadAbreviatura}</span>
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">${currencySymbol}</span>
                        <input type="number" step="0.01" name="detalles[new][precio_compra]" class="form-control" value="${precioCompra.toFixed(2)}">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">${currencySymbol}</span>
                        <input type="number" step="0.01" name="detalles[new][precio_venta]" class="form-control" value="${precioVenta.toFixed(2)}">
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-detalle"><i class="bi bi-trash-fill"></i></button>
                </td>`;
            detallesBody.appendChild(row);
            $('.select2').select2();

            // Reiniciar campos
            articuloSelect.value = '';
            precioCompraInput.value = '';
            precioVentaInput.value = '';
            cantidadInput.value = '';
            unidadAbreviaturaSpan.textContent = '';
            $('.select2').select2();

            // Aplicar validación de cantidad al nuevo input
            const newCantidadInput = row.querySelector('.cantidad-input');
            newCantidadInput.addEventListener('input', function (event) {
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
        });

        detallesBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-detalle')) {
                const row = e.target.closest('tr');
                const eliminarInput = row.querySelector('.detalle-eliminar');
                if (eliminarInput) {
                    eliminarInput.value = 1;
                    row.style.display = 'none';
                } else {
                    row.remove();
                }
            }
        });

        detallesBody.addEventListener('change', function (e) {
            if (e.target.classList.contains('articulo-select')) {
                const tipo = e.target.selectedOptions[0].getAttribute('data-tipo');
                const abreviatura = e.target.selectedOptions[0].getAttribute('data-abreviatura');
                const cantidadInput = e.target.closest('tr').querySelector('.cantidad-input');
                const unidadAbreviaturaSpan = e.target.closest('tr').querySelector('.unidad-abreviatura');
                unidadAbreviaturaSpan.textContent = abreviatura;
                if (tipo === 'unidad') {
                    cantidadInput.setAttribute('step', '1');
                    cantidadInput.setAttribute('min', '1');
                    cantidadInput.value = Math.floor(cantidadInput.value);
                } else {
                    cantidadInput.setAttribute('step', '0.01');
                    cantidadInput.setAttribute('min', '0.01');
                }
            }
        });

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
            const detallesBody = document.getElementById('detalles-body');
            if (detallesBody.children.length === 0) {
                alert("Debe haber al menos un artículo en los detalles.");
                e.preventDefault();
            }
        });
    });
</script>
@endsection
