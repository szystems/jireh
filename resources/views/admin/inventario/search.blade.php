<!-- Row start -->
<div class="row gx-3">
    <div class="col-xl-12">
        <div class="card card-background-mask-info">
            <div class="card-body">

                <div class="accordion" id="accordionSpecialTitle">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSpecialTitleOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseSpecialTitleOne" aria-expanded="true"
                                aria-controls="collapseSpecialTitleOne">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-filter text-info"></i>
                                    <div class="ms-3">
                                        <h5 class="text-yellow">Filtros de Búsqueda</h5>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapseSpecialTitleOne" class="accordion-collapse collapse"
                            aria-labelledby="headingSpecialTitleOne" data-bs-parent="#accordionSpecialTitle">
                            <div class="accordion-body">
                                <form action="{{ url('inventario') }}" method="GET">
                                    @csrf
                                    <div class="row gx-3">

                                        <div class="col-md-6 mb-3">
                                            <!-- Form Field Start -->
                                            <div class="mb-3">
                                                <label for="articulo" class="form-label">Artículo</label>
                                                <br>
                                                <select class="form-control select2-full-width" id="articulo" name="articulo" style="width: 100%;">
                                                    <option value="">Seleccione un artículo</option>
                                                    @foreach($todosArticulos as $articulo)
                                                        <option value="{{ $articulo->id }}" {{ request('articulo') == $articulo->id ? 'selected' : '' }}>
                                                            {{ $articulo->codigo }} {{ $articulo->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <!-- Form Field Start -->
                                            <div class="mb-3">
                                                <label for="categoria" class="form-label">Categoría</label>
                                                <select name="categoria" id="categoria" class="form-select select2-full-width" style="width: 100%;">
                                                    <option value="">Todas</option>
                                                    @foreach($categorias as $categoria)
                                                        <option value="{{ $categoria->id }}"{{ old('categoria', request('categoria')) == $categoria->id ? ' selected' : '' }}>{{ $categoria->nombre }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <!-- Form Field Start -->
                                            <div class="mb-3">
                                                <label for="stock" class="form-label">Stock</label>
                                                <select name="stock" class="form-select" aria-label="Default select example">
                                                    <option value=""{{ request('stock') == '' ? ' selected' : '' }}>Todos</option>
                                                    <option value="con_stock"{{ request('stock') == 'con_stock' ? ' selected' : '' }}>Con Stock</option>
                                                    <option value="sin_stock"{{ request('stock') == 'sin_stock' ? ' selected' : '' }}>Sin Stock</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <!-- Form Field Start -->
                                            <div class="mb-3">
                                                <label for="stock_minimo" class="form-label">Stock Minimo</label>
                                                <select name="stock_minimo" class="form-select" aria-label="Default select example">
                                                    <option value=""{{ request('stock_minimo') == '' ? ' selected' : '' }}>Todos</option>
                                                    <option value="<="{{ request('stock_minimo') == '<=' ? ' selected' : '' }}><=</option>
                                                    <option value=">"{{ request('stock_minimo') == '>' ? ' selected' : '' }}>></option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <!-- Form Field Start -->
                                            <div class="mb-3">
                                                <label for="tipo" class="form-label">Tipo</label>
                                                <select name="tipo" class="form-select" aria-label="Default select example">
                                                    <option value=""{{ request('tipo') == '' ? ' selected' : '' }}>Todos</option>
                                                    <option value="articulo"{{ request('tipo') == 'articulo' ? ' selected' : '' }}>Artículo</option>
                                                    <option value="servicio"{{ request('tipo') == 'servicio' ? ' selected' : '' }}>Servicio</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <!-- Form Field Start -->
                                            <div class="mb-3">
                                                <label for="ordenar" class="form-label">Ordenar por</label>
                                                <select name="ordenar" class="form-select" aria-label="Default select example">
                                                    <option value="nombre"{{ request('ordenar') == 'nombre' ? ' selected' : '' }}>Nombre</option>
                                                    <option value="codigo"{{ request('ordenar') == 'codigo' ? ' selected' : '' }}>Código</option>
                                                    <option value="precio_compra"{{ request('ordenar') == 'precio_compra' ? ' selected' : '' }}>Precio Compra</option>
                                                    <option value="precio_venta"{{ request('ordenar') == 'precio_venta' ? ' selected' : '' }}>Precio Venta</option>
                                                    <option value="stock"{{ request('ordenar') == 'stock' ? ' selected' : '' }}>Stock</option>
                                                    <option value="categoria_id"{{ request('ordenar') == 'categoria_id' ? ' selected' : '' }}>Categoría</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-1 mb-3">
                                            <!-- Form Field Start -->
                                            <div class="mb-3 mt-4">
                                                <button type="submit" class="btn btn-info">
                                                    <i class="bi bi-search"></i>
                                                </button>
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
    </div>
</div>

<style>
    /* Asegurar que Select2 ocupe el ancho completo del contenedor */
    .select2-container {
        width: 100% !important;
    }

    /* Ajuste adicional para el ancho del dropdown */
    .select2-dropdown {
        width: auto !important;
    }

    /* Asegurar que el input dentro del select2 ocupe todo el ancho disponible */
    .select2-search__field {
        width: 100% !important;
    }
</style>

<script>
    $(document).ready(function() {
        // Inicializar Select2 para artículos
        $("#articulo").select2({
            placeholder: "Buscar por nombre o código",
            allowClear: true,
            width: '100%' // Forzar ancho al 100%
        });

        // Inicializar Select2 para categorías
        $("#categoria").select2({
            placeholder: "Seleccione una categoría",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<!-- Row end -->
