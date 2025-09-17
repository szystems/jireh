@extends('layouts.admin')
@section('content')
    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="page-title">
                    <h5>Detalle de Cotización - {{ $cotizacion->numero_cotizacion }}</h5>
                </div>
            </div>
        </div>
        <div class="content-wrapper">
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <a href="{{ route('admin.cotizaciones.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver a Cotizaciones
                    </a>
                    <br>
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                            <strong><i class="bi bi-check-circle"></i> ¡Éxito!</strong> {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="card mb-3">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center pb-3">
                            <h5 class="card-title mb-0 text-white">
                                Información de Cotización
                                @switch($cotizacion->estado)
                                    @case('Generado')
                                        <span class="badge bg-info ms-2">GENERADO</span>
                                        @break
                                    @case('Aprobado')
                                        <span class="badge bg-primary ms-2">APROBADO</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary ms-2">{{ strtoupper($cotizacion->estado) }}</span>
                                @endswitch
                                
                                <!-- Estado de vigencia automático -->
                                @if($cotizacion->estado === 'Generado')
                                    @if($cotizacion->esta_vigente)
                                        <span class="badge bg-success ms-1">VIGENTE</span>
                                    @else
                                        <span class="badge bg-danger ms-1">VENCIDA</span>
                                    @endif
                                @endif
                            </h5>
                            <div>
                                @if(($cotizacion->estado === 'Generado' && $cotizacion->esta_vigente) || $cotizacion->estado === 'Aprobado')
                                    <a href="{{ route('admin.cotizaciones.edit', $cotizacion->id) }}" class="btn btn-sm btn-warning me-1">
                                        <i class="bi bi-pencil-fill"></i> Editar
                                    </a>
                                @endif
                                <a href="{{ route('admin.cotizaciones.export.single.pdf', $cotizacion->id) }}" class="btn btn-sm btn-danger me-1" target="_blank">
                                    <i class="bi bi-file-pdf-fill"></i> PDF
                                </a>
                                @if($cotizacion->estado === 'Generado' && $cotizacion->esta_vigente)
                                    <button type="button" class="btn btn-sm btn-success me-1" onclick="cambiarEstado('{{ $cotizacion->id }}', 'Aprobado')">
                                        <i class="bi bi-check-circle"></i> Aprobar
                                    </button>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td><strong>Número de Cotización:</strong></td>
                                                    <td>{{ $cotizacion->numero_cotizacion }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Fecha de Cotización:</strong></td>
                                                    <td>{{ $cotizacion->fecha_cotizacion->format('d/m/Y') }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Fecha de Vencimiento:</strong></td>
                                                    <td>
                                                        {{ $cotizacion->fecha_vencimiento->format('d/m/Y') }}
                                                        @if($cotizacion->fecha_vencimiento->isPast() && $cotizacion->estado === 'vigente')
                                                            <span class="badge bg-danger ms-1">¡Vencida!</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Estado:</strong></td>
                                                    <td>
                                                        @switch($cotizacion->estado)
                                                            @case('Generado')
                                                                <span class="badge bg-info">Generado</span>
                                                                @break
                                                            @case('Aprobado')
                                                                <span class="badge bg-primary">Aprobado</span>
                                                                @break
                                                            @default
                                                                <span class="badge bg-secondary">{{ ucfirst($cotizacion->estado) }}</span>
                                                        @endswitch
                                                    </td>
                                                </tr>
                                                @if($cotizacion->estado === 'Generado')
                                                <tr>
                                                    <td><strong>Vigencia:</strong></td>
                                                    <td>
                                                        @if($cotizacion->esta_vigente)
                                                            <span class="badge bg-success">Vigente</span>
                                                            <small class="text-muted ms-2">
                                                                ({{ \Carbon\Carbon::now()->diffInDays($cotizacion->fecha_vencimiento) }} días restantes)
                                                            </small>
                                                        @else
                                                            <span class="badge bg-danger">Vencida</span>
                                                            <small class="text-muted ms-2">
                                                                (Venció hace {{ \Carbon\Carbon::now()->diffInDays($cotizacion->fecha_vencimiento) }} días)
                                                            </small>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tbody>
                                                <tr>
                                                    <td><strong>Cliente:</strong></td>
                                                    <td>{{ $cotizacion->cliente->nombre }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Email:</strong></td>
                                                    <td>{{ $cotizacion->cliente->email ?? 'No especificado' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Teléfono:</strong></td>
                                                    <td>{{ $cotizacion->cliente->telefono ?? 'No especificado' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Vehículo:</strong></td>
                                                    <td>{{ $cotizacion->vehiculo->placa }} - {{ $cotizacion->vehiculo->marca }} {{ $cotizacion->vehiculo->modelo }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles de la cotización -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Detalles de la Cotización</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Artículo</th>
                                            <th>Cantidad</th>
                                            <th>Precio Unitario</th>
                                            <th>Descuento</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalGeneral = 0;
                                            $totalDescuentos = 0;
                                        @endphp
                                        @foreach($cotizacion->detalleCotizaciones as $detalle)
                                            @php
                                                // Usar el sub_total que ya está calculado correctamente
                                                $subtotalConDescuento = $detalle->sub_total;
                                                $totalGeneral += $subtotalConDescuento;
                                                
                                                // Calcular descuento solo para mostrar
                                                $montoDescuento = 0;
                                                if ($detalle->descuento_id && $detalle->descuento) {
                                                    $subtotalSinDescuento = $detalle->cantidad * $detalle->precio_venta;
                                                    $montoDescuento = $subtotalSinDescuento * ($detalle->descuento->porcentaje_descuento / 100);
                                                    $totalDescuentos += $montoDescuento;
                                                }
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $detalle->articulo->codigo }}</strong><br>
                                                    {{ $detalle->articulo->nombre }}
                                                </td>
                                                <td>{{ $detalle->cantidad }} {{ $detalle->articulo->unidad->abreviatura }}</td>
                                                <td>{{ $config->currency_simbol }}{{ number_format($detalle->precio_venta, 2) }}</td>
                                                <td>
                                                    @if($detalle->descuento)
                                                        {{ $detalle->descuento->nombre }}<br>
                                                        <small class="text-muted">{{ $detalle->descuento->porcentaje_descuento }}% - {{ $config->currency_simbol }}{{ number_format($montoDescuento, 2) }}</small>
                                                    @else
                                                        Sin descuento
                                                    @endif
                                                </td>
                                                <td>{{ $config->currency_simbol }}{{ number_format($subtotalConDescuento, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-dark">
                                        @if($totalDescuentos > 0)
                                            <tr>
                                                <th colspan="4" class="text-end">Subtotal sin descuentos:</th>
                                                <th>{{ $config->currency_simbol }}{{ number_format($totalGeneral + $totalDescuentos, 2) }}</th>
                                            </tr>
                                            <tr>
                                                <th colspan="4" class="text-end">Total descuentos:</th>
                                                <th class="text-danger">-{{ $config->currency_simbol }}{{ number_format($totalDescuentos, 2) }}</th>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th colspan="4" class="text-end">Total General:</th>
                                            <th class="text-success">{{ $config->currency_simbol }}{{ number_format($totalGeneral, 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($cotizacion->estado === 'convertida' && $cotizacion->venta_convertida_id)
                        <div class="card mt-3">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0 text-white">Venta Generada</h5>
                            </div>
                            <div class="card-body">
                                <p>Esta cotización fue convertida exitosamente a una venta.</p>
                                <a href="{{ route('ventas.show', $cotizacion->venta_convertida_id) }}" class="btn btn-info">
                                    <i class="bi bi-eye"></i> Ver Venta Generada
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function cambiarEstado(cotizacionId, nuevoEstado) {
            let mensaje = '¿Está seguro de cambiar el estado de esta cotización?';
            if (nuevoEstado === 'Aprobado') {
                mensaje = '¿Está seguro de aprobar esta cotización?';
            } else if (nuevoEstado === 'Generado') {
                mensaje = '¿Está seguro de regenerar esta cotización?';
            }
            
            if (confirm(mensaje)) {
                $.ajax({
                    url: '{{ route("admin.cotizaciones.cambiar.estado", ":id") }}'.replace(':id', cotizacionId),
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        estado: nuevoEstado
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error completo:', xhr);
                        let errorMessage = 'Error desconocido';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            errorMessage = xhr.responseText;
                        } else {
                            errorMessage = error;
                        }
                        
                        alert('Error al cambiar el estado: ' + errorMessage);
                    }
                });
            }
        }
    </script>
@endsection