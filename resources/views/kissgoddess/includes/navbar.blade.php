<div class="row">
    <div class="col-12">
        <div class="navbar-dark bg-dark pt-3 pb-3 mb-2">
            <form class="form-inline filter-tool" method="post" action="{{route('kissgoddess.dashboard.view')}}">
                @csrf
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-6">
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
                        <div class="col text-right">
                            @include(
                                'includes.form.sort',
                                [
                                    'default'=> '_id',
                                    'sorts' => [
                                        ['_id','ID'],
                                        ['title','Title'],
                                        ['updated_at', 'Updated'],
                                    ]
                                ]
                            )
                            @include('includes.form.pagination')
                            <button class="btn btn-primary" type="submit">
                                <em class="fas fa-search"></em> Search
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
