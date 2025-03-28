@extends('layouts.admin')

@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="page-title">
                    <h5>Clientes</h5>
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

            <!-- Estadísticas de clientes - Inicio -->
            <div class="row mb-3">
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="card bg-info bg-opacity-10 border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-lg bg-info text-white rounded-3 me-3">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Total Clientes</h6>
                                    <h4 class="mb-0">{{ $clientes->total() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="card bg-success bg-opacity-10 border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-lg bg-success text-white rounded-3 me-3">
                                    <i class="bi bi-telephone-fill"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Con Whatsapp</h6>
                                    <h4 class="mb-0">
                                        @php
                                            $whatsappCount = $clientes->filter(function($cliente) {
                                                return !empty($cliente->celular);
                                            })->count();
                                            echo $whatsappCount;
                                        @endphp
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="card bg-primary bg-opacity-10 border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-lg bg-primary text-white rounded-3 me-3">
                                    <i class="bi bi-envelope-fill"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Con Email</h6>
                                    <h4 class="mb-0">
                                        @php
                                            $emailCount = $clientes->filter(function($cliente) {
                                                return !empty($cliente->email);
                                            })->count();
                                            echo $emailCount;
                                        @endphp
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="card bg-warning bg-opacity-10 border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-lg bg-warning text-white rounded-3 me-3">
                                    <i class="bi bi-card-image"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Con Foto</h6>
                                    <h4 class="mb-0">
                                        @php
                                            $photoCount = $clientes->filter(function($cliente) {
                                                return !empty($cliente->fotografia);
                                            })->count();
                                            echo $photoCount;
                                        @endphp
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Estadísticas de clientes - Fin -->

            @include('admin.cliente.search')

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">

                        <div class="card-header">
                            <div class="card-title">
                                Listado de Clientes
                                <span class="badge bg-primary rounded-pill ms-2">{{ $clientes->total() }}</span>
                                <br>
                                <div class="mt-2 d-flex gap-2">
                                    <a target="_blank" href="{{ url('pdf-clientes') }}" type="button" class="btn btn-info btn-sm">
                                        <i class="bi bi-printer"></i> Imprimir
                                    </a>
                                    {{-- <a target="_blank" href="{{ url('exportclientes') }}" type="button" class="btn btn-success btn-sm">
                                        <i class="bi bi-file-earmark-excel-fill"></i> Excel
                                    </a> --}}
                                    <a href="{{ url('add-cliente') }}" type="button" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-square"></i> Agregar Cliente
                                    </a>
                                </div>

                                <div class="mt-2 d-flex justify-content-between align-items-center">
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check" name="view-mode" id="table-view" checked>
                                        <label class="btn btn-outline-secondary btn-sm" for="table-view">
                                            <i class="bi bi-table"></i> Vista Tabla
                                        </label>

                                        <input type="radio" class="btn-check" name="view-mode" id="card-view">
                                        <label class="btn btn-outline-secondary btn-sm" for="card-view">
                                            <i class="bi bi-grid-3x3-gap"></i> Vista Tarjetas
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">

                            <!-- Vista de tabla -->
                            <div id="table-view-content" class="table-responsive">
                                <table id="highlightRowColumn" class="table custom-table">
                                    <thead>
                                        <tr>
                                            <td align="center" width="5%"><i class="bi bi-list-task"></i></td>
                                            <td width="35%">Cliente</td>
                                            <td width="10%">Vehículos</td>
                                            <td width="10%">Ventas</td>
                                            <td width="10%">Fecha de Nacimiento</td>
                                            <td width="10%">DPI</td>
                                            <td width="10%">NIT</td>
                                            <td width="10%">Dirección</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($clientes as $cliente)
                                        <tr>
                                            <td>
                                                <div class="dropdown">
                                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-list-task"></i>
                                                    </button>
                                                    <ul class="dropdown-menu shadow">
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center" href="{{ url('show-cliente/'.$cliente->id) }}">
                                                                <i class="bi bi-eye-fill text-primary me-2"></i> Información
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center" href="{{ url('edit-cliente/'.$cliente->id) }}">
                                                                <i class="bi bi-pencil-fill text-warning me-2"></i> Editar
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <a class="dropdown-item d-flex align-items-center text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $cliente->id }}">
                                                                <i class="bi bi-trash-fill text-danger me-2"></i> Eliminar
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if ($cliente->fotografia != null)
                                                        <img src="{{ asset('assets/imgs/clientes/'.$cliente->fotografia) }}" class="img-4x rounded-5 me-3" alt="Clientes" />
                                                    @else
                                                        <img src="{{ asset('assets/imgs/clientes/usericon4.png') }}" class="img-4x rounded-5 me-3" alt="Clientes" />
                                                    @endif

                                                    <p class="m-0">
                                                        <a class="text-primary" href="{{ url('show-cliente/'.$cliente->id) }}"><b>{{ $cliente->nombre }}</b></a>
                                                        @php
                                                            $fnacimiento = null;
                                                            $edad = 0;
                                                            if ($cliente->fecha_nacimiento != null) {
                                                                $fnacimiento = date("d-m-Y", strtotime($cliente->fecha_nacimiento));
                                                                //calcular edad
                                                                $fecha_nacimiento = date("d-m-Y", strtotime($cliente->fecha_nacimiento));
                                                                $cumpleanos = new DateTime($cliente->fecha_nacimiento);
                                                                $hoy = new DateTime();
                                                                $annos = $hoy->diff($cumpleanos);
                                                                $edad = $annos->y;
                                                            }

                                                        @endphp
                                                        @if ($edad > 0)
                                                            <span class="badge bg-secondary rounded-pill ms-1">
                                                                {{ $edad }} años
                                                            </span>
                                                        @endif
                                                        <br>
                                                        <small class="d-flex flex-wrap gap-2 mt-1">
                                                            @if($cliente->email)
                                                                <a class="badge bg-primary bg-opacity-10 text-primary" href="mailto:{{ $cliente->email }}">
                                                                    <i class="bi bi-envelope"></i> {{ $cliente->email }}
                                                                </a>
                                                            @endif
                                                            <a class="badge bg-secondary bg-opacity-10 text-secondary" href="tel:+502{{ $cliente->telefono }}">
                                                                <i class="bi bi-telephone"></i> {{ $cliente->telefono }}
                                                            </a>
                                                            @if ($cliente->celular != null)
                                                                <a class="badge bg-secondary bg-opacity-10 text-secondary" href="tel:+502{{ $cliente->celular }}">
                                                                    <i class="bi bi-phone"></i> {{ $cliente->celular }}
                                                                </a>
                                                                <a class="badge bg-success text-white" href="https://wa.me/502{{ $cliente->celular }}" target="_blank">
                                                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                                                </a>
                                                            @endif
                                                        </small>
                                                    </p>
                                                </div>
                                            </td>
                                            <td align="center">
                                                @php
                                                    $vehiculosCount = \App\Models\Vehiculo::where('cliente_id', $cliente->id)
                                                        ->where('estado', 1)
                                                        ->count();
                                                @endphp
                                                <a href="{{ url('show-cliente/'.$cliente->id) }}#vehiculos" class="badge bg-primary">
                                                    <i class="bi bi-car-front"></i> {{ $vehiculosCount }}
                                                </a>
                                            </td>
                                            <td align="center">
                                                @php
                                                    $ventasCount = \App\Models\Venta::where('cliente_id', $cliente->id)
                                                        ->where('estado', '!=', 0)
                                                        ->count();
                                                @endphp
                                                <a href="{{ url('show-cliente/'.$cliente->id) }}#ventas" class="badge bg-success">
                                                    <i class="bi bi-receipt"></i> {{ $ventasCount }}
                                                </a>
                                            </td>
                                            <td align="center">
                                                @if($fnacimiento)
                                                    <span class="badge bg-light text-dark border">
                                                        {{ $fnacimiento }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td align="center">
                                                @if($cliente->dpi)
                                                    <span class="badge bg-light text-dark border">
                                                        {{ $cliente->dpi }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td align="center">
                                                @if($cliente->nit)
                                                    <span class="badge bg-light text-dark border">
                                                        {{ $cliente->nit }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $cliente->direccion }}
                                                </small>
                                            </td>
                                        </tr>
                                        @include('admin.cliente.deletemodal')
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $clientes->links() }}
                                </div>
                            </div>

                            <!-- Vista de tarjetas -->
                            <div id="card-view-content" class="row g-3" style="display: none;">
                                @foreach ($clientes as $cliente)
                                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                                        <div class="card h-100">
                                            <div class="card-header bg-light">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0">{{ $cliente->nombre }}</h6>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                                            <i class="bi bi-three-dots-vertical"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ url('show-cliente/'.$cliente->id) }}">
                                                                    <i class="bi bi-eye-fill text-primary me-2"></i> Información
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ url('edit-cliente/'.$cliente->id) }}">
                                                                    <i class="bi bi-pencil-fill text-warning me-2"></i> Editar
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $cliente->id }}">
                                                                    <i class="bi bi-trash-fill text-danger me-2"></i> Eliminar
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body text-center">
                                                @if ($cliente->fotografia != null)
                                                    <img src="{{ asset('assets/imgs/clientes/'.$cliente->fotografia) }}" class="img-6x rounded-circle mb-3" alt="Clientes" />
                                                @else
                                                    <img src="{{ asset('assets/imgs/clientes/usericon4.png') }}" class="img-6x rounded-circle mb-3" alt="Clientes" />
                                                @endif

                                                @php
                                                    $fnacimiento = null;
                                                    $edad = 0;
                                                    if ($cliente->fecha_nacimiento != null) {
                                                        $fnacimiento = date("d-m-Y", strtotime($cliente->fecha_nacimiento));
                                                        $cumpleanos = new DateTime($cliente->fecha_nacimiento);
                                                        $hoy = new DateTime();
                                                        $annos = $hoy->diff($cumpleanos);
                                                        $edad = $annos->y;
                                                    }

                                                    $vehiculosCount = \App\Models\Vehiculo::where('cliente_id', $cliente->id)
                                                        ->where('estado', 1)
                                                        ->count();

                                                    $ventasCount = \App\Models\Venta::where('cliente_id', $cliente->id)
                                                        ->where('estado', '!=', 0)
                                                        ->count();
                                                @endphp

                                                @if ($edad > 0)
                                                    <div class="badge bg-secondary mb-2">{{ $edad }} años</div>
                                                @endif

                                                <div class="d-flex justify-content-around mb-3 mt-2">
                                                    <a href="{{ url('show-cliente/'.$cliente->id) }}#vehiculos" class="badge bg-primary">
                                                        <i class="bi bi-car-front"></i> {{ $vehiculosCount }} vehículos
                                                    </a>
                                                    <a href="{{ url('show-cliente/'.$cliente->id) }}#ventas" class="badge bg-success">
                                                        <i class="bi bi-receipt"></i> {{ $ventasCount }} ventas
                                                    </a>
                                                </div>

                                                <div class="mb-2">
                                                    <i class="bi bi-card-text me-1"></i>
                                                    @if($cliente->dpi) DPI: {{ $cliente->dpi }} @else N/A @endif
                                                </div>
                                                <div class="mb-2">
                                                    <i class="bi bi-receipt me-1"></i>
                                                    @if($cliente->nit) NIT: {{ $cliente->nit }} @else N/A @endif
                                                </div>
                                            </div>
                                            <div class="card-footer bg-light">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <a href="tel:+502{{ $cliente->telefono }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-telephone"></i>
                                                    </a>
                                                    <a href="mailto:{{ $cliente->email }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-envelope"></i>
                                                    </a>
                                                    @if ($cliente->celular != null)
                                                    <a href="https://wa.me/502{{ $cliente->celular }}" target="_blank" class="btn btn-sm btn-success">
                                                        <i class="bi bi-whatsapp"></i>
                                                    </a>
                                                    @endif
                                                    <a href="{{ url('show-cliente/'.$cliente->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-info-circle"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $clientes->links() }}
                                </div>
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

    <script>
        // Script para cambiar entre vistas de tabla y tarjetas
        document.addEventListener('DOMContentLoaded', function() {
            const tableView = document.getElementById('table-view');
            const cardView = document.getElementById('card-view');
            const tableContent = document.getElementById('table-view-content');
            const cardContent = document.getElementById('card-view-content');

            tableView.addEventListener('change', function() {
                if(this.checked) {
                    tableContent.style.display = 'block';
                    cardContent.style.display = 'none';
                }
            });

            cardView.addEventListener('change', function() {
                if(this.checked) {
                    tableContent.style.display = 'none';
                    cardContent.style.display = 'block';
                }
            });
        });
    </script>
@endsection

