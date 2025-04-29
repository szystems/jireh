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
                    <h5>Ver Trabajador</h5>
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
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="card-title">Datos del Trabajador</h5>
                                </div>
                                <div class="col-md-6 text-end">
                                    <a href="{{ url('trabajadores') }}" class="btn btn-info">
                                        <i class="bi bi-arrow-left"></i> Volver
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold">Información Personal:</h6>
                                    <table class="table table-striped">
                                        <tr>
                                            <th width="30%">Nombre Completo:</th>
                                            <td>{{ $trabajador->nombre }} {{ $trabajador->apellido }}</td>
                                        </tr>
                                        <tr>
                                            <th>Teléfono:</th>
                                            <td>{{ $trabajador->telefono }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email:</th>
                                            <td>{{ $trabajador->email ?: 'No registrado' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Dirección:</th>
                                            <td>{{ $trabajador->direccion }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tipo:</th>
                                            <td>
                                                @if($trabajador->tipoTrabajador)
                                                    <span class="badge bg-info">{{ $trabajador->tipoTrabajador->nombre }}</span>
                                                @else
                                                    <span class="badge bg-secondary">No asignado</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <h6 class="fw-bold">Información Fiscal:</h6>
                                    <table class="table table-striped">
                                        <tr>
                                            <th width="30%">NIT:</th>
                                            <td>{{ $trabajador->nit ?: 'No registrado' }}</td>
                                        </tr>
                                        <tr>
                                            <th>DPI:</th>
                                            <td>{{ $trabajador->dpi ?: 'No registrado' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Estado:</th>
                                            <td>
                                                @if($trabajador->estado == 1)
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-danger">Inactivo</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- Botón para activar/desactivar trabajador con implementación simplificada -->
                                <div class="col-12 mt-3">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ url('trabajadores') }}" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left"></i> Volver
                                        </a>
                                        <div>
                                            <a href="{{ url('edit-trabajador/'.$trabajador->id) }}" class="btn btn-warning">
                                                <i class="bi bi-pencil"></i> Editar
                                            </a>
                                            <!-- Botón para abrir el modal -->
                                            <button type="button" class="btn @if($trabajador->estado == 1) btn-danger @else btn-success @endif"
                                                    data-bs-toggle="modal" data-bs-target="#estadoModalTrabajador">
                                                <i class="bi @if($trabajador->estado == 1) bi-toggle-on @else bi-toggle-off @endif"></i>
                                                {{ $trabajador->estado == 1 ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </div>
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

    @include('admin.trabajador.deletemodal')

    <!-- Modal para activar/desactivar trabajador -->
    <div class="modal fade" id="estadoModalTrabajador" tabindex="-1" aria-labelledby="estadoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="estadoModalLabel">
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

@endsection

@section('styles')
<style>
    .avatar-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Estilos para corregir el modal */
    /* Hacer que el modal aparezca en la parte superior de la pantalla */
    .modal-dialog {
        margin-top: 10vh; /* Ajusta este valor para controlar la posición vertical */
    }

    /* Asegurar que el modal esté por encima del backdrop */
    .modal {
        z-index: 1072 !important;
    }

    .modal-backdrop {
        z-index: 1071 !important;
    }

    /* Asegurar visibilidad del modal */
    .modal.show .modal-dialog {
        opacity: 1;
        visibility: visible;
    }
</style>
@endsection

@section('scripts')
<script>
// Elimina todo el JavaScript que manejaba modales, ya usamos atributos data-bs-*
</script>
@endsection
