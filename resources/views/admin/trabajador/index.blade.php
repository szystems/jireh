@extends('layouts.admin')
@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-people"></i>
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

            <!-- Sección de filtros mejorada -->
            <div class="row gx-3 mb-3">
                <div class="col-xl-12">
                    <div class="card card-background-mask-info">
                        <div class="card-body">
                            <form action="{{ url('trabajadores') }}" method="GET" id="search-form">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="input-group">
                                            <input class="form-control" placeholder="Buscar por nombre, apellido o teléfono..." name="buscar" value="{{ request('buscar') }}"/>
                                            <button class="btn btn-outline-secondary" type="submit">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <select class="form-select" name="tipo_trabajador" onchange="this.form.submit()">
                                            <option value="">Todos los tipos</option>
                                            @foreach($tipoTrabajadores as $tipo)
                                                <option value="{{ $tipo->id }}" {{ request('tipo_trabajador') == $tipo->id ? 'selected' : '' }}>
                                                    {{ $tipo->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <div class="d-flex align-items-center">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="toggleInactivos" name="mostrar_inactivos" value="1" {{ request('mostrar_inactivos') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="toggleInactivos">Mostrar inactivos</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mb-2 text-end">
                                        <a href="{{ url('trabajadores') }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-x-circle"></i> Limpiar
                                        </a>
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
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="card-title">Lista de Trabajadores</h5>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <a href="{{ url('add-trabajador') }}" class="btn btn-primary">
                                        <i class="bi bi-plus-circle"></i> Agregar Trabajador
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Nombre</th>
                                            <th class="text-center">Teléfono</th>
                                            <th class="text-center">Tipo</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($trabajadores as $trabajador)
                                            <tr class="trabajador-row {{ $trabajador->estado == 0 ? 'trabajador-inactivo' : '' }}">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <i class="bi bi-person-fill fs-4 text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $trabajador->nombre }} {{ $trabajador->apellido }}</h6>
                                                            <small class="text-muted">{{ $trabajador->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $trabajador->telefono }}</td>
                                                <td class="text-center">
                                                    @if($trabajador->tipoTrabajador)
                                                        <span class="badge bg-info">{{ $trabajador->tipoTrabajador->nombre }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">No asignado</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($trabajador->estado == 1)
                                                        <span class="badge bg-success">Activo</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactivo</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <a href="{{ url('show-trabajador/'.$trabajador->id) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </a>
                                                        <a href="{{ url('edit-trabajador/'.$trabajador->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                        @if($trabajador->estado == 1)
                                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal-{{ $trabajador->id }}" title="Desactivar">
                                                                <i class="bi bi-toggle-on"></i>
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal-{{ $trabajador->id }}" title="Activar">
                                                                <i class="bi bi-toggle-off"></i>
                                                            </button>
                                                        @endif
                                                    </div>

                                                    <!-- Versión simplificada del modal para evitar conflictos -->
<div class="modal fade" id="statusModal-{{ $trabajador->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="statusModalLabel-{{ $trabajador->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="statusModalLabel-{{ $trabajador->id }}">
            @if($trabajador->estado == 1)
                <i class="bi bi-toggle-off text-danger"></i> Desactivar Trabajador
            @else
                <i class="bi bi-toggle-on text-success"></i> Activar Trabajador
            @endif
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        @if($trabajador->estado == 1)
            ¿Está seguro que desea desactivar al trabajador <strong>{{ $trabajador->nombre }} {{ $trabajador->apellido }}</strong>?
            <br><br>
            <span class="text-muted">Al desactivar este trabajador, no aparecerá en los listados principales pero se conservarán todos sus datos.</span>
        @else
            ¿Está seguro que desea activar al trabajador <strong>{{ $trabajador->nombre }} {{ $trabajador->apellido }}</strong>?
        @endif
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <a href="{{ url('delete-trabajador/'.$trabajador->id) }}" class="btn @if($trabajador->estado == 1) btn-danger @else btn-success @endif">
            @if($trabajador->estado == 1)
                <i class="bi bi-toggle-off"></i> Desactivar
            @else
                <i class="bi bi-toggle-on"></i> Activar
            @endif
        </a>
      </div>
    </div>
  </div>
</div>

                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No hay trabajadores registrados</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación con preservación de filtros -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $trabajadores->appends(request()->query())->links() }}
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

