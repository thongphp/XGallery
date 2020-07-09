<div class="row mb-2">
    <div class="col-12">
        <nav class="navbar navbar-dark bg-dark">
            <form class="form-inline" method="post" action="{{route('jav.dashboard.view')}}">
                @csrf
                <input class="form-control input-sm mr-sm-2" type="text" name="keyword"
                       placeholder="Enter keyword" aria-label="Search"
                       value="{{request()->request->get('keyword')}}">

                @include('includes.form.filter',['name'=> 'director', 'options' => $directors,  'filterRequests' => request()->request->get('filter_director',[])])
                @include('includes.form.filter',['name'=> 'studios', 'options' => $studios,  'filterRequests' => request()->request->get('filter_studios',[])])
                @include('includes.form.filter',['name'=> 'series', 'options' => $series,  'filterRequests' => request()->request->get('filter_series',[])])

                @include('includes.form.sort',['default'=> 'id','sorts' => [ ['id','ID'],['release_date','Release date']]])

                @include('includes.form.pagination')

                <input type="hidden" name="genre" value="{{request()->request->get('genre')}}">
                <input type="hidden" name="idol" value="{{request()->request->get('idol')}}">
                <button class="btn btn-primary btn-sm my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
            </form>
        </nav>
    </div>
</div>
