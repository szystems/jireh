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
            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Lista de Artículos y Servicios</h5>
                            <a href="{{ url('add-articulo') }}" class="btn btn-primary float-end"><i class="bi bi-plus-square"></i> Agregar</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-striped flex-column">
                                    <thead>
                                        <tr>
                                            <td align="center"><i class="bi bi-list-task"></i></td>
                                            <td align="left">Artículo/Servicio</td>
                                            <td align="center"><small>Tipo</small></td>
                                            <td align="left"><small>Descripción</small></td>
                                            <td align="center">Precios</td>
                                            <td align="center">Stock</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($articulos as $articulo)
                                        <tr>
                                            <td align="center">
                                                <div class="btn-group dropend">
                                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                                        <i class="bi bi-list-task"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ url('show-articulo', $articulo->id) }}"><i class="bi bi-eye-fill text-blue"></i> Información</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ url('edit-articulo', $articulo->id) }}"><i class="bi bi-pencil-fill text-warning"></i> Editar</a>
                                                        </li>
                                                        <li>
                                                            <a type="button" class="btn bg-gradient-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $articulo->id }}">
                                                                <i class="bi bi-trash-fill text-danger"></i> Eliminar
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <a href="{{ url('show-articulo', $articulo->id) }}">
                                                            <h5 class="text-primary">{{ $articulo->nombre }}</h5>
                                                            <div class="text-muted"><small>Código: <strong>{{ $articulo->codigo }}</strong></small></div>
                                                            <div class="text-muted"><small>Categoría: <strong>{{ $articulo->categoria->nombre }}</strong></small></div>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td align="center"><small class="text-info">{{ $articulo->tipo == 'servicio' ? 'Servicio' : ($articulo->tipo == 'articulo' ? 'Artículo' : $articulo->tipo) }}</small></td>
                                            <td align="left"><small>{{ $articulo->descripcion }}</small></td>
                                            <td align="center">
                                                <div>Compra: <strong><font class="text-danger">{{ $config->currency_simbol }}.{{ number_format($articulo->precio_compra, 2, '.', ',') }}</font></strong></div>
                                                <div>Venta: <strong><font class="text-success">{{ $config->currency_simbol }}.{{ number_format($articulo->precio_venta, 2, '.', ',') }}</font></strong></div>
                                            </td>
                                            <td align="center">
                                                @if ($articulo->stock <= 0)
                                                    <strong><font class="text-danger">{{ $articulo->stock }}</font></strong>
                                                @elseif ($articulo->stock <= $articulo->stock_minimo)
                                                    <strong><font class="text-warning">{{ $articulo->stock }}</font></strong>
                                                @else
                                                    <strong><font class="text-success">{{ $articulo->stock }}</font></strong>
                                                @endif
                                                ({{ $articulo->unidad->abreviatura }})
                                            </td>
                                        </tr>
                                        @include('admin.articulo.deletemodal')
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $articulos->links() }}
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
@endsection
