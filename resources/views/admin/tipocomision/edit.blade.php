@extends('layouts.admin')
@section('content')

    <!-- Content wrapper scroll start -->
    <div class="content-wrapper-scroll">

        <!-- Main header starts -->
        <div class="main-header d-flex align-items-center justify-content-between position-relative">
            <div class="d-flex align-items-center justify-content-center">
                <div class="page-icon">
                    <i class="bi bi-piggy-bank"></i>
                </div>
                <div class="page-title">
                    <h5>Tipos de Comisiones</h5>
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
                                                <form action="{{ url('update-tipo-comision/'.$tipocomision->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="row gx-3">

                                                        <div class="col-md-4 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="nombre" class="form-label">Nombre</label>
                                                                <input name="nombre" type="text" class="form-control" placeholder="Nombre de la tipo de comision..." value="{{ $tipocomision->nombre }}" />
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
                                                                <label for="descripcion" class="form-label">Descripción</label>
                                                                <input name="descripcion" type="text" class="form-control" placeholder="Descripción de la tipo de comision..." value="{{ $tipocomision->descripcion }}" />
                                                                @if ($errors->has('descripcion'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('descripcion') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="porcentaje" class="form-label">Porcentaje</label>
                                                                <input name="porcentaje" type="number" step="0.01" class="form-control" placeholder="0.00" value="{{ $tipocomision->porcentaje }}" />
                                                                @if ($errors->has('porcentaje'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('porcentaje') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="d-flex gap-2 justify-content-center">
                                                        <a href="{{ url('unidads') }}" type="button" class="btn btn-danger">
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
