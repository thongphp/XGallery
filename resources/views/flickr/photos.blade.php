@extends('base')
@section('content')
    @include('jav.includes.navbar')
    <div class="row">
        @foreach ($items as $item)
            <div class="col-2">
                <div class="card">
                    <img class="bd-placeholder-img card-img-top" width="100%" src="{{$item->getCover()}}"/>
                    <div class="card-body">
                    </div>
                    <div class="card-footer">
                        <small class="text-muted pull-left float-left">{{$item->id}}</small>
                        <small class="text-muted pull-right float-right">{{$item->flickrcontact->realname}}</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    {{ $items->links() }}
@stop
