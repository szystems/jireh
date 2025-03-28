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
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            @if ($user->fotografia != null)
                                                <img src="{{ asset('assets/imgs/users/'.$user->fotografia) }}" class="img-fluid rounded-circle shadow border border-3 border-light" style="width: 150px; height: 150px; object-fit: cover;" />
                                            @else
                                                <img src="{{ asset('assets/imgs/users/usericon4.png') }}" class="img-fluid rounded-circle shadow border border-3 border-light" style="width: 150px; height: 150px; object-fit: cover;" />
                                            @endif
                                        </div>
                                        <h5 class="mb-0">{{ $user->name }}</h5>
                                        <div class="mt-2 mb-3">
                                            <span class="badge {{ $user->role_as == 0 ? 'bg-danger' : 'bg-success' }} px-3 py-2">
                                                <i class="bi {{ $user->role_as == 0 ? 'bi-shield-lock-fill' : 'bi-person-badge-fill' }} me-1"></i>
                                                {{ $user->role_as == 0 ? 'Administrador' : 'Vendedor' }}
                                            </span>
                                        </div>

                                        <div class="btn-group btn-group-sm d-flex flex-wrap gap-2 mt-3">
                                            <a href="{{ url('edit-user/'.$user->id) }}" class="btn btn-primary">
                                                <i class="bi bi-pencil-fill"></i> Editar
                                            </a>

                                            <a target="_blank" href="{{ url('pdf-user/'.$user->id) }}" class="btn btn-danger">
                                                <i class="bi bi-file-pdf"></i> PDF
                                            </a>

                                            @if ($user->principal != "1")
                                                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $user->id }}">
                                                    <i class="bi bi-trash-fill"></i> Eliminar
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-secondary" disabled>
                                                    <i class="bi bi-lock-fill"></i> Eliminar
                                                </button>
                                            @endif

                                            <a href="{{ url('users') }}" class="btn btn-info w-100 mt-2">
                                                <i class="bi bi-arrow-left"></i> Volver al listado
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-9">
                                <div class="card">
                                    <div class="card-header">
                                        <ul class="nav nav-tabs card-header-tabs" id="userInfoTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link active" id="personal-tab" data-bs-toggle="tab" href="#personal" role="tab" aria-controls="personal" aria-selected="true">
                                                    <i class="bi bi-person-fill"></i> Información Personal
                                                </a>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link" id="contact-tab" data-bs-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">
                                                    <i class="bi bi-telephone-fill"></i> Contacto
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content" id="userInfoTabsContent">
                                            <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0">
                                                                <span class="badge bg-light text-dark p-2 rounded-circle">
                                                                    <i class="bi bi-person-badge-fill fs-5 text-primary"></i>
                                                                </span>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-0 text-muted">Nombre Completo</h6>
                                                                <p class="fs-5">{{ $user->name }}</p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0">
                                                                <span class="badge bg-light text-dark p-2 rounded-circle">
                                                                    <i class="bi bi-calendar-date fs-5 text-primary"></i>
                                                                </span>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-0 text-muted">Fecha de Nacimiento</h6>
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
                                                                <p class="fs-5">
                                                                    <span class="text-primary">{{ $fnacimiento }}</span>
                                                                    @if ($edad > 0)
                                                                        <span class="badge bg-light text-dark border ms-1">{{ $edad }} años</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0">
                                                                <span class="badge bg-light text-dark p-2 rounded-circle">
                                                                    <i class="bi bi-envelope-fill fs-5 text-primary"></i>
                                                                </span>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-0 text-muted">Correo Electrónico</h6>
                                                                <p class="fs-5">
                                                                    <a href="mailto:{{ $user->email }}" class="text-primary text-decoration-none">
                                                                        {{ $user->email }}
                                                                    </a>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0">
                                                                <span class="badge bg-light text-dark p-2 rounded-circle">
                                                                    <i class="bi bi-shield-check fs-5 text-primary"></i>
                                                                </span>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-0 text-muted">Rol del Sistema</h6>
                                                                <p class="fs-5">
                                                                    <span class="badge {{ $user->role_as == 0 ? 'bg-danger' : 'bg-success' }}">
                                                                        {{ $user->role_as == 0 ? 'Administrador' : 'Vendedor' }}
                                                                    </span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0">
                                                                <span class="badge bg-light text-dark p-2 rounded-circle">
                                                                    <i class="bi bi-telephone-fill fs-5 text-primary"></i>
                                                                </span>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-0 text-muted">Teléfono</h6>
                                                                <p class="fs-5">
                                                                    <a href="tel:+502{{ $user->telefono }}" class="text-primary text-decoration-none">
                                                                        {{ $user->telefono }}
                                                                    </a>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0">
                                                                <span class="badge bg-light text-dark p-2 rounded-circle">
                                                                    <i class="bi bi-phone-fill fs-5 text-primary"></i>
                                                                </span>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-0 text-muted">Celular / WhatsApp</h6>
                                                                <p class="fs-5">
                                                                    @if ($user->celular)
                                                                        <a href="tel:+502{{ $user->celular }}" class="text-primary text-decoration-none">
                                                                            {{ $user->celular }}
                                                                        </a>
                                                                        <a href="https://wa.me/502{{ $user->celular }}" target="_blank" class="btn btn-sm btn-success ms-2">
                                                                            <i class="bi bi-whatsapp"></i> WhatsApp
                                                                        </a>
                                                                    @else
                                                                        <span class="text-muted">No disponible</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="d-flex">
                                                            <div class="flex-shrink-0">
                                                                <span class="badge bg-light text-dark p-2 rounded-circle">
                                                                    <i class="bi bi-geo-alt-fill fs-5 text-primary"></i>
                                                                </span>
                                                            </div>
                                                            <div class="flex-grow-1 ms-3">
                                                                <h6 class="mb-0 text-muted">Dirección</h6>
                                                                <p class="fs-5">
                                                                    @if ($user->direccion)
                                                                        {{ $user->direccion }}
                                                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($user->direccion) }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                                                            <i class="bi bi-map"></i> Ver en mapa
                                                                        </a>
                                                                    @else
                                                                        <span class="text-muted">No disponible</span>
                                                                    @endif
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white">
                                        <div class="d-flex justify-content-between align-items-center small text-muted">
                                            <div>Usuario registrado en el sistema</div>
                                            <div>ID: {{ $user->id }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Row end -->
                    </div>
                </div>
                <!-- Row end -->
            </div>
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->

    @include('admin.user.deletemodal')
@endsection
