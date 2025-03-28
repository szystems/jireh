@extends('layouts.admin')
@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi  bi-diagram-3"></i>
                </div>
                <div class="page-title">
                    <h5>Categorías</h5>
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
            <!-- Breadcrumb start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('categorias') }}">Categorías</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $categoria->nombre }}</li>
                </ol>
            </nav>
            <!-- Breadcrumb end -->

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-md-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-diagram-3 me-2"></i>{{ $categoria->nombre }}
                            </h5>
                            <div>
                                <a href="{{ url('edit-categoria/'.$categoria->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <h6 class="text-muted mb-2">Descripción</h6>
                                <p>{{ $categoria->descripcion ?: 'Sin descripción' }}</p>
                            </div>

                            <hr>

                            <!-- Artículos en esta categoría - Nuevo -->
                            <h5><i class="bi bi-box me-2"></i>Artículos en esta categoría</h5>

                            @php
                                $articulos = \App\Models\Articulo::where('categoria_id', $categoria->id)->orderBy('nombre')->get();
                            @endphp

                            @if($articulos->count() > 0)
                                <div class="table-responsive mt-3">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Código</th>
                                                <th>Nombre</th>
                                                <th class="text-end">Precio</th>
                                                <th class="text-center">Stock</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($articulos as $articulo)
                                                <tr>
                                                    <td><span class="badge bg-secondary">{{ $articulo->codigo }}</span></td>
                                                    <td>{{ $articulo->nombre }}</td>
                                                    <td class="text-end">Q {{ number_format($articulo->precio_venta, 2) }}</td>
                                                    <td class="text-center">
                                                        @if($articulo->stock > 10)
                                                            <span class="badge bg-success">{{ $articulo->stock }}</span>
                                                        @elseif($articulo->stock > 0)
                                                            <span class="badge bg-warning">{{ $articulo->stock }}</span>
                                                        @else
                                                            <span class="badge bg-danger">Agotado</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ url('show-articulo/'.$articulo->id) }}" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info mt-3">
                                    <i class="bi bi-info-circle me-2"></i> No hay artículos asociados a esta categoría
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Columna lateral - Nuevo -->
                <div class="col-md-4">
                    <!-- Estadísticas -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-graph-up me-2"></i>Estadísticas
                            </h6>
                        </div>
                        <div class="card-body">
                            @php
                                $articulosCount = \App\Models\Articulo::where('categoria_id', $categoria->id)->count();
                                $stockTotal = \App\Models\Articulo::where('categoria_id', $categoria->id)->sum('stock');
                                $valorInventario = \App\Models\Articulo::where('categoria_id', $categoria->id)
                                    ->selectRaw('SUM(stock * precio_venta) as valor_total')
                                    ->first()->valor_total ?? 0;

                                // Modificar esta parte para manejar la posibilidad de que la relación no exista
                                try {
                                    $articulosMasVendidos = \App\Models\Articulo::where('categoria_id', $categoria->id)
                                        ->withCount('detalleVentas')
                                        ->orderByDesc('detalle_ventas_count')
                                        ->take(3)
                                        ->get();
                                } catch (\Exception $e) {
                                    // Si hay un error, simplemente tomamos los primeros 3 artículos
                                    $articulosMasVendidos = \App\Models\Articulo::where('categoria_id', $categoria->id)
                                        ->orderBy('nombre')
                                        ->take(3)
                                        ->get();
                                }
                            @endphp

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Total de artículos:</span>
                                    <span class="badge bg-primary rounded-pill">{{ $articulosCount }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Stock total:</span>
                                    <span class="badge bg-info rounded-pill">{{ $stockTotal }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Valor de inventario:</span>
                                    <span class="badge bg-success rounded-pill">Q {{ number_format($valorInventario, 2) }}</span>
                                </li>
                            </ul>

                            <!-- Top artículos -->
                            @if($articulosMasVendidos->count() > 0)
                                <div class="mt-3">
                                    <h6 class="text-muted mb-2">Artículos destacados</h6>
                                    <ol class="ps-3">
                                        @foreach($articulosMasVendidos as $art)
                                            <li>{{ $art->nombre }}
                                                @if(isset($art->detalle_ventas_count))
                                                    ({{ $art->detalle_ventas_count }} ventas)
                                                @endif
                                            </li>
                                        @endforeach
                                    </ol>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-gear me-2"></i>Acciones
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ url('articulos?categoria=' . $categoria->id) }}" class="btn btn-primary">
                                    <i class="bi bi-boxes me-2"></i>Ver todos los artículos
                                </a>
                                <a href="{{ url('add-articulo?categoria=' . $categoria->id) }}" class="btn btn-success">
                                    <i class="bi bi-plus-circle me-2"></i>Añadir artículo
                                </a>
                                <a href="{{ url('edit-categoria/'.$categoria->id) }}" class="btn btn-warning">
                                    <i class="bi bi-pencil-square me-2"></i>Editar categoría
                                </a>
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $categoria->id }}">
                                    <i class="bi bi-trash me-2"></i>Eliminar categoría
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row end -->

            @include('admin.categoria.deletemodal')
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->
@endsection
