@extends('layouts.admin')
@section('content')
    <!-- Incluir Chart.js para los gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-cart-plus"></i>
                </div>
                <div class="page-title">
                    <h5>Ingresos de Inventario</h5>
                </div>
            </div>
        </div>
        <div class="content-wrapper">
            <!-- Estadísticas en cards - Siempre visibles independientemente de la pestaña -->
            <div class="row mb-4">
                @php
                    // Calcular estadísticas
                    $totalIngresos = $ingresos->count();
                    $totalGastado = $ingresos->sum(function($ingreso) {
                        return $ingreso->detalles->sum(function($detalle) {
                            return $detalle->cantidad * $detalle->precio_compra;
                        });
                    });

                    $totalArticulos = $ingresos->sum(function($ingreso) {
                        return $ingreso->detalles->sum('cantidad');
                    });

                    $tiposCompra = $ingresos->groupBy('tipo_compra')
                        ->map(function($ingresos, $tipo) {
                            return [
                                'tipo' => $tipo ?: 'Sin especificar',
                                'cantidad' => $ingresos->count(),
                                'monto' => $ingresos->sum(function($ingreso) {
                                    return $ingreso->detalles->sum(function($detalle) {
                                        return $detalle->cantidad * $detalle->precio_compra;
                                    });
                                })
                            ];
                        })->values();

                    // Para cálculos por proveedor
                    $proveedoresStats = $ingresos->groupBy('proveedor_id')
                        ->map(function($ingresos, $proveedorId) use ($proveedores) {
                            $proveedor = $proveedores->find($proveedorId);
                            $nombre = $proveedor ? $proveedor->nombre : 'Sin proveedor';

                            return [
                                'proveedor' => $nombre,
                                'cantidad' => $ingresos->count(),
                                'monto' => $ingresos->sum(function($ingreso) {
                                    return $ingreso->detalles->sum(function($detalle) {
                                        return $detalle->cantidad * $detalle->precio_compra;
                                    });
                                })
                            ];
                        })
                        ->sortByDesc('monto')
                        ->take(5)
                        ->values();

                    // Promedio por ingreso
                    $promedioGasto = $totalIngresos > 0 ? $totalGastado / $totalIngresos : 0;
                @endphp

                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-primary">
                                <i class="bi bi-receipt"></i>
                            </div>
                            <div class="fs-6 fw-bold">Ingresos</div>
                            <div class="fs-4 text-primary">{{ number_format($totalIngresos, 0, '.', ',') }}</div>
                            <div class="small text-muted">registros totales</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-danger">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <div class="fs-6 fw-bold">Total Gastado</div>
                            <div class="fs-4 text-danger">{{ $config->currency_simbol }}.{{ number_format($totalGastado, 2, '.', ',') }}</div>
                            <div class="small text-muted">monto total</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-info">
                                <i class="bi bi-box-fill"></i>
                            </div>
                            <div class="fs-6 fw-bold">Artículos</div>
                            <div class="fs-4 text-info">{{ number_format($totalArticulos, 0, '.', ',') }}</div>
                            <div class="small text-muted">unidades ingresadas</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-success">
                                <i class="bi bi-calculator"></i>
                            </div>
                            <div class="fs-6 fw-bold">Promedio</div>
                            <div class="fs-4 text-success">{{ $config->currency_simbol }}.{{ number_format($promedioGasto, 2, '.', ',') }}</div>
                            <div class="small text-muted">por ingreso</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navegación de pestañas -->
            <ul class="nav nav-tabs mb-3" id="ingresoTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="listado-tab" data-bs-toggle="tab" data-bs-target="#listado-tab-pane" type="button" role="tab" aria-controls="listado-tab-pane" aria-selected="true">
                        <i class="bi bi-list-ul"></i> Listado de Ingresos
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="estadisticas-tab" data-bs-toggle="tab" data-bs-target="#estadisticas-tab-pane" type="button" role="tab" aria-controls="estadisticas-tab-pane" aria-selected="false">
                        <i class="bi bi-bar-chart-line"></i> Estadísticas y Gráficos
                    </button>
                </li>
            </ul>

            <!-- Contenido de las pestañas -->
            <div class="tab-content" id="ingresoTabsContent">
                <!-- Pestaña de Listado -->
                <div class="tab-pane fade show active" id="listado-tab-pane" role="tabpanel" aria-labelledby="listado-tab" tabindex="0">
                    @include('admin.ingreso.search')
                    <div class="filters mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Filtros aplicados:</strong>
                                <span>Fecha: {{ \Carbon\Carbon::parse(request('fecha_desde', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d')))->format('d/m/Y') }} - {{ \Carbon\Carbon::parse(request('fecha_hasta', \Carbon\Carbon::now()->format('Y-m-d')))->format('d/m/Y') }}</span>
                                @if(request('numero_factura'))
                                    <span class="badge bg-info ms-1">Factura: {{ request('numero_factura') }}</span>
                                @endif
                                @if(request('proveedor') && $proveedores->find(request('proveedor')))
                                    <span class="badge bg-primary ms-1">Proveedor: {{ $proveedores->find(request('proveedor'))->nombre }}</span>
                                @endif
                                @if(request('tipo_compra'))
                                    <span class="badge bg-warning text-dark ms-1">Tipo: {{ request('tipo_compra') }}</span>
                                @endif
                                @if(request('usuario') && $usuarios->find(request('usuario')))
                                    <span class="badge bg-secondary ms-1">Usuario: {{ $usuarios->find(request('usuario'))->name }}</span>
                                @endif
                            </div>
                            @if(request()->anyFilled(['numero_factura', 'proveedor', 'tipo_compra', 'usuario']))
                                <a href="{{ url('ingresos') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Limpiar filtros
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="row gx-3">
                        <div class="col-sm-12 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <a href="{{ url('add-ingreso')  }}" class="btn btn-primary"><i class="bi bi-plus-square"></i> Agregar</a>
                                        <div>
                                            <a href="{{ route('ingresos.export.pdf', request()->query()) }}" class="btn btn-danger"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                                            {{-- <a href="{{ route('ingresos.export.excel', request()->query()) }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Excel</a> --}}
                                        </div>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="bg-primary text-white">
                                                <tr>
                                                    <th width="110">Acciones</th>
                                                    <th width="100">Fecha</th>
                                                    <th width="150">Factura</th>
                                                    <th width="180">Proveedor</th>
                                                    <th width="120" class="text-center">Tipo</th>
                                                    <th width="150">Usuario</th>
                                                    <th>Detalles</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($ingresos as $ingreso)
                                                    <tr>
                                                        <td>
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="{{ url('show-ingreso/'.$ingreso->id)  }}" class="btn btn-info" title="Ver detalles"><i class="bi bi-eye-fill"></i></a>
                                                                <a href="{{ url('edit-ingreso/'.$ingreso->id)  }}" class="btn btn-warning" title="Editar"><i class="bi bi-pencil-fill"></i></a>
                                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $ingreso->id }}" title="Eliminar">
                                                                    <i class="bi bi-trash-fill"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                        <td><a href="{{ url('show-ingreso/'.$ingreso->id)  }}" class="text-primary">{{ \Carbon\Carbon::parse($ingreso->fecha)->format('d/m/Y') }}</a></td>
                                                        <td><a href="{{ url('show-ingreso/'.$ingreso->id)  }}">{{ $ingreso->numero_factura ?: 'Sin factura' }}</a></td>
                                                        <td><a href="{{ url('show-proveedor/'.$ingreso->proveedor_id) }}" class="text-primary">{{ optional($ingreso->proveedor)->nombre }}</a></td>
                                                        <td class="text-center">
                                                            <span class="badge {{ $ingreso->tipo_compra == 'Car Wash' ? 'bg-info' : 'bg-warning text-dark' }}">
                                                                {{ $ingreso->tipo_compra }}
                                                            </span>
                                                        </td>
                                                        <td>{{ optional($ingreso->usuario)->name }}</td>
                                                        <td>
                                                            @php
                                                                $totalIngreso = 0;
                                                            @endphp
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-bordered table-striped m-0" style="font-size: 12px;">
                                                                    <thead class="bg-info text-white">
                                                                        <tr>
                                                                            <th>Artículo</th>
                                                                            <th class="text-center">Cantidad</th>
                                                                            <th class="text-end">Precio</th>
                                                                            <th class="text-end">Subtotal</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach($ingreso->detalles as $detalle)
                                                                            @php
                                                                                $subtotal = $detalle->cantidad * $detalle->precio_compra;
                                                                                $totalIngreso += $subtotal;
                                                                            @endphp
                                                                            <tr>
                                                                                <td class="text-primary">{{ optional($detalle->articulo)->nombre }}</td>
                                                                                <td class="text-center">{{ $detalle->cantidad }} {{ optional($detalle->articulo->unidad)->abreviatura ?? '' }}</td>
                                                                                <td class="text-end">{{ $config->currency_simbol }}.{{ number_format($detalle->precio_compra, 2, '.', ',') }}</td>
                                                                                <td class="text-end">{{ $config->currency_simbol }}.{{ number_format($subtotal, 2, '.', ',') }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                        <tr class="table-warning">
                                                                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                                            <td class="text-end"><strong>{{ $config->currency_simbol }}.{{ number_format($totalIngreso, 2, '.', ',') }}</strong></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @include('admin.ingreso.deletemodal')
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center py-4">
                                                            <div class="alert alert-info mb-0">
                                                                <i class="bi bi-info-circle me-2"></i> No se encontraron ingresos con los filtros seleccionados
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            @if($ingresos->count() > 0)
                                                <tfoot class="bg-primary text-white">
                                                    <tr>
                                                        <td colspan="6" class="text-end"><strong>Total Gastado:</strong></td>
                                                        <td class="text-end">
                                                            <h5 class="text-light mb-0">{{ $config->currency_simbol }}.{{ number_format($ingresos->sum(function($ingreso) {
                                                                return $ingreso->detalles->sum(function($detalle) {
                                                                    return $detalle->cantidad * $detalle->precio_compra;
                                                                });
                                                            }), 2, '.', ',') }}</h5>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            @endif
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pestaña de Estadísticas -->
                <div class="tab-pane fade" id="estadisticas-tab-pane" role="tabpanel" aria-labelledby="estadisticas-tab" tabindex="0">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 text-white">Análisis de Ingresos</h5>
                        </div>
                        <div class="card-body">
                            <!-- Gráficos -->
                            <div class="row">
                                <!-- Ingresos por Tipo de Compra -->
                                <div class="col-xl-6 col-lg-6 col-md-12">
                                    <div class="card mb-3">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="card-title mb-0 text-white">Ingresos por Tipo de Compra</h5>
                                        </div>
                                        <div class="card-body">
                                            <div style="height: 300px">
                                                <canvas id="chartTipoCompra"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Top Proveedores -->
                                <div class="col-xl-6 col-lg-6 col-md-12">
                                    <div class="card mb-3">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="card-title mb-0 text-white">Top 5 Proveedores</h5>
                                        </div>
                                        <div class="card-body">
                                            <div style="height: 300px">
                                                <canvas id="chartProveedores"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Resumen de Ingresos por Periodo -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="card-title mb-0 text-white">Resumen de Filtros Aplicados</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-info">
                                                <i class="bi bi-info-circle me-2"></i>
                                                <strong>Periodo analizado:</strong>
                                                {{ \Carbon\Carbon::parse(request('fecha_desde', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d')))->format('d/m/Y') }} al
                                                {{ \Carbon\Carbon::parse(request('fecha_hasta', \Carbon\Carbon::now()->format('Y-m-d')))->format('d/m/Y') }}

                                                @if(request('numero_factura') || request('proveedor') || request('tipo_compra') || request('usuario'))
                                                <hr>
                                                <strong>Filtros adicionales:</strong>
                                                <ul class="mb-0">
                                                    @if(request('numero_factura'))
                                                        <li>Factura: {{ request('numero_factura') }}</li>
                                                    @endif
                                                    @if(request('proveedor') && $proveedores->find(request('proveedor')))
                                                        <li>Proveedor: {{ $proveedores->find(request('proveedor'))->nombre }}</li>
                                                    @endif
                                                    @if(request('tipo_compra'))
                                                        <li>Tipo de compra: {{ request('tipo_compra') }}</li>
                                                    @endif
                                                    @if(request('usuario') && $usuarios->find(request('usuario')))
                                                        <li>Usuario: {{ $usuarios->find(request('usuario'))->name }}</li>
                                                    @endif
                                                </ul>
                                                @endif
                                            </div>
                                            <div class="row text-center mt-3">
                                                <div class="col-md-4">
                                                    <div class="card bg-light p-3">
                                                        <h5 class="text-primary">Total Ingresos</h5>
                                                        <h4>{{ number_format($totalIngresos, 0) }}</h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card bg-light p-3">
                                                        <h5 class="text-danger">Total Gastado</h5>
                                                        <h4>{{ $config->currency_simbol }}{{ number_format($totalGastado, 2) }}</h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card bg-light p-3">
                                                        <h5 class="text-success">Unidades Ingresadas</h5>
                                                        <h4>{{ number_format($totalArticulos, 0) }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para los gráficos -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
                    console.log('Inicializando gráficos de ingresos...');

                    // Verificar que los elementos canvas existan
                    const chartTipoCompraEl = document.getElementById('chartTipoCompra');
                    const chartProveedoresEl = document.getElementById('chartProveedores');

                    // Datos para el gráfico de tipos de compra
                    const tiposCompra = [
                        @foreach($tiposCompra as $item)
                            '{{ $item['tipo'] }}',
                        @endforeach
                    ];

                    const cantidadesPorTipo = [
                        @foreach($tiposCompra as $item)
                            {{ $item['cantidad'] }},
                        @endforeach
                    ];

                    const montosPorTipo = [
                        @foreach($tiposCompra as $item)
                            {{ $item['monto'] }},
                        @endforeach
                    ];

                    // Crear gráfico de tipos de compra si no existe ya
                    if (chartTipoCompraEl && !charts.chartTipoCompra) {
                        charts.chartTipoCompra = new Chart(chartTipoCompraEl, {
                            type: 'bar',
                            data: {
                                labels: tiposCompra,
                                datasets: [
                                    {
                                        label: 'Cantidad de ingresos',
                                        data: cantidadesPorTipo,
                                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                        borderColor: 'rgba(54, 162, 235, 1)',
                                        borderWidth: 1,
                                        yAxisID: 'y'
                                    },
                                    {
                                        label: 'Monto total',
                                        data: montosPorTipo,
                                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                                        borderColor: 'rgba(255, 99, 132, 1)',
                                        borderWidth: 1,
                                        yAxisID: 'y1'
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        type: 'linear',
                                        position: 'left',
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Cantidad'
                                        }
                                    },
                                    y1: {
                                        type: 'linear',
                                        position: 'right',
                                        beginAtZero: true,
                                        grid: {
                                            drawOnChartArea: false
                                        },
                                        title: {
                                            display: true,
                                            text: 'Monto Total'
                                        }
                                    }
                                }
                            }
                        });
                        console.log('Gráfico de tipos de compra creado');
                    }

                    // Gráfico de Top Proveedores si no existe ya
                    if (chartProveedoresEl && !charts.chartProveedores) {
                        charts.chartProveedores = new Chart(chartProveedoresEl, {
                            type: 'bar',
                            data: {
                                labels: [
                                    @foreach($proveedoresStats as $item)
                                        '{{ $item['proveedor'] }}',
                                    @endforeach
                                ],
                                datasets: [{
                                    label: 'Monto total',
                                    data: [
                                        @foreach($proveedoresStats as $item)
                                            {{ $item['monto'] }},
                                        @endforeach
                                    ],
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.6)',
                                        'rgba(54, 162, 235, 0.6)',
                                        'rgba(255, 206, 86, 0.6)',
                                        'rgba(75, 192, 192, 0.6)',
                                        'rgba(153, 102, 255, 0.6)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)',
                                        'rgba(54, 162, 235, 1)',
                                        'rgba(255, 206, 86, 1)',
                                        'rgba(75, 192, 192, 1)',
                                        'rgba(153, 102, 255, 1)'
                                    ],
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                indexAxis: 'y', // Esto hace que el gráfico sea horizontal
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const currencySimbol = "{{ $config->currency_simbol }}";
                                                return `${context.dataset.label}: ${currencySimbol}.${parseFloat(context.raw).toLocaleString('es-GT', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Monto Gastado'
                                        }
                                    }
                                }
                            }
                        });
                        console.log('Gráfico de proveedores creado');
                    }
                } catch (error) {
                    console.error('Error al crear los gráficos:', error);
                }
            }
        });
    </script>
@endsection
