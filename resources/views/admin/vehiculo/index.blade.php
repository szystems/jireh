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

            @include('admin.vehiculo.search')

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">

                        <div class="card-header">
                            <div class="card-title">
                                Listado de Vehículos

                            </div>

                        </div>
                        <div class="card-body">
                            <hr>
                            <p class="m-0 fw-normal text-dark">
                                <a href="{{ url('add-vehiculo') }}" type="button" class="btn btn-success float-end">
                                    <i class="bi bi-plus-square"></i> Agregar
                                </a>

                                {{-- <strong><u>Filtros:</u></strong>
                                <br>
                                <small>
                                    @if (request('nombre'))
                                        <strong>Nombre: </strong><font color="Blue">{{ request('nombre') }}</font>
                                    @endif
                                    @if (request('categoria_id'))
                                        @php
                                            $categoria = \App\Models\Categoria::find(request('categoria_id'));
                                        @endphp
                                        <strong>Clinica: </strong><font color="Blue">{{ $categoria->nombre }}</font>
                                    @endif
                                    @if (request('proveedor_id'))
                                        @php
                                            $proveedor = \App\Models\Proveedor::find(request('proveedor_id'));
                                        @endphp
                                        <strong>Proveedor: </strong><font color="Blue">{{ $proveedor->nombre }}</font>
                                    @endif
                                </small> --}}
                            </p>
                            <button type="button" class="btn btn-info m-1" data-bs-toggle="modal" data-bs-target="#printVehiculosModal">
                                <i class="bi bi-printer"></i> Imprimir
                            </button>

                            @include('admin.vehiculo.printvehiculosmodal')
                            <div class="table-responsive">
                                <table class="table align-middle table-striped flex-column">
                                    <thead>
                                        <tr>
                                            <td align="center"><i class="bi bi-list-task"></i></td>
                                            <td>Vehiculo</td>
                                            <td>Cliente</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vehiculos as $vehiculo)
                                        <tr>
                                            <td align="center">
                                                <div class="btn-group dropend">
                                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                                        <i class="bi bi-list-task"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ url('show-vehiculo/'.$vehiculo->id) }}"><i class="bi bi-eye-fill text-blue"></i> Información</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ url('edit-vehiculo/'.$vehiculo->id) }}"><i class="bi bi-pencil-fill text-warning"></i> Editar</a>
                                                        </li>
                                                        <li>
                                                            <a type="button" class="btn bg-gradient-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $vehiculo->id }}">
                                                                <i class="bi bi-trash-fill text-danger"></i> Eliminar
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if ($vehiculo->fotografia != null)
                                                    <a class="text-primary" href="{{ url('show-vehiculo/'.$vehiculo->id) }}">
                                                        <img src="{{ asset('assets/imgs/vehiculos/'.$vehiculo->fotografia) }}" class="img-4x rounded-2 me-3" alt="Vehículos" />
                                                    </a>
                                                    @else
                                                    <a class="text-primary" href="{{ url('show-vehiculo/'.$vehiculo->id) }}">
                                                        <img src="{{ asset('assets/imgs/vehiculos/vehiculoicon.png') }}" class="img-4x rounded-2 me-3" alt="Vehículos" />
                                                    </a>
                                                    @endif
                                                    <div>
                                                        <a class="text-primary" href="{{ url('show-vehiculo/'.$vehiculo->id) }}">
                                                            <b>{{ $vehiculo->marca }} {{ $vehiculo->modelo }} {{ $vehiculo->ano }}</b>
                                                        </a>
                                                        <br>
                                                        <small class="text-secondary">
                                                            Color: <b>{{ $vehiculo->color }}</b><br>
                                                            Placa: <b>{{ $vehiculo->placa }}</b><br>
                                                            VIN: <b>{{ $vehiculo->vin }}</b>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            @php
                                                $cliente = DB::table('clientes')->where('id', $vehiculo->cliente_id)->first();
                                            @endphp
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
                                                            <small>
                                                                ({{ $edad }} años)
                                                            </small>
                                                        @endif
                                                        <br>
                                                        <small>
                                                            <a class="text-info" href="mailto:{{ $cliente->email }}">{{ $cliente->email }}</a>
                                                            <br>
                                                            <a class="text-light" href="tel:+502{{ $cliente->telefono }}">{{ $cliente->telefono }}</a>
                                                            @if ($cliente->celular != null)
                                                            / <a class="text-light" href="tel:+502{{ $cliente->celular }}">{{ $cliente->celular }}</a>

                                                            / <a class="text-success" href="https://wa.me/502{{ $cliente->celular }}" target="_blank"><i class="bi bi-whatsapp"></i></a>
                                                            @endif
                                                        </small>

                                                    </p>

                                                </div>
                                            </td>


                                        </tr>
                                        @include('admin.vehiculo.deletemodal')
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $vehiculos->links() }}
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
@endsection

