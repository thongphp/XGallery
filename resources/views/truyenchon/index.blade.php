@extends('base')
@section('content')
    @include('truyenchon.includes.navbar')
    <div class="card-columns">
        @foreach ($items as $item)
            <div class="card">
                <a href="{{route('truyenchon.story.view',['id'=> $item->id, 'chapter'=> 'chap-1'])}}">
                    @include('includes.card.cover',['cover'=>$item->getCover(), 'alt'=>$item->title])
                </a>
                <div class="card-body">
                    <a href="{{route('truyenchon.story.view',['id'=> $item->id, 'chapter'=> 'chap-1'])}}">
                        <h5 class="card-title mr-1"><strong>{{$item->title}}</strong></h5>
                    </a>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col">
                            @if(isset($item->chapters))
                                <small class="text-muted">
                                    @foreach($item->chapters as $chapter)
                                        <a href="{{route('truyenchon.story.view', ['id' => $item->id, 'chapter' => $chapter->chapter])}}">
                                            <span class="badge badge-primary">{{$chapter->chapter}}</span>
                                        </a>
                                    @endforeach
                                </small>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-right">
                            @can(\App\Services\UserRole::PERMISSION_TRUYENCHON_DOWNLOAD)
                                @if(config('xgallery.adult.download'))
                                    @if(!count($item->chapters))
                                        <div class="btn btn-secondary btn-sm disabled"><em
                                                class="fas fa-download mr-1"></em>Download
                                        </div>
                                    @elseif($item->isDownloading())
                                        <button type="button" class="btn btn-warning btn-sm ajax-pool"
                                                data-ajax-url="{{route('truyenchon.re-download.request', $item->id)}}"
                                                data-ajax-command="download"
                                        ><em class="fas fa-download mr-1"></em>Re-download
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-primary btn-sm ajax-pool"
                                                data-ajax-url="{{route('truyenchon.download.request', $item->id)}}"
                                                data-ajax-command="download"
                                        >
                                        @include('includes.general.download')
                                    @endif
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
