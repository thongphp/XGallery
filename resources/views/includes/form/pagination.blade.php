<select class="custom-select form-control input-sm mr-sm-2" id="sortDir" name="sortDir">
    <option @if(request()->request->get('sort-dir','desc') == 'asc') selected @endif value="asc">Asc</option>
    <option @if(request()->request->get('sort-dir','desc') == 'desc') selected @endif value="desc">Desc</option>
</select>
<select class="custom-select form-control input-sm mr-sm-2" id="perPage" name="perPage">
    <option @if(request()->request->get('per-page', 15) == 15) selected @endif value="15">15</option>
    <option @if(request()->request->get('per-page', 15) == 30) selected @endif value="30">30</option>
    <option @if(request()->request->get('per-page', 15) == 60) selected @endif value="60">60</option>
    <option @if(request()->request->get('per-page', 15) == 120) selected @endif value="120">120</option>
</select>
