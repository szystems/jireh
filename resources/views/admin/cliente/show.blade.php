@extends('layouts.admin')
@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="page-title">
                    <h5>Clientes</h5>
                </div>
            </div>
            <!-- Date range start -->
            <div class="d-flex align-items-end  d-none d-sm-block">
                <h6 class="float-end text-light" id="reloj"></h6>
            </div>
        </div>
        <!-- Main header ends -->

        <!-- Content wrapper start -->
        <div class="content-wrapper">
            <div class="subscribe-header">
                <img src="{{ asset('dashboardtemplate/design/assets/images/bg.jpg') }}" class="img-fluid w-100" alt="Header" />
            </div>
            <div class="subscriber-body">
                <!-- Row start -->
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-12">
                        <!-- Row start -->
                        <div class="row align-items-end">
                            <a href="{{ url('show-cliente/'.$cliente->id) }}">
                                <div class="col-auto">
                                    @if ($cliente->fotografia != null)
                                        <img src="{{ asset('assets/imgs/clientes/'.$cliente->fotografia) }}" class="img-7xx rounded-circle" />
                                    @else
                                        <img src="{{ asset('assets/imgs/clientes/usericon4.png') }}" class="img-7xx rounded-circle" />
                                    @endif
                                </div>
                                <div class="col">
                                    <h6>Cliente</h6>
                                    <h4 class="m-0">{{ $cliente->nombre }}</h4>
                                </div>
                            </a>
                        </div>
                        <!-- Row end -->
                    </div>
                </div>
                <!-- Row end -->

                <!-- Row start -->
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-12">
                        <div class="card light">
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
                                <div class="custom-tabs-container">
                                    <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="tab-info" data-bs-toggle="tab" href="#info" role="tab"
                                                aria-controls="info" aria-selected="true">Información</a>
                                        </li>

                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="tab-vehiculos" data-bs-toggle="tab" href="#vehiculos" role="tab"
                                                aria-controls="vehiculos" aria-selected="false">Vehículos<span
                                                class="badge rounded-pill green ms-2">{{ $vehiculos->count() }}</span></a>
                                        </li>
                                    </ul>
                                    <div class="tab-content h-350">

                                        <div class="tab-pane fade show active" id="info" role="tabpanel">
                                            <!-- Row start -->
                                            <div class="row gx-3">
                                                <div class="col-sm-12 col-12">
                                                    <div class="row gx-3">

                                                        <div class="col-md-12 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <h5 class="card-title"><u>Informacíon de Cliente</u></h5>
                                                                <button type="button" class="btn btn-danger float-end m-1" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $cliente->id }}">
                                                                    <i class="bi bi-trash"></i> Eliminar
                                                                </button>
                                                                <a href="{{ url('edit-cliente/'.$cliente->id) }}" class="btn btn-warning float-end m-1" aria-current="page"><i class="bi bi-pencil"></i> Editar</a>
                                                                <a target="_blank" href="{{ url('pdf-cliente/'.$cliente->id) }}" type="button" class="btn btn-info float-end m-1">
                                                                    <i class="bi bi-printer"></i> Imprimir
                                                                </a>
                                                                @include('admin.cliente.deletemodal')
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="fullName" class="form-label">Nombre</label>
                                                                <p>{{ $cliente->nombre }}</p>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="birthDay" class="form-label">Fecha de Nacimiento</label>
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
                                                                <p>
                                                                    <strong class="text-info">{{ $fnacimiento }}</strong>
                                                                    @if ($edad > 0)
                                                                        <small>
                                                                            ({{ $edad }} años)
                                                                        </small>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="contactNumber" class="form-label">Teléfono / Celular / Whatsapp</label>
                                                                <p>
                                                                    <a class="text-info" href="tel:+502{{ $cliente->telefono }}">{{ $cliente->telefono }}</a>
                                                                    @if ($cliente->celular != null)
                                                                        <a class="text-info" href="tel:+502{{ $cliente->celular }}">/ {{ $cliente->celular }}</a>
                                                                        <a class="text-success" href="https://wa.me/502{{ $cliente->celular }}" target="_blank">/ <i class="bi bi-whatsapp"></i></a>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="emailId" class="form-label">Email</label>
                                                                <p><a class="link-info" href="mailto:{{ $cliente->email }}">{{ $cliente->email }}</a></p>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label class="form-label">DPI</label>
                                                                <p>{{ $cliente->dpi }}</p>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label class="form-label">NIT</label>
                                                                <p>{{ $cliente->nit }}</p>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label class="form-label">Dirección</label>
                                                                <p>{{ $cliente->direccion }}</p>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Row end -->
                                        </div>

                                        <div class="tab-pane fade" id="vehiculos" role="tabpanel">
                                            <!-- Row start -->
                                            <div class="row gx-3">

                                                <h5 class="card-title"><u>Vehículos</u></h5>
                                                <hr>
                                                <p class="m-0 fw-normal text-dark">
                                                    <a href="{{ url('add-vehiculo?cliente_id=' . $cliente->id) }}" type="button" class="btn btn-success m-2">
                                                        <i class="bi bi-plus-square"></i> Agregar Vehiculo
                                                    </a>
                                                </p>

                                                <div class="table-responsive">
                                                    <table class="table align-middle table-striped flex-column">
                                                        <thead>
                                                            <tr>
                                                                <td align="center"><i class="bi bi-list-task"></i></td>
                                                                <td>Vehiculo</td>
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

                                                            </tr>
                                                            @include('admin.vehiculo.deletemodal')
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>


                                            </div>
                                            <!-- Row end -->
                                        </div>

                                    </div>
                                    {{-- <div class="d-flex gap-2 justify-content-end">
                                        <button type="button" class="btn btn-outline-secondary">
                                            Cancel
                                        </button>
                                        <button type="button" class="btn btn-success">
                                            Update
                                        </button>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Row end -->
            </div>
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->

@endsection
