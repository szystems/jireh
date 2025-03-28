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
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="position-relative" id="profile-container">
                                    @if ($user->fotografia != null)
                                        <img id="profileImage" src="{{ asset('assets/imgs/users/'.$user->fotografia) }}" class="img-7xx rounded-circle shadow border border-2 border-light" />
                                    @else
                                        <img id="profileImage" src="{{ asset('assets/imgs/users/usericon4.png') }}" class="img-7xx rounded-circle shadow border border-2 border-light" />
                                    @endif
                                    <div class="position-absolute bottom-0 end-0">
                                        <label for="imageUpload" class="btn btn-sm btn-primary rounded-circle">
                                            <i class="bi bi-camera-fill"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <h5 class="text-primary mb-1">Editar Usuario</h5>
                                <h4 class="m-0">{{ $user->name }}</h4>
                                <span class="badge {{ $user->role_as == 0 ? 'bg-danger' : 'bg-success' }}">
                                    {{ $user->role_as == 0 ? 'Administrador' : 'Vendedor' }}
                                </span>
                            </div>
                            <div class="col-12 col-md-auto">
                                <div class="btn-group">
                                    <a href="{{ url('show-user/'.$user->id) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left"></i> Volver al perfil
                                    </a>
                                    <a href="{{ url('users') }}" class="btn btn-outline-info">
                                        <i class="bi bi-list"></i> Listado
                                    </a>
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
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title"><i class="bi bi-pencil-square"></i> Editar Información</h5>
                            </div>
                            <div class="card-body">
                                @if (count($errors)>0)
                                    <div class="alert alert-danger" role="alert">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{$error}}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ url('update-user/'.$user->id) }}" method="POST" enctype="multipart/form-data" id="userForm">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="row gx-3">
                                                <div class="col-md-6 mb-3">
                                                    <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                                        <input name="name" type="text" class="form-control" placeholder="Nombre completo" value="{{ $user->name }}" required />
                                                    </div>
                                                    @if ($errors->has('name'))
                                                        <div class="text-danger mt-1">{{ $errors->first('name') }}</div>
                                                    @endif
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class::bi bi-envelope-fill"></i></span>
                                                        <input name="email" type="email" class="form-control" placeholder="correo@ejemplo.com" value="{{ $user->email }}" required />
                                                    </div>
                                                    @if ($errors->has('email'))
                                                        <div class="text-danger mt-1">{{ $errors->first('email') }}</div>
                                                    @endif
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-calendar-date"></i></span>
                                                        <input type="date" name="fecha_nacimiento" class="form-control" value="{{ $user->fecha_nacimiento }}" required/>
                                                    </div>
                                                    @if ($errors->has('fecha_nacimiento'))
                                                        <div class="text-danger mt-1">{{ $errors->first('fecha_nacimiento') }}</div>
                                                    @endif
                                                </div>

                                                @if(isset($isAdmin) && $isAdmin)
                                                <div class="col-md-6 mb-3">
                                                    <label for="role_as" class="form-label">Tipo de Usuario</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-shield-fill"></i></span>
                                                        <select name="role_as" class="form-select">
                                                            <option value="0" {{ $user->role_as == 0 ? 'selected' : '' }}>Administrador</option>
                                                            <option value="1" {{ $user->role_as == 1 ? 'selected' : '' }}>Vendedor</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                @endif

                                                <div class="col-md-6 mb-3">
                                                    <label for="telefono" class="form-label">Teléfono</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                                                        <input name="telefono" type="text" pattern="[0-9]*" inputmode="numeric" class="form-control" placeholder="Teléfono fijo" value="{{ $user->telefono }}" />
                                                    </div>
                                                    @if ($errors->has('telefono'))
                                                        <div class="text-danger mt-1">{{ $errors->first('telefono') }}</div>
                                                    @endif
                                                </div>

                                                <div class="col-md-6 mb-3">
                                                    <label for="celular" class="form-label">Celular / WhatsApp</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-phone-fill"></i></span>
                                                        <input name="celular" type="text" pattern="[0-9]*" inputmode="numeric" class="form-control" placeholder="Número de celular" value="{{ $user->celular }}"/>
                                                    </div>
                                                    @if ($errors->has('celular'))
                                                        <div class="text-danger mt-1">{{ $errors->first('celular') }}</div>
                                                    @endif
                                                </div>

                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label">Dirección</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="bi bi-geo-alt-fill"></i></span>
                                                        <textarea name="direccion" class="form-control" rows="3" placeholder="Dirección completa">{{ $user->direccion }}</textarea>
                                                    </div>
                                                    @if ($errors->has('direccion'))
                                                        <div class="text-danger mt-1">{{ $errors->first('direccion') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="card mb-3">
                                                <div class="card-header">
                                                    <h6 class="mb-0">Fotografía del Usuario</h6>
                                                </div>
                                                <div class="card-body text-center">
                                                    <div class="image-preview mb-3">
                                                        @if ($user->fotografia != null)
                                                            <img id="preview" src="{{ asset('assets/imgs/users/'.$user->fotografia) }}" class="img-fluid rounded" style="max-height: 200px" alt="Vista previa de la imagen">
                                                        @else
                                                            <img id="preview" src="{{ asset('assets/imgs/users/usericon4.png') }}" class="img-fluid rounded" style="max-height: 200px" alt="Vista previa de la imagen">
                                                        @endif
                                                    </div>
                                                    <input type="file" name="fotografia" id="imageUpload" class="form-control border" accept="image/*" style="display: none;">
                                                    <label for="imageUpload" class="btn btn-outline-primary">
                                                        <i class="bi bi-upload"></i> Cambiar imagen
                                                    </label>
                                                    <p class="text-muted small mt-2">Formatos permitidos: JPG, PNG, GIF. Máximo 3MB.</p>
                                                    @if ($errors->has('fotografia'))
                                                        <div class="text-danger mt-1">{{ $errors->first('fotografia') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 justify-content-center mt-4">
                                        <a href="{{ url('show-user/'.$user->id) }}" class="btn btn-danger">
                                            <i class="bi bi-x-circle"></i> Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-check2-square"></i> Guardar Cambios
                                        </button>
                                    </div>
                                </form>
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

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Vista previa de imagen - Versión mejorada
            $("#imageUpload").change(function() {
                const file = this.files[0];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(event) {
                        // Actualizar ambas imágenes con animación
                        $("#preview").fadeOut(300, function() {
                            $(this).attr("src", event.target.result).fadeIn(300);
                        });
                        $("#profileImage").fadeOut(300, function() {
                            $(this).attr("src", event.target.result).fadeIn(300);
                        });
                    };
                    reader.readAsDataURL(file);
                } else {
                    // Si no hay archivo seleccionado, volver a la imagen predeterminada o la existente
                    @if ($user->fotografia != null)
                        var defaultImg = "{{ asset('assets/imgs/users/'.$user->fotografia) }}";
                    @else
                        var defaultImg = "{{ asset('assets/imgs/users/usericon4.png') }}";
                    @endif

                    $("#preview").attr("src", defaultImg);
                    $("#profileImage").attr("src", defaultImg);
                }
            });

            // Validación del formulario
            $("#userForm").submit(function(e) {
                var email = $("input[name='email']").val();
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!emailRegex.test(email)) {
                    e.preventDefault();
                    alert("Por favor ingrese un correo electrónico válido");
                    return false;
                }

                return true;
            });

            // Convertir inputs de teléfono a solo números
            $("input[name='telefono'], input[name='celular']").on('input', function() {
                $(this).val($(this).val().replace(/[^0-9]/g, ''));
            });
        });
    </script>
    @endpush

@endsection
