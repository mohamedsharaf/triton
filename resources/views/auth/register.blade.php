@extends('inspinia_v27.app1')

@section('title', 'Registrarse')

@section('css')
    <style type="text/css">
        body:not(.mini-navbar){
            background-color: #262626;
        }
    </style>
@endsection

@section('content')
    <div>
        <img alt="image" class="img-circle" width="180" src="{!! asset('image/logo/logo_fge_256_2018_3.png') !!}" />
    </div>

    {{-- <h3 class="text-white">Registrarse</h3> --}}
    <p class="text-white">
        Escriba su información.
    </p>

    <form class="m-t" role="form" method="POST" action="{{ route('register') }}">
        {{ csrf_field() }}

        <div class="form-group{{ $errors->has('n_documento') ? ' has-error' : '' }}">
            <input id="n_documento" type="text" class="form-control" name="n_documento" placeholder="Cédula de identidad" value="{{ old('n_documento') }}" required autofocus>

            @if ($errors->has('n_documento'))
                <span class="help-block">
                    <strong>{{ $errors->first('n_documento') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <input id="name" type="text" class="form-control" name="name" placeholder="Nombre(s)" value="{{ old('name') }}" required autofocus>

            @if ($errors->has('name'))
                <span class="help-block">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('ap_paterno') ? ' has-error' : '' }}">
            <input id="ap_paterno" type="text" class="form-control" name="ap_paterno" placeholder="Apellido paterno" value="{{ old('ap_paterno') }}" required autofocus>

            @if ($errors->has('ap_paterno'))
                <span class="help-block">
                    <strong>{{ $errors->first('ap_paterno') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('ap_materno') ? ' has-error' : '' }}">
            <input id="ap_materno" type="text" class="form-control" name="ap_materno" placeholder="Apellido materno" value="{{ old('ap_materno') }}" required autofocus>

            @if ($errors->has('ap_materno'))
                <span class="help-block">
                    <strong>{{ $errors->first('ap_materno') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('f_nacimiento') ? ' has-error' : '' }}">
            <input id="f_nacimiento" type="text" class="form-control" name="f_nacimiento" placeholder="Fecha de nacimiento" value="{{ old('f_nacimiento') }}" required autofocus>

            @if ($errors->has('f_nacimiento'))
                <span class="help-block">
                    <strong>{{ $errors->first('f_nacimiento') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <input id="email" type="email" class="form-control" name="email" placeholder="Correo electrónico" value="{{ old('email') }}" required>

            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <input id="password" type="password" class="form-control" name="password" placeholder="Contraseña" required>

            @if ($errors->has('password'))
                <span class="help-block">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>

        <div class="form-group">
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirmar Contraseña" required>
        </div>

        <button type="submit" class="btn btn-success block full-width m-b">Registrarse</button>

        <p class="text-muted text-center"><small>¿Ya tienes una cuenta?</small></p>

        <a class="btn btn-sm btn-white btn-block" href="{{ route('login') }}">Iniciar sesión</a>
    </form>
@endsection