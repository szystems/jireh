@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="card mt-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4>Detalle de Comisión #{{ $comision->id }}</h4>
                <div class="btn-group">
                    <a href="{{ route('comisiones.pdf_individual', $comision->id) }}" class="btn btn-danger btn-sm" target="_blank">
                        <i class="bi bi-file-earmark-pdf"></i> Generar PDF
                    </a>
                    <a href="{{ url('comisiones') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-arrow-left"></i> Volver al listado
                    </a>
                </div>
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
                                    <td>
                                        @if($comision->tipo_comision == 'meta_venta' || $comision->tipo_comision == 'venta_meta')
                                            <span class="badge bg-primary">Meta de Ventas</span>
                                            @if($metaInfo)
                                                <br><small>
                                                    <span class="badge bg-{{ $metaInfo['color'] }} mt-1" title="Rango: {{ $metaInfo['rango'] }}">
                                                        🏆 {{ $metaInfo['nombre'] }}
                                                    </span>
                                                </small>
                                            @endif
                                        @elseif($comision->tipo_comision == 'mecanico')
                                            <span class="badge bg-info">Mecánico</span>
                                        @elseif($comision->tipo_comision == 'carwash')
                                            <span class="badge bg-secondary">Car Wash</span>
                                        @else
                                            <span class="badge bg-dark">{{ ucfirst($comision->tipo_comision) }}</span>
                                        @endif
                                    </td>
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
                                @if($metaInfo && ($comision->tipo_comision == 'meta_venta' || $comision->tipo_comision == 'venta_meta'))
                                <tr>
                                    <th>Meta Alcanzada:</th>
                                    <td>
                                        <span class="badge bg-{{ $metaInfo['color'] }} me-2">
                                            {{ $metaInfo['nombre'] }}
                                        </span>
                                        <small class="text-muted">
                                            ({{ $metaInfo['rango'] }} - {{ $comision->porcentaje }}% comisión)
                                        </small>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        @if ($comision->estado == 'pendiente')
                                            <span class="badge bg-warning">Pendiente de Pago</span>
                                            <small class="text-muted d-block mt-1">
                                                Pendiente: Q. {{ number_format($comision->montoPendiente(), 2) }}
                                            </small>
                                        @elseif ($comision->estado == 'pagado')
                                            <span class="badge bg-success">Pagado Completamente</span>
                                            @if($comision->pagos->count() > 0)
                                                @php $pago = $comision->pagos->first() @endphp
                                                <small class="text-muted d-block mt-1">
                                                    Pagado el {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}
                                                    @if($pago->lotePago)
                                                        - <a href="{{ route('lotes-pago.show', $pago->lotePago->id) }}" class="text-decoration-none">
                                                            Lote {{ $pago->lotePago->numero_lote }}
                                                        </a>
                                                    @endif
                                                </small>
                                            @endif
                                        @elseif ($comision->estado == 'cancelado')
                                            <span class="badge bg-danger">Cancelado</span>
                                        @endif
                                    </td>
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
                                    <td>{{ $config->currency_simbol }} {{ number_format($comision->detalleVenta->subtotal, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Información de Pago</h5>
                        </div>
                        <div class="card-body">
                            @if ($comision->estado == 'pagado')
                                @php $pago = $comision->pagos->first() @endphp
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Estado:</strong><br>
                                        <span class="badge bg-success">Pagado Completamente</span>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Fecha de Pago:</strong><br>
                                        {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Monto Pagado:</strong><br>
                                        {{ $config->currency_simbol }} {{ number_format($pago->monto, 2) }}
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Lote de Pago:</strong><br>
                                        @if($pago->lotePago)
                                            <a href="{{ route('lotes-pago.show', $pago->lotePago->id) }}" 
                                               class="btn btn-sm btn-outline-success">
                                                <i class="bi bi-file-earmark-check"></i> {{ $pago->lotePago->numero_lote }}
                                            </a>
                                        @else
                                            <span class="text-muted">No disponible</span>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($pago->observaciones)
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <strong>Observaciones:</strong><br>
                                        <div class="alert alert-info mb-0">
                                            {{ $pago->observaciones }}
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                            @elseif ($comision->estado == 'pendiente')
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Estado:</strong><br>
                                        <span class="badge bg-warning">Pendiente de Pago</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Monto Total:</strong><br>
                                        {{ $config->currency_simbol }} {{ number_format($comision->monto, 2) }}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Pendiente por Pagar:</strong><br>
                                        <span class="text-danger fw-bold">{{ $config->currency_simbol }} {{ number_format($comision->montoPendiente(), 2) }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-secondary mb-0">
                                    <strong>Estado:</strong> {{ ucfirst($comision->estado) }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
                
            @if ($comision->estado != 'pagado' && $comision->montoPendiente() > 0)
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="bi bi-file-earmark-plus"></i> Crear Lote de Pago Individual</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ url('comisiones/registrar-pago/'.$comision->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <!-- Información del Monto -->
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Comisión a pagar:</strong> #{{ $comision->id }}
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <strong>Monto total:</strong> {{ $config->currency_simbol }} {{ number_format($comision->montoPendiente(), 2) }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Campo oculto para el monto -->
                                <input type="hidden" name="monto" value="{{ number_format($comision->montoPendiente(), 2, '.', '') }}">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="fecha_pago" class="form-label">
                                                <i class="bi bi-calendar"></i> Fecha de Pago *
                                            </label>
                                            <input type="date" 
                                                   name="fecha_pago" 
                                                   id="fecha_pago" 
                                                   class="form-control @error('fecha_pago') is-invalid @enderror" 
                                                   value="{{ old('fecha_pago', date('Y-m-d')) }}"
                                                   required>
                                            @error('fecha_pago')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="metodo_pago" class="form-label">
                                                <i class="bi bi-credit-card"></i> Método de Pago *
                                            </label>
                                            <select name="metodo_pago" 
                                                    id="metodo_pago" 
                                                    class="form-control @error('metodo_pago') is-invalid @enderror"
                                                    required>
                                                <option value="">Seleccionar método...</option>
                                                <option value="efectivo" {{ old('metodo_pago') == 'efectivo' ? 'selected' : '' }}>
                                                    Efectivo
                                                </option>
                                                <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>
                                                    Transferencia Bancaria
                                                </option>
                                                <option value="cheque" {{ old('metodo_pago') == 'cheque' ? 'selected' : '' }}>
                                                    Cheque
                                                </option>
                                            </select>
                                            @error('metodo_pago')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="referencia" class="form-label">
                                                <i class="bi bi-hash"></i> Referencia
                                            </label>
                                            <input type="text" 
                                                   name="referencia" 
                                                   id="referencia" 
                                                   class="form-control @error('referencia') is-invalid @enderror" 
                                                   value="{{ old('referencia') }}"
                                                   placeholder="Número de transferencia, cheque, etc.">
                                            @error('referencia')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="comprobante_imagen" class="form-label">
                                                <i class="bi bi-file-earmark-image"></i> Comprobante de Pago
                                            </label>
                                            <input type="file" 
                                                   name="comprobante_imagen" 
                                                   id="comprobante_imagen" 
                                                   class="form-control @error('comprobante_imagen') is-invalid @enderror"
                                                   accept="image/*">
                                            <small class="form-text text-muted">
                                                Formatos permitidos: JPG, PNG, GIF. Máximo 2MB.
                                            </small>
                                            @error('comprobante_imagen')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">
                                        <i class="bi bi-chat-text"></i> Observaciones
                                    </label>
                                    <textarea name="observaciones" 
                                              id="observaciones" 
                                              class="form-control @error('observaciones') is-invalid @enderror" 
                                              rows="3"
                                              placeholder="Observaciones adicionales sobre el pago...">{{ old('observaciones') }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="bi bi-file-earmark-plus"></i> Crear Lote y Registrar Pago
                                    </button>
                                </div>
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
