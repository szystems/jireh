@extends('layouts.admin')

@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <div class="page-title">
                    <h5>Categorías</h5>
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
            <div class="card mb-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="bi bi-search me-2"></i>Buscar Categorías
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ url('categorias') }}" method="GET">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="searchInput" class="form-label">Nombre de categoría</label>
                                <input id="searchInput" class="form-control" placeholder="Buscar..."
                                    name="fcategoria" value="{{ $queryCategoria ?? '' }}"/>
                            </div>
                            <div class="col-md-3 mb-3 d-flex align-items-end">
                                <button class="btn btn-primary w-100" type="submit">
                                    <i class="bi bi-search me-1"></i> Buscar
                                </button>
                            </div>
                            <div class="col-md-3 mb-3 d-flex align-items-end">
                                @if($queryCategoria ?? false)
                                <a href="{{ url('categorias') }}" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-x-circle me-1"></i> Limpiar
                                </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">
                                Listado de Categorías
                                <span class="badge bg-info ms-2">{{ $categorias->total() }}</span>
                                <a href="{{ url('add-categoria') }}" type="button" class="btn btn-success float-end">
                                    <i class="bi bi-plus-square"></i> Agregar
                                </a>

                                {{-- <a href="{{ url('export-categorias-pdf') }}" target="_blank" class="btn btn-danger float-end me-2">
                                    <i class="bi bi-file-pdf me-1"></i> PDF
                                </a> --}}
                            </div>
                        </div>
                        <div class="card-body">
                            @if(session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong><i class="bi bi-check-circle"></i> ¡Éxito!</strong> {{ session('status') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if($categorias->count() > 0)
                                <div class="table-responsive">
                                    <table class="table align-middle table-striped">
                                        <thead>
                                            <tr>
                                                <th width="10%">Acciones</th>
                                                <th>Nombre</th>
                                                <th>Descripción</th>
                                                <th>Artículos</th>
                                                <th>Estado</th>
                                                <th>Actualización</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($categorias as $categoria)
                                            <tr>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ url('show-categoria/'.$categoria->id) }}" class="btn btn-sm btn-info" title="Ver detalles">
                                                            <i class="bi bi-eye-fill"></i>
                                                        </a>
                                                        <a href="{{ url('edit-categoria/'.$categoria->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $categoria->id }}" title="Eliminar">
                                                            <i class="bi bi-trash-fill"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a class="text-primary" href="{{ url('show-categoria/'.$categoria->id) }}">
                                                        <b>{{ $categoria->nombre }}</b>
                                                    </a>
                                                </td>
                                                <td>{{ \Illuminate\Support\Str::limit($categoria->descripcion, 50) }}</td>
                                                <td>
                                                    @php
                                                        $cantidadArticulos = \App\Models\Articulo::where('categoria_id', $categoria->id)->count();
                                                    @endphp
                                                    <span class="badge bg-info">{{ $cantidadArticulos }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ $categoria->estado ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $categoria->estado ? 'Activa' : 'Inactiva' }}
                                                    </span>
                                                </td>
                                                <td><small>{{ date('d/m/Y H:i', strtotime($categoria->updated_at)) }}</small></td>
                                            </tr>
                                            @include('admin.categoria.deletemodal')
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="pagination justify-content-end mt-3">
                                        {{ $categorias->links() }}
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="bi bi-info-circle me-2"></i>
                                    No se encontraron categorías
                                    @if($queryCategoria ?? false)
                                        con el criterio de búsqueda "{{ $queryCategoria }}"
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

    <script>
        // Función para mostrar el reloj
        function actualizarReloj() {
            const ahora = new Date();
            const opciones = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('reloj').textContent = ahora.toLocaleString('es-ES', opciones);
        }

        // Iniciar reloj
        document.addEventListener('DOMContentLoaded', function() {
            actualizarReloj();
            setInterval(actualizarReloj, 1000);
        });
    </script>
@endsection

