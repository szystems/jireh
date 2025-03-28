@extends('layouts.admin')

@section('content')
    <!-- Cargar Chart.js directamente para asegurar que esté disponible -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-inboxes"></i>
                </div>
                <div class="page-title">
                    <h5>Inventario</h5>
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

            <!-- Panel de estadísticas generales -->
            <div class="row gx-3 mb-3">
                <div class="col-xl-3 col-md-6 mb-2">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Artículos</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $articulos->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-box-seam fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-2">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Valor de Inventario</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $config->currency_simbol }}.{{ number_format($articulos->sum(function($articulo) { return $articulo->precio_compra * $articulo->stock; }), 2, '.', ',') }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-2">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Artículos con Stock Bajo</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $articulos->filter(function($articulo) { return $articulo->stock <= $articulo->stock_minimo && $articulo->stock > 0; })->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-2">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Artículos Agotados</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $articulos->where('stock', '<=', 0)->count() }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="bi bi-x-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navegación de pestañas -->
            <ul class="nav nav-tabs mb-3" id="inventarioTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="listado-tab" data-bs-toggle="tab" data-bs-target="#listado-tab-pane" type="button" role="tab" aria-controls="listado-tab-pane" aria-selected="true">
                        <i class="bi bi-list-ul"></i> Listado de Inventario
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="estadisticas-tab" data-bs-toggle="tab" data-bs-target="#estadisticas-tab-pane" type="button" role="tab" aria-controls="estadisticas-tab-pane" aria-selected="false">
                        <i class="bi bi-bar-chart-line"></i> Estadísticas y Gráficos
                    </button>
                </li>
            </ul>

            <!-- Contenido de las pestañas -->
            <div class="tab-content" id="inventarioTabsContent">
                <!-- Pestaña de Listado -->
                <div class="tab-pane fade show active" id="listado-tab-pane" role="tabpanel" aria-labelledby="listado-tab" tabindex="0">
                    @include('admin.inventario.search')

                    <!-- Row start -->
                    <div class="row gx-3">
                        <div class="col-sm-12 col-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="card-title">
                                        Listado de Inventario
                                        <small class="text-secondary"><u>Filtros:</u></small>
                                        <small class="text-muted">
                                            Encontrados: <small class="text-info">{{ $articulos->count() }},</small>
                                            @if (request('articulo'))
                                                Articulo:  <small class="text-info">{{ request('articulo') }},</small>
                                            @endif
                                            @if (request('categoria'))
                                                @php
                                                    $categoria = \App\Models\Categoria::find( request('categoria') );
                                                @endphp
                                                Categoría:  <small class="text-info">{{ $categoria->nombre }},</small>
                                            @endif
                                            @if (request('proveedor_id'))
                                                @php
                                                    $proveedor = \App\Models\Proveedor::find( request('proveedor_id') );
                                                @endphp
                                                Proveedor:  <small class="text-info">{{ $proveedor->nombre }},</small>
                                            @endif
                                            @if (request('stock'))
                                                Stock:  <small class="text-info">{{ request('stock') }},</small>
                                            @endif
                                            @if (request('stock_minimo'))
                                                Stock Minimo:  <small class="text-info">{{ request('stock_minimo') }},</small>
                                            @endif
                                            @if (request('tipo'))
                                                Tipo:  <small class="text-info">{{ request('tipo') }},</small>
                                            @endif
                                            @if (request('ordenar'))
                                                Ordenar por:  <small class="text-info">{{ request('ordenar') }},</small>
                                            @endif
                                        </small>
                                        <div class="d-flex justify-content-end">
                                            <button type="button" class="btn btn-danger m-1" data-bs-toggle="modal" data-bs-target="#printInventarioModal">
                                                <i class="bi bi-file-pdf"></i> PDF
                                            </button>
                                        </div>

                                        @include('admin.inventario.printinventariomodal')
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-striped flex-column">
                                            <thead>
                                                <tr>
                                                    <td>Artículo</td>
                                                    <td align="center">Categoría</td>
                                                    @if (Auth::user()->role_as == '0')
                                                        <td align="center">Precio Compra</td>
                                                    @endif
                                                    <td align="center">Precio Venta</td>
                                                    <td>Stock</td>
                                                    <td>Estado</td>
                                                    <td>Acciones Rápidas</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($articulos as $articulo)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <p class="m-0">
                                                                <a class="text-primary" href="{{ url('show-articulo/'.$articulo->id) }}"><font color="gray"><small>{{ $articulo->codigo }}</small></font> <b>{{ $articulo->nombre }} <font color="orange">({{ $articulo->tipo }})</font></b></a>
                                                                <br>
                                                                <small>{{ $articulo->descripcion }}</small>
                                                            </p>
                                                        </div>
                                                    </td>
                                                    <td align="center">{{ $articulo->categoria->nombre }}</td>
                                                    @if (Auth::user()->role_as == '0')
                                                        <td align="center">
                                                            <p><small class=" text-info"><strong>{{ $config->currency_simbol }}.{{ number_format($articulo->precio_compra, 2, '.', ',') }}</strong></small></p>
                                                        </td>
                                                    @endif
                                                    <td align="center">
                                                        <p><small class=" text-success"><strong>{{ $config->currency_simbol }}.{{ number_format($articulo->precio_venta, 2, '.', ',') }}</strong></small></p>
                                                    </td>
                                                    <td>
                                                        <p>
                                                        @if ($articulo->stock <= 0)
                                                            <strong class="text-danger">{{ $articulo->stock }} ({{ $articulo->unidad->abreviatura }})</strong>
                                                        @elseif (($articulo->stock > 0) and ($articulo->stock <= $articulo->stock_minimo))
                                                            <strong class="text-warning">{{ $articulo->stock }} ({{ $articulo->unidad->abreviatura }})</strong>
                                                        @else
                                                            <strong class="text-success">{{ $articulo->stock }} ({{ $articulo->unidad->abreviatura }})</strong>
                                                        @endif
                                                        </p>
                                                    </td>
                                                    <td>
                                                        @if ($articulo->stock <= 0)
                                                            <span class="badge bg-danger">Agotado</span>
                                                        @elseif (($articulo->stock > 0) and ($articulo->stock <= $articulo->stock_minimo))
                                                            <span class="badge bg-warning">Stock Bajo</span>
                                                        @else
                                                            <span class="badge bg-success">Disponible</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ url('edit-articulo/'.$articulo->id) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            @if ($articulo->stock <= $articulo->stock_minimo)
                                                            <a href="{{ url('add-ingreso?articulo='.$articulo->id) }}" class="btn btn-sm btn-outline-warning" title="Reabastecer">
                                                                <i class="bi bi-plus-circle"></i>
                                                            </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Row end -->
                </div>

                <!-- Pestaña de Estadísticas -->
                <div class="tab-pane fade" id="estadisticas-tab-pane" role="tabpanel" aria-labelledby="estadisticas-tab" tabindex="0">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 text-white">Análisis de Inventario</h5>
                        </div>
                        <div class="card-body">
                            <!-- Sección de gráficos -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title">
                                                Distribución por Categorías
                                                @if(request('articulo') || request('categoria') || request('proveedor_id') || request('stock') || request('stock_minimo') || request('tipo'))
                                                    <span class="badge bg-info">Datos Filtrados</span>
                                                @endif
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="categoriaChart" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title">
                                                Estado del Inventario
                                                @if(request('articulo') || request('categoria') || request('proveedor_id') || request('stock') || request('stock_minimo') || request('tipo'))
                                                    <span class="badge bg-info">Datos Filtrados</span>
                                                @endif
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="estadoChart" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gráficos adicionales basados en filtros -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title">Valor de Inventario por Categoría</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="valorCategoriaChart" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>

                                @if(isset($chartData['precioStock']))
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title">Precio vs. Stock en Categoría: {{ $chartData['categoriaNombre'] }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="precioStockChart" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            @if(isset($chartData['tiposProveedor']))
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title">Distribución por Tipo - Proveedor: {{ $chartData['proveedorNombre'] }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="tiposProveedorChart" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(isset($chartData['stockRatio']))
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title">Artículos con Menor Ratio Stock/Stock Mínimo</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="stockRatioChart" height="150"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(isset($chartData['distribucionPrecios']))
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title">Distribución por Precio - Tipo: {{ $chartData['tipoSeleccionado'] }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="distribucionPreciosChart" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title">Estadísticas de Precio - Tipo: {{ $chartData['tipoSeleccionado'] }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4 text-center">
                                                    <div class="card bg-light p-3">
                                                        <h5 class="text-primary">Mínimo</h5>
                                                        <h4>{{ $config->currency_simbol }}{{ number_format($chartData['precioStats']['minimo'], 2) }}</h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    <div class="card bg-light p-3">
                                                        <h5 class="text-success">Promedio</h5>
                                                        <h4>{{ $config->currency_simbol }}{{ number_format($chartData['precioStats']['promedio'], 2) }}</h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    <div class="card bg-light p-3">
                                                        <h5 class="text-danger">Máximo</h5>
                                                        <h4>{{ $config->currency_simbol }}{{ number_format($chartData['precioStats']['maximo'], 2) }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->

<!-- Script para inicializar las gráficas cuando se muestra la pestaña de estadísticas -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Objeto para almacenar las instancias de los gráficos
        let charts = {};

        // Inicializar los gráficos cuando se activa la pestaña de estadísticas
        document.getElementById('estadisticas-tab').addEventListener('shown.bs.tab', function(e) {
            initializeCharts();
        });

        // Si la pestaña de estadísticas está activa al cargar la página, inicializar los gráficos
        if (document.querySelector('#estadisticas-tab.active')) {
            initializeCharts();
        }

        function initializeCharts() {
            try {
                console.log('Inicializando gráficas de inventario...');

                // Datos para el gráfico de categorías
                const categoriasData = {
                    labels: [
                        @foreach($categorias->take(5) as $categoria)
                            "{{ $categoria->nombre }}",
                        @endforeach
                        "Otras"
                    ],
                    datasets: [{
                        data: [
                            @foreach($categorias->take(5) as $categoria)
                                {{ $articulos->where('categoria_id', $categoria->id)->count() }},
                            @endforeach
                            {{ $articulos->whereNotIn('categoria_id', $categorias->take(5)->pluck('id'))->count() }}
                        ],
                        backgroundColor: [
                            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
                        ]
                    }]
                };

                // Datos para el gráfico de estado del inventario
                const estadoData = {
                    labels: ['Agotado', 'Stock Bajo', 'Disponible'],
                    datasets: [{
                        data: [
                            {{ $articulos->where('stock', '<=', 0)->count() }},
                            {{ $articulos->filter(function($articulo) { return $articulo->stock <= $articulo->stock_minimo && $articulo->stock > 0; })->count() }},
                            {{ $articulos->filter(function($articulo) { return $articulo->stock > $articulo->stock_minimo; })->count() }}
                        ],
                        backgroundColor: ['#e74a3b', '#f6c23e', '#1cc88a']
                    }]
                };

                console.log('Categorías data:', categoriasData);
                console.log('Estado data:', estadoData);

                // Verificar que los elementos canvas existan
                const categoriaChartEl = document.getElementById('categoriaChart');
                const estadoChartEl = document.getElementById('estadoChart');
                const valorCategoriaChartEl = document.getElementById('valorCategoriaChart');

                if (!categoriaChartEl) console.error('No se encontró el elemento categoriaChart');
                if (!estadoChartEl) console.error('No se encontró el elemento estadoChart');
                if (!valorCategoriaChartEl) console.error('No se encontró el elemento valorCategoriaChart');

                // Crear gráfico de categorías si no existe ya
                if (categoriaChartEl && !charts.categoriaChart) {
                    charts.categoriaChart = new Chart(categoriaChartEl, {
                        type: 'doughnut',
                        data: categoriasData,
                        options: {
                            maintainAspectRatio: false,
                            plugins: {
                                tooltip: { enabled: true },
                                legend: { position: 'bottom' }
                            }
                        }
                    });
                    console.log('Gráfico de categorías creado');
                }

                // Crear gráfico de estado si no existe ya
                if (estadoChartEl && !charts.estadoChart) {
                    charts.estadoChart = new Chart(estadoChartEl, {
                        type: 'pie',
                        data: estadoData,
                        options: {
                            maintainAspectRatio: false,
                            plugins: {
                                tooltip: { enabled: true },
                                legend: { position: 'bottom' }
                            }
                        }
                    });
                    console.log('Gráfico de estado creado');
                }

                // Verificar si existen datos para valorCategoria
                @if(isset($chartData['valorCategoria']))
                // Datos para el gráfico de valor por categoría
                const valorCategoriaData = {
                    labels: @json($chartData['valorCategoria']['labels'] ?? []),
                    datasets: [{
                        label: 'Valor en Inventario',
                        data: @json($chartData['valorCategoria']['data'] ?? []),
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
                    }]
                };

                console.log('Valor Categoría data:', valorCategoriaData);

                // Crear gráfico de valor por categoría si no existe ya
                if (valorCategoriaChartEl && !charts.valorCategoriaChart) {
                    charts.valorCategoriaChart = new Chart(valorCategoriaChartEl, {
                        type: 'bar',
                        data: valorCategoriaData,
                        options: {
                            indexAxis: 'y',
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return '{{ $config->currency_simbol }}' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                    console.log('Gráfico de valor por categoría creado');
                }
                @endif

                // Gráficos condicionales según los filtros
                @if(isset($chartData['precioStock']))
                const precioStockChartEl = document.getElementById('precioStockChart');
                if (precioStockChartEl && !charts.precioStockChart) {
                    charts.precioStockChart = new Chart(precioStockChartEl, {
                        type: 'bar',
                        data: {
                            labels: @json($chartData['precioStock']['labels']),
                            datasets: [
                                {
                                    type: 'line',
                                    label: 'Precio Venta',
                                    data: @json($chartData['precioStock']['precios']),
                                    borderColor: '#e74a3b',
                                    backgroundColor: 'rgba(231, 74, 59, 0.2)',
                                    yAxisID: 'y1',
                                    tension: 0.4
                                },
                                {
                                    type: 'bar',
                                    label: 'Stock',
                                    data: @json($chartData['precioStock']['stock']),
                                    backgroundColor: '#36b9cc',
                                    yAxisID: 'y'
                                }
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Stock'
                                    }
                                },
                                y1: {
                                    beginAtZero: true,
                                    position: 'right',
                                    title: {
                                        display: true,
                                        text: 'Precio'
                                    },
                                    grid: {
                                        drawOnChartArea: false
                                    },
                                    ticks: {
                                        callback: function(value) {
                                            return '{{ $config->currency_simbol }}' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                    console.log('Gráfico precio vs stock creado');
                }
                @endif

                @if(isset($chartData['tiposProveedor']))
                const tiposProveedorChartEl = document.getElementById('tiposProveedorChart');
                if (tiposProveedorChartEl && !charts.tiposProveedorChart) {
                    charts.tiposProveedorChart = new Chart(tiposProveedorChartEl, {
                        type: 'pie',
                        data: {
                            labels: @json($chartData['tiposProveedor']['labels']),
                            datasets: [{
                                data: @json($chartData['tiposProveedor']['data']),
                                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796']
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                    console.log('Gráfico tipos proveedor creado');
                }
                @endif

                @if(isset($chartData['stockRatio']))
                const stockRatioChartEl = document.getElementById('stockRatioChart');
                if (stockRatioChartEl && !charts.stockRatioChart) {
                    charts.stockRatioChart = new Chart(stockRatioChartEl, {
                        type: 'bar',
                        data: {
                            labels: @json($chartData['stockRatio']['labels']),
                            datasets: [
                                {
                                    label: 'Stock Actual',
                                    data: @json($chartData['stockRatio']['actuales']),
                                    backgroundColor: '#4e73df'
                                },
                                {
                                    label: 'Stock Mínimo',
                                    data: @json($chartData['stockRatio']['minimos']),
                                    backgroundColor: '#e74a3b'
                                }
                            ]
                        },
                        options: {
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Cantidad'
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        afterLabel: function(context) {
                                            const index = context.dataIndex;
                                            const ratio = (@json($chartData['stockRatio']['ratios']))[index];
                                            return 'Ratio: ' + ratio.toFixed(2);
                                        }
                                    }
                                }
                            }
                        }
                    });
                    console.log('Gráfico stock ratio creado');
                }
                @endif

                @if(isset($chartData['distribucionPrecios']))
                const distribucionPreciosChartEl = document.getElementById('distribucionPreciosChart');
                if (distribucionPreciosChartEl && !charts.distribucionPreciosChart) {
                    charts.distribucionPreciosChart = new Chart(distribucionPreciosChartEl, {
                        type: 'bar',
                        data: {
                            labels: @json($chartData['distribucionPrecios']['labels']),
                            datasets: [{
                                label: 'Cantidad de Artículos',
                                data: @json($chartData['distribucionPrecios']['data']),
                                backgroundColor: '#1cc88a'
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Cantidad'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Rango de Precios ({{ $config->currency_simbol }})'
                                    }
                                }
                            }
                        }
                    });
                    console.log('Gráfico distribución precios creado');
                }
                @endif
            } catch (error) {
                console.error('Error al crear los gráficos:', error);
            }
        }
    });
</script>

@endsection

