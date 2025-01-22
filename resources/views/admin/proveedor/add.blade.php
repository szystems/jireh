@extends('layouts.admin')
@section('content')

    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-person-video2"></i>
                </div>
                <div class="page-title">
                    <h5>Proveedores</h5>
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
                                <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA" role="tab"
                                            aria-controls="oneA" aria-selected="true">Crear Proveedor</a>
                                    </li>
                                </ul>
                                <div class="tab-content h-350">
                                    <div class="tab-pane fade show active" id="oneA" role="tabpanel">
                                        <!-- Row start -->
                                        <div class="row gx-3">
                                            <div class="col-sm-12 col-12">
                                                @if (count($errors)>0)
                                                    <div class="alert alert-danger text-white" role="alert">
                                                        <ul>
                                                            @foreach ($errors->all() as $error)
                                                                <li>{{$error}}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>

                                                @endif
                                                <form action="{{ url('insert-proveedor') }}" method="POST">
                                                    @csrf
                                                    <div class="row gx-3">

                                                        <div class="col-md-8 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="nombre" class="form-label">Nombre del Proveedor</label>
                                                                <input name="nombre" type="text" class="form-control" placeholder="Nombre de la proveedor..." value="{{ old('nombre') }}" />
                                                                @if ($errors->has('nombre'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('nombre') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="nit" class="form-label">NIT</label>
                                                                <input name="nit" type="text" class="form-control" placeholder="# de NIT..." value="{{ old('nit') }}" />
                                                                @if ($errors->has('nit'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('nit') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="contacto" class="form-label">Contacto del Proveedor</label>
                                                                <input name="contacto" type="text" class="form-control" placeholder="Contacto del proveedor..." value="{{ old('contacto') }}" />
                                                                @if ($errors->has('contacto'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('contacto') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="telefono" class="form-label">Teléfono del Proveedor</label>
                                                                <input name="telefono" type="number" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control" placeholder="Telefono del proveedor..." value="{{ old('telefono') }}" />
                                                                @if ($errors->has('telefono'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('telefono') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="celular" class="form-label">Celular del Proveedor</label>
                                                                <input name="celular" type="number" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control" placeholder="Celular del proveedor..." value="{{ old('celular') }}" />
                                                                @if ($errors->has('celular'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('celular') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="direccion" class="form-label">Dirección del Proveedor</label>
                                                                <input name="direccion" type="text" class="form-control" placeholder="Dirección del proveedor..." value="{{ old('direccion') }}" />
                                                                @if ($errors->has('direccion'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('direccion') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="email" class="form-label">Email del Proveedor</label>
                                                                <input name="email" type="text" class="form-control" placeholder="Email del proveedor..." value="{{ old('email') }}" />
                                                                @if ($errors->has('email'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('email') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="banco" class="form-label">Banco del Proveedor</label>
                                                                <input name="banco" type="text" class="form-control" placeholder="Banco del proveedor..." value="{{ old('banco') }}" />
                                                                @if ($errors->has('banco'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('banco') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="nombre_cuenta" class="form-label">Nombre de Cuenta del Proveedor</label>
                                                                <input name="nombre_cuenta" type="text" class="form-control" placeholder="Nombre de cuenta del proveedor..." value="{{ old('nombre_cuenta') }}" />
                                                                @if ($errors->has('nombre_cuenta'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('nombre_cuenta') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="tipo_cuenta" class="form-label">Tipo de Cuenta del Proveedor</label>
                                                                <select name="tipo_cuenta" class="form-select">
                                                                    <option value="">Seleccione Tipo</option>
                                                                    <option value="Monetaria">Monetaria</option>
                                                                    <option value="Ahorro">Ahorro</option>
                                                                </select>
                                                                @if ($errors->has('tipo_cuenta'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('tipo_cuenta') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="numero_cuenta" class="form-label">Número de Cuenta del Proveedor</label>
                                                                <input name="numero_cuenta" type="text" class="form-control" placeholder="Número de cuenta del proveedor..." value="{{ old('numero_cuenta') }}" />
                                                                @if ($errors->has('numero_cuenta'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('numero_cuenta') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="d-flex gap-2 justify-content-center">
                                                        <a href="{{ url('proveedores') }}" type="button" class="btn btn-danger">
                                                            <i class="bi bi-x-circle"></i> Cancelar
                                                        </a>
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="bi bi-check2-square"></i> Grabar
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!-- Row end -->
                                    </div>

                                </div>
                                {{-- <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ url('edit-user/'.$user->id) }}" type="button" class="btn btn-outline-secondary">
                                        Cancelar
                                    </a>
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
