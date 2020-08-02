@section('navbar')
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav mr-2">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><em class="fas fa-bars"></em></a>
            </li>
            @auth
                <li class="nav-item">
                    <a class="nav-link" href="{{route('user.profile.view')}}">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('user.activities.view')}}">Activities</a>
                </li>
            @endauth
        </ul>
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <form class="form-inline my-2 my-lg-0" action="">
                        <div class="mr-sm-2">
                            <div class="input-group">
                                <input class="form-control" type="text" name="keyword" value="" id=""/>
                                <div class="input-group-append">
                                    <div class="input-group-text bg-transparent">
                                        <em class="fa fa-search"></em>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                @auth
                    <li class="nav-item">
                        <a href="{{route('user.profile.view')}}">
                            <em class="fas fa-user"></em> {{\Illuminate\Support\Facades\Auth::user()->name}}
                        </a>
                    </li>
                @endauth
                @guest
                    <li class="nav-item">
                        <button type="button" class="btn btn-outline-danger btn-modal"
                                data-modal-title="Login"
                                data-modal-content="#modal-login-content"
                        >
                            <em class="fas fa-user"></em> Login
                        </button>
                        <div id="modal-login-content" class="hidden">
                            <div class="text-center">
                                <a class="btn btn-danger" href="{{route('oauth.login')}}">
                                    <em class="fab fa-google"></em> Login with Google
                                </a>
                            </div>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </nav>
@show
