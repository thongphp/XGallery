@extends('base')
@section('content')
    @include('jav.includes.navbar')
    {{ $items->links() }}
    @if ($items->total())
        <div class="card-columns">
            @foreach ($items as $item)
                @include('jav.includes.movie')
            @endforeach
        </div>
    @endif
    {{ $items->links() }}
@stop
