@extends('base')
@section('content')
    @include('flickr.includes.navbar')
    <div class="card-columns">
        @foreach ($items as $item)
            <div class="card">
                <div class="card-body">
                    <a href="{{route('flickr.contact.view', $item->nsid)}}">
                        <h5 class="card-title mr-1"><strong>{{$item->username}}</strong></h5>
                        <span class="badge badge-primary">{{$item->nsid}}</span>
                    </a>
                </div>
                <div class="card-footer">
                    <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i>{{$item->updated_at}}</small>
                    <small class="text-muted pull-right float-right"><i
                            class="far fa-calendar-alt mr-1"></i>{{$item->photos->count()}}</small>
                </div>
            </div>
        @endforeach
    </div>
    {{ $items->links() }}
@stop
