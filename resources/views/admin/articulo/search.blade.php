<!-- Row start -->
<div class="row gx-3 mb-3">
    <div class="col-xl-12">
        <div class="card card-background-mask-info">
            <div class="card-body">
                <form action="{{ url('articulos') }}" method="GET" id="search-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="input-group">
                                <input class="form-control" placeholder="Buscar por nombre o código..." name="farticulo" value="{{ $queryArticulo ?? '' }}"/>
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch">
                                <i class="bi bi-funnel"></i> Filtros avanzados
                            </button>
                        </div>
                    </div>

                    <div class="collapse mt-2" id="advancedSearch">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label for="tipo" class="form-label">Tipo</label>
                                <select class="form-select" name="ftipo">
                                    <option value="">Todos</option>
                                    <option value="articulo" {{ isset($filtros['ftipo']) && $filtros['ftipo'] == 'articulo' ? 'selected' : '' }}>Artículos</option>
                                    <option value="servicio" {{ isset($filtros['ftipo']) && $filtros['ftipo'] == 'servicio' ? 'selected' : '' }}>Servicios</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="categoria" class="form-label">Categoría</label>
                                <select class="form-select" name="fcategoria">
                                    <option value="">Todas</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" {{ isset($filtros['fcategoria']) && $filtros['fcategoria'] == $categoria->id ? 'selected' : '' }}>
                                            {{ $categoria->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="stock" class="form-label">Estado de Stock</label>
                                <select class="form-select" name="fstock">
                                    <option value="">Todos</option>
                                    <option value="disponible" {{ isset($filtros['fstock']) && $filtros['fstock'] == 'disponible' ? 'selected' : '' }}>Disponible</option>
                                    <option value="bajo" {{ isset($filtros['fstock']) && $filtros['fstock'] == 'bajo' ? 'selected' : '' }}>Bajo mínimo</option>
                                    <option value="agotado" {{ isset($filtros['fstock']) && $filtros['fstock'] == 'agotado' ? 'selected' : '' }}>Sin stock</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label for="orden" class="form-label">Ordenar por</label>
                                <select class="form-select" name="forden">
                                    <option value="nombre_asc" {{ isset($filtros['forden']) && $filtros['forden'] == 'nombre_asc' ? 'selected' : '' }}>Nombre (A-Z)</option>
                                    <option value="nombre_desc" {{ isset($filtros['forden']) && $filtros['forden'] == 'nombre_desc' ? 'selected' : '' }}>Nombre (Z-A)</option>
                                    <option value="precio_asc" {{ isset($filtros['forden']) && $filtros['forden'] == 'precio_asc' ? 'selected' : '' }}>Precio (menor a mayor)</option>
                                    <option value="precio_desc" {{ isset($filtros['forden']) && $filtros['forden'] == 'precio_desc' ? 'selected' : '' }}>Precio (mayor a menor)</option>
                                    <option value="stock_asc" {{ isset($filtros['forden']) && $filtros['forden'] == 'stock_asc' ? 'selected' : '' }}>Stock (menor a mayor)</option>
                                    <option value="stock_desc" {{ isset($filtros['forden']) && $filtros['forden'] == 'stock_desc' ? 'selected' : '' }}>Stock (mayor a menor)</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12 text-end">
                                <a href="{{ url('articulos') }}" class="btn btn-sm btn-secondary">
                                    <i class="bi bi-x-circle"></i> Limpiar filtros
                                </a>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-filter"></i> Aplicar filtros
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Row end -->
