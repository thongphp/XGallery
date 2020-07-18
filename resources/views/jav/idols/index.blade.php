@extends('base')
@section('content')
    @include('jav.idols.includes.navbar')
    @if($message ?? false)
    <div class="card-body">
        {!! $message !!}
    </div>
    @endif
    @if($items ?? false)
        <div class="card-columns">
            @foreach ($items as $item)
                @include('jav.idols.includes.idol')
            @endforeach
        </div>
        {{ $items->links() }}
    @endif
@stop
