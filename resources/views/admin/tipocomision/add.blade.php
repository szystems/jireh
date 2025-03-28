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
            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Crear Tipo de Comisión</h5>
                                <a href="{{ url('tipo-comisiones') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Volver al listado
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (count($errors)>0)
                                <div class="alert alert-danger" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{$error}}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ url('insert-tipo-comision') }}" method="POST">
                                @csrf
                                <div class="row gx-3">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                            <input name="nombre" type="text" class="form-control" placeholder="Nombre del tipo de comisión..." value="{{ old('nombre') }}" required />
                                            @if ($errors->has('nombre'))
                                                <div class="text-danger mt-1">
                                                    {{ $errors->first('nombre') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-group">
                                            <label for="porcentaje" class="form-label">Porcentaje <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input name="porcentaje" type="number" step="0.01" class="form-control" placeholder="0.00" value="{{ old('porcentaje') }}" required />
                                                <span class="input-group-text">%</span>
                                            </div>
                                            @if ($errors->has('porcentaje'))
                                                <div class="text-danger mt-1">
                                                    {{ $errors->first('porcentaje') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <label for="descripcion" class="form-label">Descripción</label>
                                            <textarea name="descripcion" class="form-control" rows="3" placeholder="Descripción del tipo de comisión...">{{ old('descripcion') }}</textarea>
                                            @if ($errors->has('descripcion'))
                                                <div class="text-danger mt-1">
                                                    {{ $errors->first('descripcion') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 justify-content-center mt-3">
                                    <a href="{{ url('tipo-comisiones') }}" class="btn btn-danger">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check2-square"></i> Grabar
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
