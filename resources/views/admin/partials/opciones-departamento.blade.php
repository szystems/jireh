@php
    $seleccionado = $seleccionado ?? '';
    $placeholder = $placeholder ?? null;
@endphp
@if($placeholder)
    <option value="" {{ $seleccionado === '' || $seleccionado === null ? 'selected' : '' }}>{{ $placeholder }}</option>
@endif
@foreach(\App\Models\Venta::departamentosActivos() as $valor => $etiqueta)
    <option value="{{ $valor }}" {{ (string) $seleccionado === (string) $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
@endforeach
@if($seleccionado && !array_key_exists($seleccionado, \App\Models\Venta::departamentosActivos()))
    <option value="{{ $seleccionado }}" selected>{{ \App\Models\Venta::etiquetaDepartamento($seleccionado) }}</option>
@endif
