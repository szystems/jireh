@extends('layouts.admin')

@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="page-title">
                    <h5>Trabajadores</h5>
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
                            <form action="{{ url('trabajadores') }}" method="GET" id="search-form">
                                @csrf
                                <div class="row align-items-end">
                                    <div class="col-md-8 mb-2 mb-md-0">
                                        <label for="searchInput" class="form-label text-white small mb-1">Buscar trabajador</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <input id="searchInput" class="form-control" list="dataListOptions"
                                                placeholder="Nombre, documento o teléfono..."
                                                name="search" value="{{ request()->input('search') }}"/>
                                            <datalist id="dataListOptions">
                                                @foreach ($trabajadores as $trabajador)
                                                    <option value="{{ $trabajador->nombre }}">{{ $trabajador->nombre }}</option>
                                                @endforeach
                                            </datalist>
                                            <button class="btn btn-primary" type="submit">
                                                Buscar
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
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
                                Listado de Trabajadores
                                <span class="badge bg-info ms-2">{{ $trabajadores->total() }} registros</span>
                                <a href="{{ url('add-trabajador') }}" type="button" class="btn btn-success float-end">
                                    <i class="bi bi-plus-square"></i> Agregar
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($trabajadores->count() > 0)
                                <div class="table-responsive">
                                    <table id="trabajadoresTable" class="table custom-table">
                                        <thead>
                                            <tr>
                                                <th width="10%" align="center">Acciones</th>
                                                <th width="45%">
                                                    <a href="{{ url('trabajadores') }}?sort=nombre&direction={{ request()->input('sort') == 'nombre' && request()->input('direction') == 'asc' ? 'desc' : 'asc' }}&search={{ request()->input('search') }}">
                                                        Información
                                                        @if(request()->input('sort') == 'nombre')
                                                            <i class="bi bi-sort-{{ request()->input('direction') == 'asc' ? 'down' : 'up' }}"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                                <th width="20%">
                                                    <a href="{{ url('trabajadores') }}?sort=no_documento&direction={{ request()->input('sort') == 'no_documento' && request()->input('direction') == 'asc' ? 'desc' : 'asc' }}&search={{ request()->input('search') }}">
                                                        No. Documento
                                                        @if(request()->input('sort') == 'no_documento')
                                                            <i class="bi bi-sort-{{ request()->input('direction') == 'asc' ? 'down' : 'up' }}"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                                <th width="25%">
                                                    <a href="{{ url('trabajadores') }}?sort=fecha_nacimiento&direction={{ request()->input('sort') == 'fecha_nacimiento' && request()->input('direction') == 'asc' ? 'desc' : 'asc' }}&search={{ request()->input('search') }}">
                                                        Fecha de Nacimiento
                                                        @if(request()->input('sort') == 'fecha_nacimiento')
                                                            <i class="bi bi-sort-{{ request()->input('direction') == 'asc' ? 'down' : 'up' }}"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($trabajadores as $trabajador)
                                            <tr class="{{ $trabajador->estado !== 'activo' ? 'table-secondary' : '' }}">
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ url('show-trabajador/'.$trabajador->id) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </a>
                                                        <a href="{{ url('edit-trabajador/'.$trabajador->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm {{ $trabajador->estado === 'activo' ? 'btn-danger' : 'btn-success' }}"
                                                            data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $trabajador->id }}"
                                                            title="{{ $trabajador->estado === 'activo' ? 'Desactivar' : 'Activar' }}">
                                                            <i class="bi {{ $trabajador->estado === 'activo' ? 'bi-trash-fill' : 'bi-check-circle-fill' }}"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column">
                                                        <div class="d-flex align-items-center">
                                                            <a class="text-primary" href="{{ url('show-trabajador/'.$trabajador->id) }}">
                                                                <b>{{ $trabajador->nombre }}</b>
                                                            </a>
                                                            @if($trabajador->estado !== 'activo')
                                                                <span class="badge bg-danger ms-2">Inactivo</span>
                                                            @endif
                                                        </div>
                                                        <a href="mailto:{{ $trabajador->email }}">{{ $trabajador->email }}</a>
                                                        @if($trabajador->telefono)
                                                            <div>
                                                                <a href="tel:+502{{ preg_replace('/[^0-9]/', '', $trabajador->telefono) }}">{{ $trabajador->telefono }}</a>
                                                                <a href="https://wa.me/502{{ preg_replace('/[^0-9]/', '', $trabajador->telefono) }}" target="_blank" class="ms-1">
                                                                    <i class="bi bi-whatsapp text-success"></i>
                                                                </a>
                                                            </div>
                                                        @endif
                                                        <small class="text-muted">{{ $trabajador->direccion }}</small>
                                                    </div>
                                                </td>
                                                <td>{{ $trabajador->no_documento ?: 'No registrado' }}</td>
                                                <td>
                                                    @if($trabajador->fecha_nacimiento)
                                                        {{ \Carbon\Carbon::parse($trabajador->fecha_nacimiento)->format('d/m/Y') }}
                                                        <span class="badge bg-secondary">{{ \Carbon\Carbon::parse($trabajador->fecha_nacimiento)->age }} años</span>
                                                    @else
                                                        <span class="text-muted">No registrada</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @include('admin.trabajador.deletemodal')
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            Mostrando {{ $trabajadores->firstItem() ?? 0 }} - {{ $trabajadores->lastItem() ?? 0 }} de {{ $trabajadores->total() }} registros
                                        </div>
                                        <div>
                                            {{ $trabajadores->appends(request()->except('page'))->links() }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-1"></i> No se encontraron trabajadores con los criterios de búsqueda especificados.
                                    <a href="{{ url('trabajadores') }}" class="alert-link">Ver todos los trabajadores</a>
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

