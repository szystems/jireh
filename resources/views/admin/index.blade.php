@extends('layouts.admin')

@section('content')
    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-house"></i>
                </div>
                <div class="page-title">
                    @php
                        $usuario = Auth::user()->name;
                        $nombre = explode(' ', trim($usuario));
                    @endphp
                    <h6>Hola!<strong> {{ ucwords($nombre[0]) }}</strong></h6>
                    {{-- <p class="float-end" id="reloj"></p> --}}
                </div>
            </div>
            <!-- Date range start -->
            <div class="d-flex align-items-end d-none d-sm-block">
                <h6 class="float-end text-light" id="reloj"></h6>
            </div>
            <!-- Date range end -->
        </div>
        <!-- Main header ends -->

        <!-- Content wrapper start -->
        <div class="content-wrapper">

            <!-- Row start -->
            <div class="row gx-3">

                {{-- <div class="col-xxl-3 col-sm-6 col-12">
                    <a href="{{ url('/dashboard') }}">
                    <div class="stats-tile d-flex align-items-center position-relative tile-blue">
                        <div class="sale-icon icon-box xl rounded-5 me-3">
                            <i class="bi bi-house-fill font-2x text-blue"></i>
                        </div>
                        <div class="sale-details">
                            <h5 class="text-light"><u>Panel de Control</u></h5>
                            <h3>296</h3>
                        </div>
                        <div class="tile-count d-flex align-items-center justify-content-center flex-column fw-bold blue">
                            <i class="bi bi-arrow-up-circle-fill font-1x"></i>
                            <span>100%</span>
                        </div>
                    </div>
                    </a>
                </div> --}}

                {{-- <div class="col-xxl-3 col-sm-6 col-12">
                    <a href="{{ url('/') }}">
                        <div class="stats-tile d-flex align-items-center position-relative tile-blue">
                            <div class="sale-icon icon-box xl rounded-5 me-3">
                                <i class="bi bi-globe font-2x text-blue"></i>
                            </div>
                            <div class="sale-details">
                                <h5 class="text-light"><u>Sitio Web</u></h5>

                            </div>
                            <div class="tile-count d-flex align-items-center justify-content-center flex-column fw-bold blue">

                            </div>
                        </div>
                    </a>
                </div> --}}
                @if (Auth::user()->role_as == 0)
                <div class="col-xxl-3 col-sm-6 col-12">
                    <a href="{{ url('show-user/'.Auth::user()->id) }}">
                        <div class="stats-tile d-flex align-items-center position-relative tile-blue">
                            {{-- <div class="sale-icon icon-box xl rounded-5 me-3"> --}}
                                {{-- <i class="bi bi-calendar2-week font-2x text-green"></i> --}}
                                <span class="avatar">
                                    @if (Auth::user()->fotografia != null)
                                        <img src="{{ asset('assets/imgs/users/'.Auth::user()->fotografia) }}" alt="Doctores" class="img-thumbnail rounded-4 border-primary m-2 img-fluid" style="height: 63px;"/>
                                    @else
                                        <img src="{{ asset('assets/imgs/users/usericon4.png') }}" alt="Doctores" class="img-thumbnail rounded-4 border-primary m-2 img-fluid" style="height: 63px;"/>
                                    @endif
                                    <span class="status online"></span>
                                </span>
                            {{-- </div> --}}
                            <div class="sale-details">
                                @php
                                    $usuario = Auth::user()->name;
                                    $nombre = explode(' ', trim($usuario));
                                @endphp
                                <h5 class="text-light"><strong> {{ ucwords($nombre[0]) }}</strong></h5>
                                {{-- <h3>725</h3> --}}
                            </div>
                            <div class="tile-count d-flex align-items-center justify-content-center flex-column fw-bold blue">
                                @php
                                    $hoy = Carbon\Carbon::now('America/Guatemala');
                                    $hoy = $hoy->format('Y-m-d');
                                    // $cita_count = \App\Models\Cita::where('fecha_cita',$hoy)->where('estado','Confirmada')->where('doctor_id',Auth::user()->id)->count();
                                @endphp
                                {{-- <i class="bi bi-shield-shaded font-1x"></i> --}}
                                {{-- <span>{{ $cita_count }}</span> --}}
                            </div>
                        </div>
                    </a>
                </div>
                @endif
                <div class="col-xxl-3 col-sm-6 col-12">
                    <a href="{{ url('users') }}">
                        <div class="stats-tile d-flex align-items-center position-relative tile-blue">
                            <div class="sale-icon icon-box xl rounded-5 me-3">
                                <i class="bi bi-people-fill font-2x text-blue"></i>
                            </div>
                            <div class="sale-details">
                                <h5 class="text-light">Usuarios</h5>
                                {{-- <h3>725</h3> --}}
                            </div>
                            <div class="tile-count d-flex align-items-center justify-content-center flex-column fw-bold blue">
                                @php
                                    $hoy = Carbon\Carbon::now('America/Guatemala');
                                    $hoy = $hoy->format('Y-m-d');
                                    // $cita_count = \App\Models\Cita::where('fecha_cita',$hoy)->where('estado','Confirmada')->count();
                                @endphp
                                {{-- <i class="bi bi-shield-shaded font-1x"></i> --}}
                                {{-- <span>{{ $cita_count }}</span> --}}
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-xxl-3 col-sm-12 col-12">
                    <a href="{{ url('config') }}">
                        <div class="stats-tile d-flex align-items-center position-relative tile-blue">
                            <div class="sale-icon icon-box xl rounded-5 me-3">
                                <i class="bi bi-gear font-2x text-blue"></i>
                            </div>
                            <div class="sale-details">
                                <h5 class="text-light">Configuración</h5>
                                {{-- <h3>95%</h3> --}}
                            </div>
                            <div class="tile-count d-flex align-items-center justify-content-center flex-column fw-bold blue">
                                {{-- @php
                                    $clientes_count = \App\Models\Cliente::where('estado',1)->count();
                                @endphp --}}
                                {{-- <i class="bi bi-shield-shaded font-1x"></i> --}}
                                {{-- <span>{{ $clientes_count }}</span> --}}
                            </div>
                        </div>
                    </a>
                </div>
                <hr>
                <div class="col-xxl-9 col-sm-12 col-12">
                    <a href="#">
                        <div class="stats-tile d-flex align-items-center position-relative tile-yellow">
                            <div class="sale-icon icon-box xl rounded-5 me-3">
                                <i class="bi bi-boxes font-2x text-yellow"></i>
                            </div>
                            <div class="sale-details">
                                <h5 class="text-light">Almacén</h5>

                                <div class="d-flex flex-wrap">
                                    <div class="me-3">
                                        <ul>
                                            <li>
                                                <a href="{{ url('categorias') }}" class="text-secondary">
                                                    <i class="bi bi-chevron-compact-right"></i>
                                                    <i class="bi bi-diagram-3"></i>
                                                    <u>Categorías</u>
                                                </a>
                                            </li>
                                            <li>
                                                {{-- <a href="{{ url('vehiculos') }}" class="text-primary">
                                                    <i class="bi bi-chevron-compact-right"></i>
                                                    <i class="bi bi-box"></i>
                                                    <u>Vehículos</u>
                                                </a> --}}
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="me-3">
                                        <ul>
                                            <li>
                                                <a href="{{ url('articulos') }}" class="text-secondary">
                                                    <i class="bi bi-chevron-compact-right"></i>
                                                    <i class="bi bi-box"></i>
                                                    <u>Artículos</u>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('servicios') }}" class="text-secondary">
                                                    <i class="bi bi-chevron-compact-right"></i>
                                                    <i class="bi bi-boxes"></i>
                                                    <u>Servicios</u>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    <div>
                                        <ul>
                                            <li>
                                                <a href="{{ url('unidades') }}" class="text-secondary">
                                                    <i class="bi bi-chevron-compact-right"></i>
                                                    <i class="bi bi-rulers"></i>
                                                    <u>Unidades de Medida</u>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('comisiones') }}" class="text-secondary">
                                                    <i class="bi bi-chevron-compact-right"></i>
                                                    <i class="bi bi-piggy-bank"></i>
                                                    <u>Tipos de Comisiones</u>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="tile-count d-flex align-items-center justify-content-center flex-column fw-bold blue">

                            </div> --}}
                        </div>
                    </a>
                </div>
                <hr>
                <div class="col-xxl-4 col-sm-6 col-12">
                    <a href="#">
                        <div class="stats-tile d-flex align-items-center position-relative tile-red">
                            <div class="sale-icon icon-box xl rounded-5 me-3">
                                <i class="bi bi-cart4 font-2x text-red"></i>
                            </div>
                            <div class="sale-details">
                                <h5 class="text-light">Compras</h5>

                                <div class="d-flex flex-wrap">
                                    <div class="me-3">
                                        <ul>
                                            <li>
                                                <a href="{{ url('ingresos') }}" class="text-secondary">
                                                    <i class="bi bi-chevron-compact-right"></i>
                                                    <i class="bi bi-cart-plus"></i>
                                                    <u>Ingresos</u>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('proveedores') }}" class="text-secondary">
                                                    <i class="bi bi-chevron-compact-right"></i>
                                                    <i class="bi bi-person-badge-fill"></i>
                                                    <u>Proveedores</u>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="tile-count d-flex align-items-center justify-content-center flex-column fw-bold blue">

                            </div> --}}
                        </div>
                    </a>
                </div>
                <div class="col-xxl-5 col-sm-6 col-12">
                    <a href="#">
                        <div class="stats-tile d-flex align-items-center position-relative tile-green">
                            <div class="sale-icon icon-box xl rounded-5 me-3">
                                <i class="bi bi-cash-stack font-2x text-green"></i>
                            </div>
                            <div class="sale-details">
                                <h5 class="text-light">Ventas</h5>

                                <div class="d-flex flex-wrap">
                                    <div class="me-3">
                                        <ul>
                                            <li>
                                                <a href="{{ url('clientes') }}" class="text-secondary">
                                                    <i class="bi bi-chevron-compact-right"></i>
                                                    <i class="bi bi-people"></i>
                                                    <u>Clientes</u>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('vehiculos') }}" class="text-secondary">
                                                    <i class="bi bi-chevron-compact-right"></i>
                                                    <i class="bi bi-car-front"></i>
                                                    <u>Vehículos</u>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="me-3">
                                        <ul>
                                            <li>
                                                <a href="{{ url('Ventas') }}" class="text-secondary">
                                                    <i class="bi bi-chevron-compact-right"></i>
                                                    <i class="bi bi-cash-stack"></i>
                                                    <u>Ventas</u>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ url('inventarios') }}" class="text-secondary">
                                                    <i class="bi bi-chevron-compact-right"></i>
                                                    <i class="bi bi-inboxes"></i>
                                                    <u>Inventario</u>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="tile-count d-flex align-items-center justify-content-center flex-column fw-bold blue">

                            </div> --}}
                        </div>
                    </a>
                </div>

            </div>
            <!-- Row end -->
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->
@endsection
