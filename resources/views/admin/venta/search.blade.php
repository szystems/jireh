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
                                <form action="{{ url('ventas')  }}" method="GET">
                                    @csrf
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="fecha_desde" class="form-label">Fecha Desde</label>
                                            <input type="date" class="form-control" name="fecha_desde" value="{{ request('fecha_desde', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d')) }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                                            <input type="date" class="form-control" name="fecha_hasta" value="{{ request('fecha_hasta', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="numero_factura" class="form-label">Número de Factura</label>
                                            <input type="text" class="form-control" name="numero_factura" value="{{ request('numero_factura') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cliente" class="form-label">Cliente</label>
                                            <br>
                                            <select name="cliente" class="form-control select2-cliente">
                                                <option value=""{{ request('cliente') == null ? 'selected' : '' }}>Seleccione un cliente</option>
                                                @foreach($clientes as $cliente)
                                                    <option value="{{ $cliente->id }}" {{ request('cliente') == $cliente->id ? 'selected' : '' }}
                                                        data-nombre="{{ $cliente->nombre }}"
                                                        data-nit="{{ $cliente->nit ?? '' }}"
                                                        data-dpi="{{ $cliente->dpi ?? '' }}"
                                                        data-telefono="{{ $cliente->telefono ?? '' }}">
                                                        {{ $cliente->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="vehiculo" class="form-label">Vehículo</label>
                                            <br>
                                            <select name="vehiculo" class="form-control select2">
                                                <option value=""{{ request('vehiculo') == null ? 'selected' : '' }}>Seleccione un vehículo</option>
                                                @foreach($vehiculos as $vehiculo)
                                                    <option value="{{ $vehiculo->id }}" {{ request('vehiculo') == $vehiculo->id ? 'selected' : '' }}>{{ $vehiculo->modelo }} - {{ $vehiculo->placa }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="tipo_venta" class="form-label">Tipo de Venta</label>
                                            <br>
                                            <select name="tipo_venta" class="form-control select2">
                                                <option value=""{{ request('tipo_venta') == null ? 'selected' : '' }}>Seleccione un tipo de venta</option>
                                                <option value="Car Wash" {{ request('tipo_venta') == 'Car Wash' ? 'selected' : '' }}>Car Wash</option>
                                                <option value="CDS" {{ request('tipo_venta') == 'CDS' ? 'selected' : '' }}>CDS</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="usuario" class="form-label">Usuario</label>
                                            <br>
                                            <select name="usuario" class="form-control select2">
                                                <option value=""{{ request('usuario') == null ? 'selected' : '' }}>Seleccione un usuario</option>
                                                @foreach($usuarios as $usuario)
                                                    <option value="{{ $usuario->id }}" {{ request('usuario') == $usuario->id ? 'selected' : '' }}>{{ $usuario->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label for="estado" class="form-label">Estado</label>
                                            <br>
                                            <select name="estado" class="form-control select2">
                                                <option value=""{{ request('estado') === null ? ' selected' : '' }}>Todos</option>
                                                <option value="1"{{ request('estado') === '1' ? ' selected' : '' }}>Activa</option>
                                                <option value="0"{{ request('estado') === '0' ? ' selected' : '' }}>Cancelada</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <label for="estado_pago" class="form-label">Pago</label>
                                            <br>
                                            <select name="estado_pago" class="form-control select2">
                                                <option value=""{{ request('estado_pago') === null ? ' selected' : '' }}>Todos</option>
                                                <option value="pagado"{{ request('estado_pago') === 'pagado' ? ' selected' : '' }}>Pagado</option>
                                                <option value="pendiente"{{ request('estado_pago') === 'pendiente' ? ' selected' : '' }}>Pendiente</option>
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
    // Esperar a que el documento esté completamente cargado
    $(document).ready(function() {
        try {
            // Inicialización básica para selects normales con ancho al 100%
            $('.select2').select2({
                width: '100%' // Asegurar que todos los select2 ocupen el 100% del ancho
            });

            // Configuración mejorada para select2 de clientes
            $('.select2-cliente').select2({
                width: '100%',
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                },
                // Usamos minimumInputLength para rendimiento
                minimumInputLength: 1,
                // Función personalizada de búsqueda
                matcher: function(params, data) {
                    // Si no hay término de búsqueda, mostrar todo
                    if (!params.term || params.term.trim() === '') {
                        return data;
                    }

                    // Convertir término a minúsculas para comparación
                    const term = params.term.toLowerCase();

                    // Obtener los datos adicionales del elemento
                    const $option = $(data.element);
                    const nombre = String($option.data('nombre') || '').toLowerCase();
                    const nit = String($option.data('nit') || '').toLowerCase();
                    const dpi = String($option.data('dpi') || '').toLowerCase();
                    const telefono = String($option.data('telefono') || '').toLowerCase();

                    // Verificar coincidencias en cualquier campo
                    if (nombre.includes(term) || nit.includes(term) ||
                        dpi.includes(term) || telefono.includes(term)) {
                        return data;
                    }

                    // No hay coincidencia
                    return null;
                },
                templateResult: function(data) {
                    if (!data.id) return data.text;

                    const $option = $(data.element);
                    const nombre = $option.data('nombre') || '';
                    const nit = $option.data('nit') || '';
                    const dpi = $option.data('dpi') || '';
                    const telefono = $option.data('telefono') || '';

                    // Crear HTML para mostrar datos adicionales
                    const $resultado = $('<div>');
                    $resultado.append('<strong>' + nombre + '</strong>');

                    if (nit) $resultado.append('<div><small>NIT: ' + nit + '</small></div>');
                    if (dpi) $resultado.append('<div><small>DPI: ' + dpi + '</small></div>');
                    if (telefono) $resultado.append('<div><small>Tel: ' + telefono + '</small></div>');

                    return $resultado;
                }
            });

            // Agregar validación de datos cargados
            console.log('Clientes cargados:', $('.select2-cliente option').length);
            $('.select2-cliente option').each(function(index) {
                if (index < 5) {
                    const $opt = $(this);
                    console.log('Cliente ' + index + ':', {
                        id: $opt.val(),
                        nombre: $opt.data('nombre'),
                        nit: $opt.data('nit'),
                        dpi: $opt.data('dpi'),
                        telefono: $opt.data('telefono')
                    });
                }
            });
        } catch (error) {
            console.error('Error inicializando Select2:', error);
        }
    });
</script>

<!-- CSS para asegurar que los contenedores de select2 ocupen el ancho completo -->
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
