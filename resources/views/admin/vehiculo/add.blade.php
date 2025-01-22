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
                                <ul class="nav nav-tabs" id="customTab2" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA" role="tab"
                                            aria-controls="oneA" aria-selected="true">Agregar Vehiculo</a>
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
                                                <form action="{{ url('insert-vehiculo') }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="row gx-3">

                                                        <div class="col-md-12 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="fcliente" class="form-label">Cliente</label>
                                                                <select name="cliente_id" id="fcliente" class="form-select select2" aria-label="Default select example" style="width: 100%;">
                                                                    <option value=""{{ request('cliente_id') == '' ? ' selected' : '' }}>Todos</option>
                                                                    @foreach($clientes as $cliente)
                                                                        <option value="{{ $cliente->id }}"{{ old('cliente_id', request('cliente_id')) == $cliente->id ? ' selected' : '' }}>
                                                                            {{ $cliente->nombre }}, DPI: {{ $cliente->dpi }}, NIT: {{ $cliente->nit }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <a href="{{ url('add-cliente') }}" class="text-primary"><i class="bi bi-plus-square"></i> Agregar Cliente</a>
                                                            </div>
                                                        </div>

                                                        <script>
                                                            $(document).ready(function() {
                                                                $('#fcliente').select2({
                                                                    placeholder: 'Seleccione cliente',
                                                                    allowClear: true,
                                                                    minimumInputLength: 1,
                                                                    language: {
                                                                        inputTooShort: function() {
                                                                            return "Por favor, ingrese 1 o más caracteres"; // Cambia el texto aquí
                                                                        }
                                                                    }
                                                                });
                                                            });
                                                        </script>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="marca" class="form-label">Marca</label>
                                                                <input name="marca" type="text" class="form-control" placeholder="Marca del vehiculo..." value="{{ old('marca') }}" />
                                                                @if ($errors->has('marca'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('marca') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="modelo" class="form-label">Modelo</label>
                                                                <input name="modelo" type="text" class="form-control" placeholder="Modelo del vehiculo..." value="{{ old('modelo') }}" />
                                                                @if ($errors->has('modelo'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('modelo') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="ano" class="form-label">Año</label>
                                                                <select name="ano" class="form-control">
                                                                    <option value="">Seleccione el año...</option>
                                                                    @for ($year = date('Y'); $year >= 1900; $year--)
                                                                        <option value="{{ $year }}" {{ old('ano') == $year ? 'selected' : '' }}>{{ $year }}</option>
                                                                    @endfor
                                                                </select>
                                                                @if ($errors->has('ano'))
                                                                    <span class="help-block opacity-7">
                                                                        <strong>
                                                                            <font color="red">{{ $errors->first('ano') }}</font>
                                                                        </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="color" class="form-label">Color</label>
                                                                <input name="color" type="text" class="form-control" placeholder="Color del vehiculo..." value="{{ old('color') }}" />
                                                                @if ($errors->has('color'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('color') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="placa" class="form-label">Placa</label>
                                                                <input name="placa" type="text" class="form-control" placeholder="Placa del vehiculo..." value="{{ old('placa') }}" />
                                                                @if ($errors->has('placa'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('placa') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label for="vin" class="form-label">VIN</label>
                                                                <input name="vin" type="text" class="form-control" placeholder="VIN del vehiculo..." value="{{ old('vin') }}" />
                                                                @if ($errors->has('vin'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('vin') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 mb-3">
                                                            <!-- Form Field Start -->
                                                            <div class="mb-3">
                                                                <label class="form-label">Imagen</label>
                                                                <input type="file" name="fotografia" class="form-control border" value="{{ old('fotografia') }}">
                                                                @if ($errors->has('fotografia'))
                                                                    <span class="help-block opacity-7">
                                                                            <strong>
                                                                                <font color="red">{{ $errors->first('fotografia') }}</font>
                                                                            </strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <hr>

                                                    </div>
                                                    <div class="d-flex gap-2 justify-content-center">
                                                        <a href="{{ url('vehiculos') }}" type="button" class="btn btn-danger">
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
