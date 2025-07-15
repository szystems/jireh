@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="card mt-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="bi bi-plus-lg"></i> Nueva Meta de Ventas</h4>
                <a href="{{ route('metas-ventas.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('metas-ventas.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Información General</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre de la Meta *</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control" 
                                           value="{{ old('nombre') }}" required placeholder="Ej: Meta Bronce">
                                </div>

                                <div class="mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea name="descripcion" id="descripcion" class="form-control" rows="3" 
                                              placeholder="Descripción opcional de la meta">{{ old('descripcion') }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="periodo" class="form-label">Período *</label>
                                    <select name="periodo" id="periodo" class="form-select" required>
                                        <option value="">Seleccionar período</option>
                                        <option value="mensual" {{ old('periodo') == 'mensual' ? 'selected' : '' }}>Mensual</option>
                                        <option value="trimestral" {{ old('periodo') == 'trimestral' ? 'selected' : '' }}>Trimestral</option>
                                        <option value="semestral" {{ old('periodo') == 'semestral' ? 'selected' : '' }}>Semestral</option>
                                        <option value="anual" {{ old('periodo') == 'anual' ? 'selected' : '' }}>Anual</option>
                                    </select>
                                </div>

                                <div class="form-check">
                                    <input type="checkbox" name="estado" id="estado" class="form-check-input" 
                                           {{ old('estado', true) ? 'checked' : '' }}>
                                    <label for="estado" class="form-check-label">Meta activa</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="bi bi-currency-dollar"></i> Configuración de Comisiones</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="monto_minimo" class="form-label">Monto Mínimo *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Q</span>
                                        <input type="number" name="monto_minimo" id="monto_minimo" class="form-control @error('monto_minimo') is-invalid @enderror" 
                                               value="{{ old('monto_minimo') }}" step="0.01" min="0" required
                                               placeholder="0.00">
                                    </div>
                                    @error('monto_minimo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Monto mínimo para alcanzar esta meta</small>
                                </div>

                                <div class="mb-3">
                                    <label for="monto_maximo" class="form-label">Monto Máximo</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Q</span>
                                        <input type="number" name="monto_maximo" id="monto_maximo" class="form-control" 
                                               value="{{ old('monto_maximo') }}" step="0.01" min="0"
                                               placeholder="Dejar vacío para sin límite">
                                    </div>
                                    <small class="text-muted">Opcional: Límite superior para esta meta</small>
                                </div>

                                <div class="mb-3">
                                    <label for="porcentaje_comision" class="form-label">Porcentaje de Comisión *</label>
                                    <div class="input-group">
                                        <input type="number" name="porcentaje_comision" id="porcentaje_comision" class="form-control" 
                                               value="{{ old('porcentaje_comision') }}" step="0.01" min="0" max="100" required
                                               placeholder="5.00">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <small class="text-muted">Porcentaje de comisión para esta meta</small>
                                </div>

                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="bi bi-calculator"></i> Vista Previa</h6>
                                        <div id="preview">
                                            <p class="mb-1"><strong>Rango:</strong> <span id="rango-preview">-</span></p>
                                            <p class="mb-1"><strong>Comisión:</strong> <span id="comision-preview">-</span></p>
                                            <p class="mb-0"><strong>Ejemplo:</strong> <span id="ejemplo-preview">-</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('metas-ventas.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-square"></i> Crear Meta
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const montoMinimo = document.getElementById('monto_minimo');
    const montoMaximo = document.getElementById('monto_maximo');
    const porcentaje = document.getElementById('porcentaje_comision');
    
    function updatePreview() {
        const min = parseFloat(montoMinimo.value) || 0;
        const max = parseFloat(montoMaximo.value) || null;
        const perc = parseFloat(porcentaje.value) || 0;
        
        // Rango
        let rango = 'Q' + min.toLocaleString('es-GT', {minimumFractionDigits: 2});
        if (max) {
            rango += ' - Q' + max.toLocaleString('es-GT', {minimumFractionDigits: 2});
        } else {
            rango += ' en adelante';
        }
        document.getElementById('rango-preview').textContent = rango;
        
        // Comisión
        document.getElementById('comision-preview').textContent = perc + '%';
        
        // Ejemplo
        const ejemploMonto = max ? (min + max) / 2 : min + 5000;
        const ejemploComision = ejemploMonto * (perc / 100);
        document.getElementById('ejemplo-preview').textContent = 
            'Ventas Q' + ejemploMonto.toLocaleString('es-GT', {minimumFractionDigits: 2}) + 
            ' = Q' + ejemploComision.toLocaleString('es-GT', {minimumFractionDigits: 2}) + ' comisión';
    }
    
    [montoMinimo, montoMaximo, porcentaje].forEach(input => {
        input.addEventListener('input', updatePreview);
    });
    
    updatePreview();
});
</script>
@endsection
