@extends('layouts.admin')
@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-rulers"></i>
                </div>
                <div class="page-title">
                    <h5>Unidades de Medida</h5>
                </div>
            </div>
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
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                <div class="d-flex align-items-center">
                                    <span class="me-2">Detalles de Unidad de Medida</span>
                                    @if($unidad->tipo == 'unidad')
                                        <span class="badge bg-primary">Unidad</span>
                                    @else
                                        <span class="badge bg-success">Decimal</span>
                                    @endif
                                </div>
                                <div class="float-end">
                                    <a href="{{ url('unidades') }}" class="btn btn-sm btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Volver
                                    </a>
                                    <a href="{{ url('edit-unidad/'.$unidad->id) }}" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil-fill"></i> Editar
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $unidad->id }}">
                                        <i class="bi bi-trash-fill"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-card mb-3">
                                        <h5 class="border-bottom pb-2 mb-3">Información General</h5>
                                        <table class="table table-striped">
                                            <tbody>
                                                <tr>
                                                    <th width="30%">Nombre:</th>
                                                    <td>{{ $unidad->nombre }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Abreviatura:</th>
                                                    <td>{{ $unidad->abreviatura }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Tipo:</th>
                                                    <td>
                                                        @if($unidad->tipo == 'unidad')
                                                            <span class="badge bg-primary">Unidad (cantidades enteras)</span>
                                                        @else
                                                            <span class="badge bg-success">Decimal (cantidades con decimales)</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Estado:</th>
                                                    <td>
                                                        @if($unidad->estado == 1)
                                                            <span class="badge bg-success">Activo</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactivo</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-card mb-3">
                                        <h5 class="border-bottom pb-2 mb-3">Información Adicional</h5>
                                        <table class="table table-striped">
                                            <tbody>
                                                <tr>
                                                    <th width="30%">Creado:</th>
                                                    <td>{{ date('d/m/Y H:i', strtotime($unidad->created_at)) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Actualizado:</th>
                                                    <td>{{ date('d/m/Y H:i', strtotime($unidad->updated_at)) }}</td>
                                                </tr>
                                                {{-- <tr>
                                                    <th>ID:</th>
                                                    <td>{{ $unidad->id }}</td>
                                                </tr> --}}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Artículos relacionados si se implementa -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="info-card">
                                        <h5 class="border-bottom pb-2 mb-3">Artículos que usan esta unidad</h5>
                                        @php
                                            $articulos = \App\Models\Articulo::where('unidad_id', $unidad->id)->limit(5)->get();
                                        @endphp

                                        @if($articulos->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Artículo</th>
                                                        <th>Precio</th>
                                                        <th>Stock</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($articulos as $articulo)
                                                    <tr>
                                                        <td>{{ $articulo->id }}</td>
                                                        <td>{{ $articulo->nombre }}</td>
                                                        <td>{{ $articulo->precio_venta }}</td>
                                                        <td>{{ $articulo->stock }}</td>
                                                        <td>
                                                            <a href="{{ url('show-articulo/'.$articulo->id) }}" class="btn btn-sm btn-info">
                                                                <i class="bi bi-eye-fill"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>

                                            @if(\App\Models\Articulo::where('unidad_id', $unidad->id)->count() > 5)
                                                <div class="text-center mt-2">
                                                    <a href="{{ url('articulos?funidad='.$unidad->id) }}" class="btn btn-outline-primary btn-sm">
                                                        Ver todos los artículos
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        @else
                                            <div class="alert alert-info">
                                                <i class="bi bi-info-circle me-2"></i> No hay artículos asociados a esta unidad de medida.
                                            </div>
                                        @endif
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

    @include('admin.unidad.deletemodal')
@endsection
