@extends('inspinia_v27.app1')

@section('title', 'Centralizador de Asistencias')

@section('css_plugins')
    <link href="{!! asset('inspinia_v27/css/plugins/iCheck/custom.css') !!}" rel="stylesheet">
@endsection

@section('css')
    <style type="text/css">
        body:not(.mini-navbar){
            background-color: #262626;
        }
    </style>
@endsection

@section('content')

    <div>
        <img alt="image" class="img-circle" width="180" src="{!! asset('image/logo/logo_fge_256.png') !!}" />
    </div>

    <h3 class="text-white">Bienvenido a TRITON</h3>
    <p class="text-white">
        Centralizador de Asistencias
    </p>

    <form class="m-t" role="form" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <input id="email" type="email" class="form-control" name="email" placeholder="Correo electrónico" value="{{ old('email') }}" required autofocus>

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
            <div class="checkbox i-checks">
                <label class="text-white">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}><i></i> Recuérdame
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-success block full-width m-b">Iniciar sesión</button>

        <a href="{{ route('password.request') }}"><small>¿Olvidaste tu contraseña?</small></a>

        <p class="text-muted text-center"><small>¿No tienes cuenta?</small></p>
        <a class="btn btn-sm btn-white btn-block" href="{{ route('register') }}">Crear cuenta</a>
    </form>

@endsection

@section('js_plugins')
    <script src="{{ asset('inspinia_v27/js/plugins/iCheck/icheck.min.js') }}"></script>
@endsection

@section('js')
    <script>
        $(document).ready(function(){
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        });
    </script>
@endsection