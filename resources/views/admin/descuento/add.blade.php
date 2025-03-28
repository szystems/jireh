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
            <div class="subscribe-header">
                <img src="{{ asset('dashboardtemplate/design/assets/images/bg.jpg') }}" class="img-fluid w-100" alt="Header" />
            </div>
            <div class="subscriber-body">
                <!-- Row start -->
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-12">
                        <!-- Row start -->
                        <div class="row align-items-end">
                            <div class="col-auto">
                                <div class="avatar-circle bg-success">
                                    <i class="bi bi-percent text-white display-4"></i>
                                </div>
                            </div>
                            <div class="col">
                                <h6>Nuevo Descuento</h6>
                            </div>
                        </div>
                        <!-- Row end -->
                    </div>
                </div>
                <!-- Row end -->

                <!-- Row start -->
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-10">
                        <div class="card light">
                            <div class="card-body">
                                <div class="custom-tabs-container">
                                    <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA" role="tab"
                                                aria-controls="oneA" aria-selected="true">Crear Descuento</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="oneA" role="tabpanel">
                                            <!-- Row start -->
                                            <div class="row gx-3">
                                                <div class="col-sm-12 col-12">
                                                    @if (count($errors)>0)
                                                        <div class="alert alert-danger" role="alert">
                                                            <ul class="mb-0">
                                                                @foreach ($errors->all() as $error)
                                                                    <li>{{$error}}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif
                                                    <form action="{{ url('insert-descuento') }}" method="POST" id="descuentoForm" class="needs-validation" novalidate>
                                                        @csrf
                                                        <input type="hidden" name="estado" value="1" />

                                                        <div class="card mb-3">
                                                            <div class="card-header bg-light">
                                                                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Información del Descuento</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row gx-3">
                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="nombre" class="form-label">Nombre del Descuento <span class="text-danger">*</span></label>
                                                                        <div class="input-group">
                                                                            <span class="input-group-text"><i class="bi bi-tag"></i></span>
                                                                            <input name="nombre" type="text" class="form-control" id="nombre"
                                                                                placeholder="Ej: Descuento Especial, Descuento por Cantidad..."
                                                                                value="{{ old('nombre') }}" required />
                                                                            <div class="invalid-feedback">
                                                                                Por favor ingrese el nombre del descuento.
                                                                            </div>
                                                                        </div>
                                                                        @if ($errors->has('nombre'))
                                                                            <span class="text-danger small">{{ $errors->first('nombre') }}</span>
                                                                        @endif
                                                                    </div>

                                                                    <div class="col-md-6 mb-3">
                                                                        <label for="porcentaje_descuento" class="form-label">Porcentaje <span class="text-danger">*</span></label>
                                                                        <div class="input-group">
                                                                            <span class="input-group-text"><i class="bi bi-percent"></i></span>
                                                                            <input name="porcentaje_descuento" type="number" class="form-control" id="porcentaje_descuento"
                                                                                placeholder="Ej: 10, 15, 20..."
                                                                                min="0" max="100" step="0.01"
                                                                                value="{{ old('porcentaje_descuento') }}" required />
                                                                            <span class="input-group-text">%</span>
                                                                            <div class="invalid-feedback">
                                                                                Por favor ingrese un porcentaje válido (0-100).
                                                                            </div>
                                                                        </div>
                                                                        @if ($errors->has('porcentaje_descuento'))
                                                                            <span class="text-danger small">{{ $errors->first('porcentaje_descuento') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>

                                                                <div class="alert alert-info mt-3">
                                                                    <i class="bi bi-info-circle me-2"></i>
                                                                    <strong>Información:</strong> Los descuentos se aplican a los productos según el porcentaje especificado.
                                                                    Por ejemplo, un descuento del 10% reducirá el precio de un producto en un 10%.
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex gap-2 justify-content-center mt-4">
                                                            <a href="{{ url('descuentos') }}" class="btn btn-danger">
                                                                <i class="bi bi-x-circle me-1"></i> Cancelar
                                                            </a>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="bi bi-check2-square me-1"></i> Guardar Descuento
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            <!-- Row end -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row end -->
            </div>
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->

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
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validación del lado del cliente
        var form = document.getElementById('descuentoForm');

        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        }, false);

        // Limitar el valor del porcentaje entre 0 y 100
        var porcentajeInput = document.getElementById('porcentaje_descuento');
        porcentajeInput.addEventListener('input', function() {
            if (this.value > 100) this.value = 100;
            if (this.value < 0) this.value = 0;
        });
    });
</script>
@endsection
