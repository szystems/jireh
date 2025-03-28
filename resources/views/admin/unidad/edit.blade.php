@extends('layouts.admin')
@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-rulers"></i>
                </div>
                <div class="page-title">
                    <h5>Unidades de Medida</h5>
                </div>
            </div>
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
                            <div class="card-title">Editar Unidad: {{ $unidad->nombre }}</div>
                        </div>
                        <div class="card-body">
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            @if (count($errors)>0)
                                <div class="alert alert-danger" role="alert">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{$error}}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ url('update-unidad/'.$unidad->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row gx-3">
                                    <div class="col-md-4 mb-3">
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                            <input id="nombre" name="nombre" type="text" class="form-control"
                                                placeholder="Nombre de la unidad de medida" value="{{ old('nombre', $unidad->nombre) }}" required />
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="mb-3">
                                            <label for="abreviatura" class="form-label">Abreviatura <span class="text-danger">*</span></label>
                                            <input id="abreviatura" name="abreviatura" type="text" class="form-control"
                                                placeholder="Abreviatura" value="{{ old('abreviatura', $unidad->abreviatura) }}" required />
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="mb-3">
                                            <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                                            <select id="tipo" name="tipo" class="form-select" required>
                                                <option value="">Seleccione tipo</option>
                                                <option value="unidad"{{ old('tipo', $unidad->tipo) == 'unidad' ? ' selected' : '' }}>Unidad</option>
                                                <option value="decimal"{{ old('tipo', $unidad->tipo) == 'decimal' ? ' selected' : '' }}>Decimal</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 justify-content-center mt-4">
                                    <a href="{{ url('unidades') }}" class="btn btn-danger">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check2-square"></i> Actualizar
                                    </button>
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
@endsection
