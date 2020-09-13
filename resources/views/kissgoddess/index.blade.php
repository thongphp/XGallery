@extends('base')
@section('content')
    @include('kissgoddess.includes.navbar')
    <div class="card-columns">
        @foreach ($items as $item)
            <div class="card">
                @php
                    $itemLink = route('kissgoddess.item.view',['id' => $item->_id]);
                @endphp
                <a href="{{$itemLink}}">@include('includes.card.cover', ['cover' => $item->getCover()])</a>
                <div class="card-body">
                    <a href="{{$itemLink}}">
                        <h5 class="card-title mr-1"><strong>{{$item->title}}</strong></h5>
                    </a>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col">
                            Images: <span class="badge badge-primary">{{count($item->images)}}</span>
                        </div>
                        <div class="col text-right">
                            @can(\App\Services\UserRole::PERMISSION_KISSGODDESS_DOWNLOAD)
                                @if(config('xgallery.adult.download'))
                                    <span class="float-right">
                                     <button
                                         type="button"
                                         class="btn btn-primary btn-sm ajax-pool"
                                         data-ajax-url="{{route('kissgoddess.download.request', $item->_id)}}"
                                         data-ajax-command="download"
                                     >
                                     @include('includes.general.download')
                                </span>
                                @endif
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {{ $items->links() }}
@stop
