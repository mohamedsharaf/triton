<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SEDNA - @yield('title') </title>

        <link rel="shortcut icon" href="{!! asset('image/logo/favicon.ico') !!}"/>

    <link href="{!! asset('css/plugins/jQueryUI/jquery-ui-1.10.4.custom.min.css') !!}" rel="stylesheet">
    <link href="{!! asset('css/plugins/jqGrid/ui.jqgrid.css') !!}" rel="stylesheet">
        <link rel="stylesheet" href="{!! asset('css/vendor.css') !!}" />
        <link rel="stylesheet" href="{!! asset('css/app.css') !!}" />

        @yield('css_plugins')
        @yield('css')

    </head>
    <body>

        <!-- Wrapper-->
            <div id="wrapper">

                <!-- Navigation -->
                @include('layouts.navigation')

                <!-- Page wraper -->
                <div id="page-wrapper" class="gray-bg">

                    <!-- Page wrapper -->
                    @include('layouts.topnavbar')

                    <!-- Main view  -->
                    @yield('content')

                    <!-- Footer -->
                    @include('layouts.footer')

                </div>
                <!-- End page wrapper-->

            </div>
        <!-- End wrapper-->

        <script src="{!! asset('js/app.js') !!}" type="text/javascript"></script>

        @yield('js_plugins')
        @yield('js')

        {{-- @section('scripts')
        @show --}}

    </body>
</html>