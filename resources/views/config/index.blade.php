@extends('base')
@section('guest-notice')
@stop
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>{{ $title }}</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline">
                    <div class="card-body box">
                        {!! form($form) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
