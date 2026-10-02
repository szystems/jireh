@extends('layouts.admin')

@section('content')
    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center">
                <div class="page-icon"><i class="bi bi-diagram-3"></i></div>
                <div class="page-title"><h5>Editar departamento</h5></div>
            </div>
        </div>
        <div class="content-wrapper">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card">
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{ url('update-departamento/'.$departamento->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="nombre">Nombre guardado</label>
                                    <input class="form-control" id="nombre" name="nombre" value="{{ old('nombre', $departamento->nombre) }}" maxlength="100" required {{ $usos > 0 ? 'readonly' : '' }}>
                                    @if($usos > 0)
                                        <small class="text-muted">Hay {{ $usos }} facturas o cotizaciones con este valor. No se renombra para no mover ventas anteriores.</small>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="etiqueta">Nombre visible</label>
                                    <input class="form-control" id="etiqueta" name="etiqueta" value="{{ old('etiqueta', $departamento->etiqueta) }}" maxlength="100" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="orden">Orden</label>
                                    <input class="form-control" id="orden" name="orden" type="number" min="0" max="9999" value="{{ old('orden', $departamento->orden) }}">
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" {{ old('activo', $departamento->activo) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activo">Activo en ventas y cotizaciones nuevas</label>
                                </div>
                                <a href="{{ url('departamentos') }}" class="btn btn-outline-secondary">Volver</a>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
