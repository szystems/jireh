@extends('layouts.admin')

@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-car-front"></i>
                </div>
                <div class="page-title">
                    <h5>Vehículos</h5>
                </div>
            </div>
            <!-- Date range start -->
            <div class="d-flex align-items-end d-none d-sm-block">
                <h6 class="float-end text-light" id="reloj"></h6>
            </div>
        </div>
        <!-- Main header ends -->

        <!-- Content wrapper start -->
        <div class="content-wrapper">
            <!-- Filtros avanzados -->
            <div class="row gx-3 mb-3">
                <div class="col-xl-12">
                    <div class="card card-background-mask-info">
                        <div class="card-body">
                            <form action="{{ url('vehiculos') }}" method="GET">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <input class="form-control" placeholder="Buscar por marca, modelo, placa..." name="fvehiculo" value="{{ $queryVehiculo ?? '' }}"/>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <select class="form-select" name="fano">
                                            <option value="">Todos los años</option>
                                            @for ($year = date('Y'); $year >= 2000; $year--)
                                                <option value="{{ $year }}" {{ isset($queryAno) && $queryAno == $year ? 'selected' : '' }}>{{ $year }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <select class="form-select select2-clientes" id="fcliente" name="fcliente" style="width: 100%;">
                                            <option value="">Todos los clientes</option>
                                            @foreach($clientes ?? [] as $cliente)
                                                <option value="{{ $cliente->id }}" {{ isset($queryCliente) && $queryCliente == $cliente->id ? 'selected' : '' }}>
                                                    {{ $cliente->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <button class="btn btn-primary w-100" type="submit">
                                            <i class="bi bi-search"></i> Filtrar
                                        </button>
                                    </div>
                                </div>
                                @if($queryVehiculo || $queryAno || $queryCliente)
                                    <div class="mt-2">
                                        <a href="{{ url('vehiculos') }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-x-circle"></i> Limpiar filtros
                                        </a>
                                        <span class="ms-2">
                                            Filtros activos:
                                            @if($queryVehiculo)<span class="badge bg-info me-1">Texto: {{ $queryVehiculo }}</span>@endif
                                            @if($queryAno)<span class="badge bg-info me-1">Año: {{ $queryAno }}</span>@endif
                                            @if($queryCliente)
                                                @php
                                                    $clienteNombre = DB::table('clientes')->where('id', $queryCliente)->value('nombre');
                                                @endphp
                                                <span class="badge bg-info me-1">Cliente: {{ $clienteNombre }}</span>
                                            @endif
                                        </span>
                                    </div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="card-title d-flex align-items-center">
                                <i class="bi bi-car-front me-2"></i> Listado de Vehículos
                                <span class="badge bg-primary ms-2">{{ $vehiculos->total() }}</span>
                            </div>
                            <div class="card-tools">
                                <ul class="nav nav-pills" id="viewTypeTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="table-tab" data-bs-toggle="tab" data-bs-target="#table-view" type="button" role="tab" aria-selected="true">
                                            <i class="bi bi-table"></i>
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="cards-tab" data-bs-toggle="tab" data-bs-target="#cards-view" type="button" role="tab" aria-selected="false">
                                            <i class="bi bi-grid-3x3-gap"></i>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#printVehiculosModal">
                                        <i class="bi bi-printer"></i> Imprimir
                                    </button>
                                </div>
                                <div>
                                    <a href="{{ url('add-vehiculo') }}" type="button" class="btn btn-success">
                                        <i class="bi bi-plus-square"></i> Agregar Vehículo
                                    </a>
                                </div>
                            </div>

                            @include('admin.vehiculo.printvehiculosmodal')

                            <div class="tab-content">
                                <!-- Vista de tabla -->
                                <div class="tab-pane fade show active" id="table-view" role="tabpanel" aria-labelledby="table-tab">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="5%"><i class="bi bi-list-task"></i></th>
                                                    <th width="45%">Vehiculo</th>
                                                    <th width="50%">Cliente</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($vehiculos as $vehiculo)
                                                <tr>
                                                    <td>
                                                        <div class="btn-group dropend">
                                                            <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="bi bi-gear"></i>
                                                            </button>
                                                            <ul class="dropdown-menu shadow">
                                                                <li><a class="dropdown-item" href="{{ url('show-vehiculo/'.$vehiculo->id) }}"><i class="bi bi-eye-fill text-primary"></i> Ver detalles</a></li>
                                                                <li><a class="dropdown-item" href="{{ url('edit-vehiculo/'.$vehiculo->id) }}"><i class="bi bi-pencil-fill text-warning"></i> Editar</a></li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li><button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $vehiculo->id }}"><i class="bi bi-trash-fill"></i> Eliminar</button></li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if ($vehiculo->fotografia != null)
                                                            <img src="{{ asset('assets/imgs/vehiculos/'.$vehiculo->fotografia) }}" class="img-4x rounded-2 me-3" alt="Vehículos" />
                                                            @else
                                                            <img src="{{ asset('assets/imgs/vehiculos/vehiculoicon.png') }}" class="img-4x rounded-2 me-3" alt="Vehículos" />
                                                            @endif
                                                            <div>
                                                                <a class="fw-bold text-primary" href="{{ url('show-vehiculo/'.$vehiculo->id) }}">
                                                                    {{ $vehiculo->marca }} {{ $vehiculo->modelo }} {{ $vehiculo->ano }}
                                                                </a>
                                                                <div class="d-flex mt-1">
                                                                    <span class="badge bg-light text-dark me-1">Color: {{ $vehiculo->color }}</span>
                                                                    <span class="badge bg-info text-white me-1">Placa: {{ $vehiculo->placa }}</span>
                                                                    <span class="badge bg-secondary">VIN: {{ $vehiculo->vin }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    @php
                                                        $cliente = DB::table('clientes')->where('id', $vehiculo->cliente_id)->first();
                                                    @endphp
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @if ($cliente->fotografia != null)
                                                                <img src="{{ asset('assets/imgs/clientes/'.$cliente->fotografia) }}" class="img-4x rounded-circle me-3" alt="Clientes" />
                                                            @else
                                                                <img src="{{ asset('assets/imgs/clientes/usericon4.png') }}" class="img-4x rounded-circle me-3" alt="Clientes" />
                                                            @endif

                                                            <div>
                                                                <a class="fw-bold text-primary" href="{{ url('show-cliente/'.$cliente->id) }}">{{ $cliente->nombre }}</a>
                                                                @php
                                                                    $edad = 0;
                                                                    if ($cliente->fecha_nacimiento != null) {
                                                                        $cumpleanos = new DateTime($cliente->fecha_nacimiento);
                                                                        $hoy = new DateTime();
                                                                        $edad = $hoy->diff($cumpleanos)->y;
                                                                    }
                                                                @endphp
                                                                @if ($edad > 0)
                                                                    <small class="text-muted">({{ $edad }} años)</small>
                                                                @endif

                                                                <div class="mt-1">
                                                                    <a class="btn btn-sm btn-outline-info me-1" href="mailto:{{ $cliente->email }}" title="{{ $cliente->email }}">
                                                                        <i class="bi bi-envelope"></i>
                                                                    </a>
                                                                    <a class="btn btn-sm btn-outline-secondary me-1" href="tel:+502{{ $cliente->telefono }}" title="{{ $cliente->telefono }}">
                                                                        <i class="bi bi-telephone"></i>
                                                                    </a>
                                                                    @if ($cliente->celular != null)
                                                                    <a class="btn btn-sm btn-outline-success" href="https://wa.me/502{{ $cliente->celular }}" target="_blank" title="WhatsApp: {{ $cliente->celular }}">
                                                                        <i class="bi bi-whatsapp"></i>
                                                                    </a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @include('admin.vehiculo.deletemodal')
                                                @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-4">
                                                        <div class="alert alert-info mb-0">
                                                            <i class="bi bi-info-circle me-2"></i> No se encontraron vehículos
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Vista de tarjetas -->
                                <div class="tab-pane fade" id="cards-view" role="tabpanel" aria-labelledby="cards-tab">
                                    <div class="row">
                                        @forelse ($vehiculos as $vehiculo)
                                            @php
                                                $cliente = DB::table('clientes')->where('id', $vehiculo->cliente_id)->first();
                                            @endphp
                                            <div class="col-md-6 col-lg-4 mb-4">
                                                <div class="card h-100 shadow-sm">
                                                    <div class="position-relative">
                                                        @if ($vehiculo->fotografia != null)
                                                            <img src="{{ asset('assets/imgs/vehiculos/'.$vehiculo->fotografia) }}" class="card-img-top" alt="Vehículo" style="height: 180px; object-fit: cover;">
                                                        @else
                                                            <img src="{{ asset('assets/imgs/vehiculos/vehiculoicon.png') }}" class="card-img-top" alt="Vehículo" style="height: 180px; object-fit: cover;">
                                                        @endif
                                                        <div class="position-absolute top-0 end-0 p-2">
                                                            <div class="dropdown">
                                                                <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown">
                                                                    <i class="bi bi-three-dots-vertical"></i>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="{{ url('show-vehiculo/'.$vehiculo->id) }}"><i class="bi bi-eye-fill text-primary"></i> Ver detalles</a></li>
                                                                    <li><a class="dropdown-item" href="{{ url('edit-vehiculo/'.$vehiculo->id) }}"><i class="bi bi-pencil-fill text-warning"></i> Editar</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li><button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $vehiculo->id }}"><i class="bi bi-trash-fill"></i> Eliminar</button></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        <h5 class="card-title">
                                                            <a href="{{ url('show-vehiculo/'.$vehiculo->id) }}" class="text-decoration-none">
                                                                {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                                                            </a>
                                                        </h5>
                                                        <div class="mb-2">
                                                            <span class="badge bg-primary me-1">{{ $vehiculo->ano }}</span>
                                                            <span class="badge bg-secondary me-1">{{ $vehiculo->color }}</span>
                                                        </div>
                                                        <p class="card-text mb-1">
                                                            <small class="text-muted"><i class="bi bi-car-front me-1"></i> Placa: {{ $vehiculo->placa }}</small>
                                                        </p>
                                                        <p class="card-text mb-3">
                                                            <small class="text-muted"><i class="bi bi-upc-scan me-1"></i> VIN: {{ $vehiculo->vin }}</small>
                                                        </p>
                                                        <div class="d-flex align-items-center mt-2">
                                                            @if ($cliente->fotografia != null)
                                                                <img src="{{ asset('assets/imgs/clientes/'.$cliente->fotografia) }}" class="rounded-circle me-2" width="30" height="30" alt="Cliente">
                                                            @else
                                                                <img src="{{ asset('assets/imgs/clientes/usericon4.png') }}" class="rounded-circle me-2" width="30" height="30" alt="Cliente">
                                                            @endif
                                                            <a href="{{ url('show-cliente/'.$cliente->id) }}" class="text-decoration-none">{{ $cliente->nombre }}</a>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer bg-white p-2">
                                                        <div class="btn-group w-100">
                                                            <a href="{{ url('show-vehiculo/'.$vehiculo->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Ver</a>
                                                            <a href="{{ url('edit-vehiculo/'.$vehiculo->id) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i> Editar</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <div class="alert alert-info text-center">
                                                    <i class="bi bi-info-circle me-2"></i> No se encontraron vehículos
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4">
                                {{ $vehiculos->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Row end -->
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->

    <style>
        /* Estilos personalizados para corregir la visualización de Select2 con Bootstrap 5 */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            padding: 0.375rem 0.75rem;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #0d6efd;
        }

        .select2-dropdown {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .select2-search__field {
            padding: 0.375rem;
            border: 1px solid #ced4da !important;
            border-radius: 0.25rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            padding-left: 0;
            color: #212529;
        }
    </style>

    <script>
        $(document).ready(function() {
            // Inicializar Select2 para el filtro de clientes
            $('.select2-clientes').select2({
                placeholder: 'Seleccione un cliente',
                allowClear: true,
                width: '100%',
                dropdownParent: $('.select2-clientes').parent(), // Asegurar que el dropdown se posicione correctamente
                language: {
                    noResults: function() {
                        return "No se encontraron resultados";
                    },
                    searching: function() {
                        return "Buscando...";
                    }
                }
            });

            // Ajustar la posición del dropdown después de abrir
            $('.select2-clientes').on('select2:open', function() {
                $('.select2-dropdown').css('margin-top', '3px');
            });

            // Guardar la preferencia de vista (tabla o tarjetas) en localStorage
            $('#viewTypeTab button').on('shown.bs.tab', function (e) {
                localStorage.setItem('vehiculosViewPreference', $(e.target).attr('id'));
            });

            // Restaurar preferencia guardada
            let savedView = localStorage.getItem('vehiculosViewPreference');
            if (savedView) {
                $('#' + savedView).tab('show');
            }
        });
    </script>
@endsection



