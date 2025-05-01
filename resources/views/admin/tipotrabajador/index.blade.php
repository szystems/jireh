@extends('layouts.admin')
@section('content')

    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-tags"></i>
                </div>
                <div class="page-title">
                    <h5>Tipos de Trabajador</h5>
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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Listado de Tipos de Trabajador</h5>
                            <a href="{{ url('add-tipo-trabajador') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Nuevo Tipo
                            </a>
                        </div>
                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bi bi-check-circle me-1"></i>
                                    {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <div class="table-responsive">
                                <table class="table align-middle table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                            <th class="text-center">Comisiones</th>
                                            <th class="text-center">Características</th>
                                            <th class="text-center">Estado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($tipoTrabajadors as $tipo)
                                            <tr>
                                                <td>{{ $tipo->id }}</td>
                                                <td><strong>{{ $tipo->nombre }}</strong></td>
                                                <td>{{ Str::limit($tipo->descripcion, 50) }}</td>
                                                <td class="text-center">
                                                    @if($tipo->aplica_comision)
                                                        <span class="badge bg-success">Aplica</span>
                                                        @if($tipo->tipo_comision)
                                                            <span class="badge bg-info">
                                                                @if($tipo->tipo_comision == 'fijo')
                                                                    Monto fijo Q{{ number_format($tipo->valor_comision, 2) }}
                                                                @elseif($tipo->tipo_comision == 'porcentaje_venta' || $tipo->tipo_comision == 'porcentaje_ganancia')
                                                                    {{ number_format($tipo->porcentaje_comision, 2) }}% 
                                                                    {{ $tipo->tipo_comision == 'porcentaje_venta' ? 'de venta' : 'de ganancia' }}
                                                                @else
                                                                    Personalizada
                                                                @endif
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="badge bg-secondary">No aplica</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($tipo->requiere_asignacion)
                                                        <span class="badge bg-warning text-dark" title="Requiere asignación a servicios específicos">
                                                            <i class="bi bi-link"></i> Asignación
                                                        </span>
                                                    @endif
                                                    
                                                    @if($tipo->permite_multiples_trabajadores)
                                                        <span class="badge bg-primary" title="Permite múltiples trabajadores por servicio">
                                                            <i class="bi bi-people"></i> Múltiple
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($tipo->estado == 'activo')
                                                        <span class="badge bg-success">Activo</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactivo</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <a href="{{ url('show-tipo-trabajador/'.$tipo->id) }}" class="btn btn-info btn-sm" title="Ver detalles">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </a>
                                                        <a href="{{ url('edit-tipo-trabajador/'.$tipo->id) }}" class="btn btn-warning btn-sm" title="Editar">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $tipo->id }}" title="Eliminar">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </div>

                                                    <!-- Modal de eliminación para cada tipo -->
                                                    <div class="modal fade" id="deleteModal-{{ $tipo->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title text-danger" id="deleteModal">
                                                                        <i class="bi bi-trash-fill text-danger"></i> Eliminar Tipo de Trabajador
                                                                    </h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>¿Está seguro de eliminar este tipo de trabajador?</p>

                                                                    @if($tipo->trabajadores()->count() > 0)
                                                                        <div class="alert alert-warning">
                                                                            <i class="bi bi-exclamation-triangle me-2"></i>
                                                                            Este tipo tiene {{ $tipo->trabajadores()->count() }} trabajadores asociados.
                                                                            La eliminación no será posible hasta que reasigne estos trabajadores a otro tipo.
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-info" data-bs-dismiss="modal">
                                                                        <i class="bi bi-x-circle"></i> Cancelar
                                                                    </button>
                                                                    @if($tipo->trabajadores()->count() == 0)
                                                                        <a href="{{ url('delete-tipo-trabajador/'.$tipo->id) }}" class="btn btn-danger">
                                                                            <i class="bi bi-trash"></i> Eliminar
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No hay tipos de trabajador registrados</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <div class="d-flex justify-content-center mt-4">
                                    {{ $tipoTrabajadors->links() }}
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
