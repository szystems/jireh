@extends('layouts.admin')

@section('content')
    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <div class="page-title">
                    <h5>Ventas por departamento</h5>
                </div>
            </div>
        </div>

        <div class="content-wrapper">
            <div class="alert alert-info">
                Aquí se ve quién vendió y en qué departamento. El monto es el total de las facturas activas.
                Las comisiones de mecánico, car wash y metas de vendedor se siguen calculando como hasta ahora; este reporte no crea tipos de comisión nuevos.
                Las facturas anteriores siguen en Car Wash o Centro de Servicios. Autos, Accesorios y Pintura aparecen cuando la factura se guarda con ese departamento.
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('ventas.por_departamento') }}" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label" for="fecha_desde">Desde</label>
                            <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="{{ $desde }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="fecha_hasta">Hasta</label>
                            <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="{{ $hasta }}">
                        </div>
                        @if(Auth::user()->role_as != 1)
                            <div class="col-md-3">
                                <label class="form-label" for="usuario_id">Vendedor</label>
                                <select class="form-control" id="usuario_id" name="usuario_id">
                                    <option value="">Todos</option>
                                    @foreach($usuarios as $usuario)
                                        <option value="{{ $usuario->id }}" {{ (string) $usuarioId === (string) $usuario->id ? 'selected' : '' }}>{{ $usuario->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Ver ventas
                            </button>
                            <a href="{{ route('admin.ventas.index') }}" class="btn btn-outline-secondary">Lista de facturas</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white">
                        {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}
                        al
                        {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}
                        · {{ $config->currency_simbol ?? 'Q' }}{{ number_format($granTotal, 2) }}
                    </h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Vendedor</th>
                                @foreach($departamentos as $etiqueta)
                                    <th class="text-end">{{ $etiqueta }}</th>
                                @endforeach
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vendedores as $vendedor)
                                <tr>
                                    <td>{{ $vendedor['nombre'] }}</td>
                                    @foreach($departamentos as $clave => $etiqueta)
                                        @php
                                            $monto = $vendedor['totales'][$clave] ?? 0;
                                            $facturas = $vendedor['facturas'][$clave] ?? 0;
                                        @endphp
                                        <td class="text-end">
                                            @if($monto > 0)
                                                {{ $config->currency_simbol ?? 'Q' }}{{ number_format($monto, 2) }}
                                                <br><small class="text-muted">{{ $facturas }} {{ $facturas === 1 ? 'factura' : 'facturas' }}</small>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="text-end"><strong>{{ $config->currency_simbol ?? 'Q' }}{{ number_format($vendedor['total'], 2) }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($departamentos) + 2 }}" class="text-center text-muted">No hay facturas activas en este período.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($vendedores))
                            <tfoot>
                                <tr>
                                    <th>Total</th>
                                    @foreach($departamentos as $clave => $etiqueta)
                                        <th class="text-end">{{ $config->currency_simbol ?? 'Q' }}{{ number_format($totalesDepartamento[$clave] ?? 0, 2) }}</th>
                                    @endforeach
                                    <th class="text-end">{{ $config->currency_simbol ?? 'Q' }}{{ number_format($granTotal, 2) }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
