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
                                            <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA" role="tab"
                                                aria-controls="oneA" aria-selected="true">Editar Información</a>
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
                                                    <form action="{{ url('update-user/'.$user->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="row gx-3">

                                                            <div class="col-md-3 mb-3">
                                                                <!-- Form Field Start -->
                                                                <div class="mb-3">
                                                                    <label for="name" class="form-label">Nombre</label>
                                                                    <input name="name" type="text" class="form-control" placeholder="Nombre Completo..." value="{{ $user->name }}" />
                                                                    @if ($errors->has('name'))
                                                                        <span class="help-block opacity-7">
                                                                                <strong>
                                                                                    <font color="red">{{ $errors->first('name') }}</font>
                                                                                </strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3 mb-3">
                                                                <!-- Form Field Start -->
                                                                <div class="mb-3">
                                                                    <label for="birthDay" class="form-label">Fecha de Nacimiento</label>
                                                                    <div class="input-group">
                                                                        <input type="date" name="fecha_nacimiento" class="form-control text-center" value="{{ $user->fecha_nacimiento }}" required/>
                                                                        @if ($errors->has('fecha_nacimiento'))
                                                                            <span class="help-block opacity-7">
                                                                                    <strong>
                                                                                        <font color="red">{{ $errors->first('fecha_nacimiento') }}</font>
                                                                                    </strong>
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-md-3 mb-3">
                                                                <!-- Form Field Start -->
                                                                <div class="mb-3">
                                                                    <label for="email" class="form-label">Email</label>
                                                                    <input name="email" type="text" class="form-control" placeholder="Correo electronico..." value="{{ $user->email }}" />
                                                                    @if ($errors->has('email'))
                                                                        <span class="help-block opacity-7">
                                                                                <strong>
                                                                                    <font color="red">{{ $errors->first('email') }}</font>
                                                                                </strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <!-- Form Field Start -->
                                                                <div class="mb-3">
                                                                    <label for="telefono" class="form-label">Teléfono</label>
                                                                    <input name="telefono" type="number" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control" placeholder="Telefono..." value="{{ $user->telefono }}" />
                                                                    @if ($errors->has('telefono'))
                                                                        <span class="help-block opacity-7">
                                                                                <strong>
                                                                                    <font color="red">{{ $errors->first('telefono') }}</font>
                                                                                </strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <!-- Form Field Start -->
                                                                <div class="mb-3">
                                                                    <label for="celular" class="form-label">Celular</label>
                                                                    <input name="celular" type="number" oninput="this.value = this.value.replace(/[^0-9]/g, '');" class="form-control" placeholder="Celular..." value="{{ $user->celular }}"/>
                                                                    @if ($errors->has('celular'))
                                                                        <span class="help-block opacity-7">
                                                                                <strong>
                                                                                    <font color="red">{{ $errors->first('celular') }}</font>
                                                                                </strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4 mb-3">
                                                                <!-- Form Field Start -->
                                                                <div class="mb-3">
                                                                    <label class="form-label">Cambiar Imagen</label>
                                                                    <input type="file" name="fotografia" class="form-control border">
                                                                    @if ($errors->has('fotografia'))
                                                                        <span class="help-block opacity-7">
                                                                                <strong>
                                                                                    <font color="red">{{ $errors->first('fotografia') }}</font>
                                                                                </strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12 mb-3">
                                                                <!-- Form Field Start -->
                                                                <div class="mb-3">
                                                                    <label class="form-label">Dirección</label>
                                                                    <textarea name="direccion" class="form-control" rows="3" placeholder="Direccion de la persona...">{{ $user->direccion }}</textarea>
                                                                    @if ($errors->has('direccion'))
                                                                        <span class="help-block opacity-7">
                                                                                <strong>
                                                                                    <font color="red">{{ $errors->first('direccion') }}</font>
                                                                                </strong>
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="d-flex gap-2 justify-content-center">
                                                            <a href="{{ url('show-user/'.$user->id) }}" type="button" class="btn btn-danger">
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
        </div>
        <!-- Content wrapper end -->
    </div>
    <!-- Content wrapper scroll end -->

@endsection
