@extends('base')
@section('content')
    <h3>{{$item->title}}</h3>
    <hr />
    <div id="slideControls" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            @foreach ($item->images as $index => $image)
                <li data-target="#slideIndicators" data-slide-to="{{$index}}"
                    class="{{$index===0 ? 'active': ''}}"></li>
            @endforeach
        </ol>
        <div class="carousel-inner">
            @foreach ($item->images as $index => $image)
                <div class="carousel-item {{$index===0 ? 'active': ''}}">
                    <img class="d-block lazy" data-src="{{$image}}" alt="">
                </div>
            @endforeach
        </div>
        <a class="carousel-control-prev" href="#slideControls" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#slideControls" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
@stop
