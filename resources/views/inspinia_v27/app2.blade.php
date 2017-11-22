<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
            <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title') </title>

        <!-- ICO -->
            <link rel="shortcut icon" href="{!! asset('image/logo/favicon.ico') !!}"/>

        <!-- Styles -->
            <link href="{!! asset('inspinia_v27/css/bootstrap.min.css') !!}" rel="stylesheet">
            <link href="{!! asset('inspinia_v27/font-awesome-47/css/font-awesome.min.css') !!}" rel="stylesheet">
            <link href="{!! asset('inspinia_v27/css/animate.css') !!}" rel="stylesheet">

            @yield('css_plugins')

            <link href="{!! asset('inspinia_v27/css/style.css') !!}" rel="stylesheet">

            @yield('css')

            <!-- Scripts -->
            <script>
              window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
              ]) !!};
            </script>
    </head>

    <body>
        <!-- Wrapper-->
            <div id="wrapper">

                <!-- Navigation -->
                @include('inspinia_v27.navigation2')

                <!-- Page wraper -->
                <div id="page-wrapper" class="gray-bg">

                    <!-- Page wrapper -->
                    @include('inspinia_v27.topnavbar2')

                    <!-- Main view  -->
                    @yield('content')

                    <!-- Footer -->
                    @include('inspinia_v27.footer2')

                </div>
                <!-- End page wrapper-->

            </div>
        <!-- End wrapper-->

        <!-- Scripts -->
            <!-- Mainly scripts -->
                <script src="{{ asset('inspinia_v27/js/jquery-3.1.1.min.js') }}"></script>
                <script src="{{ asset('inspinia_v27/js/bootstrap.min.js') }}"></script>
                <script src="{{ asset('inspinia_v27/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
                <script src="{{ asset('inspinia_v27/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

            @yield('js_plugins')
            @yield('js')
    </body>
</html>
