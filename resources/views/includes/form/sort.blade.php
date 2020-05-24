<label for="sort-by"></label>
<select class="custom-select form-control input-sm mr-sm-2" id="sort-by"
        name="sort-by">
    @foreach($sorts as $sort)
        <option @if(request()->request->get('sort-by',$default) == $sort[0]) selected @endif value="{{$sort[0]}}">
            {{$sort[1]}}
        </option>
    @endforeach
</select>
