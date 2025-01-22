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

            @include('admin.cliente.search')

            <!-- Row start -->
            <div class="row gx-3">
                <div class="col-sm-12 col-12">
                    <div class="card">

                        <div class="card-header">
                            <div class="card-title">
                                Listado de Clientes
                                <br>
                                <a target="_blank" href="{{ url('pdf-clientes') }}" type="button" class="btn btn-info btn-sm">
                                    <i class="bi bi-printer"></i> Imprimir
                                </a>
                                {{-- <a arget="_blank" href="{{ url('exportclientes') }}" type="button" class="btn btn-success btn-sm">
                                    <i class="bi bi-file-earmark-excel-fill"></i> Excel
                                </a> --}}
                                <a href="{{ url('add-cliente') }}" type="button" class="btn btn-success float-end">
                                    <i class="bi bi-plus-square"></i> Agregar
                                </a>
                            </div>

                        </div>
                        <div class="card-body">


                            <div class="table-responsive">
                                <table id="highlightRowColumn" class="table custom-table">
                                    <thead>
                                        <tr>
                                            <td align="center"><i class="bi bi-list-task"></i></td>
                                            <td>Cliente</td>
                                            <td>Fecha de Nacimiento</td>
                                            <td>DPI</td>
                                            <td>NIT</td>
                                            <td>Dirección</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($clientes as $cliente)
                                        <tr>
                                            <td>
                                                <div class="btn-group dropend">
                                                    <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                                        <i class="bi bi-list-task"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ url('show-cliente/'.$cliente->id) }}"><i class="bi bi-eye-fill text-blue"></i> Información</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ url('edit-cliente/'.$cliente->id) }}"><i class="bi bi-pencil-fill text-warning"></i> Editar</a>
                                                        </li>
                                                        <li>
                                                            <a type="button" class="btn bg-gradient-danger" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $cliente->id }}">
                                                                <i class="bi bi-trash-fill text-danger"></i> Eliminar
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
                                            <td align="center">
                                                <small>
                                                    {{ $fnacimiento }}
                                                </small>
                                            </td>
                                            <td align="center">
                                                <small>
                                                    {{ $cliente->dpi }}
                                                </small>
                                            </td>
                                            <td align="center">
                                                <small>
                                                    {{ $cliente->nit }}
                                                </small>
                                            </td>
                                            <td><small>{{ $cliente->direccion }}</small></td>
                                        </tr>
                                        @include('admin.cliente.deletemodal')
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $clientes->links() }}
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

