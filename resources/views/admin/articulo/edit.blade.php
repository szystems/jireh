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
                    <h5>Editar Artículo</h5>
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
                        <div class="card-body">
                            <form action="{{ url('update-articulo/'.$articulo->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row gx-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="codigo" class="form-label">Código</label>
                                        <input type="text" class="form-control" id="codigo" name="codigo" value="{{ $articulo->codigo }}">
                                        @if ($errors->has('codigo'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('codigo') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="nombre" class="form-label">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $articulo->nombre }}">
                                        @if ($errors->has('nombre'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('nombre') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea class="form-control" id="descripcion" name="descripcion">{{ $articulo->descripcion }}</textarea>
                                        @if ($errors->has('descripcion'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('descripcion') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="precio_compra" class="form-label">Precio de Compra</label>
                                        <input type="number" step="0.01" class="form-control" id="precio_compra" name="precio_compra" value="{{ $articulo->precio_compra }}">
                                        @if ($errors->has('precio_compra'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('precio_compra') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="precio_venta" class="form-label">Precio de Venta</label>
                                        <input type="number" step="0.01" class="form-control" id="precio_venta" name="precio_venta" value="{{ $articulo->precio_venta }}">
                                        @if ($errors->has('precio_venta'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('precio_venta') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="stock" class="form-label">Stock</label>
                                        <input type="number" step="0.01" class="form-control" id="stock" name="stock" value="{{ $articulo->stock }}">
                                        @if ($errors->has('stock'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('stock') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                                        <input type="number" step="0.01" class="form-control" id="stock_minimo" name="stock_minimo" value="{{ $articulo->stock_minimo }}">
                                        @if ($errors->has('stock_minimo'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('stock_minimo') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="categoria_id" class="form-label">Categoría</label>
                                        <select class="form-control" id="categoria_id" name="categoria_id">
                                            @foreach($categorias as $categoria)
                                                <option value="{{ $categoria->id }}" {{ $articulo->categoria_id == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('categoria_id'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('categoria_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="unidad_id" class="form-label">Unidad de Medida</label>
                                        <select class="form-control" id="unidad_id" name="unidad_id">
                                            @foreach($unidades as $unidad)
                                                <option value="{{ $unidad->id }}" {{ $articulo->unidad_id == $unidad->id ? 'selected' : '' }}>{{ $unidad->nombre }}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('unidad_id'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('unidad_id') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tipo" class="form-label">Tipo</label>
                                        <select class="form-control" id="tipo" name="tipo">
                                            <option value="articulo" {{ $articulo->tipo == 'articulo' ? 'selected' : '' }}>Artículo</option>
                                            <option value="servicio" {{ $articulo->tipo == 'servicio' ? 'selected' : '' }}>Servicio</option>
                                        </select>
                                        @if ($errors->has('tipo'))
                                            <span class="help-block text-danger">
                                                <strong>{{ $errors->first('tipo') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-12 mb-3" id="servicio-fields" style="display: {{ $articulo->tipo == 'servicio' ? 'block' : 'none' }};">
                                        <h5>Artículos del Servicio</h5>
                                        <div class="table-responsive">
                                            <h6>Artículos Existentes</h6>
                                            <table class="table align-middle table-striped flex-column" id="tabla-servicio-existentes">
                                                <thead>
                                                    <tr>
                                                        <th>Artículo</th>
                                                        <th>Cantidad</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="servicio-articulos-existentes-body">
                                                    @foreach($articulo->articulos as $articuloServicio)
                                                        <tr>
                                                            <td>
                                                                <input type="text" class="form-control" value="{{ $articuloServicio->codigo }} {{ $articuloServicio->nombre }}" readonly>
                                                                <input type="hidden" name="articulos_servicio_existentes[{{ $loop->index }}][id]" value="{{ $articuloServicio->id }}">
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01" class="form-control" name="articulos_servicio_existentes[{{ $loop->index }}][cantidad]" value="{{ $articuloServicio->pivot->cantidad }}" min="1">
                                                                @if ($errors->has('articulos_servicio_existentes.'.$loop->index.'.cantidad'))
                                                                    <span class="help-block text-danger">
                                                                        <strong>{{ $errors->first('articulos_servicio_existentes.'.$loop->index.'.cantidad') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-danger btn-sm remove-articulo-servicio">Eliminar</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <h6>Agregar Nuevos Artículos</h6>
                                            <table class="table align-middle table-striped flex-column" id="tabla-servicio-nuevos">
                                                <thead>
                                                    <tr>
                                                        <th>Artículo</th>
                                                        <th>Cantidad</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="servicio-articulos-nuevos-body">
                                                    @if(old('articulos_servicio.new'))
                                                        @foreach(old('articulos_servicio.new.id') as $index => $articuloId)
                                                            <tr>
                                                                <td>
                                                                    <select class="form-control" name="articulos_servicio[new][id][]">
                                                                        @foreach($todosArticulos as $art)
                                                                            <option value="{{ $art->id }}" {{ $art->id == $articuloId ? 'selected' : '' }}>{{ $art->codigo }} {{ $art->nombre }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="number" step="0.01" class="form-control" name="articulos_servicio[new][cantidad][]" value="{{ old('articulos_servicio.new.cantidad')[$index] }}" min="1" required>
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-danger btn-sm remove-articulo-servicio">Eliminar</button>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                </tbody>
                                            </table>
                                            <button type="button" class="btn btn-primary" id="add-articulo-servicio">Agregar Artículo</button>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <a href="{{ url('articulos') }}" class="btn btn-danger"><i class="bi bi-x-circle"></i> Cancelar</a>
                                        <button type="submit" class="btn btn-success"><i class="bi bi-check2-square"></i> Actualizar</button>
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
    document.addEventListener('DOMContentLoaded', function () {
        const tipoSelect = document.querySelector('select[name="tipo"]');
        const servicioFields = document.getElementById('servicio-fields');
        const tablaServicioExistentes = document.getElementById('tabla-servicio-existentes');
        const tablaServicioNuevos = document.getElementById('tabla-servicio-nuevos');
        const addArticuloServicioBtn = document.getElementById('add-articulo-servicio');
        const servicioArticulosExistentesBody = document.getElementById('servicio-articulos-existentes-body');
        const servicioArticulosNuevosBody = document.getElementById('servicio-articulos-nuevos-body');
        const todosArticulos = @json($todosArticulos);

        tipoSelect.addEventListener('change', function () {
            if (this.value === 'servicio') {
                servicioFields.style.display = 'block';
            } else {
                servicioFields.style.display = 'none';
                servicioArticulosExistentesBody.innerHTML = ''; // Limpiar la tabla si se cambia a "articulo"
                servicioArticulosNuevosBody.innerHTML = ''; // Limpiar la tabla si se cambia a "articulo"
            }
        });

        addArticuloServicioBtn.addEventListener('click', function () {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <select class="form-control" name="articulos_servicio[new][id][]">
                        ${todosArticulos.map(art => `<option value="${art.id}">${art.codigo} ${art.nombre}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control" name="articulos_servicio[new][cantidad][]" min="1" required>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-articulo-servicio">Eliminar</button>
                </td>
            `;
            servicioArticulosNuevosBody.appendChild(row);
        });

        servicioArticulosExistentesBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-articulo-servicio')) {
                e.target.closest('tr').remove();
            }
        });

        servicioArticulosNuevosBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-articulo-servicio')) {
                e.target.closest('tr').remove();
            }
        });
    });
</script>
@endsection
