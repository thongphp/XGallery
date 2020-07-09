<label for="sortBy"></label>
<select class="custom-select form-control input-sm mr-sm-2" id="sortBy" name="sortBy">
    @foreach($sorts as $sort)
        <option @if(request()->request->get('sortBy',$default) == $sort[0]) selected @endif value="{{$sort[0]}}">
            {{$sort[1]}}
        </option>
    @endforeach
</select>
