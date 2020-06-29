@extends('base')
@section('content')
    <div class="row">
        <div class="col-md-5 mx-auto">
            <div id="first">
                <div class="myform form ">
                    <div class="logo mb-3">
                        <div class="col-md-12 text-center">
                            <h1>Login</h1>
                        </div>
                    </div>
                    <form action="" method="post" name="login">

                        <div class="form-group">
                            <p class="text-center">By signing up you accept our <a href="#">Terms Of Use</a></p>
                        </div>
                        <div class="col-md-12 text-center ">
                            <a href="{{url('oauth/google')}}" role="button" class="btn btn-outline-danger btn-lg">
                                <em class="fas fa-globe"></em> Authenticate with Google
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
