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
                    <li class="breadcrumb-item active" aria-current="page">Nueva Categoría</li>
                </ol>
            </nav>
            <!-- Breadcrumb end -->

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="card-title"><i class="bi bi-plus-circle-fill text-success me-2"></i>Crear Nueva Categoría</h5>
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

                            <form action="{{ url('insert-categoria') }}" method="POST" id="categoriaForm">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-12 mb-3">
                                        <label for="nombre" class="form-label">Nombre de la Categoría <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-tag-fill"></i></span>
                                            <input name="nombre" type="text" class="form-control" id="nombre" placeholder="Ej: Repuestos, Accesorios..." value="{{ old('nombre') }}" required autofocus>
                                        </div>
                                        <div class="form-text">El nombre debe ser único y descriptivo</div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="descripcion" class="form-label">Descripción</label>
                                        <textarea name="descripcion" id="descripcion" class="form-control" rows="4" placeholder="Descripción detallada de la categoría...">{{ old('descripcion') }}</textarea>
                                        <div class="form-text">Agregue información adicional sobre el propósito de esta categoría</div>
                                    </div>

                                    <div class="col-md-12 text-center mb-3">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ url('categorias') }}" class="btn btn-outline-secondary">
                                                <i class="bi bi-arrow-left me-1"></i> Volver
                                            </a>
                                            <div>
                                                <button type="reset" class="btn btn-outline-danger me-2">
                                                    <i class="bi bi-x-circle me-1"></i> Limpiar
                                                </button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-check2-circle me-1"></i> Guardar
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
