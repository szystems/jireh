<!-- Row start -->
<div class="row gx-3">
    <div class="col-xl-12">
        <div class="card card-background-mask-info">
            <div class="card-body">
                <form action="{{ url('ingresos')  }}" method="GET">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="fecha_desde" class="form-label">Fecha Desde</label>
                            <input type="date" class="form-control" name="fecha_desde" value="{{ request('fecha_desde', \Carbon\Carbon::now()->subDays(30)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                            <input type="date" class="form-control" name="fecha_hasta" value="{{ request('fecha_hasta', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4">
                            <label for="numero_factura" class="form-label">Número de Factura</label>
                            <input type="text" class="form-control" name="numero_factura" value="{{ request('numero_factura') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="proveedor" class="form-label">Proveedor</label>
                            <select name="proveedor" class="form-control select2">
                                <option value=""{{ request('proveedor') == null ? 'selected' : '' }}>Seleccione un proveedor</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" {{ request('proveedor') == $proveedor->id ? 'selected' : '' }}>{{ $proveedor->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="tipo_compra" class="form-label">Tipo de Compra</label>
                            <select name="tipo_compra" class="form-control select2">
                                <option value=""{{ request('tipo_compra') == null ? 'selected' : '' }}>Seleccione un tipo de compra</option>
                                <option value="Car Wash" {{ request('tipo_compra') == 'Car Wash' ? 'selected' : '' }}>Car Wash</option>
                                <option value="CDS" {{ request('tipo_compra') == 'CDS' ? 'selected' : '' }}>CDS</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="usuario" class="form-label">Usuario</label>
                            <select name="usuario" class="form-control select2">
                                <option value=""{{ request('usuario') == null ? 'selected' : '' }}>Seleccione un usuario</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ request('usuario') == $usuario->id ? 'selected' : '' }}>{{ $usuario->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
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
        $('.select2').select2();
    });
</script>
