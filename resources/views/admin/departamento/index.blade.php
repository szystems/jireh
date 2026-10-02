@extends('layouts.admin')

@section('content')
    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <div class="page-title">
                    <h5>Departamentos</h5>
                </div>
            </div>
        </div>

        <div class="content-wrapper">
            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="alert alert-info">
                Estos nombres aparecen al crear una venta o una cotización. Agregar uno no cambia las facturas ni las comisiones ya guardadas.
                Si un departamento tiene facturas, se puede desactivar, no borrar.
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Listado</h5>
                    <a href="{{ url('add-departamento') }}" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-square"></i> Agregar
                    </a>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Orden</th>
                                <th>Valor guardado</th>
                                <th>Nombre visible</th>
                                <th class="text-end">Facturas y cotizaciones</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departamentos as $departamento)
                                <tr>
                                    <td>{{ $departamento->orden }}</td>
                                    <td>{{ $departamento->nombre }}</td>
                                    <td>{{ $departamento->etiqueta }}</td>
                                    <td class="text-end">{{ $usos[$departamento->nombre] ?? 0 }}</td>
                                    <td>
                                        @if($departamento->activo)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ url('edit-departamento/'.$departamento->id) }}" class="btn btn-outline-primary btn-sm">Editar</a>
                                        <a href="{{ url('delete-departamento/'.$departamento->id) }}" class="btn btn-outline-danger btn-sm" onclick="return confirm('{{ ($usos[$departamento->nombre] ?? 0) > 0 ? 'Tiene registros. Se va a desactivar y las facturas anteriores no se modifican.' : '¿Eliminar este departamento?' }}')">
                                            {{ ($usos[$departamento->nombre] ?? 0) > 0 ? 'Desactivar' : 'Eliminar' }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay departamentos.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
