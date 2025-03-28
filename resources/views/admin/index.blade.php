@extends('layouts.admin')

@section('content')

<!-- Content wrapper start -->
<div class="content-wrapper">

    <!-- Row start -->
    <div class="row">
        <div class="col-xxl-12">
            <div class="page-header">
                <h3 class="page-title">Panel de Control</h3>
                <div>
                    <span class="badge bg-primary-transparent" id="reloj"></span>
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->

    <!-- Row start -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title">Accesos Rápidos</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-6">
                            <a href="{{ url('add-cliente') }}" class="text-decoration-none">
                                <div class="card border border-light shadow-hover mb-0 h-100">
                                    <div class="card-body text-center py-4 d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-lg mb-3 mx-auto">
                                            <div class="avatar-title bg-primary-transparent rounded-circle d-flex align-items-center justify-content-center" style="width: 85px; height: 85px;">
                                                <i class="bi bi-person-plus text-primary" style="font-size: 2.5rem !important;"></i>
                                            </div>
                                        </div>
                                        <h5 class="mb-0 fw-semibold">Nuevo Cliente</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-6">
                            <a href="{{ url('add-vehiculo') }}" class="text-decoration-none">
                                <div class="card border border-light shadow-hover mb-0 h-100">
                                    <div class="card-body text-center py-4 d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-lg mb-3 mx-auto">
                                            <div class="avatar-title bg-success-transparent rounded-circle d-flex align-items-center justify-content-center" style="width: 85px; height: 85px;">
                                                <i class="bi bi-car-front text-success" style="font-size: 2.5rem !important;"></i>
                                            </div>
                                        </div>
                                        <h5 class="mb-0 fw-semibold">Nuevo Vehículo</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-6">
                            <a href="{{ url('add-venta') }}" class="text-decoration-none">
                                <div class="card border border-light shadow-hover mb-0 h-100">
                                    <div class="card-body text-center py-4 d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-lg mb-3 mx-auto">
                                            <div class="avatar-title bg-info-transparent rounded-circle d-flex align-items-center justify-content-center" style="width: 85px; height: 85px;">
                                                <i class="bi bi-cart-plus text-info" style="font-size: 2.5rem !important;"></i>
                                            </div>
                                        </div>
                                        <h5 class="mb-0 fw-semibold">Nueva Venta</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-6 col-sm-6">
                            <a href="{{ url('inventario') }}" class="text-decoration-none">
                                <div class="card border border-light shadow-hover mb-0 h-100">
                                    <div class="card-body text-center py-4 d-flex flex-column align-items-center justify-content-center">
                                        <div class="avatar avatar-lg mb-3 mx-auto">
                                            <div class="avatar-title bg-warning-transparent rounded-circle d-flex align-items-center justify-content-center" style="width: 85px; height: 85px;">
                                                <i class="bi bi-inboxes text-warning" style="font-size: 2.5rem !important;"></i>
                                            </div>
                                        </div>
                                        <h5 class="mb-0 fw-semibold">Inventario</h5>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->

    <!-- Row start -->
    <div class="row">
        <div class="col-xxl-3 col-sm-6 col-12">
            <div class="card bg-primary-transparent shadow-none mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="fw-semibold">Ventas Hoy</h5>
                        </div>
                        <div class="col-auto">
                            <div class="avatar avatar-sm">
                                <div class="avatar-title bg-primary-transparent-2 rounded">
                                    <i class="bi bi-cash-stack text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h2 class="mt-1">{{ $config->currency_simbol }}.{{ number_format($ventasHoy ?? 0, 2) }}</h2>
                        <div class="mt-1">
                            <span class="badge bg-white text-primary fw-bold fs-12">Diario</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6 col-12">
            <div class="card bg-success-transparent shadow-none mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="fw-semibold">Ventas Semana</h5>
                        </div>
                        <div class="col-auto">
                            <div class="avatar avatar-sm">
                                <div class="avatar-title bg-success-transparent-2 rounded">
                                    <i class="bi bi-calendar-week text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h2 class="mt-1">{{ $config->currency_simbol }}.{{ number_format($ventasSemana, 2) }}</h2>
                        <div class="mt-1">
                            <span class="badge bg-white text-success fw-bold fs-12">Semanal</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6 col-12">
            <div class="card bg-info-transparent shadow-none mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="fw-semibold">Ventas Mes</h5>
                        </div>
                        <div class="col-auto">
                            <div class="avatar avatar-sm">
                                <div class="avatar-title bg-info-transparent-2 rounded">
                                    <i class="bi bi-calendar-month text-info"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h2 class="mt-1">{{ $config->currency_simbol }}.{{ number_format($ventasMes, 2) }}</h2>
                        <div class="mt-1">
                            <span class="badge bg-white text-info fw-bold fs-12">Mensual</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-3 col-sm-6 col-12">
            <div class="card bg-warning-transparent shadow-none mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="fw-semibold">Vehículos Registrados</h5>
                        </div>
                        <div class="col-auto">
                            <div class="avatar avatar-sm">
                                <div class="avatar-title bg-warning-transparent-2 rounded">
                                    <i class="bi bi-car-front text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h2 class="mt-1">{{ number_format($totalVehiculos) }}</h2>
                        <div class="mt-1">
                            <span class="badge bg-white text-warning fw-bold fs-12">Total</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->

    <!-- Row start -->
    <div class="row">
        <div class="col-xxl-8 col-xl-8 col-lg-7 col-md-12 col-sm-12">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title">Ventas Mensuales</h5>
                        </div>
                        <div class="col-auto">
                            <div class="dropdown">
                                <a class="btn btn-sm btn-primary" href="{{ url('ventas') }}">
                                    Ver Todas
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="ventas-chart" style="height: 300px; width: 100%;"></div>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-12 col-sm-12">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="card-title">Estadísticas Generales</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mt-2">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <p class="mb-0 fw-semibold">Clientes</p>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ number_format($totalClientes) }}</h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <p class="mb-0 fw-semibold">Vehículos</p>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ number_format($totalVehiculos) }}</h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <p class="mb-0 fw-semibold">Trabajadores</p>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ number_format($totalTrabajadores) }}</h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div>
                                <p class="mb-0 fw-semibold">Usuarios</p>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ number_format($totalUsuarios) }}</h5>
                            </div>
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="mb-0 fw-semibold">Proveedores</p>
                            </div>
                            <div>
                                <h5 class="mb-0">{{ number_format($totalProveedores) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->

    <!-- Row start - Artículos con Stock Bajo y Más Vendidos -->
    <div class="row">
        <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title">Artículos con Stock Bajo</h5>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-sm btn-warning" href="{{ url('inventario') }}">
                                Ir a Inventario
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Artículo</th>
                                    <th>Stock Actual</th>
                                    <th>Stock Mínimo</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stockBajo as $articulo)
                                <tr>
                                    <td>{{ $articulo->nombre }}</td>
                                    <td>
                                        {{ $articulo->stock }}
                                        @if(isset($articulo->unidad) && $articulo->unidad)
                                            {{ $articulo->unidad->abreviatura }}
                                        @elseif(isset($articulo->unidad_abrev))
                                            {{ $articulo->unidad_abrev }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ $articulo->stock_minimo }}
                                        @if(isset($articulo->unidad) && $articulo->unidad)
                                            {{ $articulo->unidad->abreviatura }}
                                        @elseif(isset($articulo->unidad_abrev))
                                            {{ $articulo->unidad_abrev }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($articulo->stock == 0)
                                            <span class="badge bg-danger">Sin Stock</span>
                                        @else
                                            <span class="badge bg-warning">Bajo Stock</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No hay artículos con stock bajo</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title">Artículos Más Vendidos</h5>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-sm btn-info" href="{{ url('reportearticulos') }}">
                                Ver Reporte
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Artículo</th>
                                    <th class="text-end">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($articulosMasVendidos as $articulo)
                                <tr>
                                    <td>
                                        @if(isset($articulo->es_simulado) && $articulo->es_simulado)
                                            <span class="text-muted">{{ $articulo->nombre }}</span>
                                            <span class="badge bg-secondary">Simulado</span>
                                        @else
                                            <a href="{{ url('articulos/' . ($articulo->articulo_id ?? '')) }}" class="text-primary">
                                                {{ $articulo->nombre }}
                                            </a>
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">
                                        {{ number_format($articulo->total_vendido, 2) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">No hay datos de ventas</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->

    <!-- Row start -->
    <div class="row">
        <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title">Ventas Recientes</h5>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-sm btn-success" href="{{ url('ventas') }}">
                                Ver Todas
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>#</th>
                                    <th>Cliente</th>
                                    <th>Vendedor</th>
                                    <th>Total</th>
                                    <th>Estado/Pago</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ventasRecientes as $venta)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                                    <td>{{ $venta->numero_factura }}</td>
                                    <td>{{ $venta->cliente->nombre ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $nombreVendedor = 'N/A';
                                            // Intentar diferentes posibles relaciones
                                            if (isset($venta->user)) {
                                                $nombreVendedor = $venta->user->name;
                                            } elseif (isset($venta->usuario)) {
                                                $nombreVendedor = $venta->usuario->name;
                                            } elseif (isset($venta->vendedor)) {
                                                $nombreVendedor = $venta->vendedor->name;
                                            } elseif (isset($venta->idusuario)) {
                                                // Intentar obtener el usuario directamente
                                                $usuario = \App\Models\User::find($venta->idusuario);
                                                if ($usuario) {
                                                    $nombreVendedor = $usuario->name;
                                                }
                                            }
                                        @endphp
                                        {{ $nombreVendedor }}
                                    </td>
                                    <td>
                                        @php
                                            // Calcular el total sumando los subtotales de los detalles
                                            $totalVenta = 0;
                                            foreach ($venta->detalleVentas as $detalle) {
                                                $totalVenta += $detalle->sub_total;
                                            }
                                        @endphp
                                        {{ $config->currency_simbol }}.{{ number_format($totalVenta, 2) }}
                                    </td>
                                    <td>
                                        @if ($venta->estado == 1 || $venta->estado === true)
                                            <span class="badge bg-success">Activa</span>
                                        @else
                                            <span class="badge bg-danger">Cancelada</span>
                                        @endif
                                        <span class="badge {{ $venta->estado_pago == 'pagado' ? 'bg-info' : 'bg-warning' }}">
                                            {{ ucfirst($venta->estado_pago) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ url('show-venta/'.$venta->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                                @if(count($ventasRecientes) == 0)
                                <tr>
                                    <td colspan="7" class="text-center">No hay ventas registradas</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Row end -->



</div>
<!-- Content wrapper end -->

<!-- Scripts para los gráficos -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Convertir los datos a números para asegurar que sean valores numéricos
        var datosVentas = @json($totales).map(function(val) {
            return parseFloat(val);
        });

        var options = {
            chart: {
                height: 300,
                width: '100%',
                type: 'bar',
                background: '#fff',
                toolbar: {
                    show: false,
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                },
                fontFamily: 'inherit'
            },
            plotOptions: {
                bar: {
                    borderRadius: 5,
                    dataLabels: {
                        position: 'top',
                    },
                    columnWidth: '60%',
                    distributed: false,
                }
            },
            colors: ['#3f51b5'],
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return '{{ $config->currency_simbol }}.' + val.toLocaleString();
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#304758"]
                }
            },
            series: [{
                name: 'Ventas',
                data: datosVentas
            }],
            xaxis: {
                categories: @json($meses),
                position: 'bottom',
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                tooltip: {
                    enabled: true,
                }
            },
            yaxis: {
                labels: {
                    formatter: function (val) {
                        return '{{ $config->currency_simbol }}.' + val.toLocaleString();
                    }
                }
            },
            title: {
                text: 'Ventas Mensuales ' + new Date().getFullYear(),
                floating: false,
                offsetY: 0,
                align: 'center',
                style: {
                    color: '#444'
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (val) {
                        return '{{ $config->currency_simbol }}.' + val.toLocaleString();
                    }
                }
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    chart: {
                        height: 240
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: '80%'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    }
                }
            }],
            noData: {
                text: 'No hay datos disponibles',
                align: 'center',
                verticalAlign: 'middle',
                offsetX: 0,
                offsetY: 0,
                style: {
                    color: "#000000",
                    fontSize: '16px',
                    fontFamily: 'inherit'
                }
            }
        };

        var chart = new ApexCharts(
            document.querySelector("#ventas-chart"),
            options
        );

        chart.render();

        console.log('Datos gráfica:', {
            meses: @json($meses),
            totales: @json($totales),
            datosConvertidos: datosVentas
        });

    } catch (error) {
        console.error('Error al renderizar el gráfico:', error);
        document.getElementById('ventas-chart').innerHTML =
            '<div class="alert alert-warning text-center p-5">'+
                '<i class="bi bi-exclamation-triangle fs-1"></i>'+
                '<p class="mt-3">Ocurrió un error al cargar la gráfica de ventas.</p>'+
            '</div>';
    }
});
</script>

@endsection
