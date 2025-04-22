@extends('layouts.admin')
@section('content')
    <!-- Incluir Chart.js antes de usar las gráficas -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-bar-chart-line"></i>
                </div>
                <div class="page-title">
                    <h5>Reporte de Artículos Vendidos</h5>
                </div>
            </div>
        </div>
        <div class="content-wrapper">
            @include('admin.reportearticulo.search')

            <!-- Filtros utilizados -->
            <div class="filters mb-3">
                <strong>Filtros utilizados:</strong>
                <span>Fecha Desde: {{ \Carbon\Carbon::parse(request('fecha_desde', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d')))->format('d/m/Y') }}</span>,
                <span>Fecha Hasta: {{ \Carbon\Carbon::parse(request('fecha_hasta', \Carbon\Carbon::now()->format('Y-m-d')))->format('d/m/Y') }}</span>
                @if(request('codigo'))
                    , <span>Código: {{ request('codigo') }}</span>
                @endif
                @if(request('articulo') && $articulos->find(request('articulo')))
                    , <span>Artículo: {{ $articulos->find(request('articulo'))->nombre }}</span>
                @endif
                @if(request('categoria') && $categorias->find(request('categoria')))
                    , <span>Categoría: {{ $categorias->find(request('categoria'))->nombre }}</span>
                @endif
                @if(request('usuario') && $usuarios->find(request('usuario')))
                    , <span>Usuario: {{ $usuarios->find(request('usuario'))->name }}</span>
                @endif
                @if(request('cliente') && isset($clientes) && $clientes->find(request('cliente')))
                    , <span>Cliente: {{ $clientes->find(request('cliente'))->nombre }}</span>
                @endif
                @if(request('tipo_venta'))
                    , <span>Tipo de Venta: {{ request('tipo_venta') }}</span>
                @endif
                @if(request('estado') !== null)
                    , <span>Estado: {{ request('estado') == '1' ? 'Activa' : 'Cancelada' }}</span>
                @endif
            </div>

            <!-- Estadísticas en cards -->
            <div class="row mb-4">
                <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-primary">
                                <i class="bi bi-box-fill"></i>
                            </div>
                            <div class="fs-6 fw-bold">Artículos</div>
                            <div class="fs-4 text-primary">{{ number_format($totalArticulosVendidos, 0, '.', ',') }}</div>
                            <div class="small text-muted">unidades</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-success">
                                <i class="bi bi-cash-coin"></i>
                            </div>
                            <div class="fs-6 fw-bold">Total Ventas</div>
                            <div class="fs-4 text-success">{{ $config->currency_simbol }}.{{ number_format($totalVentas, 2, '.', ',') }}</div>
                            <div class="small text-muted">sin impuestos</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-4 col-md-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-warning">
                                <i class="bi bi-tag-fill"></i>
                            </div>
                            <div class="fs-6 fw-bold">Descuentos</div>
                            <div class="fs-4 text-warning">{{ $config->currency_simbol }}.{{ number_format($totalDescuentos, 2, '.', ',') }}</div>
                            <div class="small text-muted">total aplicado</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-warning">
                                <i class="bi bi-receipt-cutoff"></i>
                            </div>
                            <div class="fs-6 fw-bold">Impuestos</div>
                            <div class="fs-4 text-warning">{{ $config->currency_simbol }}.{{ number_format($totalImpuestos ?? 0, 2, '.', ',') }}</div>
                            <div class="small text-muted">total aplicado</div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="fs-5 text-success">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <div class="fs-6 fw-bold">Ganancia</div>
                            <div class="fs-4 text-success">{{ $config->currency_simbol }}.{{ number_format($totalVentas - $totalCostos - ($totalImpuestos ?? 0), 2, '.', ',') }}</div>
                            <div class="small text-muted">neta</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row mb-4">
                <!-- Top Artículos -->
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0 text-white">Top 10 Artículos más Vendidos</h5>
                        </div>
                        <div class="card-body">
                            <div style="height: 300px">
                                <canvas id="chartTopArticulos"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ventas por Categoría -->
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0 text-white">Ventas por Categoría</h5>
                        </div>
                        <div class="card-body">
                            <div style="height: 300px">
                                <canvas id="chartCategorias"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Detalles -->
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-3 text-white">Detalles de Artículos Vendidos</h5>
                    <div>
                        <a target="_blank" href="{{ route('reportearticulo.export.pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                            <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($detallesVenta) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Factura</th>
                                        <th>Artículo</th>
                                        <th>Categoría</th>
                                        <th class="text-center">Cantidad</th>
                                        <th>Precio Venta</th>
                                        <th>Precio Costo</th>
                                        <th>Descuento</th>
                                        <th>Impuestos</th>
                                        <th>Ganancia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($detallesVenta as $detalle)
                                        @php
                                            // Calcular precio unitario y subtotales
                                            $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);
                                            $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

                                            // Calcular monto de descuento
                                            $montoDescuento = 0;
                                            if($detalle->descuento_id && $detalle->descuento) {
                                                $montoDescuento = $subtotalSinDescuento * ($detalle->descuento->porcentaje_descuento / 100);
                                            }

                                            // Calcular subtotal con descuento
                                            $subtotalConDescuento = $subtotalSinDescuento - $montoDescuento;

                                            // Calcular impuestos
                                            $impuestoDetalle = $subtotalConDescuento * ($detalle->porcentaje_impuestos ?? 0) / 100;

                                            // Calcular costo total
                                            $costoTotal = $detalle->precio_costo * $detalle->cantidad;

                                            // Calcular ganancia neta
                                            $ganancia = $subtotalConDescuento - $costoTotal - $impuestoDetalle;
                                        @endphp
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($detalle->venta->fecha)->format('d/m/Y') }}</td>
                                            <td>
                                                <a href="{{ url('show-venta/'.$detalle->venta_id) }}">
                                                    {{ $detalle->venta->numero_factura ?? 'N/A' }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($detalle->articulo)
                                                    <a href="{{ url('show-articulo/'.$detalle->articulo_id) }}">
                                                        <strong>{{ $detalle->articulo->codigo }}</strong> - {{ $detalle->articulo->nombre }}
                                                    </a>
                                                @else
                                                    Artículo no disponible
                                                @endif
                                            </td>
                                            <td>
                                                @if($detalle->articulo && $detalle->articulo->categoria)
                                                    {{ $detalle->articulo->categoria->nombre }}
                                                @else
                                                    --
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $detalle->cantidad }}
                                                @if($detalle->articulo && $detalle->articulo->unidad)
                                                    {{ $detalle->articulo->unidad->abreviatura }}
                                                @endif
                                            </td>
                                            <td class="text-end">{{ $config->currency_simbol }}.{{ number_format($precioUnitario, 2, '.', ',') }}</td>
                                            <td class="text-end">{{ $config->currency_simbol }}.{{ number_format($detalle->precio_costo, 2, '.', ',') }}</td>
                                            <td class="text-end">
                                                @if($detalle->descuento_id && $detalle->descuento)
                                                    {{ $config->currency_simbol }}.{{ number_format($montoDescuento, 2, '.', ',') }}
                                                    <br>
                                                    <small>({{ $detalle->descuento->porcentaje_descuento }}%)</small>
                                                @else
                                                    --
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($impuestoDetalle > 0)
                                                    {{ $config->currency_simbol }}.{{ number_format($impuestoDetalle, 2, '.', ',') }}
                                                    <br>
                                                    <small>({{ $detalle->porcentaje_impuestos ?? 0 }}%)</small>
                                                @else
                                                    --
                                                @endif
                                            </td>
                                            <td class="text-end {{ $ganancia >= 0 ? 'text-success' : 'text-danger' }}">
                                                <strong>
                                                    {{ $config->currency_simbol }}.{{ number_format($ganancia, 2, '.', ',') }}
                                                </strong>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No se encontraron registros que coincidan con los criterios de búsqueda.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para los gráficos -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            try {
                console.log('Inicializando gráficos...');

                // Verificar que existan los datos para los gráficos
                const topArticulosData = [
                    @foreach($topArticulos as $item)
                        {{ $item['cantidad'] }},
                    @endforeach
                ];

                const topArticulosLabels = [
                    @foreach($topArticulos as $item)
                        '{{ $item['articulo'] ? str_replace("'", "", Str::limit($item['articulo']->nombre, 20)) : "N/A" }}',
                    @endforeach
                ];

                const categoriaData = [
                    @foreach($ventasPorCategoria as $item)
                        {{ $item['total'] }},
                    @endforeach
                ];

                const categoriaLabels = [
                    @foreach($ventasPorCategoria as $item)
                        '{{ str_replace("'", "", $item['categoria']) }} ({{ $item['porcentaje'] }}%)',
                    @endforeach
                ];

                console.log('Datos cargados:', {
                    topArticulosData,
                    topArticulosLabels,
                    categoriaData,
                    categoriaLabels
                });

                // Verificar que existan los elementos canvas
                const ctxTop = document.getElementById('chartTopArticulos');
                const ctxCat = document.getElementById('chartCategorias');

                if (!ctxTop || !ctxCat) {
                    console.error('No se encontraron los elementos canvas');
                    return;
                }

                // Crear gráfico de barras para Top Artículos
                if (topArticulosData.length > 0) {
                    const chartArticulos = new Chart(ctxTop.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: topArticulosLabels,
                            datasets: [{
                                label: 'Cantidad vendida',
                                data: topArticulosData,
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                    console.log('Gráfico de artículos creado');
                } else {
                    document.querySelector('#chartTopArticulos').closest('.card-body').innerHTML =
                        '<div class="alert alert-info">No hay datos suficientes para mostrar este gráfico</div>';
                }

                // Crear gráfico de pastel para Categorías
                if (categoriaData.length > 0) {
                    const chartCategorias = new Chart(ctxCat.getContext('2d'), {
                        type: 'pie',
                        data: {
                            labels: categoriaLabels,
                            datasets: [{
                                data: categoriaData,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.6)',
                                    'rgba(54, 162, 235, 0.6)',
                                    'rgba(255, 206, 86, 0.6)',
                                    'rgba(75, 192, 192, 0.6)',
                                    'rgba(153, 102, 255, 0.6)',
                                    'rgba(255, 159, 64, 0.6)',
                                    'rgba(199, 199, 199, 0.6)',
                                    'rgba(83, 102, 255, 0.6)',
                                    'rgba(40, 159, 64, 0.6)',
                                    'rgba(210, 199, 199, 0.6)'
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)',
                                    'rgba(199, 199, 199, 1)',
                                    'rgba(83, 102, 255, 1)',
                                    'rgba(40, 159, 64, 1)',
                                    'rgba(210, 199, 199, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                    console.log('Gráfico de categorías creado');
                } else {
                    document.querySelector('#chartCategorias').closest('.card-body').innerHTML =
                        '<div class="alert alert-info">No hay datos suficientes para mostrar este gráfico</div>';
                }
            } catch (error) {
                console.error('Error al crear los gráficos:', error);
                // Mostrar mensaje de error en los contenedores de los gráficos
                const containers = document.querySelectorAll('#chartTopArticulos, #chartCategorias');
                containers.forEach(container => {
                    if (container) {
                        container.closest('.card-body').innerHTML =
                            '<div class="alert alert-danger">Error al cargar el gráfico: ' + error.message + '</div>';
                    }
                });
            }
        });
    </script>
@endsection
