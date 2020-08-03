@extends('base')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 mb-4">
                <h1>{{$title}}</h1>
            </div>
        </div>
        @if($activities ?? false)
            <div class="row">
                <div class="col-12">
                    <div class="timeline">
                        @php
                            $date = '';
                        @endphp
                        @foreach ($activities as $activity)
                            @php
                                $activityDate = $activity->created_at->format('d-m-Y');
                            @endphp
                            @if (empty($date) || $date !== $activityDate)
                                @php
                                    $date = $activityDate;
                                @endphp
                                <div class="time-label">
                                    <span class="bg-gradient-cyan">{{$date}}</span>
                                </div>
                            @endif
                            <div class="mb-4">
                                @if($activity->action === 'download')
                                    <em class="fas fa-download bg-gradient-blue mt-2"></em>
                                @else
                                    <em class="fas fa-briefcase bg-secondary mt-2"></em>
                                @endif
                                <div class="timeline-item">
                                    <div class="time p-3">
                                        <em class="fas fa-clock"></em> {{$activity->created_at->diffForHumans()}}
                                    </div>
                                    <div class="timeline-header p-3">
                                        <div class="font-weight-bold">
                                            {{sprintf($activity->text, $actor->name, $activity->action)}}
                                        </div>
                                    </div>
                                    <div class="timeline-body">
                                        @if(View::exists('user.includes.' . $activity->object_table))
                                            @include('user.includes.' . $activity->object_table)
                                        @else
                                            @include('user.includes.default')
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    {{ $activities->links() }}
                </div>
            </div>
        @endif
    </div>
@stop
