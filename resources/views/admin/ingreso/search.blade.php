<!-- Row start -->
<div class="row gx-3 mb-3">
    <div class="col-xl-12">
        <div class="card card-background-mask-info">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtrar Ingresos</h5>
            </div>
            <div class="card-body">
                <form action="{{ url('ingresos')  }}" method="GET">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="fecha_desde" class="form-label"><i class="bi bi-calendar-event"></i> Fecha Desde</label>
                            <input type="date" class="form-control" name="fecha_desde" value="{{ request('fecha_desde', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="fecha_hasta" class="form-label"><i class="bi bi-calendar-event"></i> Fecha Hasta</label>
                            <input type="date" class="form-control" name="fecha_hasta" value="{{ request('fecha_hasta', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="numero_factura" class="form-label"><i class="bi bi-receipt"></i> Número de Factura</label>
                            <input type="text" class="form-control" name="numero_factura" value="{{ request('numero_factura') }}" placeholder="Buscar por número de factura">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="proveedor" class="form-label"><i class="bi bi-shop"></i> Proveedor</label>
                            <select name="proveedor" class="form-control select2" data-placeholder="Seleccione un proveedor">
                                <option value=""{{ request('proveedor') == null ? 'selected' : '' }}>Todos los proveedores</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" {{ request('proveedor') == $proveedor->id ? 'selected' : '' }}>{{ $proveedor->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tipo_compra" class="form-label"><i class="bi bi-tag"></i> Tipo de Compra</label>
                            <select name="tipo_compra" class="form-control select2">
                                <option value=""{{ request('tipo_compra') == null ? 'selected' : '' }}>Todos los tipos</option>
                                <option value="Car Wash" {{ request('tipo_compra') == 'Car Wash' ? 'selected' : '' }}>Car Wash</option>
                                <option value="CDS" {{ request('tipo_compra') == 'CDS' ? 'selected' : '' }}>CDS</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="usuario" class="form-label"><i class="bi bi-person"></i> Usuario</label>
                            <select name="usuario" class="form-control select2">
                                <option value=""{{ request('usuario') == null ? 'selected' : '' }}>Todos los usuarios</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ request('usuario') == $usuario->id ? 'selected' : '' }}>{{ $usuario->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        @if(request()->anyFilled(['numero_factura', 'proveedor', 'tipo_compra', 'usuario']))
                            <a href="{{ url('ingresos') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Limpiar
                            </a>
                        @else
                            <div></div>
                        @endif
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Row end -->

<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: "bootstrap-5",
            width: '100%'
        });
    });
</script>
