@extends('layouts.app')

@section('content')

    <body class="login-container">
        <!-- Login box start -->
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('password.email') }}">
            @csrf
                <div class="login-box rounded-2 p-5">
                    <div class="login-form">
                        <a href="{{ url('/') }}" class="login-logo mb-3 align-self-center d-flex justify-content-center">
                            <div class="border border-primary custom-bg p-2 rounded" style="width: 100%;">
                                <img src="{{ asset('img/logo.png') }}" alt="Jireh Automotriz" class="img-fluid w-100" />
                            </div>
                        </a>
                        <h3 class="fw-bold text-center mb-3"><strong><u>Recuperar acceso</u></strong></h3>
                        <h5 class="fw-light mb-3">
                            Para acceder a su cuenta, ingrese el correo electrónico que
                            pertenece a su cuenta.
                        </h5>
                        <div class="mb-3">
                            <label class="form-label">Tu Email</label>
                            <input id="email" type="email" placeholder="Ingresa tu email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="d-grid pt-3">
                            <button type="submit" class="btn btn-lg btn-primary">
                                Enviar enlace de restablecimiento
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- Login box end -->
    </body>

@endsection
