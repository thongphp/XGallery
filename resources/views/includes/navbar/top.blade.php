@section('navbar')
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <form class="form-inline my-2 my-lg-0" action="">
                        <span class="mr-sm-2">
                            <div class="input-group">
                                <input class="form-control" type="text" name="keyword" value="" id="">
                                <span class="input-group-append">
                                    <div class="input-group-text bg-transparent"><i
                                            class="fa fa-search"></i>
                                    </div>
                                </span>
                            </div>
                        </span>
                    </form>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                @auth
                    <li class="nav-item">
                        <i class="fas fa-user"></i> {{\Illuminate\Support\Facades\Auth::user()->getAuthIdentifier()}}
                    </li>
                @endauth
            </ul>
        </div>
    </nav>
@show
