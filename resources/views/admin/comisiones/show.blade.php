@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="card mt-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4>Detalle de Comisión #{{ $comision->id }}</h4>
                <a href="{{ url('comisiones') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i> Volver al listado
                </a>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Información de la Comisión</h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th width="30%">ID:</th>
                                    <td>{{ $comision->id }}</td>
                                </tr>
                                <tr>
                                    <th>Tipo:</th>
                                    <td>{{ ucfirst($comision->tipo_comision) }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha:</th>
                                    <td>{{ \Carbon\Carbon::parse($comision->fecha_calculo)->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Monto:</th>
                                    <td>Q. {{ number_format($comision->monto, 2) }}</td>
                                </tr>
                                @if($comision->porcentaje)
                                <tr>
                                    <th>Porcentaje:</th>
                                    <td>{{ $comision->porcentaje }}%</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        @if ($comision->estado == 'pendiente')
                                            <span class="badge bg-warning">Pendiente</span>
                                        @elseif ($comision->estado == 'pagado')
                                            <span class="badge bg-success">Pagado</span>
                                        @elseif ($comision->estado == 'cancelado')
                                            <span class="badge bg-danger">Cancelado</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Monto Pagado:</th>
                                    <td>Q. {{ number_format($comision->montoPagado(), 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Monto Pendiente:</th>
                                    <td>Q. {{ number_format($comision->montoPendiente(), 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Destinatario de la Comisión</h5>
                        </div>
                        <div class="card-body">
                            @if ($comision->commissionable_type == 'App\Models\Trabajador')
                                <table class="table">
                                    <tr>
                                        <th width="30%">Tipo:</th>
                                        <td><span class="badge bg-secondary">Trabajador</span></td>
                                    </tr>
                                    <tr>
                                        <th>Nombre:</th>
                                        <td>{{ $comision->commissionable->nombre }} {{ $comision->commissionable->apellido }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tipo:</th>
                                        <td>{{ $comision->commissionable->tipoTrabajador ? $comision->commissionable->tipoTrabajador->nombre : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Teléfono:</th>
                                        <td>{{ $comision->commissionable->telefono }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ver Perfil:</th>
                                        <td>
                                            <a href="{{ url('show-trabajador/'.$comision->commissionable->id) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-person"></i> Ver Trabajador
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            @elseif ($comision->commissionable_type == 'App\Models\User')
                                <table class="table">
                                    <tr>
                                        <th width="30%">Tipo:</th>
                                        <td><span class="badge bg-primary">Vendedor</span></td>
                                    </tr>
                                    <tr>
                                        <th>Nombre:</th>
                                        <td>{{ $comision->commissionable->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>{{ $comision->commissionable->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Ver Perfil:</th>
                                        <td>
                                            <a href="{{ url('show-user/'.$comision->commissionable->id) }}" class="btn btn-sm btn-info">
                                                <i class="bi bi-person"></i> Ver Usuario
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                @if ($comision->venta)
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Información de la Venta</h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th width="30%">No. Venta:</th>
                                    <td>{{ $comision->venta->id }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha:</th>
                                    <td>{{ \Carbon\Carbon::parse($comision->venta->fecha_hora)->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Cliente:</th>
                                    <td>{{ $comision->venta->cliente->nombre ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Total:</th>
                                    <td>Q. {{ number_format($comision->venta->total, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Ver Venta:</th>
                                    <td>
                                        <a href="{{ url('show-venta/'.$comision->venta->id) }}" class="btn btn-sm btn-success">
                                            <i class="bi bi-receipt"></i> Ver Venta
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
                
                @if ($comision->detalleVenta)
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Detalle del Servicio/Producto</h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th width="30%">Artículo:</th>
                                    <td>{{ $comision->detalleVenta->articulo->nombre ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Cantidad:</th>
                                    <td>{{ $comision->detalleVenta->cantidad }}</td>
                                </tr>
                                <tr>
                                    <th>Precio:</th>
                                    <td>Q. {{ number_format($comision->detalleVenta->precio_venta, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Descuento:</th>
                                    <td>{{ $comision->detalleVenta->descuento }}%</td>
                                </tr>
                                <tr>
                                    <th>Subtotal:</th>
                                    <td>Q. {{ number_format($comision->detalleVenta->subtotal, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Historial de Pagos</h5>
                        </div>
                        <div class="card-body">
                            @if ($comision->pagos->count() > 0)
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha</th>
                                            <th>Monto</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($comision->pagos as $pago)
                                            <tr>
                                                <td>{{ $pago->id }}</td>
                                                <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                                                <td>Q. {{ number_format($pago->monto, 2) }}</td>
                                                <td>{{ $pago->observaciones }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2">Total:</th>
                                            <th colspan="2">Q. {{ number_format($comision->montoPagado(), 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            @else
                                <div class="alert alert-info">No hay pagos registrados para esta comisión.</div>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if ($comision->estado != 'pagado' && $comision->montoPendiente() > 0)
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Registrar Pago</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('comisiones/registrar-pago/'.$comision->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="monto" class="form-label">Monto a Pagar</label>
                                    <input type="number" step="0.01" min="0.01" max="{{ $comision->montoPendiente() }}" name="monto" id="monto" class="form-control" value="{{ $comision->montoPendiente() }}" required>
                                    <div class="form-text">Monto pendiente: Q. {{ number_format($comision->montoPendiente(), 2) }}</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="fecha_pago" class="form-label">Fecha de Pago</label>
                                    <input type="date" name="fecha_pago" id="fecha_pago" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <textarea name="observaciones" id="observaciones" class="form-control" rows="3"></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-cash"></i> Registrar Pago
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
