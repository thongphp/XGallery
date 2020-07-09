<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>XGallery - {{$title}}</title>

@section('stylesheets')
    <!-- Fonts -->
        <link href="//fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link href="{{ asset('storage/vendor/AdminLTE/dist/css/adminlte.min.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('storage/css/xgallery.css') }}" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css"/>
        <link rel="stylesheet" href="//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"/>
        <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
              integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk"
              crossorigin="anonymous">

        <link rel="stylesheet"
              href="{{ asset('storage/vendor/AdminLTE/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}"/>
        <link rel="stylesheet"
              href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css"/>
        <link rel="stylesheet"
              href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"/>
        <link rel="stylesheet"
              href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    @show
    @include('includes.head.metadata')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico"/>

</head>
<body class="app-{{App::environment()}} sidebar-mini layout-fixed sidebar-open">
@include('includes.navbar.top')
@include('includes.navbar.sidebar')

<div class="content-wrapper" style="min-height: 400px; background: none;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 messages mt-2">
            </div>
        </div>
        <div aria-live="polite" aria-atomic="true" style="position: relative; z-index: 9999;">
            <!-- Position it -->
            <div style="position: fixed; top: 15px; right: 30px; z-index: 1" class="toast-container">

                <!-- Then put toasts within -->
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                @include('includes.flash')
            </div>
            <div class="col-12">
                @yield('content')
            </div>
        </div>
    </div>
</div>
<div id="overlay">
    <div class="d-flex justify-content-center">
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</div>
@include('includes.footer')
@section('scripts')
    <script
        src="//code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
        crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>
    <script src="//stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
            integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI"
            crossorigin="anonymous"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <script src="{{ asset('storage/vendor/AdminLTE/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('storage/vendor/AdminLTE/dist/js/demo.js') }}"></script>
    <!--<script src="{{ asset('storage/vendor/AdminLTE/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>-->
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/vanilla-lazyload@15.1.1/dist/lazyload.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('storage/js/xgallery.js') }}"></script>
    <script>
        jQuery(document).ready(function () {
            xgallery.ajax.init();
            var lazyLoadInstance = new LazyLoad({
                elements_selector: ".lazy"
                // ... more custom settings?
            });
        })
    </script>
@show
</body>
</html>
