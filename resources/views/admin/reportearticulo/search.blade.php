{{-- filepath: c:\Users\szott\Dropbox\Desarrollo\jireh\resources\views\admin\venta\search.blade.php --}}
<!-- Row start -->
<div class="row gx-3 mb-4">
    <div class="col-xl-12">
        <div class="accordion" id="accordionFiltros">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFiltros">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiltros" aria-expanded="false" aria-controls="collapseFiltros">
                        <i class="bi bi-funnel me-2"></i> Filtros de Búsqueda
                    </button>
                </h2>
                <div id="collapseFiltros" class="accordion-collapse collapse" aria-labelledby="headingFiltros" data-bs-parent="#accordionFiltros">
                    <div class="accordion-body">
                        <div class="card card-background-mask-info border-0">
                            <div class="card-body">
                                <form action="{{ url('reportearticulos')  }}" method="GET">
                                    @csrf
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label for="fecha_desde" class="form-label">Fecha Desde</label>
                                            <input type="date" class="form-control" name="fecha_desde" value="{{ request('fecha_desde', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d')) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                                            <input type="date" class="form-control" name="fecha_hasta" value="{{ request('fecha_hasta', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="codigo" class="form-label">Código de Artículo</label>
                                            <input type="text" class="form-control" name="codigo" value="{{ request('codigo') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="articulo" class="form-label">Artículo</label>
                                            <select name="articulo" class="form-control select2">
                                                <option value=""{{ request('articulo') == null ? 'selected' : '' }}>Todos los artículos</option>
                                                @foreach($articulos as $articulo)
                                                    <option value="{{ $articulo->id }}" {{ request('articulo') == $articulo->id ? 'selected' : '' }}>{{ $articulo->codigo }} - {{ $articulo->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="categoria" class="form-label">Categoría</label>
                                            <select name="categoria" class="form-control select2">
                                                <option value=""{{ request('categoria') == null ? 'selected' : '' }}>Todas las categorías</option>
                                                @foreach($categorias as $categoria)
                                                    <option value="{{ $categoria->id }}" {{ request('categoria') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="usuario" class="form-label">Vendedor</label>
                                            <select name="usuario" class="form-control select2">
                                                <option value=""{{ request('usuario') == null ? 'selected' : '' }}>Todos los vendedores</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}" {{ request('usuario') == $usuario->id ? 'selected' : '' }}>{{ $usuario->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="cliente" class="form-label">Cliente</label>
                                            <select name="cliente" class="form-control select2">
                                                <option value=""{{ request('cliente') == null ? 'selected' : '' }}>Todos los clientes</option>
                                                @foreach($clientes as $cliente)
                                                    <option value="{{ $cliente->id }}" {{ request('cliente') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="tipo_venta" class="form-label">Tipo de Venta</label>
                                            <select name="tipo_venta" class="form-control select2">
                                                <option value=""{{ request('tipo_venta') == null ? 'selected' : '' }}>Todos los tipos</option>
                                                <option value="Car Wash" {{ request('tipo_venta') == 'Car Wash' ? 'selected' : '' }}>Car Wash</option>
                                                <option value="CDS" {{ request('tipo_venta') == 'CDS' ? 'selected' : '' }}>CDS</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="estado" class="form-label">Estado de Venta</label>
                                            <select name="estado" class="form-control select2">
                                                <option value=""{{ request('estado') === null ? ' selected' : '' }}>Todos</option>
                                                <option value="1"{{ request('estado') === '1' || request('estado') === null ? ' selected' : '' }}>Activa</option>
                                                <option value="0"{{ request('estado') === '0' ? ' selected' : '' }}>Cancelada</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="bi bi-search"></i> Buscar
                                        </button>
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
<!-- Row end -->

<script>
    // Inicializar select2 cuando el documento esté listo
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });
    });
</script>

<style>
    /* Hacer que el contenedor de select2 ocupe el ancho completo */
    .select2-container {
        width: 100% !important;
    }

    /* Asegurar que el elemento select2-selection también se ajuste al 100% */
    .select2-selection {
        width: 100% !important;
    }
</style>
