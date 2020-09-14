<div class="row">
    <div class="col-12">
        <div class="navbar-dark bg-dark pt-3 pb-3 mb-2">
            <form class="form-inline filter-tool" method="post" action="{{route('jav.idols.dashboard.view')}}">
                @csrf
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-3">
                            <input
                                class="form-control input-sm"
                                type="text"
                                name="{{\App\Repositories\ConfigRepository::KEY_KEYWORD}}"
                                placeholder="Enter keyword"
                                aria-label="Search"
                                value="{{request()->request->get(\App\Repositories\ConfigRepository::KEY_KEYWORD)}}"
                                style="width: 100%"
                            />
                        </div>
                        <div class="col-3">
                            @include('includes.form.filter',
                                [
                                    'name'=> \App\Repositories\ConfigRepository::JAV_IDOLS_FILTER_CITY,
                                    'options' => $cities,
                                    'title' => 'Choose city'
                                ]
                            )
                        </div>
                        <div class="col text-right">
                            @include(
                                'includes.form.sort',
                                [
                                    'default'=> 'id',
                                    'sorts' => [
                                        ['id','ID'],
                                        ['name','Name'],
                                        ['age', 'Age'],
                                        ['height','Height'],
                                        ['breast','Breast'],
                                        ['waist','Waist'],
                                        ['hips','Hips'],
                                    ]
                                ]
                            )
                            @include('includes.form.pagination')
                            <button class="btn btn-primary" type="submit">
                                <em class="fas fa-search"></em> Search
                            </button>
                        </div>
                    </div>
                    <div class="w-100 mt-2"></div>
                    <div class="row">
                        <div class="col-4">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        Age from
                                    </div>
                                </div>
                                <input
                                    type="number"
                                    class="form-control"
                                    value="{{request()->request->get(\App\Repositories\ConfigRepository::JAV_IDOLS_FILTER_AGE_FROM)}}"
                                    name="{{\App\Repositories\ConfigRepository::JAV_IDOLS_FILTER_AGE_FROM}}"
                                />
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        To
                                    </div>
                                </div>
                                <input
                                    type="number"
                                    class="form-control"
                                    value="{{request()->request->get(\App\Repositories\ConfigRepository::JAV_IDOLS_FILTER_AGE_TO)}}"
                                    name="{{\App\Repositories\ConfigRepository::JAV_IDOLS_FILTER_AGE_TO}}"
                                />
                            </div>
                        </div>
                        <div class="col">
                            @include('includes.form.filterNumber',
                                [
                                    'name' => \App\Repositories\ConfigRepository::JAV_IDOLS_FILTER_HEIGHT,
                                    'min' => 100,
                                    'max' => 210,
                                    'value' => request()->request->get(\App\Repositories\ConfigRepository::JAV_IDOLS_FILTER_HEIGHT),
                                    'text' => 'cm',
                                    'placeholder' => 'Height',
                                ]
                            )
                        </div>
                        <div class="col">
                            @include('includes.form.filterNumber',
                                [
                                    'name' => \App\Repositories\ConfigRepository::JAV_IDOLS_FILTER_BREAST,
                                    'min' => 0,
                                    'max' => 999,
                                    'value' => request()->request->get(\App\Repositories\ConfigRepository::JAV_IDOLS_FILTER_BREAST),
                                    'text' => 'cm',
                                    'placeholder' => 'Breast',
                                ]
                            )
                        </div>
                        <div class="col">
                            @include('includes.form.filterNumber',
                                [
                                    'name' => \App\Repositories\ConfigRepository::JAV_IDOLS_FILTER_WAIST,
                                    'min' => 0,
                                    'max' => 999,
                                    'value' => request()->request->get(\App\Repositories\ConfigRepository::JAV_IDOLS_FILTER_WAIST),
                                    'text' => 'cm',
                                    'placeholder' => 'Waist',
                                ]
                            )
                        </div>
                        <div class="col">
                            @include('includes.form.filterNumber',
                                [
                                    'name' => \App\Repositories\ConfigRepository::JAV_IDOLS_FILTER_HIPS,
                                    'min' => 0,
                                    'max' => 999,
                                    'value' => request()->request->get(\App\Repositories\ConfigRepository::JAV_IDOLS_FILTER_HIPS),
                                    'text' => 'cm',
                                    'placeholder' => 'Hips',
                                ]
                            )
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
