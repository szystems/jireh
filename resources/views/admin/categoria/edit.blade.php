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
            <!-- Breadcrumb start -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('categorias') }}">Categorías</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('show-categoria/'.$categoria->id) }}">{{ $categoria->nombre }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
            <!-- Breadcrumb end -->

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="card-title"><i class="bi bi-pencil-fill text-warning me-2"></i>Editar Categoría</h5>
                        </div>
                        <div class="card-body">
                            @if (count($errors) > 0)
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Error!</strong> Por favor corrija los siguientes errores:
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ url('update-categoria/'.$categoria->id) }}" method="POST" id="categoriaForm">
                                @csrf
                                @method('PUT')
                                <div class="row g-3">
                                    <div class="col-md-12 mb-3">
                                        <label for="nombre" class="form-label">Nombre de la Categoría <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-tag-fill"></i></span>
                                            <input name="nombre" type="text" class="form-control" id="nombre" value="{{ old('nombre', $categoria->nombre) }}" required autofocus>
                                        </div>
                                        <div class="form-text">El nombre debe ser único y descriptivo</div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea name="descripcion" id="descripcion" class="form-control" rows="4">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                                        <div class="form-text">Agregue información adicional sobre el propósito de esta categoría</div>
                                    </div>

                                    <!-- Estadísticas de información - Nuevo -->
                                    <div class="col-md-12 mb-3">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title"><i class="bi bi-info-circle me-2"></i>Información</h6>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p class="mb-1"><strong>Fecha de creación:</strong><br>
                                                            <span class="text-muted">{{ $categoria->created_at->format('d/m/Y H:i') }}</span>
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-1"><strong>Última actualización:</strong><br>
                                                            <span class="text-muted">{{ $categoria->updated_at->format('d/m/Y H:i') }}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 text-center mb-3">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ url('show-categoria/'.$categoria->id) }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-arrow-left me-1"></i> Volver
                                            </a>
                                            <div>
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="bi bi-check2-circle me-1"></i> Actualizar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Validación del formulario
            const form = document.getElementById('categoriaForm');
            const nombreInput = document.getElementById('nombre');

            form.addEventListener('submit', function(e) {
                let valid = true;

                if (nombreInput.value.trim() === '') {
                    nombreInput.classList.add('is-invalid');
                    valid = false;
                } else {
                    nombreInput.classList.remove('is-invalid');
                }

                if (!valid) {
                    e.preventDefault();
                    alert('Por favor complete todos los campos requeridos.');
                }
            });
        });
    </script>
@endsection
