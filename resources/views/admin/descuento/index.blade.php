@extends('layouts.admin')

@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi-piggy-bank"></i>
                </div>
                <div class="page-title">
                    <h5>Descuentos</h5>
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

            <!-- Búsqueda y filtros -->
            <div class="row gx-3 mb-3">
                <div class="col-xl-12">
                    <div class="card card-background-mask-info">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Filtros y búsqueda</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('descuentos') }}" method="GET" id="search-form">
                                @csrf
                                <div class="row align-items-end">
                                    <div class="col-md-7 mb-2 mb-md-0">
                                        <label for="searchInput" class="form-label text-white small mb-1">Buscar descuento</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <input id="searchInput" class="form-control"
                                                placeholder="Nombre del descuento..."
                                                name="fdescuento" value="{{ $queryDescuento }}"/>
                                            <button class="btn btn-primary" type="submit">
                                                Buscar
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                                        <label class="form-label text-white small mb-1">Estado</label>
                                        <div class="d-flex align-items-center bg-white rounded px-3 py-2">
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox"
                                                    id="mostrarInactivos" name="mostrar_inactivos"
                                                    onchange="document.getElementById('search-form').submit()"
                                                    {{ request()->input('mostrar_inactivos') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="mostrarInactivos">
                                                    Mostrar inactivos
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-6">
                                        <div class="d-flex justify-content-end h-100 align-items-center">
                                            <span class="badge bg-info fs-6">
                                                <i class="bi bi-percent me-1"></i>
                                                {{ $descuentos->total() }} descuentos
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">

                        <div class="card-header">
                            <div class="card-title">
                                Listado de Descuentos
                                <a href="{{ url('add-descuento') }}" type="button" class="btn btn-success float-end">
                                    <i class="bi bi-plus-square"></i> Agregar
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($descuentos->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th width="15%" class="text-center">Acciones</th>
                                                <th width="60%">Nombre del Descuento</th>
                                                <th width="25%" class="text-center">Porcentaje</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($descuentos as $descuento)
                                            <tr class="{{ !$descuento->estado ? 'table-secondary' : '' }}">
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ url('show-descuento/'.$descuento->id) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </a>
                                                        <a href="{{ url('edit-descuento/'.$descuento->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm {{ $descuento->estado ? 'btn-danger' : 'btn-success' }}" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $descuento->id }}" title="{{ $descuento->estado ? 'Desactivar' : 'Activar' }}">
                                                            <i class="bi {{ $descuento->estado ? 'bi-trash-fill' : 'bi-check-circle-fill' }}"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a class="text-primary fw-bold" href="{{ url('show-descuento/'.$descuento->id) }}">
                                                        {{ $descuento->nombre }}
                                                    </a>
                                                    @if(!$descuento->estado)
                                                        <span class="badge bg-danger ms-2">Inactivo</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary fs-6">{{ $descuento->porcentaje_descuento }}%</span>
                                                </td>
                                            </tr>
                                            @include('admin.descuento.deletemodal')
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            Mostrando {{ $descuentos->firstItem() ?? 0 }} - {{ $descuentos->lastItem() ?? 0 }} de {{ $descuentos->total() }} registros
                                        </div>
                                        <div>
                                            {{ $descuentos->appends(request()->except('page'))->links() }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-1"></i> No se encontraron descuentos con los criterios de búsqueda especificados.
                                    <a href="{{ url('descuentos') }}" class="alert-link">Ver todos los descuentos</a>
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar tooltips para los botones de acción
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection

