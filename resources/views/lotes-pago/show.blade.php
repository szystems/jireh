@extends('layouts.admin')

@section('title', 'Detalle Lote de Pago - ' . $lotePago->numero_lote)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Navegación -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('lotes-pago.index') }}">Lotes de Pago</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $lotePago->numero_lote }}
                    </li>
                </ol>
            </nav>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Información del Lote -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-file-earmark-plus"></i> 
                        Lote de Pago: {{ $lotePago->numero_lote }}
                    </h4>
                    <div class="btn-group">
                        <a href="{{ route('lotes-pago.pdf.individual', $lotePago) }}" class="btn btn-danger" target="_blank">
                            <i class="bi bi-file-earmark-pdf"></i> Generar PDF
                        </a>
                        @if($lotePago->estado != 'anulado')
                            <a href="{{ route('lotes-pago.edit', $lotePago) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        @endif
                        <a href="{{ route('lotes-pago.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Número de Lote:</strong></td>
                                    <td>{{ $lotePago->numero_lote }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha de Pago:</strong></td>
                                    <td>{{ $lotePago->fecha_pago->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Método de Pago:</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($lotePago->metodo_pago) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Referencia:</strong></td>
                                    <td>{{ $lotePago->referencia ?: 'Sin referencia' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado:</strong></td>
                                    <td>
                                        @if($lotePago->estado == 'procesando')
                                            <span class="badge bg-warning">Procesando</span>
                                        @elseif($lotePago->estado == 'completado')
                                            <span class="badge bg-success">Completado</span>
                                        @else
                                            <span class="badge bg-danger">Anulado</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Cantidad de Comisiones:</strong></td>
                                    <td>{{ $lotePago->cantidad_comisiones }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Monto Total:</strong></td>
                                    <td>
                                        <span class="text-success h5">
                                            {{ $config->currency_simbol }}{{ number_format($lotePago->monto_total, 2) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Usuario Creador:</strong></td>
                                    <td>{{ $lotePago->usuario->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha de Creación:</strong></td>
                                    <td>{{ $lotePago->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                @if($lotePago->observaciones)
                                <tr>
                                    <td><strong>Observaciones:</strong></td>
                                    <td>{{ $lotePago->observaciones }}</td>
                                </tr>
                                @endif
                                @if($lotePago->comprobante_imagen)
                                <tr>
                                    <td><strong>Comprobante:</strong></td>
                                    <td>
                                        <a href="{{ asset('uploads/comprobantes/' . $lotePago->comprobante_imagen) }}" 
                                           target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-file-earmark-image"></i> Ver Comprobante
                                        </a>
                                    </td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalle de Comisiones Pagadas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul"></i> 
                        Comisiones Incluidas en este Lote
                    </h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID Comisión</th>
                                    <th>Trabajador</th>
                                    <th>Venta</th>
                                    <th>Tipo</th>
                                    <th>Monto Comisión</th>
                                    <th>Fecha Venta</th>
                                    <th>Estado Pago</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lotePago->pagosComisiones as $pago)
                                    <tr>
                                        <td>
                                            <strong>#{{ $pago->comision->id }}</strong>
                                        </td>
                                        <td>
                                            @if($pago->comision->commissionable_type == 'App\Models\User')
                                                {{ $pago->comision->commissionable->name ?? 'Usuario eliminado' }}
                                                <small class="text-muted">(Vendedor)</small>
                                            @else
                                                {{ $pago->comision->commissionable->nombre ?? 'N/A' }} 
                                                {{ $pago->comision->commissionable->apellido ?? '' }}
                                                <small class="text-muted">(Trabajador)</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($pago->comision->venta_id)
                                                <a href="{{ route('ventas.show', $pago->comision->venta_id) }}" target="_blank" class="text-decoration-none">
                                                    <i class="bi bi-receipt"></i> Venta #{{ $pago->comision->venta->id }}
                                                </a>
                                                <br>
                                                <small class="text-muted">
                                                    Cliente: {{ $pago->comision->venta->cliente->nombre ?? 'N/A' }}
                                                </small>
                                            @else
                                                @if($pago->comision->tipo_comision == 'meta_venta')
                                                    @php
                                                        $resumenMeta = $pago->comision->info_meta_resumen;
                                                        $metaInfo = $resumenMeta['meta_info'] ?? null;
                                                    @endphp
                                                    
                                                    <div class="meta-ventas-resumen">
                                                        <div class="d-flex align-items-center mb-1">
                                                            <i class="bi bi-trophy text-warning me-1"></i>
                                                            <strong>Meta de Ventas - {{ $metaInfo['nombre'] ?? 'N/A' }}</strong>
                                                        </div>
                                                        
                                                        <div class="row text-sm">
                                                            <div class="col-12">
                                                                <small class="text-muted">
                                                                    <i class="bi bi-calendar-month"></i> 
                                                                    Período: {{ $resumenMeta['periodo'] ?? 'N/A' }}
                                                                </small>
                                                            </div>
                                                            <div class="col-12">
                                                                <small class="text-muted">
                                                                    <i class="bi bi-cart-check"></i> 
                                                                    {{ $resumenMeta['cantidad_ventas'] ?? 0 }} ventas - 
                                                                    {{ $config->currency_simbol }}{{ number_format($resumenMeta['total_vendido'] ?? 0, 2) }}
                                                                </small>
                                                            </div>
                                                            @if(isset($resumenMeta['fecha_inicio']) && $resumenMeta['cantidad_ventas'] > 0)
                                                            <div class="col-12">
                                                                <small class="text-muted">
                                                                    <i class="bi bi-calendar-range"></i> 
                                                                    {{ $resumenMeta['fecha_inicio'] }} - {{ $resumenMeta['fecha_fin'] }}
                                                                </small>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        
                                                        @if($resumenMeta['cantidad_ventas'] > 0)
                                                        <div class="mt-2">
                                                            <button class="btn btn-sm btn-outline-primary" 
                                                                    onclick="verDetallesMeta({{ $pago->comision->id }})"
                                                                    title="Ver todas las ventas que conforman esta meta">
                                                                <i class="bi bi-list-ul"></i> Ver {{ $resumenMeta['cantidad_ventas'] }} ventas
                                                            </button>
                                                        </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                    <br>
                                                    <small class="text-muted">N/A</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $badgeColor = 'secondary';
                                                switch($pago->comision->tipo_comision) {
                                                    case 'meta_venta': 
                                                        $badgeColor = 'primary'; 
                                                        break;
                                                    case 'mecanico': 
                                                        $badgeColor = 'warning'; 
                                                        break;
                                                    case 'carwash': 
                                                        $badgeColor = 'info'; 
                                                        break;
                                                }

                                                // Información de meta para comisiones meta_venta
                                                $metaInfo = null;
                                                if ($pago->comision->tipo_comision === 'meta_venta') {
                                                    $porcentaje = $pago->comision->porcentaje;
                                                    switch($porcentaje) {
                                                        case 3:
                                                            $metaInfo = [
                                                                'nombre' => 'Bronce',
                                                                'color' => 'warning',
                                                                'rango' => '$1K - $2.5K'
                                                            ];
                                                            break;
                                                        case 5:
                                                            $metaInfo = [
                                                                'nombre' => 'Plata', 
                                                                'color' => 'secondary',
                                                                'rango' => '$2.5K - $5K'
                                                            ];
                                                            break;
                                                        case 8:
                                                            $metaInfo = [
                                                                'nombre' => 'Oro',
                                                                'color' => 'success', 
                                                                'rango' => '$5K+'
                                                            ];
                                                            break;
                                                        default:
                                                            $metaInfo = [
                                                                'nombre' => 'Desconocida',
                                                                'color' => 'dark',
                                                                'rango' => 'N/A'
                                                            ];
                                                    }
                                                }
                                            @endphp
                                            <span class="badge bg-{{ $badgeColor }}">
                                                {{ ucfirst(str_replace('_', ' ', $pago->comision->tipo_comision ?? 'N/A')) }}
                                            </span>
                                            @if($metaInfo)
                                                <br><small><span class="badge bg-{{ $metaInfo['color'] }} mt-1" title="Meta alcanzada: {{ $metaInfo['rango'] }}">
                                                    {{ $metaInfo['nombre'] }}
                                                </span></small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong class="text-success">
                                                {{ $config->currency_simbol }}{{ number_format($pago->monto, 2) }}
                                            </strong>
                                        </td>
                                        <td>
                                            @if($pago->comision->venta && $pago->comision->venta->fecha)
                                                {{ $pago->comision->venta->fecha->format('d/m/Y') }}
                                            @else
                                                @if($pago->comision->tipo_comision == 'meta_venta')
                                                    <span class="text-muted">
                                                        Período: {{ $pago->comision->fecha_calculo->format('m/Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if($pago->estado == 'completado')
                                                <span class="badge bg-success">Completado</span>
                                            @elseif($pago->estado == 'anulado')
                                                <span class="badge bg-danger">Anulado</span>
                                            @else
                                                <span class="badge bg-warning">{{ ucfirst($pago->estado) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end">Total del Lote:</th>
                                    <th class="text-success">
                                        {{ $config->currency_simbol }}{{ number_format($lotePago->pagosComisiones->sum('monto'), 2) }}
                                    </th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Acciones Adicionales -->
            @if($lotePago->estado != 'anulado')
                <div class="card mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="bi bi-exclamation-triangle"></i> 
                            Acciones de Administración
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Este lote está {{ $lotePago->estado }}. Puede editar la información del lote o anularlo si es necesario.
                            <strong>Anular el lote cancelará todos los pagos asociados.</strong>
                        </p>
                        <div class="btn-group">
                            <a href="{{ route('lotes-pago.edit', $lotePago) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Editar Lote
                            </a>
                            <form method="POST" action="{{ route('lotes-pago.destroy', $lotePago) }}" 
                                  style="display: inline;"
                                  onsubmit="return confirm('¿Está seguro de anular este lote de pago? Esta acción anulará todos los pagos asociados y no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-x-circle"></i> Anular Lote
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para detalles de meta de ventas -->
<div class="modal fade" id="modalDetallesMeta" tabindex="-1" aria-labelledby="modalDetallesMetaLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetallesMetaLabel">
                    <i class="bi bi-trophy"></i> Detalles de Meta de Ventas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="contenidoDetallesMeta">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando detalles de la meta...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function verDetallesMeta(comisionId) {
    console.log('Cargando detalles de meta para comisión:', comisionId);
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalDetallesMeta'));
    modal.show();
    
    // Cargar el contenido via AJAX
    fetch(`/comisiones/${comisionId}/detalles-meta`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('contenidoDetallesMeta').innerHTML = data.html;
            } else {
                document.getElementById('contenidoDetallesMeta').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> 
                        Error al cargar los detalles: ${data.message || 'Error desconocido'}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('contenidoDetallesMeta').innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> 
                    Error de conexión al cargar los detalles de la meta.
                </div>
            `;
        });
}
</script>
@endsection
