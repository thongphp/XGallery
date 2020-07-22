@extends('base')
@section('content')
    {{ $items->links() }}
    <div class="card-columns">
        @foreach ($items as $item)
            <div class="card">
                <a href="{{route('xiuren.item.view',['id' => $item->id])}}">@include('includes.card.cover', ['cover' => $item->getCover()])</a>
                <div class="card-body">

                </div>
                <div class="card-footer">
                    <small class="text-muted">
                        <span class="badge badge-primary">{{count($item->images)}}</span>
                    </small>
                    @if(config('services.adult.download'))
                        <span class="float-right">
                         <button type="button" class="btn btn-primary btn-sm ajax-pool"
                                 data-ajax-url="{{route('xiuren.download.request', $item->id)}}"
                                 data-ajax-command="download"
                         >
                        @include('includes.general.download')
                    </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    {{ $items->links() }}
@stop
