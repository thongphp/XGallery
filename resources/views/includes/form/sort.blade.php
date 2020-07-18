<label for="{{\App\Repositories\ConfigRepository::KEY_SORT_BY}}"></label>
<select class="custom-select form-control input-sm mr-sm-2" id="{{\App\Repositories\ConfigRepository::KEY_SORT_BY}}" name="{{\App\Repositories\ConfigRepository::KEY_SORT_BY}}">
    @foreach($sorts as $sort)
        <option @if(request()->request->get(\App\Repositories\ConfigRepository::KEY_SORT_BY, $default) === $sort[0]) selected @endif value="{{$sort[0]}}">
            {{$sort[1]}}
        </option>
    @endforeach
</select>
