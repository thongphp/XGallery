@extends('base')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="offset-2 col-8">
                <form class="form ajax-form" method="post" action="{{route('flickr.download.request')}}">
                    @csrf
                    <div class="row">
                        <div class="col-10">
                            <input class="form-control input-sm mr-sm-2"
                                   type="text"
                                   name="url"
                                   placeholder="Enter download URL"
                                   aria-label="Search"
                                   value="{{request()->get('keyword')}}"
                            />
                        </div>
                        <div class="col-2">
                            <button class="btn btn-primary btn-block my-2 my-sm-0"
                                    type="submit">@include('includes.general.download')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
