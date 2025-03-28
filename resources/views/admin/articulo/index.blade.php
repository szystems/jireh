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
                    <h5>Artículos y Servicios</h5>
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
            @include('admin.articulo.search')

            <!-- Estadísticas resumen -->
            <div class="row gx-3 mb-3">
                <div class="col-md-3">
                    <div class="card border-left-primary">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-box text-primary fs-1"></i>
                                </div>
                                <div class="col">
                                    <div class="text-xs text-muted">Total Artículos</div>
                                    <div class="fs-5 mb-0 fw-bold">{{ $estadisticas['total_articulos'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-success">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-tools text-success fs-1"></i>
                                </div>
                                <div class="col">
                                    <div class="text-xs text-muted">Total Servicios</div>
                                    <div class="fs-5 mb-0 fw-bold">{{ $estadisticas['total_servicios'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-exclamation-triangle text-warning fs-1"></i>
                                </div>
                                <div class="col">
                                    <div class="text-xs text-muted">Stock Bajo</div>
                                    <div class="fs-5 mb-0 fw-bold">{{ $estadisticas['stock_bajo'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-danger">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="bi bi-x-circle text-danger fs-1"></i>
                                </div>
                                <div class="col">
                                    <div class="text-xs text-muted">Sin Stock</div>
                                    <div class="fs-5 mb-0 fw-bold">{{ $estadisticas['sin_stock'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Lista de Artículos y Servicios</h5>
                            <div class="d-flex">
                                <div class="btn-group me-2">
                                    <a href="{{ url('export-articulos-pdf') }}{{ request()->getQueryString() ? '?'.request()->getQueryString() : '' }}" class="btn btn-outline-danger btn-sm" target="_blank">
                                        <i class="bi bi-file-pdf"></i> PDF
                                    </a>
                                </div>
                                <div class="btn-group me-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm view-option {{ $viewMode == 'table' ? 'active' : '' }}" data-view="table">
                                        <i class="bi bi-table"></i> Tabla
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm view-option {{ $viewMode == 'cards' ? 'active' : '' }}" data-view="cards">
                                        <i class="bi bi-grid-3x3-gap"></i> Tarjetas
                                    </button>
                                </div>
                                <a href="{{ url('add-articulo') }}" class="btn btn-primary"><i class="bi bi-plus-square"></i> Agregar</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Vista de tabla -->
                            <div class="table-responsive {{ $viewMode == 'table' ? '' : 'd-none' }}" id="table-view">
                                <table class="table align-middle table-striped flex-column">
                                    <thead>
                                        <tr>
                                            <th class="text-center" width="60px"><i class="bi bi-list-task"></i></th>
                                            <th>Artículo/Servicio</th>
                                            <th class="text-center"><span class="badge bg-info">Tipo</span></th>
                                            <th>Descripción</th>
                                            <th class="text-center">Precios</th>
                                            <th class="text-center">Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($articulos as $articulo)
                                        <tr>
                                            <td class="text-center">
                                                <div class="btn-group dropend">
                                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-list-task"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ url('show-articulo', $articulo->id) }}">
                                                                <i class="bi bi-eye-fill text-blue"></i> Información
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ url('edit-articulo', $articulo->id) }}">
                                                                <i class="bi bi-pencil-fill text-warning"></i> Editar
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $articulo->id }}">
                                                                <i class="bi bi-trash-fill text-danger"></i> Eliminar
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <a href="{{ url('show-articulo', $articulo->id) }}">
                                                            <h5 class="text-primary">{{ $articulo->nombre }}</h5>
                                                            <div class="text-muted">
                                                                <div><small>Código: <strong>{{ $articulo->codigo ?: 'N/A' }}</strong></small></div>
                                                                <div><small>Categoría: <strong>{{ $articulo->categoria->nombre }}</strong></small></div>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($articulo->tipo == 'servicio')
                                                    <span class="badge bg-success">Servicio</span>
                                                @else
                                                    <span class="badge bg-primary">Artículo</span>
                                                @endif
                                            </td>
                                            <td><small>{{ Str::limit($articulo->descripcion, 50) }}</small></td>
                                            <td class="text-center">
                                                <div>Compra: <strong><span class="text-danger">{{ $config->currency_simbol }}.{{ number_format($articulo->precio_compra, 2, '.', ',') }}</span></strong></div>
                                                <div>Venta: <strong><span class="text-success">{{ $config->currency_simbol }}.{{ number_format($articulo->precio_venta, 2, '.', ',') }}</span></strong></div>
                                                <small class="text-muted">Ganancia: {{ number_format((($articulo->precio_venta - $articulo->precio_compra) / $articulo->precio_compra) * 100, 1) }}%</small>
                                            </td>
                                            <td class="text-center">
                                                @if($articulo->tipo == 'servicio')
                                                    <span class="badge bg-secondary">N/A</span>
                                                @else
                                                    @if ($articulo->stock <= 0)
                                                        <span class="badge bg-danger">Sin stock</span>
                                                    @elseif ($articulo->stock <= $articulo->stock_minimo)
                                                        <span class="badge bg-warning text-dark">Bajo: {{ $articulo->stock }} {{ $articulo->unidad->abreviatura }}</span>
                                                    @else
                                                        <span class="badge bg-success">{{ $articulo->stock }} {{ $articulo->unidad->abreviatura }}</span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                        @include('admin.articulo.deletemodal')
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Vista de tarjetas -->
                            <div class="row {{ $viewMode == 'cards' ? '' : 'd-none' }}" id="cards-view">
                                @foreach ($articulos as $articulo)
                                <div class="col-md-4 col-xl-3 mb-3">
                                    <div class="card h-100 {{ $articulo->tipo == 'servicio' ? 'border-success' : 'border-primary' }}">
                                        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                                            <span class="{{ $articulo->tipo == 'servicio' ? 'text-success' : 'text-primary' }}">
                                                <i class="{{ $articulo->tipo == 'servicio' ? 'bi bi-tools' : 'bi bi-box' }}"></i>
                                                {{ $articulo->tipo == 'servicio' ? 'Servicio' : 'Artículo' }}
                                            </span>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ url('show-articulo', $articulo->id) }}">
                                                            <i class="bi bi-eye-fill text-blue"></i> Ver detalles
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ url('edit-articulo', $articulo->id) }}">
                                                            <i class="bi bi-pencil-fill text-warning"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $articulo->id }}">
                                                            <i class="bi bi-trash-fill text-danger"></i> Eliminar
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <a href="{{ url('show-articulo', $articulo->id) }}" class="text-decoration-none">
                                                    {{ Str::limit($articulo->nombre, 40) }}
                                                </a>
                                            </h5>
                                            <h6 class="card-subtitle mb-2 text-muted">Código: {{ $articulo->codigo ?: 'N/A' }}</h6>
                                            <p class="card-text small">{{ Str::limit($articulo->descripcion, 60) }}</p>
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Categoría:</span>
                                                <span class="text-primary">{{ $articulo->categoria->nombre }}</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Venta:</span>
                                                <span class="text-success">{{ $config->currency_simbol }}.{{ number_format($articulo->precio_venta, 2) }}</span>
                                            </li>
                                            {{-- @if($articulo->tipo == 'articulo') --}}
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Stock:</span>
                                                @if ($articulo->stock <= 0)
                                                    <span class="badge bg-danger">Sin stock</span>
                                                @elseif ($articulo->stock <= $articulo->stock_minimo)
                                                    <span class="badge bg-warning text-dark">{{ $articulo->stock }} {{ $articulo->unidad->abreviatura }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ $articulo->stock }} {{ $articulo->unidad->abreviatura }}</span>
                                                @endif
                                            </li>
                                            {{-- @endif --}}
                                        </ul>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $articulos->appends(request()->query())->links() }}
                            </div>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Cambio de vista (tabla/tarjetas)
        const viewOptions = document.querySelectorAll('.view-option');
        const tableView = document.getElementById('table-view');
        const cardsView = document.getElementById('cards-view');

        viewOptions.forEach(option => {
            option.addEventListener('click', function() {
                const view = this.getAttribute('data-view');

                // Actualizar botones activos
                viewOptions.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Mostrar vista seleccionada
                if (view === 'table') {
                    tableView.classList.remove('d-none');
                    cardsView.classList.add('d-none');
                } else {
                    tableView.classList.add('d-none');
                    cardsView.classList.remove('d-none');
                }

                // Guardar preferencia en localStorage
                localStorage.setItem('articulosViewMode', view);

                // Actualizar preferencia en servidor
                fetch('{{ url("set-view-preference") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        preference: 'articulos_view_mode',
                        value: view
                    })
                });
            });
        });

        // Inicializar vista desde localStorage si existe
        const savedViewMode = localStorage.getItem('articulosViewMode');
        if (savedViewMode) {
            document.querySelector(`.view-option[data-view="${savedViewMode}"]`).click();
        }
    });
</script>
@endsection
