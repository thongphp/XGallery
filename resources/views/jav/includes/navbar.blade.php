<div class="row mb-2">
    <div class="col-12">
        <nav class="navbar navbar-dark bg-dark">
            <form class="form-inline" method="get" action="{{route('jav.dashboard.view')}}">
                @csrf
                <input class="form-control input-sm mr-sm-2" type="text" name="keyword"
                       placeholder="Enter keyword" aria-label="Search"
                       value="{{request()->request->get('keyword')}}">
                <label for="searchBy"></label><select class="custom-select form-control input-sm mr-sm-2" id="searchBy"
                                                      name="searchBy">
                    <option @if(request()->request->get('searchBy', 'keyword') == 'keyword') selected
                            @endif value="keyword">Keyword
                    </option>
                    <option @if(request()->request->get('searchBy', 'keyword') == 'director') selected
                            @endif value="director">Director
                    </option>
                    <option @if(request()->request->get('searchBy', 'keyword') == 'studio') selected
                            @endif value="studio">Studio
                    </option>
                    <option @if(request()->request->get('searchBy', 'keyword') == 'label') selected
                            @endif value="label">Label
                    </option>
                </select>
                @include('includes.form.sort',['default'=> 'id','sorts' => [ ['id','ID'],['release_date','Release date'],['is_downloadable','Downloadable']]])
                <input data-provide="datepicker" class="custom-select form-control mr-sm-2" name="fromDate"/>
                <input data-provide="datepicker" class="custom-select form-control mr-sm-2" name="toDate"/>

                @include('includes.form.pagination')

                <input type="hidden" name="genre" value="{{request()->request->get('genre')}}">
                <input type="hidden" name="idol" value="{{request()->request->get('idol')}}">
                <button class="btn btn-primary btn-sm my-2 my-sm-0" type="submit"><i class="fas fa-search"></i></button>
            </form>
        </nav>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active"
                    aria-current="page"><a href="{{route('jav.dashboard.view')}}"><i class="fas fa-home"></i></a></li>
                @if (request()->request->get('idol'))
                    <li class="breadcrumb-item active"
                        aria-current="page">{{\App\Models\JavIdols::find(request()->request->get('idol'))->name}}</li>
                @endif
                @if (request()->request->get('genre'))
                    <li class="breadcrumb-item active"
                        aria-current="page">{{\App\Models\JavGenres::find(request()->request->get('genre'))->name}}</li>
                @endif
            </ol>
        </nav>


    </div>
</div>
