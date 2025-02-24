<!-- filepath: resources/views/ingresos/index.blade.php -->
@extends('layouts.admin')
@section('content')
    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-cart-plus"></i>
                </div>
                <div class="page-title">
                    <h5>Lista de Ingresos</h5>
                </div>
            </div>
        </div>
        <div class="content-wrapper">
            @include('admin.ingreso.search')
            <div class="filters mb-3">
                <strong>Filtros utilizados:</strong>
                <span>Fecha Desde: {{ \Carbon\Carbon::parse(request('fecha_desde', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d')))->format('d/m/Y') }}</span>,
                <span>Fecha Hasta: {{ \Carbon\Carbon::parse(request('fecha_hasta', \Carbon\Carbon::now()->format('Y-m-d')))->format('d/m/Y') }}</span>
                @if(request('numero_factura'))
                    , <span>Número de Factura: {{ request('numero_factura') }}</span>
                @endif
                @if(request('proveedor') && $proveedores->find(request('proveedor')))
                    , <span>Proveedor: {{ $proveedores->find(request('proveedor'))->nombre }}</span>
                @endif
                @if(request('tipo_compra'))
                    , <span>Tipo de Compra: {{ request('tipo_compra') }}</span>
                @endif
                @if(request('usuario') && $usuarios->find(request('usuario')))
                    , <span>Usuario: {{ $usuarios->find(request('usuario'))->name }}</span>
                @endif
            </div>
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <a href="{{ url('add-ingreso')  }}" class="btn btn-primary"><i class="bi bi-plus-square"></i> Agregar</a>
                                <div>
                                    <a href="{{ route('ingresos.export.pdf', request()->query()) }}" class="btn btn-danger"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                                    {{-- <a href="{{ route('ingresos.export.excel', request()->query()) }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Excel</a> --}}
                                </div>
                            </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Acciones</th>
                                        <th>Fecha</th>
                                        <th>Número de Factura</th>
                                        <th>Proveedor</th>
                                        <th class="text-center">Tipo de Compra</th>
                                        <th>Usuario</th>
                                        <th>Detalles</th>
                                        <th class="text-center">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ingresos as $ingreso)
                                        <tr>
                                            <td>
                                                <a href="{{ url('show-ingreso/'.$ingreso->id)  }}" class="btn btn-info btn-sm"><i class="bi bi-eye-fill"></i></a>
                                                <a href="{{ url('edit-ingreso/'.$ingreso->id)  }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil-fill "></i></a>
                                                <a type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $ingreso->id }}">
                                                    <i class="bi bi-trash-fill"></i>
                                                </a>
                                            </td>
                                            <td><a href="{{ url('show-ingreso/'.$ingreso->id)  }}" class=" text-primary">{{ \Carbon\Carbon::parse($ingreso->fecha)->format('d/m/Y') }}</a></td>
                                            <td><a href="{{ url('show-ingreso/'.$ingreso->id)  }}">{{ $ingreso->numero_factura }}</a></td>
                                            <td><a href="{{ url('show-proveedor/'.$ingreso->proveedor_id) }}" class=" text-primary">{{ optional($ingreso->proveedor)->nombre }}</a></td>
                                            <td class="text-center">{{ $ingreso->tipo_compra }}</td>
                                            <td >{{ optional($ingreso->usuario)->name }}</td>
                                            <td>
                                                <table class="table table-sm table-bordered table-striped" style="font-size: 12px;">
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
                                                                <td class="text-primary">{{ optional($detalle->articulo)->nombre }}</td>
                                                                <td class="text-center">{{ $detalle->cantidad }}</td>
                                                                <td class="text-end">{{ $config->currency_simbol }}.{{ number_format($detalle->precio_compra, 2, '.', ',') }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </td>
                                            <td class="text-end text-warning">{{ $config->currency_simbol }}.{{ number_format($ingreso->detalles->sum('precio_compra'), 2, '.', ',') }}</td>

                                        </tr>
                                        @include('admin.ingreso.deletemodal')
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7" class="text-end"><strong>Total Gastado:</strong></td>
                                        <td class="text-end text-warning"><strong>{{ $config->currency_simbol }}.{{ number_format($ingresos->sum(function($ingreso) { return $ingreso->detalles->sum('precio_compra'); }), 2, '.', ',') }}</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
