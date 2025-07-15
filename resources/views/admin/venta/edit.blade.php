@extends('layouts.admin')

@section('content')
    <!-- Estilos personalizados para SweetAlert -->
    <style>
        /* Mejorar espaciado entre botones de SweetAlert */
        .swal2-actions {
            gap: 20px !important; /* Aumentar espaciado entre botones */
            margin: 1.5em auto 0 !important;
        }
        
        /* Mejorar contraste y diseño del botón cancelar */
        .swal2-styled.btn-secondary,
        .swal2-cancel {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: #ffffff !important;
            font-weight: 500 !important;
            box-shadow: 0 2px 4px rgba(108, 117, 125, 0.3) !important;
        }
        
        .swal2-styled.btn-secondary:hover,
        .swal2-cancel:hover {
            background-color: #545b62 !important;
            border-color: #4e555b !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(108, 117, 125, 0.4) !important;
        }
        
        /* Mejorar el botón de confirmar */
        .swal2-styled.btn-danger,
        .swal2-confirm {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #ffffff !important;
            font-weight: 500 !important;
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3) !important;
        }
        
        .swal2-styled.btn-danger:hover,
        .swal2-confirm:hover {
            background-color: #c82333 !important;
            border-color: #bd2130 !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4) !important;
        }
        
        /* Transiciones suaves para los botones */
        .swal2-styled {
            transition: all 0.2s ease !important;
        }
        
        /* Mejorar el contenedor de botones */
        .swal2-actions button {
            margin: 0 10px !important;
            min-width: 120px !important;
            padding: 10px 20px !important;
            border-radius: 6px !important;
        }
    </style>

    <div class="content-wrapper-scroll">
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div class="page-title">
                    <h5>Editar Venta @if ($venta->numero_factura) - Factura: {{ $venta->numero_factura }} @endif</h5>
                </div>
            </div>
        </div>
        <div class="content-wrapper">
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-body">
                            @if (count($errors)>0)
                                <div class="alert alert-danger text-white" role="alert">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{$error}}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{ url('update-venta/'.$venta->id) }}" method="POST" id="forma-editar-venta">
                                @csrf
                                @method('PUT')
                                <div class="row gx-3">
                                    <!-- Información básica de la venta -->
                                    <div class="col-md-6 mb-3">
                                        <label for="numero_factura" class="form-label">Número de Factura</label>
                                        <input type="text" class="form-control" id="numero_factura" name="numero_factura" value="{{ old('numero_factura', $venta->numero_factura) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="fecha" class="form-label">Fecha</label>
                                        <input type="date" class="form-control" id="fecha" name="fecha" value="{{ old('fecha', $venta->fecha ? $venta->fecha->format('Y-m-d') : '') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cliente_id" class="form-label">Cliente</label>
                                        <select class="form-control select2" id="cliente_id" name="cliente_id" required>
                                            <option value="">Seleccione un cliente</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ (old('cliente_id', $venta->cliente_id) == $cliente->id) ? 'selected' : '' }}>
                                                    {{ $cliente->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="vehiculo_id" class="form-label">Vehículo</label>
                                        <select class="form-control select2" id="vehiculo_id" name="vehiculo_id" required>
                                            <option value="">Seleccione un vehículo</option>
                                            @php
                                                $vehiculoIdSelected = old('vehiculo_id', $venta->vehiculo_id);
                                                $vehiculoMostrar = null;
                                                if($vehiculoIdSelected) {
                                                    $vehiculoMostrar = \App\Models\Vehiculo::find($vehiculoIdSelected);
                                                }
                                            @endphp
                                            @if($vehiculoMostrar)
                                                <option value="{{ $vehiculoMostrar->id }}" selected>
                                                    {{ $vehiculoMostrar->marca }} {{ $vehiculoMostrar->modelo }} - {{ $vehiculoMostrar->placa }}
                                                </option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="tipo_venta" class="form-label">Tipo de Venta</label>
                                        <select class="form-control" id="tipo_venta" name="tipo_venta" required>
                                            <option value="Car Wash" {{ (old('tipo_venta', $venta->tipo_venta) == 'Car Wash') ? 'selected' : '' }}>Car Wash</option>
                                            <option value="CDS" {{ (old('tipo_venta', $venta->tipo_venta) == 'CDS') ? 'selected' : '' }}>CDS</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="estado_pago" class="form-label">Estado de Pago</label>
                                        <select class="form-control" id="estado_pago" name="estado_pago" required>
                                            <option value="pendiente" {{ (old('estado_pago', $venta->estado_pago) == 'pendiente') ? 'selected' : '' }}>Pendiente</option>
                                            <option value="pagado" {{ (old('estado_pago', $venta->estado_pago) == 'pagado') ? 'selected' : '' }}>Pagado</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="estado" value="{{ $venta->estado }}">
                                    <input type="hidden" name="usuario_id" value="{{ $venta->usuario_id }}">

                                    <!-- Detalles existentes de la venta -->
                                    <div class="col-md-12 mb-3">
                                        <h5>Detalles de la Venta</h5>

                                        @if($venta->detalleVentas->count() > 0)
                                            <h6 class="mt-4 mb-3">Detalles Existentes</h6>
                                            <div class="table-responsive mb-4">
                                                <table class="table table-striped" id="tabla-detalles-existentes">
                                                    <thead>
                                                        <tr>
                                                            <th>Artículo</th>
                                                            <th>Cantidad</th>
                                                            <th>Precio</th>
                                                            <th>Descuento</th>
                                                            <th>Subtotal</th>
                                                            <th>Trabajadores</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($venta->detalleVentas as $detalle)
                                                            @php
                                                                // Calcular precio unitario correctamente
                                                                $precioUnitario = $detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad);

                                                                // Calcular subtotal sin descuento
                                                                $subtotalSinDescuento = $precioUnitario * $detalle->cantidad;

                                                                // Calcular descuento si existe
                                                                $montoDescuento = 0;
                                                                if($detalle->descuento_id) {
                                                                    $descuento = \App\Models\Descuento::find($detalle->descuento_id);
                                                                    if($descuento) {
                                                                        $porcentajeDescuento = $descuento->porcentaje_descuento;
                                                                        $montoDescuento = $subtotalSinDescuento * ($porcentajeDescuento / 100);
                                                                    }
                                                                }

                                                                // Calcular subtotal final
                                                                $subtotalFinal = $subtotalSinDescuento - $montoDescuento;

                                                                // Obtener el tipo de unidad
                                                                $unidadTipo = $detalle->articulo && $detalle->articulo->unidad ? $detalle->articulo->unidad->tipo : 'decimal';
                                                                $step = $unidadTipo === 'unidad' ? '1' : '0.01';
                                                                $min = $unidadTipo === 'unidad' ? '1' : '0.01';
                                                            @endphp
                                                            <tr id="detalle-row-{{ $detalle->id }}" class="detalle-existente">
                                                                <td>
                                                                    @if($detalle->articulo)
                                                                        {{ $detalle->articulo->nombre }}
                                                                    @else
                                                                        Artículo no disponible
                                                                    @endif
                                                                    <input type="hidden" name="detalles[{{ $detalle->id }}][articulo_id]" value="{{ $detalle->articulo_id }}">
                                                                </td>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <input type="number" class="form-control cantidad-input"
                                                                            name="detalles[{{ $detalle->id }}][cantidad]"
                                                                            value="{{ old('detalles.'.$detalle->id.'.cantidad', $detalle->cantidad) }}"
                                                                            min="{{ $min }}" step="{{ $step }}"
                                                                            data-detalle-id="{{ $detalle->id }}"
                                                                            data-precio="{{ $precioUnitario }}"
                                                                            data-descuento-id="{{ $detalle->descuento_id }}"
                                                                            data-unidad-tipo="{{ $unidadTipo }}">
                                                                        <span class="input-group-text">
                                                                            {{ ($detalle->articulo && $detalle->articulo->unidad) ? $detalle->articulo->unidad->abreviatura : '' }}
                                                                        </span>
                                                                    </div>
                                                                </td>
                                                                <td>{{ $config->currency_simbol }}.{{ number_format($precioUnitario, 2) }}</td>
                                                                <td>
                                                                    <select class="form-control select2 descuento-select"
                                                                        name="detalles[{{ $detalle->id }}][descuento_id]"
                                                                        data-detalle-id="{{ $detalle->id }}">
                                                                        <option value="">Sin descuento</option>
                                                                        @foreach($descuentos as $descuento)
                                                                            <option value="{{ $descuento->id }}"
                                                                                data-porcentaje="{{ $descuento->porcentaje_descuento }}"
                                                                                {{ $detalle->descuento_id == $descuento->id ? 'selected' : '' }}>
                                                                                {{ $descuento->nombre }} ({{ $descuento->porcentaje_descuento }}%)
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </td>
                                                                <td class="subtotal-cell" id="subtotal-{{ $detalle->id }}">
                                                                    {{ $config->currency_simbol }}.{{ number_format($subtotalFinal, 2) }}
                                                                    <input type="hidden" name="detalles[{{ $detalle->id }}][sub_total]" value="{{ $subtotalFinal }}" class="subtotal-input">
                                                                    <input type="hidden" name="detalles[{{ $detalle->id }}][usuario_id]" value="{{ $detalle->usuario_id }}">
                                                                </td>
                                                                <td id="trabajadores-text-{{ $detalle->id }}">
                                                                    @if($detalle->articulo && $detalle->articulo->tipo == 'servicio')
                                                                        @php
                                                                            $trabajadoresAsignados = $detalle->trabajadoresCarwash;
                                                                            $numTrabajadores = $trabajadoresAsignados->count();
                                                                        @endphp

                                                                        <div id="trabajadores-{{ $detalle->id }}">
                                                                            @foreach($trabajadoresAsignados as $trabajador)
                                                                                <input type="hidden" name="trabajadores_carwash[{{ $detalle->id }}][]" value="{{ $trabajador->id }}">
                                                                            @endforeach
                                                                        </div>

                                                                        @if($numTrabajadores > 0)
                                                                            <span class="badge bg-info">{{ $numTrabajadores }} trabajador(es)</span>
                                                                            <div class="small mt-1">
                                                                                @if($numTrabajadores <= 2)
                                                                                    @foreach($trabajadoresAsignados as $index => $trabajador)
                                                                                        {{ $trabajador->nombre_completo }}@if($index < $numTrabajadores-1), @endif
                                                                                    @endforeach
                                                                                @else
                                                                                    {{ $trabajadoresAsignados[0]->nombre_completo }},
                                                                                    {{ $trabajadoresAsignados[1]->nombre_completo }}
                                                                                    y {{ $numTrabajadores - 2 }} más
                                                                                @endif
                                                                            </div>
                                                                        @else
                                                                            <span class="badge bg-warning">Sin asignar</span>
                                                                        @endif
                                                                    @else
                                                                        <span class="text-muted">No aplica</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-danger btn-sm eliminar-detalle" data-detalle-id="{{ $detalle->id }}">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                    <input type="hidden" name="detalles[{{ $detalle->id }}][eliminar]" value="0" id="eliminar-{{ $detalle->id }}">

                                                                    @if($detalle->articulo && $detalle->articulo->tipo == 'servicio')
                                                                        <button type="button" class="btn btn-primary btn-sm mt-1 editar-trabajadores" data-detalle-id="{{ $detalle->id }}" data-articulo-nombre="{{ $detalle->articulo->nombre }}">
                                                                            <i class="bi bi-people-fill"></i> Editar trabajadores
                                                                        </button>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif

                                        <!-- Formulario para agregar nuevos detalles -->
                                        <div class="card bg-light p-3 mb-3">
                                            <h6>Agregar Nuevo Detalle</h6>
                                            <div class="row">
                                                <div class="col-md-5 mb-2">
                                                    <label for="articulo">Artículo</label>
                                                    <select id="articulo" class="form-control select2-no-auto">
                                                        <option value="" selected>Seleccione un artículo</option>
                                                        @foreach($todosArticulos as $articulo)
                                                            <option value="{{ $articulo->id }}"
                                                                data-precio="{{ $articulo->precio_venta }}"
                                                                data-precio-venta="{{ $articulo->precio_venta }}"
                                                                data-stock="{{ $articulo->stock }}"
                                                                data-unidad="{{ $articulo->unidad->abreviatura ?? '' }}"
                                                                data-unidad-abreviatura="{{ $articulo->unidad->abreviatura ?? '' }}"
                                                                data-unidad-tipo="{{ $articulo->unidad->tipo ?? 'decimal' }}"
                                                                data-tipo="{{ $articulo->tipo }}">
                                                                {{ $articulo->codigo }} - {{ $articulo->nombre }} ({{ $config->currency_simbol }}.{{ number_format($articulo->precio_venta, 2) }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <label for="stock">Stock</label>
                                                    <div class="input-group">
                                                        <input type="text" id="stock" class="form-control" readonly>
                                                        <span class="input-group-text" id="unidad-abreviatura"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 mb-2">
                                                    <label for="cantidad-nuevo">Cantidad</label>
                                                    <div class="input-group">
                                                        <input type="number" id="cantidad-nuevo" class="form-control" min="0.01" step="0.01">
                                                        <span class="input-group-text" id="unidad-cantidad"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label for="descuento-nuevo">Descuento</label>
                                                    <select id="descuento-nuevo" class="form-control select2">
                                                        <option value="">Sin descuento</option>
                                                        @foreach($descuentos as $descuento)
                                                            <option value="{{ $descuento->id }}" data-porcentaje="{{ $descuento->porcentaje_descuento }}">
                                                                {{ $descuento->nombre }} ({{ $descuento->porcentaje_descuento }}%)
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Campo para seleccionar trabajadores de carwash -->
                                                <div class="col-md-12 mb-3" id="trabajadores-carwash-container" style="display:none">
                                                    <div class="card bg-info bg-opacity-10 p-3">
                                                        <h6 class="card-title mb-2">
                                                            <i class="bi bi-people-fill"></i> Asignar Trabajadores Car Wash
                                                        </h6>
                                                        <label for="trabajadores-carwash-nuevo" class="form-label">Seleccione los trabajadores que atenderán este servicio:</label>
                                                        <select id="trabajadores-carwash-nuevo" class="form-control select2" multiple>
                                                            @foreach($trabajadoresCarwash as $trabajador)
                                                                <option value="{{ $trabajador->id }}">{{ $trabajador->nombre_completo }} ({{ $trabajador->tipoTrabajador ? $trabajador->tipoTrabajador->nombre : 'Sin tipo' }})</option>
                                                            @endforeach
                                                        </select>
                                                        <small class="text-muted mt-2">
                                                            <i class="bi bi-info-circle"></i> Las comisiones se calcularán automáticamente para los trabajadores asignados
                                                        </small>
                                                    </div>
                                                </div>

                                                <div class="col-md-12 text-end mt-2">
                                                    <button type="button" id="agregar-detalle" class="btn btn-primary">
                                                        <i class="bi bi-plus-circle"></i> Agregar Detalle
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tabla para nuevos detalles -->
                                        <div id="nuevos-detalles-container" style="display: none;">
                                            <h6 class="mt-4 mb-3">Nuevos Detalles</h6>
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="tabla-nuevos-detalles">
                                                    <thead>
                                                        <tr>
                                                            <th>Artículo</th>
                                                            <th>Cantidad</th>
                                                            <th>Precio</th>
                                                            <th>Descuento</th>
                                                            <th>Subtotal</th>
                                                            <th>Trabajadores</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="nuevos-detalles">
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mt-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <a href="{{ url('ventas')  }}" class="btn btn-danger"><i class="bi bi-x-circle"></i> Cancelar</a>
                                                <button type="submit" class="btn btn-success" id="btn-guardar-cambios"><i class="bi bi-check-circle"></i> Guardar Cambios</button>
                                                <span id="mensaje-guardando" class="ms-2 d-none"><i class="bi bi-hourglass"></i> Guardando cambios...</span>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <h4 id="total-venta" class="text-primary">
                                                    Total: {{ $config->currency_simbol }}.{{ number_format($venta->detalleVentas->sum('sub_total'), 2) }}
                                                </h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar trabajadores (único modal global) -->
    <div class="modal fade" id="editar-trabajadores-modal" tabindex="-1" aria-labelledby="editarTrabajadoresModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editarTrabajadoresModalLabel">
                        <i class="bi bi-people-fill me-2"></i> Asignar Trabajadores Car Wash
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Servicio:</strong> <span id="servicio-nombre"></span></p>
                    <div class="form-group">
                        <label class="form-label">Seleccione los trabajadores:</label>
                        <select id="trabajadores-carwash-edit" class="form-control select2-modal" multiple>
                            @foreach($trabajadoresCarwash as $trabajador)
                                <option value="{{ $trabajador->id }}">
                                    {{ $trabajador->nombre_completo }} ({{ $trabajador->tipoTrabajador ? $trabajador->tipoTrabajador->nombre : 'Sin tipo' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted mt-2 d-block">
                            <i class="bi bi-info-circle"></i>
                            Las comisiones se calcularán automáticamente para los trabajadores asignados
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cerrar
                    </button>
                    <button type="button" class="btn btn-primary" id="guardar-trabajadores">
                        <i class="bi bi-check2-circle"></i> Aplicar cambios
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Depuración: Mostrar cuántos artículos se están cargando
        console.log('Cargando artículos: {{ ($articulos ?? collect())->count() }}');

        window.jirehVentaConfig = {
            currencySymbol: '{{ $config->currency_simbol }}',
            vehiculoIdOriginal: '{{ old("vehiculo_id", $venta->vehiculo_id ?? "") }}',
            csrfToken: '{{ csrf_token() }}',
            ventasUrl: '{{ route("admin.ventas.index") }}',
            ventaId: {{ $venta->id }},
            detallesOriginales: {!! json_encode(($venta->detalleVentas ?? collect())->mapWithKeys(function ($detalle) {
                return [$detalle->id => [
                    'precio_unitario' => (float) ($detalle->precio_venta ?? 0),
                    'cantidad' => (float) ($detalle->cantidad ?? 0), // Cambiado a float para consistencia
                    'descuento_id' => $detalle->descuento_id ?? null, // Añadir descuento_id
                    'descuento_porcentaje' => (float) ($detalle->descuento_porcentaje ?? 0),
                    'trabajadores_asignados' => ($detalle->trabajadoresCarwash ?? collect())->pluck('id')->toArray(),
                    'articulo_tipo' => $detalle->articulo->tipo ?? 'producto', // Añadir tipo de artículo
                    'unidad_tipo' => $detalle->articulo->unidad->tipo ?? 'decimal' // Añadir tipo de unidad
                ]];
            })) !!},
            articulos: {!! json_encode(
                ($articulos ?? collect())->map(function($articulo) use ($config) {
                    return [
                        'id' => $articulo->id,
                        'text' => mb_convert_encoding($articulo->nombre . ($articulo->codigo ? ' (Cod: ' . $articulo->codigo . ')' : '') . " (" . ($config->currency_simbol) . number_format($articulo->precio_venta, $config->numero_decimales_precio ?? 2) . ")", 'UTF-8', 'UTF-8'),
                        'precio_venta' => (float) $articulo->precio_venta,
                        'tipo_articulo' => $articulo->tipo,
                        'unidad_abreviatura' => $articulo->unidad ? ($articulo->unidad->abreviatura ?? '') : '', // Acceso más seguro
                        'unidad_tipo' => $articulo->unidad ? ($articulo->unidad->tipo ?? 'decimal') : 'decimal', // Acceso más seguro
                        'stock_disponible' => $articulo->stock_disponible_venta, // Asegúrate que este accesor exista y devuelva algo serializable
                        'es_servicio' => ($articulo->tipo == 'servicio')
                    ];
                })->values()->all(),
                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PARTIAL_OUTPUT_ON_ERROR
            ) ?: '[]' !!},
            // ... Asegúrate que $todosDescuentos se pasa a la vista y contiene id, nombre, porcentaje_descuento
            descuentos: {!! json_encode(
                ($todosDescuentos ?? collect())->map(function($descuento) {
                    return [
                        'id' => $descuento->id,
                        'text' => mb_convert_encoding($descuento->nombre . ' (' . (float)$descuento->porcentaje_descuento . '%)', 'UTF-8', 'UTF-8'),
                        'porcentaje' => (float) $descuento->porcentaje_descuento
                    ];
                })->values()->all(),
                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PARTIAL_OUTPUT_ON_ERROR
            ) ?: '[]' !!},
            allTrabajadoresParaModal: {!! json_encode(
                ($trabajadoresCarwash ?? collect())->map(function($trabajador) {
                    $tipoTrabajadorNombre = $trabajador->tipoTrabajador ? $trabajador->tipoTrabajador->nombre : '';
                    return [
                        'id' => $trabajador->id,
                        'text' => mb_convert_encoding($trabajador->nombre_completo . ($tipoTrabajadorNombre ? ' (' . $tipoTrabajadorNombre . ')' : ''), 'UTF-8', 'UTF-8')
                    ];
                })->values()->all(),
                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_PARTIAL_OUTPUT_ON_ERROR
            ) ?: '[]' !!},
            rutaActualizarDetalleBase: '{{ route("admin.ventas.detalle.update", ["venta" => $venta->id, "detalle" => "DETALLE_ID_PLACEHOLDER"]) }}',
            rutaEliminarDetalleBase: '{{ route("admin.ventas.detalle.destroy", ["venta" => $venta->id, "detalle" => "DETALLE_ID_PLACEHOLDER"]) }}',
            rutaRestaurarDetalleBase: '{{ route("admin.ventas.detalle.restore", ["venta" => $venta->id, "detalle" => "DETALLE_ID_PLACEHOLDER"]) }}',
            rutaObtenerArticulosParaVenta: '{{ route("api.articulos.para_venta") }}', // Ruta para la API de artículos
            rutaObtenerVehiculosBase: '{{ url("admin/clientes") }}/',
            rutaGuardarVenta: '{{ route("admin.ventas.update", $venta->id) }}'
        };
    </script>

    <script src="{{ asset('js/venta/edit-venta-main-simplified.js') }}?v={{ time() }}"></script>
    <script>
        $(document).ready(function() {
            console.log('Edit venta: Inicializando eventos...');
            
            // Variables para el modal de trabajadores
            let detalleActualEditando = null;
            
            // Rehabilitar botón de guardar si hay errores de validación (página cargada con errores)
            @if (count($errors) > 0)
                console.log('Se detectaron errores de validación, rehabilitando botón');
                $('#btn-guardar-cambios').prop('disabled', false)
                                        .html('<i class="bi bi-check-circle"></i> Guardar Cambios');
                $('#mensaje-guardando').addClass('d-none');
            @endif
            
            // Fix para el error de setSelectionRange en inputs number
            $(document).on('input focus blur', 'input[type="number"]', function(e) {
                try {
                    // Prevenir el error de setSelectionRange en inputs number
                    if (this.type === 'number' && typeof this.setSelectionRange === 'function') {
                        // No hacer nada - evitar setSelectionRange en inputs number
                    }
                } catch (error) {
                    console.warn('Error evitado en input number:', error);
                }
            });

            // Configurar eventos para la carga dinámica de vehículos
            $('#cliente_id').on('select2:select', function (e) {
                // Verificar que el evento tiene los datos necesarios
                if (!e.params || !e.params.data) {
                    console.warn('Evento select2:select sin datos válidos');
                    return;
                }
                
                var clienteId = e.params.data.id;
                console.log('Cliente seleccionado:', clienteId);
                
                if (clienteId) {
                    // Limpiar vehículos actuales
                    $('#vehiculo_id').empty().append('<option value="">Cargando vehículos...</option>');
                    
                    // Cargar vehículos del cliente
                    $.get('/api/clientes/' + clienteId + '/vehiculos')
                        .done(function(data) {
                            $('#vehiculo_id').empty().append('<option value="">Seleccione un vehículo</option>');
                            
                            if (data && data.length > 0) {
                                $.each(data, function(index, vehiculo) {
                                    var option = new Option(
                                        vehiculo.marca + ' ' + vehiculo.modelo + ' - ' + vehiculo.placa,
                                        vehiculo.id,
                                        false,
                                        false
                                    );
                                    $('#vehiculo_id').append(option);
                                });
                                
                                // Reseleccionar el vehículo si hay uno preservado
                                var vehiculoIdPreservado = '{{ old("vehiculo_id", $venta->vehiculo_id ?? "") }}';
                                if (vehiculoIdPreservado) {
                                    $('#vehiculo_id').val(vehiculoIdPreservado).trigger('change');
                                }
                            } else {
                                $('#vehiculo_id').append('<option value="">No hay vehículos disponibles</option>');
                            }
                        })
                        .fail(function(xhr, status, error) {
                            console.error('Error al cargar vehículos:', error);
                            $('#vehiculo_id').empty().append('<option value="">Error al cargar vehículos</option>');
                        });
                } else {
                    $('#vehiculo_id').empty().append('<option value="">Seleccione un vehículo</option>');
                }
            });
            
            // Preservar vehiculo_id si hay errores de validación - MÉTODO CORREGIDO
            var clienteIdPreservado = '{{ old("cliente_id", $venta->cliente_id ?? "") }}';
            var vehiculoIdPreservado = '{{ old("vehiculo_id", $venta->vehiculo_id ?? "") }}';
            
            if (clienteIdPreservado && vehiculoIdPreservado) {
                console.log('Preservando selección - Cliente:', clienteIdPreservado, 'Vehículo:', vehiculoIdPreservado);
                
                // Cargar vehículos directamente sin disparar el evento select2:select
                $.get('/api/clientes/' + clienteIdPreservado + '/vehiculos')
                    .done(function(data) {
                        $('#vehiculo_id').empty().append('<option value="">Seleccione un vehículo</option>');
                        
                        if (data && data.length > 0) {
                            $.each(data, function(index, vehiculo) {
                                var option = new Option(
                                    vehiculo.marca + ' ' + vehiculo.modelo + ' - ' + vehiculo.placa,
                                    vehiculo.id,
                                    false,
                                    vehiculo.id == vehiculoIdPreservado
                                );
                                $('#vehiculo_id').append(option);
                            });
                            $('#vehiculo_id').trigger('change');
                            console.log('Vehículo preservado correctamente:', vehiculoIdPreservado);
                        }
                    })
                    .fail(function() {
                        console.error('Error al cargar vehículos preservados');
                    });
            }

            // EVENTO: Submit del formulario - COMENTADO PARA EVITAR CONFLICTO CON JS EXTERNO
            /*
            $('#forma-editar-venta').on('submit', function(e) {
                console.log('🚀 FORMULARIO ENVIÁNDOSE - Iniciando proceso');
                
                // Obtener la URL de acción del formulario
                const formAction = $(this).attr('action');
                const formMethod = $(this).attr('method');
                console.log('URL de destino:', formAction);
                console.log('Método:', formMethod);
                
                // Validaciones básicas esenciales
                const clienteId = $('#cliente_id').val();
                const vehiculoId = $('#vehiculo_id').val();
                const fecha = $('#fecha').val();
                
                console.log('Datos básicos:', { 
                    clienteId: clienteId, 
                    vehiculoId: vehiculoId, 
                    fecha: fecha,
                    clienteIdType: typeof clienteId,
                    vehiculoIdType: typeof vehiculoId
                });
                
                // Validaciones mínimas con logging
                if (!clienteId || clienteId === '' || clienteId === '0') {
                    console.error('❌ Validación fallida: Cliente no válido');
                    alert('Debe seleccionar un cliente');
                    e.preventDefault();
                    return false;
                }
                
                if (!vehiculoId || vehiculoId === '' || vehiculoId === '0') {
                    console.error('❌ Validación fallida: Vehículo no válido');
                    alert('Debe seleccionar un vehículo');
                    e.preventDefault();
                    return false;
                }
                
                if (!fecha || fecha === '') {
                    console.error('❌ Validación fallida: Fecha no válida');
                    alert('Debe ingresar una fecha');
                    e.preventDefault();
                    return false;
                }
                
                console.log('✅ Validaciones básicas pasadas');
                
                // Verificar tokens CSRF
                const csrfToken = $('input[name="_token"]').val();
                const methodField = $('input[name="_method"]').val();
                console.log('CSRF Token:', csrfToken ? 'Presente' : 'FALTANTE');
                console.log('Method Field:', methodField ? methodField : 'FALTANTE');
                
                // Deshabilitar botón para prevenir doble envío
                const $btnGuardar = $('#btn-guardar-cambios');
                $btnGuardar.prop('disabled', true).text('Guardando...');
                
                // Agregar evento para detectar cambios en la página
                $(window).on('beforeunload.debug', function() {
                    console.log('🔄 PÁGINA DESCARGÁNDOSE - Navegando a nueva página o recargando');
                });
                
                // Timeout para detectar si la página no cambia
                setTimeout(function() {
                    console.log('⏰ TIMEOUT 3s: Verificando si el formulario se procesó');
                    if ($btnGuardar.prop('disabled')) {
                        console.warn('⚠️  El botón sigue deshabilitado después de 3s - Posible problema');
                        $btnGuardar.prop('disabled', false).text('Guardar Cambios');
                    }
                }, 3000);
                
                console.log('🚀 PERMITIENDO ENVÍO - Todo OK, enviando formulario');
                
                // Permitir el envío normal del formulario
                return true;
            });
            */

            // EVENTO: Eliminar detalle existente
            $(document).on('click', '.eliminar-detalle', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const detalleId = $(this).data('detalle-id');
                const $filaDetalle = $(`#detalle-row-${detalleId}`);
                const articuloNombre = $filaDetalle.find('td:first').text().trim();
                
                console.log('Eliminando detalle existente:', detalleId);
                
                // Usar SweetAlert para una mejor experiencia
                Swal.fire({
                    title: '¿Eliminar detalle?',
                    html: `¿Está seguro de que desea eliminar este detalle?<br><strong>${articuloNombre}</strong>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="bi bi-trash"></i> Sí, eliminar',
                    cancelButtonText: '<i class="bi bi-x-circle"></i> Cancelar',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        eliminarDetalleExistente(detalleId);
                        
                        // Mostrar mensaje de confirmación
                        Swal.fire({
                            title: '¡Eliminado!',
                            text: 'El detalle ha sido marcado para eliminación.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                });
            });
            
            // Función para eliminar detalle existente
            function eliminarDetalleExistente(detalleId) {
                console.log('Procesando eliminación del detalle:', detalleId);
                
                // Verificar que existen los elementos necesarios
                const $eliminarInput = $(`#eliminar-${detalleId}`);
                const $filaDetalle = $(`#detalle-row-${detalleId}`);
                
                if ($eliminarInput.length === 0) {
                    console.error('No se encontró el input de eliminación para detalle:', detalleId);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudo marcar el detalle para eliminación',
                        icon: 'error',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }
                
                if ($filaDetalle.length === 0) {
                    console.error('No se encontró la fila del detalle:', detalleId);
                    Swal.fire({
                        title: 'Error',
                        text: 'No se encontró la fila del detalle',
                        icon: 'error',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }
                
                // Marcar como eliminado
                $eliminarInput.val('1');
                console.log('Input eliminación marcado:', $eliminarInput.attr('name'), '=', $eliminarInput.val());
                
                // Agregar clases para identificación y ocultar con animación suave
                $filaDetalle.addClass('detalle-oculto-por-eliminacion bg-danger bg-opacity-25')
                           .fadeOut(300, function() {
                               // Actualizar total después de la animación
                               if (typeof window.actualizarTotalVenta === 'function') {
                                   window.actualizarTotalVenta();
                               }
                           });
                
                // Marcar que hay cambios
                if (typeof window.marcarCambio === 'function') {
                    window.marcarCambio();
                }
                
                console.log('Detalle marcado para eliminación exitosamente:', detalleId);
            }

            // EVENTO: Editar trabajadores del modal
            $(document).on('click', '.editar-trabajadores', function() {
                const detalleId = $(this).data('detalle-id');
                const articuloNombre = $(this).data('articulo-nombre');
                
                console.log('🔧 Abriendo modal para detalle:', detalleId);
                console.log('📋 Artículo:', articuloNombre);
                
                detalleActualEditando = detalleId;
                
                // Configurar el modal
                $('#servicio-nombre').text(articuloNombre || 'Servicio');
                
                // Limpiar select de trabajadores primero
                $('#trabajadores-carwash-edit').val(null).trigger('change');
                console.log('🧹 Select de trabajadores limpiado');
                
                // Obtener trabajadores asignados actualmente
                const trabajadoresAsignados = [];
                $(`#trabajadores-${detalleId} input[name="trabajadores_carwash[${detalleId}][]"]`).each(function() {
                    const trabajadorId = $(this).val();
                    if (trabajadorId && trabajadorId.trim() !== '') {
                        trabajadoresAsignados.push(trabajadorId);
                    }
                });
                
                console.log('👥 Trabajadores asignados encontrados:', trabajadoresAsignados);
                
                // Verificar que las opciones existen en el select
                console.log('🔍 Verificando opciones disponibles...');
                const opcionesDisponibles = [];
                $('#trabajadores-carwash-edit option').each(function() {
                    opcionesDisponibles.push($(this).val());
                });
                console.log('📋 Opciones en select:', opcionesDisponibles);
                
                // Preseleccionar trabajadores en el modal
                if (trabajadoresAsignados.length > 0) {
                    console.log('🎯 Preseleccionando trabajadores:', trabajadoresAsignados);
                    $('#trabajadores-carwash-edit').val(trabajadoresAsignados).trigger('change');
                    
                    // Verificar que se seleccionaron correctamente
                    setTimeout(() => {
                        const seleccionados = $('#trabajadores-carwash-edit').val() || [];
                        console.log('✅ Trabajadores seleccionados en modal:', seleccionados);
                    }, 100);
                } else {
                    console.log('ℹ️ No hay trabajadores previamente asignados');
                }
                
                // Mostrar el modal
                $('#editar-trabajadores-modal').modal('show');
            });

            // EVENTO: Modal mostrado - Re-inicializar Select2 si es necesario
            $('#editar-trabajadores-modal').on('shown.bs.modal', function() {
                console.log('🔧 Modal mostrado - Verificando Select2...');
                
                // Verificar si Select2 está funcionando
                if (!$('#trabajadores-carwash-edit').hasClass('select2-hidden-accessible')) {
                    console.log('⚠️ Select2 no inicializado en modal - Re-inicializando...');
                    $('#trabajadores-carwash-edit').select2({
                        dropdownParent: $('#editar-trabajadores-modal'),
                        language: {
                            noResults: () => "No se encontraron resultados",
                            searching: () => "Buscando..."
                        },
                        width: '100%',
                        closeOnSelect: false,
                        placeholder: "Seleccione trabajadores"
                    });
                }
                
                // Forzar focus en el select para asegurar que funcione
                setTimeout(() => {
                    $('#trabajadores-carwash-edit').focus();
                }, 200);
            });

            // EVENTO: Guardar trabajadores del modal
            $('#guardar-trabajadores').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('🔧 INICIANDO GUARDADO DE TRABAJADORES');
                console.log('Detalle actual editando:', detalleActualEditando);
                
                if (!detalleActualEditando) {
                    console.error('❌ No hay detalle seleccionado para editar');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', 'No hay detalle seleccionado para editar', 'error');
                    } else {
                        alert('Error: No hay detalle seleccionado para editar');
                    }
                    return;
                }

                const trabajadoresSeleccionados = $('#trabajadores-carwash-edit').val() || [];
                console.log('📋 Trabajadores seleccionados:', trabajadoresSeleccionados);
                console.log('📊 Cantidad de trabajadores:', trabajadoresSeleccionados.length);

                // Determinar si es detalle nuevo o existente
                const esDetalleNuevo = detalleActualEditando.toString().startsWith('nuevo-');
                const containerId = esDetalleNuevo ? 
                    `trabajadores-${detalleActualEditando}` : 
                    `trabajadores-${detalleActualEditando}`;
                const textoContainerId = esDetalleNuevo ?
                    `trabajadores-text-${detalleActualEditando}` :
                    `trabajadores-text-${detalleActualEditando}`;
                
                console.log('🔍 Tipo de detalle:', esDetalleNuevo ? 'NUEVO' : 'EXISTENTE');
                console.log('🔍 Container ID:', containerId);
                console.log('🔍 Texto container ID:', textoContainerId);

                // Verificar que el container de trabajadores existe
                let $containerTrabajadores = $(`#${containerId}`);
                console.log('🔍 Buscando container:', `#${containerId}`);
                console.log('📦 Container encontrado:', $containerTrabajadores.length > 0);
                
                if ($containerTrabajadores.length === 0) {
                    console.error('❌ No se encontró el container de trabajadores para detalle:', detalleActualEditando);
                    
                    // Buscar containers alternativos
                    console.log('🔍 Buscando containers alternativos...');
                    const selectorAlternativo = esDetalleNuevo ? 
                        `tr[id="nuevo-detalle-${detalleActualEditando.replace('nuevo-', '')}"] div[id^="trabajadores-"]` :
                        `tr[id="detalle-row-${detalleActualEditando}"] div[id^="trabajadores-"]`;
                    
                    const $containerAlternativo = $(selectorAlternativo);
                    console.log('🔍 Selector alternativo:', selectorAlternativo);
                    console.log('📦 Container alternativo encontrado:', $containerAlternativo.length);
                    
                    if ($containerAlternativo.length === 0) {
                        console.error('❌ No se encontró ningún container para trabajadores');
                        if (typeof Swal !== 'undefined') {
                            Swal.fire('Error', 'No se encontró el container de trabajadores para este detalle', 'error');
                        } else {
                            alert('Error: No se encontró el container de trabajadores');
                        }
                        return;
                    } else {
                        // Usar el container alternativo
                        console.log('✅ Usando container alternativo');
                        $containerTrabajadores = $containerAlternativo;
                    }
                }

                // Verificar inputs existentes antes de limpiar
                const $inputsExistentes = $containerTrabajadores.find('input[name*="trabajadores_carwash"]');
                console.log('🗂️ Inputs existentes antes de limpiar:', $inputsExistentes.length);
                $inputsExistentes.each(function() {
                    console.log('  - Input existente:', this.name, '=', this.value);
                });

                // Verificar que el container está dentro del formulario
                const $formulario = $('#forma-editar-venta');
                const containerEnFormulario = $formulario.find($containerTrabajadores).length > 0;
                console.log('📝 Container está dentro del formulario:', containerEnFormulario);
                
                if (!containerEnFormulario) {
                    console.error('❌ El container no está dentro del formulario principal');
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', 'El container de trabajadores no está dentro del formulario', 'error');
                    }
                    return;
                }

                // Limpiar trabajadores existentes del container específico
                $containerTrabajadores.empty();
                console.log('🧹 Container limpiado completamente');

                // Agregar los nuevos trabajadores con verificación mejorada
                let inputsCreados = 0;
                let inputsHtml = '';
                
                console.log('➕ Creando nuevos inputs para trabajadores...');
                trabajadoresSeleccionados.forEach(function(trabajadorId, index) {
                    console.log(`  - Procesando trabajador ${index + 1}:`, trabajadorId);
                    
                    if (trabajadorId && trabajadorId.toString().trim() !== '') {
                        // Generar el nombre del input según el tipo de detalle
                        let inputName;
                        if (esDetalleNuevo) {
                            const detalleNumero = detalleActualEditando.replace('nuevo-', '');
                            inputName = `nuevos_detalles[${detalleNumero}][trabajadores_carwash][]`;
                        } else {
                            inputName = `trabajadores_carwash[${detalleActualEditando}][]`;
                        }
                        
                        const inputHtml = `<input type="hidden" name="${inputName}" value="${trabajadorId}">`;
                        
                        // Crear el elemento y agregarlo
                        const $nuevoInput = $(inputHtml);
                        $containerTrabajadores.append($nuevoInput);
                        
                        // Verificar que se agregó correctamente
                        const inputAgregado = $containerTrabajadores.find(`input[value="${trabajadorId}"]`).length > 0;
                        console.log(`    ✅ Input creado y verificado:`, inputHtml, 'Agregado:', inputAgregado);
                        
                        if (inputAgregado) {
                            inputsCreados++;
                            inputsHtml += inputHtml + '\n';
                        } else {
                            console.error(`    ❌ Error: Input no se agregó correctamente para trabajador ${trabajadorId}`);
                        }
                    } else {
                        console.log(`    ⚠️ ID trabajador inválido:`, trabajadorId);
                    }
                });

                console.log(`📊 RESUMEN CREACIÓN: ${inputsCreados} inputs creados exitosamente de ${trabajadoresSeleccionados.length} seleccionados`);
                console.log('📝 HTML de inputs creados:\n', inputsHtml);

                // Verificar que los inputs se crearon correctamente
                const $inputsNuevos = $containerTrabajadores.find('input[name*="trabajadores_carwash"]');
                console.log('✅ Verificación final - Inputs en DOM:', $inputsNuevos.length);
                $inputsNuevos.each(function() {
                    console.log('  - Input verificado:', this.name, '=', this.value);
                });

                // Actualizar el texto mostrado en la interfaz
                const numTrabajadores = trabajadoresSeleccionados.length;
                let textoTrabajadores = '';

                if (numTrabajadores > 0) {
                    textoTrabajadores = `<span class="badge bg-info">${numTrabajadores} trabajador(es)</span>`;
                    
                    // Mostrar nombres si hay pocos trabajadores
                    if (numTrabajadores <= 3) {
                        const nombres = [];
                        trabajadoresSeleccionados.forEach(function(trabajadorId) {
                            const $option = $(`#trabajadores-carwash-edit option[value="${trabajadorId}"]`);
                            if ($option.length) {
                                let nombreCompleto = $option.text().trim();
                                // Remover texto entre paréntesis al final si existe
                                nombreCompleto = nombreCompleto.replace(/\s*\([^)]*\)\s*$/, '');
                                nombres.push(nombreCompleto);
                            }
                        });
                        if (nombres.length > 0) {
                            textoTrabajadores += `<div class="small mt-1">${nombres.join(', ')}</div>`;
                        }
                    }
                } else {
                    textoTrabajadores = '<span class="badge bg-warning">Sin asignar</span>';
                }

                // Actualizar el texto en la celda correspondiente
                const $textoContainer = $(`#${textoContainerId}`);
                console.log('🎨 Actualizando interfaz visual...');
                console.log('📍 Buscando container de texto:', `#${textoContainerId}`);
                console.log('📦 Container de texto encontrado:', $textoContainer.length > 0);
                
                if ($textoContainer.length > 0) {
                    // Para nuevos detalles, mantener el container de inputs y botón
                    if (esDetalleNuevo) {
                        const $containerInputs = $textoContainer.find(`#${containerId}`);
                        const $botonEditar = $textoContainer.find('.editar-trabajadores');
                        
                        $textoContainer.html(textoTrabajadores);
                        
                        // Re-agregar el container y botón si existen
                        if ($containerInputs.length > 0) {
                            $textoContainer.prepend($containerInputs);
                        }
                        if ($botonEditar.length > 0) {
                            $textoContainer.append($botonEditar);
                        }
                    } else {
                        // Para detalles existentes, solo actualizar el texto después del container
                        const $containerInputs = $textoContainer.find(`#${containerId}`);
                        const $botonEditar = $textoContainer.find('.editar-trabajadores');
                        
                        $textoContainer.empty();
                        
                        if ($containerInputs.length > 0) {
                            $textoContainer.append($containerInputs);
                        }
                        $textoContainer.append(textoTrabajadores);
                        if ($botonEditar.length > 0) {
                            $textoContainer.append($botonEditar);
                        }
                    }
                    
                    console.log('✅ Texto actualizado en interfaz:', textoTrabajadores);
                } else {
                    console.warn('⚠️ No se encontró el container de texto para trabajadores:', detalleActualEditando);
                    
                    // Buscar contenedor alternativo según el tipo
                    const selectorTextoAlternativo = esDetalleNuevo ?
                        `tr[id="nuevo-detalle-${detalleActualEditando.replace('nuevo-', '')}"] td[id^="trabajadores-text-"]` :
                        `tr[id="detalle-row-${detalleActualEditando}"] td[id^="trabajadores-text-"]`;
                    
                    const $textoAlternativo = $(selectorTextoAlternativo);
                    if ($textoAlternativo.length > 0) {
                        $textoAlternativo.html(textoTrabajadores);
                        console.log('✅ Usando container de texto alternativo');
                    }
                }

                // Marcar que hubo cambios
                if (typeof window.marcarCambio === 'function') {
                    window.marcarCambio();
                    console.log('🔄 Cambios marcados');
                }

                // Mostrar mensaje de éxito
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Trabajadores actualizados',
                        text: `Se asignaron ${numTrabajadores} trabajador(es) al servicio`,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }

                // Cerrar modal
                $('#editar-trabajadores-modal').modal('hide');
                
                console.log('🎉 Trabajadores actualizados exitosamente para detalle:', detalleActualEditando);
                console.log('📊 ESTADO FINAL:');
                console.log('  - Inputs creados:', inputsCreados);
                console.log('  - Trabajadores asignados:', trabajadoresSeleccionados);
                console.log('  - Container actualizado:', $containerTrabajadores.length > 0);
                
                // Verificación final después de un pequeño delay para asegurar que el DOM se actualizó
                setTimeout(function() {
                    console.log('🔍 VERIFICACIÓN FINAL POST-GUARDADO:');
                    const $inputsFinales = $containerTrabajadores.find('input[name*="trabajadores_carwash"]');
                    console.log('  - Inputs finales en container:', $inputsFinales.length);
                    
                    $inputsFinales.each(function(index) {
                        console.log(`    ${index + 1}. ${this.name} = "${this.value}"`);
                    });
                    
                    // Verificar también en todo el formulario
                    const busquedaInputs = esDetalleNuevo ?
                        `#forma-editar-venta input[name*="nuevos_detalles[${detalleActualEditando.replace('nuevo-', '')}][trabajadores_carwash]"]` :
                        `#forma-editar-venta input[name*="trabajadores_carwash[${detalleActualEditando}]"]`;
                    
                    const $todosLosInputs = $(busquedaInputs);
                    console.log('  - Búsqueda inputs:', busquedaInputs);
                    console.log('  - Total inputs en formulario para este detalle:', $todosLosInputs.length);
                    
                    if ($todosLosInputs.length !== inputsCreados) {
                        console.error('❌ DISCREPANCIA: Se esperaban', inputsCreados, 'inputs pero se encontraron', $todosLosInputs.length);
                    } else {
                        console.log('✅ ÉXITO: Cantidad de inputs coincide con lo esperado');
                    }
                }, 100);
                
                console.log('🔧 FIN DEL PROCESO DE GUARDADO');
                
                // Reset variable
                detalleActualEditando = null;
            });

            // EVENTO: Agregar nuevo detalle
            let contadorDetalles = 0;
            $('#agregar-detalle').on('click', function() {
                console.log('🆕 INICIANDO AGREGAR NUEVO DETALLE');
                console.log('🔍 Estado del botón:', $(this).length, '- Deshabilitado:', $(this).prop('disabled'));
                
                // Verificar que los elementos existen
                console.log('🔍 Verificando elementos:');
                console.log('  - Select artículo:', $('#articulo').length);
                console.log('  - Input cantidad:', $('#cantidad-nuevo').length);
                console.log('  - Select descuento:', $('#descuento-nuevo').length);
                console.log('  - Container nuevos detalles:', $('#nuevos-detalles').length);
                
                // Obtener datos del formulario
                const articuloId = $('#articulo').val();
                const cantidad = parseFloat($('#cantidad-nuevo').val());
                const descuentoId = $('#descuento-nuevo').val();
                const trabajadoresCarwash = $('#trabajadores-carwash-nuevo').val() || [];
                
                console.log('📋 Datos del nuevo detalle:', {
                    articuloId: articuloId,
                    cantidad: cantidad,
                    descuentoId: descuentoId,
                    trabajadores: trabajadoresCarwash
                });

                // Validaciones básicas
                if (!articuloId) {
                    console.error('❌ Artículo no seleccionado');
                    Swal.fire('Error', 'Debe seleccionar un artículo', 'error');
                    return;
                }

                if (!cantidad || cantidad <= 0) {
                    console.error('❌ Cantidad inválida');
                    Swal.fire('Error', 'Debe ingresar una cantidad válida', 'error');
                    return;
                }

                // Obtener datos del artículo seleccionado
                const $articuloOption = $('#articulo option:selected');
                const precioVenta = parseFloat($articuloOption.data('precio-venta'));
                const tipoArticulo = $articuloOption.data('tipo');
                const unidadAbreviatura = $articuloOption.data('unidad-abreviatura');
                const articuloNombre = $articuloOption.text();

                console.log('📦 Datos del artículo:', {
                    precio: precioVenta,
                    tipo: tipoArticulo,
                    unidad: unidadAbreviatura,
                    nombre: articuloNombre
                });

                // Calcular totales
                let subtotalSinDescuento = precioVenta * cantidad;
                let montoDescuento = 0;
                let porcentajeDescuento = 0;
                let nombreDescuento = '';

                if (descuentoId) {
                    const $descuentoOption = $('#descuento-nuevo option:selected');
                    porcentajeDescuento = parseFloat($descuentoOption.data('porcentaje'));
                    nombreDescuento = $descuentoOption.text();
                    montoDescuento = subtotalSinDescuento * (porcentajeDescuento / 100);
                }

                const subtotalFinal = subtotalSinDescuento - montoDescuento;

                console.log('💰 Cálculos:', {
                    subtotalSinDescuento: subtotalSinDescuento,
                    montoDescuento: montoDescuento,
                    subtotalFinal: subtotalFinal
                });

                // Generar ID único para el nuevo detalle
                contadorDetalles++;
                const nuevoDetalleIndex = contadorDetalles;

                // Crear inputs ocultos para el nuevo detalle
                let inputsHtml = `
                    <input type="hidden" name="nuevos_detalles[${nuevoDetalleIndex}][articulo_id]" value="${articuloId}">
                    <input type="hidden" name="nuevos_detalles[${nuevoDetalleIndex}][cantidad]" value="${cantidad}">
                    <input type="hidden" name="nuevos_detalles[${nuevoDetalleIndex}][precio_unitario]" value="${precioVenta}">
                    <input type="hidden" name="nuevos_detalles[${nuevoDetalleIndex}][sub_total]" value="${subtotalFinal}">
                    <input type="hidden" name="nuevos_detalles[${nuevoDetalleIndex}][descuento_id]" value="${descuentoId || ''}">
                `;

                // Agregar inputs de trabajadores si es servicio
                let inputsTrabajadores = '';
                let textoTrabajadores = '<span class="text-muted">No aplica</span>';
                let containerTrabajadores = '';
                let botonEditarTrabajadores = '';
                
                if (tipoArticulo === 'servicio') {
                    // Crear container para trabajadores (similar a detalles existentes)
                    containerTrabajadores = `<div id="trabajadores-nuevo-${nuevoDetalleIndex}">`;
                    
                    if (trabajadoresCarwash.length > 0) {
                        trabajadoresCarwash.forEach(function(trabajadorId) {
                            containerTrabajadores += `<input type="hidden" name="nuevos_detalles[${nuevoDetalleIndex}][trabajadores_carwash][]" value="${trabajadorId}">`;
                        });
                        textoTrabajadores = `<span class="badge bg-info">${trabajadoresCarwash.length} trabajador(es)</span>`;
                        
                        // Mostrar nombres si hay pocos trabajadores
                        if (trabajadoresCarwash.length <= 3) {
                            const nombres = [];
                            trabajadoresCarwash.forEach(function(trabajadorId) {
                                const $option = $(`#trabajadores-carwash-nuevo option[value="${trabajadorId}"]`);
                                if ($option.length) {
                                    let nombreCompleto = $option.text().trim();
                                    nombreCompleto = nombreCompleto.replace(/\s*\([^)]*\)\s*$/, '');
                                    nombres.push(nombreCompleto);
                                }
                            });
                            if (nombres.length > 0) {
                                textoTrabajadores += `<div class="small mt-1">${nombres.join(', ')}</div>`;
                            }
                        }
                    } else {
                        textoTrabajadores = '<span class="badge bg-warning">Sin asignar</span>';
                    }
                    
                    containerTrabajadores += '</div>';
                    
                    // Crear botón para editar trabajadores
                    botonEditarTrabajadores = `
                        <button type="button" class="btn btn-primary btn-sm mt-1 editar-trabajadores" 
                                data-detalle-id="nuevo-${nuevoDetalleIndex}" 
                                data-articulo-nombre="${articuloNombre}">
                            <i class="bi bi-people-fill"></i> Editar trabajadores
                        </button>
                    `;
                }

                inputsHtml += containerTrabajadores;

                // Crear fila de la tabla
                const nuevaFila = `
                    <tr id="nuevo-detalle-${nuevoDetalleIndex}">
                        <td>
                            ${articuloNombre}
                            ${inputsHtml}
                        </td>
                        <td>${cantidad} ${unidadAbreviatura}</td>
                        <td>{{ $config->currency_simbol }}.${precioVenta.toFixed(2)}</td>
                        <td>${nombreDescuento || 'Sin descuento'}</td>
                        <td>{{ $config->currency_simbol }}.${subtotalFinal.toFixed(2)}</td>
                        <td id="trabajadores-text-nuevo-${nuevoDetalleIndex}">
                            ${containerTrabajadores}
                            ${textoTrabajadores}
                            ${botonEditarTrabajadores}
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm eliminar-nuevo-detalle" data-index="${nuevoDetalleIndex}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                // Agregar la fila a la tabla
                $('#nuevos-detalles').append(nuevaFila);
                $('#nuevos-detalles-container').show();

                console.log('✅ Nuevo detalle agregado exitosamente:', nuevoDetalleIndex);

                // Limpiar formulario
                $('#articulo').val('').trigger('change');
                $('#cantidad-nuevo').val('');
                $('#descuento-nuevo').val('').trigger('change');
                $('#trabajadores-carwash-nuevo').val(null).trigger('change');
                $('#trabajadores-carwash-container').hide();
                $('#stock').val('');
                $('#unidad-abreviatura').text('');
                $('#unidad-cantidad').text('');

                // Actualizar total si existe la función
                if (typeof window.actualizarTotalVenta === 'function') {
                    window.actualizarTotalVenta();
                }

                // Marcar cambios
                if (typeof window.marcarCambio === 'function') {
                    window.marcarCambio();
                }

                // Mostrar mensaje de éxito
                Swal.fire({
                    title: 'Detalle agregado',
                    text: 'El nuevo detalle se agregó correctamente',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });

            // EVENTO: Eliminar nuevo detalle
            $(document).on('click', '.eliminar-nuevo-detalle', function() {
                const index = $(this).data('index');
                const $fila = $(`#nuevo-detalle-${index}`);
                
                console.log('🗑️ Eliminando nuevo detalle:', index);
                
                $fila.fadeOut(300, function() {
                    $(this).remove();
                    
                    // Ocultar container si no hay más detalles nuevos
                    if ($('#nuevos-detalles tr').length === 0) {
                        $('#nuevos-detalles-container').hide();
                    }
                    
                    // Actualizar total
                    if (typeof window.actualizarTotalVenta === 'function') {
                        window.actualizarTotalVenta();
                    }
                });
            });

            // EVENTO: Cambio en select de artículo para mostrar/ocultar trabajadores
            $('#articulo').on('change', function() {
                const $selected = $(this).find('option:selected');
                const tipoArticulo = $selected.data('tipo');
                const precio = $selected.data('precio-venta');
                const stock = $selected.data('stock');
                const unidadAbreviatura = $selected.data('unidad-abreviatura');
                const unidadTipo = $selected.data('unidad-tipo');

                console.log('📦 Artículo seleccionado:', {
                    tipo: tipoArticulo,
                    precio: precio,
                    stock: stock,
                    unidad: unidadAbreviatura,
                    unidadTipo: unidadTipo
                });

                // Mostrar/ocultar trabajadores según el tipo
                if (tipoArticulo === 'servicio') {
                    $('#trabajadores-carwash-container').show();
                } else {
                    $('#trabajadores-carwash-container').hide();
                    $('#trabajadores-carwash-nuevo').val(null).trigger('change');
                }

                // Actualizar información de stock y unidades
                $('#stock').val(stock || '');
                $('#unidad-abreviatura').text(unidadAbreviatura || '');
                $('#unidad-cantidad').text(unidadAbreviatura || '');

                // Configurar input de cantidad según tipo de unidad
                const $cantidadInput = $('#cantidad-nuevo');
                if (unidadTipo === 'unidad') {
                    $cantidadInput.attr('min', '1').attr('step', '1');
                } else {
                    $cantidadInput.attr('min', '0.01').attr('step', '0.01');
                }
            });

            // Función específica para debugging de un detalle
            window.debugDetalleTrabajadores = function(detalleId) {
                console.log(`🔍 === DEBUG DETALLE ${detalleId} ===`);
                
                const container = $(`#trabajadores-${detalleId}`);
                console.log('Container encontrado:', container.length > 0);
                
                if (container.length > 0) {
                    const inputs = container.find('input[name*="trabajadores_carwash"]');
                    console.log('Inputs en container:', inputs.length);
                    
                    inputs.each(function(index) {
                        console.log(`  ${index + 1}. ${this.name} = "${this.value}"`);
                    });
                    
                    // Verificar si está dentro del formulario
                    const enFormulario = $('#forma-editar-venta').find(container).length > 0;
                    console.log('Container dentro del formulario:', enFormulario);
                }
                
                // Buscar inputs sueltos para este detalle
                const inputsSueltos = $(`input[name*="trabajadores_carwash[${detalleId}]"]`).not(container.find('input'));
                if (inputsSueltos.length > 0) {
                    console.log('⚠️ Inputs sueltos encontrados (fuera del container):', inputsSueltos.length);
                    inputsSueltos.each(function(index) {
                        console.log(`  Suelto ${index + 1}. ${this.name} = "${this.value}"`);
                    });
                }
                
                return container;
            };

            console.log('✅ Todos los eventos JavaScript inicializados correctamente');
        });
    </script>
    
    <!-- Script de debugging integrado -->
    <script src="{{ asset('js/debugging/form-debug-integrated.js') }}"></script>
@endsection
