<!-- filepath: /c:/Users/szott/Dropbox/Desarrollo/jireh/resources/views/admin/articulo/add.blade.php -->
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
                    <h5>Agregar Artículo</h5>
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
                            <form action="{{ url('insert-articulo') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="codigo" class="form-label">Código</label>
                                        <input type="text" name="codigo" class="form-control" value="{{ old('codigo') }}">
                                        @if ($errors->has('codigo'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('codigo') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-8">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}">
                                        @if ($errors->has('nombre'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('nombre') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-12">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion') }}">
                                        @if ($errors->has('descripcion'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('descripcion') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="categoria_id" class="form-label">Categoría</label>
                                        <select name="categoria_id" class="form-control">
                                            @foreach($categorias as $categoria)
                                                <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('categoria_id'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('categoria_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>


                                </div>

                                <div class="row mb-3">

                                    <div class="col-md-6">
                                        <label for="precio_compra" class="form-label">Precio de Compra</label>
                                        <input type="number" step="0.01" name="precio_compra" class="form-control" value="{{ old('precio_compra') }}">
                                        @if ($errors->has('precio_compra'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('precio_compra') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="precio_venta" class="form-label">Precio de Venta</label>
                                        <input type="number" step="0.01" name="precio_venta" class="form-control" value="{{ old('precio_venta') }}">
                                        @if ($errors->has('precio_venta'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('precio_venta') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="tipo_comision_vendedor_id" class="form-label">Tipo de Comisión para Vendedor</label>
                                        <select name="tipo_comision_vendedor_id" class="form-control">
                                            @foreach($tipoComisiones as $tipoComision)
                                                <option value="{{ $tipoComision->id }}" {{ old('tipo_comision_vendedor_id') == $tipoComision->id ? 'selected' : '' }}>{{ $tipoComision->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('tipo_comision_vendedor_id'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('tipo_comision_vendedor_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tipo_comision_trabajador_id" class="form-label">Tipo de Comisión para Trabajador</label>
                                        <select name="tipo_comision_trabajador_id" class="form-control">
                                            @foreach($tipoComisiones as $tipoComision)
                                                <option value="{{ $tipoComision->id }}" {{ old('tipo_comision_trabajador_id') == $tipoComision->id ? 'selected' : '' }}>{{ $tipoComision->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('tipo_comision_trabajador_id'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('tipo_comision_trabajador_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label for="unidad_id" class="form-label">Unidad de Medida</label>
                                        <select name="unidad_id" class="form-control" id="unidad_id">
                                            @foreach($unidades as $unidad)
                                                <option value="{{ $unidad->id }}" data-tipo="{{ $unidad->tipo }}" {{ old('unidad_id') == $unidad->id ? 'selected' : '' }}>{{ $unidad->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('unidad_id'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('unidad_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <label for="stock" class="form-label">Stock</label>
                                        <input type="number" name="stock" class="form-control" id="stock" value="{{ old('stock') }}">
                                        @if ($errors->has('stock'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('stock') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                                        <input type="number" name="stock_minimo" class="form-control" id="stock_minimo" value="{{ old('stock_minimo') }}">
                                        @if ($errors->has('stock_minimo'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('stock_minimo') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-3">
                                        <label for="tipo" class="form-label">Tipo</label>
                                        <select name="tipo" class="form-control" id="tipo">
                                            <option value="articulo" {{ old('tipo') == 'articulo' ? 'selected' : '' }}>Artículo</option>
                                            <option value="servicio" {{ old('tipo') == 'servicio' ? 'selected' : '' }}>Servicio</option>
                                        </select>
                                        @if ($errors->has('tipo'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('tipo') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="row mb-3">


                                </div>
                                <div class="row mb-3" id="servicio-fields" style="display: none;">
                                    <div class="col-md-6">
                                        <label for="articulo_servicio" class="form-label">Artículo</label>
                                        <select name="articulo_servicio" id="articulo_servicio" class="form-control">
                                            @foreach($articulos as $articulo)
                                                <option value="{{ $articulo->id }}">{{ $articulo->codigo }} {{ $articulo->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="cantidad_servicio" class="form-label">Cantidad</label>
                                        <input type="number" name="cantidad_servicio" id="cantidad_servicio" value="1" class="form-control">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" id="add-articulo-servicio" class="btn btn-primary">Agregar</button>
                                    </div>
                                </div>
                                <div class="row mb-3" id="tabla-servicio" style="display: none;">
                                    <div class="col-md-12">
                                        @if ($errors->has('cantidades_servicio'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('cantidades_servicio') }}</strong>
                                            </span>
                                        @endif
                                        @if ($errors->has('articulos_servicio'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('articulos_servicio') }}</strong>
                                            </span>
                                        @endif
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Artículo</th>
                                                    <th>Cantidad</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="servicio-articulos-body">
                                                @if(old('articulos_servicio'))
                                                    @foreach(old('articulos_servicio') as $articuloId => $cantidad)
                                                        <tr>
                                                            <td>{{ $articuloId }}</td>
                                                            <td>{{ $articulos->find($articuloId)->codigo }} {{ $articulos->find($articuloId)->nombre }}</td>
                                                            <td>{{ $cantidad }}</td>
                                                            <td><button type="button" class="btn btn-danger btn-sm remove-articulo-servicio">Eliminar</button></td>
                                                            <input type="hidden" name="articulos_servicio[{{ $articuloId }}]" value="{{ $cantidad }}">
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ url('articulos') }}" type="button" class="btn btn-danger">
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
        const tipoSelect = document.querySelector('select[name="tipo"]');
        const servicioFields = document.getElementById('servicio-fields');
        const tablaServicio = document.getElementById('tabla-servicio');
        const addArticuloServicioBtn = document.getElementById('add-articulo-servicio');
        const servicioArticulosBody = document.getElementById('servicio-articulos-body');
        const unidadSelect = document.getElementById('unidad_id');
        const stockInput = document.getElementById('stock');
        const stockMinimoInput = document.getElementById('stock_minimo');
        const articuloServicioSelect = document.getElementById('articulo_servicio');
        const cantidadServicioInput = document.getElementById('cantidad_servicio');

        tipoSelect.addEventListener('change', function () {
            if (this.value === 'servicio') {
                servicioFields.style.display = 'flex';
                tablaServicio.style.display = 'block';
            } else {
                servicioFields.style.display = 'none';
                tablaServicio.style.display = 'none';
                servicioArticulosBody.innerHTML = ''; // Limpiar la tabla si se cambia a "articulo"
            }
        });

        addArticuloServicioBtn.addEventListener('click', function () {
            const articuloId = articuloServicioSelect.value;
            const articuloNombre = articuloServicioSelect.options[articuloServicioSelect.selectedIndex].text;
            const cantidad = cantidadServicioInput.value;

            if (articuloId && cantidad) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${articuloId}</td>
                    <td>${articuloNombre}</td>
                    <td>${cantidad}</td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-articulo-servicio">Eliminar</button></td>
                    <input type="hidden" name="articulos_servicio[${articuloId}]" value="${cantidad}">
                `;
                servicioArticulosBody.appendChild(row);

                // Limpiar los campos después de agregar
                articuloServicioSelect.value = '';
                cantidadServicioInput.value = '';
            }
        });

        // Eliminar artículo de la tabla
        servicioArticulosBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-articulo-servicio')) {
                e.target.closest('tr').remove();
            }
        });

        // Mostrar u ocultar los campos de servicio al cargar la página
        if (tipoSelect.value === 'servicio') {
            servicioFields.style.display = 'flex';
            tablaServicio.style.display = 'block';
        } else {
            servicioFields.style.display = 'none';
            tablaServicio.style.display = 'none';
        }

        // Cambiar el tipo de input para stock y stock_minimo según la unidad de medida seleccionada
        unidadSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const tipoUnidad = selectedOption.getAttribute('data-tipo');

            if (tipoUnidad === 'unidad') {
                stockInput.setAttribute('step', '1');
                stockInput.setAttribute('min', '0');
                stockMinimoInput.setAttribute('step', '1');
                stockMinimoInput.setAttribute('min', '0');

                // Validar y corregir el valor ingresado
                stockInput.addEventListener('input', validateIntegerInput);
                stockMinimoInput.addEventListener('input', validateIntegerInput);
            } else if (tipoUnidad === 'decimal') {
                stockInput.setAttribute('step', '0.01');
                stockInput.setAttribute('min', '0');
                stockMinimoInput.setAttribute('step', '0.01');
                stockMinimoInput.setAttribute('min', '0');

                // Validar y corregir el valor ingresado
                stockInput.removeEventListener('input', validateIntegerInput);
                stockMinimoInput.removeEventListener('input', validateIntegerInput);
            }
        });

        // Función para validar y corregir el valor ingresado en los campos de stock y stock_minimo
        function validateIntegerInput(event) {
            event.target.value = event.target.value.replace(/[^0-9]/g, '');
        }

        // Disparar el evento change al cargar la página para establecer el tipo de input correcto
        unidadSelect.dispatchEvent(new Event('change'));

        // Cambiar el tipo de input para cantidad_servicio según la unidad de medida del artículo seleccionado
        articuloServicioSelect.addEventListener('change', function () {
            const articuloId = this.value;

            // Obtener el tipo de unidad desde la tabla unidads
            fetch(`/api/articulos/${articuloId}/unidad`)
                .then(response => response.json())
                .then(data => {
                    const tipoUnidad = data.tipo;

                    if (tipoUnidad === 'unidad') {
                        cantidadServicioInput.setAttribute('step', '1');
                        cantidadServicioInput.setAttribute('min', '0');

                        // Validar y corregir el valor ingresado
                        cantidadServicioInput.addEventListener('input', validateIntegerInput);
                    } else if (tipoUnidad === 'decimal') {
                        cantidadServicioInput.setAttribute('step', '0.01');
                        cantidadServicioInput.setAttribute('min', '0');

                        // Validar y corregir el valor ingresado
                        cantidadServicioInput.removeEventListener('input', validateIntegerInput);
                    }
                });
        });
    });
</script>
@endsection
