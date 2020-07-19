<div class="row">
    <div class="col-12">
        <div class="navbar-dark bg-dark pt-3 pb-3 mb-2">
            <form class="form-inline filter-tool" method="post" action="{{route('jav.dashboard.view')}}">
                @csrf
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-6">
                            <input class="form-control input-sm" type="text" name="keyword"
                                   placeholder="Enter keyword" aria-label="Search"
                                   value="{{request()->request->get('keyword')}}"
                                   style="width: 100%"
                            />
                        </div>
                        <div class="col-2">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input
                                        class="custom-control-input"
                                        type="checkbox"
                                        id="{{\App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_DOWNLOADABLE}}"
                                        name="{{\App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_DOWNLOADABLE}}"
                                        @if($downloadable) checked @endif
                                        value="1"
                                    />
                                    <label for="{{\App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_DOWNLOADABLE}}" class="custom-control-label">
                                        Only Downloadable
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col text-right">
                            @include('includes.form.sort',['default'=> 'id','sorts' => [ ['id','ID'],['release_date','Release date']]])
                            @include('includes.form.pagination')
                            <input type="hidden" name="genre" value="{{request()->request->get('genre')}}">
                            <input type="hidden" name="idol" value="{{request()->request->get('idol')}}">
                            <button class="btn btn-primary" type="submit">
                                <em class="fas fa-search"></em> Search
                            </button>
                        </div>
                    </div>
                    <div class="w-100 mt-2"></div>
                    <div class="row">
                        <div class="col">
                            @include('includes.form.filter',
                                [
                                    'name'=> \App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_DIRECTOR,
                                    'options' => $directors,
                                    'title' => 'Choose director'
                                ]
                            )
                        </div>
                        <div class="col">
                            @include('includes.form.filter',
                                [
                                    'name'=> \App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_STUDIO,
                                    'options' => $studios,
                                    'title' => 'Choose studio'
                                ]
                            )
                        </div>
                        <div class="col">
                            @include('includes.form.filter',
                                [
                                    'name'=> \App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_SERIES,
                                    'options' => $series,
                                    'title' => 'Choose series'
                                ]
                            )
                        </div>
                        <div class="col">
                            @include('includes.form.filter',
                                 [
                                     'name'=> \App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_CHANNEL,
                                     'options' => $channels,
                                     'title' => 'Choose channel'
                                 ]
                             )
                        </div>
                        <div class="col">
                            @include('includes.form.filter',
                                [
                                    'name'=> \App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_IDOL,
                                    'options' => $idols,
                                    'title' => 'Choose idol'
                                ]
                            )
                        </div>
                        <div class="col">
                            @include('includes.form.filter',
                                [
                                    'name'=> \App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_GENRE,
                                    'options' => $genres,
                                    'title' => 'Choose genre'
                                ]
                            )
                        </div>
                    </div>
                    <div class="w-100 mt-2"></div>
                    <div class="row">
                        <div class="col-6">
                            <div class="input-daterange input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        From
                                    </div>
                                </div>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{$dateFrom}}"
                                    name="{{\App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_FROM}}"
                                />
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        To
                                    </div>
                                </div>
                                <input
                                    type="text"
                                    class="form-control"
                                    value="{{$dateTo}}"
                                    name="{{\App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_TO}}"
                                />
                            </div>
                        </div>
                        <div class="col">
                            @include('includes.form.filterNumber',
                                [
                                    'name' => \App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_IDOL_HEIGHT,
                                    'min' => 100,
                                    'max' => 210,
                                    'value' => $idolHeight,
                                    'text' => 'cm',
                                    'placeholder' => 'Idol height',
                                ]
                            )
                        </div>
                        <div class="col">
                            @include('includes.form.filterNumber',
                                [
                                    'name' => \App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_IDOL_BREAST,
                                    'min' => 0,
                                    'max' => 999,
                                    'value' => $idolBreast,
                                    'text' => 'cm',
                                    'placeholder' => 'Idol breast',
                                ]
                            )
                        </div>
                        <div class="col">
                            @include('includes.form.filterNumber',
                                [
                                    'name' => \App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_IDOL_WAIST,
                                    'min' => 0,
                                    'max' => 999,
                                    'value' => $idolWaist,
                                    'text' => 'cm',
                                    'placeholder' => 'Idol waist',
                                ]
                            )
                        </div>
                        <div class="col">
                            @include('includes.form.filterNumber',
                                [
                                    'name' => \App\Repositories\ConfigRepository::KEY_JAV_MOVIES_FILTER_IDOL_HIPS,
                                    'min' => 0,
                                    'max' => 999,
                                    'value' => $idolHips,
                                    'text' => 'cm',
                                    'placeholder' => 'Idol hips',
                                ]
                            )
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
