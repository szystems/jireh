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
            @if (session('resent'))
                <div class="alert alert-success" role="alert">
                    {{ __('Se ha enviado un nuevo enlace de verificación a su dirección de correo electrónico.') }}
                </div>
            @endif
            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div class="login-box rounded-2 p-5">
                    <div class="login-form">
                        <a href="{{ url('/') }}" class="login-logo mb-3 align-self-center d-flex justify-content-center">
                            <div class="border border-primary custom-bg p-2 rounded" style="width: 100%;">
                                <img src="{{ asset('img/logo.png') }}" alt="Jireh Automotriz" class="img-fluid w-100" />
                            </div>
                        </a>
                        <h3 class="fw-bold text-center mb-3"><strong><u>Verifica tu dirección tu email</u></strong></h3>
                        <h5 class="fw-light mb-3">Antes de continuar, revise su correo electrónico para obtener un enlace de verificación.</h5>
                        <h5 class="fw-light mb-3">Si no recibiste el correo electrónico.</h5>
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('Haga clic aquí para solicitar otro') }}</button>.
                        </form>
                    </div>
                </div>
            </form>
        </div>
        <!-- Login box end -->
    </body>

@endsection
