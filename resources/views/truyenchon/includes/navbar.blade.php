<div class="row mb-2">
    <div class="col-12">
        <nav class="navbar navbar-dark bg-dark">
            <form class="form-inline" method="get" action="{{route('truyenchon.dashboard.view')}}">
                @csrf
                <input class="form-control input-sm mr-sm-2" type="Enter your search keyword" name="keyword"
                       placeholder="Search" aria-label="Search" value="{{request()->get('keyword')}}">

                <select class="custom-select form-control input-sm mr-sm-2"
                        id="{{\App\Repositories\ConfigRepository::KEY_SORT_BY}}"
                        name="{{\App\Repositories\ConfigRepository::KEY_SORT_BY}}"
                >
                    <option @if(request()->get(\App\Repositories\ConfigRepository::KEY_SORT_BY,'id') === 'id') selected @endif value="id">ID</option>
                    <option @if(request()->get(\App\Repositories\ConfigRepository::KEY_SORT_BY,'id') === 'title') selected @endif value="title">Title</option>
                </select>
                @include('includes.form.pagination')
                <button class="btn btn-primary btn-sm my-2 my-sm-0" type="submit">Search</button>
            </form>
        </nav>
    </div>
</div>
