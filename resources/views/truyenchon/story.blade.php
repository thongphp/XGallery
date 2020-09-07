@extends('base')
@section('content')
    @include('truyenchon.includes.navbar')
    <h1>{{$story->title}}</h1>
    @if($items)
    <div id="slideControls" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            @foreach ($items as $index => $item)
                <li data-target="#slideIndicators" data-slide-to="{{$index}}"
                    class="{{$index===0 ? 'active': ''}}"></li>
            @endforeach
        </ol>
        <div class="carousel-inner">
            @foreach ($items as $index => $item)
                <div class="carousel-item {{$index===0 ? 'active': ''}}">
                    <img class="d-block lazy" data-src="{{$item}}" alt="">
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
    @endif
    <hr />
    @if($prev)
        <a class="btn btn-primary"
           href="{{route('truyenchon.story.view',['id'=> $story->id, 'chapter'=> $prev])}}">Prev</a>
    @endif
    @if($next)
        <a class="btn btn-primary"
           href="{{route('truyenchon.story.view',['id'=> $story->id, 'chapter'=> $next])}}">Next</a>
    @endif
@stop
@section('scripts')
    @parent
    @if($items)
    <script>
        jQuery('.carousel').carousel({
            'interval': false
        });
    </script>
    @endif
@endsection
