@extends('inspinia_v27.app1')

@section('title', 'Restablecer contraseña')

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

    <h3 class="text-white">Restablecer contraseña</h3>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <form class="m-t" role="form" method="POST" action="{{ route('password.email') }}">
        {{ csrf_field() }}

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <input id="email" type="email" class="form-control" name="email" placeholder="Correo electrónico" value="{{ old('email') }}" required autofocus>

            @if ($errors->has('email'))
                <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>

        <button type="submit" class="btn btn-success block full-width m-b">Enviar enlace para restablecer contraseña</button>

        <p class="text-muted text-center"><small>¿Ya tienes una cuenta?</small></p>

        <a class="btn btn-sm btn-white btn-block" href="{{ route('login') }}">Iniciar sesión</a>

        <br>

        <a href="https://docs.google.com/document/d/1hO68gnG1oNGdfIx2v7kGimGqL4IFW0JO71q_2cNr6B0/edit?usp=sharing" class="btn btn-sm btn-warning btn-block" target="_blank">Manual</a>
    </form>

@endsection