@extends('layouts.admin')

@section('content')
    <!-- Incluir Chart.js para los gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div class="page-title">
                    <h5>Cotizaciones</h5>
                </div>
            </div>
        </div>
        <div class="content-wrapper">
            <!-- Estadísticas en cards -->
            <div class="row mb-4">
                @php
                    // Calcular estadísticas separando estados de vigencia
                    $totalCotizaciones = $cotizaciones->count();
                    $cotizacionesGeneradas = $cotizaciones->filter(function($c) { return $c->estado === 'Generado'; })->count();
                    $cotizacionesAprobadas = $cotizaciones->filter(function($c) { return $c->estado === 'Aprobado'; })->count();
                    
                    // Para las generadas, calcular vigentes/vencidas
                    $cotizacionesVigentes = $cotizaciones->filter(function($c) { 
                        return $c->estado === 'Generado' && $c->esta_vigente; 
                    })->count();
                    $cotizacionesVencidas = $cotizaciones->filter(function($c) { 
                        return $c->estado === 'Generado' && !$c->esta_vigente; 
                    })->count();
                    
                    $totalDescuentos = $cotizaciones->sum(function($cotizacion) {
                        $descuento = 0;
                        foreach ($cotizacion->detalleCotizaciones as $detalle) {
                            if ($detalle->descuento_id && $detalle->descuento) {
                                // Usar precio histórico guardado en detalle
                                $precioUnitario = $detalle->precio_venta > 0 ? $detalle->precio_venta : ($detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad));
                                $subtotal = $precioUnitario * $detalle->cantidad;
                                $descuento += $subtotal * ($detalle->descuento->porcentaje_descuento / 100);
                            }
                        }
                        return $descuento;
                    });

                    $totalArticulos = $cotizaciones->sum(function($cotizacion) {
                        return $cotizacion->detalleCotizaciones->sum('cantidad');
                    });
                @endphp

                <div class="col-md-2">
                    <div class="stats-tile">
                        <div class="sale-icon shade-blue">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div class="sale-details">
                            <h5>{{ $totalCotizaciones }}</h5>
                            <p>Total Cotizaciones</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="stats-tile">
                        <div class="sale-icon shade-green">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="sale-details">
                            <h5>{{ $cotizacionesVigentes }}</h5>
                            <p>Vigentes</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="stats-tile">
                        <div class="sale-icon shade-red">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="sale-details">
                            <h5>{{ $cotizacionesVencidas }}</h5>
                            <p>Vencidas</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="stats-tile">
                        <div class="sale-icon shade-yellow">
                            <i class="bi bi-hand-thumbs-up"></i>
                        </div>
                        <div class="sale-details">
                            <h5>{{ $cotizacionesAprobadas }}</h5>
                            <p>Aprobadas</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="stats-tile">
                        <div class="sale-icon shade-purple">
                            <i class="bi bi-file-plus"></i>
                        </div>
                        <div class="sale-details">
                            <h5>{{ $cotizacionesGeneradas }}</h5>
                            <p>Generadas</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs para diferentes vistas -->
            <ul class="nav nav-tabs" id="cotizacionTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="todas-tab" data-bs-toggle="tab" data-bs-target="#todas" type="button" role="tab">
                        Todas las Cotizaciones
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="generadas-tab" data-bs-toggle="tab" data-bs-target="#generadas" type="button" role="tab">
                        Generadas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="vigentes-tab" data-bs-toggle="tab" data-bs-target="#vigentes" type="button" role="tab">
                        Vigentes
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="vencidas-tab" data-bs-toggle="tab" data-bs-target="#vencidas" type="button" role="tab">
                        Vencidas
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="aprobadas-tab" data-bs-toggle="tab" data-bs-target="#aprobadas" type="button" role="tab">
                        Aprobadas
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="cotizacionTabContent">
                <!-- Todas las cotizaciones -->
                <div class="tab-pane fade show active" id="todas" role="tabpanel">
                    <div class="card mt-3">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5>Todas las Cotizaciones</h5>
                            <a href="{{ route('admin.cotizaciones.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus"></i> Nueva Cotización
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="todasCotizacionesTable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Número</th>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Estado</th>
                                            <th>Vencimiento</th>
                                            <th>Total</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cotizaciones as $cotizacion)
                                            <tr>
                                                <td>{{ $cotizacion->numero_cotizacion }}</td>
                                                <td>{{ $cotizacion->fecha_cotizacion->format('d/m/Y') }}</td>
                                                <td>{{ $cotizacion->cliente->nombre }}</td>
                                                <td>
                                                    <!-- Estado badge (Generado/Aprobado) -->
                                                    @if($cotizacion->estado === 'Aprobado')
                                                        <span class="badge bg-primary">Aprobado</span>
                                                    @else
                                                        <span class="badge bg-info">Generado</span>
                                                    @endif
                                                    
                                                    <!-- Vigencia badge (solo para Generado) -->
                                                    @if($cotizacion->estado === 'Generado')
                                                        <br>
                                                        @if($cotizacion->esta_vigente)
                                                            @php
                                                                $diasRestantes = now()->diffInDays($cotizacion->fecha_vencimiento, false);
                                                            @endphp
                                                            <span class="badge bg-success small">
                                                                Vigente ({{ $diasRestantes }} días)
                                                            </span>
                                                        @else
                                                            @php
                                                                $diasVencida = now()->diffInDays($cotizacion->fecha_vencimiento);
                                                            @endphp
                                                            <span class="badge bg-danger small">
                                                                Vencida ({{ $diasVencida }} días)
                                                            </span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>{{ $cotizacion->fecha_vencimiento->format('d/m/Y') }}</td>
                                                <td>{{ $config->currency_simbol }}{{ number_format($cotizacion->detalleCotizaciones->sum('sub_total'), 2) }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.cotizaciones.show', $cotizacion->id) }}" class="btn btn-sm btn-info" title="Ver">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.cotizaciones.edit', $cotizacion->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="{{ route('admin.cotizaciones.export.single.pdf', $cotizacion->id) }}" class="btn btn-sm btn-danger" title="PDF" target="_blank">
                                                            <i class="bi bi-file-pdf"></i>
                                                        </a>
                                                        @if($cotizacion->estado === 'Generado' && $cotizacion->esta_vigente)
                                                            <button type="button" class="btn btn-sm btn-success" onclick="cambiarEstado({{ $cotizacion->id }}, 'Aprobado')" title="Aprobar">
                                                                <i class="bi bi-check"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cotizaciones generadas -->
                <div class="tab-pane fade" id="generadas" role="tabpanel">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Cotizaciones Generadas (En Proceso)</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="generadasCotizacionesTable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Número</th>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Estado</th>
                                            <th>Vencimiento</th>
                                            <th>Total</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cotizaciones->where('estado', 'Generado') as $cotizacion)
                                            <tr>
                                                <td>{{ $cotizacion->numero_cotizacion }}</td>
                                                <td>{{ $cotizacion->fecha_cotizacion->format('d/m/Y') }}</td>
                                                <td>{{ $cotizacion->cliente->nombre }}</td>
                                                <td>
                                                    <!-- Vigencia badge -->
                                                    @if($cotizacion->esta_vigente)
                                                        @php
                                                            $diasRestantes = now()->diffInDays($cotizacion->fecha_vencimiento, false);
                                                        @endphp
                                                        <span class="badge bg-success">
                                                            Vigente ({{ $diasRestantes }} días)
                                                        </span>
                                                    @else
                                                        @php
                                                            $diasVencida = now()->diffInDays($cotizacion->fecha_vencimiento);
                                                        @endphp
                                                        <span class="badge bg-danger">
                                                            Vencida ({{ $diasVencida }} días)
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>{{ $cotizacion->fecha_vencimiento->format('d/m/Y') }}</td>
                                                <td>{{ $config->currency_simbol }}{{ number_format($cotizacion->detalleCotizaciones->sum('sub_total'), 2) }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.cotizaciones.show', $cotizacion->id) }}" class="btn btn-sm btn-info" title="Ver">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.cotizaciones.edit', $cotizacion->id) }}" class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="{{ route('admin.cotizaciones.export.single.pdf', $cotizacion->id) }}" class="btn btn-sm btn-danger" title="PDF" target="_blank">
                                                            <i class="bi bi-file-pdf"></i>
                                                        </a>
                                                        @if($cotizacion->esta_vigente)
                                                            <button type="button" class="btn btn-sm btn-success" onclick="cambiarEstado({{ $cotizacion->id }}, 'Aprobado')" title="Aprobar">
                                                                <i class="bi bi-check"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cotizaciones vigentes -->
                <div class="tab-pane fade" id="vigentes" role="tabpanel">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Cotizaciones Vigentes</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="vigentesCotizacionesTable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Número</th>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Vencimiento</th>
                                            <th>Total</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cotizaciones->filter(function($c) { return $c->estado === 'Generado' && $c->esta_vigente; }) as $cotizacion)
                                            <tr>
                                                <td>{{ $cotizacion->numero_cotizacion }}</td>
                                                <td>{{ $cotizacion->fecha_cotizacion->format('d/m/Y') }}</td>
                                                <td>{{ $cotizacion->cliente->nombre }}</td>
                                                <td>{{ $cotizacion->fecha_vencimiento->format('d/m/Y') }}</td>
                                                <td>{{ $config->currency_simbol }}{{ number_format($cotizacion->detalleCotizaciones->sum('sub_total'), 2) }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.cotizaciones.show', $cotizacion->id) }}" class="btn btn-sm btn-info">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.cotizaciones.edit', $cotizacion->id) }}" class="btn btn-sm btn-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="{{ route('admin.cotizaciones.export.single.pdf', $cotizacion->id) }}" class="btn btn-sm btn-danger" target="_blank">
                                                            <i class="bi bi-file-pdf"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-success" onclick="cambiarEstado({{ $cotizacion->id }}, 'Aprobado')" title="Aprobar">
                                                            <i class="bi bi-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-secondary" onclick="cambiarEstado({{ $cotizacion->id }}, 'Generado')" title="Regenerar">
                                                            <i class="bi bi-arrow-clockwise"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cotizaciones vencidas -->
                <div class="tab-pane fade" id="vencidas" role="tabpanel">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Cotizaciones Vencidas</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="vencidasCotizacionesTable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Número</th>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Vencimiento</th>
                                            <th>Total</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cotizaciones->filter(function($c) { return $c->estado === 'Generado' && !$c->esta_vigente; }) as $cotizacion)
                                            <tr>
                                                <td>{{ $cotizacion->numero_cotizacion }}</td>
                                                <td>{{ $cotizacion->fecha_cotizacion->format('d/m/Y') }}</td>
                                                <td>{{ $cotizacion->cliente->nombre }}</td>
                                                <td>{{ $cotizacion->fecha_vencimiento->format('d/m/Y') }}</td>
                                                <td>{{ $config->currency_simbol }}{{ number_format($cotizacion->detalleCotizaciones->sum('sub_total'), 2) }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.cotizaciones.show', $cotizacion->id) }}" class="btn btn-sm btn-info">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.cotizaciones.export.single.pdf', $cotizacion->id) }}" class="btn btn-sm btn-danger" target="_blank">
                                                            <i class="bi bi-file-pdf"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cotizaciones aprobadas -->
                <div class="tab-pane fade" id="aprobadas" role="tabpanel">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5>Cotizaciones Aprobadas</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="aprobadasCotizacionesTable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Número</th>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Vencimiento</th>
                                            <th>Total</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cotizaciones->where('estado', 'Aprobado') as $cotizacion)
                                            <tr>
                                                <td>{{ $cotizacion->numero_cotizacion }}</td>
                                                <td>{{ $cotizacion->fecha_cotizacion->format('d/m/Y') }}</td>
                                                <td>{{ $cotizacion->cliente->nombre }}</td>
                                                <td>{{ $cotizacion->fecha_vencimiento->format('d/m/Y') }}</td>
                                                <td>{{ $config->currency_simbol }}{{ number_format($cotizacion->detalleCotizaciones->sum('sub_total'), 2) }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.cotizaciones.show', $cotizacion->id) }}" class="btn btn-sm btn-info">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.cotizaciones.export.single.pdf', $cotizacion->id) }}" class="btn btn-sm btn-danger" target="_blank">
                                                            <i class="bi bi-file-pdf"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts para DataTables y funcionalidades -->
    <script>
        $(document).ready(function() {
            // Inicializar DataTables para todas las tablas
            $('#todasCotizacionesTable, #generadasCotizacionesTable, #vigentesCotizacionesTable, #vencidasCotizacionesTable, #aprobadasCotizacionesTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                },
                order: [[0, 'desc']],
                pageLength: 25
            });
        });

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