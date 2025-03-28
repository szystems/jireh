@extends('layouts.admin')

@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-piggy-bank"></i>
                </div>
                <div class="page-title">
                    <h5>Tipos de Comisiones</h5>
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

            @include('admin.tipocomision.search')

            <!-- Mensajes de estado -->
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">

                        <div class="card-header">
                            <div class="card-title">
                                Listado de tipos de comisiones
                                <a href="{{ url('add-tipo-comision') }}" type="button" class="btn btn-success float-end">
                                    <i class="bi bi-plus-square"></i> Agregar
                                </a>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table align-middle table-striped">
                                    <thead>
                                        <tr>
                                            <th width="15%">Acciones</th>
                                            <th width="35%">Nombre</th>
                                            <th width="30%">Descripción</th>
                                            <th width="20%">Porcentaje</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($tipocomisiones as $tipocomision)
                                        <tr>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ url('show-tipo-comision/'.$tipocomision->id) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </a>
                                                    <a href="{{ url('edit-tipo-comision/'.$tipocomision->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $tipocomision->id }}" title="Eliminar">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td>
                                                <a class="text-primary fw-bold" href="{{ url('show-tipo-comision/'.$tipocomision->id) }}">
                                                    {{ $tipocomision->nombre }}
                                                </a>
                                            </td>
                                            <td>{{ $tipocomision->descripcion }}</td>
                                            <td><span class="badge bg-success">{{ $tipocomision->porcentaje }}%</span></td>
                                        </tr>
                                        @include('admin.tipocomision.deletemodal')
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No se encontraron registros</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $tipocomisiones->links() }}
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

