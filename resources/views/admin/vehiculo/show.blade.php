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


            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="custom-tabs-container">
                                <div class="col-12 col-md-auto float-end">
                                    <div class="btn-group-sm m-3">
                                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#printVehiculoModal{{ $vehiculo->id }}">
                                            <i class="bi bi-printer"></i> Imprimir
                                        </button>
                                        <a href="{{ url('edit-vehiculo/'.$vehiculo->id) }}" class="btn btn-warning" aria-current="page"><i class="bi bi-pencil"></i> Editar</a>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $vehiculo->id }}">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                        @include('admin.vehiculo.printvehiculomodal')
                                        @include('admin.vehiculo.deletemodal')
                                    </div>
                                </div>
                                <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA" role="tab"
                                            aria-controls="oneA" aria-selected="true">Información</a>
                                    </li>
                                </ul>
                                <div class="tab-content h-350">
                                    <div class="tab-pane fade show active" id="oneA" role="tabpanel">
                                        <!-- Row start -->
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                <div class="row gx-3">

                                                    <div class="col-md-3 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label for="cliente" class="form-label">Cliente</label>


                                                            <p class="m-0">
                                                                @if ($vehiculo->cliente->fotografia != null)
                                                                    <img src="{{ asset('assets/imgs/clientes/'.$vehiculo->cliente->fotografia) }}" class="img-4x rounded-5 me-3" alt="Clientes" />
                                                                @else
                                                                    <img src="{{ asset('assets/imgs/clientes/usericon4.png') }}" class="img-4x rounded-5 me-3" alt="Clientes" />
                                                                @endif
                                                                <a class="text-primary" href="{{ url('show-cliente/'.$vehiculo->cliente->id) }}"><b>{{ $vehiculo->cliente->nombre }}</b></a>
                                                                @php
                                                                    $fnacimiento = null;
                                                                    $edad = 0;
                                                                    if ($vehiculo->cliente->fecha_nacimiento != null) {
                                                                        $fnacimiento = date("d-m-Y", strtotime($vehiculo->cliente->fecha_nacimiento));
                                                                        //calcular edad
                                                                        $fecha_nacimiento = date("d-m-Y", strtotime($vehiculo->cliente->fecha_nacimiento));
                                                                        $cumpleanos = new DateTime($vehiculo->cliente->fecha_nacimiento);
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
                                                                    <a class="text-info" href="mailto:{{ $vehiculo->cliente->email }}">{{ $vehiculo->cliente->email }}</a>
                                                                    <br>
                                                                    <a class="text-light" href="tel:+502{{ $vehiculo->cliente->telefono }}">{{ $vehiculo->cliente->telefono }}</a>
                                                                    @if ($vehiculo->cliente->celular != null)
                                                                    / <a class="text-light" href="tel:+502{{ $vehiculo->cliente->celular }}">{{ $vehiculo->cliente->celular }}</a>

                                                                    / <a class="text-success" href="https://wa.me/502{{ $vehiculo->cliente->celular }}" target="_blank"><i class="bi bi-whatsapp"></i></a>
                                                                    @endif
                                                                </small>

                                                            </p>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="col-md-12 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label for="vehiculo" class="form-label">Vehículo</label>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label for="marca" class="form-label">Marca</label>
                                                            <p>{{ $vehiculo->marca }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label for="modelo" class="form-label">Modelo</label>
                                                            <p>{{ $vehiculo->modelo }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label for="ano" class="form-label">Año</label>
                                                            <p>{{ $vehiculo->ano }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label for="color" class="form-label">color</label>
                                                            <p>{{ $vehiculo->color }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Placa</label>
                                                            <p>{{ $vehiculo->placa }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label class="form-label">VIN</label>
                                                            <p>{{ $vehiculo->vin }}</p>
                                                        </div>
                                                    </div>

                                                    @if ($vehiculo->fotografia != null)
                                                    <div class="col-md-12 mb-3">
                                                        <!-- Form Field Start -->
                                                        <div class="mb-3">
                                                            <label class="form-label">Imágen</label>
                                                            <div class="brand">
                                                                <img src="{{ asset('assets/imgs/vehiculos/'.$vehiculo->fotografia) }}" class="img-thumbnail" style="height: 50%;" alt="Vehiculo" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif



                                                    <hr>



                                                </div>
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
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->

@endsection
