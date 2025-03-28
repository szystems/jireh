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
            <!-- Date range start -->
            <div class="d-flex align-items-end d-none d-sm-block">
                <h6 class="float-end text-light" id="reloj"></h6>
            </div>
        </div>
        <!-- Main header ends -->

        <!-- Content wrapper start -->
        <div class="content-wrapper">

            @include('admin.unidad.search')

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">

                        <div class="card-header">
                            <div class="card-title">
                                Listado de Unidades de Medida
                                <span class="badge bg-info ms-2">{{ $unidades->total() }}</span>
                                <a href="{{ url('add-unidad') }}" type="button" class="btn btn-success float-end">
                                    <i class="bi bi-plus-square"></i> Agregar
                                </a>
                                @if($queryUnidad)
                                    <a href="{{ url('unidades') }}" class="btn btn-outline-secondary float-end me-2">
                                        <i class="bi bi-x-circle"></i> Limpiar filtros
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if($unidades->count() > 0)
                                <div class="table-responsive">
                                    <table class="table align-middle table-striped">
                                        <thead>
                                            <tr>
                                                <th width="10%">Acciones</th>
                                                <th>Nombre</th>
                                                <th>Abreviatura</th>
                                                <th>Tipo</th>
                                                <th>Actualización</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($unidades as $unidad)
                                            <tr>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ url('show-unidad/'.$unidad->id) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </a>
                                                        <a href="{{ url('edit-unidad/'.$unidad->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $unidad->id }}" title="Eliminar">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a class="text-primary" href="{{ url('show-unidad/'.$unidad->id) }}">
                                                        <b>{{ $unidad->nombre }}</b>
                                                    </a>
                                                </td>
                                                <td>{{ $unidad->abreviatura }}</td>
                                                <td>
                                                    @if($unidad->tipo == 'unidad')
                                                        <span class="badge bg-primary">Unidad</span>
                                                    @else
                                                        <span class="badge bg-success">Decimal</span>
                                                    @endif
                                                </td>
                                                <td><small>{{ date('d/m/Y H:i', strtotime($unidad->updated_at)) }}</small></td>
                                            </tr>
                                            @include('admin.unidad.deletemodal')
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="pagination justify-content-end mt-3">
                                        {{ $unidades->links() }}
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="bi bi-info-circle me-2"></i>
                                    No se encontraron unidades de medida
                                    @if($queryUnidad)
                                        con el criterio de búsqueda "{{ $queryUnidad }}"
                                    @endif
                                </div>
                            @endif
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

