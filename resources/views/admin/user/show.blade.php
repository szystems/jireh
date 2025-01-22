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
                    <h5>Usuarios</h5>
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
            <div class="subscribe-header">
                <img src="{{ asset('dashboardtemplate/design/assets/images/bg.jpg') }}" class="img-fluid w-100" alt="Header" />
            </div>
            <div class="subscriber-body">
                <!-- Row start -->
                <div class="row justify-content-center mt-4">
                    <div class="col-lg-12">
                        <!-- Row start -->
                        <div class="row align-items-end">
                            <div class="col-auto">
                                @if ($user->fotografia != null)
                                    <img src="{{ asset('assets/imgs/users/'.$user->fotografia) }}" class="img-7xx rounded-circle" />
                                @else
                                    <img src="{{ asset('assets/imgs/users/usericon4.png') }}" class="img-7xx rounded-circle" />
                                @endif
                            </div>
                            <div class="col">
                                <h6>Usuario</h6>
                                <h4 class="m-0">{{ $user->name }}</h4>
                            </div>
                            <div class="col-12 col-md-auto">
                                <div class="btn-group-sm m-3">
                                    <a target="_blank" href="{{ url('pdf-user/'.$user->id) }}" type="button" class="btn btn-info">
                                        <i class="bi bi-printer"></i> Imprimir
                                    </a>
                                    <a href="{{ url('edit-user/'.$user->id) }}" class="btn btn-warning" aria-current="page"><i class="bi bi-pencil"></i> Editar</a>
                                    @if ($user->principal == "1")
										<button disabled type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $user->id }}">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $user->id }}">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    @endif

                                    @include('admin.user.deletemodal')
                                </div>
                            </div>
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
                                <div class="custom-tabs-container">
                                    <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="tab-docInfo" data-bs-toggle="tab" href="#docInfo" role="tab"
                                                aria-controls="docInfo" aria-selected="true">Información</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content h-350">

                                        <div class="tab-pane fade show active" id="docInfo" role="tabpanel">
                                            <!-- Row start -->
                                            <div class="row gx-3">
                                                <div class="col-sm-12 col-12">
                                                    <div class="row gx-3">

                                                        <div class="col-md-4 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="fullName" class="form-label">Nombre</label>
                                                                <p>{{ $user->name }}</p>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="birthDay" class="form-label">Fecha de Nacimiento</label>
                                                                @php
                                                                    $fnacimiento = null;
                                                                    $edad = 0;
                                                                    if ($user->fecha_nacimiento != null) {
                                                                        $fnacimiento = date("d-m-Y", strtotime($user->fecha_nacimiento));
                                                                        //calcular edad
                                                                        $fecha_nacimiento = date("d-m-Y", strtotime($user->fecha_nacimiento));
                                                                        $cumpleanos = new DateTime($user->fecha_nacimiento);
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

                                                        <div class="col-md-4 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="emailId" class="form-label">Email</label>
                                                                <p><a class="link-info" href="mailto:{{ $user->email }}">{{ $user->email }}</a></p>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="contactNumber" class="form-label">Teléfono / Celular / Whatsapp</label>
                                                                <p>
                                                                    <a class="text-info" href="tel:+502{{ $user->telefono }}">{{ $user->telefono }}</a>
                                                                    @if ($user->celular != null)
                                                                        <a class="text-info" href="tel:+502{{ $user->celular }}">/ {{ $user->celular }}</a>
                                                                        <a class="text-success" href="https://wa.me/502{{ $user->celular }}" target="_blank">/ <i class="bi bi-whatsapp"></i></a>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>


                                                        <div class="col-md-4 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label class="form-label">Dirección</label>
                                                                <p>{{ $user->direccion }}</p>
                                                            </div>
                                                        </div>



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
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->
@endsection
