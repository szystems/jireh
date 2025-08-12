@extends('layouts.admin')

@section('title', 'Crear Lote de Pago - Comisiones')

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
                        Crear Nuevo Lote
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

            <!-- Filtros Avanzados para Comisiones -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-funnel"></i> 
                        Filtros para Comisiones Pendientes
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('lotes-pago.create') }}" id="filtrosForm" class="row g-3">
                        <!-- Filtros por Persona -->
                        <div class="col-md-3">
                            <label class="form-label">Trabajador</label>
                            <select name="trabajador_id" class="form-select">
                                <option value="">Todos los trabajadores</option>
                                @foreach($trabajadores as $trabajador)
                                    <option value="{{ $trabajador->id }}" 
                                        {{ request('trabajador_id') == $trabajador->id ? 'selected' : '' }}>
                                        {{ $trabajador->nombre }} {{ $trabajador->apellido }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Vendedor</label>
                            <select name="vendedor_id" class="form-select">
                                <option value="">Todos los vendedores</option>
                                @foreach($vendedores as $vendedor)
                                    <option value="{{ $vendedor->id }}" 
                                        {{ request('vendedor_id') == $vendedor->id ? 'selected' : '' }}>
                                        {{ $vendedor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtros por Tipo y Período -->
                        <div class="col-md-3">
                            <label class="form-label">Tipo de Comisión</label>
                            <select name="tipo_comision" class="form-select">
                                <option value="">Todos los tipos</option>
                                @foreach($tiposComision as $tipo)
                                    <option value="{{ $tipo }}" 
                                        {{ request('tipo_comision') == $tipo ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $tipo)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Período Rápido</label>
                            <select name="periodo" class="form-select">
                                <option value="">Todos los períodos</option>
                                
                                <!-- Períodos diarios -->
                                <option value="hoy" {{ (request('periodo') == 'hoy' || $periodoSeleccionado == 'hoy') ? 'selected' : '' }}>
                                    Hoy
                                </option>
                                <option value="ayer" {{ (request('periodo') == 'ayer' || $periodoSeleccionado == 'ayer') ? 'selected' : '' }}>
                                    Ayer
                                </option>
                                
                                <!-- Períodos semanales -->
                                <option value="esta_semana" {{ (request('periodo') == 'esta_semana' || $periodoSeleccionado == 'esta_semana') ? 'selected' : '' }}>
                                    Esta Semana
                                </option>
                                <option value="semana_anterior" {{ (request('periodo') == 'semana_anterior' || $periodoSeleccionado == 'semana_anterior') ? 'selected' : '' }}>
                                    Semana Anterior
                                </option>
                                
                                <!-- Períodos mensuales -->
                                <option value="este_mes" {{ (request('periodo') == 'este_mes' || $periodoSeleccionado == 'este_mes') ? 'selected' : '' }}>
                                    Este Mes (Por defecto)
                                </option>
                                <option value="mes_anterior" {{ (request('periodo') == 'mes_anterior' || $periodoSeleccionado == 'mes_anterior') ? 'selected' : '' }}>
                                    Mes Anterior
                                </option>
                                
                                <!-- Períodos mayores -->
                                <option value="este_trimestre" {{ (request('periodo') == 'este_trimestre' || $periodoSeleccionado == 'este_trimestre') ? 'selected' : '' }}>
                                    Este Trimestre
                                </option>
                                <option value="este_año" {{ (request('periodo') == 'este_año' || $periodoSeleccionado == 'este_año') ? 'selected' : '' }}>
                                    Este Año
                                </option>
                                <option value="año_anterior" {{ (request('periodo') == 'año_anterior' || $periodoSeleccionado == 'año_anterior') ? 'selected' : '' }}>
                                    Año Anterior
                                </option>
                                
                                <!-- Períodos deslizantes -->
                                <option value="ultimos_30_dias" {{ (request('periodo') == 'ultimos_30_dias' || $periodoSeleccionado == 'ultimos_30_dias') ? 'selected' : '' }}>
                                    Últimos 30 días
                                </option>
                                <option value="ultimos_90_dias" {{ (request('periodo') == 'ultimos_90_dias' || $periodoSeleccionado == 'ultimos_90_dias') ? 'selected' : '' }}>
                                    Últimos 90 días
                                </option>
                            </select>
                            <small class="form-text text-muted">
                                Si no seleccionas otros filtros, por defecto se muestran las comisiones de este mes
                            </small>
                        </div>

                        <!-- Filtros por Fecha Personalizada -->
                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="bi bi-calendar-event"></i> Fecha Inicio (Personalizada)
                            </label>
                            <input type="date" name="fecha_inicio" class="form-control" 
                                   value="{{ request('fecha_inicio') }}"
                                   placeholder="Seleccionar fecha de inicio">
                            <small class="form-text text-muted">
                                Anula el período rápido si se selecciona
                            </small>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">
                                <i class="bi bi-calendar-check"></i> Fecha Fin (Personalizada)
                            </label>
                            <input type="date" name="fecha_fin" class="form-control" 
                                   value="{{ request('fecha_fin') }}"
                                   placeholder="Seleccionar fecha final">
                            <small class="form-text text-muted">
                                Opcional: si no se especifica, hasta hoy
                            </small>
                        </div>

                        <!-- Filtros por Monto -->
                        <div class="col-md-3">
                            <label class="form-label">Monto Mínimo</label>
                            <input type="number" name="monto_minimo" class="form-control" 
                                   step="0.01" placeholder="0.00" value="{{ request('monto_minimo') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Monto Máximo</label>
                            <input type="number" name="monto_maximo" class="form-control" 
                                   step="0.01" placeholder="999999.99" value="{{ request('monto_maximo') }}">
                        </div>

                        <!-- Botones de Acción -->
                        <div class="col-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Aplicar Filtros
                                </button>
                                <a href="{{ route('lotes-pago.create') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Limpiar Filtros
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Estadísticas de Filtros -->
                    @if(request()->hasAny(['trabajador_id', 'vendedor_id', 'tipo_comision', 'periodo', 'fecha_inicio', 'fecha_fin', 'monto_minimo', 'monto_maximo']))
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Resultados:</strong> 
                                    Se encontraron {{ $estadisticas['total_comisiones'] }} comisiones pendientes
                                    por un monto total de {{ $config->currency_simbol }} {{ number_format($estadisticas['monto_total'], 2) }}
                                    <br><small class="text-muted">
                                        <i class="bi bi-check-square"></i> Todas las comisiones se muestran sin paginación para facilitar la selección
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <form action="{{ route('lotes-pago.store') }}" method="POST" enctype="multipart/form-data" id="form-lote-pago">
                @csrf

                <!-- Información del Lote -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-file-earmark-plus"></i> 
                            Información del Lote de Pago
                        </h5>
                    </div>
                    <div class="card-body">
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
                                              placeholder="Observaciones adicionales sobre el lote de pago...">{{ old('observaciones') }}</textarea>
                                    @error('observaciones')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selección de Comisiones -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-check2-square"></i> 
                            Seleccionar Comisiones a Pagar
                        </h5>
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Todas las comisiones pendientes se muestran sin paginación para facilitar la selección múltiple
                        </small>
                    </div>
                    <div class="card-body">
                        @if($comisionesPendientes->count() > 0)
                            <!-- Resumen de selección -->
                            <div class="alert alert-info" id="resumen-seleccion" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Comisiones seleccionadas: </strong>
                                        <span id="cantidad-seleccionadas">0</span>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <strong>Total a pagar: </strong>
                                        <span id="total-seleccionado" class="text-success">{{ $config->currency_simbol }}0.00</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabla de comisiones -->
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="5%">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>ID</th>
                                            <th>Trabajador</th>
                                            <th>Venta</th>
                                            <th>Cliente</th>
                                            <th>Tipo</th>
                                            <th>Monto</th>
                                            <th>Fecha Venta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($comisionesPendientes as $comision)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" 
                                                           name="comision_ids[]" 
                                                           value="{{ $comision->id }}"
                                                           class="form-check-input"
                                                           data-monto="{{ $comision->monto }}">
                                                </td>
                                                <td>
                                                    <strong>#{{ $comision->id }}</strong>
                                                </td>
                                                <td>
                                                    @if($comision->commissionable_type == 'App\Models\User')
                                                        {{ $comision->commissionable->name ?? 'Usuario eliminado' }}
                                                        <small class="text-muted">(Vendedor)</small>
                                                    @else
                                                        {{ $comision->commissionable->nombre ?? 'N/A' }}
                                                        {{ $comision->commissionable->apellido ?? '' }}
                                                        <small class="text-muted">(Trabajador)</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($comision->venta_id)
                                                        <a href="{{ route('ventas.show', $comision->venta_id) }}" target="_blank" class="text-decoration-none">
                                                            <i class="bi bi-receipt"></i> Venta #{{ $comision->venta->id }}
                                                        </a>
                                                    @else
                                                        @if($comision->tipo_comision == 'meta_venta')
                                                            <span class="text-muted">
                                                                <i class="bi bi-trophy"></i> Meta de Ventas
                                                            </span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($comision->venta && $comision->venta->cliente)
                                                        {{ $comision->venta->cliente->nombre }}
                                                    @else
                                                        @if($comision->tipo_comision == 'meta_venta')
                                                            <span class="text-muted">Múltiples clientes</span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $badgeColor = 'secondary';
                                                        switch($comision->tipo_comision) {
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
                                                        if ($comision->tipo_comision === 'meta_venta') {
                                                            $porcentaje = $comision->porcentaje;
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
                                                        {{ ucfirst(str_replace('_', ' ', $comision->tipo_comision ?? 'N/A')) }}
                                                    </span>
                                                    @if($metaInfo)
                                                        <br><small><span class="badge bg-{{ $metaInfo['color'] }} mt-1" title="Meta alcanzada: {{ $metaInfo['rango'] }}">
                                                            {{ $metaInfo['nombre'] }}
                                                        </span></small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong class="text-success">
                                                        {{ $config->currency_simbol }}{{ number_format($comision->monto, 2) }}
                                                    </strong>
                                                </td>
                                                <td>
                                                    @if($comision->venta && $comision->venta->fecha)
                                                        {{ $comision->venta->fecha->format('d/m/Y') }}
                                                    @else
                                                        @if($comision->tipo_comision == 'meta_venta')
                                                            <span class="text-muted">
                                                                Período: {{ $comision->fecha_calculo->format('m/Y') }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @error('comisiones_seleccionadas')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror

                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-inbox h1 text-muted mb-3"></i>
                                <h5 class="text-muted">No hay comisiones pendientes de pago</h5>
                                <p class="text-muted">
                                    Todas las comisiones han sido pagadas o no hay comisiones generadas aún.
                                </p>
                                <a href="{{ route('comisiones.index') }}" class="btn btn-primary">
                                    <i class="bi bi-eye"></i> Ver Comisiones
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('lotes-pago.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                            
                            @if($comisionesPendientes->count() > 0)
                                <button type="submit" class="btn btn-success" id="btn-crear-lote" disabled>
                                    <i class="bi bi-save"></i> Crear Lote de Pago
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
console.log('Iniciando script...');

$(document).ready(function() {
    console.log('Document ready ejecutado');
    
    try {
        // Verificar jQuery
        if (typeof $ === 'undefined') {
            console.error('jQuery no está disponible');
            return;
        }
        
        console.log('jQuery disponible, versión:', $.fn.jquery);
        
        // Funcionalidad de filtros mejorada
        setupFilterInteraction();
        
        // Buscar elementos
        var selectAllCheckbox = $('#selectAll');
        var individualCheckboxes = $('input[name="comision_ids[]"]');
        
        console.log('selectAll encontrado:', selectAllCheckbox.length);
        console.log('Checkboxes individuales:', individualCheckboxes.length);
        
        if (selectAllCheckbox.length === 0) {
            console.error('No se encontró el checkbox selectAll');
            return;
        }
        
        if (individualCheckboxes.length === 0) {
            console.error('No se encontraron checkboxes individuales');
            return;
        }
        
        // Configurar evento del checkbox principal
        selectAllCheckbox.on('change', function() {
            console.log('SelectAll cambió a:', this.checked);
            
            // Cambiar todos los checkboxes individuales
            individualCheckboxes.prop('checked', this.checked);
            
            // Actualizar UI
            actualizarUI();
        });
        
        // Configurar eventos de checkboxes individuales
        individualCheckboxes.on('change', function() {
            console.log('Checkbox individual cambió');
            actualizarUI();
            actualizarSelectAll();
        });
        
        // Función para actualizar la interfaz
        function actualizarUI() {
            var checkedBoxes = individualCheckboxes.filter(':checked');
            var cantidad = checkedBoxes.length;
            var total = 0;
            
            checkedBoxes.each(function() {
                var monto = parseFloat($(this).data('monto')) || 0;
                total += monto;
            });
            
            console.log('Actualizando UI - Cantidad:', cantidad, 'Total:', total);
            
            // Actualizar elementos
            $('#cantidad-seleccionadas').text(cantidad);
            $('#total-seleccionado').text('{{ $config->currency_simbol }}' + total.toFixed(2));
            
            // Mostrar/ocultar resumen
            if (cantidad > 0) {
                $('#resumen-seleccion').show();
                $('#btn-crear-lote').prop('disabled', false);
            } else {
                $('#resumen-seleccion').hide();
                $('#btn-crear-lote').prop('disabled', true);
            }
        }
        
        // Función para actualizar estado del selectAll
        function actualizarSelectAll() {
            var total = individualCheckboxes.length;
            var checked = individualCheckboxes.filter(':checked').length;
            
            if (checked === 0) {
                selectAllCheckbox.prop('checked', false).prop('indeterminate', false);
            } else if (checked === total) {
                selectAllCheckbox.prop('checked', true).prop('indeterminate', false);
            } else {
                selectAllCheckbox.prop('checked', false).prop('indeterminate', true);
            }
        }
        
        // Inicializar
        actualizarUI();
        actualizarSelectAll();
        
        console.log('Configuración completada exitosamente');
        
    } catch (error) {
        console.error('Error en el script:', error);
    }
});

// Filtros y validación del formulario
$(document).ready(function() {
    try {
        // Auto-submit de filtros
        $('#filtrosForm select').on('change', function() {
            $('#filtrosForm').submit();
        });

        $('#filtrosForm input[type="date"]').on('change', function() {
            setTimeout(function() {
                $('#filtrosForm').submit();
            }, 500);
        });

        // Limpiar filtros cruzados
        $('input[name="fecha_inicio"], input[name="fecha_fin"]').on('change', function() {
            if ($(this).val()) {
                $('select[name="periodo"]').val('');
            }
        });

        $('select[name="periodo"]').on('change', function() {
            if ($(this).val()) {
                $('input[name="fecha_inicio"], input[name="fecha_fin"]').val('');
            }
        });

        // Validación del formulario
        $('#form-lote-pago').on('submit', function(e) {
            var seleccionadas = $('input[name="comision_ids[]"]:checked');
            
            if (seleccionadas.length === 0) {
                e.preventDefault();
                alert('Debe seleccionar al menos una comisión para crear el lote de pago.');
                return false;
            }

            var total = $('#total-seleccionado').text();
            var confirmMessage = '¿Está seguro de crear el lote de pago con ' + seleccionadas.length + ' comisiones por un total de ' + total + '?';
            
            if (!confirm(confirmMessage)) {
                e.preventDefault();
                return false;
            }
        });
        
    } catch (error) {
        console.error('Error en filtros/validación:', error);
    }
});

// Función para configurar la interacción de filtros
function setupFilterInteraction() {
    console.log('Configurando interacción de filtros...');
    
    // Elementos de filtros
    const periodoSelect = $('select[name="periodo"]');
    const fechaInicio = $('input[name="fecha_inicio"]');
    const fechaFin = $('input[name="fecha_fin"]');
    
    // Cuando se selecciona un período rápido, limpiar las fechas personalizadas
    periodoSelect.on('change', function() {
        if ($(this).val() !== '') {
            console.log('Período seleccionado:', $(this).val(), '- Limpiando fechas personalizadas');
            fechaInicio.val('');
            fechaFin.val('');
            
            // Mostrar mensaje temporal
            showFilterMessage('Se aplicó el período: ' + $(this).find('option:selected').text());
        }
    });
    
    // Cuando se selecciona una fecha personalizada, limpiar el período rápido
    fechaInicio.add(fechaFin).on('change', function() {
        if ($(this).val() !== '') {
            console.log('Fecha personalizada seleccionada - Limpiando período rápido');
            periodoSelect.val('');
            
            // Mostrar mensaje temporal
            showFilterMessage('Usando fechas personalizadas - Período rápido desactivado');
        }
    });
}

// Función para mostrar mensajes temporales sobre filtros
function showFilterMessage(message) {
    // Crear o actualizar el mensaje
    let messageDiv = $('#filter-message');
    if (messageDiv.length === 0) {
        messageDiv = $('<div id="filter-message" class="alert alert-info alert-dismissible fade show mt-2" role="alert"></div>');
        $('select[name="periodo"]').closest('.col-md-3').append(messageDiv);
    }
    
    messageDiv.html(`
        <i class="bi bi-info-circle"></i> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `);
    
    // Auto-ocultar después de 3 segundos
    setTimeout(() => {
        messageDiv.fadeOut();
    }, 3000);
}
</script>
@endsection
@endsection
