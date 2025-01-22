@extends('layouts.app')

@section('content')

    <body class="login-container">
        <!-- Login box start -->
        <div class="container">
            <form method="POST" action="{{ route('login') }}">
            @csrf
                <div class="login-box rounded-2 p-5">
                    <div class="login-form">

                        <a href="{{ url('/') }}" class="login-logo mb-3 align-self-center d-flex justify-content-center">
                            <div class="border border-primary custom-bg p-2 rounded" style="width: 100%;">
                                <img src="{{ asset('img/logo.png') }}" alt="Jireh Automotriz" class="img-fluid w-100" />
                            </div>
                        </a>
                        <h3 class="fw-bold text-center mb-3"><strong><u>Iniciar sesión</u></strong></h3>
                        <h5 class="fw-light mb-3">
                            Coloca tus credenciales para poder iniciar sesión.
                        </h5>


                            <div class="mb-3">

                                <label class="form-label">Tu Email</label>
                                <input type="email" id="email" class="form-control" placeholder="Ingresa tu email" @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <div class="alert alert-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">

                                <label class="form-label">Tu Contraseña</label>
                                <input id="password" type="password" class="form-control" placeholder="Ingresa tu contraseña" @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                @error('password')
                                    <div class="alert alert-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                @enderror
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="rememberPassword">Recordar</label>
                                </div>
                                <a href="{{ route('password.request') }}" class="text-blue text-decoration-underline">¿Olvidaste tu contraseña?</a>
                            </div>
                            <div class="d-grid py-3">
                                <button type="submit" class="btn btn-lg btn-primary">
                                    Iniciar sesión
                                </button>
                            </div>
                        </form>
                        {{-- <div class="text-center py-3">or login with</div>
                        <div class="d-flex gap-2 justify-content-center">
                            <button type="submit" class="btn btn-outline-light">
                                <img src="assets/images/google.svg" class="login-icon" alt="Login with Google" />
                            </button>
                            <button type="submit" class="btn btn-outline-light">
                                <img src="assets/images/facebook.svg" class="login-icon" alt="Login with Facebook" />
                            </button>
                        </div> --}}
                        {{-- <div class="text-center pt-3">
                            <span>¿No está registrado?</span>
                            <a href="signup.html" class="text-blue text-decoration-underline ms-2">
                                Crear una cuenta</a>
                        </div> --}}
                    </div>
                </div>
            </form>
        </div>
        <!-- Login box end -->
    </body>

@endsection
