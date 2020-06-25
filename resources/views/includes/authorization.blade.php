@extends('base')
@section('content')
    <div class="box">
        <div class="box-body p-4">
            <div class="row">
                <div class="col-12">
                    <div class="text-center">
                        @if (!$flickr)
                            <a href="{{url('oauth/flickr')}}" role="button" class="btn btn-outline-primary btn-lg">
                                <em class="fas fa-globe"></em> Authenticate with Flickr
                            </a>
                        @endif
                        @if (!$google)
                            <a href="{{url('oauth/google')}}" role="button" class="btn btn-outline-danger btn-lg">
                                <em class="fas fa-globe"></em> Authenticate with Google
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
