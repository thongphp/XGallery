<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

@section('stylesheets')
    <!-- Fonts -->
        <link href="//fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <!-- AdminLTE -->
        <link href="{{ asset('storage/vendor/AdminLTE/dist/css/adminlte.min.css') }}" rel="stylesheet" type="text/css">
        <link rel="stylesheet"
              href="{{ asset('storage/vendor/AdminLTE/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}"/>

        <!-- Icons -->
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css"/>
        <link rel="stylesheet" href="//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"/>

        <!-- Bootstrap -->
        <link rel="stylesheet" href="//stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>

        <link rel="stylesheet"
              href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css"/>
        <link rel="stylesheet"
              href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"/>
        <link rel="stylesheet"
              href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
        <link href="{{ asset('storage/css/xgallery.css') }}" rel="stylesheet" type="text/css">
    @show
    {!! \Butschster\Head\Facades\Meta::toHtml() !!}
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
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
                @section('guest-notice')
                    @guest
                        <div class="alert alert-info text-center">
                            <div>Please <strong><a class="text-danger"
                                                   href="{{route('oauth.login')}}">Login</a></strong> with Google to use
                                features
                            </div>
                        </div>
                    @endguest
                @stop
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
<div class="modal fade" id="xgallery-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer hidden"></div>
        </div>
    </div>
</div>
@include('includes.footer')
@section('scripts')
    <script
        src="//code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
        crossorigin="anonymous"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <script src="//cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
            integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
            crossorigin="anonymous"></script>

    <script src="//stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Bootstrap -->
    <script src="{{ asset('storage/vendor/AdminLTE/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('storage/vendor/AdminLTE/dist/js/demo.js') }}"></script>
    <!--<script src="{{ asset('storage/vendor/AdminLTE/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>-->
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

    <script src="//cdn.jsdelivr.net/npm/vanilla-lazyload@17.1.2/dist/lazyload.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="{{ asset('storage/js/xgallery.js') }}"></script>
@show
<!-- Go to www.addthis.com/dashboard to customize your tools -->
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-559ccedc7fb46463"></script>
</body>
</html>
