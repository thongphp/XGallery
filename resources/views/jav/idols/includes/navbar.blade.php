<div class="row mb-2">
    <div class="col-12">
        <nav class="navbar navbar-dark bg-dark">
            <form class="form-inline" method="post" action="{{route('jav.idols.dashboard.view')}}">
                @csrf
                <input class="form-control input-sm mr-sm-2" type="text" name="keyword"
                       placeholder="Enter keyword" aria-label="Search"
                       value="{{request()->request->get('keyword')}}"
                />

                @include('includes.form.sort',['default'=> 'id','sorts' => [ ['id','ID'],['name','Name']]])

                @include('includes.form.pagination')

                <button class="btn btn-primary btn-sm my-2 my-sm-0" type="submit"><em class="fas fa-search"></em></button>
            </form>
        </nav>
    </div>
</div>
