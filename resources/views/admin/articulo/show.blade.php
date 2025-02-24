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
                    <h5>Artículos</h5>
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

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="custom-tabs-container">
                                <div class="col-12 col-md-auto float-end">
                                    <div class="btn-group-sm m-3">
                                        <a href="{{ url('edit-articulo/'.$articulo->id) }}" class="btn btn-warning" aria-current="page"><i class="bi bi-pencil"></i> Editar</a>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $articulo->id }}">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                        @include('admin.articulo.deletemodal')
                                    </div>
                                </div>
                                <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA" role="tab"
                                            aria-controls="oneA" aria-selected="true">Información</a>
                                    </li>
                                </ul>
                                <div class="tab-content h-350">
                                    <div class="tab-pane fade show active" id="oneA" role="tabpanel">
                                        <!-- Row start -->
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <div class="row gx-3">
                                                    <div class="col-md-6">
                                                        <label for="codigo" class="form-label">Código</label>
                                                        <p>{{ $articulo->codigo }}</p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="nombre" class="form-label">Nombre</label>
                                                        <p>{{ $articulo->nombre }}</p>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="tipo" class="form-label">Tipo</label>
                                                        <p class="text-info">{{ $articulo->tipo == 'servicio' ? 'Servicio' : ($articulo->tipo == 'articulo' ? 'Artículo' : $articulo->tipo) }}</p>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label for="descripcion" class="form-label">Descripción</label>
                                                        <p>{{ $articulo->descripcion }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="categoria" class="form-label">Categoría</label>
                                                        <p><a class=" text-primary" href="{{ url('show-categoria/'.$articulo->categoria_id) }}">{{ $articulo->categoria->nombre }}</a></p>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label for="precio_compra" class="form-label">Precio de Compra</label>
                                                            <p class="text-danger">{{ $config->currency_simbol }}.{{ number_format($articulo->precio_compra, 2, '.', ',') }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label for="precio_venta" class="form-label">Precio de Venta</label>
                                                            <p class="text-success">{{ $config->currency_simbol }}.{{ number_format($articulo->precio_venta, 2, '.', ',') }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="stock" class="form-label">Stock</label>
                                                        <p>{{ $articulo->stock }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                                                        <p>{{ $articulo->stock_minimo }}</p>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="unidad" class="form-label">Unidad de Medida</label>
                                                        <p><a class=" text-primary" href="{{ url('show-unidad/'.$articulo->categoria_id) }}">{{ $articulo->unidad->nombre }} ({{ $articulo->unidad->abreviatura }})</a></p>
                                                    </div>


                                                </div>
                                                @if($articulo->tipo == 'servicio')
                                                    <div class="col-md-12 mb-3">
                                                        <h5>Artículos del Servicio</h5>
                                                        <div class="table-responsive">
                                                            <table class="table align-middle table-striped flex-column">
                                                                <thead>
                                                                    <tr>
                                                                        <td><strong>Artículo</strong></td>
                                                                        <td align="center"><strong>Cantidad</strong></td>
                                                                        <td align="right"><strong>Costo</strong></td>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @php
                                                                        $totalCosto = 0;
                                                                    @endphp
                                                                    @foreach($articulo->articulos as $articuloServicio)
                                                                        @php
                                                                            $costo = $articuloServicio->precio_compra * $articuloServicio->pivot->cantidad;
                                                                            $totalCosto += $costo;
                                                                        @endphp
                                                                        <tr>
                                                                            <td>
                                                                                <a href="{{ url('show-articulo', $articuloServicio->id) }}">
                                                                                    <h5 class="text-primary">{{ $articuloServicio->nombre }}</h5>
                                                                                    <div class="text-muted"><small>Código: <strong>{{ $articuloServicio->codigo }}</strong></small></div>
                                                                                    <div class="text-muted"><small>Categoría: <strong>{{ $articuloServicio->categoria->nombre }}</strong></small></div>
                                                                                </a>
                                                                            </td>
                                                                            <td align="center">{{ $articuloServicio->pivot->cantidad }} ({{ $articuloServicio->unidad->abreviatura }})</td>
                                                                            <td align="right" class="text-warning">{{ $config->currency_simbol }}.{{ number_format($costo, 2, '.', ',') }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <th colspan="2" class="text-end">Total Costo:</th>
                                                                        <th class="text-end text-warning">{{ $config->currency_simbol }}.{{ number_format($totalCosto, 2, '.', ',') }}</th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <!-- Row end -->
                                    </div>
                                </div>
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
