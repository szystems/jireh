@extends('layouts.admin')

@section('content')
    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center">
                <div class="page-icon"><i class="bi bi-diagram-3"></i></div>
                <div class="page-title"><h5>Nuevo departamento</h5></div>
            </div>
        </div>
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{ url('insert-departamento') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="nombre">Nombre</label>
                                    <input class="form-control" id="nombre" name="nombre" value="{{ old('nombre') }}" maxlength="100" required placeholder="Ejemplo: CWAG2">
                                    <small class="text-muted">Es el valor que se guarda en la factura. Ejemplo: CWAG2, CDSAG2, AUTOSAG2.</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="etiqueta">Nombre visible</label>
                                    <input class="form-control" id="etiqueta" name="etiqueta" value="{{ old('etiqueta') }}" maxlength="100" required placeholder="Ejemplo: Carwash agencia 2">
                                </div>
                                <a href="{{ url('departamentos') }}" class="btn btn-outline-secondary">Volver</a>
                                <button type="submit" class="btn btn-success">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
