@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="card mt-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="bi bi-eye"></i> Detalles de Meta de Ventas</h4>
                <div>
                    <a href="{{ route('metas-ventas.edit', $metaVenta) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="{{ route('metas-ventas.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-info-circle"></i> Información de la Meta</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">ID:</th>
                                    <td>{{ $metaVenta->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nombre:</th>
                                    <td><strong>{{ $metaVenta->nombre }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Descripción:</th>
                                    <td>{{ $metaVenta->descripcion ?: 'Sin descripción' }}</td>
                                </tr>
                                <tr>
                                    <th>Período:</th>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($metaVenta->periodo) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td>
                                        @if($metaVenta->estado)
                                            <span class="badge bg-success">Activa</span>
                                        @else
                                            <span class="badge bg-danger">Inactiva</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Creada:</th>
                                    <td>{{ $metaVenta->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Actualizada:</th>
                                    <td>{{ $metaVenta->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-currency-dollar"></i> Configuración de Comisión</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <h6 class="text-muted">Rango de Montos</h6>
                                <h4 class="text-primary">{{ $metaVenta->rango_formateado }}</h4>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-muted">Porcentaje de Comisión</h6>
                                <h3 class="text-success">{{ $metaVenta->porcentaje_formateado }}</h3>
                            </div>

                            <hr>

                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="bi bi-calculator"></i> Simulador</h6>
                                    <div class="mb-2">
                                        <label for="simulador_monto" class="form-label">Monto de ventas:</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Q</span>
                                            <input type="number" id="simulador_monto" class="form-control" 
                                                   value="{{ ($metaVenta->monto_minimo + ($metaVenta->monto_maximo ?: ($metaVenta->monto_minimo + 10000))) / 2 }}" 
                                                   step="100" min="0">
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <strong>Comisión: <span id="comision_resultado" class="text-success">Q0.00</span></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-gear"></i> Acciones</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <form action="{{ route('metas-ventas.toggle-estado', $metaVenta) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-{{ $metaVenta->estado ? 'secondary' : 'success' }} w-100">
                                        <i class="bi bi-{{ $metaVenta->estado ? 'toggle-off' : 'toggle-on' }}"></i>
                                        {{ $metaVenta->estado ? 'Desactivar' : 'Activar' }} Meta
                                    </button>
                                </form>
                                
                                <form action="{{ route('metas-ventas.destroy', $metaVenta) }}" method="POST" 
                                      onsubmit="return confirm('¿Estás seguro de eliminar esta meta?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="bi bi-trash"></i> Eliminar Meta
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const simuladorMonto = document.getElementById('simulador_monto');
    const comisionResultado = document.getElementById('comision_resultado');
    const porcentajeComision = {{ $metaVenta->porcentaje_comision }};
    const montoMinimo = {{ $metaVenta->monto_minimo }};
    const montoMaximo = {{ $metaVenta->monto_maximo ?: 'null' }};
    
    function calcularComision() {
        const monto = parseFloat(simuladorMonto.value) || 0;
        
        // Verificar si el monto está en el rango de esta meta
        if (monto >= montoMinimo && (montoMaximo === null || monto <= montoMaximo)) {
            const comision = monto * (porcentajeComision / 100);
            comisionResultado.textContent = 'Q' + comision.toLocaleString('es-GT', {minimumFractionDigits: 2});
            comisionResultado.className = 'text-success';
        } else {
            comisionResultado.textContent = 'Fuera del rango de esta meta';
            comisionResultado.className = 'text-warning';
        }
    }
    
    simuladorMonto.addEventListener('input', calcularComision);
    calcularComision(); // Calcular inicial
});
</script>
@endsection
