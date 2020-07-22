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
                            @if(config('services.adult.download'))
                                 <button type="button" class="btn btn-primary btn-sm ajax-pool"
                                         data-ajax-url="{{route('truyenchon.download.request', $item->id)}}"
                                         data-ajax-command="download"
                                 >
                                @include('includes.general.download')
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {{ $items->links() }}
@stop
