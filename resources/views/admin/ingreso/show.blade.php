@extends('layouts.admin')
@section('content')
    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-cart-plus"></i>
                </div>
                <div class="page-title">
                    <h5>Detalles del Ingreso</h5>
                </div>
            </div>
        </div>
        <div class="content-wrapper">
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <a href="{{ url('ingresos')  }}" class="btn btn-primary"><i class="bi bi-arrow-left"></i> Volver</a>
                                <div>
                                    <a href="{{ route('ingresos.export.single.pdf', ['id' => $ingreso->id]) }}" class="btn btn-danger"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                                    <a href="{{ url('edit-ingreso/'.$ingreso->id)  }}" class="btn btn-warning"><i class="bi bi-pencil-fill"></i> Editar</a>
                                    <a type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $ingreso->id }}">
                                        <i class="bi bi-trash-fill"></i> Eliminar
                                    </a>
                                </div>
                            </div>
                            <div class="section-title"><strong>Información del Ingreso</strong></div>
                            <hr>
                            <table class="table table-borderless">
                                <tr>
                                    <th>Fecha</th>
                                    <td>{{ \Carbon\Carbon::parse($ingreso->fecha)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Número de Factura</th>
                                    <td>{{ $ingreso->numero_factura }}</td>
                                </tr>
                                <tr>
                                    <th>Proveedor</th>
                                    <td><a class="text-primary" href="{{ url('show-proveedor/'.$ingreso->proveedor_id) }}">{{ optional($ingreso->proveedor)->nombre ?? 'N/A' }}</a></td>
                                </tr>
                                <tr>
                                    <th>Tipo de Compra</th>
                                    <td>{{ $ingreso->tipo_compra }}</td>
                                </tr>
                                <tr>
                                    <th>Usuario</th>
                                    <td>{{ $ingreso->usuario ? $ingreso->usuario->name : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Total</th>
                                    <td class="text-warning">{{ $config->currency_simbol }}.{{ number_format($ingreso->detalles->sum('precio_compra'), 2, '.', ',') }}</td>
                                </tr>
                            </table>
                            <div class="section-title"><strong>Detalles del Ingreso</strong></div>
                            <hr>
                            <table class="table table-sm table-bordered table-striped" style="font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th>Artículo</th>
                                        <th class="text-center">Cantidad</th>
                                        <th class="text-end">Precio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ingreso->detalles as $detalle)
                                        <tr>
                                            <td class="text-info">{{ optional($detalle->articulo)->nombre }}</td>
                                            <td class="text-center">{{ $detalle->cantidad }}</td>
                                            <td class="text-end"><strong>{{ $config->currency_simbol }}.{{ number_format($detalle->precio_compra, 2, '.', ',') }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.ingreso.deletemodal')
@endsection
