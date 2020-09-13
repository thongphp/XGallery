@extends('base')
@section('content')
    @include('xiuren.includes.navbar')
    <div class="card-columns">
        @foreach ($items as $item)
            @php
                $itemLink = route('xiuren.item.view',['id' => $item->_id]);
            @endphp
            <div class="card">
                <a href="{{$itemLink}}">@include('includes.card.cover', ['cover' => $item->getCover()])</a>
                <div class="card-body">
                    <a href="{{$itemLink}}">
                        <h5 class="card-title mr-1"><strong>{{$item->getTitle()}}</strong></h5>
                    </a>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col">
                            Total images: <span class="badge badge-primary">{{count($item->images)}}</span>
                        </div>
                        <div class="col text-right">
                            @can(\App\Services\UserRole::PERMISSION_XIUREN_DOWNLOAD)
                                @if(config('xgallery.adult.download'))
                                    <span class="float-right">
                                     <button
                                         type="button"
                                         class="btn btn-primary btn-sm ajax-pool"
                                         data-ajax-url="{{route('xiuren.download.request', $item->_id)}}"
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
