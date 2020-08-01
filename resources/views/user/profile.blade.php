@extends('base')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Profile</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="card card-primary card-outline">
                    <div class="card-body box">
                        <div class="row">
                            <div class="col-6">
                                <div class="text-center">
                                    <img src="{{$googleInfo->avatar}}" class="profile-user-img img-fluid img-circle" alt="{{$user->name}}" />
                                </div>
                                <h3 class="profile-username text-center">{{$user->name}}</h3>
                                <hr />
                                <div class="text-center">
                                    <a href="{{route('user.logout')}}" class="btn btn-outline-danger">
                                        <em class="fas fa-sign-out-alt"></em> Logout
                                    </a>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="list-group list-group-unbordered">
                                    <div class="list-group-item">
                                        <span class="text-bold">Role: </span> <span class="text-muted">@if($user->isAdmin()) Admin @else User @endif</span>
                                    </div>
                                    <div class="list-group-item">
                                        <span class="text-bold">Email: </span> <span class="text-muted">{{$user->email}}</span>
                                    </div>
                                    <div class="list-group-item">
                                        <span class="text-bold">Created: </span> <span class="text-muted">{{$googleInfo->created_at}}</span>
                                    </div>
                                    <div class="list-group-item">
                                        <span class="text-bold">Updated: </span> <span class="text-muted">{{$googleInfo->updated_at}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
