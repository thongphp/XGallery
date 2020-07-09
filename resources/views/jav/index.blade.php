@extends('base')
@section('content')

    @include('jav.includes.navbar')
    @if ($items->total())
        <div class="card-columns">
            @foreach ($items as $item)
                @include('jav.includes.movie')
            @endforeach
        </div>
    @endif
    {{ $items->links() }}
        <fieldset>
            @csrf
        </fieldset>

@stop
