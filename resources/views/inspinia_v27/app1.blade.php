<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') </title>
    <link rel="shortcut icon" href="{!! asset('image/logo/favicon.ico') !!}"/>

    <!-- Styles -->
    <link href="{!! asset('inspinia_v27/css/bootstrap.min.css') !!}" rel="stylesheet">
    <link href="{!! asset('inspinia_v27/font-awesome/css/font-awesome.css') !!}" rel="stylesheet">

    <link href="{!! asset('inspinia_v27/css/animate.css') !!}" rel="stylesheet">
    <link href="{!! asset('inspinia_v27/css/style.css') !!}" rel="stylesheet">

    @yield('css_plugins')
    @yield('css')

    <!-- Scripts -->
    <script>
      window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
      ]) !!};
    </script>
  </head>

  <body class="black-bg">
    <div class="middle-box text-center loginscreen animated fadeInDown">
      <div>
        @yield('content')

        <p class="m-t text-white"> <small><strong>Copyright &copy;</strong> Fiscalía General del Estado 2012-{{date('Y')}}</small> </p>
      </div>
    </div>

    <!-- Scripts -->
      <script src="{{ asset('inspinia_v27/js/jquery-3.1.1.min.js') }}"></script>
      <script src="{{ asset('inspinia_v27/js/bootstrap.min.js') }}"></script>

    @yield('js_plugins')
    @yield('js')
  </body>
</html>