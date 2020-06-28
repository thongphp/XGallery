@extends('base')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-8">
                <form class="form ajax-form" method="post" action="{{route('flickr.download.request')}}">
                    @csrf
                    <div class="row">
                        <div class="col-10">
                            <input class="form-control input-sm mr-sm-2"
                                   type="text"
                                   name="url"
                                   placeholder="Enter download URL"
                                   aria-label="Search"
                                   value="{{request()->get('url')}}"
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
        <hr/>
        <div class="row mt-5">
            <div class="col-12">
                <form class="form" method="post" action="{{route('flickr.dashboard.view')}}">
                    @csrf
                    <div class="row">
                        <div class="col-10">
                            <input class="form-control input-sm mr-sm-2"
                                   type="text"
                                   name="url"
                                   placeholder="Enter Flickr URL to extract data"
                                   aria-label="Search"
                                   value="{{request()->get('url')}}"
                            />
                        </div>
                        <div class="col-2">
                            <button class="btn btn-primary btn-block my-2 my-sm-0"
                                    type="submit"><i class="fas fa-info-circle mr-1"></i>Information
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @isset($profile)
                <div class="col-12 mt-5">
                    <div class="row">
                        <div class="col-6">
                            <div class="card">
                                <h5 class="card-header">
                                    <a href="{{$profile->profileurl}}"
                                       target="_blank">{{$profile->username}}</a>
                                    <span class="float-right">{{$profile->nsid}}</span>
                                </h5>

                                <div class="card-body">
                                    {!! $message !!}
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">{{$data->photoset->title}}</li>
                                    <li class="list-group-item">{{$data->photoset->id}}</li>
                                </ul>
                                <div class="card-footer text-muted">
                                    <a href="{{$profile->photosurl}}" target="_blank">{{$profile->photosurl}}</a>
                                    <span class="float-right">{{$profile->photos->count}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endisset
        </div>
    </div>
@stop
