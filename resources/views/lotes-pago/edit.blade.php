@extends('layouts.admin')

@section('title', 'Editar Lote de Pago - ' . $lotePago->numero_lote)

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
                    <li class="breadcrumb-item">
                        <a href="{{ route('lotes-pago.show', $lotePago) }}">{{ $lotePago->numero_lote }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Editar
                    </li>
                </ol>
            </nav>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h6>Por favor corrige los siguientes errores:</h6>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('lotes-pago.update', $lotePago) }}" method="POST" enctype="multipart/form-data" id="form-editar-lote">
                @csrf
                @method('PUT')

                <!-- Información del Lote -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-pencil"></i> 
                            Editar Lote de Pago: {{ $lotePago->numero_lote }}
                        </h5>
                        <span class="badge bg-info">{{ ucfirst($lotePago->estado) }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="numero_lote" class="form-label">
                                        <i class="bi bi-upc"></i> Número de Lote
                                    </label>
                                    <input type="text" 
                                           name="numero_lote" 
                                           id="numero_lote" 
                                           class="form-control" 
                                           value="{{ $lotePago->numero_lote }}"
                                           readonly>
                                    <small class="form-text text-muted">
                                        El número de lote no se puede modificar.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_pago" class="form-label">
                                        <i class="bi bi-calendar"></i> Fecha de Pago *
                                    </label>
                                    <input type="date" 
                                           name="fecha_pago" 
                                           id="fecha_pago" 
                                           class="form-control @error('fecha_pago') is-invalid @enderror" 
                                           value="{{ old('fecha_pago', $lotePago->fecha_pago->format('Y-m-d')) }}"
                                           required>
                                    @error('fecha_pago')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
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
                                        <option value="efectivo" {{ old('metodo_pago', $lotePago->metodo_pago) == 'efectivo' ? 'selected' : '' }}>
                                            Efectivo
                                        </option>
                                        <option value="transferencia" {{ old('metodo_pago', $lotePago->metodo_pago) == 'transferencia' ? 'selected' : '' }}>
                                            Transferencia Bancaria
                                        </option>
                                        <option value="cheque" {{ old('metodo_pago', $lotePago->metodo_pago) == 'cheque' ? 'selected' : '' }}>
                                            Cheque
                                        </option>
                                    </select>
                                    @error('metodo_pago')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="referencia" class="form-label">
                                        <i class="bi bi-hash"></i> Referencia
                                    </label>
                                    <input type="text" 
                                           name="referencia" 
                                           id="referencia" 
                                           class="form-control @error('referencia') is-invalid @enderror" 
                                           value="{{ old('referencia', $lotePago->referencia) }}"
                                           placeholder="Número de transferencia, cheque, etc.">
                                    @error('referencia')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
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
                                        Formatos permitidos: JPG, PNG, GIF. Máximo 2MB. Déjalo vacío para mantener el actual.
                                    </small>
                                    @error('comprobante_imagen')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                @if($lotePago->comprobante_imagen)
                                    <div class="mb-3">
                                        <label class="form-label">Comprobante Actual</label>
                                        <div>
                                            <a href="{{ asset('uploads/comprobantes/' . $lotePago->comprobante_imagen) }}" 
                                               target="_blank" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-file-earmark-image"></i> Ver Comprobante Actual
                                            </a>
                                        </div>
                                        <small class="form-text text-muted">
                                            Se mantendrá este comprobante si no subes uno nuevo.
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">
                                        <i class="bi bi-chat-text"></i> Observaciones
                                    </label>
                                    <textarea name="observaciones" 
                                              id="observaciones" 
                                              class="form-control @error('observaciones') is-invalid @enderror" 
                                              rows="3"
                                              placeholder="Observaciones adicionales sobre el lote de pago...">{{ old('observaciones', $lotePago->observaciones) }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Información de solo lectura -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Cantidad de Comisiones</label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ $lotePago->cantidad_comisiones }}"
                                           readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Monto Total</label>
                                    <input type="text" 
                                           class="form-control text-success" 
                                           value="{{ $config->currency_simbol }}{{ number_format($lotePago->monto_total, 2) }}"
                                           readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Usuario Creador</label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ $lotePago->usuario->name ?? 'N/A' }}"
                                           readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Fecha Creación</label>
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ $lotePago->created_at->format('d/m/Y H:i') }}"
                                           readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comisiones Incluidas (Solo lectura) -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul"></i> 
                            Comisiones Incluidas en este Lote
                        </h5>
                        <small class="text-muted">
                            Las comisiones incluidas en el lote no se pueden modificar. Solo se puede editar la información general del lote.
                        </small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Trabajador</th>
                                        <th>Venta</th>
                                        <th>Cliente</th>
                                        <th>Tipo</th>
                                        <th>Monto</th>
                                        <th>Estado</th>
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
                                                @else
                                                    @if($pago->comision->tipo_comision == 'meta_venta')
                                                        <span class="text-muted">
                                                            <i class="bi bi-trophy"></i> Meta de Ventas
                                                        </span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if($pago->comision->venta && $pago->comision->venta->cliente)
                                                    {{ $pago->comision->venta->cliente->nombre }}
                                                @else
                                                    @if($pago->comision->tipo_comision == 'meta_venta')
                                                        <span class="text-muted">Múltiples clientes</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
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
                                        <th colspan="5" class="text-end">Total del Lote:</th>
                                        <th class="text-success">
                                            {{ $config->currency_simbol }}{{ number_format($lotePago->pagosComisiones->sum('monto'), 2) }}
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('lotes-pago.show', $lotePago) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            
                            <button type="submit" class="btn btn-warning" id="btn-actualizar-lote">
                                <i class="bi bi-save"></i> Actualizar Lote de Pago
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario
    document.getElementById('form-editar-lote').addEventListener('submit', function(e) {
        if (!confirm('¿Está seguro de actualizar la información de este lote de pago? Los cambios se aplicarán a todos los pagos asociados.')) {
            e.preventDefault();
            return false;
        }
    });

    // Preview del archivo seleccionado
    document.getElementById('comprobante_imagen').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
            
            // Crear o actualizar mensaje de preview
            let preview = document.getElementById('file-preview');
            if (!preview) {
                preview = document.createElement('small');
                preview.id = 'file-preview';
                preview.className = 'form-text text-info';
                this.parentNode.appendChild(preview);
            }
            
            preview.innerHTML = `<i class="bi bi-file-earmark-image"></i> Archivo seleccionado: ${fileName} (${fileSize} MB)`;
            
            // Validar tamaño
            if (file.size > 2 * 1024 * 1024) { // 2MB
                preview.className = 'form-text text-danger';
                preview.innerHTML = `<i class="bi bi-exclamation-triangle"></i> El archivo es muy grande (${fileSize} MB). Máximo permitido: 2MB`;
                this.value = '';
            }
        }
    });
});
</script>
@endpush
@endsection
