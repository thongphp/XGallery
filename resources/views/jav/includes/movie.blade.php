<div class="card">
    <a href="{{route('jav.movie.view',$item->id)}}">
        @include('includes.card.cover',['cover' => $item->getCover(), 'width' => '100%', 'alt' => $item->name])
    </a>
    <div class="card-body">
        <a href="{{route('jav.movie.view',$item->id)}}">
            <h5 class="card-title mr-1"><strong>{{$item->dvd_id}}</strong></h5>
        </a>
        @if(!empty($item->description))
            <p class="card-text">{{$item->description}}</p>
        @else
            <p class="card-text">{{$item->name}}</p>
        @endif
        @php
            $request = request()->all();
        @endphp
        <div class="row">
            <div class="col-12">
                <ul class="list-group list-group-flush">
                    @if(!empty($item->director))
                        <li class="list-group-item director">
                            <i class="fas fa-user mr-2"></i><strong class="mr-1">Director</strong>
                            <a href="{{jav_build_route_with_filter_param(\App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_DIRECTOR, $item->director)}}">
                                <span class="text-info">{{$item->director}}</span>
                            </a>
                        </li>
                    @endif
                    @if(!empty($item->studio))
                        <li class="list-group-item studio">
                            <i class="fas fa-tag mr-2"></i><strong class="mr-1">Studio</strong>
                            <a href="{{jav_build_route_with_filter_param(\App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_STUDIO, $item->studio)}}">
                                <span class="text-info">{{$item->studio}}</span>
                            </a>
                        </li>
                    @endif
                    @if(!empty($item->label))
                        <li class="list-group-item label">
                            <i class="fas fa-tag mr-2"></i><strong class="mr-1">Label</strong>
                            <span class="text-info">{{$item->label}}</span>
                        </li>
                    @endif
                    @if($item->genres->count() > 0)
                        <li class="list-group-item tag">
                            <i class="fas fa-tags"></i>
                            @foreach ($item->genres as $genre)
                                <a href="{{jav_build_route_with_filter_param(\App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_GENRE, $genre->id)}}">
                                    <span class="badge badge-pill badge-dark">{{$genre->name}}</span>
                                </a>
                            @endforeach
                        </li>
                    @endif
                    @if($item->idols->count() > 0)
                        <li class="list-group-item actress">
                            <em class="fas fa-female"></em>
                            @foreach ($item->idols as $idol)
                                <a href="{{jav_build_route_with_filter_param(\App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_IDOL, $idol->id)}}">
                                    <span class="badge badge-pill badge-info">{{$idol->name}}</span>
                                </a>
                            @endforeach
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i>{{$item->release_date}}</small>
        @can(\App\Services\UserRole::PERMISSION_JAV_DOWNLOAD)
            @if(config('xgallery.adult.download'))
                <span class="float-right">
                 <button type="button"
                         class="btn @if($item->is_downloadable == 1)btn-primary @else btn-warning @endif btn-sm ajax-pool"
                         data-ajax-url="{{route('jav.download.request', $item->dvd_id)}}"
                         data-ajax-command="download"
                 >
                @include('includes.general.download')
                </button>
            </span>
            @endif
        @endcan
    </div>
</div>
