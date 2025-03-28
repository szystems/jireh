@extends('layouts.admin')
@section('content')
    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-cart-plus"></i>
                </div>
                <div class="page-title">
                    <h5>Detalles del Ingreso #{{ $ingreso->id }}</h5>
                </div>
            </div>
        </div>
        <div class="content-wrapper">
            <div class="row gx-3">
                <!-- Columna de información general -->
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-3"><i class="bi bi-info-circle"></i> Información General</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-center mb-3">
                                <span class="badge bg-{{ $ingreso->tipo_compra == 'Car Wash' ? 'info' : 'warning text-dark' }} fs-5 px-4 py-2">
                                    <i class="bi bi-tag-fill me-2"></i> {{ $ingreso->tipo_compra }}
                                </span>
                            </div>
                            <table class="table table-borderless">
                                <tr>
                                    <th><i class="bi bi-calendar-date"></i> Fecha:</th>
                                    <td class="text-end fw-bold">{{ \Carbon\Carbon::parse($ingreso->fecha)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-receipt"></i> Factura:</th>
                                    <td class="text-end">{{ $ingreso->numero_factura ?: 'Sin número de factura' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-shop"></i> Proveedor:</th>
                                    <td class="text-end">
                                        <a class="text-primary" href="{{ url('show-proveedor/'.$ingreso->proveedor_id) }}">
                                            {{ optional($ingreso->proveedor)->nombre ?? 'N/A' }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-person"></i> Usuario:</th>
                                    <td class="text-end">{{ $ingreso->usuario ? $ingreso->usuario->name : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th><i class="bi bi-clock-history"></i> Creado:</th>
                                    <td class="text-end">{{ $ingreso->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @if($ingreso->created_at != $ingreso->updated_at)
                                <tr>
                                    <th><i class="bi bi-arrow-clockwise"></i> Actualizado:</th>
                                    <td class="text-end">{{ $ingreso->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ url('ingresos') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Volver
                                </a>
                                <a href="{{ route('ingresos.export.single.pdf', ['id' => $ingreso->id]) }}" class="btn btn-danger">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna de resumen financiero -->
                <div class="col-md-8 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-3"><i class="bi bi-cart-check-fill"></i> Detalles del Ingreso</h5>
                            <div>
                                <a href="{{ url('edit-ingreso/'.$ingreso->id) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil-fill"></i> Editar
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $ingreso->id }}">
                                    <i class="bi bi-trash-fill"></i> Eliminar
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-striped" style="font-size: 14px;">
                                    <thead class="bg-info text-white">
                                        <tr>
                                            <th>Artículo</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-end">Precio</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalIngreso = 0;
                                            $totalItems = 0;
                                        @endphp
                                        @foreach($ingreso->detalles as $detalle)
                                            @php
                                                $subtotal = $detalle->cantidad * $detalle->precio_compra;
                                                $totalIngreso += $subtotal;
                                                $totalItems += $detalle->cantidad;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong class="text-primary">{{ optional($detalle->articulo)->nombre }}</strong>
                                                    @if(optional($detalle->articulo)->codigo)
                                                        <br><small class="text-muted">Código: {{ $detalle->articulo->codigo }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center align-middle">
                                                    <span class="badge bg-secondary">
                                                        {{ $detalle->cantidad }} {{ optional($detalle->articulo->unidad)->abreviatura ?? '' }}
                                                    </span>
                                                </td>
                                                <td class="text-end align-middle">
                                                    {{ $config->currency_simbol }}.{{ number_format($detalle->precio_compra, 2, '.', ',') }}
                                                </td>
                                                <td class="text-end align-middle">
                                                    <strong>{{ $config->currency_simbol }}.{{ number_format($subtotal, 2, '.', ',') }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-warning">
                                        <tr>
                                            <td><strong>Resumen</strong></td>
                                            <td class="text-center"><strong>{{ $totalItems }} artículos</strong></td>
                                            <td class="text-end" colspan="2">
                                                <h5 class="mb-0 text-success">Total: {{ $config->currency_simbol }}.{{ number_format($totalIngreso, 2, '.', ',') }}</h5>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notas o información adicional, si es necesario -->
                @if(false) <!-- Desactivado por ahora, activar si se requiere -->
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-journals"></i> Notas Adicionales</h5>
                        </div>
                        <div class="card-body">
                            <p>No hay notas adicionales para este ingreso.</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @include('admin.ingreso.deletemodal')
@endsection
