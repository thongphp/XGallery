<div class="card">
    <a href="{{route('jav.idol.view',$item->id)}}">
        @include('includes.card.cover',['cover' => $item->getCover(), 'width' => '100%', 'alt' => $item->name])
    </a>
    <div class="card-body">
        <h5>
            <a href="{{route('jav.idol.view',$item->id)}}"><strong>{{$item->name}}</strong></a>
        </h5>
        <div class="row">
            <div class="col-12">
                <ul class="list-group list-group-flush">
                    @if ($item->getBirthday())
                        <li class="list-group-item">
                            <em class="far fa-calendar-alt mr-1"></em>{{$item->getBirthday()}}
                        </li>
                    @endif
                    @if ($item->getAge())
                        @php
                            $age = $item->getAge();
                        @endphp
                        <li class="list-group-item">
                            Age: <a href="{{jav_build_route_has_filter(\App\Repositories\ConfigRepository::JAV_IDOLS_FILTER_AGE_FROM, $age, false, false)}}">
                                {{$age}}
                            </a>
                        </li>
                    @endif
                    @if ($item->city)
                        <li class="list-group-item">
                            City: <a href="{{jav_build_route_has_filter(\App\Repositories\ConfigRepository::JAV_IDOLS_FILTER_CITY, $item->city, true, false)}}">
                                {{$item->city}}
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="text-muted text-right">{{$item->movies()->count()}} Movies</div>
    </div>
</div>
